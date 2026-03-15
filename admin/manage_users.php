<?php
require_once __DIR__ . "/../core/middleware.php";
requireRole("admin");
require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/header.php";

if(isset($_GET['toggle'])){
  $id=intval($_GET['toggle']);
  mysqli_query($conn,"UPDATE users SET status = IF(status='active','inactive','active') WHERE id=$id AND role<>'admin'");
  echo '<div class="alert success">User status updated ✅</div>';
}
?>
<h1>Manage Users</h1>
<table class="table">
<tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Action</th></tr>
<?php
$res=mysqli_query($conn,"SELECT id,name,email,role,status FROM users ORDER BY created_at DESC");
while($u=mysqli_fetch_assoc($res)){
  echo "<tr>
    <td>".e($u['name'])."</td>
    <td>".e($u['email'])."</td>
    <td>".e($u['role'])."</td>
    <td>".e($u['status'])."</td>
    <td>";
  if($u['role']!=='admin'){
    echo "<a class='btn' href='/carconnect/admin/manage_users.php?toggle=".intval($u['id'])."'>Toggle</a>";
  }
  echo "</td></tr>";
}
?>
</table>
<?php require_once __DIR__ . "/../includes/footer.php"; ?>
