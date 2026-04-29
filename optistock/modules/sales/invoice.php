<?php
require_once __DIR__ . '/../../includes/auth_check.php';
$pageTitle = 'Invoice';

$invoiceNo = $_GET['invoice'] ?? '';
if (!$invoiceNo) { header('Location: ' . BASE_URL . '/modules/sales/index.php'); exit; }

$stmt = $pdo->prepare('
  SELECT s.*, u.name AS staff_name,
         COALESCE(c.name,"Walk-in Customer") AS customer_name,
         c.phone AS customer_phone, c.address AS customer_address
  FROM sales s
  LEFT JOIN customers c ON c.id = s.customer_id
  LEFT JOIN users u ON u.id = s.user_id
  WHERE s.invoice_no = ?
');
$stmt->execute([$invoiceNo]);
$sale = $stmt->fetch();
if (!$sale) { set_flash('danger','Invoice not found.'); header('Location: ' . BASE_URL . '/modules/sales/index.php'); exit; }

$items = $pdo->prepare('
  SELECT si.*, p.name AS product_name, p.sku
  FROM sale_items si
  JOIN products p ON p.id = si.product_id
  WHERE si.sale_id = ?
');
$items->execute([$sale['id']]);
$items = $items->fetchAll();

include __DIR__ . '/../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3 no-print">
  <h5 class="mb-0">Invoice — <?= htmlspecialchars($sale['invoice_no']) ?></h5>
  <div>
    <a href="<?= BASE_URL ?>/modules/sales/index.php" class="btn btn-outline-secondary btn-sm mr-2">Back to Sales</a>
    <a href="<?= BASE_URL ?>/modules/sales/pos.php" class="btn btn-primary btn-sm mr-2">New Sale</a>
    <button onclick="window.print()" class="btn btn-outline-dark btn-sm">
      <span class="material-icons align-middle" style="font-size:14px;">print</span> Print
    </button>
  </div>
</div>

<div class="card" id="invoiceCard">
  <div class="invoice-header p-4">
    <div class="row">
      <div class="col-6">
        <div class="invoice-logo">
          <span class="material-icons align-middle" style="font-size:28px;">inventory_2</span> OptiStock
        </div>
        <div class="mt-1" style="opacity:.85;font-size:.9rem;">Inventory Management System</div>
      </div>
      <div class="col-6 text-right">
        <h4 class="mb-1">INVOICE</h4>
        <div class="font-weight-bold" style="font-size:1.1rem;"><?= htmlspecialchars($sale['invoice_no']) ?></div>
        <div style="opacity:.85;"><?= date('d M Y H:i', strtotime($sale['sale_date'])) ?></div>
      </div>
    </div>
  </div>
  <div class="card-body">
    <div class="row mb-4">
      <div class="col-md-6">
        <h6 class="text-muted small text-uppercase">Billed To</h6>
        <div class="font-weight-bold"><?= htmlspecialchars($sale['customer_name']) ?></div>
        <?php if ($sale['customer_phone']): ?>
        <div class="text-muted small"><?= htmlspecialchars($sale['customer_phone']) ?></div>
        <?php endif; ?>
        <?php if ($sale['customer_address']): ?>
        <div class="text-muted small"><?= htmlspecialchars($sale['customer_address']) ?></div>
        <?php endif; ?>
      </div>
      <div class="col-md-6 text-md-right">
        <h6 class="text-muted small text-uppercase">Payment Info</h6>
        <div>Type: <strong><?= ucfirst(str_replace('_',' ',$sale['payment_type'])) ?></strong></div>
        <div>Staff: <strong><?= htmlspecialchars($sale['staff_name']) ?></strong></div>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-bordered">
        <thead class="thead-light">
          <tr><th>#</th><th>Product</th><th>SKU</th><th class="text-right">Qty</th><th class="text-right">Unit Price</th><th class="text-right">Total</th></tr>
        </thead>
        <tbody>
          <?php foreach ($items as $i => $item): ?>
          <tr>
            <td><?= $i + 1 ?></td>
            <td><?= htmlspecialchars($item['product_name']) ?></td>
            <td><code><?= htmlspecialchars($item['sku']) ?></code></td>
            <td class="text-right"><?= $item['quantity'] ?></td>
            <td class="text-right">৳<?= number_format($item['unit_price'], 2) ?></td>
            <td class="text-right">৳<?= number_format($item['total_price'], 2) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr><td colspan="5" class="text-right">Subtotal:</td><td class="text-right">৳<?= number_format($sale['subtotal'], 2) ?></td></tr>
          <?php if ($sale['discount'] > 0): ?>
          <tr><td colspan="5" class="text-right text-danger">Discount:</td><td class="text-right text-danger">-৳<?= number_format($sale['discount'], 2) ?></td></tr>
          <?php endif; ?>
          <tr class="font-weight-bold"><td colspan="5" class="text-right">Total:</td><td class="text-right" style="color:#00BCD4;font-size:1.1rem;">৳<?= number_format($sale['total_amount'], 2) ?></td></tr>
          <tr><td colspan="5" class="text-right">Paid:</td><td class="text-right" style="color:#00C853;">৳<?= number_format($sale['paid_amount'], 2) ?></td></tr>
          <?php if ($sale['due_amount'] > 0): ?>
          <tr><td colspan="5" class="text-right text-danger">Due:</td><td class="text-right text-danger">৳<?= number_format($sale['due_amount'], 2) ?></td></tr>
          <?php endif; ?>
        </tfoot>
      </table>
    </div>

    <?php if ($sale['note']): ?>
    <p class="text-muted"><strong>Note:</strong> <?= htmlspecialchars($sale['note']) ?></p>
    <?php endif; ?>

    <div class="text-center mt-4 text-muted small">
      <p>Thank you for your purchase! &mdash; OptiStock &copy; <?= date('Y') ?></p>
    </div>
  </div>
</div>

<?php
$extraJs = '<script>var baseUrl = "' . BASE_URL . '";</script>';
include __DIR__ . '/../../includes/footer.php';
