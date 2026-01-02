<?php
declare(strict_types=1);
/** @var array $products */
/** @var array $categories */
$title = 'Products';
$subtitle = 'Manage catalog';
ob_start();
?>

<!-- ADD PRODUCT FORM (TIDAK DIUBAH) -->
<form method="post" action="<?= BASE_URL ?>/public/index.php?route=products"
      class="grid grid-cols-1 md:grid-cols-5 gap-3 mb-4">

  <input name="sku" required placeholder="SKU"
    class="rounded-xl bg-slate-950 border border-slate-800/70 px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-slate-500">

  <input name="name" required placeholder="Product name"
    class="rounded-xl bg-slate-950 border border-slate-800/70 px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-slate-500 md:col-span-2">

  <select name="category_id"
    class="rounded-xl bg-slate-950 border border-slate-800/70 px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-slate-500">
    <option value="">Category (optional)</option>
    <?php foreach ($categories as $c): ?>
      <option value="<?= (int)$c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
    <?php endforeach; ?>
  </select>

  <input type="number" name="price" required min="0" placeholder="Price"
    class="rounded-xl bg-slate-950 border border-slate-800/70 px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-slate-500">

  <div class="md:col-span-5 flex gap-3">
    <input type="number" name="stock" required min="0" placeholder="Initial stock"
      class="flex-1 rounded-xl bg-slate-950 border border-slate-800/70 px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-slate-500">
    <button class="rounded-xl bg-white text-slate-900 px-5 py-3 text-sm font-semibold hover:bg-slate-100">
      Add
    </button>
  </div>
</form>

<!-- PRODUCTS TABLE -->
<div class="overflow-x-auto">
<table class="min-w-full text-sm">
  <thead class="text-slate-400">
    <tr>
      <th class="text-left py-2">SKU</th>
      <th class="text-left py-2">Name</th>
      <th class="text-left py-2">Category</th>
      <th class="text-right py-2">Price</th>
      <th class="text-right py-2">Stock</th>
      <th class="text-right py-2">Action</th>
    </tr>
  </thead>

  <tbody class="divide-y divide-slate-800/60">
    <?php foreach ($products as $p): ?>
      <tr class="hover:bg-slate-950/40">
        <td class="py-3"><?= htmlspecialchars($p['sku']) ?></td>
        <td class="py-3 font-medium"><?= htmlspecialchars($p['name']) ?></td>
        <td class="py-3 text-slate-300"><?= htmlspecialchars($p['category_name'] ?? '-') ?></td>
        <td class="py-3 text-right">
          Rp <?= number_format((int)$p['sell_price'], 0, ',', '.') ?>
        </td>
        <td class="py-3 text-right"><?= (int)$p['stock'] ?></td>

        <!-- ACTION -->
        <td class="py-3 text-right space-x-2">
          <button
            onclick="openEdit(
              <?= (int)$p['id'] ?>,
              '<?= htmlspecialchars($p['sku'], ENT_QUOTES) ?>',
              '<?= htmlspecialchars($p['name'], ENT_QUOTES) ?>',
              <?= (int)$p['sell_price'] ?>,
              <?= (int)($p['category_id'] ?? 0) ?>
            )"
            class="px-3 py-1 rounded-lg bg-slate-700 hover:bg-slate-600 text-xs">
            Edit
          </button>

          <a
            href="<?= BASE_URL ?>/public/index.php?route=products&delete=<?= (int)$p['id'] ?>"
            onclick="return confirm('Yakin hapus produk ini?')"
            class="px-3 py-1 rounded-lg bg-red-600/80 hover:bg-red-600 text-xs">
            Delete
          </a>
        </td>
      </tr>
    <?php endforeach; ?>

    <?php if (empty($products)): ?>
      <tr>
        <td colspan="6" class="py-6 text-center text-slate-400">
          No products
        </td>
      </tr>
    <?php endif; ?>
  </tbody>
</table>
</div>

<!-- EDIT MODAL -->
<div id="editModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50">
  <div class="bg-slate-900 rounded-xl p-6 w-full max-w-md">
    <h3 class="text-lg font-semibold mb-4">Edit Product</h3>

    <form method="post" action="<?= BASE_URL ?>/public/index.php?route=products" class="space-y-3">
      <input type="hidden" name="id" id="edit_id">

      <input name="sku" id="edit_sku" required
        class="w-full rounded-xl bg-slate-950 border border-slate-800 px-4 py-2 text-sm">

      <input name="name" id="edit_name" required
        class="w-full rounded-xl bg-slate-950 border border-slate-800 px-4 py-2 text-sm">

      <select name="category_id" id="edit_category"
        class="w-full rounded-xl bg-slate-950 border border-slate-800 px-4 py-2 text-sm">
        <option value="">Category</option>
        <?php foreach ($categories as $c): ?>
          <option value="<?= (int)$c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
        <?php endforeach; ?>
      </select>

      <input type="number" name="price" id="edit_price" min="0" required
        class="w-full rounded-xl bg-slate-950 border border-slate-800 px-4 py-2 text-sm">

      <div class="flex justify-end gap-2 pt-2">
        <button type="button" onclick="closeEdit()"
          class="px-4 py-2 rounded-lg bg-slate-700">
          Cancel
        </button>
        <button name="update"
          class="px-4 py-2 rounded-lg bg-white text-slate-900 font-semibold">
          Update
        </button>
      </div>
    </form>
  </div>
</div>

<!-- JAVASCRIPT -->
<script>
function openEdit(id, sku, name, price, categoryId) {
  document.getElementById('edit_id').value = id;
  document.getElementById('edit_sku').value = sku;
  document.getElementById('edit_name').value = name;
  document.getElementById('edit_price').value = price;
  document.getElementById('edit_category').value = categoryId || '';
  document.getElementById('editModal').classList.remove('hidden');
  document.getElementById('editModal').classList.add('flex');
}

function closeEdit() {
  document.getElementById('editModal').classList.add('hidden');
  document.getElementById('editModal').classList.remove('flex');
}
</script>

<?php
$body = ob_get_clean();
$actions = null;
require __DIR__ . '/_card.php';
