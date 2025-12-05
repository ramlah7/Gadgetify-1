<?php include "../../includes/auth_check.php"; ?>
<?php
require_once '../../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Validate input
if (!isset($_GET['id'])) {
    $_SESSION['error_message'] = "Product ID not provided.";
    header("Location: ../../pages/shop.php");
    exit;
}

$product_id = intval($_GET['id']);
$quantity = isset($_GET['qty']) ? intval($_GET['qty']) : 1;

// Validate quantity
if ($quantity <= 0) {
    $quantity = 1;
}

// Verify product exists
$product_check = $conn->prepare("SELECT id FROM products WHERE id = ?");
$product_check->bind_param("i", $product_id);
$product_check->execute();
$product_check->store_result();

if ($product_check->num_rows == 0) {
    $_SESSION['error_message'] = "Product not found.";
    $product_check->close();
    header("Location: ../../pages/shop.php");
    exit;
}
$product_check->close();

// --- STEP 1: Check/Create Cart ---
$cart_sql = "SELECT id FROM cart WHERE user_id = ?";
$stmt = $conn->prepare($cart_sql);
if (!$stmt) { die("Prepare Cart SELECT failed: " . $conn->error); }
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 0) {
    // Create cart
    $create = $conn->prepare("INSERT INTO cart (user_id) VALUES (?)");
    if (!$create) { die("Prepare Cart INSERT failed: " . $conn->error); }
    $create->bind_param("i", $user_id);
    if (!$create->execute()) {
        die("CART INSERT FAILED: " . $create->error);
    }
    $cart_id = $create->insert_id;
    $create->close();
} else {
    $stmt->bind_result($cart_id);
    $stmt->fetch();
    $stmt->close();
}

// --- STEP 2: Check Cart Items ---
$check_item = $conn->prepare("SELECT id, quantity FROM cart_items WHERE cart_id = ? AND product_id = ?");
if (!$check_item) { die("Prepare Cart Items SELECT failed: " . $conn->error); }
$check_item->bind_param("ii", $cart_id, $product_id);
$check_item->execute();
$check_item->store_result();

if ($check_item->num_rows > 0) {
    $check_item->bind_result($item_id, $old_qty);
    $check_item->fetch();

    $new_qty = $old_qty + $quantity;

    $update = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
    if (!$update) { die("Prepare Cart Items UPDATE failed: " . $conn->error); }
    $update->bind_param("ii", $new_qty, $item_id);
    if (!$update->execute()) {
        die("CART ITEMS UPDATE FAILED: " . $update->error);
    }
    $update->close(); 

} else {
    $check_item->close();
    
    // Insert new item
    $insert = $conn->prepare("INSERT INTO cart_items (cart_id, product_id, quantity) VALUES (?, ?, ?)");
    if (!$insert) { die("Prepare Cart Items INSERT failed: " . $conn->error); }
    $insert->bind_param("iii", $cart_id, $product_id, $quantity);
    if (!$insert->execute()) {
        die("CART ITEMS INSERT FAILED: " . $insert->error);
    }
    $insert->close();
}

// Update cart item count in session
$count_stmt = $conn->prepare("SELECT SUM(quantity) as total FROM cart_items WHERE cart_id = ?");
$count_stmt->bind_param("i", $cart_id);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$count_row = $count_result->fetch_assoc();
$_SESSION['cart_item_count'] = $count_row['total'] ?? 0;
$count_stmt->close();

// Set success message
$_SESSION['success_message'] = "Item added to cart!";

// Redirect back to cart
header("Location: cart.php");
exit;
?>