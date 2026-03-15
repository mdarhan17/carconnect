<?php
require_once __DIR__ . "/../core/middleware.php";
requireRole("admin");
require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/header.php";
?>
<h1>Admin Dashboard</h1>
<div style="display:flex;gap:12px;flex-wrap:wrap">
  <a class="btn primary" href="/carconnect/admin/manage_cars.php">Approve Cars</a>
  <a class="btn" href="/carconnect/admin/manage_users.php">Manage Users</a>
  <a class="btn" href="/carconnect/admin/view_reports.php">Reports</a>
</div>

<?php
$pending = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM car_listings WHERE status='pending'"))['c'];
$users = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM users"))['c'];
$orders = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM orders"))['c'];
?>
<div class="grid" style="grid-template-columns:repeat(3,1fr)">
  <div class="card"><div class="p"><div class="muted">Pending Listings</div><div style="font-size:26px;font-weight:900"><?php echo intval($pending); ?></div></div></div>
  <div class="card"><div class="p"><div class="muted">Total Users</div><div style="font-size:26px;font-weight:900"><?php echo intval($users); ?></div></div></div>
  <div class="card"><div class="p"><div class="muted">Total Orders</div><div style="font-size:26px;font-weight:900"><?php echo intval($orders); ?></div></div></div>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
