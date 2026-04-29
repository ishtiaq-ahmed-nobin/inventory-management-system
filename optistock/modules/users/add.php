<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_role(['admin']);
$pageTitle = 'Add User';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $role     = $_POST['role'] ?? 'staff';
    $status   = $_POST['status'] ?? 'active';

    if (!$name)     $errors[] = 'Name is required.';
    if (!$email)    $errors[] = 'Email is required.';
    if (!$password) $errors[] = 'Password is required.';
    if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';

    if ($email) {
        $chk = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $chk->execute([$email]);
        if ($chk->fetch()) $errors[] = 'Email already exists.';
    }

    if (!$errors) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $pdo->prepare('INSERT INTO users (name,email,phone,password,role,status) VALUES (?,?,?,?,?,?)')->execute([$name,$email,$phone,$hash,$role,$status]);
        log_activity($pdo, $_SESSION['user_id'], "Added user: $name ($role)", 'users');
        set_flash('success', 'User created successfully.');
        header('Location: ' . BASE_URL . '/modules/users/index.php');
        exit;
    }
}

include __DIR__ . '/../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h5 class="mb-0"><span class="material-icons align-middle mr-2">person_add</span>Add User</h5>
  <a href="<?= BASE_URL ?>/modules/users/index.php" class="btn btn-outline-secondary btn-sm">Back</a>
</div>

<?php if ($errors): ?><div class="alert alert-danger"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div><?php endif; ?>

<div class="card"><div class="card-body">
  <form method="POST">
    <div class="row">
      <div class="col-md-6"><div class="form-group">
        <label>Full Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
      </div></div>
      <div class="col-md-6"><div class="form-group">
        <label>Email <span class="text-danger">*</span></label>
        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
      </div></div>
      <div class="col-md-4"><div class="form-group">
        <label>Phone</label>
        <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
      </div></div>
      <div class="col-md-4"><div class="form-group">
        <label>Role</label>
        <select name="role" class="form-control">
          <option value="staff"   <?= ($_POST['role'] ?? 'staff')==='staff'?'selected':'' ?>>Staff</option>
          <option value="manager" <?= ($_POST['role'] ?? '')==='manager'?'selected':'' ?>>Manager</option>
          <option value="admin"   <?= ($_POST['role'] ?? '')==='admin'?'selected':'' ?>>Admin</option>
        </select>
      </div></div>
      <div class="col-md-4"><div class="form-group">
        <label>Status</label>
        <select name="status" class="form-control">
          <option value="active"   <?= ($_POST['status'] ?? 'active')==='active'?'selected':'' ?>>Active</option>
          <option value="inactive" <?= ($_POST['status'] ?? '')==='inactive'?'selected':'' ?>>Inactive</option>
        </select>
      </div></div>
      <div class="col-md-6"><div class="form-group">
        <label>Password <span class="text-danger">*</span></label>
        <input type="password" name="password" class="form-control" required minlength="6">
      </div></div>
    </div>
    <hr>
    <button type="submit" class="btn btn-primary"><span class="material-icons align-middle mr-1" style="font-size:16px;">save</span>Create User</button>
    <a href="<?= BASE_URL ?>/modules/users/index.php" class="btn btn-outline-secondary ml-2">Cancel</a>
  </form>
</div></div>

<?php
$extraJs = '<script>var baseUrl = "' . BASE_URL . '";</script>';
include __DIR__ . '/../../includes/footer.php';
