<?php
require_once __DIR__ . "/../core/middleware.php";
requireRole("seller");
require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/header.php";

$sellerId = intval($_SESSION['user_id']);
?>
<h1>Orders Received</h1>

<table class="table">
<tr><th>Order ID</th><th>Car</th><th>Total</th><th>Status</th><th>Date</th></tr>
<?php
$stmt = mysqli_prepare($conn, "SELECT o.id, o.total_price, o.order_status, o.order_date, c.make, c.model
  FROM orders o JOIN car_listings c ON c.id=o.car_id
  WHERE o.seller_id=? ORDER BY o.order_date DESC");
mysqli_stmt_bind_param($stmt,"i",$sellerId);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while($r=mysqli_fetch_assoc($res)){
  echo "<tr>
    <td>".intval($r['id'])."</td>
    <td>".e($r['make'])." ".e($r['model'])."</td>
    <td>₹".number_format((float)$r['total_price'])."</td>
    <td>".e($r['order_status'])."</td>
    <td>".e($r['order_date'])."</td>
  </tr>";
}
?>
</table>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
