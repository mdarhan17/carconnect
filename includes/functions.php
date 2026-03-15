<?php
function e($str) { return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8'); }

function post($key, $default = "") {
    return isset($_POST[$key]) ? trim((string)$_POST[$key]) : $default;
}

function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function redirect($path) {
    header("Location: " . $path);
    exit();
}

function require_post() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit("Method Not Allowed");
    }
}
?>
