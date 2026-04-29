<?php
require_once __DIR__ . '/../../includes/auth_check.php';
$pageTitle = 'Sales History';

$from = $_GET['from'] ?? date('Y-m-01');
$to   = $_GET['to']   ?? date('Y-m-d');

$stmt = $pdo->prepare('
  SELECT s.*, COALESCE(c.name,"Walk-in") AS customer_name, u.name AS staff_name
  FROM sales s
  LEFT JOIN customers c ON c.id = s.customer_id
  LEFT JOIN users u ON u.id = s.user_id
  WHERE DATE(s.sale_date) BETWEEN ? AND ?
  ORDER BY s.sale_date DESC
');
$stmt->execute([$from, $to]);
$sales = $stmt->fetchAll();

$totalRevenue = array_sum(array_column($sales, 'total_amount'));

include __DIR__ . '/../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h5 class="mb-0"><span class="material-icons align-middle mr-2">receipt_long</span>Sales History</h5>
  <a href="<?= BASE_URL ?>/modules/sales/pos.php" class="btn btn-primary">
    <span class="material-icons align-middle mr-1" style="font-size:16px;">point_of_sale</span>New Sale
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
    <span>Total Sales: <strong><?= count($sales) ?></strong></span>
    <span>Revenue: <strong>৳<?= number_format($totalRevenue, 2) ?></strong></span>
  </div>
  <div class="table-responsive">
    <table class="table table-hover datatable mb-0">
      <thead>
        <tr><th>#</th><th>Invoice</th><th>Customer</th><th>Total</th><th>Paid</th><th>Due</th><th>Payment</th><th>Staff</th><th>Date</th><th></th></tr>
      </thead>
      <tbody>
        <?php foreach ($sales as $i => $s): ?>
        <tr>
          <td><?= $i + 1 ?></td>
          <td><strong><?= htmlspecialchars($s['invoice_no']) ?></strong></td>
          <td><?= htmlspecialchars($s['customer_name']) ?></td>
          <td>৳<?= number_format($s['total_amount'], 2) ?></td>
          <td style="color:#00C853;">৳<?= number_format($s['paid_amount'], 2) ?></td>
          <td><?= $s['due_amount'] > 0 ? '<span class="text-danger">৳' . number_format($s['due_amount'], 2) . '</span>' : '<span class="text-success">৳0.00</span>' ?></td>
          <td><span class="badge badge-info"><?= ucfirst(str_replace('_',' ',$s['payment_type'])) ?></span></td>
          <td><?= htmlspecialchars($s['staff_name']) ?></td>
          <td><?= date('d M Y H:i', strtotime($s['sale_date'])) ?></td>
          <td>
            <a href="<?= BASE_URL ?>/modules/sales/invoice.php?invoice=<?= urlencode($s['invoice_no']) ?>" class="btn btn-sm btn-outline-primary" target="_blank">
              <span class="material-icons" style="font-size:14px;">receipt</span>
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
