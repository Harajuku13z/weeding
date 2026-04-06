<?php
require_once __DIR__ . '/config.php';

$pdo = db();
$log = [];

function run($pdo, string $sql, string $label, array &$log): void {
    try {
        $pdo->exec($sql);
        $log[] = ['ok', $label];
    } catch (PDOException $e) {
        if (str_contains($e->getMessage(), 'Duplicate column name') || str_contains($e->getMessage(), 'already exists')) {
            $log[] = ['skip', $label . ' (déjà fait)'];
        } else {
            $log[] = ['err', $label . ' — ' . $e->getMessage()];
        }
    }
}

/* ================================================================
   1. CRÉATION DES TABLES (si elles n'existent pas)
   ================================================================ */

run($pdo, "CREATE TABLE IF NOT EXISTS guests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(100) DEFAULT '',
    email VARCHAR(150) DEFAULT '',
    status ENUM('pending','accepted','maybe','declined') DEFAULT 'pending',
    companions INT DEFAULT 0,
    dietary VARCHAR(255) DEFAULT '',
    message TEXT,
    responded_at DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", 'Table guests', $log);

run($pdo, "CREATE TABLE IF NOT EXISTS gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,
    caption VARCHAR(255) DEFAULT '',
    sort_order INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", 'Table gallery', $log);

run($pdo, "CREATE TABLE IF NOT EXISTS programme (
    id INT AUTO_INCREMENT PRIMARY KEY,
    time_label VARCHAR(10) NOT NULL DEFAULT '',
    title VARCHAR(150) NOT NULL DEFAULT '',
    description TEXT,
    sort_order INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", 'Table programme', $log);

run($pdo, "CREATE TABLE IF NOT EXISTS lieux (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL DEFAULT '',
    address VARCHAR(255) DEFAULT '',
    photo VARCHAR(255) DEFAULT '',
    maps_url VARCHAR(500) DEFAULT '',
    maps_embed VARCHAR(500) DEFAULT '',
    icon VARCHAR(50) DEFAULT 'bi-geo-alt-fill',
    sort_order INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", 'Table lieux', $log);

run($pdo, "CREATE TABLE IF NOT EXISTS ambiance_photos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,
    caption VARCHAR(255) DEFAULT '',
    sort_order INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", 'Table ambiance_photos', $log);

run($pdo, "CREATE TABLE IF NOT EXISTS ambiance_colors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    color_hex VARCHAR(7) NOT NULL DEFAULT '#FFFFFF',
    color_name VARCHAR(50) DEFAULT '',
    sort_order INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", 'Table ambiance_colors', $log);

run($pdo, "CREATE TABLE IF NOT EXISTS hebergements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL DEFAULT '',
    distance VARCHAR(100) DEFAULT '',
    description TEXT,
    photo VARCHAR(255) DEFAULT '',
    link VARCHAR(500) DEFAULT '',
    sort_order INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", 'Table hebergements', $log);

run($pdo, "CREATE TABLE IF NOT EXISTS reminders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    guest_id INT NOT NULL,
    delay_days INT NOT NULL DEFAULT 14,
    remind_at DATE NOT NULL,
    sent TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_guest (guest_id),
    INDEX idx_remind (remind_at, sent)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", 'Table reminders', $log);

run($pdo, "CREATE TABLE IF NOT EXISTS settings (
    skey VARCHAR(50) PRIMARY KEY,
    svalue TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", 'Table settings', $log);

/* ================================================================
   2. MODIFICATIONS DE TABLES (ALTER TABLE)
   Chaque ALTER est indépendant — si la colonne existe déjà, on skip.
   ================================================================ */

run($pdo, "ALTER TABLE lieux ADD COLUMN photo VARCHAR(255) DEFAULT '' AFTER address",
    'Lieux → ajout colonne photo', $log);

/* ================================================================
   3. DONNÉES PAR DÉFAUT (INSERT IGNORE = pas de doublon)
   ================================================================ */

$stmtC = $pdo->prepare("INSERT IGNORE INTO ambiance_colors (id, color_hex, color_name, sort_order) VALUES (:id, :h, :n, :s)");
$stmtC->execute(['id' => 1, 'h' => '#F5EFE6', 'n' => 'Beige doux', 's' => 1]);
$stmtC->execute(['id' => 2, 'h' => '#A8C8E0', 'n' => 'Bleu ciel', 's' => 2]);
$stmtC->execute(['id' => 3, 'h' => '#6AAF7B', 'n' => 'Vert sauge', 's' => 3]);
$stmtC->execute(['id' => 4, 'h' => '#D4A853', 'n' => 'Or doux', 's' => 4]);
$stmtC->execute(['id' => 5, 'h' => '#E8DFD2', 'n' => 'Champagne', 's' => 5]);
$log[] = ['ok', 'Données par défaut → ambiance_colors'];

$stmtL = $pdo->prepare("INSERT IGNORE INTO lieux (id, name, address, maps_url, maps_embed, icon, sort_order) VALUES (:id, :n, :a, :mu, :me, :ic, :s)");
$stmtL->execute(['id' => 1, 'n' => 'Mairie de Chevigny', 'a' => 'Place du Général de Gaulle, 21800 Chevigny-Saint-Sauveur', 'mu' => 'https://maps.google.com/?q=Mairie+Chevigny-Saint-Sauveur', 'me' => '', 'ic' => 'bi-building', 's' => 1]);
$stmtL->execute(['id' => 2, 'n' => 'Le Lieu Dit', 'a' => '5 Rue Parmentier, 21000 Dijon', 'mu' => 'https://maps.google.com/?q=5+Rue+Parmentier+21000+Dijon', 'me' => '', 'ic' => 'bi-geo-alt-fill', 's' => 2]);
$log[] = ['ok', 'Données par défaut → lieux'];

$stmtP = $pdo->prepare("INSERT IGNORE INTO programme (id, time_label, title, description, sort_order) VALUES (:id, :t, :ti, :d, :s)");
$stmtP->execute(['id' => 1, 't' => '20:00', 'ti' => 'Dîner de gala', 'd' => 'Un repas raffiné pour célébrer notre union entourés de nos proches.', 's' => 1]);
$stmtP->execute(['id' => 2, 't' => '22:30', 'ti' => 'Soirée dansante', 'd' => 'Musique, danse et moments de joie jusqu\'au bout de la nuit.', 's' => 2]);
$stmtP->execute(['id' => 3, 't' => '11:00', 'ti' => 'Brunch du lendemain', 'd' => 'Pour prolonger le bonheur autour d\'un brunch convivial.', 's' => 3]);
$log[] = ['ok', 'Données par défaut → programme'];

$defaults = [
    'bride_name'    => 'Lisa',
    'groom_name'    => 'Christ',
    'wedding_date'  => '2026-06-06',
    'wedding_time'  => '15:00',
    'story_text'    => 'Notre histoire a commencé un soir d\'automne à Paris, lors d\'une soirée entre amis. Un regard, un sourire, et le monde s\'est mis à tourner différemment. Après trois années d\'une belle aventure partagée, il était temps de dire oui pour la vie.',
    'quote'         => 'L\'amour est notre seul vrai trésor',
    'hero_subtitle' => 'Nous nous disons oui',
    'rsvp_deadline' => '15 Mai 2025',
    'theme_primary' => '#A8C8E0',
    'theme_accent'  => '#7B9EC4',
    'theme_dark'    => '#2C3E50',
];
$stmt = $pdo->prepare("INSERT IGNORE INTO settings (skey, svalue) VALUES (:k, :v)");
foreach ($defaults as $k => $v) {
    $stmt->execute(['k' => $k, 'v' => $v]);
}
$log[] = ['ok', 'Données par défaut → settings'];

$codes = ['LISA2026', 'CHRIST26', 'AMOUR026', 'INVITE01', 'INVITE02', 'INVITE03', 'INVITE04', 'INVITE05'];
$stmtG = $pdo->prepare("INSERT IGNORE INTO guests (code, name) VALUES (:c, :n)");
foreach ($codes as $c) {
    $stmtG->execute(['c' => $c, 'n' => '']);
}
$log[] = ['ok', 'Codes d\'invitation'];

/* ================================================================
   4. DOSSIERS UPLOADS
   ================================================================ */

$dirs = [
    __DIR__ . '/uploads/gallery/',
    __DIR__ . '/uploads/lieux/',
    __DIR__ . '/uploads/hebergements/',
    __DIR__ . '/uploads/ambiance/',
];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
        $log[] = ['ok', 'Dossier créé : ' . basename(dirname($dir)) . '/' . basename($dir)];
    }
}

/* ================================================================
   RENDU HTML
   ================================================================ */

$icons = ['ok' => '✅', 'skip' => '⏭️', 'err' => '❌'];
$colors = ['ok' => '#27ae60', 'skip' => '#e67e22', 'err' => '#e74c3c'];

echo '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Installation / Mise à jour</title>
<style>
body{font-family:"Jost",sans-serif;max-width:650px;margin:50px auto;padding:20px;background:#fafaf7;color:#2d3436}
h1{color:#7B9EC4;margin-bottom:6px}
.sub{color:#888;font-size:14px;margin-bottom:30px}
.line{display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:8px;margin-bottom:6px;font-size:14px;background:#fff;border:1px solid #eee}
.line span:first-child{font-size:18px}
.warn{margin-top:24px;padding:16px;background:#fff3cd;border:1px solid #ffc107;border-radius:8px;font-size:13px;color:#856404}
.links{margin-top:20px;display:flex;gap:12px}
.links a{background:#7B9EC4;color:#fff;padding:10px 20px;border-radius:6px;text-decoration:none;font-size:14px}
.links a:hover{background:#5f86ad}
</style></head><body>
<h1>Installation / Mise à jour</h1>
<p class="sub">Toutes les opérations ont été exécutées.</p>';

foreach ($log as $entry) {
    $type = $entry[0];
    $msg = htmlspecialchars($entry[1], ENT_QUOTES, 'UTF-8');
    echo '<div class="line"><span>' . $icons[$type] . '</span><span style="color:' . $colors[$type] . '">' . $msg . '</span></div>';
}

echo '<div class="warn">⚠️ <strong>Supprimez ou renommez ce fichier</strong> après l\'installation pour des raisons de sécurité.</div>';
echo '<div class="links"><a href="/">Voir le site</a><a href="/admin/">Admin</a></div>';
echo '</body></html>';
