<?php

/**
 * Hostinger / LiteSpeed shared hosting bridge.
 * Forwards all requests to public/index.php.
 */

$publicPath = __DIR__ . '/public';

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? ''
);

if ($uri !== '/' && file_exists($publicPath . $uri)) {
    return false;
}

$_SERVER['SCRIPT_FILENAME'] = $publicPath . '/index.php';
$_SERVER['DOCUMENT_ROOT'] = $publicPath;

chdir($publicPath);

require_once $publicPath . '/index.php';
