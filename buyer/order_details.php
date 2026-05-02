<?php
require_once "../core/middleware.php";
requireRole("buyer");

require_once "../includes/db_connect.php";
require_once "../includes/functions.php";
require_once "../includes/header.php";

$id = (int)($_GET['id'] ?? 0);
$buyerId = $_SESSION['user_id'];

$res = mysqli_query($conn,"
SELECT o.*,c.make,c.model,s.name seller
FROM orders o
JOIN car_listings c ON c.id=o.car_id
JOIN sellers s ON s.id=o.seller_id
WHERE o.id=$id AND o.buyer_id=$buyerId
");

$order = mysqli_fetch_assoc($res);
?>

<h1>Order Details</h1>

<?php if($order): ?>

<div class="card p">

<h3><?php echo e($order['make']." ".$order['model']); ?></h3>

<p>Seller: <?php echo e($order['seller']); ?></p>
<p>Status: <?php echo $order['order_status']; ?></p>
<p>Total: ₹<?php echo number_format($order['total_price']); ?></p>

</div>

<?php else: ?>

<p class="alert">Order not found</p>

<?php endif; ?>

<?php require_once "../includes/footer.php"; ?>