<?php
require_once __DIR__ . "/../core/middleware.php";
requireRole("seller");

require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/header.php";

$sellerId = (int)$_SESSION['user_id'];

/* FILTERS */
$keyword = trim($_GET['q'] ?? "");
$minPrice = (int)($_GET['min'] ?? 0);
$maxPrice = (int)($_GET['max'] ?? 0);
?>

<h1>🔍 Search Your Cars</h1>

<form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:20px">

<input class="input" name="q" placeholder="Search by brand/model"
value="<?php echo e($keyword); ?>">

<input class="input" name="min" type="number" placeholder="Min Price"
value="<?php echo $minPrice ?: ''; ?>">

<input class="input" name="max" type="number" placeholder="Max Price"
value="<?php echo $maxPrice ?: ''; ?>">

<button class="btn primary">Search</button>

<a class="btn" href="search_cars.php">Reset</a>

</form>

<div class="grid">

<?php

$sql = "SELECT * FROM car_listings WHERE seller_id=?";
$params = [$sellerId];
$types = "i";

/* SEARCH */
if($keyword){
  $sql .= " AND (make LIKE ? OR model LIKE ?)";
  $types .= "ss";
  $search = "%$keyword%";
  $params[] = $search;
  $params[] = $search;
}

/* PRICE FILTER */
if($minPrice > 0){
  $sql .= " AND price >= ?";
  $types .= "i";
  $params[] = $minPrice;
}

if($maxPrice > 0){
  $sql .= " AND price <= ?";
  $types .= "i";
  $params[] = $maxPrice;
}

$sql .= " ORDER BY created_at DESC";

/* EXECUTE */
$stmt = mysqli_prepare($conn,$sql);
mysqli_stmt_bind_param($stmt,$types,...$params);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

/* RESULT */

if(mysqli_num_rows($res)>0){

while($c=mysqli_fetch_assoc($res)){

$img = $c['image_path'] ?: "/carconnect/assets/images/default_car.jpg";

/* STATUS COLOR */
$statusColor = "#888";
if($c['status']=="approved") $statusColor="#4caf50";
if($c['status']=="pending")  $statusColor="#ff9800";
if($c['status']=="sold")     $statusColor="#9c27b0";

echo "

<div class='card'>

<img src='$img' style='height:180px;object-fit:cover'>

<div class='p'>

<div style='font-weight:800;font-size:16px'>
".e($c['make'])." ".e($c['model'])."
</div>

<div class='muted'>
".e($c['year'])."
</div>

<div style='margin-top:6px;font-weight:700;color:#00d2ff'>
₹".number_format($c['price'])."
</div>

<div style='margin-top:6px;color:$statusColor;font-weight:700'>
".ucfirst($c['status'])."
</div>

<div style='margin-top:10px'>
<a class='btn' href='edit_car.php?id=".$c['id']."'>✏️ Edit</a>
</div>

</div>

</div>

";

}

}else{

echo "

<div style='text-align:center;width:100%'>

<p class='muted' style='font-size:18px'>
No cars found 🚗
</p>

<p class='muted'>
Try adjusting filters or add new listings.
</p>

<a class='btn primary' href='add_car.php'>
➕ Add Car
</a>

</div>

";

}

?>

</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>