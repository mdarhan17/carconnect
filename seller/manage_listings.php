<?php
require_once __DIR__ . "/../core/middleware.php";
requireRole("seller");
require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/header.php";

$sellerId = intval($_SESSION['user_id']);
?>
<h1>Manage Listings</h1>
<p class="muted">Edit your cars. After edit listing goes pending for admin approval.</p>

<table class="table">
<tr><th>Car</th><th>Status</th><th>Price</th><th>Action</th></tr>
<?php
$stmt = mysqli_prepare($conn, "SELECT id,make,model,status,price FROM car_listings WHERE seller_id=? ORDER BY created_at DESC");
mysqli_stmt_bind_param($stmt,"i",$sellerId);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while($c=mysqli_fetch_assoc($res)){
  echo "<tr>
    <td>".e($c['make'])." ".e($c['model'])."</td>
    <td>".e($c['status'])."</td>
    <td>₹".number_format((float)$c['price'])."</td>
    <td><a class='btn' href='/carconnect/seller/edit_car.php?id=".intval($c['id'])."'>Edit</a></td>
  </tr>";
}
?>
</table>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
