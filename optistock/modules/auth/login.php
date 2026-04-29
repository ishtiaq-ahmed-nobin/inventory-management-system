<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

if (isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/modules/dashboard/index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email && $password) {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? AND status = "active" LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['name']     = $user['name'];
            $_SESSION['email']    = $user['email'];
            $_SESSION['role']     = $user['role'];
            $_SESSION['dark_mode'] = $user['dark_mode'];
            log_activity($pdo, $user['id'], 'Logged in', 'auth');
            header('Location: ' . BASE_URL . '/modules/dashboard/index.php');
            exit;
        } else {
            $error = 'Invalid email or password.';
        }
    } else {
        $error = 'Please enter email and password.';
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login — OptiStock</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/dark.css">
<script>
  // Apply dark mode before render to avoid flash
  if (localStorage.getItem('optistock_dark_mode') === '1') {
    document.documentElement.className = 'dark-ready';
  }
  var baseUrl = '<?= BASE_URL ?>';
</script>
</head>
<body>
<div class="login-wrapper">
  <div class="col-md-4 col-sm-10 col-11">
    <div class="card login-card shadow">
      <div class="card-body p-5">
        <div class="text-center mb-4">
          <span class="material-icons login-logo" style="font-size:3rem;">inventory_2</span>
          <h3 class="font-weight-bold login-logo mt-1">OptiStock</h3>
          <p class="text-muted small">Inventory Management System</p>
        </div>

        <?php if ($error): ?>
          <div class="alert alert-danger alert-dismissible fade show">
            <?= htmlspecialchars($error) ?>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
          </div>
        <?php endif; ?>

        <form method="POST">
          <div class="form-group">
            <label><span class="material-icons align-middle mr-1" style="font-size:16px;">email</span>Email</label>
            <input type="email" name="email" class="form-control" placeholder="admin@optistock.com"
              value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required autofocus>
          </div>
          <div class="form-group">
            <label><span class="material-icons align-middle mr-1" style="font-size:16px;">lock</span>Password</label>
            <div class="input-group">
              <input type="password" name="password" id="passwordInput" class="form-control" placeholder="Enter password" required>
              <div class="input-group-append">
                <button type="button" class="btn btn-outline-secondary" id="togglePass" tabindex="-1">
                  <span class="material-icons" style="font-size:16px;vertical-align:middle;" id="passIcon">visibility</span>
                </button>
              </div>
            </div>
          </div>
          <button type="submit" class="btn btn-primary btn-block mt-3">
            <span class="material-icons align-middle mr-1" style="font-size:16px;">login</span>Sign In
          </button>
        </form>

        <div class="text-center mt-4">
          <small class="text-muted">Default: admin@optistock.com / admin123</small>
        </div>
      </div>
      <div class="card-footer text-center text-muted py-2">
        <small>&copy; <?= date('Y') ?> OptiStock</small>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/darkmode.js"></script>
<script>
$(function(){
  $('#togglePass').on('click', function(){
    var t = $('#passwordInput');
    var i = $('#passIcon');
    if(t.attr('type')==='password'){t.attr('type','text');i.text('visibility_off');}
    else{t.attr('type','password');i.text('visibility');}
  });
});
</script>
</body>
</html>
