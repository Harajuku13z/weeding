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

function mail_deliver_php(string $to, string $subject, string $from, string $fromName, string $bodyText, ?string $bodyHtml, ?string $icsFilename, ?string $icsRaw): bool
{
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

    $hdr = implode("\r\n", $headers);
    $extra = '';
    if (filter_var($from, FILTER_VALIDATE_EMAIL)) {
        $extra = '-f' . $from;
    }

    return @mail(
        $to,
        mb_encode_mimeheader($subject, 'UTF-8', 'Q'),
        $body,
        $hdr,
        $extra
    );
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

    $mime = mail_build_mime_body($bodyText, $bodyHtml, $icsFilename, $icsRaw);

    if (mail_uses_smtp()) {
        $port = defined('MAIL_SMTP_PORT') ? (int) MAIL_SMTP_PORT : 465;
        $pass = defined('MAIL_SMTP_PASS') ? MAIL_SMTP_PASS : '';
        $user = MAIL_SMTP_USER;

        $ok = smtp_send_mail(
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
        if ($ok) {
            return true;
        }
        if (!defined('MAIL_SMTP_FALLBACK_PHP_MAIL') || MAIL_SMTP_FALLBACK_PHP_MAIL !== false) {
            mail_smtp_log('Secours : envoi via mail() PHP');

            return mail_deliver_php($to, $subject, $from, $fromName, $bodyText, $bodyHtml, $icsFilename, $icsRaw);
        }

        return false;
    }

    return mail_deliver_php($to, $subject, $from, $fromName, $bodyText, $bodyHtml, $icsFilename, $icsRaw);
}

/** @deprecated logique interne — utiliser mail_deliver */
function mail_send_raw(string $to, string $subject, string $bodyText): bool
{
    return mail_deliver($to, $subject, $bodyText, null, null, null);
}

function mail_format_time_fr(string $hi): string
{
    if (preg_match('/^(\d{1,2}):(\d{2})$/', $hi, $m)) {
        return (int) $m[1] . 'h' . $m[2];
    }

    return $hi;
}

/**
 * @return array{0: string, 1: string} [subject, html]
 */
function mail_rsvp_confirmation_html(
    string $guestName,
    string $status,
    string $bride,
    string $groom,
    string $weddingDateFormatted,
    string $weddingTimeHi,
    string $ceremonyLocation,
    string $siteUrl,
    bool $withCalendarHint,
    string $rsvpDeadline,
    int $companions,
    string $dietaryNote
): array {
    $greet = $guestName !== '' ? htmlspecialchars($guestName, ENT_QUOTES, 'UTF-8') : 'vous';
    $br = htmlspecialchars($bride, ENT_QUOTES, 'UTF-8');
    $gr = htmlspecialchars($groom, ENT_QUOTES, 'UTF-8');
    $dateL = htmlspecialchars($weddingDateFormatted, ENT_QUOTES, 'UTF-8');
    $timeFr = htmlspecialchars(mail_format_time_fr($weddingTimeHi), ENT_QUOTES, 'UTF-8');
    $loc = trim($ceremonyLocation) !== '' ? htmlspecialchars($ceremonyLocation, ENT_QUOTES, 'UTF-8') : '';
    $url = htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8');
    $deadlineH = trim($rsvpDeadline) !== '' ? htmlspecialchars($rsvpDeadline, ENT_QUOTES, 'UTF-8') : '';

    if ($status === 'accepted') {
        $title = 'Confirmation — Nous vous attendons avec joie';
        $preheader = 'Votre présence au mariage de ' . $bride . ' et ' . $groom . ' est bien enregistrée.';
        $badgeLabel = 'Présence confirmée';
        $badgeSub = 'Nous avons bien enregistré votre réponse « oui ».';
        $boxBg = '#e8f5e9';
        $boxBorder = '#43a047';
        $badgeColor = '#2e7d32';
        $lead = "Merci infiniment : nous avons le plaisir de vous compter parmi nous pour célébrer le mariage de <strong>{$br}</strong> et <strong>{$gr}</strong>.";
        $extra = $withCalendarHint
            ? '<p style="margin:0;font-size:14px;color:#5a6b7a;line-height:1.6;border-top:1px solid #e0e0e0;padding-top:20px;">'
            . '<strong style="color:#2C3E50;">Ajouter à votre agenda</strong><br>'
            . 'Une pièce jointe <strong>fichier .ics</strong> est jointe : ouvrez-la pour enregistrer la date dans votre calendrier (Google, Outlook, Apple…).</p>'
            : '';
    } elseif ($status === 'declined') {
        $title = 'Confirmation de votre réponse';
        $preheader = 'Nous avons bien reçu votre réponse pour le mariage de ' . $bride . ' et ' . $groom . '.';
        $badgeLabel = 'Réponse enregistrée';
        $badgeSub = 'Vous nous avez indiqué ne pas pouvoir être des nôtres ce jour-là.';
        $boxBg = '#fce4ec';
        $boxBorder = '#d81b60';
        $badgeColor = '#ad1457';
        $lead = "Nous avons bien pris note que vous ne pourrez pas être présent(e) au mariage de <strong>{$br}</strong> et <strong>{$gr}</strong>. Merci de nous avoir prévenus ; nous espérons vous revoir très bientôt.";
        $extra = '';
    } else {
        $title = 'Confirmation — Réponse « à confirmer »';
        $preheader = 'Votre réponse pour le mariage de ' . $bride . ' et ' . $groom . ' est enregistrée ; finalisez sur le site si besoin.';
        $badgeLabel = 'Réponse en attente';
        $badgeSub = 'Vous avez choisi « à confirmer » — vous pourrez préciser ou modifier sur le site.';
        $boxBg = '#fff8e1';
        $boxBorder = '#ff8f00';
        $badgeColor = '#e65100';
        $lead = "Nous avons bien enregistré votre réponse « à confirmer » pour le mariage de <strong>{$br}</strong> et <strong>{$gr}</strong>. "
            . 'Pensez à revenir sur le site lorsque vous aurez tranché ; vous pouvez aussi y demander un rappel par e-mail.';
        $extra = '';
    }

    $detailRows = '<tr><td style="padding:10px 0;border-bottom:1px solid #eceff1;font-size:14px;color:#546e7a;width:36%;vertical-align:top;">Date</td>'
        . '<td style="padding:10px 0;border-bottom:1px solid #eceff1;font-size:16px;color:#2C3E50;font-weight:600;vertical-align:top;">' . $dateL . '</td></tr>'
        . '<tr><td style="padding:10px 0;border-bottom:1px solid #eceff1;font-size:14px;color:#546e7a;vertical-align:top;">Heure</td>'
        . '<td style="padding:10px 0;border-bottom:1px solid #eceff1;font-size:16px;color:#2C3E50;vertical-align:top;">' . $timeFr . '</td></tr>';
    if ($loc !== '') {
        $detailRows .= '<tr><td style="padding:10px 0;border-bottom:1px solid #eceff1;font-size:14px;color:#546e7a;vertical-align:top;">Lieu</td>'
            . '<td style="padding:10px 0;border-bottom:1px solid #eceff1;font-size:16px;color:#2C3E50;vertical-align:top;">' . $loc . '</td></tr>';
    }

    $guestExtra = '';
    if ($companions > 0) {
        $guestExtra .= '<tr><td style="padding:10px 0;border-bottom:1px solid #eceff1;font-size:14px;color:#546e7a;vertical-align:top;">Invités indiqués</td>'
            . '<td style="padding:10px 0;border-bottom:1px solid #eceff1;font-size:15px;color:#2C3E50;vertical-align:top;">'
            . htmlspecialchars((string) $companions, ENT_QUOTES, 'UTF-8') . ' accompagnant(s)</td></tr>';
    }
    if (trim($dietaryNote) !== '') {
        $dn = htmlspecialchars($dietaryNote, ENT_QUOTES, 'UTF-8');
        $guestExtra .= '<tr><td style="padding:10px 0;border-bottom:1px solid #eceff1;font-size:14px;color:#546e7a;vertical-align:top;">Message / repas</td>'
            . '<td style="padding:10px 0;border-bottom:1px solid #eceff1;font-size:15px;color:#2C3E50;vertical-align:top;">' . $dn . '</td></tr>';
    }

    $deadlineBlock = '';
    if ($deadlineH !== '') {
        $deadlineBlock = '<p style="margin:20px 0 0;font-size:13px;color:#78909c;line-height:1.5;">'
            . '<strong style="color:#546e7a;">Date limite RSVP</strong> (indicative) : ' . $deadlineH . '</p>';
    }

    $html = '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">'
        . '<meta http-equiv="X-UA-Compatible" content="IE=edge">'
        . '<title>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</title></head>'
        . '<body style="margin:0;padding:0;background:#eef2f6;font-family:\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif;">'
        . '<div style="display:none;max-height:0;overflow:hidden;mso-hide:all;">' . htmlspecialchars($preheader, ENT_QUOTES, 'UTF-8') . '</div>'
        . '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#eef2f6;padding:28px 14px;">'
        . '<tr><td align="center">'
        . '<table role="presentation" width="100%" style="max-width:600px;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 12px 40px rgba(44,62,80,.1);border:1px solid #e8ecf1;">'
        . '<tr><td style="padding:32px 36px 28px;background:linear-gradient(145deg,#A8C8E0 0%,#7B9EC4 100%);text-align:center;">'
        . '<p style="margin:0 0 8px;font-size:10px;letter-spacing:.28em;text-transform:uppercase;color:rgba(255,255,255,.9);font-weight:600;">Invitation mariage</p>'
        . '<p style="margin:0;font-family:Georgia,\'Times New Roman\',serif;font-size:28px;font-weight:400;color:#ffffff;line-height:1.2;">' . $br . ' <span style="font-weight:300;opacity:.9;">&amp;</span> ' . $gr . '</p>'
        . '</td></tr>'
        . '<tr><td style="padding:28px 36px 8px;">'
        . '<p style="margin:0 0 20px;font-size:17px;color:#2C3E50;line-height:1.6;">Bonjour ' . $greet . ',</p>'
        . '<div style="background:' . $boxBg . ';border-left:4px solid ' . $boxBorder . ';border-radius:0 12px 12px 0;padding:18px 20px;margin-bottom:22px;">'
        . '<p style="margin:0 0 6px;font-size:11px;letter-spacing:.12em;text-transform:uppercase;font-weight:700;color:' . $badgeColor . ';">Confirmation</p>'
        . '<p style="margin:0 0 4px;font-size:20px;font-weight:700;color:#2C3E50;line-height:1.3;">' . htmlspecialchars($badgeLabel, ENT_QUOTES, 'UTF-8') . '</p>'
        . '<p style="margin:0;font-size:14px;color:#455a64;line-height:1.55;">' . htmlspecialchars($badgeSub, ENT_QUOTES, 'UTF-8') . '</p>'
        . '</div>'
        . '<p style="margin:0 0 22px;font-size:15px;color:#37474f;line-height:1.65;">' . $lead . '</p>'
        . '<p style="margin:0 0 10px;font-size:11px;letter-spacing:.14em;text-transform:uppercase;font-weight:600;color:#90a4ae;">Détails de l\'événement</p>'
        . '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-bottom:8px;">'
        . $detailRows . $guestExtra
        . '</table>'
        . $deadlineBlock
        . '</td></tr>'
        . '<tr><td style="padding:8px 36px 32px;">' . $extra
        . '<p style="margin:24px 0 0;text-align:center;">'
        . '<a href="' . $url . '" style="display:inline-block;padding:14px 28px;background:#2C3E50;color:#ffffff !important;text-decoration:none;border-radius:10px;font-size:14px;font-weight:600;letter-spacing:.03em;">Voir l\'invitation et le programme</a></p>'
        . '<p style="margin:24px 0 0;font-size:13px;color:#b0bec5;text-align:center;line-height:1.5;">Cet e-mail confirme les informations transmises via le formulaire RSVP du site.<br>'
        . 'Pour toute question, répondez à ce message ou contactez les futurs mariés.</p>'
        . '<p style="margin:18px 0 0;font-size:14px;color:#78909c;text-align:center;font-family:Georgia,serif;font-style:italic;">— ' . $br . ' &amp; ' . $gr . '</p>'
        . '</td></tr>'
        . '</table></td></tr></table></body></html>';

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
    string $icsUid,
    string $rsvpDeadline = '',
    int $companions = 0,
    string $dietaryNote = ''
): bool {
    $site = mail_site_url();
    [$subject, $html] = mail_rsvp_confirmation_html(
        $guestName,
        $status,
        $bride,
        $groom,
        $weddingDateFormatted,
        $weddingTimeHi,
        $ceremonyLocation,
        $site,
        $status === 'accepted',
        $rsvpDeadline,
        $companions,
        $dietaryNote
    );

    $timeFr = mail_format_time_fr($weddingTimeHi);
    $locBlock = trim($ceremonyLocation) !== '' ? "\nLieu : {$ceremonyLocation}\n" : "\n";
    $deadLineTxt = trim($rsvpDeadline) !== '' ? "Date limite RSVP (indicative) : {$rsvpDeadline}\n" : '';
    $compTxt = $companions > 0 ? "Accompagnants indiqués : {$companions}\n" : '';
    $dietTxt = trim($dietaryNote) !== '' ? "Message / repas : {$dietaryNote}\n" : '';

    if ($status === 'accepted') {
        $body = "Bonjour" . ($guestName !== '' ? " {$guestName}" : '') . ",\n\n"
            . "CONFIRMATION — Présence confirmée\n\n"
            . "Merci d'avoir confirmé votre présence au mariage de {$bride} et {$groom}.\n\n"
            . "Date : {$weddingDateFormatted}\n"
            . "Heure : {$timeFr}" . $locBlock
            . $compTxt . $dietTxt . $deadLineTxt . "\n"
            . "Un fichier calendrier (.ics) est joint : ouvrez-le pour ajouter l'événement à votre agenda.\n\n"
            . "Site : {$site}\n\n"
            . "— {$bride} & {$groom}\n";
    } elseif ($status === 'declined') {
        $body = "Bonjour" . ($guestName !== '' ? " {$guestName}" : '') . ",\n\n"
            . "CONFIRMATION — Réponse enregistrée\n\n"
            . "Nous avons bien enregistré que vous ne pourrez pas être des nôtres pour le mariage de {$bride} et {$groom}.\n\n"
            . "Date de l'événement : {$weddingDateFormatted}\n"
            . "Heure : {$timeFr}" . $locBlock
            . $deadLineTxt . "\n"
            . "Site : {$site}\n\n"
            . "— {$bride} & {$groom}\n";
    } else {
        $body = "Bonjour" . ($guestName !== '' ? " {$guestName}" : '') . ",\n\n"
            . "CONFIRMATION — Réponse « à confirmer »\n\n"
            . "Nous avons bien enregistré votre réponse pour le mariage de {$bride} et {$groom}.\n\n"
            . "Date : {$weddingDateFormatted}\n"
            . "Heure : {$timeFr}" . $locBlock
            . $compTxt . $dietTxt . $deadLineTxt . "\n"
            . "Finalisez ou modifiez votre choix sur le site : {$site}\n\n"
            . "— {$bride} & {$groom}\n";
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
