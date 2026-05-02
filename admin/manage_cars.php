<?php
require_once __DIR__ . "/../core/middleware.php";
requireRole("admin");

require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/header.php";

/* ===================== */
/* HANDLE ACTIONS */
/* ===================== */

if($_SERVER['REQUEST_METHOD']=="POST"){

  $id = intval($_POST['id']);

  if($_POST['action']=="approve"){
    mysqli_query($conn,"UPDATE car_listings SET status='approved' WHERE id=$id");
    header("Location: manage_cars.php?msg=approved");
    exit();
  }

  if($_POST['action']=="reject"){
    mysqli_query($conn,"UPDATE car_listings SET status='rejected' WHERE id=$id");
    header("Location: manage_cars.php?msg=rejected");
    exit();
  }

  if($_POST['action']=="delete"){
    mysqli_query($conn,"DELETE FROM car_listings WHERE id=$id");
    header("Location: manage_cars.php?msg=deleted");
    exit();
  }
}

/* ===================== */
/* MESSAGE */
/* ===================== */

if(isset($_GET['msg'])){
  if($_GET['msg']=="approved") echo "<div class='alert success'>Car Approved ✅</div>";
  if($_GET['msg']=="rejected") echo "<div class='alert'>Car Rejected ❌</div>";
  if($_GET['msg']=="deleted") echo "<div class='alert success'>Car Deleted 🗑️</div>";
}
?>

<h1>🚗 Manage Car Listings</h1>

<!-- ===================== -->
<!-- PENDING CARS -->
<!-- ===================== -->

<h2>⏳ Pending Approval</h2>

<div class="grid">

<?php

$res = mysqli_query($conn,"
SELECT c.*, s.name seller
FROM car_listings c
JOIN sellers s ON s.id=c.seller_id
WHERE c.status='pending'
ORDER BY c.id DESC
");

if($res && mysqli_num_rows($res)>0){

while($c=mysqli_fetch_assoc($res)){

$img = $c['image_path'] ?: "/carconnect/assets/images/default_car.jpg";

echo "

<div class='card'>

<img src='".e($img)."' style='height:200px;object-fit:cover'>

<div class='p'>

<div style='font-weight:800;font-size:18px'>
".e($c['make'])." ".e($c['model'])."
</div>

<div class='muted'>
Year: ".e($c['year'])."
</div>

<div style='margin-top:6px;color:#00d2ff;font-weight:700'>
₹".number_format((float)$c['price'])."
</div>

<div class='muted' style='margin-top:6px'>
Seller: ".e($c['seller'])."
</div>

<div class='muted' style='margin-top:6px;font-size:13px'>
Mileage: ".e($c['mileage'])." km • 
Fuel: ".e($c['fuel_type'])." • 
Transmission: ".e($c['transmission'])."
</div>

<div class='muted' style='margin-top:6px;font-size:13px'>
".e(substr($c['description'],0,100))."...
</div>

<div style='margin-top:12px;display:flex;gap:8px;flex-wrap:wrap'>

<form method='POST'>
<input type='hidden' name='id' value='{$c['id']}'>
<button name='action' value='approve' class='btn primary'>Approve</button>
</form>

<form method='POST'>
<input type='hidden' name='id' value='{$c['id']}'>
<button name='action' value='reject' class='btn'>Reject</button>
</form>

<form method='POST' onsubmit=\"return confirm('Delete this car?')\">
<input type='hidden' name='id' value='{$c['id']}'>
<button name='action' value='delete' class='btn' style='background:#ef4444;color:white'>Delete</button>
</form>

</div>

</div>

</div>

";

}

}else{
echo "<p class='muted'>No pending cars</p>";
}

?>

</div>

<!-- ===================== -->
<!-- APPROVED CARS -->
<!-- ===================== -->

<h2 style="margin-top:40px">✅ Approved Cars</h2>

<div class="grid">

<?php

$res = mysqli_query($conn,"
SELECT c.*, s.name seller
FROM car_listings c
JOIN sellers s ON s.id=c.seller_id
WHERE c.status='approved'
ORDER BY c.id DESC
");

if($res && mysqli_num_rows($res)>0){

while($c=mysqli_fetch_assoc($res)){

$img = $c['image_path'] ?: "/carconnect/assets/images/default_car.jpg";

echo "

<div class='card'>

<img src='".e($img)."' style='height:200px;object-fit:cover'>

<div class='p'>

<div style='font-weight:800;font-size:18px'>
".e($c['make'])." ".e($c['model'])."
</div>

<div class='muted'>
Year: ".e($c['year'])."
</div>

<div style='margin-top:6px;color:#00d2ff;font-weight:700'>
₹".number_format((float)$c['price'])."
</div>

<div class='muted' style='margin-top:6px'>
Seller: ".e($c['seller'])."
</div>

<div style='margin-top:12px'>

<form method='POST' onsubmit=\"return confirm('Delete this car?')\">
<input type='hidden' name='id' value='{$c['id']}'>
<button name='action' value='delete' class='btn' style='background:#ef4444;color:white'>Delete</button>
</form>

</div>

</div>

</div>

";

}

}else{
echo "<p class='muted'>No approved cars</p>";
}

?>

</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>