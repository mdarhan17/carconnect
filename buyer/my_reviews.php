<?php
require_once "../core/middleware.php";
requireRole("buyer");

require_once "../includes/db_connect.php";
require_once "../includes/functions.php";
require_once "../includes/header.php";

$buyerId = (int)$_SESSION['user_id'];

$res = mysqli_query($conn,"
SELECT r.*,c.make,c.model
FROM reviews r
JOIN car_listings c ON c.id=r.car_id
WHERE r.user_id=$buyerId
ORDER BY r.created_at DESC
");
?>

<h1>My Reviews</h1>

<table class="table">
<tr><th>Car</th><th>Rating</th><th>Comment</th></tr>

<?php while($r=mysqli_fetch_assoc($res)): ?>

<tr>
<td><?php echo e($r['make']." ".$r['model']); ?></td>
<td><?php echo $r['rating']; ?> ⭐</td>
<td><?php echo e($r['comment']); ?></td>
</tr>

<?php endwhile; ?>

</table>

<?php require_once "../includes/footer.php"; ?>