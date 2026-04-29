<?php
require_once __DIR__ . '/../../includes/auth_check.php';
$pageTitle = 'Receive Stock';

$products  = $pdo->query('SELECT id, name, sku, purchase_price, supplier_id FROM products WHERE status="active" ORDER BY name')->fetchAll();
$suppliers = $pdo->query('SELECT * FROM suppliers ORDER BY name')->fetchAll();

$errors = [];
$savedId = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId    = (int)($_POST['product_id'] ?? 0);
    $supplierId   = $_POST['supplier_id'] ?: null;
    $quantity     = (int)($_POST['quantity'] ?? 0);
    $unitCost     = (float)($_POST['unit_cost'] ?? 0);
    $note         = trim($_POST['note'] ?? '');
    $receivedDate = $_POST['received_date'] ?? date('Y-m-d H:i:s');

    if (!$productId) $errors[] = 'Please select a product.';
    if ($quantity <= 0) $errors[] = 'Quantity must be greater than 0.';

    if (!$errors) {
        $totalCost = $quantity * $unitCost;
        $stmt = $pdo->prepare('INSERT INTO stock_in (product_id,supplier_id,user_id,quantity,unit_cost,total_cost,note,received_date) VALUES (?,?,?,?,?,?,?,?)');
        $stmt->execute([$productId, $supplierId, $_SESSION['user_id'], $quantity, $unitCost, $totalCost, $note, $receivedDate]);
        $savedId = $pdo->lastInsertId();

        // Update stock
        $pdo->prepare('UPDATE products SET quantity = quantity + ? WHERE id = ?')->execute([$quantity, $productId]);

        log_activity($pdo, $_SESSION['user_id'], "Received $quantity units of product #$productId", 'stock_in');
        set_flash('success', "Stock received successfully! <a href='" . BASE_URL . "/modules/stock_in/index.php' class='alert-link'>View records</a>");
        header('Location: ' . BASE_URL . '/modules/stock_in/add.php?grn=' . $savedId);
        exit;
    }
}

// Show GRN after save
$grnData = null;
if (isset($_GET['grn'])) {
    $gs = $pdo->prepare('SELECT si.*, p.name AS product_name, p.sku, s.name AS supplier_name, u.name AS staff_name FROM stock_in si JOIN products p ON p.id=si.product_id LEFT JOIN suppliers s ON s.id=si.supplier_id LEFT JOIN users u ON u.id=si.user_id WHERE si.id=?');
    $gs->execute([(int)$_GET['grn']]);
    $grnData = $gs->fetch();
}

include __DIR__ . '/../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h5 class="mb-0"><span class="material-icons align-middle mr-2">add_box</span>Receive Stock</h5>
  <a href="<?= BASE_URL ?>/modules/stock_in/index.php" class="btn btn-outline-secondary btn-sm">Back to List</a>
</div>

<?php if ($errors): ?>
  <div class="alert alert-danger"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div>
<?php endif; ?>

<?php if ($grnData): ?>
<!-- GRN Preview -->
<div class="card grn-card mb-4" id="grnSection">
  <div class="card-header d-flex justify-content-between align-items-center">
    <span><span class="material-icons align-middle mr-1" style="font-size:18px;">receipt</span>Goods Received Note (GRN #<?= $grnData['id'] ?>)</span>
    <button onclick="window.print()" class="btn btn-sm btn-outline-primary no-print">
      <span class="material-icons align-middle" style="font-size:14px;">print</span> Print GRN
    </button>
  </div>
  <div class="card-body">
    <div class="row mb-3">
      <div class="col-md-6">
        <p><strong>Date:</strong> <?= date('d M Y H:i', strtotime($grnData['received_date'])) ?></p>
        <p><strong>Product:</strong> <?= htmlspecialchars($grnData['product_name']) ?> (<?= htmlspecialchars($grnData['sku']) ?>)</p>
        <p><strong>Supplier:</strong> <?= htmlspecialchars($grnData['supplier_name'] ?? 'N/A') ?></p>
      </div>
      <div class="col-md-6">
        <p><strong>Quantity Received:</strong> <?= $grnData['quantity'] ?></p>
        <p><strong>Unit Cost:</strong> ৳<?= number_format($grnData['unit_cost'], 2) ?></p>
        <p><strong>Total Cost:</strong> <strong>৳<?= number_format($grnData['total_cost'], 2) ?></strong></p>
        <p><strong>Received By:</strong> <?= htmlspecialchars($grnData['staff_name']) ?></p>
      </div>
    </div>
    <?php if ($grnData['note']): ?>
    <p><strong>Note:</strong> <?= htmlspecialchars($grnData['note']) ?></p>
    <?php endif; ?>
  </div>
</div>
<?php endif; ?>

<div class="card">
  <div class="card-header">Add Stock Receipt</div>
  <div class="card-body">
    <form method="POST">
      <div class="row">
        <div class="col-md-5">
          <div class="form-group">
            <label>Product <span class="text-danger">*</span></label>
            <select name="product_id" id="productSelect" class="form-control" required>
              <option value="">-- Select Product --</option>
              <?php foreach ($products as $p): ?>
              <option value="<?= $p['id'] ?>" data-price="<?= $p['purchase_price'] ?>" data-supplier="<?= $p['supplier_id'] ?>">
                <?= htmlspecialchars($p['name']) ?> (<?= $p['sku'] ?>)
              </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label>Supplier</label>
            <select name="supplier_id" id="supplierSelect" class="form-control">
              <option value="">-- Select Supplier --</option>
              <?php foreach ($suppliers as $sup): ?>
              <option value="<?= $sup['id'] ?>"><?= htmlspecialchars($sup['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label>Received Date</label>
            <input type="datetime-local" name="received_date" class="form-control" value="<?= date('Y-m-d\TH:i') ?>">
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label>Quantity <span class="text-danger">*</span></label>
            <input type="number" name="quantity" id="qtyInput" class="form-control" min="1" value="1" required>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label>Unit Cost (৳)</label>
            <input type="number" name="unit_cost" id="unitCostInput" class="form-control" step="0.01" min="0" value="0">
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label>Total Cost (৳)</label>
            <input type="text" id="totalCostDisplay" class="form-control" readonly value="0.00">
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label>&nbsp;</label><br>
            <button type="submit" class="btn btn-primary btn-block">
              <span class="material-icons align-middle" style="font-size:16px;">save</span> Receive
            </button>
          </div>
        </div>
        <div class="col-md-12">
          <div class="form-group">
            <label>Note</label>
            <textarea name="note" class="form-control" rows="2" placeholder="Optional note..."></textarea>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

<?php
$extraJs = <<<JS
<script>
$('#productSelect').on('change', function(){
  var opt = $(this).find(':selected');
  var price = opt.data('price') || 0;
  var supId = opt.data('supplier') || '';
  $('#unitCostInput').val(parseFloat(price).toFixed(2));
  if(supId) $('#supplierSelect').val(supId);
  calcTotal();
});
function calcTotal(){
  var q = parseInt($('#qtyInput').val())||0;
  var c = parseFloat($('#unitCostInput').val())||0;
  $('#totalCostDisplay').val((q*c).toFixed(2));
}
$('#qtyInput, #unitCostInput').on('input', calcTotal);
</script>
JS;
include __DIR__ . '/../../includes/footer.php';
