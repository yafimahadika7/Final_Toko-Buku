<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';

function require_role(array $allowed): void {
  $u = current_user();
  $role = $u['role'] ?? '';
  if (!in_array($role, $allowed, true)) {
    flash_set('error', 'Access denied.');
    header('Location: ' . BASE_URL . '/public/index.php?route=dashboard');
    exit;
  }
}
