<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Online Car Connect</title>
  <link rel="stylesheet" href="/carconnect/assets/css/style.css">
  <link rel="stylesheet" href="/carconnect/assets/css/responsive.css">
</head>
<body>
<header class="nav">
  <div class="nav__inner">
    <a class="brand" href="/carconnect/index.php">Online Car Connect</a>

    <nav class="nav__links">
      <a href="/carconnect/index.php">Home</a>
      <a href="/carconnect/about.php">About</a>
      <a href="/carconnect/contact.php">Contact</a>
      <a href="/carconnect/buyer/car_listings.php">Browse Cars</a>

      <?php if (!empty($_SESSION['role'])): ?>
        <?php if ($_SESSION['role'] === 'buyer'): ?>
          <a href="/carconnect/buyer/home.php">Dashboard</a>
          <a href="/carconnect/buyer/logout.php">Logout</a>
        <?php elseif ($_SESSION['role'] === 'seller'): ?>
          <a href="/carconnect/seller/seller_dashboard.php">Dashboard</a>
          <a href="/carconnect/seller/logout.php">Logout</a>
        <?php elseif ($_SESSION['role'] === 'admin'): ?>
          <a href="/carconnect/admin/admin_dashboard.php">Admin</a>
          <a href="/carconnect/admin/logout.php">Logout</a>
        <?php endif; ?>
      <?php else: ?>
        <a href="/carconnect/buyer/login.php">Buyer</a>
        <a href="/carconnect/seller/login.php">Seller</a>
        <a href="/carconnect/admin/login.php">Admin</a>
      <?php endif; ?>
    </nav>
  </div>
</header>

<main class="container">
