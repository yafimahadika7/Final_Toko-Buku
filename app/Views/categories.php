<?php
declare(strict_types=1);
/** @var array $categories */
$title = 'Categories';
$subtitle = 'Manage product categories';
ob_start();
?>

<!-- ADD CATEGORY (TIDAK DIUBAH) -->
<form method="post" action="<?= BASE_URL ?>/public/index.php?route=categories"
      class="flex flex-col md:flex-row gap-3 mb-4">
  <input name="name" required placeholder="Category name"
    class="flex-1 rounded-xl bg-slate-950 border border-slate-800/70 px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-slate-500">
  <button class="rounded-xl bg-white text-slate-900 px-5 py-3 text-sm font-semibold hover:bg-slate-100">
    Add
  </button>
</form>

<!-- TABLE -->
<div class="overflow-x-auto">
<table class="min-w-full text-sm">
  <thead class="text-slate-400">
    <tr>
      <th class="text-left py-2">Name</th>
      <th class="text-right py-2">Action</th>
    </tr>
  </thead>

  <tbody class="divide-y divide-slate-800/60">
    <?php foreach ($categories as $c): ?>
      <tr class="hover:bg-slate-950/40">
        <td class="py-3 font-medium"><?= htmlspecialchars($c['name']) ?></td>

        <td class="py-3 text-right space-x-2">
          <!-- EDIT -->
          <button
            onclick="openEditCategory(
              <?= (int)$c['id'] ?>,
              '<?= htmlspecialchars($c['name'], ENT_QUOTES) ?>'
            )"
            class="px-3 py-1 rounded-lg bg-slate-700 hover:bg-slate-600 text-xs">
            Edit
          </button>

          <!-- DELETE -->
          <a
            href="<?= BASE_URL ?>/public/index.php?route=categories&delete=<?= (int)$c['id'] ?>"
            onclick="return confirm('Yakin hapus kategori ini?')"
            class="px-3 py-1 rounded-lg bg-red-600/80 hover:bg-red-600 text-xs">
            Delete
          </a>
        </td>
      </tr>
    <?php endforeach; ?>

    <?php if (empty($categories)): ?>
      <tr>
        <td colspan="2" class="py-6 text-center text-slate-400">
          No categories
        </td>
      </tr>
    <?php endif; ?>
  </tbody>
</table>
</div>

<!-- EDIT MODAL -->
<div id="editCategoryModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50">
  <div class="bg-slate-900 rounded-xl p-6 w-full max-w-md">
    <h3 class="text-lg font-semibold mb-4">Edit Category</h3>

    <form method="post" action="<?= BASE_URL ?>/public/index.php?route=categories" class="space-y-3">
      <input type="hidden" name="id" id="edit_cat_id">

      <input name="name" id="edit_cat_name" required
        class="w-full rounded-xl bg-slate-950 border border-slate-800 px-4 py-2 text-sm">

      <div class="flex justify-end gap-2 pt-2">
        <button type="button" onclick="closeEditCategory()"
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
function openEditCategory(id, name) {
  document.getElementById('edit_cat_id').value = id;
  document.getElementById('edit_cat_name').value = name;
  document.getElementById('editCategoryModal').classList.remove('hidden');
  document.getElementById('editCategoryModal').classList.add('flex');
}

function closeEditCategory() {
  document.getElementById('editCategoryModal').classList.add('hidden');
  document.getElementById('editCategoryModal').classList.remove('flex');
}
</script>

<?php
$body = ob_get_clean();
$actions = null;
require __DIR__ . '/_card.php';
