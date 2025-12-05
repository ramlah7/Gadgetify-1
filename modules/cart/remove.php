<?php include "../../includes/auth_check.php"; ?>
<?php
require_once __DIR__ . '/../../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$item_id = intval($_GET['id']);

if ($item_id <= 0) {
    $_SESSION['error_message'] = "Invalid item ID.";
    header("Location: cart.php");
    exit;
}

$delete = $conn->prepare("DELETE FROM cart_items WHERE id = ?");
$delete->bind_param("i", $item_id);

if (!$delete->execute()) {
    $_SESSION['error_message'] = "Failed to remove item from cart.";
} else {
    $_SESSION['success_message'] = "Item removed from cart!";
}

$delete->close();

header("Location: cart.php");
exit;
