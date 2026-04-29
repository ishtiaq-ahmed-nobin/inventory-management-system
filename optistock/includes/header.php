<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!defined('BASE_URL')) require_once __DIR__ . '/../config/db.php';
$darkClass = ($_SESSION['dark_mode'] ?? 0) ? 'dark-mode' : '';
$pageTitle = $pageTitle ?? 'OptiStock';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle) ?> — OptiStock</title>
<link rel="icon" href="favicon.ico" type="/optistock/assets/img/favicon.svg">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/dark.css">
<?php if (!empty($extraCss)) echo $extraCss; ?>
</head>
<body class="<?= $darkClass ?>">

<div class="wrapper d-flex">
  <!-- Sidebar -->
  <?php include __DIR__ . '/sidebar.php'; ?>

  <!-- Main content -->
  <div class="main-content flex-grow-1">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom px-3 py-2">
      <button class="btn btn-sm btn-outline-secondary mr-3 d-lg-none" id="sidebarToggle">
        <span class="material-icons" style="font-size:20px;vertical-align:middle;">menu</span>
      </button>
      <span class="navbar-brand mb-0 h6 text-muted"><?= htmlspecialchars($pageTitle) ?></span>
      <div class="ml-auto d-flex align-items-center">
        <button id="darkModeToggle" class="btn btn-sm btn-outline-secondary mr-2" title="Toggle dark mode">
          <span class="material-icons" id="darkModeIcon" style="font-size:18px;vertical-align:middle;">
            <?= ($_SESSION['dark_mode'] ?? 0) ? 'light_mode' : 'dark_mode' ?>
          </span>
        </button>
        <div class="dropdown">
          <a href="#" class="dropdown-toggle text-dark text-decoration-none" data-toggle="dropdown">
            <span class="material-icons align-middle" style="font-size:20px;">account_circle</span>
            <span class="ml-1 d-none d-md-inline"><?= htmlspecialchars($_SESSION['name'] ?? 'User') ?></span>
            <span class="badge badge-info ml-1"><?= htmlspecialchars(ucfirst($_SESSION['role'] ?? '')) ?></span>
          </a>
          <div class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="<?= BASE_URL ?>/modules/auth/logout.php">
              <span class="material-icons align-middle mr-1" style="font-size:16px;">logout</span>Logout
            </a>
          </div>
        </div>
      </div>
    </nav>

    <!-- Page content -->
    <div class="content-area p-4">
      <?= get_flash() ?>
