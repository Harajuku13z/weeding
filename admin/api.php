<?php
require_once __DIR__ . '/../config.php';

if (!isAdmin()) {
    jsonResponse(['success' => false, 'message' => 'Non autorisé.'], 401);
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$pdo = db();

switch ($action) {

    /* ─── GALLERY ──────────────────────────────────────── */
    case 'gallery_list':
        $rows = $pdo->query("SELECT * FROM gallery ORDER BY sort_order ASC, id DESC")->fetchAll();
        jsonResponse(['success' => true, 'data' => $rows]);
        break;

    case 'gallery_upload':
        if (empty($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            jsonResponse(['success' => false, 'message' => 'Aucun fichier reçu.']);
        }
        $file = $_FILES['image'];
        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (!in_array($file['type'], $allowed)) {
            jsonResponse(['success' => false, 'message' => 'Format non supporté (JPG, PNG, WEBP, GIF).']);
        }
        if ($file['size'] > 5 * 1024 * 1024) {
            jsonResponse(['success' => false, 'message' => 'Fichier trop lourd (max 5 Mo).']);
        }
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $name = uniqid('img_') . '.' . strtolower($ext);
        $dest = UPLOAD_DIR . $name;
        if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0775, true);
        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            jsonResponse(['success' => false, 'message' => 'Erreur lors de l\'upload.']);
        }
        $caption = sanitize($_POST['caption'] ?? '');
        $stmt = $pdo->prepare("INSERT INTO gallery (filename, caption) VALUES (:f, :c)");
        $stmt->execute(['f' => $name, 'c' => $caption]);
        jsonResponse(['success' => true, 'message' => 'Image ajoutée.', 'id' => $pdo->lastInsertId()]);
        break;

    case 'gallery_delete':
        $id = (int) ($_POST['id'] ?? 0);
        $row = $pdo->prepare("SELECT filename FROM gallery WHERE id = :id");
        $row->execute(['id' => $id]);
        $img = $row->fetch();
        if ($img) {
            @unlink(UPLOAD_DIR . $img['filename']);
            $pdo->prepare("DELETE FROM gallery WHERE id = :id")->execute(['id' => $id]);
        }
        jsonResponse(['success' => true, 'message' => 'Image supprimée.']);
        break;

    /* ─── GUESTS ───────────────────────────────────────── */
    case 'guests_list':
        $rows = $pdo->query("SELECT * FROM guests ORDER BY responded_at DESC, id DESC")->fetchAll();
        jsonResponse(['success' => true, 'data' => $rows]);
        break;

    case 'guest_add':
        $code = strtoupper(trim($_POST['code'] ?? ''));
        $name = sanitize($_POST['name'] ?? '');
        if (empty($code)) jsonResponse(['success' => false, 'message' => 'Code requis.']);
        $stmt = $pdo->prepare("INSERT IGNORE INTO guests (code, name) VALUES (:c, :n)");
        $stmt->execute(['c' => $code, 'n' => $name]);
        if ($stmt->rowCount() === 0) {
            jsonResponse(['success' => false, 'message' => 'Ce code existe déjà.']);
        }
        jsonResponse(['success' => true, 'message' => 'Invité ajouté.']);
        break;

    case 'guest_delete':
        $id = (int) ($_POST['id'] ?? 0);
        $pdo->prepare("DELETE FROM guests WHERE id = :id")->execute(['id' => $id]);
        jsonResponse(['success' => true, 'message' => 'Invité supprimé.']);
        break;

    /* ─── SETTINGS ─────────────────────────────────────── */
    case 'settings_get':
        $rows = $pdo->query("SELECT skey, svalue FROM settings")->fetchAll();
        $out = [];
        foreach ($rows as $r) $out[$r['skey']] = $r['svalue'];
        jsonResponse(['success' => true, 'data' => $out]);
        break;

    case 'settings_save':
        $fields = ['bride_name', 'groom_name', 'wedding_date', 'hero_subtitle', 'quote', 'story_text'];
        $stmt = $pdo->prepare("INSERT INTO settings (skey, svalue) VALUES (:k, :v) ON DUPLICATE KEY UPDATE svalue = :v2");
        foreach ($fields as $f) {
            if (isset($_POST[$f])) {
                $val = trim($_POST[$f]);
                $stmt->execute(['k' => $f, 'v' => $val, 'v2' => $val]);
            }
        }
        jsonResponse(['success' => true, 'message' => 'Paramètres sauvegardés.']);
        break;

    default:
        jsonResponse(['success' => false, 'message' => 'Action inconnue.'], 400);
}
