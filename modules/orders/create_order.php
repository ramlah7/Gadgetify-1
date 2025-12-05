<?php include "../includes/auth_check.php"; ?>
<?php
require_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$conn->begin_transaction(); // Start transaction for atomicity

try {
    // 1. Get the user's current Cart ID
    $cart_stmt = $conn->prepare("SELECT id FROM cart WHERE user_id = ?");
    $cart_stmt->bind_param("i", $user_id);
    $cart_stmt->execute();
    $cart_result = $cart_stmt->get_result();
    
    if ($cart_result->num_rows === 0) {
        throw new Exception("Cart is empty or not found.");
    }
    $cart_row = $cart_result->fetch_assoc();
    $cart_id = $cart_row['id'];
    $cart_stmt->close();

    // 2. Fetch all cart items with product details and lock the products for update
    // Lock prevents other users from simultaneously buying the last stock.
    $items_sql = "SELECT ci.product_id, ci.quantity, p.price, p.stock 
                  FROM cart_items ci
                  JOIN products p ON ci.product_id = p.id
                  WHERE ci.cart_id = ? FOR UPDATE"; // Lock rows in products table
    $items_stmt = $conn->prepare($items_sql);
    $items_stmt->bind_param("i", $cart_id);
    $items_stmt->execute();
    $cart_items = $items_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $items_stmt->close();

    if (empty($cart_items)) {
        throw new Exception("Cart is empty.");
    }

    $total_amount = 0;
    
    // Check stock and calculate total
    foreach ($cart_items as $item) {
        if ($item['stock'] < $item['quantity']) {
            throw new Exception("Not enough stock for product ID: " . $item['product_id']);
        }
        $total_amount += $item['price'] * $item['quantity'];
    }

    // 3. Create the new Order record
    $order_sql = "INSERT INTO orders (user_id, total_amount, status, payment_status) 
                  VALUES (?, ?, 'pending', 'unpaid')";
    $order_stmt = $conn->prepare($order_sql);
    $order_stmt->bind_param("id", $user_id, $total_amount);
    $order_stmt->execute();
    $order_id = $conn->insert_id;
    $order_stmt->close();

    // 4. Move Cart Items to Order Items and update product stock
    $insert_item_sql = "INSERT INTO order_items (order_id, product_id, price, quantity) VALUES (?, ?, ?, ?)";
    $update_stock_sql = "UPDATE products SET stock = stock - ? WHERE id = ?";
    
    foreach ($cart_items as $item) {
        // Insert into order_items
        $item_stmt = $conn->prepare($insert_item_sql);
        $item_stmt->bind_param("iidi", $order_id, $item['product_id'], $item['price'], $item['quantity']);
        $item_stmt->execute();
        $item_stmt->close();

        // Update product stock
        $stock_stmt = $conn->prepare($update_stock_sql);
        $stock_stmt->bind_param("ii", $item['quantity'], $item['product_id']);
        $stock_stmt->execute();
        $stock_stmt->close();
    }

    // 5. Clear the Cart Items (the main cart row remains for the user)
    $clear_cart_stmt = $conn->prepare("DELETE FROM cart_items WHERE cart_id = ?");
    $clear_cart_stmt->bind_param("i", $cart_id);
    $clear_cart_stmt->execute();
    $clear_cart_stmt->close();
    
    // 6. Update cart count session variable
    $_SESSION['cart_item_count'] = 0;

    // Commit the transaction
    $conn->commit();
    
    // Redirect to the new order details or success page
    header("Location: order_history.php?order_id=" . $order_id);
    exit;

} catch (Exception $e) {
    // An error occurred, rollback the transaction
    $conn->rollback();
    // In a real application, you'd log the error and display a user-friendly message
    $_SESSION['error_message'] = "Order creation failed: " . $e->getMessage();
    header("Location: ../cart/cart.php");
    exit;
}

// Ensure proper closure if not redirected
$conn->close();