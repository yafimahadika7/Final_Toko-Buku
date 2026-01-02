<?php
declare(strict_types=1);
/** @var array $rows */
$title='Sales Report'; $subtitle='Owner only';
ob_start();
?>
<div class="overflow-x-auto">
<table class="min-w-full text-sm">
  <thead class="text-slate-400"><tr><th class="text-left py-2">Date</th><th class="text-right py-2">Total</th></tr></thead>
  <tbody class="divide-y divide-slate-800/60">
    <?php foreach($rows as $r): ?>
      <tr class="hover:bg-slate-950/40">
        <td class="py-3 font-medium"><?= htmlspecialchars($r['d']) ?></td>
        <td class="py-3 text-right font-semibold">Rp <?= number_format((int)$r['total'],0,',','.') ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>
<?php
$body=ob_get_clean(); $actions=null; require __DIR__.'/_card.php';
