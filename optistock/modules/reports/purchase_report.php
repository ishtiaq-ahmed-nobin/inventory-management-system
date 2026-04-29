<?php
require_once __DIR__ . '/../../includes/auth_check.php';
$pageTitle = 'Purchase Report';

$from = $_GET['from'] ?? date('Y-m-01');
$to   = $_GET['to']   ?? date('Y-m-d');

$stmt = $pdo->prepare('
  SELECT si.received_date, p.name AS product, p.sku, s.name AS supplier,
         si.quantity, si.unit_cost, si.total_cost, u.name AS staff
  FROM stock_in si
  JOIN products p ON p.id = si.product_id
  LEFT JOIN suppliers s ON s.id = si.supplier_id
  LEFT JOIN users u ON u.id = si.user_id
  WHERE DATE(si.received_date) BETWEEN ? AND ?
  ORDER BY si.received_date DESC
');
$stmt->execute([$from, $to]);
$records = $stmt->fetchAll();

$totalCost = array_sum(array_column($records, 'total_cost'));
$totalQty  = array_sum(array_column($records, 'quantity'));

include __DIR__ . '/../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h5 class="mb-0"><span class="material-icons align-middle mr-2">bar_chart</span>Purchase Report</h5>
  <div class="no-print">
    <a href="<?= BASE_URL ?>/modules/reports/sales_report.php" class="btn btn-outline-secondary btn-sm mr-1">Sales</a>
    <a href="<?= BASE_URL ?>/modules/reports/stock_report.php" class="btn btn-outline-secondary btn-sm mr-1">Stock</a>
    <a href="<?= BASE_URL ?>/modules/reports/profit_loss.php" class="btn btn-outline-secondary btn-sm mr-1">P&amp;L</a>
    <button onclick="window.print()" class="btn btn-outline-dark btn-sm">
      <span class="material-icons align-middle" style="font-size:14px;">print</span> Print
    </button>
  </div>
</div>

<div class="card mb-3 no-print">
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

<div class="row mb-3">
  <div class="col-md-4">
    <div class="card text-center p-3">
      <div class="text-muted small">Total Records</div>
      <div class="h4 text-primary"><?= count($records) ?></div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card text-center p-3">
      <div class="text-muted small">Total Quantity</div>
      <div class="h4" style="color:#26C6DA;"><?= number_format($totalQty) ?></div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card text-center p-3">
      <div class="text-muted small">Total Cost</div>
      <div class="h4 text-danger">৳<?= number_format($totalCost, 2) ?></div>
    </div>
  </div>
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table table-hover datatable mb-0">
      <thead>
        <tr><th>Date</th><th>Product</th><th>Supplier</th><th>Qty</th><th>Unit Cost</th><th>Total Cost</th><th>Staff</th></tr>
      </thead>
      <tbody>
        <?php foreach ($records as $r): ?>
        <tr>
          <td><?= date('d M Y', strtotime($r['received_date'])) ?></td>
          <td><?= htmlspecialchars($r['product']) ?> <small class="text-muted">(<?= $r['sku'] ?>)</small></td>
          <td><?= htmlspecialchars($r['supplier'] ?? '—') ?></td>
          <td><?= number_format($r['quantity']) ?></td>
          <td>৳<?= number_format($r['unit_cost'], 2) ?></td>
          <td>৳<?= number_format($r['total_cost'], 2) ?></td>
          <td><?= htmlspecialchars($r['staff']) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php
$extraJs = '<script>var baseUrl = "' . BASE_URL . '";</script>';
include __DIR__ . '/../../includes/footer.php';
