<?php
if (!defined('BASE_URL')) require_once __DIR__ . '/../config/db.php';

// Low stock count for badge
$lowStockCount = 0;
if (isset($pdo)) {
    try {
        $ls = $pdo->query('SELECT COUNT(*) FROM products WHERE quantity <= low_stock_alert AND status = "active"');
        $lowStockCount = (int)$ls->fetchColumn();
    } catch (Exception $e) {}
}

$currentPage = basename($_SERVER['PHP_SELF']);
$currentDir  = basename(dirname($_SERVER['PHP_SELF']));

function isActive(string $dir, string $file = ''): string {
    global $currentDir, $currentPage;
    if ($file && $currentPage === $file && $currentDir === $dir) return 'active';
    if (!$file && $currentDir === $dir) return 'active';
    return '';
}
?>
<nav class="sidebar d-flex flex-column" id="sidebar">
  <div class="sidebar-header p-3 d-flex align-items-center">
    <span class="material-icons mr-2" style="color:#00BCD4;font-size:28px;">inventory_2</span>
    <span class="brand-text font-weight-bold" style="font-size:1.2rem;">OptiStock</span>
    <button class="btn btn-sm ml-auto d-lg-none text-white" id="sidebarClose">
      <span class="material-icons">close</span>
    </button>
  </div>
  <ul class="nav flex-column px-2 flex-grow-1">

    <li class="nav-item py-1">
      <a class="nav-link <?= isActive('dashboard') ?>" href="<?= BASE_URL ?>/modules/dashboard/index.php">
        <span class="material-icons align-middle mr-2">dashboard</span>Dashboard
      </a>
    </li>

    <li class="nav-item py-1">
      <a class="nav-link <?= isActive('inventory') ?>" href="<?= BASE_URL ?>/modules/inventory/index.php">
        <span class="material-icons align-middle mr-2">inventory</span>Inventory
        <?php if ($lowStockCount > 0): ?>
          <span class="badge badge-warning ml-auto float-right"><?= $lowStockCount ?></span>
        <?php endif; ?>
      </a>
    </li>

    <li class="nav-item py-1">
      <a class="nav-link <?= isActive('stock_in') ?>" href="<?= BASE_URL ?>/modules/stock_in/index.php">
        <span class="material-icons align-middle mr-2">add_box</span>Stock In
      </a>
    </li>

    <li class="nav-item py-1">
      <a class="nav-link <?= isActive('sales') ?>" href="<?= BASE_URL ?>/modules/sales/pos.php">
        <span class="material-icons align-middle mr-2">point_of_sale</span>POS / Sales
      </a>
    </li>

    <li class="nav-item py-1">
      <a class="nav-link <?= isActive('suppliers') ?>" href="<?= BASE_URL ?>/modules/suppliers/index.php">
        <span class="material-icons align-middle mr-2">local_shipping</span>Suppliers
      </a>
    </li>

    <li class="nav-item py-1">
      <a class="nav-link <?= isActive('customers') ?>" href="<?= BASE_URL ?>/modules/customers/index.php">
        <span class="material-icons align-middle mr-2">people</span>Customers
      </a>
    </li>

    <li class="nav-item py-1">
      <a class="nav-link <?= isActive('reports') ?>" href="<?= BASE_URL ?>/modules/reports/sales_report.php">
        <span class="material-icons align-middle mr-2">bar_chart</span>Reports
      </a>
    </li>

    <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
    <li class="nav-item py-2">
      <a class="nav-link <?= isActive('users') ?>" href="<?= BASE_URL ?>/modules/users/index.php">
        <span class="material-icons align-middle mr-2">manage_accounts</span>Users
      </a>
    </li>
    <?php endif; ?>

    <li class="nav-item py-2 mt-auto">
      <a class="nav-link text-danger" href="<?= BASE_URL ?>/modules/auth/logout.php">
        <span class="material-icons align-middle mr-2">logout</span>Logout
      </a>
    </li>

  </ul>
</nav>

<!-- Overlay for mobile -->
<div id="sidebarOverlay" class="sidebar-overlay d-lg-none"></div>
