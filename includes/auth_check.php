<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: /Gadgetify/modules/auth/login.php");
    exit;
}
?>
