<?php
require_once __DIR__ . '/../../includes/auth_check.php';
$pageTitle = 'Point of Sale';

$customers = $pdo->query('SELECT * FROM customers ORDER BY name')->fetchAll();

// Load all active products server-side (no AJAX needed)
$allProducts = $pdo->query(
    'SELECT id, name, sku, barcode, selling_price, quantity FROM products WHERE status = "active" ORDER BY name'
)->fetchAll();

// Handle sale submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['complete_sale'])) {
    $cartJson   = $_POST['cart_json'] ?? '[]';
    $cart       = json_decode($cartJson, true);
    $customerId = $_POST['customer_id'] ?: null;
    $discount   = (float)($_POST['discount'] ?? 0);
    $paidAmount = (float)($_POST['paid_amount'] ?? 0);
    $payType    = $_POST['payment_type'] ?? 'cash';
    $note       = trim($_POST['note'] ?? '');

    $errors = [];
    if (!$cart) $errors[] = 'Cart is empty.';

    foreach ($cart as $item) {
        $chk = $pdo->prepare('SELECT quantity FROM products WHERE id = ?');
        $chk->execute([$item['id']]);
        $row = $chk->fetch();
        if (!$row || $row['quantity'] < $item['qty']) {
            $errors[] = 'Insufficient stock for: ' . htmlspecialchars($item['name']);
        }
    }

    if (!$errors) {
        $subtotal  = array_sum(array_map(fn($i) => $i['qty'] * $i['price'], $cart));
        $total     = max(0, $subtotal - $discount);
        $due       = max(0, $total - $paidAmount);
        $invoiceNo = 'INV-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

        $stmt = $pdo->prepare('INSERT INTO sales (invoice_no,customer_id,user_id,subtotal,discount,total_amount,paid_amount,due_amount,payment_type,note,sale_date) VALUES (?,?,?,?,?,?,?,?,?,?,NOW())');
        $stmt->execute([$invoiceNo, $customerId, $_SESSION['user_id'], $subtotal, $discount, $total, $paidAmount, $due, $payType, $note]);
        $saleId = $pdo->lastInsertId();

        foreach ($cart as $item) {
            $pdo->prepare('INSERT INTO sale_items (sale_id,product_id,quantity,unit_price,total_price) VALUES (?,?,?,?,?)')->execute([$saleId, $item['id'], $item['qty'], $item['price'], $item['qty'] * $item['price']]);
            $pdo->prepare('UPDATE products SET quantity = quantity - ? WHERE id = ?')->execute([$item['qty'], $item['id']]);
        }

        if ($customerId && $due > 0) {
            $pdo->prepare('UPDATE customers SET due_balance = due_balance + ? WHERE id = ?')->execute([$due, $customerId]);
        }

        log_activity($pdo, $_SESSION['user_id'], "Completed sale $invoiceNo total ৳$total", 'sales');
        header('Location: ' . BASE_URL . '/modules/sales/invoice.php?invoice=' . urlencode($invoiceNo));
        exit;
    }
}

// Embed product list as JSON for client-side filtering
$productsJson = json_encode($allProducts);

include __DIR__ . '/../../includes/header.php';
?>

<?php if (!empty($errors)): ?>
  <div class="alert alert-danger"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div>
<?php endif; ?>

