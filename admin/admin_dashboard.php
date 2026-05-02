<?php
require_once __DIR__ . "/../core/middleware.php";
requireRole("admin");

require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/header.php";

/* ===================== */
/* SAFE COUNT FUNCTION */
/* ===================== */

function safeCount($conn, $sql){
    $res = mysqli_query($conn, $sql);
    if($res){
        $row = mysqli_fetch_assoc($res);
        return $row ? (int)$row['c'] : 0;
    }
    return 0;
}

/* ===================== */
/* STATS */
/* ===================== */

$pending      = safeCount($conn,"SELECT COUNT(*) c FROM car_listings WHERE status='pending'");
$approved     = safeCount($conn,"SELECT COUNT(*) c FROM car_listings WHERE status='approved'");
$sold         = safeCount($conn,"SELECT COUNT(*) c FROM car_listings WHERE status='sold'");
$totalBuyers  = safeCount($conn,"SELECT COUNT(*) c FROM buyers");
$totalSellers = safeCount($conn,"SELECT COUNT(*) c FROM sellers");
$totalOrders  = safeCount($conn,"SELECT COUNT(*) c FROM orders");
$totalCategories = safeCount($conn,"SELECT COUNT(*) c FROM car_categories");

$salesRes = mysqli_query($conn,"SELECT IFNULL(SUM(total_price),0) s FROM orders");
$totalSales = $salesRes ? mysqli_fetch_assoc($salesRes)['s'] : 0;
?>

<h1>🚀 Admin Dashboard</h1>

<?php if($pending > 0): ?>
<div class="alert">
⚠️ <?php echo intval($pending); ?> cars pending approval
</div>
<?php endif; ?>

<!-- ===================== -->
<!-- QUICK ACTIONS -->
<!-- ===================== -->

<div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:20px">

<a class="btn primary" href="/carconnect/admin/manage_cars.php">🚗 Manage Cars</a>

<a class="btn" href="/carconnect/admin/manage_categories.php">
🏷️ Categories
</a>

<a class="btn" href="/carconnect/cars.php">
🔍 Browse Cars
</a>

<a class="btn" href="/carconnect/admin/manage_users.php">👥 Users</a>

<a class="btn" href="/carconnect/admin/manage_orders.php">📦 Orders</a>

<a class="btn" href="/carconnect/admin/manage_payments.php">💳 Payments</a>

<a class="btn" href="/carconnect/admin/manage_reviews.php">⭐ Reviews</a>

<a class="btn" href="/carconnect/admin/view_messages.php">💬 Messages</a>

<a class="btn" href="/carconnect/admin/view_reports.php">📊 Reports</a>

</div>

<!-- ===================== -->
<!-- STATS CARDS -->
<!-- ===================== -->

<div class="grid" style="grid-template-columns:repeat(4,1fr);gap:20px">

<div class="card">
<div class="p">
<div class="muted">Pending Cars</div>
<div style="font-size:26px;font-weight:900;color:#f59e0b">
<?php echo $pending; ?>
</div>
</div>
</div>

<div class="card">
<div class="p">
<div class="muted">Approved Cars</div>
<div style="font-size:26px;font-weight:900;color:#16a34a">
<?php echo $approved; ?>
</div>
</div>
</div>

<div class="card">
<div class="p">
<div class="muted">Sold Cars</div>
<div style="font-size:26px;font-weight:900;color:#2563eb">
<?php echo $sold; ?>
</div>
</div>
</div>

<div class="card">
<div class="p">
<div class="muted">Total Users</div>
<div style="font-size:26px;font-weight:900">
<?php echo $totalBuyers + $totalSellers; ?>
</div>
</div>
</div>

<div class="card">
<div class="p">
<div class="muted">Total Buyers</div>
<div style="font-size:26px;font-weight:900;color:#06b6d4">
<?php echo $totalBuyers; ?>
</div>
</div>
</div>

<div class="card">
<div class="p">
<div class="muted">Total Sellers</div>
<div style="font-size:26px;font-weight:900;color:#8b5cf6">
<?php echo $totalSellers; ?>
</div>
</div>
</div>

<div class="card">
<div class="p">
<div class="muted">Orders</div>
<div style="font-size:26px;font-weight:900;color:#ef4444">
<?php echo $totalOrders; ?>
</div>
</div>
</div>

<div class="card">
<div class="p">
<div class="muted">Revenue</div>
<div style="font-size:26px;font-weight:900;color:#06b6d4">
₹<?php echo number_format((float)$totalSales); ?>
</div>
</div>
</div>

<!-- ✅ NEW CATEGORY CARD -->
<div class="card">
<div class="p">
<div class="muted">Categories</div>
<div style="font-size:26px;font-weight:900;color:#f97316">
<?php echo $totalCategories; ?>
</div>
</div>
</div>

</div>

<!-- ===================== -->
<!-- TOP SELLING CARS -->
<!-- ===================== -->

<h2 style="margin-top:30px">🔥 Top Selling Cars</h2>

<table class="table">
<tr>
<th>Car</th>
<th>Orders</th>
</tr>

<?php
$top = mysqli_query($conn,"
SELECT c.make,c.model,COUNT(*) cnt
FROM orders o
JOIN car_listings c ON c.id=o.car_id
GROUP BY o.car_id,c.make,c.model
ORDER BY cnt DESC
LIMIT 5
");

if($top && mysqli_num_rows($top)>0){
while($t=mysqli_fetch_assoc($top)){
echo "<tr>
<td>".e($t['make'])." ".e($t['model'])."</td>
<td>".$t['cnt']."</td>
</tr>";
}
}else{
echo "<tr><td colspan='2'>No data</td></tr>";
}
?>
</table>

<!-- ===================== -->
<!-- RECENT ORDERS -->
<!-- ===================== -->

<h2 style="margin-top:30px">📦 Recent Orders</h2>

<table class="table">

<tr>
<th>ID</th>
<th>Car</th>
<th>Buyer</th>
<th>Status</th>
<th>Price</th>
</tr>

<?php
$res = mysqli_query($conn,"
SELECT o.id,o.total_price,o.order_status,c.make,c.model,b.name buyer
FROM orders o
JOIN car_listings c ON c.id=o.car_id
JOIN buyers b ON b.id=o.buyer_id
ORDER BY o.id DESC
LIMIT 5
");

if($res && mysqli_num_rows($res)>0){
while($o=mysqli_fetch_assoc($res)){

$color="#6b7280";
if($o['order_status']=="completed") $color="#16a34a";
if($o['order_status']=="pending") $color="#f59e0b";
if($o['order_status']=="cancelled") $color="#ef4444";

echo "<tr>
<td>#".$o['id']."</td>
<td>".e($o['make'])." ".e($o['model'])."</td>
<td>".e($o['buyer'])."</td>
<td><span style='background:$color;color:#fff;padding:5px 12px;border-radius:20px'>".$o['order_status']."</span></td>
<td>₹".number_format($o['total_price'])."</td>
</tr>";
}
}else{
echo "<tr><td colspan='5'>No orders</td></tr>";
}
?>

</table>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>