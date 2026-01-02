<?php
declare(strict_types=1);
/** @var array $stats */
/** @var array $recent */
?>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
  <div class="rounded-2xl border border-slate-800/60 bg-slate-900/40 p-5 shadow-soft">
    <div class="text-sm text-slate-400">Today Sales</div>
    <div class="text-2xl font-semibold mt-1">Rp <?= number_format((int)$stats['today_sales'], 0, ',', '.') ?></div>
  </div>

  <div class="rounded-2xl border border-slate-800/60 bg-slate-900/40 p-5 shadow-soft">
    <div class="text-sm text-slate-400">Products</div>
    <div class="text-2xl font-semibold mt-1"><?= (int)$stats['products'] ?></div>
  </div>

  <div class="rounded-2xl border border-slate-800/60 bg-slate-900/40 p-5 shadow-soft">
    <div class="text-sm text-slate-400">Low Stock (≤ <?= $lowStockLimit ?>)</div>
    <div class="text-2xl font-semibold mt-1 text-rose-400">
  <?= $stats['low_stock'] ?>
</div>


  </div>
</div>

<div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-4">
  <div class="lg:col-span-2 rounded-2xl border border-slate-800/60 bg-slate-900/40 p-5 shadow-soft">
    <div class="flex items-center justify-between mb-3">
      <div>
        <div class="text-sm text-slate-400">Sales</div>
        <div class="text-lg font-semibold">Last 7 days</div>
      </div>
      <select id="chartDays"
        class="rounded-xl bg-slate-950 border border-slate-800/70 px-3 py-2 text-sm">
        <option value="7" selected>7 days</option>
        <option value="14">14 days</option>
        <option value="30">30 days</option>
      </select>
    </div>

    <div class="h-64 rounded-xl bg-slate-950/60 border border-slate-800/60 grid place-items-center">
      <canvas id="salesChart" class="w-full h-full"></canvas>
    </div>
  </div>

  <div class="rounded-2xl border border-slate-800/60 bg-slate-900/40 p-5 shadow-soft">
    <div class="text-sm text-slate-400 mb-2">Recent Transactions</div>
    <div class="space-y-2">
      <?php foreach ($recent as $r): ?>
      <div class="flex items-center justify-between rounded-xl border border-slate-800/60 bg-slate-950/40 px-3 py-2">
        <div>
          <div class="text-sm font-medium"><?= htmlspecialchars($r['invoice_no']) ?></div>
          <div class="text-xs text-slate-400"><?= htmlspecialchars($r['sale_date']) ?></div>
        </div>
        <div class="text-sm font-semibold">Rp <?= number_format((int)$r['grand_total'],0,',','.') ?></div>
      </div>
      <?php endforeach; ?>
      <?php if (empty($recent)): ?>
        <div class="text-sm text-slate-400">No transactions yet.</div>
      <?php endif; ?>
    </div>
  </div>
  <div class="rounded-2xl border border-slate-800/60 bg-slate-900/40 p-5 shadow-soft mt-4">
  <div class="text-sm text-slate-400 mb-2">Low Stock Items</div>
  <?php if (empty($lowStockItems)): ?>
    <div class="text-slate-400 text-sm">All stocks are safe 👍</div>
  <?php else: ?>
    <div class="space-y-2">
      <?php foreach ($lowStockItems as $i): ?>
        <div class="flex justify-between text-sm">
          <span><?= htmlspecialchars($i['name']) ?></span>
          <span class="text-rose-400 font-semibold"><?= $i['stock'] ?></span>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
</div>
