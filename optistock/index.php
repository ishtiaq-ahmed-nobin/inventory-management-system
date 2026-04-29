<?php
session_start();
require_once __DIR__ . '/config/db.php';

if (isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/modules/dashboard/index.php');
} else {
    header('Location: ' . BASE_URL . '/modules/auth/login.php');
}
exit;