<div class="row" style="min-height:80vh;">

  <!-- Left: Products -->
  <div class="col-md-5 mb-4">
    <div class="card h-100 d-flex flex-column">
      <div class="card-header font-weight-bold d-flex align-items-center">
        <span class="material-icons align-middle mr-2" style="font-size:18px;">inventory</span>
        Products
        <span class="badge badge-secondary ml-2" id="productCount"><?= count($allProducts) ?></span>
        <div class="ml-auto" style="width: 50%;">
          <div class="input-group input-group-sm">
            <div class="input-group-prepend">
              <span class="input-group-text"><span class="material-icons" style="font-size:14px;">search</span></span>
            </div>
            <input type="text" id="productSearch" class="form-control"
              placeholder="Search name, SKU or scan barcode...">
            <div class="input-group-append">
              <button class="btn btn-outline-secondary" type="button" id="clearSearch" title="Clear">
                <span class="material-icons" style="font-size:14px;vertical-align:middle;">close</span>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Product grid -->
      <div class="card-body p-2" style="overflow-y:auto; max-height:72vh;">
        <div class="row m-0" id="productGrid">
          <?php foreach ($allProducts as $p): ?>
          <div class="col-6 p-1 product-item"
               data-name="<?= strtolower(htmlspecialchars($p['name'])) ?>"
               data-sku="<?= strtolower(htmlspecialchars($p['sku'])) ?>"
               data-barcode="<?= htmlspecialchars($p['barcode'] ?? '') ?>">
            <div class="card pos-product-card h-100 p-2"
                 onclick="addToCart(<?= htmlspecialchars(json_encode($p)) ?>)"
                 style="cursor:pointer;">
              <div class="font-weight-bold" style="font-size:.82rem; line-height:1.2; min-height:2.4em;">
                <?= htmlspecialchars($p['name']) ?>
              </div>
              <div class="text-muted" style="font-size:.72rem;"><?= htmlspecialchars($p['sku']) ?></div>
              <div class="d-flex justify-content-between align-items-center mt-1">
                <span class="text-primary font-weight-bold" style="font-size:.85rem;">
                  ৳<?= number_format($p['selling_price'], 2) ?>
                </span>
                <?php if ($p['quantity'] <= 0): ?>
                  <span class="stock-out">Out</span>
                <?php elseif ($p['quantity'] <= 5): ?>
                  <span class="stock-low"><?= $p['quantity'] ?></span>
                <?php else: ?>
                  <span class="stock-ok"><?= $p['quantity'] ?></span>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
          <?php if (!$allProducts): ?>
          <div class="col-12 text-center text-muted py-5">
            <span class="material-icons" style="font-size:3rem;">inventory_2</span>
            <p class="mt-2">No active products found.<br>
              <a href="<?= BASE_URL ?>/modules/inventory/add.php">Add a product</a>
            </p>
          </div>
          <?php endif; ?>
        </div>
        <p class="text-center text-muted py-3 d-none" id="noResults">No products match your search.</p>
      </div>
    </div>
  </div>

  <!-- Right: Cart -->
  <div class="col-md-7 mb-4">
    <div class="card h-100 d-flex flex-column">
      <div class="card-header font-weight-bold d-flex align-items-center">
        <span class="material-icons align-middle mr-2" style="font-size:18px;">shopping_cart</span>
        Cart
        <span class="badge badge-primary ml-1" id="cartCount">0</span>
        <button type="button" class="btn btn-outline-danger btn-sm ml-auto" onclick="clearCart()" title="Clear cart">
          <span class="material-icons align-middle" style="font-size:14px;">delete_sweep</span>
        </button>
      </div>

      <div class="card-body p-0 d-flex flex-column" style="overflow:hidden;">
        <!-- Cart items -->
        <div style="overflow-y:auto; flex:1; max-height:30vh;">
          <table class="table table-sm mb-0" id="cartTable">
            <thead class="thead-light" style="position:sticky;top:0;z-index:1;">
              <tr>
                <th>Product</th>
                <th style="width:70px;">Qty</th>
                <th style="width:80px;">Price</th>
                <th style="width:80px;">Total</th>
                <th style="width:32px;"></th>
              </tr>
            </thead>
            <tbody id="cartBody">
              <tr id="emptyCartRow">
                <td colspan="5" class="text-center text-muted py-3" style="font-size:.85rem;">
                  <span class="material-icons d-block" style="font-size:2rem;opacity:.3;">shopping_cart</span>
                  Click a product to add
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Totals + payment form -->
        <div class="border-top p-3">
          <form method="POST" id="saleForm">
            <input type="hidden" name="complete_sale" value="1">
            <input type="hidden" name="cart_json" id="cartJson">

            <div class="d-flex justify-content-between mb-1">
              <span class="text-muted">Subtotal</span>
              <span id="cartSubtotal" class="font-weight-bold">৳0.00</span>
            </div>

            <div class="form-group mb-2">
              <div class="input-group input-group-sm">
                <div class="input-group-prepend"><span class="input-group-text">Discount ৳</span></div>
                <input type="number" name="discount" id="discountInput" class="form-control" step="0.01" min="0" value="0">
              </div>
            </div>

            <div class="d-flex justify-content-between mb-2 font-weight-bold" style="font-size:1.05rem;">
              <span>Total</span>
              <span id="totalDisplay" class="text-primary">৳0.00</span>
            </div>

            <div class="form-group mb-2">
              <div class="input-group input-group-sm">
                <div class="input-group-prepend"><span class="input-group-text">Paid ৳</span></div>
                <input type="number" name="paid_amount" id="paidInput" class="form-control" step="0.01" min="0" value="0">
              </div>
            </div>

            <div class="d-flex justify-content-between mb-3">
              <span class="text-muted">Due</span>
              <span id="dueDisplay" class="font-weight-bold text-danger">৳0.00</span>
            </div>

            <div class="form-group mb-2">
              <select name="customer_id" class="form-control form-control-sm">
                <?php foreach ($customers as $c): ?>
                <option value="<?= $c['id'] ?>" <?= $c['name'] === 'Walk-in Customer' ? 'selected' : '' ?>>
                  <?= htmlspecialchars($c['name']) ?>
                </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-group mb-2">
              <select name="payment_type" class="form-control form-control-sm">
                <option value="cash">Cash</option>
                <option value="card">Card</option>
                <option value="mobile_banking">Mobile Banking (bKash/Nagad)</option>
              </select>
            </div>

            <div class="form-group mb-3">
              <input type="text" name="note" class="form-control form-control-sm" placeholder="Note (optional)">
            </div>

            <button type="submit" class="btn btn-primary btn-block" id="completeSaleBtn">
              <span class="material-icons align-middle mr-1" style="font-size:18px;">check_circle</span>
              Complete Sale
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
$extraJs = <<<'JS'
<script>
var cart = [];

