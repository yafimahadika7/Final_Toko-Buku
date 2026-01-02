<?php
declare(strict_types=1);
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../Helpers/auth.php';

$u = current_user();
$success = flash_get('success');
$error = flash_get('error');
$role = $u['role'] ?? '';

$routes = [
  'dashboard'    => ['label' => 'Dashboard',     'path' => 'dashboard',     'icon' => '🏠', 'roles' => ['admin','kasir','owner']],
  'products'     => ['label' => 'Products',      'path' => 'products',      'icon' => '📦', 'roles' => ['admin']],                 // admin only
  'categories'   => ['label' => 'Categories',    'path' => 'categories',    'icon' => '🏷️', 'roles' => ['admin']],                 // admin only

  'transactions' => ['label' => 'Transactions',  'path' => 'transactions',  'icon' => '🧾', 'roles' => ['admin','kasir']],          // admin+kasir

  'stock_in'     => ['label' => 'Add Stock',     'path' => 'stock_in',      'icon' => '➕', 'roles' => ['admin','kasir']],          // kasir boleh tambah stok

  'reports_sales'=> ['label' => 'Sales Report',  'path' => 'reports_sales', 'icon' => '📈', 'roles' => ['owner', 'admin']],                 // owner only
  'reports_stock'=> ['label' => 'Stock Report',  'path' => 'reports_stock', 'icon' => '📊', 'roles' => ['owner', 'admin']],                 // owner only

  'users_add'    => ['label' => 'Add User',      'path' => 'users_add',     'icon' => '👥', 'roles' => ['admin']],                 // admin only
];

$route = $_GET['route'] ?? 'dashboard';
$title = $routes[$route]['label'] ?? 'Dashboard';

?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($title) ?> • <?= htmlspecialchars(APP_NAME) ?></title>

  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          boxShadow: {
            soft: '0 10px 30px rgba(0,0,0,.25)'
          }
        }
      }
    }
  </script>
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/app.css">
  <script>window.BASE_URL = <?=json_encode(BASE_URL) ?>;</script>
</head>

<body class="bg-slate-950 text-slate-100">
  <?php if ($route === 'login'): ?>
    <?php require __DIR__ . '/login.php'; ?>
  <?php else: ?>
  <div class="min-h-screen flex">
    <!-- Sidebar -->
    <aside class="w-72 bg-slate-950 border-r border-slate-800/60 hidden md:block">
      <div class="px-6 py-6">
        <div class="flex items-center gap-3">
          <div class="h-10 w-10 rounded-xl bg-slate-800 grid place-items-center shadow-soft">🛒</div>
          <div>
            <div class="font-semibold leading-tight"><?= htmlspecialchars(APP_NAME) ?></div>
            <div class="text-xs text-slate-400"><?= htmlspecialchars($u['role'] ?? 'user') ?></div>
          </div>
        </div>
      </div>

      <nav class="px-3 pb-6">
      <?php foreach ($routes as $key => $item): 
      if (!in_array($role, $item['roles'], true)) continue;
      $active = ($route === $key);
      ?>
          <a href="<?= BASE_URL ?>/public/index.php?route=<?= urlencode($key) ?>"
             class="flex items-center gap-3 px-4 py-3 rounded-xl mb-1
                    <?= $active ? 'bg-slate-800/70 text-white' : 'text-slate-300 hover:bg-slate-900/60 hover:text-white' ?>">
            <span class="text-lg"><?= $item['icon'] ?></span>
            <span class="text-sm font-medium"><?= htmlspecialchars($item['label']) ?></span>
          </a>
        <?php endforeach; ?>

        <div class="mt-4 border-t border-slate-800/60 pt-4 px-4">
          <div class="text-xs text-slate-400">Signed in as</div>
          <div class="text-sm font-medium"><?= htmlspecialchars($u['name'] ?? $u['username'] ?? '-') ?></div>

          <a href="<?= BASE_URL ?>/public/index.php?route=logout"
             class="mt-3 inline-flex items-center justify-center w-full px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-800/60 text-sm">
            Logout
          </a>
        </div>
      </nav>
    </aside>

    <!-- Main -->
    <main class="flex-1">
      <!-- Top bar (minimal, matches v0 vibe) -->
      <div class="sticky top-0 z-10 bg-slate-950/80 backdrop-blur border-b border-slate-800/60">
        <div class="px-6 py-4 flex items-center justify-between">
          <div>
            <div class="text-sm text-slate-400">Overview</div>
            <div class="text-xl font-semibold"><?= htmlspecialchars($title) ?></div>
          </div>

          <div class="flex items-center gap-3">
            <div class="hidden sm:block text-sm text-slate-300"><?= htmlspecialchars($u['username'] ?? '') ?></div>
            <div class="h-9 w-9 rounded-full bg-slate-800 grid place-items-center">🙂</div>
          </div>
        </div>
      </div>

      <div class="px-6 py-6">
        <?php if ($success): ?>
          <div class="mb-4 rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-emerald-200">
            <?= $success ?>
          </div>
        <?php endif; ?>
        <?php if ($error): ?>
          <div class="mb-4 rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-rose-200">
            <?= $error ?>
          </div>
        <?php endif; ?>

        <?php require __DIR__ . '/' . $view . '.php'; ?>
      </div>
    </main>
  </div>
  <?php endif; ?>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="<?= BASE_URL ?>/assets/js/app.js"></script>
  <script src="<?= BASE_URL ?>/assets/js/transactions.js"></script>

</body>
</html>
