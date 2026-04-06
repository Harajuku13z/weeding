<?php
/**
 * Envoi d'e-mails simples (fonction mail() PHP).
 * Configurez MAIL_FROM, MAIL_ENABLED et SITE_PUBLIC_URL dans config.php
 */

declare(strict_types=1);

function mail_is_enabled(): bool
{
    return defined('MAIL_ENABLED') && MAIL_ENABLED === true;
}

function mail_site_url(): string
{
    if (defined('SITE_PUBLIC_URL') && SITE_PUBLIC_URL !== '') {
        return rtrim(SITE_PUBLIC_URL, '/');
    }
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (string) ($_SERVER['SERVER_PORT'] ?? '') === '443';
    $scheme = $https ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $base = app_base_path();

    return $scheme . '://' . $host . $base;
}

function mail_send_raw(string $to, string $subject, string $bodyText): bool
{
    if (!mail_is_enabled()) {
        return false;
    }
    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    $from = defined('MAIL_FROM') ? MAIL_FROM : 'noreply@localhost';
    $fromName = defined('MAIL_FROM_NAME') ? MAIL_FROM_NAME : 'Mariage';
    $headers = [
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8',
        'Content-Transfer-Encoding: 8bit',
        'From: ' . mb_encode_mimeheader($fromName, 'UTF-8', 'Q') . " <{$from}>",
        'Reply-To: ' . $from,
    ];

    return @mail(
        $to,
        mb_encode_mimeheader($subject, 'UTF-8', 'Q'),
        $bodyText,
        implode("\r\n", $headers)
    );
}

function mail_rsvp_confirmation(
    string $to,
    string $guestName,
    string $status,
    string $bride,
    string $groom,
    string $weddingDateFormatted
): bool {
    $site = mail_site_url();
    if ($status === 'accepted') {
        $subject = 'Confirmation — Nous vous attendons avec joie';
        $body = "Bonjour" . ($guestName !== '' ? " {$guestName}" : '') . ",\n\n";
        $body .= "Merci d'avoir confirmé votre présence au mariage de {$bride} et {$groom}.\n";
        $body .= "Date : {$weddingDateFormatted}\n\n";
        $body .= "Nous avons hâte de partager ce moment avec vous.\n\n";
        $body .= "— {$bride} & {$groom}\n";
        $body .= $site . "\n";
    } elseif ($status === 'declined') {
        $subject = 'Nous avons bien reçu votre réponse';
        $body = "Bonjour" . ($guestName !== '' ? " {$guestName}" : '') . ",\n\n";
        $body .= "Nous avons bien enregistré que vous ne pourrez pas être des nôtres pour le mariage de {$bride} et {$groom}.\n";
        $body .= "Nous espérons vous revoir très bientôt.\n\n";
        $body .= "— {$bride} & {$groom}\n";
        $body .= $site . "\n";
    } else {
        $subject = 'Réponse enregistrée — à confirmer';
        $body = "Bonjour" . ($guestName !== '' ? " {$guestName}" : '') . ",\n\n";
        $body .= "Nous avons bien reçu votre réponse « à confirmer » pour le mariage de {$bride} et {$groom} ({$weddingDateFormatted}).\n";
        $body .= "Vous pourrez choisir un rappel sur la page de confirmation. "
            . "Un petit message vous sera envoyé à la date prévue pour vous inviter à confirmer.\n\n";
        $body .= $site . "\n";
    }

    return mail_send_raw($to, $subject, $body);
}

function mail_reminder_scheduled(string $to, string $guestName, string $whenLabel, string $remindDateFr): bool
{
    $subject = 'Rappel enregistré pour votre invitation';
    $body = "Bonjour" . ($guestName !== '' ? " {$guestName}" : '') . ",\n\n";
    $body .= "Vous avez demandé un rappel {$whenLabel} pour finaliser votre réponse.\n";
    $body .= "Nous vous enverrons un message autour du {$remindDateFr}.\n\n";
    $body .= mail_site_url() . "\n";

    return mail_send_raw($to, $subject, $body);
}

function mail_reminder_due(string $to, string $guestName, string $bride, string $groom, string $weddingDateFormatted): bool
{
    $site = mail_site_url() . '/#rsvp';
    $subject = 'Rappel — Pensez à confirmer votre présence';
    $body = "Bonjour" . ($guestName !== '' ? " {$guestName}" : '') . ",\n\n";
    $body .= "Petit rappel amical : vous souhaitiez confirmer votre présence plus tard "
        . "pour le mariage de {$bride} et {$groom} ({$weddingDateFormatted}).\n\n";
    $body .= "Pour nous envoyer votre réponse : {$site}\n\n";
    $body .= "— {$bride} & {$groom}\n";

    return mail_send_raw($to, $subject, $body);
}
