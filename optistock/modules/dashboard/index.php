<?php
require_once __DIR__ . '/../../includes/auth_check.php';
$pageTitle = 'Dashboard';

// Stats
$totalProducts  = (int)$pdo->query('SELECT COUNT(*) FROM products WHERE status="active"')->fetchColumn();
$stockValue     = (float)$pdo->query('SELECT COALESCE(SUM(quantity * purchase_price),0) FROM products WHERE status="active"')->fetchColumn();
$todaySales     = (float)$pdo->query('SELECT COALESCE(SUM(total_amount),0) FROM sales WHERE DATE(sale_date)=CURDATE()')->fetchColumn();
$lowStockItems  = (int)$pdo->query('SELECT COUNT(*) FROM products WHERE quantity <= low_stock_alert AND status="active"')->fetchColumn();

// Recent sales
$recentSales = $pdo->query('
  SELECT s.invoice_no, s.total_amount, s.sale_date, s.payment_type,
         COALESCE(c.name,"Walk-in") AS customer, u.name AS staff
  FROM sales s
  LEFT JOIN customers c ON c.id = s.customer_id
  LEFT JOIN users u ON u.id = s.user_id
  ORDER BY s.sale_date DESC LIMIT 10
')->fetchAll();

// Low stock products
$lowStock = $pdo->query('
  SELECT p.name, p.sku, p.quantity, p.low_stock_alert, cat.name AS category
  FROM products p
  LEFT JOIN categories cat ON cat.id = p.category_id
  WHERE p.quantity <= p.low_stock_alert AND p.status="active"
  ORDER BY p.quantity ASC LIMIT 10
')->fetchAll();

// Last 7 days sales for chart
$chartData = $pdo->query('
  SELECT DATE(sale_date) AS day, SUM(total_amount) AS total
  FROM sales
  WHERE sale_date >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
  GROUP BY DATE(sale_date)
  ORDER BY day ASC
')->fetchAll();

$chartDays   = [];
$chartTotals = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $chartDays[] = date('D d', strtotime($date));
    $found = false;
    foreach ($chartData as $row) {
        if ($row['day'] === $date) { $chartTotals[] = (float)$row['total']; $found = true; break; }
    }
    if (!$found) $chartTotals[] = 0;
}

include __DIR__ . '/../../includes/header.php';
?>

<div class="row mb-4">
  <div class="col-xl-3 col-md-6 mb-3">
    <div class="card stat-card border-left-primary h-100">
      <div class="card-body d-flex align-items-center">
        <div class="flex-grow-1">
          <div class="text-muted small mb-1">Total Products</div>
          <div class="stat-number text-primary"><?= number_format($totalProducts) ?></div>
        </div>
        <span class="material-icons stat-icon text-primary">inventory</span>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6 mb-3">
    <div class="card stat-card h-100" style="border-left-color:#00C853;">
      <div class="card-body d-flex align-items-center">
        <div class="flex-grow-1">
          <div class="text-muted small mb-1">Total Stock Value</div>
          <div class="stat-number" style="color:#00C853;">৳<?= number_format($stockValue, 2) ?></div>
        </div>
        <span class="material-icons stat-icon" style="color:#00C853;">account_balance_wallet</span>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6 mb-3">
    <div class="card stat-card h-100" style="border-left-color:#26C6DA;">
      <div class="card-body d-flex align-items-center">
        <div class="flex-grow-1">
          <div class="text-muted small mb-1">Today's Sales</div>
          <div class="stat-number" style="color:#26C6DA;">৳<?= number_format($todaySales, 2) ?></div>
        </div>
        <span class="material-icons stat-icon" style="color:#26C6DA;">point_of_sale</span>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6 mb-3">
    <div class="card stat-card h-100" style="border-left-color:#FF1744;">
      <div class="card-body d-flex align-items-center">
        <div class="flex-grow-1">
          <div class="text-muted small mb-1">Low Stock Items</div>
          <div class="stat-number" style="color:#FF1744;"><?= number_format($lowStockItems) ?></div>
        </div>
        <span class="material-icons stat-icon" style="color:#FF1744;">warning</span>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-8 mb-4">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <span><span class="material-icons align-middle mr-1" style="font-size:18px;">bar_chart</span>Last 7 Days Sales</span>
      </div>
      <div class="card-body">
        <canvas id="salesChart" height="100"></canvas>
      </div>
    </div>
  </div>
  <div class="col-lg-4 mb-4">
    <div class="card h-100">
      <div class="card-header">
        <span class="material-icons align-middle mr-1" style="font-size:18px;color:#FF1744;">warning</span>Low Stock Alert
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-sm mb-0">
            <thead><tr><th>Product</th><th>Qty</th><th>Min</th></tr></thead>
            <tbody>
              <?php foreach ($lowStock as $ls): ?>
              <tr>
                <td><?= htmlspecialchars($ls['name']) ?></td>
                <td><span class="stock-<?= $ls['quantity'] <= 0 ? 'out' : 'low' ?>"><?= $ls['quantity'] ?></span></td>
                <td><?= $ls['low_stock_alert'] ?></td>
              </tr>
              <?php endforeach; ?>
              <?php if (!$lowStock): ?>
              <tr><td colspan="3" class="text-center text-muted py-3">All stock levels are OK</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <span class="material-icons align-middle mr-1" style="font-size:18px;">receipt_long</span>Recent Sales
  </div>
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead><tr><th>Invoice</th><th>Customer</th><th>Amount</th><th>Payment</th><th>Staff</th><th>Date</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($recentSales as $sale): ?>
        <tr>
          <td><strong><?= htmlspecialchars($sale['invoice_no']) ?></strong></td>
          <td><?= htmlspecialchars($sale['customer']) ?></td>
          <td>৳<?= number_format($sale['total_amount'], 2) ?></td>
          <td><span class="badge badge-info"><?= ucfirst(str_replace('_',' ',$sale['payment_type'])) ?></span></td>
          <td><?= htmlspecialchars($sale['staff']) ?></td>
          <td><?= date('d M Y H:i', strtotime($sale['sale_date'])) ?></td>
          <td><a href="<?= BASE_URL ?>/modules/sales/invoice.php?id=<?= urlencode($sale['invoice_no']) ?>" class="btn btn-sm btn-outline-primary" target="_blank"><span class="material-icons align-middle" style="font-size:14px;">receipt</span></a></td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$recentSales): ?>
        <tr><td colspan="7" class="text-center text-muted py-3">No sales yet</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php
$chartDaysJson   = json_encode($chartDays);
$chartTotalsJson = json_encode($chartTotals);
$extraJs = <<<JS
<script>
var salesChartInstance;
var chartLabels  = $chartDaysJson;
var chartData    = $chartTotalsJson;

function getChartColors() {
  var dark = document.body.classList.contains('dark-mode');
  return {
    bar:   dark ? 'rgba(0,188,212,0.5)'   : 'rgba(0,188,212,0.7)',
    border:dark ? '#26C6DA'               : '#00BCD4',
    text:  dark ? '#CDD9E5'               : '#212529',
    grid:  dark ? '#2D3748'               : '#E0E0E0'
  };
}

function initSalesChart() {
  var c = getChartColors();
  var ctx = document.getElementById('salesChart').getContext('2d');
  if (salesChartInstance) salesChartInstance.destroy();
  salesChartInstance = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: chartLabels,
      datasets: [{
        label: 'Sales (৳)',
        data: chartData,
        backgroundColor: c.bar,
        borderColor: c.border,
        borderWidth: 2,
        borderRadius: 4
      }]
    },
    options: {
      responsive: true,
      plugins: { legend: { display: false } },
      scales: {
        y: { ticks: { color: c.text }, grid: { color: c.grid } },
        x: { ticks: { color: c.text }, grid: { color: c.grid } }
      }
    }
  });
}

function refreshCharts() { initSalesChart(); }
initSalesChart();
</script>
JS;
include __DIR__ . '/../../includes/footer.php';
