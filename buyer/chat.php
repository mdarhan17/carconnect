<?php
require_once "../core/middleware.php";
requireRole("buyer");

require_once "../includes/db_connect.php";
require_once "../includes/header.php";

$buyer = $_SESSION['user_id'];
$seller = intval($_GET['seller'] ?? 0);
$car = intval($_GET['car_id'] ?? 0);

/* SEND MESSAGE */

if($_SERVER['REQUEST_METHOD']=="POST"){

$msg = mysqli_real_escape_string($conn,$_POST['message']);

mysqli_query($conn,"
INSERT INTO messages(car_id,sender_id,receiver_id,message)
VALUES($car,$buyer,$seller,'$msg')
");

}
?>

<h2>Chat with Seller</h2>

<div class="card p" style="height:350px;overflow:auto">

<?php

$res=mysqli_query($conn,"
SELECT * FROM messages
WHERE car_id=$car
AND (
(sender_id=$buyer AND receiver_id=$seller)
OR
(sender_id=$seller AND receiver_id=$buyer)
)
ORDER BY created_at ASC
");

while($m=mysqli_fetch_assoc($res)){

if($m['sender_id']==$buyer){

echo "
<div style='text-align:right;margin-bottom:8px'>
<span class='btn primary'>".$m['message']."</span>
</div>
";

}else{

echo "
<div style='text-align:left;margin-bottom:8px'>
<span class='btn'>".$m['message']."</span>
</div>
";

}

}

?>

</div>

<form method="POST" style="margin-top:10px;display:flex;gap:8px">

<input class="input" name="message" placeholder="Type your message..." required>

<button class="btn primary">Send</button>

</form>

<?php require_once "../includes/footer.php"; ?>