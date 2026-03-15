<?php
require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/header.php";

$err = "";
$success = "";

// OPTIONAL SECURITY KEY (so random log admin na bana sake)
$admin_secret_key = "ADMIN123";  // change this after first use

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = post('name');
    $email = post('email');
    $password = post('password');
    $secret = post('secret_key');

    if (!$name || !$password || !is_valid_email($email)) {
        $err = "Please fill all fields correctly.";
    }
    elseif ($secret !== $admin_secret_key) {
        $err = "Invalid Admin Secret Key!";
    }
    else {

        $check = mysqli_prepare($conn, "SELECT id FROM users WHERE email=? LIMIT 1");
        mysqli_stmt_bind_param($check, "s", $email);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {
            $err = "Email already exists.";
        } else {

            $hash = password_hash($password, PASSWORD_DEFAULT);
            $role = "admin";
            $status = "active";

            $stmt = mysqli_prepare($conn, "INSERT INTO users(name,email,password,role,status) VALUES (?,?,?,?,?)");
            mysqli_stmt_bind_param($stmt, "sssss", $name, $email, $hash, $role, $status);

            if (mysqli_stmt_execute($stmt)) {
                $success = "Admin registered successfully! You can login now.";
            } else {
                $err = "Registration failed.";
            }
        }
    }
}
?>

<h1>Admin Registration</h1>

<?php if ($err) echo '<div class="alert">'.$err.'</div>'; ?>
<?php if ($success) echo '<div class="alert success">'.$success.'</div>'; ?>

<form class="form" method="POST">

    <label>Name</label>
    <input class="input" type="text" name="name" required>

    <label>Email</label>
    <input class="input" type="email" name="email" required>

    <label>Password</label>
    <input class="input" type="password" name="password" required>

    <label>Admin Secret Key</label>
    <input class="input" type="text" name="secret_key" required>

    <div style="margin-top:15px;">
        <button class="btn primary">Register Admin</button>
    </div>

    <p class="muted" style="margin-top:10px;">
        Already Admin? <a href="/carconnect/admin/login.php">Login</a>
    </p>

</form>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>