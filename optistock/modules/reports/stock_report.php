<?php
require_once __DIR__ . '/../../includes/auth_check.php';
$pageTitle = 'Stock Report';

$catFilter = $_GET['category'] ?? '';
$locFilter = $_GET['location'] ?? '';

$sql = 'SELECT p.*, cat.name AS category, l.name AS location, (p.quantity * p.purchase_price) AS stock_value
        FROM products p
        LEFT JOIN categories cat ON cat.id = p.category_id
        LEFT JOIN locations l ON l.id = p.location_id
        WHERE 1=1';
$params = [];
if ($catFilter) { $sql .= ' AND p.category_id = ?'; $params[] = $catFilter; }
if ($locFilter) { $sql .= ' AND p.location_id = ?'; $params[] = $locFilter; }
$sql .= ' ORDER BY p.name';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$categories = $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();
$locations  = $pdo->query('SELECT * FROM locations ORDER BY name')->fetchAll();
$totalValue = array_sum(array_column($products, 'stock_value'));

include __DIR__ . '/../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h5 class="mb-0"><span class="material-icons align-middle mr-2">bar_chart</span>Stock Report</h5>
  <div class="no-print">
    <a href="<?= BASE_URL ?>/modules/reports/sales_report.php" class="btn btn-outline-secondary btn-sm mr-1">Sales</a>
    <a href="<?= BASE_URL ?>/modules/reports/purchase_report.php" class="btn btn-outline-secondary btn-sm mr-1">Purchases</a>
    <a href="<?= BASE_URL ?>/modules/reports/profit_loss.php" class="btn btn-outline-secondary btn-sm mr-1">P&amp;L</a>
    <button onclick="window.print()" class="btn btn-outline-dark btn-sm">
      <span class="material-icons align-middle" style="font-size:14px;">print</span> Print
    </button>
  </div>
</div>

<div class="card mb-3 no-print">
  <div class="card-body py-2">
    <form method="GET" class="form-inline">
      <label class="mr-2">Category:</label>
      <select name="category" class="form-control form-control-sm mr-3">
        <option value="">All</option>
        <?php foreach ($categories as $cat): ?>
        <option value="<?= $cat['id'] ?>" <?= $catFilter == $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
        <?php endforeach; ?>
      </select>
      <label class="mr-2">Location:</label>
      <select name="location" class="form-control form-control-sm mr-3">
        <option value="">All</option>
        <?php foreach ($locations as $loc): ?>
        <option value="<?= $loc['id'] ?>" <?= $locFilter == $loc['id'] ? 'selected' : '' ?>><?= htmlspecialchars($loc['name']) ?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit" class="btn btn-sm btn-primary">Filter</button>
    </form>
  </div>
</div>

<div class="card mb-3">
  <div class="card-body text-center">
    <span class="text-muted">Total Stock Value: </span>
    <span class="h4 ml-2" style="color:#00C853;">৳<?= number_format($totalValue, 2) ?></span>
    <span class="text-muted ml-3">Products: <?= count($products) ?></span>
  </div>
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table table-hover datatable mb-0">
      <thead>
        <tr><th>Product</th><th>SKU</th><th>Category</th><th>Location</th><th>Qty</th><th>Buy Price</th><th>Sell Price</th><th>Stock Value</th><th>Status</th></tr>
      </thead>
      <tbody>
        <?php foreach ($products as $p): ?>
        <?php $isLow = $p['quantity'] <= $p['low_stock_alert']; ?>
        <tr <?= $isLow ? 'class="table-warning"' : '' ?>>
          <td><?= htmlspecialchars($p['name']) ?></td>
          <td><code><?= htmlspecialchars($p['sku']) ?></code></td>
          <td><?= htmlspecialchars($p['category'] ?? '—') ?></td>
          <td><?= htmlspecialchars($p['location'] ?? '—') ?></td>
          <td>
            <?php
              if ($p['quantity'] <= 0) echo '<span class="stock-out">' . $p['quantity'] . '</span>';
              elseif ($isLow)          echo '<span class="stock-low">' . $p['quantity'] . '</span>';
              else                     echo '<span class="stock-ok">' . $p['quantity'] . '</span>';
            ?>
          </td>
          <td>৳<?= number_format($p['purchase_price'], 2) ?></td>
          <td>৳<?= number_format($p['selling_price'], 2) ?></td>
          <td>৳<?= number_format($p['stock_value'], 2) ?></td>
          <td><span class="status-<?= $p['status'] ?>"><?= ucfirst($p['status']) ?></span></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php
$extraJs = '<script>var baseUrl = "' . BASE_URL . '";</script>';
include __DIR__ . '/../../includes/footer.php';
