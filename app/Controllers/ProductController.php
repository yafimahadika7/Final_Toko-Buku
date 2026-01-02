<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../Helpers/auth.php';

final class ProductController {

  public static function index(PDO $pdo): array {

    /* =========================
       DELETE PRODUCT
    ========================== */
    if (isset($_GET['delete'])) {
      $id = (int)$_GET['delete'];

      // Cek apakah produk sudah dipakai transaksi
      $cek = $pdo->prepare("
        SELECT 1 FROM sale_items WHERE product_id = ? LIMIT 1
      ");
      $cek->execute([$id]);

      if ($cek->fetch()) {
        flash_set('error', 'Product cannot be deleted. Already used in transactions.');
      } else {
        $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$id]);
        flash_set('success', 'Product deleted.');
      }

      header('Location: ' . BASE_URL . '/public/index.php?route=products');
      exit;
    }

    /* =========================
       UPDATE PRODUCT
    ========================== */
    if (isset($_POST['update'])) {
      $id         = (int)$_POST['id'];
      $sku        = trim($_POST['sku'] ?? '');
      $name       = trim($_POST['name'] ?? '');
      $categoryId = $_POST['category_id'] !== '' ? (int)$_POST['category_id'] : null;
      $price      = (int)($_POST['price'] ?? 0);

      if ($id <= 0 || $sku === '' || $name === '' || $price < 0) {
        flash_set('error', 'Invalid product update.');
        header('Location: ' . BASE_URL . '/public/index.php?route=products');
        exit;
      }

      $st = $pdo->prepare("
        UPDATE products
        SET sku = ?, name = ?, category_id = ?, sell_price = ?
        WHERE id = ?
      ");
      $st->execute([$sku, $name, $categoryId, $price, $id]);

      flash_set('success', 'Product updated.');
      header('Location: ' . BASE_URL . '/public/index.php?route=products');
      exit;
    }

    /* =========================
       CREATE PRODUCT
    ========================== */
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $sku        = trim($_POST['sku'] ?? '');
      $name       = trim($_POST['name'] ?? '');
      $categoryId = $_POST['category_id'] !== '' ? (int)$_POST['category_id'] : null;
      $price      = (int)($_POST['price'] ?? 0);
      $stock      = (int)($_POST['stock'] ?? 0);

      if ($sku === '' || $name === '' || $price < 0 || $stock < 0) {
        flash_set('error', 'Invalid product input.');
        header('Location: ' . BASE_URL . '/public/index.php?route=products');
        exit;
      }

      $pdo->beginTransaction();

      try {
        $st = $pdo->prepare("
          INSERT INTO products (sku, name, category_id, sell_price, stock)
          VALUES (?,?,?,?,?)
        ");
        $st->execute([$sku, $name, $categoryId, $price, $stock]);

        if ($stock > 0) {
          $pid = (int)$pdo->lastInsertId();
          $mv = $pdo->prepare("
            INSERT INTO stock_movements (product_id, type, qty, note)
            VALUES (?,?,?,?)
          ");
          $mv->execute([$pid, 'IN', $stock, 'Initial stock']);
        }

        $pdo->commit();
        flash_set('success', 'Product created.');

      } catch (Throwable $e) {
        $pdo->rollBack();
        flash_set('error', 'Failed to create product.');
      }

      header('Location: ' . BASE_URL . '/public/index.php?route=products');
      exit;
    }

    /* =========================
       LOAD DATA
    ========================== */
    $categories = $pdo->query("
      SELECT id, name FROM categories ORDER BY name ASC
    ")->fetchAll();

    $products = $pdo->query("
      SELECT p.*, c.name AS category_name
      FROM products p
      LEFT JOIN categories c ON c.id = p.category_id
      ORDER BY p.id DESC
    ")->fetchAll();

    return [
      'view' => 'products',
      'data' => compact('products', 'categories')
    ];
  }
}
