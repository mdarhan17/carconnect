<?php
require_once __DIR__ . "/../core/middleware.php";
requireRole("seller");

require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/header.php";

$sellerId = (int)$_SESSION['user_id'];
?>

<h1>📦 Orders Received</h1>

<table class="table">

<tr>
<th>ID</th>
<th>Car</th>
<th>Buyer</th>
<th>Price</th>
<th>Status</th>
<th>Payment</th>
<th>Date</th>
<th>Action</th>
</tr>

<?php

$stmt = mysqli_prepare($conn,"
SELECT 
o.id,
o.total_price,
o.order_status,
o.car_id,
o.buyer_id,
c.make,
c.model,
b.name buyer,
p.payment_status
FROM orders o
JOIN car_listings c ON c.id=o.car_id
JOIN buyers b ON b.id=o.buyer_id
LEFT JOIN payments p ON p.order_id=o.id
WHERE o.seller_id=?
ORDER BY o.id DESC
");

mysqli_stmt_bind_param($stmt,"i",$sellerId);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($res)>0){

while($r=mysqli_fetch_assoc($res)){

/* STATUS COLOR */
$statusColor = "#888";
if($r['order_status']=="completed") $statusColor="#4caf50";
if($r['order_status']=="pending")   $statusColor="#ff9800";
if($r['order_status']=="cancelled") $statusColor="#f44336";

/* PAYMENT COLOR */
$payColor = "#888";
if($r['payment_status']=="paid")    $payColor="#4caf50";
if($r['payment_status']=="pending") $payColor="#ff9800";
if($r['payment_status']=="failed")  $payColor="#f44336";

echo "

<tr>

<td>#".intval($r['id'])."</td>

<td>".e($r['make'])." ".e($r['model'])."</td>

<td>".e($r['buyer'])."</td>

<td>₹".number_format((float)$r['total_price'])."</td>

<td style='color:$statusColor;font-weight:700'>
".ucfirst($r['order_status'])."
</td>

<td style='color:$payColor;font-weight:700'>
".ucfirst($r['payment_status'] ?? 'N/A')."
</td>

<td>#".intval($r['id'])."</td>

<td>

<a class='btn'
href='chat.php?buyer=".$r['buyer_id']."&car_id=".$r['car_id']."'>
💬 Chat
</a>

</td>

</tr>

";

}

}else{

echo "

<tr>
<td colspan='8' style='text-align:center'>

<p class='muted'>No orders yet 📦</p>

</td>
</tr>

";

}

?>

</table>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>