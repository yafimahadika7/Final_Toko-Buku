<?php
declare(strict_types=1);
/** @var array $users */

$title = 'Add User';
$subtitle = 'Admin only';
ob_start();
?>

<!-- ================= ADD USER FORM ================= -->
<form method="post" action="<?= BASE_URL ?>/public/index.php?route=users_add"
      class="grid grid-cols-1 md:grid-cols-5 gap-3 mb-6">

  <input type="hidden" name="create_user" value="1">

  <input name="name" required placeholder="Name"
    class="rounded-xl bg-slate-950 border border-slate-800/70 px-4 py-3 text-sm">

  <input name="username" required placeholder="Username"
    class="rounded-xl bg-slate-950 border border-slate-800/70 px-4 py-3 text-sm">

  <input name="password" required placeholder="Password"
    class="rounded-xl bg-slate-950 border border-slate-800/70 px-4 py-3 text-sm">

  <select name="role"
    class="rounded-xl bg-slate-950 border border-slate-800/70 px-4 py-3 text-sm">
    <option value="admin">admin</option>
    <option value="kasir">kasir</option>
    <option value="owner">owner</option>
  </select>

  <button class="rounded-xl bg-white text-slate-900 px-5 py-3 text-sm font-semibold">
    Create
  </button>
</form>

<!-- ================= USER TABLE ================= -->
<div class="overflow-x-auto">
<table class="min-w-full text-sm">
  <thead class="text-slate-400">
    <tr>
      <th class="text-left py-2">Name</th>
      <th class="text-left py-2">Username</th>
      <th class="text-left py-2">Role</th>
      <th class="text-center py-2">Active</th>
      <th class="text-right py-2">Action</th>
    </tr>
  </thead>

  <tbody class="divide-y divide-slate-800/60">
    <?php foreach ($users as $u): ?>
      <tr class="hover:bg-slate-950/40">
        <td class="py-3"><?= htmlspecialchars($u['name']) ?></td>
        <td class="py-3"><?= htmlspecialchars($u['username']) ?></td>
        <td class="py-3"><?= htmlspecialchars($u['role']) ?></td>
        <td class="py-3 text-center"><?= (int)$u['is_active'] ?></td>
        <td class="py-3 text-right space-x-2">

          <!-- EDIT -->
          <button
            onclick="openEditUser(
              <?= (int)$u['id'] ?>,
              '<?= htmlspecialchars($u['name'], ENT_QUOTES) ?>',
              '<?= htmlspecialchars($u['role'], ENT_QUOTES) ?>',
              <?= (int)$u['is_active'] ?>
            )"
            class="px-3 py-1 rounded-lg bg-slate-700 hover:bg-slate-600 text-xs">
            Edit
          </button>

          <!-- DELETE -->
          <?php if ($u['id'] !== $_SESSION['user']['id']): ?>
            <a
              href="<?= BASE_URL ?>/public/index.php?route=users_add&delete=<?= (int)$u['id'] ?>"
              onclick="return confirm('Delete this user?')"
              class="px-3 py-1 rounded-lg bg-red-600/80 hover:bg-red-600 text-xs">
              Delete
            </a>
          <?php else: ?>
            <span class="text-slate-500 text-xs">—</span>
          <?php endif; ?>

        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>

<!-- ================= EDIT MODAL ================= -->
<div id="editUserModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50">
  <div class="bg-slate-900 rounded-xl p-6 w-full max-w-sm">
    <h3 class="text-lg font-semibold mb-4">Edit User</h3>

    <form method="post" action="<?= BASE_URL ?>/public/index.php?route=users_add" class="space-y-3">
      <input type="hidden" name="update_user" value="1">
      <input type="hidden" name="id" id="edit_user_id">

      <input name="name" id="edit_user_name" required
        class="w-full rounded-xl bg-slate-950 border border-slate-800 px-4 py-2 text-sm">

      <select name="role" id="edit_user_role"
        class="w-full rounded-xl bg-slate-950 border border-slate-800 px-4 py-2 text-sm">
        <option value="admin">admin</option>
        <option value="kasir">kasir</option>
        <option value="owner">owner</option>
      </select>

      <select name="is_active" id="edit_user_active"
        class="w-full rounded-xl bg-slate-950 border border-slate-800 px-4 py-2 text-sm">
        <option value="1">Active</option>
        <option value="0">Inactive</option>
      </select>

      <div class="flex justify-end gap-2 pt-2">
        <button type="button" onclick="closeEditUser()"
          class="px-4 py-2 rounded-lg bg-slate-700">
          Cancel
        </button>
        <button class="px-4 py-2 rounded-lg bg-white text-slate-900 font-semibold">
          Update
        </button>
      </div>
    </form>
  </div>
</div>

<script>
function openEditUser(id, name, role, active) {
  document.getElementById('edit_user_id').value = id;
  document.getElementById('edit_user_name').value = name;
  document.getElementById('edit_user_role').value = role;
  document.getElementById('edit_user_active').value = active;
  document.getElementById('editUserModal').classList.remove('hidden');
  document.getElementById('editUserModal').classList.add('flex');
}
function closeEditUser() {
  document.getElementById('editUserModal').classList.add('hidden');
  document.getElementById('editUserModal').classList.remove('flex');
}
</script>

<?php
$body = ob_get_clean();
$actions = null;
require __DIR__ . '/_card.php';
