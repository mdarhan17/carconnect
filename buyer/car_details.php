<?php
require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/header.php";

$id = (int)($_GET['id'] ?? 0);

/* ===================== */
/* FETCH CAR */
/* ===================== */

$stmt = mysqli_prepare($conn,"
SELECT c.*, s.name AS seller_name, s.id AS seller_id
FROM car_listings c
JOIN sellers s ON s.id = c.seller_id
WHERE c.id=? AND c.status='approved'
LIMIT 1
");

mysqli_stmt_bind_param($stmt,"i",$id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$car = mysqli_fetch_assoc($res);

if(!$car){
  echo '<div class="alert">Car not found.</div>';
  require_once __DIR__ . "/../includes/footer.php";
  exit();
}

$img = $car['image_path'] 
? e($car['image_path']) 
: "/carconnect/assets/images/default_car.jpg";
?>

<h1 style="text-align:center;margin-bottom:20px">
🚗 <?php echo e($car['make'])." ".e($car['model']); ?>
</h1>

<div class="card" style="max-width:900px;margin:auto">

<img src="<?php echo $img; ?>" style="height:360px;object-fit:cover;border-radius:10px 10px 0 0">

<div class="p">

<!-- PRICE -->
<h2 style="color:#00d2ff;margin:5px 0">
₹<?php echo number_format((float)$car['price']); ?>
</h2>

<!-- SPECS -->
<div class="muted" style="margin-bottom:10px">

<?php echo e($car['year']); ?> • 
<?php echo e($car['mileage']); ?> km • 
<?php echo e($car['fuel_type']); ?> • 
<?php echo e($car['transmission']); ?>

</div>

<!-- DESCRIPTION -->
<p style="line-height:1.6">
<?php echo nl2br(e($car['description'])); ?>
</p>

<!-- SELLER -->
<div class="muted" style="margin-top:10px">
Seller: <strong><?php echo e($car['seller_name']); ?></strong>
</div>

<!-- BUTTONS -->
<div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:20px">

<a class="btn"
href="/carconnect/buyer/wishlist.php?add=<?php echo intval($car['id']); ?>">
❤️ Wishlist
</a>

<a class="btn primary"
href="/carconnect/buyer/checkout.php?id=<?php echo intval($car['id']); ?>">
💳 Buy Now
</a>

<a class="btn"
href="/carconnect/buyer/chat.php?car_id=<?php echo intval($car['id']); ?>&seller=<?php echo intval($car['seller_id']); ?>">
💬 Chat
</a>

</div>

</div>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>