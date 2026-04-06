<?php

declare(strict_types=1);

/**
 * Envoi SMTP Hostinger : SMTPS 465, puis secours STARTTLS 587 si la connexion SSL échoue.
 */

function mail_smtp_log(string $msg): void
{
    error_log('[mariage-mail] ' . $msg);
}

function smtp_ssl_stream_context(string $host): array
{
    $verify = !defined('MAIL_SMTP_SSL_VERIFY') || MAIL_SMTP_SSL_VERIFY === true;

    return [
        'ssl' => [
            'verify_peer'       => $verify,
            'verify_peer_name'  => $verify,
            'allow_self_signed' => false,
            'peer_name'         => $host,
            'SNI_enabled'       => true,
        ],
    ];
}

function smtp_dot_stuff(string $data): string
{
    $lines = explode("\r\n", str_replace("\n", "\r\n", str_replace("\r\n", "\n", $data)));
    $out = [];
    foreach ($lines as $line) {
        $out[] = (str_starts_with($line, '.')) ? '.' . $line : $line;
    }

    return implode("\r\n", $out);
}

/**
 * @param resource $fp
 */
function smtp_read_response($fp): array
{
    $lines = [];
    while (($line = fgets($fp, 8192)) !== false) {
        $lines[] = $line;
        if (strlen($line) >= 4 && $line[3] === ' ') {
            break;
        }
    }

    return $lines;
}

/**
 * @param resource $fp
 */
function smtp_expect($fp, array $okCodes): void
{
    $lines = smtp_read_response($fp);
    $first = $lines[0] ?? '';
    $code = (int) substr($first, 0, 3);
    if (!in_array($code, $okCodes, true)) {
        throw new RuntimeException('SMTP: réponse inattendue ' . trim(implode('', $lines)));
    }
}

/**
 * @param resource $fp
 */
function smtp_cmd($fp, string $line, array $okCodes): void
{
    fwrite($fp, $line . "\r\n");
    smtp_expect($fp, $okCodes);
}

function smtp_ehlo_domain(): string
{
    if (defined('MAIL_FROM') && MAIL_FROM !== '' && str_contains(MAIL_FROM, '@')) {
        return substr(strrchr(MAIL_FROM, '@'), 1) ?: 'localhost';
    }

    return 'localhost';
}

/**
 * @return resource|false
 */
function smtp_connect_smtps(string $host, int $port, int $timeout = 45)
{
    $ctx = stream_context_create(smtp_ssl_stream_context($host));

    $fp = @stream_socket_client(
        'ssl://' . $host . ':' . $port,
        $errno,
        $errstr,
        $timeout,
        STREAM_CLIENT_CONNECT,
        $ctx
    );
    if ($fp === false) {
        mail_smtp_log("SMTPS connexion échouée ({$host}:{$port}) : {$errstr} ({$errno})");
    }

    return $fp;
}

/**
 * @return resource|false
 */
function smtp_connect_starttls(string $host, int $port, int $timeout = 45)
{
    $fp = @stream_socket_client(
        'tcp://' . $host . ':' . $port,
        $errno,
        $errstr,
        $timeout,
        STREAM_CLIENT_CONNECT
    );
    if ($fp === false) {
        mail_smtp_log("TCP {$port} échoué : {$errstr} ({$errno})");

        return false;
    }
    stream_set_timeout($fp, 60);

    try {
        smtp_expect($fp, [220]);
        $ehlo = smtp_ehlo_domain();
        smtp_cmd($fp, 'EHLO ' . $ehlo, [250]);
        smtp_cmd($fp, 'STARTTLS', [220]);
        $cryptoOk = @stream_socket_enable_crypto(
            $fp,
            true,
            STREAM_CRYPTO_METHOD_TLS_CLIENT
        );
        if (!$cryptoOk) {
            mail_smtp_log('STARTTLS : négociation TLS échouée');
            fclose($fp);

            return false;
        }
        smtp_cmd($fp, 'EHLO ' . $ehlo, [250]);
    } catch (Throwable $e) {
        mail_smtp_log('STARTTLS : ' . $e->getMessage());
        fclose($fp);

        return false;
    }

    return $fp;
}

