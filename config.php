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

/** E-mails (confirmation RSVP, rappels). Mettez false pour désactiver l’envoi. */
define('MAIL_ENABLED', true);
/** Expéditeur (boîte Hostinger / domaine vérifié) */
define('MAIL_FROM', 'contact@lisalovechrist.fr');
define('MAIL_FROM_NAME', 'Lisa & Christ');
/** Réponses invités (optionnel, par défaut = MAIL_FROM) */
define('MAIL_REPLY_TO', 'contact@lisalovechrist.fr');
/**
 * SMTP Hostinger : SSL port 465. Laisser MAIL_SMTP_HOST vide pour utiliser mail() PHP à la place.
 * En production, préférez des variables d’environnement pour MAIL_SMTP_PASS (ne pas versionner le secret).
 */
define('MAIL_SMTP_HOST', 'smtp.hostinger.com');
define('MAIL_SMTP_PORT', 465);
define('MAIL_SMTP_USER', 'contact@lisalovechrist.fr');
$_mailSmtpPass = getenv('MAIL_SMTP_PASS');
define('MAIL_SMTP_PASS', ($_mailSmtpPass !== false && $_mailSmtpPass !== '') ? $_mailSmtpPass : 'Harajuku1993@');
unset($_mailSmtpPass);
/** Vérification stricte du certificat SSL (mettre false seulement si connexion SMTP échoue sur l’hébergeur). */
define('MAIL_SMTP_SSL_VERIFY', true);
/** Si SMTP échoue, tenter la fonction mail() PHP (souvent configurée chez Hostinger). */
define('MAIL_SMTP_FALLBACK_PHP_MAIL', true);
/**
 * DKIM : signature des e-mails (fortement recommandé contre les spams).
 * Chez Hostinger : E-mails → DKIM, activer puis copier sélecteur + enregistrer la clé privée
 * dans un fichier hors webroot (non versionné). Laisser MAIL_DKIM_DOMAIN vide pour désactiver.
 */
define('MAIL_DKIM_DOMAIN', '');
define('MAIL_DKIM_SELECTOR', 'default');
define('MAIL_DKIM_PRIVATE_FILE', '');
/** URL du site dans les e-mails ; ex. https://lisalovechrist.fr ou '' pour la deviner (HTTP_HOST). */
define('SITE_PUBLIC_URL', 'https://lisalovechrist.fr');
/**
 * Sécuriser cron_reminders.php : appelez https://votresite.fr/cron_reminders.php?key=VOTRE_CLE
 * Définissez une chaîne longue aléatoire en production.
 */
define('CRON_SECRET', '');

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

/** Date affichage FR (e-mails, textes) à partir de Y-m-d */
function format_date_fr(?string $ymd): string {
    if ($ymd === null || $ymd === '') {
        return '';
    }
    $ts = strtotime($ymd);
    if ($ts === false) {
        return '';
    }
    $mois = ['', 'janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
    $n = (int) date('n', $ts);

    return (int) date('j', $ts) . ' ' . ($mois[$n] ?? '') . ' ' . date('Y', $ts);
}

/** Préfixe URL du dossier de l'app (ex. '' à la racine, '/mariage' en sous-dossier). */
function app_base_path(): string {
    static $cached = null;
    if ($cached !== null) {
        return $cached;
    }
    $script = $_SERVER['SCRIPT_NAME'] ?? '/';
    $dir = str_replace('\\', '/', dirname($script));
    if ($dir === '/' || $dir === '.' || $dir === '') {
        $cached = '';
        return $cached;
    }
    $cached = rtrim($dir, '/');
    return $cached;
}

/** Chemin absolu depuis le domaine : /api/rsvp.php ou /dossier/api/rsvp.php */
function app_url(string $path): string {
    $path = ltrim($path, '/');
    $base = app_base_path();
    return ($base === '' ? '/' : $base . '/') . $path;
}

/**
 * Valide un upload image (taille, extension, MIME détecté).
 * Retourne null si OK, sinon un message d'erreur affichable.
 */
function validate_image_upload(array $file, int $maxBytes = 5_242_880): ?string {
    $err = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
    if ($err !== UPLOAD_ERR_OK) {
        return match ($err) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'Fichier trop lourd (max 5 Mo).',
            UPLOAD_ERR_PARTIAL => 'Upload incomplet — réessayez.',
            UPLOAD_ERR_NO_FILE => 'Aucun fichier reçu.',
            default => 'Erreur lors de l\'envoi du fichier.',
        };
    }
    $tmp = $file['tmp_name'] ?? '';
    if ($tmp === '' || !is_uploaded_file($tmp)) {
        return 'Aucun fichier reçu.';
    }
    if ((int) ($file['size'] ?? 0) > $maxBytes) {
        return 'Fichier trop lourd (max 5 Mo).';
    }
    $ext = strtolower(pathinfo((string) ($file['name'] ?? ''), PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
        return 'Format non supporté (JPG, PNG, WEBP, GIF).';
    }
    if (function_exists('finfo_open')) {
        $f = finfo_open(FILEINFO_MIME_TYPE);
        $detected = $f ? finfo_file($f, $tmp) : '';
        if ($f) {
            finfo_close($f);
        }
        $ok = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (!in_array($detected, $ok, true)) {
            return 'Format non supporté ou fichier corrompu (JPG, PNG, WEBP, GIF).';
        }
    }

    return null;
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
