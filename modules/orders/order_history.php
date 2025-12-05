<?php include "../../includes/auth_check.php"; ?>
<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

// Check login
if (!isset($_SESSION["user_id"])) {
    header("Location: ../../modules/auth/login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

// Display success message if order was just created
$success_message = '';
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

// Fetch all orders for the current user
$sql = "SELECT id, total_amount, status, payment_status, created_at 
        FROM orders 
        WHERE user_id = ?
        ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders_result = $stmt->get_result();
$stmt->close();
?>

<div class="container" style="margin-top:40px;">
    <h2>My Order History 📜</h2>
    
    <?php if ($success_message): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($success_message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <?php if ($orders_result->num_rows > 0): ?>
        <table class="table table-bordered" style="margin-top:20px;">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = $orders_result->fetch_assoc()): ?>
                <tr>
                    <td>#<?= htmlspecialchars($order["id"]) ?></td>
                    <td><?= date("Y-m-d H:i", strtotime($order["created_at"])) ?></td>
                    <td>$<?= number_format($order["total_amount"], 2) ?></td>
                    <td><span class="badge status-<?= strtolower($order["status"]) ?>">
                        <?= ucfirst(htmlspecialchars($order["status"])) ?>
                    </span></td>
                    <td><span class="badge payment-<?= strtolower($order["payment_status"]) ?>">
                        <?= ucfirst(htmlspecialchars($order["payment_status"])) ?>
                    </span></td>
                    <td>
                        <a href="order_details.php?id=<?= $order["id"] ?>" class="btn btn-info btn-sm">View</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>You have not placed any orders yet.</p>
        <a href="../../pages/shop.php" class="btn btn-primary">Start Shopping</a>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>