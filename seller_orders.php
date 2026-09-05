<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
@include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$seller_id = $_SESSION['user_id'];

@include 'header1.php';

// Ensure seller_id exists in order_items by checking product's seller_id during order insertion (not here)

$sql = "SELECT 
            o.id AS order_id, 
            u.name AS buyer_name, 
            o.number, 
            o.email, 
            o.method, 
            o.address, 
            o.placed_on, 
            oi.product_id, 
            oi.quantity, 
            p.name AS product_name, 
            p.image, 
            o.payment_status,
            o.total_price
        FROM orders o
        JOIN users u ON o.user_id = u.id
        JOIN order_items oi ON o.id = oi.order_id
        JOIN product p ON oi.product_id = p.id
        WHERE p.seller_id = ?
        ORDER BY o.placed_on DESC";

$stmt = $conn->prepare($sql);
$stmt->execute([$seller_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8" />
   <meta http-equiv="X-UA-Compatible" content="IE=edge" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0" />
   <title>Crafty's Shelf - Home</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
   <link rel="stylesheet" href="css/style.css" />
</head>

<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 20px;
    }
    h2 {
        text-align: center;
        color: #444;
        margin-bottom: 25px;
    }
    .orders-table {
        width: 100%;
        border-collapse: collapse;
        background: #fff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .orders-table th, .orders-table td {
        padding: 12px 15px;
        text-align: center;
        border-bottom: 1px solid #eee;
    }
    .orders-table th {
        background-color: #222;
        color: #fff;
        text-transform: uppercase;
        font-size: 14px;
    }
    .orders-table tr:hover {
        background-color: #f9f9f9;
    }
    .product-img {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 6px;
    }
    .status-paid {
        color: green;
        font-weight: bold;
    }
    .status-pending {
        color: orange;
        font-weight: bold;
    }
    .no-orders {
        text-align: center;
        color: #888;
        font-size: 18px;
        margin-top: 50px;
    }
</style>

<h2>Your Product Orders</h2>

<?php if (!empty($orders)): ?>
    <table class="orders-table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Buyer Name</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Method</th>
                <th>Address</th>
                <th>Date</th>
                <th>Product</th>
                <th>Image</th>
                <th>Qty</th>
                <th>Total Price</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?= $order['order_id'] ?></td>
                    <td><?= htmlspecialchars($order['buyer_name']) ?></td>
                    <td><?= htmlspecialchars($order['number']) ?></td>
                    <td><?= htmlspecialchars($order['email']) ?></td>
                    <td><?= htmlspecialchars($order['method']) ?></td>
                    <td><?= nl2br(htmlspecialchars($order['address'])) ?></td>
                    <td><?= htmlspecialchars($order['placed_on']) ?></td>
                    <td><?= htmlspecialchars($order['product_name']) ?></td>
                    <td>
                        <?php if (!empty($order['image'])): ?>
                            <img src="uploads/<?= htmlspecialchars($order['image']) ?>" alt="Product Image" class="product-img">
                        <?php else: ?>
                            <span>No Image</span>
                        <?php endif; ?>
                    </td>
                    <td><?= (int)$order['quantity'] ?></td>
                    <td>$<?= number_format($order['total_price'], 2) ?></td>
                    <td class="<?= $order['payment_status'] === 'completed' ? 'status-paid' : 'status-pending' ?>">
                        <?= ucfirst($order['payment_status']) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p class="no-orders">No orders found for your products.</p>
<?php endif; ?>

<?php 'footer.php'; 
?>
