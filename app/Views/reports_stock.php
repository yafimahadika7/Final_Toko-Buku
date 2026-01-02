<?php
declare(strict_types=1);
/** @var array $rows */
$title='Stock Report'; $subtitle='Owner only';
ob_start();
?>
<div class="overflow-x-auto">
<table class="min-w-full text-sm">
  <thead class="text-slate-400"><tr><th class="text-left py-2">SKU</th><th class="text-left py-2">Product</th><th class="text-right py-2">Stock</th></tr></thead>
  <tbody class="divide-y divide-slate-800/60">
    <?php foreach($rows as $r): ?>
      <tr class="hover:bg-slate-950/40">
        <td class="py-3"><?= htmlspecialchars($r['sku']) ?></td>
        <td class="py-3 font-medium"><?= htmlspecialchars($r['name']) ?></td>
        <td class="py-3 text-right"><?= (int)$r['stock'] ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>
<?php
$body=ob_get_clean(); $actions=null; require __DIR__.'/_card.php';
