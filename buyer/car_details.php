<?php
require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/header.php";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = mysqli_prepare(
$conn,
"SELECT c.*, u.name as seller_name, u.id as seller_id
FROM car_listings c
JOIN users u ON u.id=c.seller_id
WHERE c.id=? AND c.status='approved'
LIMIT 1"
);

mysqli_stmt_bind_param($stmt, "i", $id);
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

<h1><?php echo e($car['make'])." ".e($car['model']); ?></h1>

<div class="card" style="max-width:860px;margin:0 auto">

<img src="<?php echo $img; ?>" style="height:320px;object-fit:cover">

<div class="p">

<p class="muted">

<?php echo e($car['year']); ?> • 
Mileage: <?php echo e($car['mileage']); ?> km • 
Fuel: <?php echo e($car['fuel_type']); ?> • 
Transmission: <?php echo e($car['transmission']); ?>

</p>

<h2 style="margin:8px 0">
₹<?php echo number_format((float)$car['price']); ?>
</h2>

<p>
<?php echo nl2br(e($car['description'])); ?>
</p>

<p class="muted">
Seller: <?php echo e($car['seller_name']); ?>
</p>

<div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:12px">

<a class="btn"
href="/carconnect/buyer/wishlist.php?add=<?php echo intval($car['id']); ?>">
❤️ Add to Wishlist
</a>

<a class="btn primary"
href="/carconnect/buyer/checkout.php?id=<?php echo intval($car['id']); ?>">
💳 Buy Now
</a>

<a class="btn"
href="/carconnect/buyer/chat.php?car_id=<?php echo intval($car['id']); ?>&seller=<?php echo intval($car['seller_id']); ?>">
💬 Chat with Seller
</a>

</div>

</div>

</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>