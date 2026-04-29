<?php
require_once __DIR__ . '/../../includes/auth_check.php';
$pageTitle = 'Sales Report';

$from = $_GET['from'] ?? date('Y-m-01');
$to   = $_GET['to']   ?? date('Y-m-d');

$stmt = $pdo->prepare('
  SELECT s.invoice_no, s.sale_date, s.subtotal, s.discount, s.total_amount,
         s.paid_amount, s.due_amount, s.payment_type,
         COALESCE(c.name,"Walk-in") AS customer,
         u.name AS staff,
         COUNT(si.id) AS item_count
  FROM sales s
  LEFT JOIN customers c ON c.id = s.customer_id
  LEFT JOIN users u ON u.id = s.user_id
  LEFT JOIN sale_items si ON si.sale_id = s.id
  WHERE DATE(s.sale_date) BETWEEN ? AND ?
  GROUP BY s.id
  ORDER BY s.sale_date DESC
');
$stmt->execute([$from, $to]);
$sales = $stmt->fetchAll();

$totalRevenue = array_sum(array_column($sales, 'total_amount'));
$totalPaid    = array_sum(array_column($sales, 'paid_amount'));
$totalDue     = array_sum(array_column($sales, 'due_amount'));

include __DIR__ . '/../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h5 class="mb-0"><span class="material-icons align-middle mr-2">bar_chart</span>Sales Report</h5>
  <div class="no-print">
    <a href="<?= BASE_URL ?>/modules/reports/purchase_report.php" class="btn btn-outline-secondary btn-sm mr-1">Purchases</a>
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
      <div class="text-muted small">Total Sales</div>
      <div class="h4 text-primary"><?= count($sales) ?></div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card text-center p-3">
      <div class="text-muted small">Total Revenue</div>
      <div class="h4" style="color:#00C853;">৳<?= number_format($totalRevenue, 2) ?></div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card text-center p-3">
      <div class="text-muted small">Total Due</div>
      <div class="h4 text-danger">৳<?= number_format($totalDue, 2) ?></div>
    </div>
  </div>
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table table-hover datatable mb-0">
      <thead>
        <tr><th>Date</th><th>Invoice</th><th>Customer</th><th>Items</th><th>Total</th><th>Payment</th><th>Staff</th></tr>
      </thead>
      <tbody>
        <?php foreach ($sales as $s): ?>
        <tr>
          <td><?= date('d M Y', strtotime($s['sale_date'])) ?></td>
          <td><?= htmlspecialchars($s['invoice_no']) ?></td>
          <td><?= htmlspecialchars($s['customer']) ?></td>
          <td><?= $s['item_count'] ?></td>
          <td>৳<?= number_format($s['total_amount'], 2) ?></td>
          <td><span class="badge badge-info"><?= ucfirst(str_replace('_',' ',$s['payment_type'])) ?></span></td>
          <td><?= htmlspecialchars($s['staff']) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php
$extraJs = '<script>var baseUrl = "' . BASE_URL . '";</script>';
include __DIR__ . '/../../includes/footer.php';
