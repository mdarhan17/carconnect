<?php
require_once __DIR__ . "/../core/middleware.php";
requireRole("admin");
require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/header.php";

$totalSales = mysqli_fetch_assoc(mysqli_query($conn,"SELECT IFNULL(SUM(total_price),0) s FROM orders"))['s'];
$topCars = mysqli_query($conn,"SELECT c.make,c.model,COUNT(*) cnt FROM orders o JOIN car_listings c ON c.id=o.car_id GROUP BY o.car_id ORDER BY cnt DESC LIMIT 5");
?>
<h1>Reports</h1>
<div class="card" style="max-width:520px">
  <div class="p">
    <div class="muted">Total Sales</div>
    <div style="font-size:28px;font-weight:900">₹<?php echo number_format((float)$totalSales); ?></div>
  </div>
</div>

<h2 style="margin-top:18px">Top Cars</h2>
<table class="table">
<tr><th>Car</th><th>Orders</th></tr>
<?php while($r=mysqli_fetch_assoc($topCars)){
  echo "<tr><td>".e($r['make'])." ".e($r['model'])."</td><td>".intval($r['cnt'])."</td></tr>";
} ?>
</table>

<p class="muted" style="margin-top:12px">
Analytics pages: <a class="btn" href="/carconnect/analytics/car_sales_analytics.php">Sales</a>
<a class="btn" href="/carconnect/analytics/user_activity_analytics.php">Users</a>
</p>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
