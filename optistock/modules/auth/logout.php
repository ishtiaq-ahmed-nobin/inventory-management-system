<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

if (isset($_SESSION['user_id'])) {
    log_activity($pdo, $_SESSION['user_id'], 'Logged out', 'auth');
}

session_destroy();
header('Location: ' . BASE_URL . '/modules/auth/login.php');
exit;
