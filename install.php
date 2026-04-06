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

$pdo->exec("CREATE TABLE IF NOT EXISTS settings (
    skey VARCHAR(50) PRIMARY KEY,
    svalue TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

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
