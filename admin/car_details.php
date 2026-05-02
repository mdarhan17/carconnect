<?php
require_once __DIR__ . "/../core/middleware.php";
requireRole("admin");

require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/header.php";

$id = (int)($_GET['id'] ?? 0);

/* FETCH */
$stmt = mysqli_prepare($conn,"
SELECT c.*, s.name AS seller_name
FROM car_listings c
JOIN sellers s ON s.id = c.seller_id
WHERE c.id=? LIMIT 1
");

mysqli_stmt_bind_param($stmt,"i",$id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$car = mysqli_fetch_assoc($res);

if(!$car){
  echo "<div class='alert'>Car not found</div>";
  require_once __DIR__ . "/../includes/footer.php";
  exit();
}

$img = $car['image_path'] ?: "/carconnect/assets/images/default_car.jpg";
?>

<h1>🚗 <?php echo e($car['make']." ".$car['model']); ?></h1>

<div class="card" style="max-width:900px;margin:auto">

<img src="<?php echo e($img); ?>" style="height:350px;object-fit:cover">

<div class="p">

<h2 style="color:#00d2ff">₹<?php echo number_format($car['price']); ?></h2>

<p class="muted">
<?php echo e($car['year']); ?> • 
<?php echo e($car['mileage']); ?> km • 
<?php echo e($car['fuel_type']); ?> • 
<?php echo e($car['transmission']); ?>
</p>

<p><?php echo nl2br(e($car['description'])); ?></p>

<p class="muted">Seller: <?php echo e($car['seller_name']); ?></p>

<p>Status: <b><?php echo e($car['status']); ?></b></p>

</div>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>