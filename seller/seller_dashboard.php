<?php
require_once __DIR__ . "/../core/middleware.php";
requireRole("seller");

require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/header.php";

$sellerId = intval($_SESSION['user_id']);
?>

<h1>Seller Dashboard</h1>

<div style="display:flex;gap:12px;flex-wrap:wrap">

<a class="btn primary"
href="/carconnect/seller/add_car.php">
➕ Add Car
</a>

<a class="btn"
href="/carconnect/seller/manage_listings.php">
📋 Manage Listings
</a>

<a class="btn"
href="/carconnect/seller/view_orders.php">
📦 View Orders
</a>

<a class="btn"
href="/carconnect/seller/chat.php">
💬 Buyer Messages
</a>

</div>


<h2 style="margin-top:18px">Your Listings</h2>

<table class="table">

<tr>
<th>Car</th>
<th>Price</th>
<th>Status</th>
<th>Action</th>
</tr>

<?php

$stmt = mysqli_prepare(
$conn,
"SELECT id,make,model,price,status
FROM car_listings
WHERE seller_id=?
ORDER BY created_at DESC"
);

mysqli_stmt_bind_param($stmt,"i",$sellerId);
mysqli_stmt_execute($stmt);

$res = mysqli_stmt_get_result($stmt);

while($c=mysqli_fetch_assoc($res)){

echo "

<tr>

<td>".e($c['make'])." ".e($c['model'])."</td>

<td>₹".number_format((float)$c['price'])."</td>

<td>".e($c['status'])."</td>

<td>

<a class='btn'
href='/carconnect/seller/edit_car.php?id=".intval($c['id'])."'>
✏️ Edit
</a>

</td>

</tr>

";

}

?>

</table>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>