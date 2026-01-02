<?php
/** @var string $title */
/** @var string $subtitle */
/** @var string $body */
/** @var string|null $actions */
?>
<div class="rounded-2xl border border-slate-800/60 bg-slate-900/40 p-5 shadow-soft">
  <div class="flex items-center justify-between mb-4">
    <div>
      <div class="text-sm text-slate-400"><?= htmlspecialchars($subtitle) ?></div>
      <div class="text-lg font-semibold"><?= htmlspecialchars($title) ?></div>
    </div>
    <?= $actions ?? '' ?>
  </div>
  <?= $body ?>
</div>
