<?php
require_once __DIR__ . "/../core/middleware.php";
requireRole("admin");
require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/header.php";

$buyers = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM users WHERE role='buyer'"))['c'];
$sellers = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM users WHERE role='seller'"))['c'];
?>
<h1>User Activity Analytics</h1>
<div class="grid" style="grid-template-columns:repeat(2,1fr)">
  <div class="card"><div class="p"><div class="muted">Buyers</div><div style="font-size:28px;font-weight:900"><?php echo intval($buyers); ?></div></div></div>
  <div class="card"><div class="p"><div class="muted">Sellers</div><div style="font-size:28px;font-weight:900"><?php echo intval($sellers); ?></div></div></div>
</div>
<?php require_once __DIR__ . "/../includes/footer.php"; ?>
