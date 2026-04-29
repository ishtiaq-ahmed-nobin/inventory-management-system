<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_role(['admin']);
$pageTitle = 'Edit User';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$id]);
$user = $stmt->fetch();
if (!$user) { set_flash('danger','User not found.'); header('Location: ' . BASE_URL . '/modules/users/index.php'); exit; }

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $role     = $_POST['role'] ?? 'staff';
    $status   = $_POST['status'] ?? 'active';

    if (!$name)  $errors[] = 'Name is required.';
    if (!$email) $errors[] = 'Email is required.';

    if ($email) {
        $chk = $pdo->prepare('SELECT id FROM users WHERE email = ? AND id != ?');
        $chk->execute([$email, $id]);
        if ($chk->fetch()) $errors[] = 'Email already exists.';
    }

    if (!$errors) {
        if ($password) {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $pdo->prepare('UPDATE users SET name=?,email=?,phone=?,password=?,role=?,status=? WHERE id=?')->execute([$name,$email,$phone,$hash,$role,$status,$id]);
        } else {
            $pdo->prepare('UPDATE users SET name=?,email=?,phone=?,role=?,status=? WHERE id=?')->execute([$name,$email,$phone,$role,$status,$id]);
        }
        log_activity($pdo, $_SESSION['user_id'], "Updated user: $name", 'users');
        set_flash('success', 'User updated.');
        header('Location: ' . BASE_URL . '/modules/users/index.php');
        exit;
    }
    $user = array_merge($user, compact('name','email','phone','role','status'));
}

include __DIR__ . '/../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h5 class="mb-0"><span class="material-icons align-middle mr-2">edit</span>Edit User</h5>
  <a href="<?= BASE_URL ?>/modules/users/index.php" class="btn btn-outline-secondary btn-sm">Back</a>
</div>

<?php if ($errors): ?><div class="alert alert-danger"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div><?php endif; ?>

<div class="card"><div class="card-body">
  <form method="POST">
    <div class="row">
      <div class="col-md-6"><div class="form-group">
        <label>Full Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
      </div></div>
      <div class="col-md-6"><div class="form-group">
        <label>Email <span class="text-danger">*</span></label>
        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>"
          <?= $id === (int)$_SESSION['user_id'] ? 'readonly' : '' ?> required>
      </div></div>
      <div class="col-md-4"><div class="form-group">
        <label>Phone</label>
        <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
      </div></div>
      <div class="col-md-4"><div class="form-group">
        <label>Role</label>
        <select name="role" class="form-control" <?= $id === (int)$_SESSION['user_id'] ? 'disabled' : '' ?>>
          <option value="staff"   <?= $user['role']==='staff'?'selected':'' ?>>Staff</option>
          <option value="manager" <?= $user['role']==='manager'?'selected':'' ?>>Manager</option>
          <option value="admin"   <?= $user['role']==='admin'?'selected':'' ?>>Admin</option>
        </select>
        <?php if ($id === (int)$_SESSION['user_id']): ?>
          <input type="hidden" name="role" value="<?= htmlspecialchars($user['role']) ?>">
        <?php endif; ?>
      </div></div>
      <div class="col-md-4"><div class="form-group">
        <label>Status</label>
        <select name="status" class="form-control" <?= $id === (int)$_SESSION['user_id'] ? 'disabled' : '' ?>>
          <option value="active"   <?= $user['status']==='active'?'selected':'' ?>>Active</option>
          <option value="inactive" <?= $user['status']==='inactive'?'selected':'' ?>>Inactive</option>
        </select>
        <?php if ($id === (int)$_SESSION['user_id']): ?>
          <input type="hidden" name="status" value="<?= htmlspecialchars($user['status']) ?>">
        <?php endif; ?>
      </div></div>
      <div class="col-md-6"><div class="form-group">
        <label>New Password <small class="text-muted">(leave blank to keep current)</small></label>
        <input type="password" name="password" class="form-control" minlength="6" placeholder="Leave blank to keep unchanged">
      </div></div>
    </div>
    <hr>
    <button type="submit" class="btn btn-primary"><span class="material-icons align-middle mr-1" style="font-size:16px;">save</span>Update User</button>
    <a href="<?= BASE_URL ?>/modules/users/index.php" class="btn btn-outline-secondary ml-2">Cancel</a>
  </form>
</div></div>

<?php
$extraJs = '<script>var baseUrl = "' . BASE_URL . '";</script>';
include __DIR__ . '/../../includes/footer.php';
