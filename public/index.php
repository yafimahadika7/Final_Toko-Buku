<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../app/Helpers/auth.php';
require_once __DIR__ . '/../app/Helpers/view.php';

$route = $_GET['route'] ?? 'dashboard';

// Auth routes
if ($route === 'logout') {
  ensure_session();
  session_unset();
  session_destroy();
  header('Location: ' . BASE_URL . '/public/index.php?route=login');
  exit;
}

if ($route === 'login') {
  ensure_session();
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $st = $pdo->prepare("SELECT id, name, username, role, is_active, password_hash FROM users WHERE username=? LIMIT 1");
    $st->execute([$username]);
    $u = $st->fetch();

    if (!$u) {
      flash_set('error', 'User not found.');
      header('Location: ' . BASE_URL . '/public/index.php?route=login'); exit;
    }
    if ((int)$u['is_active'] !== 1) {
      flash_set('error', 'User inactive.');
      header('Location: ' . BASE_URL . '/public/index.php?route=login'); exit;
    }

    // DEV MODE: plain password_hash column stores plain password
    // (You can switch to bcrypt later.)
    if ($password !== (string)$u['password_hash']) {
      flash_set('error', 'Invalid password.');
      header('Location: ' . BASE_URL . '/public/index.php?route=login'); exit;
    }

    $_SESSION['user'] = [
      'id' => (int)$u['id'],
      'name' => $u['name'],
      'username' => $u['username'],
      'role' => $u['role'],
    ];

    header('Location: ' . BASE_URL . '/public/index.php?route=dashboard');
    exit;
  }

  render('login', ['view' => 'login']);
  exit;
}
// Everything else requires login
require_login();

// Dispatch controllers
require_once __DIR__ . '/../app/Helpers/role.php';

switch ($route) {

  case 'dashboard':
    require_once __DIR__ . '/../app/Controllers/DashboardController.php';
    $res = DashboardController::index($pdo);
    break;

  case 'products':
    require_role(['admin']);
    require_once __DIR__ . '/../app/Controllers/ProductController.php';
    $res = ProductController::index($pdo);
    break;

  case 'categories':
    require_role(['admin']);
    require_once __DIR__ . '/../app/Controllers/CategoryController.php';
    $res = CategoryController::index($pdo);
    break;

  case 'transactions':
    require_role(['admin','kasir']);
    require_once __DIR__ . '/../app/Controllers/TransactionController.php';
    $res = TransactionController::index($pdo);
    break;

  case 'stock_in':
    require_role(['admin','kasir']);
    require_once __DIR__ . '/../app/Controllers/StockInController.php';
    $res = StockInController::index($pdo);
    break;

  case 'users_add':
    require_role(['admin']);
    require_once __DIR__ . '/../app/Controllers/UserAddController.php';
    $res = UserAddController::index($pdo);
    break;

  case 'reports_sales':
    require_role(['owner','admin']);
    require_once __DIR__ . '/../app/Controllers/ReportsSalesController.php';
    $res = ReportsSalesController::index($pdo);
    break;

  case 'reports_stock':
    require_role(['owner','admin']);
    require_once __DIR__ . '/../app/Controllers/ReportsStockController.php';
    $res = ReportsStockController::index($pdo);
    break;


  default:
    http_response_code(404);
    flash_set('error', 'Route not found.');
    header('Location: ' . BASE_URL . '/public/index.php?route=dashboard');
    exit;
}


$view = $res['view'];
$data = $res['data'] ?? [];
$data['view'] = $view;
render($view, $data);
