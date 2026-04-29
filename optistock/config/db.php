<?php
// Auto-detect base URL — works on any server/computer
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
$docRoot  = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? ''), '/');
$selfDir  = rtrim(str_replace('\\', '/', dirname(__DIR__)), '/');
$basePath = str_replace($docRoot, '', $selfDir);

define('BASE_URL', $protocol . '://' . $host . $basePath);

// Database credentials
define('DB_HOST',    'localhost');
define('DB_NAME',    'optistock');
define('DB_USER',    'root');
define('DB_PASS',    '');
define('DB_CHARSET', 'utf8mb4');

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die('<div style="font-family:sans-serif;padding:20px;color:red;"><h2>Database Error</h2><p>' . htmlspecialchars($e->getMessage()) . '</p><p>Please import <code>database/optistock.sql</code> and check credentials in <code>config/db.php</code>.</p></div>');
}

// Flash message helper
function set_flash(string $type, string $msg): void {
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}

function get_flash(): string {
    if (!isset($_SESSION['flash'])) return '';
    $f   = $_SESSION['flash'];
    unset($_SESSION['flash']);
    $cls = $f['type'] === 'success' ? 'success' : ($f['type'] === 'warning' ? 'warning' : 'danger');
    return '<div class="alert alert-' . $cls . ' alert-dismissible fade show" role="alert">'
        . htmlspecialchars($f['msg'])
        . '<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>';
}

// Log activity
function log_activity(PDO $pdo, int $userId, string $action, string $module = ''): void {
    $stmt = $pdo->prepare('INSERT INTO activity_log (user_id, action, module) VALUES (?, ?, ?)');
    $stmt->execute([$userId, $action, $module]);
}
