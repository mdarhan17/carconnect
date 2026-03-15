<?php
require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/header.php";

$q = isset($_GET['q']) ? trim($_GET['q']) : "";
?>
<h1>Browse Cars</h1>

<form class="form" method="GET" style="max-width:700px">
  <label>Search (make/model)</label>
  <input class="input" name="q" value="<?php echo e($q); ?>" placeholder="e.g. Honda City">
  <div style="margin-top:12px"><button class="btn primary">Search</button></div>
</form>

<div class="grid" style="margin-top:18px">
<?php
if($q !== ""){
  $like = "%".$q."%";
  $stmt = mysqli_prepare($conn, "SELECT id,make,model,year,price,image_path FROM car_listings WHERE status='approved' AND (make LIKE ? OR model LIKE ?) ORDER BY created_at DESC");
  mysqli_stmt_bind_param($stmt, "ss", $like, $like);
  mysqli_stmt_execute($stmt);
  $res = mysqli_stmt_get_result($stmt);
} else {
  $res = mysqli_query($conn, "SELECT id,make,model,year,price,image_path FROM car_listings WHERE status='approved' ORDER BY created_at DESC");
}

while($c = mysqli_fetch_assoc($res)){
  $img = $c['image_path'] ? e($c['image_path']) : "/carconnect/assets/images/default_car.jpg";
  echo '<div class="card">
    <img src="'.$img.'" alt="car">
    <div class="p">
      <div style="font-weight:800">'.e($c['make']).' '.e($c['model']).'</div>
      <div class="muted">'.e($c['year']).' • ₹'.number_format((float)$c['price']).'</div>
      <div style="margin-top:10px"><a class="btn" href="/carconnect/buyer/car_details.php?id='.intval($c['id']).'">View Details</a></div>
    </div>
  </div>';
}
?>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
