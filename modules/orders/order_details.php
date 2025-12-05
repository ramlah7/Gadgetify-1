<?php include "../../includes/auth_check.php"; ?>
<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

// Check login
if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

// Validate order ID
if (!isset($_GET['id'])) {
    $_SESSION['error_message'] = "Order ID not provided.";
    header("Location: order_history.php");
    exit;
}

$order_id = intval($_GET['id']);

// Fetch order details - ensure user owns this order
$order_sql = "SELECT id, user_id, total_amount, status, payment_status, created_at 
              FROM orders 
              WHERE id = ? AND user_id = ?";
$order_stmt = $conn->prepare($order_sql);
$order_stmt->bind_param("ii", $order_id, $user_id);
$order_stmt->execute();
$order_result = $order_stmt->get_result();

if ($order_result->num_rows === 0) {
    $_SESSION['error_message'] = "Order not found or you do not have permission to view it.";
    $order_stmt->close();
    header("Location: order_history.php");
    exit;
}

$order = $order_result->fetch_assoc();
$order_stmt->close();

// Fetch order items
$items_sql = "SELECT oi.id, oi.product_id, oi.quantity, oi.price, p.name, p.image_url
              FROM order_items oi
              JOIN products p ON oi.product_id = p.id
              WHERE oi.order_id = ?
              ORDER BY oi.id DESC";
$items_stmt = $conn->prepare($items_sql);
$items_stmt->bind_param("i", $order_id);
$items_stmt->execute();
$items_result = $items_stmt->get_result();
$items_stmt->close();
?>

<div class="container" style="margin-top:40px;">
    <h2>Order Details #<?= htmlspecialchars($order['id']) ?></h2>
    
    <div class="row" style="margin-bottom: 30px;">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Order Information</h5>
                    <p><strong>Order ID:</strong> #<?= htmlspecialchars($order['id']) ?></p>
                    <p><strong>Date:</strong> <?= date("F j, Y g:i A", strtotime($order['created_at'])) ?></p>
                    <p><strong>Total Amount:</strong> $<?= number_format($order['total_amount'], 2) ?></p>
                    <p><strong>Status:</strong> <span class="badge status-<?= strtolower($order['status']) ?>">
                        <?= ucfirst(htmlspecialchars($order['status'])) ?>
                    </span></p>
                    <p><strong>Payment Status:</strong> <span class="badge payment-<?= strtolower($order['payment_status']) ?>">
                        <?= ucfirst(htmlspecialchars($order['payment_status'])) ?>
                    </span></p>
                </div>
            </div>
        </div>
    </div>

    <h4>Order Items</h4>
    <?php if ($items_result->num_rows > 0): ?>
        <table class="table table-bordered" style="margin-top:20px;">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Image</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($item = $items_result->fetch_assoc()): 
                    $subtotal = $item['price'] * $item['quantity'];
                ?>
                <tr>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td>
                        <img src="../../assets/images/<?= htmlspecialchars($item['image_url']) ?>"
                             width="70" height="70" style="object-fit:cover;">
                    </td>
                    <td>$<?= number_format($item['price'], 2) ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td>$<?= number_format($subtotal, 2) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No items found for this order.</p>
    <?php endif; ?>

    <div style="margin-top:20px;">
        <a href="order_history.php" class="btn btn-secondary">Back to Orders</a>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
