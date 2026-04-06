<?php
/**
 * À planifier 1× par jour (cron Hostinger) :
 * php /home/.../public_html/cron_reminders.php
 * ou en HTTP : https://votredomaine.fr/cron_reminders.php?key=VOTRE_CRON_SECRET
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/mail.php';

header('Content-Type: text/plain; charset=utf-8');

if (php_sapi_name() !== 'cli') {
    $key = $_GET['key'] ?? '';
    if (!defined('CRON_SECRET') || CRON_SECRET === '' || !hash_equals(CRON_SECRET, $key)) {
        http_response_code(403);
        echo 'Forbidden';
        exit;
    }
}

if (!mail_is_enabled()) {
    echo "MAIL_ENABLED = false — rien envoyé.\n";
    exit(0);
}

$pdo = db();
$settings = [];
foreach ($pdo->query("SELECT skey, svalue FROM settings")->fetchAll() as $r) {
    $settings[$r['skey']] = $r['svalue'];
}
$bride = $settings['bride_name'] ?? '';
$groom = $settings['groom_name'] ?? '';
$w = $settings['wedding_date'] ?? '';
$wFmt = format_date_fr($w);

$sql = "SELECT r.id AS rid, r.guest_id, g.email, g.name
        FROM reminders r
        INNER JOIN guests g ON g.id = r.guest_id
        WHERE r.sent = 0 AND r.remind_at <= CURDATE() AND g.email != '' AND g.status = 'maybe'";
$rows = $pdo->query($sql)->fetchAll();

$sent = 0;
foreach ($rows as $row) {
    $ok = mail_reminder_due(
        $row['email'],
        (string) $row['name'],
        $bride,
        $groom,
        $wFmt
    );
    if ($ok) {
        $pdo->prepare("UPDATE reminders SET sent = 1 WHERE id = :id")->execute(['id' => $row['rid']]);
        $sent++;
    }
}

echo "Rappels envoyés : {$sent}\n";
