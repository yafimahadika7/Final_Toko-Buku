<?php
declare(strict_types=1);

final class ReportsStockController {
  public static function index(PDO $pdo): array {
    $rows = $pdo->query("SELECT sku,name,stock FROM products ORDER BY name ASC")->fetchAll();
    return ['view'=>'reports_stock','data'=>compact('rows')];
  }
}
