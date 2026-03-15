<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: /carconnect/index.php");
        exit();
    }
}

function requireRole($role) {
    requireLogin();
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $role) {
        header("Location: /carconnect/index.php");
        exit();
    }
}

function redirectByRole($role) {
    if ($role === 'admin') header("Location: /carconnect/admin/admin_dashboard.php");
    if ($role === 'buyer') header("Location: /carconnect/buyer/home.php");
    if ($role === 'seller') header("Location: /carconnect/seller/seller_dashboard.php");
    exit();
}
?>
