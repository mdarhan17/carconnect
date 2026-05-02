<?php
require_once "../core/middleware.php";
requireRole("admin");

require_once "../includes/db_connect.php";
require_once "../includes/functions.php";
require_once "../includes/header.php";

/* UPDATE STATUS (SAFE) */
if(isset($_GET['complete'])){
  $id = intval($_GET['complete']);
  mysqli_query($conn,"UPDATE orders SET order_status='completed' WHERE id=$id");
}
?>

<h1>📦 Manage Orders</h1>

<table class="table">
<tr>
<th>ID</th>
<th>Car</th>
<th>Buyer</th>
<th>Seller</th>
<th>Price</th>
<th>Status</th>
<th>Action</th>
</tr>

<?php

$res = mysqli_query($conn,"
SELECT 
o.id,
o.total_price,
o.order_status,
c.make,
c.model,
b.name AS buyer,
s.name AS seller
FROM orders o
JOIN car_listings c ON c.id=o.car_id
JOIN buyers b ON b.id=o.buyer_id
JOIN sellers s ON s.id=o.seller_id
ORDER BY o.id DESC
");

if($res && mysqli_num_rows($res)>0){

while($o=mysqli_fetch_assoc($res)){

$statusColor = "#999";
if($o['order_status']=="completed") $statusColor="#16a34a";
if($o['order_status']=="pending")   $statusColor="#f59e0b";
if($o['order_status']=="cancelled") $statusColor="#ef4444";

echo "<tr>

<td>#".intval($o['id'])."</td>

<td>".e($o['make'])." ".e($o['model'])."</td>

<td>".e($o['buyer'])."</td>

<td>".e($o['seller'])."</td>

<td>₹".number_format((float)$o['total_price'])."</td>

<td>
<span style='
background:$statusColor;
color:#fff;
padding:5px 12px;
border-radius:20px;
font-size:12px;
font-weight:700
'>
".ucfirst($o['order_status'])."
</span>
</td>

<td>";

if($o['order_status']=="pending"){
echo "<a class='btn primary' href='?complete=".$o['id']."'>Complete</a>";
}else{
echo "<span class='muted'>—</span>";
}

echo "</td></tr>";

}

}else{

echo "<tr>
<td colspan='7' style='text-align:center'>
No orders found 📦
</td>
</tr>";

}

?>

</table>

<?php require_once "../includes/footer.php"; ?>