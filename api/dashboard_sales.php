<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../app/Helpers/auth.php';

require_login();

$days = (int)($_GET['days'] ?? 7);
if ($days <= 0 || $days > 90) $days = 7;

$sql = "
  SELECT DATE(sale_date) as d, COALESCE(SUM(grand_total),0) as t
  FROM sales
  WHERE sale_date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
  GROUP BY DATE(sale_date)
  ORDER BY d ASC
";
$st = $pdo->prepare($sql);
$st->execute([$days]);

header('Content-Type: application/json; charset=utf-8');
echo json_encode($st->fetchAll(), JSON_UNESCAPED_UNICODE);
