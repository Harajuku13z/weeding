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

    /* ─── PROGRAMME ────────────────────────────────────── */
    case 'programme_list':
        $rows = $pdo->query("SELECT * FROM programme ORDER BY sort_order ASC, id ASC")->fetchAll();
        jsonResponse(['success' => true, 'data' => $rows]);
        break;

    case 'programme_add':
        $time  = sanitize($_POST['time_label'] ?? '');
        $title = sanitize($_POST['title'] ?? '');
        $desc  = sanitize($_POST['description'] ?? '');
        $order = (int) ($_POST['sort_order'] ?? 0);
        if (empty($time) || empty($title)) jsonResponse(['success' => false, 'message' => 'Heure et titre requis.']);
        $stmt = $pdo->prepare("INSERT INTO programme (time_label, title, description, sort_order) VALUES (:t, :ti, :d, :s)");
        $stmt->execute(['t' => $time, 'ti' => $title, 'd' => $desc, 's' => $order]);
        jsonResponse(['success' => true, 'message' => 'Élément ajouté au programme.']);
        break;

    case 'programme_update':
        $id    = (int) ($_POST['id'] ?? 0);
        $time  = sanitize($_POST['time_label'] ?? '');
        $title = sanitize($_POST['title'] ?? '');
        $desc  = sanitize($_POST['description'] ?? '');
        $order = (int) ($_POST['sort_order'] ?? 0);
        if (!$id || empty($time) || empty($title)) jsonResponse(['success' => false, 'message' => 'Données manquantes.']);
        $stmt = $pdo->prepare("UPDATE programme SET time_label = :t, title = :ti, description = :d, sort_order = :s WHERE id = :id");
        $stmt->execute(['t' => $time, 'ti' => $title, 'd' => $desc, 's' => $order, 'id' => $id]);
        jsonResponse(['success' => true, 'message' => 'Programme mis à jour.']);
        break;

    case 'programme_delete':
        $id = (int) ($_POST['id'] ?? 0);
        $pdo->prepare("DELETE FROM programme WHERE id = :id")->execute(['id' => $id]);
        jsonResponse(['success' => true, 'message' => 'Élément supprimé.']);
        break;

    /* ─── LIEUX ────────────────────────────────────────── */
    case 'lieux_list':
        $rows = $pdo->query("SELECT * FROM lieux ORDER BY sort_order ASC, id ASC")->fetchAll();
        jsonResponse(['success' => true, 'data' => $rows]);
        break;

    case 'lieux_add':
        $name    = sanitize($_POST['name'] ?? '');
        $address = sanitize($_POST['address'] ?? '');
        $mapsUrl = trim($_POST['maps_url'] ?? '');
        $embed   = trim($_POST['maps_embed'] ?? '');
        $icon    = sanitize($_POST['icon'] ?? 'bi-geo-alt-fill');
        $order   = (int) ($_POST['sort_order'] ?? 0);
        if (empty($name)) jsonResponse(['success' => false, 'message' => 'Nom du lieu requis.']);

        $photo = '';
        if (!empty($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['photo'];
            $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            if (!in_array($file['type'], $allowed)) jsonResponse(['success' => false, 'message' => 'Format photo non supporté.']);
            if ($file['size'] > 5 * 1024 * 1024) jsonResponse(['success' => false, 'message' => 'Photo trop lourde (max 5 Mo).']);
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $photo = uniqid('lieu_') . '.' . $ext;
            if (!is_dir(UPLOAD_DIR_LIEUX)) mkdir(UPLOAD_DIR_LIEUX, 0775, true);
            if (!move_uploaded_file($file['tmp_name'], UPLOAD_DIR_LIEUX . $photo)) {
                jsonResponse(['success' => false, 'message' => 'Erreur upload photo.']);
            }
        }

        $stmt = $pdo->prepare("INSERT INTO lieux (name, address, photo, maps_url, maps_embed, icon, sort_order) VALUES (:n, :a, :p, :mu, :me, :ic, :s)");
        $stmt->execute(['n' => $name, 'a' => $address, 'p' => $photo, 'mu' => $mapsUrl, 'me' => $embed, 'ic' => $icon, 's' => $order]);
        jsonResponse(['success' => true, 'message' => 'Lieu ajouté.']);
        break;

    case 'lieux_update':
        $id      = (int) ($_POST['id'] ?? 0);
        $name    = sanitize($_POST['name'] ?? '');
        $address = sanitize($_POST['address'] ?? '');
        $mapsUrl = trim($_POST['maps_url'] ?? '');
        $embed   = trim($_POST['maps_embed'] ?? '');
        $icon    = sanitize($_POST['icon'] ?? 'bi-geo-alt-fill');
        $order   = (int) ($_POST['sort_order'] ?? 0);
        if (!$id || empty($name)) jsonResponse(['success' => false, 'message' => 'Données manquantes.']);

        $photoSql = '';
        $params = ['n' => $name, 'a' => $address, 'mu' => $mapsUrl, 'me' => $embed, 'ic' => $icon, 's' => $order, 'id' => $id];

        if (!empty($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['photo'];
            $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            if (!in_array($file['type'], $allowed)) jsonResponse(['success' => false, 'message' => 'Format photo non supporté.']);
            if ($file['size'] > 5 * 1024 * 1024) jsonResponse(['success' => false, 'message' => 'Photo trop lourde (max 5 Mo).']);
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $newPhoto = uniqid('lieu_') . '.' . $ext;
            if (!is_dir(UPLOAD_DIR_LIEUX)) mkdir(UPLOAD_DIR_LIEUX, 0775, true);
            if (!move_uploaded_file($file['tmp_name'], UPLOAD_DIR_LIEUX . $newPhoto)) {
                jsonResponse(['success' => false, 'message' => 'Erreur upload photo.']);
            }
            $old = $pdo->prepare("SELECT photo FROM lieux WHERE id = :id");
            $old->execute(['id' => $id]);
            $oldRow = $old->fetch();
            if ($oldRow && $oldRow['photo']) @unlink(UPLOAD_DIR_LIEUX . $oldRow['photo']);
            $photoSql = ', photo = :p';
            $params['p'] = $newPhoto;
        }

        $stmt = $pdo->prepare("UPDATE lieux SET name = :n, address = :a, maps_url = :mu, maps_embed = :me, icon = :ic, sort_order = :s{$photoSql} WHERE id = :id");
        $stmt->execute($params);
        jsonResponse(['success' => true, 'message' => 'Lieu mis à jour.']);
        break;

    case 'lieux_delete':
        $id = (int) ($_POST['id'] ?? 0);
        $old = $pdo->prepare("SELECT photo FROM lieux WHERE id = :id");
        $old->execute(['id' => $id]);
        $oldRow = $old->fetch();
        if ($oldRow && $oldRow['photo']) @unlink(UPLOAD_DIR_LIEUX . $oldRow['photo']);
        $pdo->prepare("DELETE FROM lieux WHERE id = :id")->execute(['id' => $id]);
        jsonResponse(['success' => true, 'message' => 'Lieu supprimé.']);
        break;

    /* ─── AMBIANCE PHOTOS ─────────────────────────────── */
    case 'ambiance_photos_list':
        $rows = $pdo->query("SELECT * FROM ambiance_photos ORDER BY sort_order ASC, id DESC")->fetchAll();
        jsonResponse(['success' => true, 'data' => $rows]);
        break;

    case 'ambiance_photo_upload':
        if (empty($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            jsonResponse(['success' => false, 'message' => 'Aucun fichier reçu.']);
        }
        $file = $_FILES['image'];
        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (!in_array($file['type'], $allowed)) jsonResponse(['success' => false, 'message' => 'Format non supporté.']);
        if ($file['size'] > 5 * 1024 * 1024) jsonResponse(['success' => false, 'message' => 'Fichier trop lourd (max 5 Mo).']);
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $name = uniqid('amb_') . '.' . $ext;
        if (!is_dir(UPLOAD_DIR_AMBIANCE)) mkdir(UPLOAD_DIR_AMBIANCE, 0775, true);
        if (!move_uploaded_file($file['tmp_name'], UPLOAD_DIR_AMBIANCE . $name)) {
            jsonResponse(['success' => false, 'message' => 'Erreur upload.']);
        }
        $caption = sanitize($_POST['caption'] ?? '');
        $stmt = $pdo->prepare("INSERT INTO ambiance_photos (filename, caption) VALUES (:f, :c)");
        $stmt->execute(['f' => $name, 'c' => $caption]);
        jsonResponse(['success' => true, 'message' => 'Photo ambiance ajoutée.']);
        break;

    case 'ambiance_photo_delete':
        $id = (int) ($_POST['id'] ?? 0);
        $row = $pdo->prepare("SELECT filename FROM ambiance_photos WHERE id = :id");
        $row->execute(['id' => $id]);
        $img = $row->fetch();
        if ($img) {
            @unlink(UPLOAD_DIR_AMBIANCE . $img['filename']);
            $pdo->prepare("DELETE FROM ambiance_photos WHERE id = :id")->execute(['id' => $id]);
        }
        jsonResponse(['success' => true, 'message' => 'Photo supprimée.']);
        break;

    /* ─── AMBIANCE COLORS ─────────────────────────────── */
    case 'ambiance_colors_list':
        $rows = $pdo->query("SELECT * FROM ambiance_colors ORDER BY sort_order ASC, id ASC")->fetchAll();
        jsonResponse(['success' => true, 'data' => $rows]);
        break;

    case 'ambiance_color_add':
        $hex  = trim($_POST['color_hex'] ?? '#FFFFFF');
        $name = sanitize($_POST['color_name'] ?? '');
        $order = (int) ($_POST['sort_order'] ?? 0);
        if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $hex)) jsonResponse(['success' => false, 'message' => 'Couleur invalide.']);
        $stmt = $pdo->prepare("INSERT INTO ambiance_colors (color_hex, color_name, sort_order) VALUES (:h, :n, :s)");
        $stmt->execute(['h' => $hex, 'n' => $name, 's' => $order]);
        jsonResponse(['success' => true, 'message' => 'Couleur ajoutée.']);
        break;

    case 'ambiance_color_update':
        $id   = (int) ($_POST['id'] ?? 0);
        $hex  = trim($_POST['color_hex'] ?? '#FFFFFF');
        $name = sanitize($_POST['color_name'] ?? '');
        $order = (int) ($_POST['sort_order'] ?? 0);
        if (!$id) jsonResponse(['success' => false, 'message' => 'ID manquant.']);
        $stmt = $pdo->prepare("UPDATE ambiance_colors SET color_hex = :h, color_name = :n, sort_order = :s WHERE id = :id");
        $stmt->execute(['h' => $hex, 'n' => $name, 's' => $order, 'id' => $id]);
        jsonResponse(['success' => true, 'message' => 'Couleur mise à jour.']);
        break;

    case 'ambiance_color_delete':
        $id = (int) ($_POST['id'] ?? 0);
        $pdo->prepare("DELETE FROM ambiance_colors WHERE id = :id")->execute(['id' => $id]);
        jsonResponse(['success' => true, 'message' => 'Couleur supprimée.']);
        break;

    /* ─── SETTINGS ─────────────────────────────────────── */
    case 'settings_get':
        $rows = $pdo->query("SELECT skey, svalue FROM settings")->fetchAll();
        $out = [];
        foreach ($rows as $r) $out[$r['skey']] = $r['svalue'];
        jsonResponse(['success' => true, 'data' => $out]);
        break;

    case 'settings_save':
        $fields = ['bride_name', 'groom_name', 'wedding_date', 'hero_subtitle', 'quote', 'story_text', 'rsvp_deadline', 'theme_primary', 'theme_accent', 'theme_dark'];
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
