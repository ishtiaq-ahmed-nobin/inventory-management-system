<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_role(['admin']);
$pageTitle = 'User Management';

$users = $pdo->query('SELECT * FROM users ORDER BY created_at DESC')->fetchAll();
include __DIR__ . '/../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h5 class="mb-0"><span class="material-icons align-middle mr-2">manage_accounts</span>User Management</h5>
  <a href="<?= BASE_URL ?>/modules/users/add.php" class="btn btn-primary">
    <span class="material-icons align-middle mr-1" style="font-size:16px;">person_add</span>Add User
  </a>
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table table-hover datatable mb-0">
      <thead>
        <tr><th>#</th><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Status</th><th>Created</th><th>Actions</th></tr>
      </thead>
      <tbody>
        <?php foreach ($users as $i => $u): ?>
        <tr>
          <td><?= $i + 1 ?></td>
          <td><?= htmlspecialchars($u['name']) ?></td>
          <td><?= htmlspecialchars($u['email']) ?></td>
          <td><?= htmlspecialchars($u['phone'] ?? '—') ?></td>
          <td>
            <span class="badge <?= $u['role']==='admin'?'badge-danger':($u['role']==='manager'?'badge-warning':'badge-secondary') ?>">
              <?= ucfirst($u['role']) ?>
            </span>
          </td>
          <td><span class="status-<?= $u['status'] ?>"><?= ucfirst($u['status']) ?></span></td>
          <td><?= date('d M Y', strtotime($u['created_at'])) ?></td>
          <td>
            <a href="<?= BASE_URL ?>/modules/users/edit.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-outline-primary">
              <span class="material-icons" style="font-size:14px;">edit</span>
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
