<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/mail.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Méthode non autorisée.'], 405);
}

$guestId   = (int) ($_POST['guest_id'] ?? 0);
$delayDays = (int) ($_POST['delay_days'] ?? 0);

if (!$guestId || !in_array($delayDays, [7, 14, 30])) {
    jsonResponse(['success' => false, 'message' => 'Données invalides.']);
}

$pdo = db();

$row = $pdo->prepare("SELECT id, email, name FROM guests WHERE id = :id LIMIT 1");
$row->execute(['id' => $guestId]);
$guest = $row->fetch();

if (!$guest) {
    jsonResponse(['success' => false, 'message' => 'Invité non trouvé.']);
}

$remindAt = date('Y-m-d', strtotime("+{$delayDays} days"));
$remindFr = date('d/m/Y', strtotime($remindAt));

$existing = $pdo->prepare("SELECT id FROM reminders WHERE guest_id = :gid LIMIT 1");
$existing->execute(['gid' => $guestId]);

if ($existing->fetch()) {
    $stmt = $pdo->prepare("UPDATE reminders SET delay_days = :d, remind_at = :r, sent = 0 WHERE guest_id = :gid");
    $stmt->execute(['d' => $delayDays, 'r' => $remindAt, 'gid' => $guestId]);
} else {
    $stmt = $pdo->prepare("INSERT INTO reminders (guest_id, delay_days, remind_at) VALUES (:gid, :d, :r)");
    $stmt->execute(['gid' => $guestId, 'd' => $delayDays, 'r' => $remindAt]);
}

$labels = [7 => 'dans une semaine', 14 => 'dans deux semaines', 30 => 'dans un mois'];
$when = $labels[$delayDays] ?? ($delayDays . ' jours');

if (($guest['email'] ?? '') !== '' && filter_var($guest['email'], FILTER_VALIDATE_EMAIL)) {
    mail_reminder_scheduled($guest['email'], (string) $guest['name'], $when, $remindFr);
}

jsonResponse([
    'success' => true,
    'message' => 'Rappel enregistré pour ' . ($labels[$delayDays] ?? $delayDays . ' jours') . '.',
]);
