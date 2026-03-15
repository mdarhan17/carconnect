<?php
require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../core/middleware.php";
require_once __DIR__ . "/../includes/header.php";

if (!empty($_SESSION['role'])) redirect("/carconnect/index.php");

$err = "";
if($_SERVER['REQUEST_METHOD']==='POST'){
  $email = post('email');
  $pass  = post('password');

  $stmt = mysqli_prepare($conn, "SELECT id,password,role FROM users WHERE email=? AND role='seller' AND status='active' LIMIT 1");
  mysqli_stmt_bind_param($stmt,"s",$email);
  mysqli_stmt_execute($stmt);
  $res = mysqli_stmt_get_result($stmt);
  $u = mysqli_fetch_assoc($res);

  if($u && password_verify($pass, $u['password'])){
    $_SESSION['user_id'] = $u['id'];
    $_SESSION['role'] = $u['role'];
    redirectByRole($u['role']);
  } else {
    $err = "Invalid login.";
  }
}
?>
<h1>Seller Login</h1>
<?php if($err) echo '<div class="alert">'.$err.'</div>'; ?>
<form class="form" method="POST" data-validate>
  <label>Email</label><input class="input" type="email" name="email" required>
  <label>Password</label><input class="input" type="password" name="password" required>
  <div style="margin-top:12px"><button class="btn primary">Login</button></div>
  <p class="muted" style="margin-top:10px">New? <a href="/carconnect/seller/register.php">Register</a></p>
</form>
<?php require_once __DIR__ . "/../includes/footer.php"; ?>
