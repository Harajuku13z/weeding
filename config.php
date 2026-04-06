<?php
session_start();

define('DB_HOST', 'localhost');
define('DB_NAME', 'u686558857_weeding');
define('DB_USER', 'u686558857_weeding');
define('DB_PASS', 'Harajuku1993@');

define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'motsdepasse');

define('UPLOAD_DIR', __DIR__ . '/uploads/gallery/');
define('UPLOAD_URL', 'uploads/gallery/');
define('UPLOAD_DIR_LIEUX', __DIR__ . '/uploads/lieux/');
define('UPLOAD_URL_LIEUX', 'uploads/lieux/');
define('UPLOAD_DIR_AMBIANCE', __DIR__ . '/uploads/ambiance/');
define('UPLOAD_URL_AMBIANCE', 'uploads/ambiance/');
define('UPLOAD_DIR_HOTEL', __DIR__ . '/uploads/hebergements/');
define('UPLOAD_URL_HOTEL', 'uploads/hebergements/');

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            http_response_code(500);
            die('Erreur de connexion à la base de données.');
        }
    }
    return $pdo;
}

function isAdmin(): bool {
    return isset($_SESSION['admin_logged']) && $_SESSION['admin_logged'] === true;
}

function jsonResponse(array $data, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function sanitize(string $str): string {
    return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8');
}

/** Normalise une couleur hex (#RRGGBB), accepte #888888, 888888, #888. */
function normalize_hex_color(string $raw): ?string {
    $h = strtoupper(trim($raw));
    if ($h === '') {
        return null;
    }
    if ($h[0] === '#') {
        $h = substr($h, 1);
    }
    if (preg_match('/^[0-9A-F]{3}$/', $h)) {
        return '#' . $h[0] . $h[0] . $h[1] . $h[1] . $h[2] . $h[2];
    }
    if (preg_match('/^[0-9A-F]{6}$/', $h)) {
        return '#' . $h;
    }
    return null;
}
