<?php
require_once __DIR__ . "/../core/middleware.php";
requireRole("admin");

require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/header.php";

/* ===================== */
/* TOTAL REVENUE */
/* ===================== */

$total = mysqli_fetch_assoc(
  mysqli_query($conn,"SELECT IFNULL(SUM(total_price),0) s FROM orders")
)['s'];

$totalOrders = mysqli_fetch_assoc(
  mysqli_query($conn,"SELECT COUNT(*) c FROM orders")
)['c'];

/* ===================== */
/* MONTHLY DATA */
/* ===================== */

$data = mysqli_query($conn,"
SELECT 
DATE_FORMAT(created_at,'%Y-%m') AS month,
SUM(total_price) AS total
FROM orders
GROUP BY month
ORDER BY month ASC
");

$months = [];
$sales = [];

while($row=mysqli_fetch_assoc($data)){
  $months[] = $row['month'];
  $sales[] = $row['total'];
}
?>

<h1>📊 Car Sales Analytics</h1>

<!-- STATS -->

<div class="grid" style="grid-template-columns:repeat(2,1fr);gap:20px">

<div class="card">
<div class="p">
<div class="muted">Total Revenue</div>
<div style="font-size:28px;font-weight:900;color:#00d2ff">
₹<?php echo number_format((float)$total); ?>
</div>
</div>
</div>

<div class="card">
<div class="p">
<div class="muted">Total Orders</div>
<div style="font-size:28px;font-weight:900">
<?php echo $totalOrders; ?>
</div>
</div>
</div>

</div>

<!-- CHART -->

<h2 style="margin-top:30px">Monthly Sales</h2>

<canvas id="salesChart" height="100"></canvas>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const ctx = document.getElementById('salesChart');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($months); ?>,
        datasets: [{
            label: 'Sales (₹)',
            data: <?php echo json_encode($sales); ?>,
            borderWidth: 2,
            tension: 0.3
        }]
    },
    options: {
        responsive: true
    }
});
</script>

<!-- RECENT SALES -->

<h2 style="margin-top:30px">Recent Sales</h2>

<table class="table">

<tr>
<th>Order ID</th>
<th>Amount</th>
<th>Date</th>
</tr>

<?php
$res = mysqli_query($conn,"
SELECT id,total_price,created_at
FROM orders
ORDER BY created_at DESC
LIMIT 5
");

if($res && mysqli_num_rows($res)>0){

while($o=mysqli_fetch_assoc($res)){

echo "
<tr>
<td>#{$o['id']}</td>
<td>₹".number_format($o['total_price'])."</td>
<td>".date("d M Y",strtotime($o['created_at']))."</td>
</tr>
";

}

}else{
echo "<tr><td colspan='3'>No sales yet</td></tr>";
}
?>

</table>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>