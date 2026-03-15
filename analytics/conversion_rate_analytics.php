<?php
require_once __DIR__ . "/../core/middleware.php";
requireRole("admin");
require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/header.php";

$views = 0; // add car_views table later
$sales = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM orders"))['c'];
$rate = $views>0 ? ($sales/$views)*100 : 0;
?>
<h1>Conversion Analytics</h1>
<div class="card" style="max-width:520px"><div class="p">
  <div class="muted">Orders</div>
  <div style="font-size:28px;font-weight:900"><?php echo intval($sales); ?></div>
  <div class="muted" style="margin-top:8px">Conversion rate needs car_views tracking.</div>
</div></div>
<?php require_once __DIR__ . "/../includes/footer.php"; ?>
