<?php
require_once __DIR__ . "/../core/middleware.php";
requireRole("admin");
require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/header.php";

$total = mysqli_fetch_assoc(mysqli_query($conn,"SELECT IFNULL(SUM(total_price),0) s FROM orders"))['s'];
?>
<h1>Car Sales Analytics</h1>
<div class="card" style="max-width:520px"><div class="p">
  <div class="muted">Total Revenue</div>
  <div style="font-size:30px;font-weight:900">₹<?php echo number_format((float)$total); ?></div>
</div></div>
<?php require_once __DIR__ . "/../includes/footer.php"; ?>
