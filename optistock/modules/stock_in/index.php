<?php
require_once __DIR__ . '/../../includes/auth_check.php';
$pageTitle = 'Stock In (Purchases)';

$from = $_GET['from'] ?? date('Y-m-01');
$to   = $_GET['to']   ?? date('Y-m-d');

$stmt = $pdo->prepare('
  SELECT si.*, p.name AS product_name, p.sku,
         s.name AS supplier_name, u.name AS staff_name
  FROM stock_in si
  JOIN products p ON p.id = si.product_id
  LEFT JOIN suppliers s ON s.id = si.supplier_id
  LEFT JOIN users u ON u.id = si.user_id
  WHERE DATE(si.received_date) BETWEEN ? AND ?
  ORDER BY si.received_date DESC
');
$stmt->execute([$from, $to]);
$records = $stmt->fetchAll();

$total = array_sum(array_column($records, 'total_cost'));

include __DIR__ . '/../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h5 class="mb-0"><span class="material-icons align-middle mr-2">add_box</span>Stock In — Purchase Records</h5>
  <a href="<?= BASE_URL ?>/modules/stock_in/add.php" class="btn btn-primary">
    <span class="material-icons align-middle mr-1" style="font-size:16px;">add</span>Receive Stock
  </a>
</div>

<div class="card mb-3">
  <div class="card-body py-2">
    <form method="GET" class="form-inline">
      <label class="mr-2">From:</label>
      <input type="date" name="from" class="form-control form-control-sm mr-3" value="<?= $from ?>">
      <label class="mr-2">To:</label>
      <input type="date" name="to" class="form-control form-control-sm mr-3" value="<?= $to ?>">
      <button type="submit" class="btn btn-sm btn-primary">Filter</button>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-header d-flex justify-content-between">
    <span>Total Records: <strong><?= count($records) ?></strong></span>
    <span>Total Cost: <strong>৳<?= number_format($total, 2) ?></strong></span>
  </div>
  <div class="table-responsive">
    <table class="table table-hover datatable mb-0">
      <thead>
        <tr><th>#</th><th>Date</th><th>Product</th><th>Supplier</th><th>Qty</th><th>Unit Cost</th><th>Total Cost</th><th>Note</th><th>Staff</th></tr>
      </thead>
      <tbody>
        <?php foreach ($records as $i => $r): ?>
        <tr>
          <td><?= $i + 1 ?></td>
          <td><?= date('d M Y', strtotime($r['received_date'])) ?></td>
          <td><strong><?= htmlspecialchars($r['product_name']) ?></strong><br><small class="text-muted"><?= htmlspecialchars($r['sku']) ?></small></td>
          <td><?= htmlspecialchars($r['supplier_name'] ?? '—') ?></td>
          <td><?= number_format($r['quantity']) ?></td>
          <td>৳<?= number_format($r['unit_cost'], 2) ?></td>
          <td>৳<?= number_format($r['total_cost'], 2) ?></td>
          <td><?= htmlspecialchars($r['note'] ?? '—') ?></td>
          <td><?= htmlspecialchars($r['staff_name']) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php
$extraJs = '<script>var baseUrl = "' . BASE_URL . '";</script>';
include __DIR__ . '/../../includes/footer.php';
