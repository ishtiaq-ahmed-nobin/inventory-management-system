<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_role(['admin', 'manager']);
$pageTitle = 'Add Supplier';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if (!$name) $errors[] = 'Supplier name is required.';

    if (!$errors) {
        $stmt = $pdo->prepare('INSERT INTO suppliers (name, phone, email, address) VALUES (?, ?, ?, ?)');
        $stmt->execute([$name, $phone, $email, $address]);
        log_activity($pdo, $_SESSION['user_id'], "Added supplier: $name", 'suppliers');
        set_flash('success', 'Supplier added successfully.');
        header('Location: ' . BASE_URL . '/modules/suppliers/index.php');
        exit;
    }
}

include __DIR__ . '/../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h5 class="mb-0"><span class="material-icons align-middle mr-2">add</span>Add Supplier</h5>
  <a href="<?= BASE_URL ?>/modules/suppliers/index.php" class="btn btn-outline-secondary btn-sm">Back</a>
</div>

<?php if ($errors): ?><div class="alert alert-danger"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div><?php endif; ?>

<div class="card"><div class="card-body">
  <form method="POST">
    <div class="row">
      <div class="col-md-6"><div class="form-group">
        <label>Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
      </div></div>
      <div class="col-md-3"><div class="form-group">
        <label>Phone</label>
        <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
      </div></div>
      <div class="col-md-3"><div class="form-group">
        <label>Email</label>
        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
      </div></div>
      <div class="col-md-12"><div class="form-group">
        <label>Address</label>
        <textarea name="address" class="form-control" rows="2"><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
      </div></div>
    </div>
    <hr>
    <button type="submit" class="btn btn-primary"><span class="material-icons align-middle mr-1" style="font-size:16px;">save</span>Save</button>
    <a href="<?= BASE_URL ?>/modules/suppliers/index.php" class="btn btn-outline-secondary ml-2">Cancel</a>
  </form>
</div></div>

<?php
$extraJs = '<script>var baseUrl = "' . BASE_URL . '";</script>';
include __DIR__ . '/../../includes/footer.php';
