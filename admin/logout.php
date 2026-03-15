<?php
session_start();
session_destroy();
header("Location: /carconnect/index.php");
exit();
?>
