<?php
declare(strict_types=1);

function ensure_session(): void {
  if (session_status() === PHP_SESSION_NONE) session_start();
}

function is_logged_in(): bool {
  ensure_session();
  return !empty($_SESSION['user']);
}

function require_login(): void {
  if (!is_logged_in()) {
    header('Location: ' . BASE_URL . '/public/index.php?route=login');
    exit;
  }
}

function current_user(): array {
  ensure_session();
  return $_SESSION['user'] ?? [];
}

function flash_set(string $key, string $value): void {
  ensure_session();
  $_SESSION['flash'][$key] = $value;
}

function flash_get(string $key): ?string {
  ensure_session();
  if (!isset($_SESSION['flash'][$key])) return null;
  $v = $_SESSION['flash'][$key];
  unset($_SESSION['flash'][$key]);
  return $v;
}
