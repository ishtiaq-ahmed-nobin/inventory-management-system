<?php
require_once __DIR__ . '/../../includes/auth_check.php';
$pageTitle = 'Inventory';

$products = $pdo->query('
  SELECT p.*, cat.name AS category_name, s.name AS supplier_name, l.name AS location_name
  FROM products p
  LEFT JOIN categories cat ON cat.id = p.category_id
  LEFT JOIN suppliers s ON s.id = p.supplier_id
  LEFT JOIN locations l ON l.id = p.location_id
  ORDER BY p.created_at DESC
')->fetchAll();

include __DIR__ . '/../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h5 class="mb-0"><span class="material-icons align-middle mr-2">inventory</span>Products</h5>
  <a href="<?= BASE_URL ?>/modules/inventory/add.php" class="btn btn-primary">
    <span class="material-icons align-middle mr-1" style="font-size:16px;">add</span>Add Product
  </a>
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table table-hover datatable mb-0">
      <thead>
        <tr>
          <th>#</th><th>Name</th><th>SKU</th><th>Category</th><th>Qty</th>
          <th>Buy Price</th><th>Sell Price</th><th>Status</th><th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($products as $i => $p): ?>
        <tr>
          <td><?= $i + 1 ?></td>
          <td>
            <strong><?= htmlspecialchars($p['name']) ?></strong>
            <?php if ($p['barcode']): ?>
              <br><small class="text-muted"><?= htmlspecialchars($p['barcode']) ?></small>
            <?php endif; ?>
          </td>
          <td><code><?= htmlspecialchars($p['sku']) ?></code></td>
          <td><?= htmlspecialchars($p['category_name'] ?? '—') ?></td>
          <td>
            <?php
              if ($p['quantity'] <= 0) echo '<span class="stock-out">' . $p['quantity'] . '</span>';
              elseif ($p['quantity'] <= $p['low_stock_alert']) echo '<span class="stock-low">' . $p['quantity'] . '</span>';
              else echo '<span class="stock-ok">' . $p['quantity'] . '</span>';
            ?>
          </td>
          <td>৳<?= number_format($p['purchase_price'], 2) ?></td>
          <td>৳<?= number_format($p['selling_price'], 2) ?></td>
          <td><span class="status-<?= $p['status'] ?>"><?= ucfirst($p['status']) ?></span></td>
          <td>
            <a href="<?= BASE_URL ?>/modules/inventory/edit.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary mr-1" title="Edit">
              <span class="material-icons" style="font-size:14px;">edit</span>
            </a>
            <a href="<?= BASE_URL ?>/modules/inventory/delete.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-danger btn-confirm-delete" title="Delete">
              <span class="material-icons" style="font-size:14px;">delete</span>
            </a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php
$extraJs = '<script>var baseUrl = "' . BASE_URL . '";</script>';
include __DIR__ . '/../../includes/footer.php';
