<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/mail.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Méthode non autorisée.'], 405);
}

$code       = strtoupper(trim($_POST['code'] ?? ''));
$status     = $_POST['status'] ?? '';
$companions = (int) ($_POST['companions'] ?? 0);
$dietary    = trim($_POST['dietary'] ?? '');
$message    = trim($_POST['message'] ?? '');
$emailRaw   = strtolower(trim($_POST['email'] ?? ''));

if (empty($code) || strlen($code) < 4 || strlen($code) > 20) {
    jsonResponse(['success' => false, 'message' => 'Code d\'invitation invalide.']);
}

if (!in_array($status, ['accepted', 'maybe', 'declined'])) {
    jsonResponse(['success' => false, 'message' => 'Veuillez sélectionner votre réponse.']);
}

if ($emailRaw !== '' && !filter_var($emailRaw, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(['success' => false, 'message' => 'Adresse e-mail invalide.']);
}

if ($companions < 0 || $companions > 10) {
    $companions = 0;
}

$pdo = db();

$stmt = $pdo->prepare("SELECT id, name, email FROM guests WHERE code = :c LIMIT 1");
$stmt->execute(['c' => $code]);
$guest = $stmt->fetch();

if (!$guest) {
    jsonResponse(['success' => false, 'message' => 'Ce code d\'invitation n\'existe pas. Vérifiez et réessayez.']);
}

$emailSave = $emailRaw !== '' ? $emailRaw : ($guest['email'] ?? '');

$update = $pdo->prepare("UPDATE guests SET status = :s, companions = :comp, dietary = :d, message = :m, email = :e, responded_at = NOW() WHERE id = :id");
$update->execute([
    's'    => $status,
    'comp' => $companions,
    'd'    => sanitize($dietary),
    'm'    => sanitize($message),
    'e'    => $emailSave,
    'id'   => $guest['id'],
]);

$settings = [];
foreach ($pdo->query("SELECT skey, svalue FROM settings")->fetchAll() as $r) {
    $settings[$r['skey']] = $r['svalue'];
}
$bride = $settings['bride_name'] ?? '';
$groom = $settings['groom_name'] ?? '';
$w    = $settings['wedding_date'] ?? '';
$wFmt = format_date_fr($w);

if ($emailSave !== '' && filter_var($emailSave, FILTER_VALIDATE_EMAIL)) {
    mail_rsvp_confirmation($emailSave, (string) $guest['name'], $status, $bride, $groom, $wFmt);
}

$labels = ['accepted' => 'acceptée', 'maybe' => 'en attente', 'declined' => 'déclinée'];
jsonResponse([
    'success'  => true,
    'guest_id' => $guest['id'],
    'message'  => 'Merci ! Votre réponse (' . ($labels[$status] ?? $status) . ') a bien été enregistrée.',
]);
