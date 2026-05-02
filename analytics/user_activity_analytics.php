<?php
require_once __DIR__ . "/../core/middleware.php";
requireRole("admin");

require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/header.php";

/* ===================== */
/* TOTAL USERS */
/* ===================== */

$buyers = mysqli_fetch_assoc(
  mysqli_query($conn,"SELECT COUNT(*) c FROM buyers")
)['c'];

$sellers = mysqli_fetch_assoc(
  mysqli_query($conn,"SELECT COUNT(*) c FROM sellers")
)['c'];

/* ===================== */
/* MONTHLY GROWTH */
/* ===================== */

$data = mysqli_query($conn,"
SELECT DATE_FORMAT(created_at,'%Y-%m') month,
(SELECT COUNT(*) FROM buyers b WHERE DATE_FORMAT(b.created_at,'%Y-%m')=month) buyers,
(SELECT COUNT(*) FROM sellers s WHERE DATE_FORMAT(s.created_at,'%Y-%m')=month) sellers
FROM buyers
GROUP BY month
ORDER BY month ASC
");

$months = [];
$buyerData = [];
$sellerData = [];

while($row=mysqli_fetch_assoc($data)){
  $months[] = $row['month'];
  $buyerData[] = $row['buyers'];
  $sellerData[] = $row['sellers'];
}
?>

<h1>👥 User Activity Analytics</h1>

<!-- ===================== -->
<!-- STATS -->
<!-- ===================== -->

<div class="grid" style="grid-template-columns:repeat(2,1fr);gap:20px">

<div class="card">
<div class="p">
<div class="muted">Total Buyers</div>
<div style="font-size:28px;font-weight:900;color:#00d2ff">
<?php echo $buyers; ?>
</div>
</div>
</div>

<div class="card">
<div class="p">
<div class="muted">Total Sellers</div>
<div style="font-size:28px;font-weight:900;color:#4caf50">
<?php echo $sellers; ?>
</div>
</div>
</div>

</div>

<!-- ===================== -->
<!-- CHART -->
<!-- ===================== -->

<h2 style="margin-top:30px">User Growth</h2>

<canvas id="userChart"></canvas>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
new Chart(document.getElementById('userChart'), {
    type: 'line',
    data: {
        labels: <?php echo json_encode($months); ?>,
        datasets: [
        {
            label: 'Buyers',
            data: <?php echo json_encode($buyerData); ?>,
            borderWidth: 2,
            tension: 0.3
        },
        {
            label: 'Sellers',
            data: <?php echo json_encode($sellerData); ?>,
            borderWidth: 2,
            tension: 0.3
        }
        ]
    }
});
</script>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>