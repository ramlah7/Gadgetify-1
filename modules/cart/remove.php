<?php include "../includes/auth_check.php"; ?>
<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../user/login.php");
    exit;
}

$item_id = intval($_GET['id']);

$delete = $conn->prepare("DELETE FROM cart_items WHERE id = ?");
$delete->bind_param("i", $item_id);
$delete->execute();

header("Location: cart.php");
exit;
?>
