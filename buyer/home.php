<?php
require_once __DIR__ . "/../core/middleware.php";
requireRole("buyer");
require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/header.php";
?>
<h1>Buyer Dashboard</h1>
<p class="muted">Welcome! Browse cars and manage wishlist/orders.</p>
<div style="display:flex;gap:12px;flex-wrap:wrap">
  <a class="btn primary" href="/carconnect/buyer/car_listings.php">Browse Cars</a>
  <a class="btn" href="/carconnect/buyer/wishlist.php">Wishlist</a>
  <a class="btn" href="/carconnect/buyer/order_history.php">Orders</a>
</div>
<?php require_once __DIR__ . "/../includes/footer.php"; ?>