/* ── Cart rendering ── */
function renderCart() {
  var tbody  = document.getElementById('cartBody');
  var empty  = document.getElementById('emptyCartRow');
  var sub    = 0;

  // Remove old item rows (keep emptyCartRow)
  var rows = tbody.querySelectorAll('tr.cart-item');
  rows.forEach(function(r){ tbody.removeChild(r); });

  if (cart.length === 0) {
    empty.style.display = '';
  } else {
    empty.style.display = 'none';
    cart.forEach(function(item, i) {
      var lineTotal = item.qty * item.price;
      sub += lineTotal;
      var tr = document.createElement('tr');
      tr.className = 'cart-item';
      tr.innerHTML =
        '<td style="font-size:.8rem;max-width:100px;overflow:hidden;">' +
          '<span title="' + item.name + '">' + item.name.substring(0,20) + (item.name.length>20?'…':'') + '</span>' +
        '</td>' +
        '<td>' +
          '<input type="number" class="form-control form-control-sm p-1" style="width:55px;" ' +
          'value="' + item.qty + '" min="1" max="' + item.stock + '" ' +
          'onchange="updateQty(' + i + ',this.value)">' +
        '</td>' +
        '<td style="font-size:.8rem;">৳' + item.price.toFixed(2) + '</td>' +
        '<td style="font-size:.8rem;">৳' + lineTotal.toFixed(2) + '</td>' +
        '<td>' +
          '<button type="button" class="btn btn-sm btn-outline-danger p-0 px-1" onclick="removeItem(' + i + ')">' +
            '<span class="material-icons" style="font-size:14px;line-height:1.4;">close</span>' +
          '</button>' +
        '</td>';
      tbody.appendChild(tr);
    });
  }

  document.getElementById('cartSubtotal').textContent = '৳' + sub.toFixed(2);
  document.getElementById('cartCount').textContent = cart.length;
  calcTotal(sub);
  document.getElementById('cartJson').value = JSON.stringify(cart);
}

