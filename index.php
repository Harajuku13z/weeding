<?php

/**
 * Hostinger / shared hosting bridge.
 * Redirects all requests to the public/ directory.
 */

// Point to the public directory
$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? ''
);

// If the requested file exists in public/, serve it directly
if ($uri !== '/' && file_exists(__DIR__ . '/public' . $uri)) {
    return false;
}

// Otherwise, load the Laravel front controller
require_once __DIR__ . '/public/index.php';
