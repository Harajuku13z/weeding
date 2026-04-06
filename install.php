<?php
require_once __DIR__ . '/config.php';

$pdo = db();

$pdo->exec("CREATE TABLE IF NOT EXISTS guests (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$pdo->exec("CREATE TABLE IF NOT EXISTS gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,
    caption VARCHAR(255) DEFAULT '',
    sort_order INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$pdo->exec("CREATE TABLE IF NOT EXISTS programme (
    id INT AUTO_INCREMENT PRIMARY KEY,
    time_label VARCHAR(10) NOT NULL DEFAULT '',
    title VARCHAR(150) NOT NULL DEFAULT '',
    description TEXT,
    sort_order INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$pdo->exec("CREATE TABLE IF NOT EXISTS lieux (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL DEFAULT '',
    address VARCHAR(255) DEFAULT '',
    photo VARCHAR(255) DEFAULT '',
    maps_url VARCHAR(500) DEFAULT '',
    maps_embed VARCHAR(500) DEFAULT '',
    icon VARCHAR(50) DEFAULT 'bi-geo-alt-fill',
    sort_order INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$stmtL = $pdo->prepare("INSERT IGNORE INTO lieux (id, name, address, maps_url, maps_embed, icon, sort_order) VALUES (:id, :n, :a, :mu, :me, :ic, :s)");
$stmtL->execute(['id' => 1, 'n' => 'Mairie de Chevigny', 'a' => 'Place du Général de Gaulle, 21800 Chevigny-Saint-Sauveur', 'mu' => 'https://maps.google.com/?q=Mairie+Chevigny-Saint-Sauveur', 'me' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2710.5!2d5.1312!3d47.2972!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47f29e3e3b2c0001%3A0x1!2sPlace+du+G%C3%A9n%C3%A9ral+de+Gaulle%2C+21800+Chevigny-Saint-Sauveur!5e0!3m2!1sfr!2sfr', 'ic' => 'bi-building', 's' => 1]);
$stmtL->execute(['id' => 2, 'n' => 'Le Lieu Dit', 'a' => '5 Rue Parmentier, 21000 Dijon', 'mu' => 'https://maps.google.com/?q=5+Rue+Parmentier+21000+Dijon', 'me' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2710.5!2d5.0415!3d47.3220!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47f29e3e3b2c0001%3A0x1!2s5+Rue+Parmentier%2C+21000+Dijon!5e0!3m2!1sfr!2sfr', 'ic' => 'bi-geo-alt-fill', 's' => 2]);

$pdo->exec("CREATE TABLE IF NOT EXISTS settings (
    skey VARCHAR(50) PRIMARY KEY,
    svalue TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$stmtP = $pdo->prepare("INSERT IGNORE INTO programme (id, time_label, title, description, sort_order) VALUES (:id, :t, :ti, :d, :s)");
$stmtP->execute(['id' => 1, 't' => '20:00', 'ti' => 'Dîner de gala', 'd' => 'Un repas raffiné pour célébrer notre union entourés de nos proches.', 's' => 1]);
$stmtP->execute(['id' => 2, 't' => '22:30', 'ti' => 'Soirée dansante', 'd' => 'Musique, danse et moments de joie jusqu\'au bout de la nuit.', 's' => 2]);
$stmtP->execute(['id' => 3, 't' => '11:00', 'ti' => 'Brunch du lendemain', 'd' => 'Pour prolonger le bonheur autour d\'un brunch convivial.', 's' => 3]);

$defaults = [
    'bride_name' => 'Lisa',
    'groom_name' => 'Christ',
    'wedding_date' => '2026-06-06',
    'wedding_time' => '15:00',
    'story_text' => 'Notre histoire a commencé un soir d\'automne à Paris, lors d\'une soirée entre amis. Un regard, un sourire, et le monde s\'est mis à tourner différemment. Après trois années d\'une belle aventure partagée, il était temps de dire oui pour la vie.',
    'quote' => 'L\'amour est notre seul vrai trésor',
    'hero_subtitle' => 'Invitation au mariage',
];

$stmt = $pdo->prepare("INSERT IGNORE INTO settings (skey, svalue) VALUES (:k, :v)");
foreach ($defaults as $k => $v) {
    $stmt->execute(['k' => $k, 'v' => $v]);
}

$codes = ['LISA2026', 'CHRIST26', 'AMOUR026', 'INVITE01', 'INVITE02', 'INVITE03', 'INVITE04', 'INVITE05'];
$stmtG = $pdo->prepare("INSERT IGNORE INTO guests (code, name) VALUES (:c, :n)");
foreach ($codes as $c) {
    $stmtG->execute(['c' => $c, 'n' => '']);
}

echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Installation</title>
<style>body{font-family:sans-serif;max-width:600px;margin:60px auto;padding:20px;background:#fafaf7;color:#2d3436}
h1{color:#7B9EC4}.ok{color:#27ae60;font-weight:600}code{background:#eee;padding:2px 8px;border-radius:4px}</style></head>
<body><h1>Installation terminée</h1>
<p class="ok">Les tables ont été créées avec succès.</p>
<p>Codes d\'invitation créés : <code>' . implode('</code>, <code>', $codes) . '</code></p>
<p><strong>Supprimez ce fichier</strong> après l\'installation pour des raisons de sécurité.</p>
<p><a href="/">Voir le site</a> | <a href="/admin/">Accéder à l\'admin</a></p>
</body></html>';
