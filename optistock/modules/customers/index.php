<?php
require_once __DIR__ . '/../../includes/auth_check.php';
$pageTitle = 'Customers';

$customers = $pdo->query('SELECT * FROM customers ORDER BY name')->fetchAll();
include __DIR__ . '/../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h5 class="mb-0"><span class="material-icons align-middle mr-2">people</span>Customers</h5>
  <a href="<?= BASE_URL ?>/modules/customers/add.php" class="btn btn-primary">
    <span class="material-icons align-middle mr-1" style="font-size:16px;">person_add</span>Add Customer
  </a>
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table table-hover datatable mb-0">
      <thead>
        <tr><th>#</th><th>Name</th><th>Phone</th><th>Email</th><th>Due Balance</th><th>Actions</th></tr>
      </thead>
      <tbody>
        <?php foreach ($customers as $i => $c): ?>
        <tr>
          <td><?= $i + 1 ?></td>
          <td><strong><?= htmlspecialchars($c['name']) ?></strong></td>
          <td><?= htmlspecialchars($c['phone'] ?? '—') ?></td>
          <td><?= htmlspecialchars($c['email'] ?? '—') ?></td>
          <td><?= $c['due_balance'] > 0 ? '<span class="text-danger">৳' . number_format($c['due_balance'], 2) . '</span>' : '৳0.00' ?></td>
          <td>
            <a href="<?= BASE_URL ?>/modules/customers/edit.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-outline-primary">
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
