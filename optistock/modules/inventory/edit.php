<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_role(['admin', 'manager']);
$pageTitle = 'Edit Product';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
$stmt->execute([$id]);
$product = $stmt->fetch();
if (!$product) { set_flash('danger','Product not found.'); header('Location: ' . BASE_URL . '/modules/inventory/index.php'); exit; }

$categories = $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();
$suppliers  = $pdo->query('SELECT * FROM suppliers ORDER BY name')->fetchAll();
$locations  = $pdo->query('SELECT * FROM locations ORDER BY name')->fetchAll();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'name'            => trim($_POST['name'] ?? ''),
        'sku'             => trim($_POST['sku'] ?? ''),
        'barcode'         => trim($_POST['barcode'] ?? ''),
        'category_id'     => $_POST['category_id'] ?: null,
        'supplier_id'     => $_POST['supplier_id'] ?: null,
        'location_id'     => $_POST['location_id'] ?: null,
        'purchase_price'  => (float)($_POST['purchase_price'] ?? 0),
        'selling_price'   => (float)($_POST['selling_price'] ?? 0),
        'quantity'        => (int)($_POST['quantity'] ?? 0),
        'low_stock_alert' => (int)($_POST['low_stock_alert'] ?? 5),
        'description'     => trim($_POST['description'] ?? ''),
        'status'          => $_POST['status'] ?? 'active',
    ];

    if (!$data['name']) $errors[] = 'Product name is required.';
    if (!$data['sku'])  $errors[] = 'SKU is required.';

    if ($data['sku']) {
        $chk = $pdo->prepare('SELECT id FROM products WHERE sku = ? AND id != ?');
        $chk->execute([$data['sku'], $id]);
        if ($chk->fetch()) $errors[] = 'SKU already exists.';
    }

    if (!$errors) {
        $stmt = $pdo->prepare('UPDATE products SET name=?,sku=?,barcode=?,category_id=?,supplier_id=?,location_id=?,purchase_price=?,selling_price=?,quantity=?,low_stock_alert=?,description=?,status=? WHERE id=?');
        $stmt->execute([$data['name'],$data['sku'],$data['barcode'],$data['category_id'],$data['supplier_id'],$data['location_id'],$data['purchase_price'],$data['selling_price'],$data['quantity'],$data['low_stock_alert'],$data['description'],$data['status'],$id]);
        log_activity($pdo, $_SESSION['user_id'], 'Updated product: ' . $data['name'], 'inventory');
        set_flash('success', 'Product updated successfully.');
        header('Location: ' . BASE_URL . '/modules/inventory/index.php');
        exit;
    }
    $product = array_merge($product, $data);
}

include __DIR__ . '/../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h5 class="mb-0"><span class="material-icons align-middle mr-2">edit</span>Edit Product</h5>
  <a href="<?= BASE_URL ?>/modules/inventory/index.php" class="btn btn-outline-secondary btn-sm">
    <span class="material-icons align-middle" style="font-size:14px;">arrow_back</span> Back
  </a>
</div>

<?php if ($errors): ?>
  <div class="alert alert-danger"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div>
<?php endif; ?>

<div class="card">
  <div class="card-body">
    <form method="POST">
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label>Product Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label>SKU <span class="text-danger">*</span></label>
            <input type="text" name="sku" id="skuInput" class="form-control" value="<?= htmlspecialchars($product['sku']) ?>" required>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label>Barcode</label>
            <div class="input-group">
              <input type="text" name="barcode" id="barcodeInput" class="form-control" value="<?= htmlspecialchars($product['barcode'] ?? '') ?>">
              <div class="input-group-append">
                <button type="button" class="btn btn-outline-secondary" id="genBarcode" title="Generate">
                  <span class="material-icons" style="font-size:14px;vertical-align:middle;">barcode_reader</span>
                </button>
              </div>
            </div>
            <div class="mt-2 text-center" id="barcodePreview"></div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label>Category</label>
            <select name="category_id" class="form-control">
              <option value="">-- Select Category --</option>
              <?php foreach ($categories as $cat): ?>
              <option value="<?= $cat['id'] ?>" <?= $product['category_id'] == $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label>Supplier</label>
            <select name="supplier_id" class="form-control">
              <option value="">-- Select Supplier --</option>
              <?php foreach ($suppliers as $sup): ?>
              <option value="<?= $sup['id'] ?>" <?= $product['supplier_id'] == $sup['id'] ? 'selected' : '' ?>><?= htmlspecialchars($sup['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label>Location</label>
            <select name="location_id" class="form-control">
              <option value="">-- Select Location --</option>
              <?php foreach ($locations as $loc): ?>
              <option value="<?= $loc['id'] ?>" <?= $product['location_id'] == $loc['id'] ? 'selected' : '' ?>><?= htmlspecialchars($loc['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label>Purchase Price (৳)</label>
            <input type="number" name="purchase_price" class="form-control" step="0.01" min="0" value="<?= htmlspecialchars($product['purchase_price']) ?>">
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label>Selling Price (৳)</label>
            <input type="number" name="selling_price" class="form-control" step="0.01" min="0" value="<?= htmlspecialchars($product['selling_price']) ?>">
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label>Quantity</label>
            <input type="number" name="quantity" class="form-control" min="0" value="<?= htmlspecialchars($product['quantity']) ?>">
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label>Low Stock Alert</label>
            <input type="number" name="low_stock_alert" class="form-control" min="0" value="<?= htmlspecialchars($product['low_stock_alert']) ?>">
          </div>
        </div>
        <div class="col-md-9">
          <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="2"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control">
              <option value="active" <?= $product['status'] === 'active' ? 'selected' : '' ?>>Active</option>
              <option value="inactive" <?= $product['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
            </select>
          </div>
        </div>
      </div>
      <hr>
      <button type="submit" class="btn btn-primary">
        <span class="material-icons align-middle mr-1" style="font-size:16px;">save</span>Update Product
      </button>
      <a href="<?= BASE_URL ?>/modules/inventory/index.php" class="btn btn-outline-secondary ml-2">Cancel</a>
    </form>
  </div>
</div>

<?php
$extraJs = '<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>' . <<<'JS'
<script>
function renderBarcode(val){
  if(!val){document.getElementById('barcodePreview').innerHTML='';return;}
  document.getElementById('barcodePreview').innerHTML='<svg id="barcodeSvg"></svg>';
  try{JsBarcode('#barcodeSvg',val,{format:'CODE128',width:1.5,height:40,displayValue:true,fontSize:10});}catch(e){}
}
document.getElementById('genBarcode').addEventListener('click',function(){
  var val=Date.now().toString().slice(-12);
  document.getElementById('barcodeInput').value=val;renderBarcode(val);
});
document.getElementById('barcodeInput').addEventListener('input',function(){renderBarcode(this.value);});
(function(){ var v=document.getElementById('barcodeInput').value; if(v) renderBarcode(v); })();
</script>
JS;
include __DIR__ . '/../../includes/footer.php';
