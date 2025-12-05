
<?php
require_once __DIR__ . '/../../includes/admin_check.php';
require_once __DIR__ . '/../../includes/header.php';
?>
<style>
.admin-boxes {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 25px;
    margin-top: 30px;
}

.admin-card {
    background: #fff;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    transition: 0.2s ease;
    text-align: center;
}

.admin-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 5px 12px rgba(0,0,0,0.2);
}

.admin-card h3 {
    margin-bottom: 12px;
    font-size: 20px;
}

.admin-card a {
    padding: 10px 20px;
    background: #007bff;
    color: white;
    border-radius: 6px;
    text-decoration: none;
    display: inline-block;
    margin-top: 10px;
}

.admin-card a:hover {
    background: #0056b3;
}
</style>

<div class="container" style="margin-top:40px;">
    <h2>Admin Dashboard</h2>
    <p>Welcome, <strong><?php echo $_SESSION['user_name']; ?></strong> (Admin)</p>

    <div class="admin-boxes">

        <div class="admin-card">
            <h3>Add New Product</h3>
            <p>Add items that will appear in the shop.</p>
            <a href="/Gadgetify/modules/admin/add_product.php">Add Product</a>
        </div>

        <div class="admin-card">
            <h3>Manage Products</h3>
            <p>Edit, delete or view all products.</p>
            <a href="/Gadgetify/modules/admin/products_list.php">View Products</a>
        </div>

        <div class="admin-card">
            <h3>Orders (Coming Soon)</h3>
            <p>Manage customer orders.</p>
            <a href="#">Feature Coming</a>
        </div>

        <div class="admin-card">
            <h3>Users (Future)</h3>
            <p>View customers / manage admins.</p>
            <a href="#">Feature Coming</a>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
