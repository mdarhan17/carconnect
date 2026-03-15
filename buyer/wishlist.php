<?php
require_once __DIR__ . "/../core/middleware.php";
requireRole("buyer");
require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/header.php";

$buyerId = intval($_SESSION['user_id']);

if(isset($_GET['add'])){
  $carId = intval($_GET['add']);
  $stmt = mysqli_prepare($conn, "INSERT IGNORE INTO wishlist(buyer_id,car_id) VALUES (?,?)");
  mysqli_stmt_bind_param($stmt,"ii",$buyerId,$carId);
  mysqli_stmt_execute($stmt);
  echo '<div class="alert success">Added to wishlist ✅</div>';
}

if(isset($_GET['remove'])){
  $carId = intval($_GET['remove']);
  $stmt = mysqli_prepare($conn, "DELETE FROM wishlist WHERE buyer_id=? AND car_id=?");
  mysqli_stmt_bind_param($stmt,"ii",$buyerId,$carId);
  mysqli_stmt_execute($stmt);
  echo '<div class="alert success">Removed ✅</div>';
}
?>
<h1>Your Wishlist</h1>

<table class="table">
  <tr><th>Car</th><th>Price</th><th>Action</th></tr>
<?php
$sql = "SELECT c.id, c.make, c.model, c.price FROM wishlist w JOIN car_listings c ON c.id=w.car_id WHERE w.buyer_id=? ORDER BY w.created_at DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt,"i",$buyerId);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

while($c = mysqli_fetch_assoc($res)){
  echo "<tr>
    <td>".e($c['make'])." ".e($c['model'])."</td>
    <td>₹".number_format((float)$c['price'])."</td>
    <td>
      <a class='btn' href='/carconnect/buyer/car_details.php?id=".intval($c['id'])."'>View</a>
      <a class='btn' href='/carconnect/buyer/wishlist.php?remove=".intval($c['id'])."'>Remove</a>
    </td>
  </tr>";
}
?>
</table>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
