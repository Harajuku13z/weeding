<?php
/**
 * E-mails : SMTP Hostinger (465 SSL) si configuré, sinon mail() PHP.
 */

declare(strict_types=1);

require_once __DIR__ . '/smtp.php';

function mail_is_enabled(): bool
{
    return defined('MAIL_ENABLED') && MAIL_ENABLED === true;
}

function mail_uses_smtp(): bool
{
    return defined('MAIL_SMTP_HOST')
        && MAIL_SMTP_HOST !== ''
        && defined('MAIL_SMTP_USER')
        && MAIL_SMTP_USER !== '';
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

function mail_ics_escape(string $text): string
{
    return str_replace(
        ["\\", ';', ',', "\n", "\r"],
        ['\\\\', '\;', '\,', '\n', ''],
        $text
    );
}

function mail_wedding_ics(
    string $uid,
    string $summary,
    string $description,
    string $location,
    string $weddingDateYmd,
    string $weddingTimeHi,
    string $eventUrl
): string {
    try {
        $tz = new DateTimeZone('Europe/Paris');
        $start = DateTimeImmutable::createFromFormat('Y-m-d H:i', $weddingDateYmd . ' ' . $weddingTimeHi, $tz);
        if ($start === false) {
            $start = new DateTimeImmutable($weddingDateYmd . ' 15:00', $tz);
        }
        $end = $start->modify('+6 hours');
    } catch (Throwable $e) {
        return '';
    }

    $dtstamp = (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format('Ymd\THis\Z');
    $dtstart = $start->format('Ymd\THis');
    $dtend = $end->format('Ymd\THis');

    $desc = mail_ics_escape($description);
    $loc = mail_ics_escape($location);
    $sum = mail_ics_escape($summary);
    $url = mail_ics_escape($eventUrl);

    return "BEGIN:VCALENDAR\r\n"
        . "VERSION:2.0\r\n"
        . "PRODID:-//LisaLoveChrist//Wedding//FR\r\n"
        . "CALSCALE:GREGORIAN\r\n"
        . "METHOD:PUBLISH\r\n"
        . "BEGIN:VEVENT\r\n"
        . "UID:" . $uid . "\r\n"
        . "DTSTAMP:" . $dtstamp . "\r\n"
        . "DTSTART;TZID=Europe/Paris:" . $dtstart . "\r\n"
        . "DTEND;TZID=Europe/Paris:" . $dtend . "\r\n"
        . "SUMMARY:" . $sum . "\r\n"
        . "DESCRIPTION:" . $desc . "\r\n"
        . ($loc !== '' ? "LOCATION:" . $loc . "\r\n" : '')
        . ($url !== '' ? "URL:" . $url . "\r\n" : '')
        . "END:VEVENT\r\n"
        . "END:VCALENDAR\r\n";
}

function mail_part_base64(string $content, string $contentType): string
{
    return "Content-Type: {$contentType}\r\n"
        . "Content-Transfer-Encoding: base64\r\n\r\n"
        . chunk_split(base64_encode($content), 76, "\r\n");
}

function mail_build_mime_body(string $plain, ?string $html, ?string $icsFilename, ?string $icsRaw): string
{
    $altB = 'alt_' . bin2hex(random_bytes(8));

    $altPart = '--' . $altB . "\r\n" . mail_part_base64($plain, 'text/plain; charset=UTF-8') . "\r\n";
    if ($html !== null && $html !== '') {
        $altPart .= '--' . $altB . "\r\n" . mail_part_base64($html, 'text/html; charset=UTF-8') . "\r\n";
    }
    $altPart .= '--' . $altB . "--\r\n";

    if ($icsRaw === null || $icsRaw === '') {
        return "Content-Type: multipart/alternative; boundary=\"{$altB}\"\r\n\r\n" . $altPart;
    }

    $mixB = 'mix_' . bin2hex(random_bytes(8));
    $fn = preg_replace('/[^a-zA-Z0-9._-]/', '_', $icsFilename ?: 'evenement.ics');

    $icsPart = '--' . $mixB . "\r\n"
        . 'Content-Type: text/calendar; charset=UTF-8; method=PUBLISH; name="' . $fn . "\"\r\n"
        . "Content-Transfer-Encoding: base64\r\n"
        . 'Content-Disposition: attachment; filename="' . $fn . "\"\r\n\r\n"
        . chunk_split(base64_encode($icsRaw), 76, "\r\n");

    return "Content-Type: multipart/mixed; boundary=\"{$mixB}\"\r\n\r\n"
        . '--' . $mixB . "\r\n"
        . "Content-Type: multipart/alternative; boundary=\"{$altB}\"\r\n\r\n"
        . $altPart
        . $icsPart
        . '--' . $mixB . "--\r\n";
}

function mail_deliver(string $to, string $subject, string $bodyText, ?string $bodyHtml = null, ?string $icsFilename = null, ?string $icsRaw = null): bool
{
    if (!mail_is_enabled()) {
        return false;
    }
    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    $from = defined('MAIL_FROM') ? MAIL_FROM : 'noreply@localhost';
    $fromName = defined('MAIL_FROM_NAME') ? MAIL_FROM_NAME : 'Mariage';

    if (mail_uses_smtp()) {
        $port = defined('MAIL_SMTP_PORT') ? (int) MAIL_SMTP_PORT : 465;
        $pass = defined('MAIL_SMTP_PASS') ? MAIL_SMTP_PASS : '';
        $user = MAIL_SMTP_USER;

        $mime = mail_build_mime_body($bodyText, $bodyHtml, $icsFilename, $icsRaw);

        return smtp_send_mail(
            MAIL_SMTP_HOST,
            $port,
            $user,
            $pass,
            $from,
            $fromName,
            $to,
            $subject,
            $mime
        );
    }

    $headers = [
        'MIME-Version: 1.0',
    ];
    if ($icsRaw !== null && $icsRaw !== '') {
        $mime = mail_build_mime_body($bodyText, $bodyHtml, $icsFilename, $icsRaw);
        if (preg_match('/^Content-Type: (.+)\r\n\r\n(.*)/s', $mime, $m)) {
            $headers[] = 'Content-Type: ' . trim($m[1]);
            $body = $m[2];
        } else {
            $headers[] = 'Content-Type: text/plain; charset=UTF-8';
            $body = $bodyText;
        }
    } elseif ($bodyHtml !== null && $bodyHtml !== '') {
        $b = 'b_' . bin2hex(random_bytes(6));
        $headers[] = 'Content-Type: multipart/alternative; boundary="' . $b . '"';
        $body = "--{$b}\r\n"
            . mail_part_base64($bodyText, 'text/plain; charset=UTF-8') . "\r\n"
            . "--{$b}\r\n"
            . mail_part_base64($bodyHtml, 'text/html; charset=UTF-8') . "\r\n"
            . "--{$b}--";
    } else {
        $headers[] = 'Content-Type: text/plain; charset=UTF-8';
        $headers[] = 'Content-Transfer-Encoding: 8bit';
        $body = $bodyText;
    }
    $headers[] = 'From: ' . mb_encode_mimeheader($fromName, 'UTF-8', 'Q') . " <{$from}>";
    if (defined('MAIL_REPLY_TO') && MAIL_REPLY_TO !== '' && filter_var(MAIL_REPLY_TO, FILTER_VALIDATE_EMAIL)) {
        $headers[] = 'Reply-To: ' . MAIL_REPLY_TO;
    } else {
        $headers[] = 'Reply-To: ' . $from;
    }

    return @mail(
        $to,
        mb_encode_mimeheader($subject, 'UTF-8', 'Q'),
        $body,
        implode("\r\n", $headers)
    );
}

/** @deprecated logique interne — utiliser mail_deliver */
function mail_send_raw(string $to, string $subject, string $bodyText): bool
{
    return mail_deliver($to, $subject, $bodyText, null, null, null);
}

function mail_rsvp_confirmation_html(
    string $guestName,
    string $status,
    string $bride,
    string $groom,
    string $weddingDateFormatted,
    string $siteUrl,
    bool $withCalendarHint
): array {
    $greet = $guestName !== '' ? htmlspecialchars($guestName, ENT_QUOTES, 'UTF-8') : 'vous';
    $br = htmlspecialchars($bride, ENT_QUOTES, 'UTF-8');
    $gr = htmlspecialchars($groom, ENT_QUOTES, 'UTF-8');
    $dateL = htmlspecialchars($weddingDateFormatted, ENT_QUOTES, 'UTF-8');
    $url = htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8');

    if ($status === 'accepted') {
        $title = 'Merci — nous vous attendons avec joie';
        $lead = "Merci d'avoir confirmé votre présence au mariage de <strong>{$br}</strong> et <strong>{$gr}</strong>.";
        $extra = $withCalendarHint
            ? '<p style="margin:20px 0 0;font-size:14px;color:#5a6b7a;">Une pièce jointe <strong>fichier .ics</strong> est jointe à ce message : ouvrez-la pour <strong>ajouter la date à votre agenda</strong> (téléphone, Google Agenda, Outlook, Apple Calendrier…).</p>'
            : '';
    } elseif ($status === 'declined') {
        $title = 'Nous avons bien reçu votre réponse';
        $lead = "Nous avons enregistré que vous ne pourrez pas être des nôtres pour le mariage de <strong>{$br}</strong> et <strong>{$gr}</strong>. Nous espérons vous revoir très bientôt.";
        $extra = '';
    } else {
        $title = 'Réponse enregistrée — à confirmer';
        $lead = "Nous avons bien reçu votre réponse « à confirmer » pour le mariage de <strong>{$br}</strong> et <strong>{$gr}</strong> ({$dateL}). Vous pouvez finaliser votre choix et demander un rappel sur le site.";
        $extra = '';
    }

    $html = '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">'
        . '<title>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</title></head>'
        . '<body style="margin:0;padding:0;background:#f4f7fa;font-family:Georgia,\'Times New Roman\',serif;">'
        . '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f4f7fa;padding:24px 12px;">'
        . '<tr><td align="center">'
        . '<table role="presentation" width="100%" style="max-width:560px;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 8px 32px rgba(44,62,80,.08);">'
        . '<tr><td style="padding:28px 32px;background:linear-gradient(135deg,#A8C8E0 0%,#7B9EC4 100%);">'
        . '<p style="margin:0;font-size:11px;letter-spacing:.2em;text-transform:uppercase;color:rgba(255,255,255,.85);">Mariage</p>'
        . '<h1 style="margin:8px 0 0;font-size:24px;font-weight:400;color:#ffffff;line-height:1.25;">' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h1></td></tr>'
        . '<tr><td style="padding:28px 32px 32px;color:#2C3E50;font-size:16px;line-height:1.65;">'
        . '<p style="margin:0 0 16px;">Bonjour ' . $greet . ',</p>'
        . '<p style="margin:0 0 16px;">' . $lead . '</p>';

    if ($status === 'accepted' || $status === 'maybe') {
        $html .= '<p style="margin:16px 0;padding:14px 18px;background:#f0f6fb;border-left:4px solid #7B9EC4;border-radius:0 8px 8px 0;">'
            . '<strong>Date du mariage</strong><br><span style="font-size:18px;">' . $dateL . '</span></p>';
    }

    $html .= $extra
        . '<p style="margin:24px 0 0;"><a href="' . $url . '" style="display:inline-block;padding:12px 22px;background:#2C3E50;color:#ffffff;text-decoration:none;border-radius:8px;font-size:14px;">Voir le site de l\'invitation</a></p>'
        . '<p style="margin:28px 0 0;font-size:13px;color:#8899a8;">— ' . $br . ' &amp; ' . $gr . '</p>'
        . '</td></tr></table></td></tr></table></body></html>';

    return [$title, $html];
}

function mail_rsvp_confirmation(
    string $to,
    string $guestName,
    string $status,
    string $bride,
    string $groom,
    string $weddingDateFormatted,
    string $weddingDateYmd,
    string $weddingTimeHi,
    string $ceremonyLocation,
    string $icsUid
): bool {
    $site = mail_site_url();
    [$subject, $html] = mail_rsvp_confirmation_html(
        $guestName,
        $status,
        $bride,
        $groom,
        $weddingDateFormatted,
        $site,
        $status === 'accepted'
    );

    if ($status === 'accepted') {
        $body = "Bonjour" . ($guestName !== '' ? " {$guestName}" : '') . ",\n\n"
            . "Merci d'avoir confirmé votre présence au mariage de {$bride} et {$groom}.\n"
            . "Date : {$weddingDateFormatted}\n\n"
            . "Un fichier calendrier (.ics) est joint à cet e-mail : ouvrez-le pour ajouter l'événement à votre agenda.\n\n"
            . "— {$bride} & {$groom}\n"
            . $site . "\n";
    } elseif ($status === 'declined') {
        $body = "Bonjour" . ($guestName !== '' ? " {$guestName}" : '') . ",\n\n"
            . "Nous avons bien enregistré que vous ne pourrez pas être des nôtres pour le mariage de {$bride} et {$groom}.\n"
            . "Nous espérons vous revoir très bientôt.\n\n"
            . "— {$bride} & {$groom}\n"
            . $site . "\n";
    } else {
        $body = "Bonjour" . ($guestName !== '' ? " {$guestName}" : '') . ",\n\n"
            . "Nous avons bien reçu votre réponse « à confirmer » pour le mariage de {$bride} et {$groom} ({$weddingDateFormatted}).\n"
            . "Vous pourrez choisir un rappel sur la page de confirmation.\n\n"
            . $site . "\n";
    }

    $ics = '';
    $icsFn = null;
    if ($status === 'accepted' && $weddingDateYmd !== '') {
        $ics = mail_wedding_ics(
            $icsUid,
            "Mariage de {$bride} & {$groom}",
            "Mariage de {$bride} et {$groom}. Infos et RSVP : {$site}",
            $ceremonyLocation,
            $weddingDateYmd,
            $weddingTimeHi,
            $site
        );
        $icsFn = 'mariage-' . preg_replace('/[^a-z0-9]+/i', '-', $bride . '-' . $groom) . '.ics';
    }

    return mail_deliver($to, $subject, $body, $html, $icsFn, $ics !== '' ? $ics : null);
}

function mail_simple_html_body(string $plainText): string
{
    $escaped = '';
    foreach (preg_split("/\r\n|\n|\r/", $plainText) as $line) {
        $escaped .= htmlspecialchars($line, ENT_QUOTES, 'UTF-8') . "<br>\n";
    }

    return '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"></head>'
        . '<body style="margin:16px;font-family:Georgia,serif;font-size:16px;color:#2C3E50;line-height:1.6;">'
        . $escaped
        . '</body></html>';
}

function mail_reminder_scheduled(string $to, string $guestName, string $whenLabel, string $remindDateFr): bool
{
    $subject = 'Rappel enregistré pour votre invitation';
    $body = "Bonjour" . ($guestName !== '' ? " {$guestName}" : '') . ",\n\n"
        . "Vous avez demandé un rappel {$whenLabel} pour finaliser votre réponse.\n"
        . "Nous vous enverrons un message autour du {$remindDateFr}.\n\n"
        . mail_site_url() . "\n";

    return mail_deliver($to, $subject, $body, mail_simple_html_body($body), null, null);
}

function mail_reminder_due(string $to, string $guestName, string $bride, string $groom, string $weddingDateFormatted): bool
{
    $site = mail_site_url() . '/#rsvp';
    $subject = 'Rappel — Pensez à confirmer votre présence';
    $body = "Bonjour" . ($guestName !== '' ? " {$guestName}" : '') . ",\n\n"
        . "Petit rappel amical : vous souhaitiez confirmer votre présence plus tard "
        . "pour le mariage de {$bride} et {$groom} ({$weddingDateFormatted}).\n\n"
        . "Pour nous envoyer votre réponse : {$site}\n\n"
        . "— {$bride} & {$groom}\n";

    return mail_deliver($to, $subject, $body, mail_simple_html_body($body), null, null);
}
