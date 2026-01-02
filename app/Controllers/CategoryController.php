<?php
declare(strict_types=1);

require_once __DIR__ . '/../Helpers/auth.php';

final class CategoryController {

  public static function index(PDO $pdo): array {

    /* =========================
       DELETE CATEGORY
    ========================== */
    if (isset($_GET['delete'])) {
      $id = (int)$_GET['delete'];

      // cek apakah kategori dipakai produk
      $cek = $pdo->prepare("
        SELECT 1 FROM products WHERE category_id = ? LIMIT 1
      ");
      $cek->execute([$id]);

      if ($cek->fetch()) {
        flash_set('error', 'Category cannot be deleted. Used by products.');
      } else {
        $pdo->prepare("DELETE FROM categories WHERE id = ?")->execute([$id]);
        flash_set('success', 'Category deleted.');
      }

      header('Location: ' . BASE_URL . '/public/index.php?route=categories');
      exit;
    }

    /* =========================
       UPDATE CATEGORY
    ========================== */
    if (isset($_POST['update'])) {
      $id   = (int)$_POST['id'];
      $name = trim($_POST['name'] ?? '');

      if ($id <= 0 || $name === '') {
        flash_set('error', 'Invalid category update.');
        header('Location: ' . BASE_URL . '/public/index.php?route=categories');
        exit;
      }

      $st = $pdo->prepare("UPDATE categories SET name=? WHERE id=?");
      $st->execute([$name, $id]);

      flash_set('success', 'Category updated.');
      header('Location: ' . BASE_URL . '/public/index.php?route=categories');
      exit;
    }

    /* =========================
       CREATE CATEGORY
    ========================== */
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $name = trim($_POST['name'] ?? '');

      if ($name === '') {
        flash_set('error', 'Category name is required.');
        header('Location: ' . BASE_URL . '/public/index.php?route=categories');
        exit;
      }

      $st = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
      $st->execute([$name]);

      flash_set('success', 'Category created.');
      header('Location: ' . BASE_URL . '/public/index.php?route=categories');
      exit;
    }

    /* =========================
       LOAD DATA
    ========================== */
    $categories = $pdo->query("
      SELECT id, name FROM categories ORDER BY id DESC
    ")->fetchAll();

    return [
      'view' => 'categories',
      'data' => compact('categories')
    ];
  }
}
