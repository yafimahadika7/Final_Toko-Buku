<?php
declare(strict_types=1);
/** @var array $products */

$title = 'Add Stock';
$subtitle = 'Kasir & Admin';
ob_start();
?>

<!-- ================= ADD STOCK FORM ================= -->
<form method="post" action="<?= BASE_URL ?>/public/index.php?route=stock_in"
      class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-6">

  <input type="hidden" name="add_stock" value="1">

  <select name="product_id" required
    class="rounded-xl bg-slate-950 border border-slate-800/70 px-4 py-3 text-sm">
    <option value="">Select product</option>
    <?php foreach ($products as $p): ?>
      <option value="<?= (int)$p['id'] ?>">
        <?= htmlspecialchars($p['name']) ?> (Stock: <?= (int)$p['stock'] ?>)
      </option>
    <?php endforeach; ?>
  </select>

  <input type="number" name="qty" min="1" required placeholder="Qty"
    class="rounded-xl bg-slate-950 border border-slate-800/70 px-4 py-3 text-sm">

  <button
    class="rounded-xl bg-white text-slate-900 px-5 py-3 text-sm font-semibold hover:bg-slate-100">
    Add
  </button>
</form>

<!-- ================= PRODUCT STOCK TABLE ================= -->
<div class="overflow-x-auto">
<table class="min-w-full text-sm">
  <thead class="text-slate-400">
    <tr>
      <th class="text-left py-2">SKU</th>
      <th class="text-left py-2">Name</th>
      <th class="text-right py-2">Stock</th>
      <th class="text-right py-2">Action</th>
    </tr>
  </thead>

  <tbody class="divide-y divide-slate-800/60">
    <?php foreach ($products as $p): ?>
      <tr class="hover:bg-slate-950/40">
        <td class="py-3"><?= htmlspecialchars($p['sku']) ?></td>
        <td class="py-3 font-medium"><?= htmlspecialchars($p['name']) ?></td>
        <td class="py-3 text-right"><?= (int)$p['stock'] ?></td>
        <td class="py-3 text-right space-x-2">

          <!-- EDIT STOCK -->
          <button
            onclick="openEditStock(<?= (int)$p['id'] ?>, <?= (int)$p['stock'] ?>)"
            class="px-3 py-1 rounded-lg bg-slate-700 hover:bg-slate-600 text-xs">
            Edit
          </button>

          <!-- DELETE STOCK (SET STOCK = 0) -->
          <a
            href="<?= BASE_URL ?>/public/index.php?route=stock_in&reset=<?= (int)$p['id'] ?>"
            onclick="return confirm('Reset stock produk ini ke 0?')"
            class="px-3 py-1 rounded-lg bg-red-600/80 hover:bg-red-600 text-xs">
            Delete
          </a>

        </td>
      </tr>
    <?php endforeach; ?>

    <?php if (empty($products)): ?>
      <tr>
        <td colspan="4" class="py-6 text-center text-slate-400">
          No products
        </td>
      </tr>
    <?php endif; ?>
  </tbody>
</table>
</div>

<!-- ================= EDIT MODAL ================= -->
<div id="editStockModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50">
  <div class="bg-slate-900 rounded-xl p-6 w-full max-w-sm">
    <h3 class="text-lg font-semibold mb-4">Edit Stock</h3>

    <form method="post" action="<?= BASE_URL ?>/public/index.php?route=stock_in" class="space-y-3">
      <input type="hidden" name="update_direct" value="1">
      <input type="hidden" name="product_id" id="edit_product_id">

      <input type="number" name="stock" id="edit_stock_value" min="0" required
        class="w-full rounded-xl bg-slate-950 border border-slate-800 px-4 py-2 text-sm">

      <div class="flex justify-end gap-2 pt-2">
        <button type="button" onclick="closeEditStock()"
          class="px-4 py-2 rounded-lg bg-slate-700">
          Cancel
        </button>
        <button
          class="px-4 py-2 rounded-lg bg-white text-slate-900 font-semibold">
          Update
        </button>
      </div>
    </form>
  </div>
</div>

<script>
function openEditStock(id, stock) {
  document.getElementById('edit_product_id').value = id;
  document.getElementById('edit_stock_value').value = stock;
  document.getElementById('editStockModal').classList.remove('hidden');
  document.getElementById('editStockModal').classList.add('flex');
}
function closeEditStock() {
  document.getElementById('editStockModal').classList.add('hidden');
  document.getElementById('editStockModal').classList.remove('flex');
}
</script>

<?php
$body = ob_get_clean();
$actions = null;
require __DIR__ . '/_card.php';
