<?php
require_once "../core/middleware.php";
requireRole("buyer");

require_once "../includes/db_connect.php";
require_once "../includes/functions.php";
require_once "../includes/header.php";

$buyerId = (int)$_SESSION['user_id'];
$carId = (int)($_GET['car_id'] ?? 0);

if($_SERVER['REQUEST_METHOD']=="POST"){

$rating = (int)$_POST['rating'];
$comment = trim($_POST['comment']);

$stmt = mysqli_prepare($conn,"
INSERT INTO reviews(car_id,user_id,rating,comment)
VALUES(?,?,?,?)
");

mysqli_stmt_bind_param($stmt,"iiis",$carId,$buyerId,$rating,$comment);
mysqli_stmt_execute($stmt);

echo "<div class='alert success'>Review added ✅</div>";
}
?>

<h2>Add Review</h2>

<form method="POST" class="form">
<label>Rating (1-5)</label>
<input class="input" name="rating" type="number" min="1" max="5" required>

<label>Comment</label>
<textarea class="input" name="comment"></textarea>

<button class="btn primary">Submit</button>
</form>

<?php require_once "../includes/footer.php"; ?>