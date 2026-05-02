<?php 
session_start();

/* 🔒 LOGIN REQUIRED */
if(empty($_SESSION['user_id'])){
    header("Location: /carconnect/auth/login.php");
    exit();
}

require_once __DIR__ . "/includes/header.php"; 
require_once __DIR__ . "/includes/db_connect.php"; 
require_once __DIR__ . "/includes/functions.php";

/* FILTERS */
$brand = trim($_GET['brand'] ?? "");
$category = (int)($_GET['category'] ?? 0);

/* ROLE */
$role = $_SESSION['role'] ?? 'buyer';

/* ROLE BASED PATH */
$path = "/carconnect/buyer/car_details.php";

if($role === "admin"){
  $path = "/carconnect/admin/car_details.php";
}
elseif($role === "seller"){
  $path = "/carconnect/seller/car_details.php";
}
?>

<h2 class="page-title">Browse Cars</h2>

<!-- 🔍 FILTER FORM -->
<form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:20px">

<!-- BRAND -->
<input class="input" name="brand" placeholder="Search brand" value="<?php echo e($brand); ?>">

<!-- CATEGORY -->
<select class="input" name="category">
<option value="">All Categories</option>

<?php
$resCat = mysqli_query($conn,"SELECT * FROM car_categories ORDER BY name ASC");

while($cat=mysqli_fetch_assoc($resCat)){
$sel = ($category == $cat['id']) ? "selected" : "";
echo "<option value='".$cat['id']."' $sel>".e($cat['name'])."</option>";
}
?>

</select>

<button class="btn primary">Filter</button>
<a class="btn" href="/carconnect/cars.php">Reset</a>

</form>

<div class="grid">

<?php

/* ===================== */
/* QUERY BUILD */
/* ===================== */

$sql = "SELECT * FROM car_listings WHERE status='approved'";
$params = [];
$types = "";

/* BRAND FILTER */
if($brand){
    $sql .= " AND make LIKE ?";
    $types .= "s";
    $params[] = "%$brand%";
}

/* CATEGORY FILTER */
if($category > 0){
    $sql .= " AND category_id=?";
    $types .= "i";
    $params[] = $category;
}

$sql .= " ORDER BY created_at DESC";

/* EXECUTE */
$stmt = mysqli_prepare($conn,$sql);

if(!empty($params)){
    mysqli_stmt_bind_param($stmt,$types,...$params);
}

mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

/* ===================== */
/* RESULT */
/* ===================== */

if(mysqli_num_rows($res)>0){

while($c = mysqli_fetch_assoc($res)){

$img = $c['image_path'] 
? e($c['image_path']) 
: "/carconnect/assets/images/default_car.jpg";
?>

<div class="card car-card">

<img src="<?php echo $img; ?>" style="height:200px;object-fit:cover">

<div class="p">

<div class="car-title">
<?php echo e($c['make']." ".$c['model']); ?>
</div>

<div class="muted">
<?php echo e($c['year']); ?>
</div>

<div class="price">
₹<?php echo number_format((float)$c['price']); ?>
</div>

<div class="car-meta">
Mileage: <?php echo e($c['mileage']); ?> km<br>
Fuel: <?php echo e($c['fuel_type']); ?><br>
Transmission: <?php echo e($c['transmission']); ?>
</div>

<div class="mt-10">

<a class="btn primary"
href="<?php echo $path; ?>?id=<?php echo intval($c['id']); ?>">
View Details
</a>

</div>

</div>

</div>

<?php
}

}else{
?>

<div class="empty-state">

<p class="empty-title">🚗 No cars found</p>

<p class="muted">
Try changing filters or search again.
</p>

<a class="btn primary" href="/carconnect/cars.php">
Browse All Cars
</a>

</div>

<?php } ?>

</div>

<?php require_once __DIR__ . "/includes/footer.php"; ?>