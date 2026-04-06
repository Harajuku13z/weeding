<?php
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Méthode non autorisée.'], 405);
}

$code     = strtoupper(trim($_POST['code'] ?? ''));
$status   = $_POST['status'] ?? '';
$companions = (int) ($_POST['companions'] ?? 0);
$dietary  = trim($_POST['dietary'] ?? '');
$message  = trim($_POST['message'] ?? '');

if (empty($code) || strlen($code) < 4 || strlen($code) > 20) {
    jsonResponse(['success' => false, 'message' => 'Code d\'invitation invalide.']);
}

if (!in_array($status, ['accepted', 'maybe', 'declined'])) {
    jsonResponse(['success' => false, 'message' => 'Veuillez sélectionner votre réponse.']);
}

if ($companions < 0 || $companions > 10) {
    $companions = 0;
}

$pdo = db();

$stmt = $pdo->prepare("SELECT id, status FROM guests WHERE code = :c LIMIT 1");
$stmt->execute(['c' => $code]);
$guest = $stmt->fetch();

if (!$guest) {
    jsonResponse(['success' => false, 'message' => 'Ce code d\'invitation n\'existe pas. Vérifiez et réessayez.']);
}

$update = $pdo->prepare("UPDATE guests SET status = :s, companions = :comp, dietary = :d, message = :m, responded_at = NOW() WHERE id = :id");
$update->execute([
    's'    => $status,
    'comp' => $companions,
    'd'    => sanitize($dietary),
    'm'    => sanitize($message),
    'id'   => $guest['id'],
]);

$labels = ['accepted' => 'acceptée', 'maybe' => 'en attente', 'declined' => 'déclinée'];
jsonResponse([
    'success'  => true,
    'guest_id' => $guest['id'],
    'message'  => 'Merci ! Votre réponse (' . ($labels[$status] ?? $status) . ') a bien été enregistrée.'
]);
