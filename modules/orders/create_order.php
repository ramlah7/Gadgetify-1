<?php include "../../includes/auth_check.php"; ?>
<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../modules/auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    $conn->begin_transaction();
    
    // 1. Get the user's current Cart ID
    $cart_stmt = $conn->prepare("SELECT id FROM cart WHERE user_id = ?");
    $cart_stmt->bind_param("i", $user_id);
    $cart_stmt->execute();
    $cart_result = $cart_stmt->get_result();
    
    if ($cart_result->num_rows === 0) {
        throw new Exception("Cart not found for user. Please add items to cart first.");
    }
    
    $cart_row = $cart_result->fetch_assoc();
    $cart_id = $cart_row['id'];
    $cart_stmt->close();

    // 2. Fetch all cart items with product details
    $items_sql = "SELECT ci.id AS cart_item_id, ci.product_id, ci.quantity, p.price, p.stock 
                  FROM cart_items ci
                  JOIN products p ON ci.product_id = p.id
                  WHERE ci.cart_id = ?";
    $items_stmt = $conn->prepare($items_sql);
    $items_stmt->bind_param("i", $cart_id);
    $items_stmt->execute();
    $items_result = $items_stmt->get_result();
    $cart_items = $items_result->fetch_all(MYSQLI_ASSOC);
    $items_stmt->close();

    if (empty($cart_items)) {
        throw new Exception("Your cart is empty. Please add items before checking out.");
    }

    $total_amount = 0;
    
    // Check stock and calculate total
    foreach ($cart_items as $item) {
        if ($item['stock'] < $item['quantity']) {
            throw new Exception("Not enough stock for product ID: " . $item['product_id'] . ". Available: " . $item['stock']);
        }
        $total_amount += $item['price'] * $item['quantity'];
    }

    // 3. Create the new Order record
    $order_sql = "INSERT INTO orders (user_id, total_amount, status, payment_status) VALUES (?, ?, 'pending', 'unpaid')";
    $order_stmt = $conn->prepare($order_sql);
    $order_stmt->bind_param("id", $user_id, $total_amount);
    
    if (!$order_stmt->execute()) {
        throw new Exception("Failed to create order: " . $order_stmt->error);
    }
    
    $order_id = $order_stmt->insert_id;
    $order_stmt->close();

    // 4. Move Cart Items to Order Items and update product stock
    foreach ($cart_items as $item) {
        // Insert order item
        $order_item_sql = "INSERT INTO order_items (order_id, product_id, price, quantity) VALUES (?, ?, ?, ?)";
        $order_item_stmt = $conn->prepare($order_item_sql);
        $order_item_stmt->bind_param("iidi", $order_id, $item['product_id'], $item['price'], $item['quantity']);
        
        if (!$order_item_stmt->execute()) {
            throw new Exception("Failed to add order item: " . $order_item_stmt->error);
        }
        $order_item_stmt->close();

        // Update product stock
        $stock_update = "UPDATE products SET stock = stock - ? WHERE id = ?";
        $stock_stmt = $conn->prepare($stock_update);
        $stock_stmt->bind_param("ii", $item['quantity'], $item['product_id']);
        
        if (!$stock_stmt->execute()) {
            throw new Exception("Failed to update stock: " . $stock_stmt->error);
        }
        $stock_stmt->close();
    }

    // 5. Clear the Cart Items
    $clear_cart = "DELETE FROM cart_items WHERE cart_id = ?";
    $clear_stmt = $conn->prepare($clear_cart);
    $clear_stmt->bind_param("i", $cart_id);
    
    if (!$clear_stmt->execute()) {
        throw new Exception("Failed to clear cart: " . $clear_stmt->error);
    }
    $clear_stmt->close();

    // Commit the transaction
    $conn->commit();
    
    // Redirect to order history
    $_SESSION['success_message'] = "Order created successfully!";
    header("Location: order_history.php");
    exit;

} catch (Exception $e) {
    // An error occurred, rollback the transaction
    $conn->rollback();
    $_SESSION['error_message'] = "Order creation failed: " . $e->getMessage();
    header("Location: ../cart/cart.php");
    exit;
}