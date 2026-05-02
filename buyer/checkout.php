<?php
require_once __DIR__ . "/../core/middleware.php";
requireRole("buyer");

require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/header.php";

$buyerId = (int)$_SESSION['user_id'];
$carId = (int)($_GET['id'] ?? 0);

/* ===================== */
/* FETCH CAR */
/* ===================== */

$stmt = mysqli_prepare($conn,"
SELECT id,seller_id,make,model,price,status
FROM car_listings
WHERE id=? LIMIT 1
");

mysqli_stmt_bind_param($stmt,"i",$carId);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$car = mysqli_fetch_assoc($res);

if(!$car || $car['status'] !== 'approved'){
  echo '<div class="alert">Car not available.</div>';
  require_once __DIR__ . "/../includes/footer.php";
  exit();
}

/* ===================== */
/* PAYMENT SUCCESS */
/* ===================== */

if(isset($_GET['payment']) && $_GET['payment']=="done"){

  $sellerId = (int)$car['seller_id'];
  $price = (float)$car['price'];

  /* CHECK IF ALREADY SOLD */
  $check = mysqli_query($conn,"SELECT status FROM car_listings WHERE id=$carId");
  $row = mysqli_fetch_assoc($check);

  if($row['status'] == 'sold'){
    echo '<div class="alert">Car already sold.</div>';
    require_once __DIR__ . "/../includes/footer.php";
    exit();
  }

  mysqli_begin_transaction($conn);

  try {

    /* ORDER (PENDING FIRST) */
    $stmt1 = mysqli_prepare($conn,"
    INSERT INTO orders(car_id,buyer_id,seller_id,total_price,order_status)
    VALUES (?,?,?,?, 'pending')
    ");
    mysqli_stmt_bind_param($stmt1,"iiid",$carId,$buyerId,$sellerId,$price);
    mysqli_stmt_execute($stmt1);

    $orderId = mysqli_insert_id($conn);

    /* PAYMENT */
    $method = "razorpay_demo";

    $stmt2 = mysqli_prepare($conn,"
    INSERT INTO payments(order_id,payment_method,payment_status)
    VALUES (?,?, 'paid')
    ");
    mysqli_stmt_bind_param($stmt2,"is",$orderId,$method);
    mysqli_stmt_execute($stmt2);

    /* UPDATE ORDER STATUS */
    mysqli_query($conn,"UPDATE orders SET order_status='completed' WHERE id=$orderId");

    /* MARK SOLD */
    mysqli_query($conn,"UPDATE car_listings SET status='sold' WHERE id=$carId");

    mysqli_commit($conn);

    echo '
    <div class="alert success">
    🎉 Payment Successful! Car booked.
    </div>

    <a class="btn primary" href="order_history.php">
    View Orders
    </a>
    ';

    exit();

  } catch(Exception $e){
    mysqli_rollback($conn);
    echo '<div class="alert">Payment failed.</div>';
  }
}
?>

<h1>Checkout</h1>

<div class="card" style="max-width:720px;margin:auto">

<div class="p">

<div style="font-weight:800;font-size:22px">
<?php echo e($car['make'])." ".e($car['model']); ?>
</div>

<div class="muted" style="margin-top:6px">
Secure Checkout
</div>

<h2 style="color:#00d2ff;margin-top:10px">
₹<?php echo number_format((float)$car['price']); ?>
</h2>

<button class="btn primary"
onclick="openPayment()"
style="width:100%;margin-top:20px">

💳 Pay Securely

</button>

</div>
</div>

<!-- PAYMENT MODAL -->

<div id="paymentModal" style="
display:none;
position:fixed;
top:0;left:0;
width:100%;height:100%;
background:rgba(0,0,0,0.7);
justify-content:center;
align-items:center;
z-index:999;
">

<div style="
background:white;
width:360px;
padding:25px;
border-radius:14px;
text-align:center;
color:#111;
">

<h2 style="color:#0d47a1">Razorpay</h2>

<p style="font-size:14px;color:#555">
Demo Secure Payment
</p>

<hr>

<h3>₹<?php echo number_format($car['price']); ?></h3>

<input class="input"
placeholder="Enter UPI ID (demo@upi)"
style="margin-top:10px">

<button onclick="fakePay()"
class="btn primary"
style="margin-top:15px;width:100%">

Pay Now

</button>

<button onclick="closePayment()"
class="btn"
style="margin-top:10px;width:100%">

Cancel

</button>

<p style="font-size:12px;color:#777;margin-top:10px">
Test Payment Mode
</p>

</div>
</div>

<script>

function openPayment(){
document.getElementById("paymentModal").style.display="flex";
}

function closePayment(){
document.getElementById("paymentModal").style.display="none";
}

function fakePay(){

alert("Processing payment...");

setTimeout(function(){
window.location.href="checkout.php?id=<?php echo $carId; ?>&payment=done";
},1200);

}

</script>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>