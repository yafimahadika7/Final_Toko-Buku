<?php
declare(strict_types=1);

function render(string $view, array $data = []): void {
  extract($data, EXTR_SKIP);
  require __DIR__ . '/../Views/layout.php';
}
