<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_role(['admin', 'manager']);

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT name FROM products WHERE id = ?');
$stmt->execute([$id]);
$product = $stmt->fetch();

if ($product) {
    $del = $pdo->prepare('UPDATE products SET status = "inactive" WHERE id = ?');
    $del->execute([$id]);
    log_activity($pdo, $_SESSION['user_id'], 'Deactivated product: ' . $product['name'], 'inventory');
    set_flash('success', 'Product deactivated successfully.');
} else {
    set_flash('danger', 'Product not found.');
}

header('Location: ' . BASE_URL . '/modules/inventory/index.php');
exit;
