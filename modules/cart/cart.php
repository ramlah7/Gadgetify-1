<?php include "../includes/auth_check.php"; ?>
<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';

// Check login
if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

// Fetch cart items
$sql = "SELECT ci.id AS cart_item_id, p.id AS product_id, p.name, p.price, 
               p.image_url, ci.quantity
        FROM cart_items ci
        JOIN cart c ON ci.cart_id = c.id
        JOIN products p ON ci.product_id = p.id
        WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Calculate total
$total = 0;
?>

<div class="container" style="margin-top:40px;">
    <h2>Your Cart</h2>

    <?php if ($result->num_rows > 0): ?>
        <table class="table table-bordered" style="margin-top:20px;">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Image</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                    <th>Remove</th>
                </tr>
            </thead>

            <tbody>
                <?php while ($row = $result->fetch_assoc()): 
                    $subtotal = $row["price"] * $row["quantity"];
                    $total += $subtotal;
                ?>
                <tr>
                    <td><?= htmlspecialchars($row["name"]) ?></td>

                    <td>
                        <img src="../assets/images/<?= htmlspecialchars($row["image"]) ?>"
                             width="70" height="70" style="object-fit:cover;">
                    </td>

                    <td>$<?= number_format($row["price"], 2) ?></td>

                    <td><?= $row["quantity"] ?></td>

                    <td>$<?= number_format($subtotal, 2) ?></td>

                    <td>
                        <a href="remove.php?id=<?= $row["cart_id"] ?>" 
                           class="btn btn-danger btn-sm">Remove</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <h3>Total: $<?= number_format($total, 2) ?></h3>

        <a href="../checkout.php" class="btn btn-primary" style="margin-top:10px;">
            Proceed to Checkout
        </a>

    <?php else: ?>
        <p>Your cart is empty.</p>
    <?php endif; ?>

</div>

<?php include '../../includes/footer.php'; ?>
