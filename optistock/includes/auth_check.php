<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/modules/auth/login.php');
    exit;
}

function require_role(array $roles): void {
    if (!in_array($_SESSION['role'] ?? '', $roles, true)) {
        $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Access denied. Insufficient permissions.'];
        header('Location: ' . BASE_URL . '/modules/dashboard/index.php');
        exit;
    }
}
