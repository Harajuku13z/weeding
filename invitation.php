<?php
/**
 * Ancien lien invitation.php?code=… → renvoie vers l’accueil (hero) avec le faire-part en modale.
 */
require_once __DIR__ . '/config.php';

$code = strtoupper(trim($_GET['code'] ?? ''));
if ($code === '') {
    header('Location: ' . app_url(''));
    exit;
}

$stmt = db()->prepare('SELECT id FROM guests WHERE code = :c LIMIT 1');
$stmt->execute(['c' => $code]);
if (!$stmt->fetch()) {
    header('Location: ' . app_url(''));
    exit;
}

$bp = app_base_path();
$prefix = ($bp === '' ? '/' : $bp . '/');
$target = $prefix . '?invite=' . rawurlencode($code) . '&skip_intro=1';
header('Location: ' . $target, true, 302);
exit;
