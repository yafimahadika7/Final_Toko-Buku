<?php
declare(strict_types=1);
require_once __DIR__ . '/../../config/db.php';

final class DashboardController {
  public static function index(PDO $pdo): array {
    $todaySales = (int)$pdo->query("SELECT COALESCE(SUM(grand_total),0) FROM sales WHERE DATE(sale_date)=CURDATE()")->fetchColumn();
    $products = (int)$pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
    $lowStockLimit = 5;
    $lowStockItems = $pdo->prepare("
     SELECT sku, name, stock
    FROM products
    WHERE stock <= ?
    ORDER BY stock ASC
    LIMIT 5
    ");
    $lowStockItems->execute([$lowStockLimit]);
    $lowStockItems = $lowStockItems->fetchAll();


    $lowStockCount = (int)$pdo
    ->prepare("SELECT COUNT(*) FROM products WHERE stock <= ?")
    ->execute([$lowStockLimit])
    ?: 0;

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE stock <= ?");
    $stmt->execute([$lowStockLimit]);
    $lowStockCount = (int)$stmt->fetchColumn();

    $recent = $pdo->query("SELECT invoice_no, sale_date, grand_total FROM sales ORDER BY id DESC LIMIT 5")->fetchAll();

    return [
      'view' => 'dashboard',
      'data' => [
        'stats' => ['today_sales' => $todaySales,'products' => $products,'low_stock' => $lowStockCount,], 'lowStockLimit' => $lowStockLimit, 'lowStockItems' => $lowStockItems,
        'recent' => $recent,
      ]
    ];
  }
}
