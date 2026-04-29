<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

$dark = isset($_POST['dark_mode']) ? (int)$_POST['dark_mode'] : 0;
$dark = $dark ? 1 : 0;

$pdo->prepare('UPDATE users SET dark_mode = ? WHERE id = ?')->execute([$dark, $_SESSION['user_id']]);
$_SESSION['dark_mode'] = $dark;

echo json_encode(['success' => true]);
