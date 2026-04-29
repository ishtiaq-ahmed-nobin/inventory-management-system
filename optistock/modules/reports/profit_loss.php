<?php
require_once __DIR__ . '/../../includes/auth_check.php';
$pageTitle = 'Profit & Loss Report';

// Monthly P&L: revenue vs COGS (purchase_price × qty sold)
$monthlyCogs = $pdo->query('
  SELECT
    DATE_FORMAT(s.sale_date, "%Y-%m") AS month,
    SUM(s.total_amount) AS revenue,
    SUM(si.quantity * p.purchase_price) AS cogs
  FROM sales s
  JOIN sale_items si ON si.sale_id = s.id
  JOIN products p ON p.id = si.product_id
  GROUP BY DATE_FORMAT(s.sale_date, "%Y-%m")
  ORDER BY month DESC
  LIMIT 12
')->fetchAll();

$totalRevenue = array_sum(array_column($monthlyCogs, 'revenue'));
$totalCogs    = array_sum(array_column($monthlyCogs, 'cogs'));
$grossProfit  = $totalRevenue - $totalCogs;

$chartMonths  = array_reverse(array_column($monthlyCogs, 'month'));
$chartRevenue = array_reverse(array_column($monthlyCogs, 'revenue'));
$chartCogs    = array_reverse(array_column($monthlyCogs, 'cogs'));

include __DIR__ . '/../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h5 class="mb-0"><span class="material-icons align-middle mr-2">trending_up</span>Profit & Loss</h5>
  <div class="no-print">
    <a href="<?= BASE_URL ?>/modules/reports/sales_report.php" class="btn btn-outline-secondary btn-sm mr-1">Sales</a>
    <a href="<?= BASE_URL ?>/modules/reports/purchase_report.php" class="btn btn-outline-secondary btn-sm mr-1">Purchases</a>
    <a href="<?= BASE_URL ?>/modules/reports/stock_report.php" class="btn btn-outline-secondary btn-sm mr-1">Stock</a>
    <button onclick="window.print()" class="btn btn-outline-dark btn-sm">
      <span class="material-icons align-middle" style="font-size:14px;">print</span> Print
    </button>
  </div>
</div>

<div class="row mb-4">
  <div class="col-md-4">
    <div class="card text-center p-3">
      <div class="text-muted small">Total Revenue</div>
      <div class="h4" style="color:#00BCD4;">৳<?= number_format($totalRevenue, 2) ?></div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card text-center p-3">
      <div class="text-muted small">Cost of Goods Sold</div>
      <div class="h4 text-danger">৳<?= number_format($totalCogs, 2) ?></div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card text-center p-3">
      <div class="text-muted small">Gross Profit</div>
      <div class="h4 <?= $grossProfit >= 0 ? '' : 'text-danger' ?>" style="<?= $grossProfit >= 0 ? 'color:#00C853;' : '' ?>">৳<?= number_format($grossProfit, 2) ?></div>
    </div>
  </div>
</div>

<div class="card mb-4">
  <div class="card-header">Monthly Chart (Last 12 Months)</div>
  <div class="card-body">
    <canvas id="plChart" height="80"></canvas>
  </div>
</div>

<div class="card">
  <div class="card-header">Monthly Breakdown</div>
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead><tr><th>Month</th><th>Revenue</th><th>COGS</th><th>Gross Profit</th><th>Margin</th></tr></thead>
      <tbody>
        <?php foreach ($monthlyCogs as $row): ?>
        <?php $profit = $row['revenue'] - $row['cogs']; $margin = $row['revenue'] > 0 ? ($profit / $row['revenue'] * 100) : 0; ?>
        <tr>
          <td><?= date('M Y', strtotime($row['month'] . '-01')) ?></td>
          <td>৳<?= number_format($row['revenue'], 2) ?></td>
          <td>৳<?= number_format($row['cogs'], 2) ?></td>
          <td class="<?= $profit >= 0 ? 'text-success' : 'text-danger' ?>">৳<?= number_format($profit, 2) ?></td>
          <td><?= number_format($margin, 1) ?>%</td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$monthlyCogs): ?><tr><td colspan="5" class="text-center text-muted py-3">No data</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php
$chartMonthsJson  = json_encode($chartMonths);
$chartRevenueJson = json_encode(array_map('floatval', $chartRevenue));
$chartCogsJson    = json_encode(array_map('floatval', $chartCogs));
$extraJs = <<<JS
<script>
var plChartInstance;
function initPlChart(){
  var dark = document.body.classList.contains('dark-mode');
  var textColor = dark ? '#CDD9E5' : '#212529';
  var gridColor = dark ? '#2D3748' : '#E0E0E0';
  var ctx = document.getElementById('plChart').getContext('2d');
  if(plChartInstance) plChartInstance.destroy();
  plChartInstance = new Chart(ctx,{
    type:'bar',
    data:{
      labels: $chartMonthsJson,
      datasets:[
        {label:'Revenue',data:$chartRevenueJson,backgroundColor:dark?'rgba(0,188,212,0.5)':'rgba(0,188,212,0.7)',borderColor:'#00BCD4',borderWidth:2,borderRadius:4},
        {label:'COGS',data:$chartCogsJson,backgroundColor:dark?'rgba(255,23,68,0.35)':'rgba(255,23,68,0.6)',borderColor:'#FF1744',borderWidth:2,borderRadius:4}
      ]
    },
    options:{
      responsive:true,
      plugins:{legend:{labels:{color:textColor}}},
      scales:{
        y:{ticks:{color:textColor},grid:{color:gridColor}},
        x:{ticks:{color:textColor},grid:{color:gridColor}}
      }
    }
  });
}
function refreshCharts(){ initPlChart(); }
initPlChart();
</script>
JS;
include __DIR__ . '/../../includes/footer.php';
