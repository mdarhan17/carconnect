<?php
require_once __DIR__ . "/../core/middleware.php";
requireRole("seller");

require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/header.php";

$id = (int)($_GET['id'] ?? 0);
$seller = $_SESSION['user_id'];

/* ===================== */
/* DELETE HANDLER (SAFE) */
/* ===================== */

if(isset($_POST['delete'])){

  $stmt = mysqli_prepare($conn,"
  DELETE FROM car_listings 
  WHERE id=? AND seller_id=?
  ");

  mysqli_stmt_bind_param($stmt,"ii",$id,$seller);
  mysqli_stmt_execute($stmt);

  header("Location: seller_dashboard.php?msg=deleted");
  exit();
}

/* ===================== */
/* FETCH CAR (IMPORTANT FIX) */
/* ===================== */

$stmt = mysqli_prepare($conn,"
SELECT * FROM car_listings 
WHERE id=?
LIMIT 1
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

<h1 style="text-align:center;margin-bottom:20px">
🚗 <?php echo e($car['make']." ".$car['model']); ?>
</h1>

<div class="card" style="max-width:900px;margin:auto">

<img src="<?php echo e($img); ?>" style="height:350px;object-fit:cover;border-radius:10px 10px 0 0">

<div class="p">

<!-- PRICE -->
<h2 style="color:#00d2ff;margin:5px 0">
₹<?php echo number_format((float)$car['price']); ?>
</h2>

<!-- SPECS -->
<p class="muted">
<?php echo e($car['year']); ?> • 
<?php echo e($car['mileage']); ?> km • 
<?php echo e($car['fuel_type']); ?> • 
<?php echo e($car['transmission']); ?>
</p>

<!-- DESCRIPTION -->
<p style="line-height:1.6">
<?php echo nl2br(e($car['description'])); ?>
</p>

<!-- STATUS -->
<p class="muted">
Status: <b><?php echo e($car['status']); ?></b>
</p>

<!-- ACTIONS (IMPORTANT CONDITION) -->
<?php if($car['seller_id'] == $seller): ?>

<div style="margin-top:20px;display:flex;gap:10px;flex-wrap:wrap">

<a class="btn"
href="edit_car.php?id=<?php echo $car['id']; ?>">
✏️ Edit
</a>

<form method="POST" onsubmit="return confirm('Delete this car permanently?')">
<button name="delete" class="btn" style="background:#ff4d4d;color:white">
🗑️ Delete
</button>
</form>

</div>

<?php endif; ?>

</div>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>