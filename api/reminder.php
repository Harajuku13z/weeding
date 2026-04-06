<?php
require_once __DIR__ . '/../config.php';

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

$check = $pdo->prepare("SELECT id FROM guests WHERE id = :id LIMIT 1");
$check->execute(['id' => $guestId]);
if (!$check->fetch()) {
    jsonResponse(['success' => false, 'message' => 'Invité non trouvé.']);
}

$remindAt = date('Y-m-d', strtotime("+{$delayDays} days"));

$existing = $pdo->prepare("SELECT id FROM reminders WHERE guest_id = :gid LIMIT 1");
$existing->execute(['gid' => $guestId]);

if ($existing->fetch()) {
    $stmt = $pdo->prepare("UPDATE reminders SET delay_days = :d, remind_at = :r, sent = 0 WHERE guest_id = :gid");
    $stmt->execute(['d' => $delayDays, 'r' => $remindAt, 'gid' => $guestId]);
} else {
    $stmt = $pdo->prepare("INSERT INTO reminders (guest_id, delay_days, remind_at) VALUES (:gid, :d, :r)");
    $stmt->execute(['gid' => $guestId, 'd' => $delayDays, 'r' => $remindAt]);
}

$labels = [7 => '1 semaine', 14 => '2 semaines', 30 => '1 mois'];
jsonResponse([
    'success' => true,
    'message' => 'Rappel enregistré pour dans ' . ($labels[$delayDays] ?? $delayDays . ' jours') . '.'
]);
