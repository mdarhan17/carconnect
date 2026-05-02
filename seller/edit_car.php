<?php
require_once __DIR__ . "/../core/middleware.php";
requireRole("seller");

require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/header.php";

$sellerId = (int)$_SESSION['user_id'];
$id = (int)($_GET['id'] ?? 0);

/* ===================== */
/* FETCH CAR */
/* ===================== */

$stmt = mysqli_prepare($conn,"
SELECT * FROM car_listings 
WHERE id=? AND seller_id=? 
LIMIT 1
");
mysqli_stmt_bind_param($stmt,"ii",$id,$sellerId);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$car = mysqli_fetch_assoc($res);

if(!$car){
  echo '<div class="alert">Car not found</div>';
  require_once __DIR__ . "/../includes/footer.php";
  exit();
}

$err="";
$ok="";

/* ===================== */
/* UPDATE */
/* ===================== */

if($_SERVER['REQUEST_METHOD']==='POST'){

$brand = post("brand");
$model = post("model");
$year  = (int)post("year");
$price = (float)post("price");
$mileage = (int)post("mileage");
$fuel = post("fuel_type");
$trans = post("transmission");
$desc = post("description");

$imgPath = $car['image_path'];

/* VALIDATION */
if(!$brand || !$model || $year<=0 || $price<=0){
  $err="Please fill required fields correctly";
}

/* IMAGE */
if(!$err && !empty($_FILES['image']['name'])){

$ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
$allow = ["jpg","jpeg","png","webp"];

if(!in_array($ext,$allow)){
  $err="Invalid image format";
}
elseif($_FILES['image']['size'] > 5*1024*1024){
  $err="Max 5MB allowed";
}
else{

$newName = uniqid("car_").".".$ext;
$uploadDir = __DIR__."/../uploads/";

if(!is_dir($uploadDir)){
  mkdir($uploadDir,0777,true);
}

$targetAbs = $uploadDir.$newName;
$targetRel = "/carconnect/uploads/".$newName;

if(move_uploaded_file($_FILES['image']['tmp_name'],$targetAbs)){
  $imgPath = $targetRel;
}else{
  $err="Upload failed";
}

}

}

/* UPDATE QUERY */

if(!$err){

$stmt2 = mysqli_prepare($conn,"
UPDATE car_listings
SET make=?, model=?, year=?, price=?, mileage=?, fuel_type=?, transmission=?, description=?, image_path=?, status='pending'
WHERE id=? AND seller_id=?
");

mysqli_stmt_bind_param(
$stmt2,
"ssidissssii",
$brand,
$model,
$year,
$price,
$mileage,
$fuel,
$trans,
$desc,
$imgPath,
$id,
$sellerId
);

if(mysqli_stmt_execute($stmt2)){
  $ok="Updated successfully. Waiting for admin approval.";

  // refresh data
  $stmt = mysqli_prepare($conn,"SELECT * FROM car_listings WHERE id=? AND seller_id=?");
  mysqli_stmt_bind_param($stmt,"ii",$id,$sellerId);
  mysqli_stmt_execute($stmt);
  $res = mysqli_stmt_get_result($stmt);
  $car = mysqli_fetch_assoc($res);

}else{
  $err="Update failed";
}

}

}
?>

<h1>✏️ Edit Car</h1>

<?php if($err): ?>
<div class="alert"><?php echo e($err); ?></div>
<?php endif; ?>

<?php if($ok): ?>
<div class="alert success"><?php echo e($ok); ?></div>
<?php endif; ?>

<form class="form" method="POST" enctype="multipart/form-data">

<!-- BRAND -->
<label>Brand</label>
<select class="input" name="brand" required>
<option value="">Select Brand</option>
<?php
$brands = ["Maruti","Tata","Mahindra","Hyundai","Honda","Toyota","Kia","BMW","Audi"];
foreach($brands as $b){
$selected = ($car['make']==$b) ? "selected" : "";
echo "<option $selected>$b</option>";
}
?>
</select>

<!-- MODEL -->
<label>Model</label>
<input class="input" name="model" value="<?php echo e($car['model']); ?>" required>

<!-- YEAR -->
<label>Year</label>
<input class="input" type="number" name="year" value="<?php echo e($car['year']); ?>" required>

<!-- PRICE -->
<label>Price (₹)</label>
<input class="input" type="number" name="price" value="<?php echo e($car['price']); ?>" required>

<!-- MILEAGE -->
<label>Mileage</label>
<input class="input" type="number" name="mileage" value="<?php echo e($car['mileage']); ?>">

<!-- FUEL -->
<label>Fuel Type</label>
<select class="input" name="fuel_type">
<?php
$fuels = ["Petrol","Diesel","CNG","Electric"];
foreach($fuels as $f){
$sel = ($car['fuel_type']==$f)?"selected":"";
echo "<option $sel>$f</option>";
}
?>
</select>

<!-- TRANSMISSION -->
<label>Transmission</label>
<select class="input" name="transmission">
<?php
$trs = ["Manual","Automatic"];
foreach($trs as $t){
$sel = ($car['transmission']==$t)?"selected":"";
echo "<option $sel>$t</option>";
}
?>
</select>

<!-- DESCRIPTION -->
<label>Description</label>
<textarea class="input" name="description"><?php echo e($car['description']); ?></textarea>

<!-- IMAGE -->
<label>Change Image</label>
<input class="input" type="file" name="image">

<?php if($car['image_path']): ?>
<img src="<?php echo $car['image_path']; ?>" style="height:80px;margin-top:8px">
<?php endif; ?>

<button class="btn primary" style="margin-top:15px">
Update Car
</button>

</form>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>