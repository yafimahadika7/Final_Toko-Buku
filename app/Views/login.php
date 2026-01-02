<?php
declare(strict_types=1);
require_once __DIR__ . '/../../config/app.php';
?>
<div class="min-h-screen flex items-center justify-center px-4">
  <div class="w-full max-w-md">
    <div class="rounded-2xl border border-slate-800/60 bg-slate-900/40 shadow-soft p-6">
      <div class="flex items-center gap-3 mb-6">
        <div class="h-10 w-10 rounded-xl bg-slate-800 grid place-items-center">🔐</div>
        <div>
          <div class="text-lg font-semibold">Sign in</div>
          <div class="text-sm text-slate-400">Use your account to continue</div>
        </div>
      </div>

      <form method="post" action="<?= BASE_URL ?>/public/index.php?route=login" class="space-y-4">
        <div>
          <label class="text-sm text-slate-300">Username</label>
          <input name="username" required
            class="mt-2 w-full rounded-xl bg-slate-950 border border-slate-800/70 px-4 py-3 outline-none focus:ring-2 focus:ring-slate-500"
            placeholder="admin" />
        </div>
        <div>
          <label class="text-sm text-slate-300">Password</label>
          <input type="password" name="password" required
            class="mt-2 w-full rounded-xl bg-slate-950 border border-slate-800/70 px-4 py-3 outline-none focus:ring-2 focus:ring-slate-500"
            placeholder="••••••••" />
        </div>

        <button class="w-full rounded-xl bg-white text-slate-900 font-semibold py-3 hover:bg-slate-100 transition">
          Sign in
        </button>

        <div class="text-xs text-slate-400">
          Demo: admin/admin123 • kasir/kasir123 • owner/owner123
        </div>
      </form>
    </div>
  </div>
</div>