/**
 * @param resource $fp
 */
function smtp_send_payload(
    $fp,
    string $fromEmail,
    string $fromName,
    string $toEmail,
    string $subjectUtf8,
    string $mimeBody
): void {
    $fromHeader = mb_encode_mimeheader($fromName, 'UTF-8', 'Q') . ' <' . $fromEmail . '>';
    $subjHeader = mb_encode_mimeheader($subjectUtf8, 'UTF-8', 'Q');
    $dateHeader = gmdate('D, d M Y H:i:s') . ' +0000';
    $domain = preg_replace('/^.*@/', '', $fromEmail) ?: 'localhost';
    $msgId = '<' . bin2hex(random_bytes(16)) . '@' . $domain . '>';

    $headers = [
        'Date: ' . $dateHeader,
        'Message-ID: ' . $msgId,
        'MIME-Version: 1.0',
        'From: ' . $fromHeader,
        'To: ' . $toEmail,
        'Subject: ' . $subjHeader,
    ];

    $payload = implode("\r\n", $headers) . "\r\n\r\n" . $mimeBody;
    $payload = smtp_dot_stuff($payload);

    fwrite($fp, $payload . "\r\n.\r\n");
    smtp_expect($fp, [250]);
}

/**
 * @param resource $fp
 */
function smtp_session_auth_and_mail(
    $fp,
    string $user,
    string $pass,
    string $fromEmail,
    string $fromName,
    string $toEmail,
    string $subjectUtf8,
    string $mimeBody
): bool {
    try {
        if ($pass === '') {
            mail_smtp_log('Mot de passe SMTP vide — vérifiez config / variable MAIL_SMTP_PASS');

            return false;
        }
        smtp_cmd($fp, 'AUTH LOGIN', [334]);
        smtp_cmd($fp, base64_encode($user), [334]);
        smtp_cmd($fp, base64_encode($pass), [235]);
        smtp_cmd($fp, 'MAIL FROM:<' . $fromEmail . '>', [250]);
        smtp_cmd($fp, 'RCPT TO:<' . $toEmail . '>', [250, 251, 252]);
        smtp_cmd($fp, 'DATA', [354]);
        smtp_send_payload($fp, $fromEmail, $fromName, $toEmail, $subjectUtf8, $mimeBody);
        smtp_cmd($fp, 'QUIT', [221]);
    } catch (Throwable $e) {
        mail_smtp_log('Session SMTP : ' . $e->getMessage());

        return false;
    }

    return true;
}

function smtp_send_mail(
    string $host,
    int $port,
    string $user,
    string $pass,
    string $fromEmail,
    string $fromName,
    string $toEmail,
    string $subjectUtf8,
    string $mimeBody
): bool {
    if (!filter_var($fromEmail, FILTER_VALIDATE_EMAIL) || !filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
        mail_smtp_log('Adresse/expéditeur invalide pour SMTP');

        return false;
    }

    $ehlo = smtp_ehlo_domain();
    $port = $port > 0 ? $port : 465;
    $fp = false;

    if ((int) $port === 587) {
        $fp = smtp_connect_starttls($host, 587, 45);
    } else {
        $fp = smtp_connect_smtps($host, $port, 45);
        if ($fp !== false) {
            stream_set_timeout($fp, 60);
            try {
                smtp_expect($fp, [220]);
                smtp_cmd($fp, 'EHLO ' . $ehlo, [250]);
            } catch (Throwable $e) {
                mail_smtp_log('SMTPS après connexion : ' . $e->getMessage());
                fclose($fp);
                $fp = false;
            }
        }
        if ($fp === false) {
            mail_smtp_log('Tentative STARTTLS sur port 587…');
            $fp = smtp_connect_starttls($host, 587, 45);
        }
    }

    if ($fp === false) {
        return false;
    }

    $ok = smtp_session_auth_and_mail(
        $fp,
        $user,
        $pass,
        $fromEmail,
        $fromName,
        $toEmail,
        $subjectUtf8,
        $mimeBody
    );

    @fclose($fp);

    return $ok;
}
