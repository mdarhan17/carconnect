<?php
require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/header.php";

if (!empty($_SESSION['role'])) redirect("/carconnect/index.php");

$err = "";
if($_SERVER['REQUEST_METHOD']==='POST'){
  $name = post('name');
  $email = post('email');
  $pass = post('password');

  if(!$name || !$pass || !is_valid_email($email)){
    $err = "Valid details daalo.";
  } else {
    $hash = password_hash($pass, PASSWORD_DEFAULT);
    $role = "seller";
    $stmt = mysqli_prepare($conn, "INSERT INTO users(name,email,password,role) VALUES (?,?,?,?)");
    mysqli_stmt_bind_param($stmt,"ssss",$name,$email,$hash,$role);
    if(!mysqli_stmt_execute($stmt)){
      $err = "Email already registered ho sakta hai.";
    } else {
      redirect("/carconnect/seller/login.php");
    }
  }
}
?>
<h1>Seller Register</h1>
<?php if($err) echo '<div class="alert">'.$err.'</div>'; ?>
<form class="form" method="POST" data-validate>
  <label>Name</label><input class="input" name="name" required>
  <label>Email</label><input class="input" type="email" name="email" required>
  <label>Password</label><input class="input" type="password" name="password" required>
  <div style="margin-top:12px"><button class="btn primary">Create Seller Account</button></div>
  <p class="muted" style="margin-top:10px">Already? <a href="/carconnect/seller/login.php">Login</a></p>
</form>
<?php require_once __DIR__ . "/../includes/footer.php"; ?>
