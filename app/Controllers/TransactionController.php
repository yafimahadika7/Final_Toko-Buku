<?php
declare(strict_types=1);
require_once __DIR__ . '/../Helpers/auth.php';

final class TransactionController
{
  private static function generateInvoice(PDO $pdo): string {
    // INV-YYYYMMDD-XXXX
    $prefix = 'INV-' . date('Ymd') . '-';
    $st = $pdo->prepare("SELECT COUNT(*) FROM sales WHERE invoice_no LIKE ?");
    $st->execute([$prefix . '%']);
    $n = (int)$st->fetchColumn() + 1;
    return $prefix . str_pad((string)$n, 4, '0', STR_PAD_LEFT);
  }

  public static function index(PDO $pdo): array
  {
    // CREATE TRANSACTION
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      ensure_session();

      $customerId = ($_POST['customer_id'] ?? '') !== '' ? (int)$_POST['customer_id'] : null;
      $qtyMap = $_POST['qty'] ?? [];
      $userId = (int)($_SESSION['user']['id'] ?? 0);

      // collect items qty > 0
      $items = [];
      foreach ($qtyMap as $pid => $qty) {
        $q = (int)$qty;
        $pid = (int)$pid;
        if ($q > 0) $items[] = ['product_id' => $pid, 'qty' => $q];
      }

      if (empty($items)) {
        flash_set('error', 'Add at least one item.');
        header('Location: ' . BASE_URL . '/public/index.php?route=transactions'); exit;
      }

      $pdo->beginTransaction();
      try {
        // invoice auto, collision-safe
        $invoice = self::generateInvoice($pdo);

        $stSale = $pdo->prepare("INSERT INTO sales (invoice_no, sale_date, user_id, customer_id, grand_total, status)
                                 VALUES (?, NOW(), ?, ?, 0, 'PAID')");
        $stSale->execute([$invoice, $userId, $customerId]);
        $saleId = (int)$pdo->lastInsertId();

        $total = 0;

        $getP = $pdo->prepare("SELECT id, name, sell_price, stock FROM products WHERE id=? FOR UPDATE");
        $insD = $pdo->prepare("INSERT INTO sales_detail (sale_id, product_id, price, qty, subtotal) VALUES (?,?,?,?,?)");
        $updS = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id=?");
        $mov  = $pdo->prepare("INSERT INTO stock_movements (product_id, type, qty, note) VALUES (?,?,?,?)");

        foreach ($items as $it) {
          $getP->execute([$it['product_id']]);
          $p = $getP->fetch();
          if (!$p) throw new Exception('Product not found.');
          if ((int)$p['stock'] < (int)$it['qty']) throw new Exception('Insufficient stock: ' . $p['name']);

          $price = (int)$p['sell_price'];
          $sub = $price * (int)$it['qty'];
          $total += $sub;

          $insD->execute([$saleId, $it['product_id'], $price, $it['qty'], $sub]);
          $updS->execute([$it['qty'], $it['product_id']]);
          $mov->execute([$it['product_id'], 'OUT', $it['qty'], 'Sale ' . $invoice]);
        }

        $pdo->prepare("UPDATE sales SET grand_total=? WHERE id=?")->execute([$total, $saleId]);
        $pdo->commit();

        flash_set('success', "Transaction created: {$invoice}");
        header('Location: ' . BASE_URL . '/public/index.php?route=transactions'); exit;
      } catch (Throwable $e) {
        $pdo->rollBack();
        flash_set('error', 'Failed: ' . htmlspecialchars($e->getMessage()));
        header('Location: ' . BASE_URL . '/public/index.php?route=transactions'); exit;
      }
    }

    // VIEW DATA
    $invoicePreview = self::generateInvoice($pdo);

    $categories = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC")->fetchAll();

    $cat = ($_GET['cat'] ?? '') !== '' ? (int)$_GET['cat'] : null;

    if ($cat) {
      $st = $pdo->prepare("
        SELECT p.id, p.name, p.sell_price, p.stock, c.name AS category_name, c.id AS category_id
        FROM products p
        LEFT JOIN categories c ON c.id = p.category_id
        WHERE p.stock > 0 AND p.category_id = ?
        ORDER BY c.name ASC, p.name ASC
      ");
      $st->execute([$cat]);
      $products = $st->fetchAll();
    } else {
      $products = $pdo->query("
        SELECT p.id, p.name, p.sell_price, p.stock, c.name AS category_name, c.id AS category_id
        FROM products p
        LEFT JOIN categories c ON c.id = p.category_id
        WHERE p.stock > 0
        ORDER BY c.name ASC, p.name ASC
      ")->fetchAll();
    }

    $customers = $pdo->query("SELECT id, name FROM customers ORDER BY name ASC")->fetchAll();

    $transactions = $pdo->query("SELECT invoice_no, sale_date, grand_total FROM sales ORDER BY id DESC LIMIT 50")->fetchAll();

    return [
      'view' => 'transactions',
      'data' => compact('products','customers','transactions','categories','invoicePreview','cat')
    ];
  }
}
