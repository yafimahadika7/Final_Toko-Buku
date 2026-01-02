<?php
declare(strict_types=1);

final class ReportsSalesController {
  public static function index(PDO $pdo): array {
    $rows = $pdo->query("
      SELECT DATE(sale_date) d, SUM(grand_total) total
      FROM sales
      WHERE status='PAID'
      GROUP BY DATE(sale_date)
      ORDER BY d DESC
      LIMIT 60
    ")->fetchAll();
    return ['view'=>'reports_sales','data'=>compact('rows')];
  }
}
