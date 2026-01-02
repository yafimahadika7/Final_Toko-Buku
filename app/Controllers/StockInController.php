<?php
declare(strict_types=1);

require_once __DIR__ . '/../Helpers/auth.php';

final class StockInController
{
  public static function index(PDO $pdo): array
  {
    /* =====================================================
       RESET STOCK (DELETE BUTTON)
       ===================================================== */
    if (isset($_GET['reset'])) {
      $productId = (int)$_GET['reset'];

      if ($productId > 0) {
        $pdo->prepare("
          UPDATE products 
          SET stock = 0 
          WHERE id = ?
        ")->execute([$productId]);

        flash_set('success', 'Stock berhasil di-reset.');
      }

      header('Location: ' . BASE_URL . '/public/index.php?route=stock_in');
      exit;
    }

    /* =====================================================
       UPDATE STOCK LANGSUNG (EDIT MODAL)
       ===================================================== */
    if (isset($_POST['update_direct'])) {
      $productId = (int)($_POST['product_id'] ?? 0);
      $stock     = (int)($_POST['stock'] ?? 0);

      if ($productId <= 0 || $stock < 0) {
        flash_set('error', 'Input stok tidak valid.');
        header('Location: ' . BASE_URL . '/public/index.php?route=stock_in');
        exit;
      }

      $pdo->prepare("
        UPDATE products 
        SET stock = ? 
        WHERE id = ?
      ")->execute([$stock, $productId]);

      flash_set('success', 'Stock berhasil diperbarui.');
      header('Location: ' . BASE_URL . '/public/index.php?route=stock_in');
      exit;
    }

    /* =====================================================
       ADD STOCK (FORM ATAS)
       ===================================================== */
    if (isset($_POST['add_stock'])) {
      $productId = (int)($_POST['product_id'] ?? 0);
      $qty       = (int)($_POST['qty'] ?? 0);

      if ($productId <= 0 || $qty <= 0) {
        flash_set('error', 'Pilih produk dan qty harus lebih dari 0.');
        header('Location: ' . BASE_URL . '/public/index.php?route=stock_in');
        exit;
      }

      $pdo->prepare("
        UPDATE products 
        SET stock = stock + ?
        WHERE id = ?
      ")->execute([$qty, $productId]);

      flash_set('success', 'Stock berhasil ditambahkan.');
      header('Location: ' . BASE_URL . '/public/index.php?route=stock_in');
      exit;
    }

    /* =====================================================
       LOAD DATA PRODUK
       ===================================================== */
    $products = $pdo->query("
      SELECT id, sku, name, stock
      FROM products
      ORDER BY name ASC
    ")->fetchAll();

    return [
      'view' => 'stock_in',
      'data' => compact('products')
    ];
  }
}
