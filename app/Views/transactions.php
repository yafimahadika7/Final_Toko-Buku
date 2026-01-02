<?php
declare(strict_types=1);
/** @var array $products */
/** @var array $customers */
/** @var array $transactions */
/** @var array $categories */
/** @var string $invoicePreview */
/** @var int|null $cat */

$title = 'Transactions';
$subtitle = 'Create and view sales';
ob_start();
?>

<form method="post" action="<?= BASE_URL ?>/public/index.php?route=transactions" class="rounded-2xl border border-slate-800/60 bg-slate-950/40 p-4 mb-4">
  <div class="grid grid-cols-1 md:grid-cols-4 gap-3">

    <input value="<?= htmlspecialchars($invoicePreview) ?>" readonly
      class="rounded-xl bg-slate-950 border border-slate-800/70 px-4 py-3 text-sm text-slate-300"
      title="Invoice is auto-generated on submit" />

    <button class="rounded-xl bg-white text-slate-900 px-5 py-3 text-sm font-semibold hover:bg-slate-100">
      Submit Transaction
    </button>

    <a href="<?= BASE_URL ?>/public/index.php?route=transactions"
       class="rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-800/60 px-5 py-3 text-sm text-center">
      Reset
    </a>
  </div>

  <!-- Category Filter (sort/filter by category) -->
  <div class="mt-4 flex flex-col md:flex-row md:items-center gap-3">
    <div class="text-sm text-slate-300">Filter by Category:</div>
    <select id="categoryFilter" class="w-full md:w-72 rounded-xl bg-slate-950 border border-slate-800/70 px-4 py-2 text-sm">
      <option value="">All categories</option>
      <?php foreach ($categories as $c): ?>
        <option value="<?= (int)$c['id'] ?>" <?= ($cat === (int)$c['id']) ? 'selected' : '' ?>>
          <?= htmlspecialchars($c['name']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="mt-4 grid grid-cols-1 lg:grid-cols-2 gap-4">
    <!-- Product picker -->
    <div class="rounded-2xl border border-slate-800/60 bg-slate-900/40 p-4">
      <div class="text-sm text-slate-400 mb-2">Products (click +)</div>

      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="text-slate-400">
            <tr>
              <th class="text-left py-2">Product</th>
              <th class="text-left py-2">Category</th>
              <th class="text-right py-2">Price</th>
              <th class="text-right py-2">Stock</th>
              <th class="text-right py-2"></th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-800/60">
            <?php foreach ($products as $p): ?>
              <tr class="hover:bg-slate-950/40">
                <td class="py-3 font-medium"><?= htmlspecialchars($p['name']) ?></td>
                <td class="py-3 text-slate-300"><?= htmlspecialchars($p['category_name'] ?? '-') ?></td>
                <td class="py-3 text-right">Rp <?= number_format((int)$p['sell_price'],0,',','.') ?></td>
                <td class="py-3 text-right text-slate-300"><?= (int)$p['stock'] ?></td>
                <td class="py-3 text-right">
                  <button type="button"
                    class="add-btn inline-flex items-center justify-center h-9 w-9 rounded-xl bg-slate-950 border border-slate-800/70 hover:bg-slate-800"
                    data-id="<?= (int)$p['id'] ?>"
                    data-name="<?= htmlspecialchars($p['name'], ENT_QUOTES) ?>"
                    data-price="<?= (int)$p['sell_price'] ?>"
                    data-stock="<?= (int)$p['stock'] ?>">
                    +
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($products)): ?>
              <tr><td colspan="5" class="py-6 text-center text-slate-400">No products</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Cart -->
    <div class="rounded-2xl border border-slate-800/60 bg-slate-900/40 p-4">
      <div class="flex items-center justify-between mb-2">
        <div class="text-sm text-slate-400">Cart</div>
        <div class="text-sm font-semibold">Total: <span id="cartTotal">Rp 0</span></div>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full text-sm" id="cartTable">
          <thead class="text-slate-400">
            <tr>
              <th class="text-left py-2">Item</th>
              <th class="text-right py-2">Qty</th>
              <th class="text-right py-2">Subtotal</th>
              <th class="text-right py-2"></th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-800/60">
            <tr id="cartEmptyRow">
              <td colspan="4" class="py-6 text-center text-slate-400">Cart is empty</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Hidden inputs qty[product_id] will be injected by JS -->
      <div id="hiddenInputs"></div>
    </div>
  </div>
</form>

<!-- Transaction history -->
<div class="overflow-x-auto mt-6">
  <table class="min-w-full text-sm">
    <thead class="text-slate-400">
      <tr>
        <th class="text-left py-2">Invoice</th>
        <th class="text-left py-2">Date</th>
        <th class="text-right py-2">Total</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-slate-800/60">
      <?php foreach ($transactions as $t): ?>
        <tr class="hover:bg-slate-950/40">
          <td class="py-3 font-medium"><?= htmlspecialchars($t['invoice_no']) ?></td>
          <td class="py-3 text-slate-300"><?= htmlspecialchars($t['sale_date']) ?></td>
          <td class="py-3 text-right font-semibold">Rp <?= number_format((int)$t['grand_total'],0,',','.') ?></td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($transactions)): ?>
        <tr><td colspan="3" class="py-6 text-center text-slate-400">No transactions</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php
$body = ob_get_clean();
$actions = null;
require __DIR__ . '/_card.php';
