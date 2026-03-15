<?php
require_once __DIR__ . "/../core/middleware.php";
requireRole("admin");

require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/header.php";

/* APPROVE */

if(isset($_GET['approve'])){
  $id = intval($_GET['approve']);
  mysqli_query($conn,"UPDATE car_listings SET status='approved' WHERE id=$id");
  echo '<div class="alert success">Car approved ✅</div>';
}

/* SET PENDING */

if(isset($_GET['reject'])){
  $id = intval($_GET['reject']);
  mysqli_query($conn,"UPDATE car_listings SET status='pending' WHERE id=$id");
  echo '<div class="alert">Car set to pending.</div>';
}

/* DELETE CAR */

if(isset($_GET['delete'])){
  $id = intval($_GET['delete']);

  mysqli_query($conn,"DELETE FROM car_listings WHERE id=$id");

  echo '<div class="alert success">Car deleted successfully 🗑️</div>';
}
?>

<h1>Manage Car Listings</h1>

<table class="table">

<tr>
<th>Car</th>
<th>Seller</th>
<th>Price</th>
<th>Status</th>
<th>Action</th>
</tr>

<?php

$sql="
SELECT 
c.id,
c.make,
c.model,
c.price,
c.status,
u.name seller
FROM car_listings c
JOIN users u ON u.id=c.seller_id
ORDER BY c.created_at DESC
";

$res=mysqli_query($conn,$sql);

while($c=mysqli_fetch_assoc($res)){

echo "

<tr>

<td>".e($c['make'])." ".e($c['model'])."</td>

<td>".e($c['seller'])."</td>

<td>₹".number_format((float)$c['price'])."</td>

<td>".e($c['status'])."</td>

<td>

<a class='btn primary'
href='/carconnect/admin/manage_cars.php?approve=".intval($c['id'])."'>
Approve
</a>

<a class='btn'
href='/carconnect/admin/manage_cars.php?reject=".intval($c['id'])."'>
Pending
</a>

<a class='btn'
style='background:#ff4d4d;color:white'
onclick=\"return confirm('Delete this car?')\"
href='/carconnect/admin/manage_cars.php?delete=".intval($c['id'])."'>
Delete
</a>

</td>

</tr>

";

}

?>

</table>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>