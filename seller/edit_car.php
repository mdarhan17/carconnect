<?php
require_once __DIR__ . "/../core/middleware.php";
requireRole("seller");

require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/header.php";

$sellerId = intval($_SESSION['user_id']);
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

/* GET CAR DATA */

$stmt = mysqli_prepare($conn,"SELECT * FROM car_listings WHERE id=? AND seller_id=? LIMIT 1");
mysqli_stmt_bind_param($stmt,"ii",$id,$sellerId);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$car = mysqli_fetch_assoc($res);

if(!$car){
  echo '<div class="alert">Car not found.</div>';
  require_once __DIR__ . "/../includes/footer.php";
  exit();
}

$err="";
$ok="";

/* UPDATE CAR */

if($_SERVER['REQUEST_METHOD']==='POST'){

$make = post("make");
$model = post("model");
$year = intval(post("year"));
$price = floatval(post("price"));
$desc = post("description");

$imgPath = $car['image_path'];

/* IMAGE UPLOAD */

if(isset($_FILES['image']) && $_FILES['image']['error'] === 0){

$ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

$allow = ["jpg","jpeg","png","webp"];

if(in_array($ext,$allow) && $_FILES['image']['size'] <= 5*1024*1024){

$newName = uniqid("car_",true).".".$ext;

$targetRel = "/carconnect/uploads/".$newName;

$targetAbs = __DIR__."/../uploads/".$newName;

if(move_uploaded_file($_FILES['image']['tmp_name'],$targetAbs)){
$imgPath = $targetRel;
}else{
$err="Image upload failed.";
}

}else{
$err="Invalid image file.";
}

}

/* UPDATE QUERY */

if(!$err){

$stmt2 = mysqli_prepare($conn,"
UPDATE car_listings 
SET make=?,model=?,year=?,price=?,description=?,image_path=?,status='pending'
WHERE id=? AND seller_id=?
");

mysqli_stmt_bind_param(
$stmt2,
"ssidssii",
$make,
$model,
$year,
$price,
$desc,
$imgPath,
$id,
$sellerId
);

if(mysqli_stmt_execute($stmt2)){

$ok="Updated successfully. Waiting for admin approval.";

$stmt = mysqli_prepare($conn,"SELECT * FROM car_listings WHERE id=? AND seller_id=? LIMIT 1");
mysqli_stmt_bind_param($stmt,"ii",$id,$sellerId);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$car = mysqli_fetch_assoc($res);

}else{

$err="Update failed.";

}

}

}

?>

<h1>Edit Car</h1>

<?php if($err) echo '<div class="alert">'.$err.'</div>'; ?>
<?php if($ok) echo '<div class="alert success">'.$ok.'</div>'; ?>

<form class="form" method="POST" enctype="multipart/form-data">

<label>Make</label>
<input class="input" name="make" value="<?php echo e($car['make']); ?>" required>

<label>Model</label>
<input class="input" name="model" value="<?php echo e($car['model']); ?>" required>

<label>Year</label>
<input class="input" name="year" value="<?php echo e($car['year']); ?>" required>

<label>Price</label>
<input class="input" name="price" value="<?php echo e($car['price']); ?>" required>

<label>Description</label>
<textarea class="input" name="description" rows="5"><?php echo e($car['description']); ?></textarea>

<label>New Image (optional)</label>
<input class="input" type="file" name="image" accept="image/*">

<div style="margin-top:12px">
<button class="btn primary">Update Car</button>
</div>

</form>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>