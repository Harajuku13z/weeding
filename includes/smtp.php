<?php

declare(strict_types=1);

/**
 * Envoi SMTP minimal (SMTPS port 465 — SSL dès la connexion).
 * Utilisé lorsque MAIL_SMTP_HOST est défini et non vide dans config.php
 */

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

/**
 * @return resource|false
 */
function smtp_connect_ssl(string $host, int $port, int $timeout = 45)
{
    $ctx = stream_context_create([
        'ssl' => [
            'verify_peer'       => true,
            'verify_peer_name'  => true,
            'allow_self_signed' => false,
        ],
    ]);

    return @stream_socket_client(
        'ssl://' . $host . ':' . $port,
        $errno,
        $errstr,
        $timeout,
        STREAM_CLIENT_CONNECT,
        $ctx
    );
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
        return false;
    }

    $fp = smtp_connect_ssl($host, $port);
    if ($fp === false) {
        return false;
    }
    stream_set_timeout($fp, 60);

    try {
        smtp_expect($fp, [220]);
        $ehloHost = 'lisalovechrist.fr';
        smtp_cmd($fp, 'EHLO ' . $ehloHost, [250]);
        smtp_cmd($fp, 'AUTH LOGIN', [334]);
        smtp_cmd($fp, base64_encode($user), [334]);
        smtp_cmd($fp, base64_encode($pass), [235]);
        smtp_cmd($fp, 'MAIL FROM:<' . $fromEmail . '>', [250]);
        smtp_cmd($fp, 'RCPT TO:<' . $toEmail . '>', [250, 251]);
        smtp_cmd($fp, 'DATA', [354]);

        $fromHeader = mb_encode_mimeheader($fromName, 'UTF-8', 'Q') . ' <' . $fromEmail . '>';
        $subjHeader = mb_encode_mimeheader($subjectUtf8, 'UTF-8', 'Q');
        $dateHeader = gmdate('D, d M Y H:i:s') . ' +0000';
        $msgId = '<' . bin2hex(random_bytes(16)) . '@' . preg_replace('/^.*@/', '', $fromEmail) . '>';

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
        smtp_cmd($fp, 'QUIT', [221]);
    } catch (Throwable $e) {
        @fclose($fp);

        return false;
    }

    fclose($fp);

    return true;
}
