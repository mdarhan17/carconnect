<?php
require_once __DIR__ . "/../core/middleware.php";
requireRole("admin");
require_once __DIR__ . "/../includes/header.php";
?>
<h1>Car Views Analytics</h1>
<div class="alert">Optional module: add car_views table if you want view tracking.</div>
<?php require_once __DIR__ . "/../includes/footer.php"; ?>
