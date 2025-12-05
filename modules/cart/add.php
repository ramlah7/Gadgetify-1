<?php include "../includes/auth_check.php"; ?>
<?php
session_start();


include '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = intval($_GET['id']);
$quantity = isset($_GET['qty']) ? intval($_GET['qty']) : 1;

// 1. Check if the user already has a cart
$cart_sql = "SELECT id FROM cart WHERE user_id = ?";
$stmt = $conn->prepare($cart_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 0) {
    // Create cart
    $create = $conn->prepare("INSERT INTO cart (user_id) VALUES (?)");
    $create->bind_param("i", $user_id);
    $create->execute();
    $cart_id = $create->insert_id;
} else {
    $stmt->bind_result($cart_id);
    $stmt->fetch();
}

// 2. Check if product already exists in cart_items
$check_item = $conn->prepare("SELECT id, quantity FROM cart_items WHERE cart_id = ? AND product_id = ?");
$check_item->bind_param("ii", $cart_id, $product_id);
$check_item->execute();
$check_item->store_result();

if ($check_item->num_rows > 0) {
    // Update quantity
    $check_item->bind_result($item_id, $old_qty);
    $check_item->fetch();

    $new_qty = $old_qty + $quantity;

    $update = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
    $update->bind_param("ii", $new_qty, $item_id);
    $update->execute();
} else {
    // Insert new item
    $insert = $conn->prepare("INSERT INTO cart_items (cart_id, product_id, quantity) VALUES (?, ?, ?)");
    $insert->bind_param("iii", $cart_id, $product_id, $quantity);
    $insert->execute();
}

// Redirect back to cart
header("Location: cart.php?added=1");
exit;
?>
