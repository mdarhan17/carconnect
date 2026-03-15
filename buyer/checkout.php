<?php
require_once __DIR__ . "/../core/middleware.php";
requireRole("buyer");
require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/header.php";

$buyerId = intval($_SESSION['user_id']);
$carId = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = mysqli_prepare($conn, "SELECT id,seller_id,make,model,price,status FROM car_listings WHERE id=? AND status='approved' LIMIT 1");
mysqli_stmt_bind_param($stmt,"i",$carId);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$car = mysqli_fetch_assoc($res);

if(!$car){
  echo '<div class="alert">Car not available.</div>';
  require_once __DIR__ . "/../includes/footer.php";
  exit();
}

$err = "";
if($_SERVER['REQUEST_METHOD']==='POST'){
  // Create Order (payment gateway later)
  $sellerId = intval($car['seller_id']);
  $price = (float)$car['price'];

  mysqli_begin_transaction($conn);
  try {
    $stmt1 = mysqli_prepare($conn, "INSERT INTO orders(car_id,buyer_id,seller_id,total_price,order_status) VALUES (?,?,?,?, 'pending')");
    mysqli_stmt_bind_param($stmt1,"iiid",$carId,$buyerId,$sellerId,$price);
    mysqli_stmt_execute($stmt1);
    $orderId = mysqli_insert_id($conn);

    $method = "cod";
    $stmt2 = mysqli_prepare($conn, "INSERT INTO payments(order_id,payment_method,payment_status) VALUES (?,?, 'pending')");
    mysqli_stmt_bind_param($stmt2,"is",$orderId,$method);
    mysqli_stmt_execute($stmt2);

    // Mark car as sold (optional: after payment success, but for demo we'll mark sold)
    $stmt3 = mysqli_prepare($conn, "UPDATE car_listings SET status='sold' WHERE id=?");
    mysqli_stmt_bind_param($stmt3,"i",$carId);
    mysqli_stmt_execute($stmt3);

    mysqli_commit($conn);
    echo '<div class="alert success">Order created ✅ (Payment gateway next)</div>';
  } catch(Exception $e){
    mysqli_rollback($conn);
    $err = "Checkout failed.";
  }
}
?>
<h1>Checkout</h1>
<div class="card" style="max-width:720px">
  <div class="p">
    <div style="font-weight:800"><?php echo e($car['make'])." ".e($car['model']); ?></div>
    <div class="muted">Total: ₹<?php echo number_format((float)$car['price']); ?></div>

    <?php if($err) echo '<div class="alert">'.$err.'</div>'; ?>

    <form method="POST" class="form" style="max-width:none;margin-top:12px">
      <label>Payment Method</label>
      <select class="input" name="payment_method">
        <option value="cod">Cash on Delivery (Demo)</option>
      </select>
      <div style="margin-top:12px">
        <button class="btn primary">Confirm Order</button>
      </div>
    </form>
  </div>
</div>
<?php require_once __DIR__ . "/../includes/footer.php"; ?>
