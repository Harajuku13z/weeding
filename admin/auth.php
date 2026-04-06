<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'login') {
        $user = trim($_POST['username'] ?? '');
        $pass = trim($_POST['password'] ?? '');
        if ($user === ADMIN_USER && $pass === ADMIN_PASS) {
            $_SESSION['admin_logged'] = true;
            header('Location: index.php');
            exit;
        }
        header('Location: index.php?error=1');
        exit;
    }

    if ($action === 'logout') {
        session_destroy();
        header('Location: index.php');
        exit;
    }
}

header('Location: index.php');