function calcTotal(sub) {
  if (sub === undefined) sub = cart.reduce(function(a,i){ return a + i.qty*i.price; }, 0);
  var disc  = parseFloat(document.getElementById('discountInput').value) || 0;
  var total = Math.max(0, sub - disc);
  var paid  = parseFloat(document.getElementById('paidInput').value) || 0;
  var due   = Math.max(0, total - paid);
  document.getElementById('totalDisplay').textContent = '৳' + total.toFixed(2);
  document.getElementById('dueDisplay').textContent   = '৳' + due.toFixed(2);
}

function addToCart(product) {
  var id  = parseInt(product.id);
  var idx = cart.findIndex(function(i){ return i.id === id; });
  if (idx >= 0) {
    if (cart[idx].qty < parseInt(product.quantity)) {
      cart[idx].qty++;
    } else {
      alert('Not enough stock! Available: ' + product.quantity);
      return;
    }
  } else {
    if (parseInt(product.quantity) < 1) {
      alert('This product is out of stock!');
      return;
    }
    cart.push({
      id:    id,
      name:  product.name,
      sku:   product.sku,
      price: parseFloat(product.selling_price),
      qty:   1,
      stock: parseInt(product.quantity)
    });
  }
  renderCart();
}

function removeItem(i) { cart.splice(i, 1); renderCart(); }
function clearCart()   { cart = [];          renderCart(); }

function updateQty(i, val) {
  var q = Math.max(1, Math.min(parseInt(val) || 1, cart[i].stock));
  cart[i].qty = q;
  renderCart();
}

/* ── Client-side product search / filter ── */
var allItems = document.querySelectorAll('.product-item');

document.getElementById('productSearch').addEventListener('input', function() {
  filterProducts(this.value.trim().toLowerCase());
});

document.getElementById('clearSearch').addEventListener('click', function() {
  document.getElementById('productSearch').value = '';
  filterProducts('');
  document.getElementById('productSearch').focus();
});

function filterProducts(q) {
  var shown = 0;
  allItems.forEach(function(el) {
    var match = !q ||
      el.dataset.name.includes(q) ||
      el.dataset.sku.includes(q) ||
      el.dataset.barcode.includes(q);
    el.style.display = match ? '' : 'none';
    if (match) shown++;
  });
  document.getElementById('productCount').textContent = shown;
  document.getElementById('noResults').classList.toggle('d-none', shown > 0);
}

/* ── Barcode scanner: Enter triggers single-match add ── */
document.getElementById('productSearch').addEventListener('keydown', function(e) {
  if (e.key !== 'Enter') return;
  e.preventDefault();
  var q = this.value.trim().toLowerCase();
  if (!q) return;
  var matches = [];
  allItems.forEach(function(el) {
    if (el.dataset.barcode === q || el.dataset.sku === q) matches.push(el);
  });
  if (matches.length === 1) {
    matches[0].querySelector('.pos-product-card').click();
    document.getElementById('productSearch').value = '';
    filterProducts('');
  }
});

/* ── Discount / paid listeners ── */
document.getElementById('discountInput').addEventListener('input', function(){ calcTotal(); });
document.getElementById('paidInput').addEventListener('input',    function(){ calcTotal(); });

/* ── Form submit guard ── */
document.getElementById('saleForm').addEventListener('submit', function(e) {
  if (!cart.length) { e.preventDefault(); alert('Cart is empty — add products first.'); return; }
  document.getElementById('cartJson').value = JSON.stringify(cart);
});

renderCart();
</script>
JS;
include __DIR__ . '/../../includes/footer.php';
