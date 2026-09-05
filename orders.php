orders.php
<?php
@include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';

// Handle cancellation submission
if (isset($_POST['cancel_order'])) {
    $order_id = $_POST['order_id'];
    $reason = trim($_POST['reason']);

    if (empty($reason)) {
        $message = "Please select a cancellation reason.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
        $stmt->execute([$order_id, $user_id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            $message = "Invalid order.";
        } elseif ($order['payment_status'] == 'completed') {
            $message = "Order already completed and cannot be cancelled.";
        } else {
            $insert = $conn->prepare("INSERT INTO order_cancellations (order_id, user_id, reason) VALUES (?, ?, ?)");
            $insert->execute([$order_id, $user_id, $reason]);

            $update = $conn->prepare("UPDATE orders SET payment_status = 'cancellation_requested' WHERE id = ?");
            $update->execute([$order_id]);

            $message = "Cancellation request sent to admin.";
        }
    }
}

$orders = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY placed_on DESC");
$orders->execute([$user_id]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Cancel Order</title>
<link rel="stylesheet" href="css/style.css" />
<style>
  .message {
    padding: 10px;
    margin: 10px 0;
    background: #d4edda;
    color: #155724;
    border-radius: 5px;
    font-size: 1.1rem;
  }
  .error {
    background: #f8d7da;
    color: #721c24;
  }
  .cancel-orders h1 {
    font-size: 2rem;
    margin-bottom: 1.5rem;
    text-align: center;
  }
  .order-box {
    border: 1px solid #ddd;
    padding: 1.2rem;
    margin-bottom: 1.5rem;
    border-radius: 5px;
    font-size: 1.6rem;
    background-color: #f9f9f9;
  }
  .order-box p {
    margin: 0.5rem 0;
  }
  form {
    margin-top: 1rem;
  }
  select, button {
    padding: 0.6rem;
    font-size: 1.6rem;
  }
  button[name="cancel_order"] {
    background-color: #dc3545;
    color: white;
    border: none;
    padding: 0.6rem 1.2rem;
    font-size: 1rem;
    border-radius: 4px;
    cursor: pointer;
  }
  button[name="cancel_order"]:hover {
    background-color: #c82333;
  }
</style>
</head>
<body>

<?php @include 'header1.php'; ?>

<section class="cancel-orders">
  <h1>Your Orders</h1>

  <?php if ($message): ?>
    <div class="message <?= strpos($message, 'error') !== false ? 'error' : '' ?>"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>

  <?php if ($orders->rowCount() > 0): ?>
    <?php while ($order = $orders->fetch(PDO::FETCH_ASSOC)): ?>
      <div class="order-box">
        <p><strong>Order ID:</strong> <?= $order['id'] ?></p>
        <p><strong>Placed on:</strong> <?= $order['placed_on'] ?></p>
        <p><strong>Total Products:</strong> <?= $order['total_products'] ?></p>
        <p><strong>Total Price:</strong> <?= $order['total_price'] ?>/-</p>
        <p><strong>Status:</strong> <?= $order['payment_status'] ?></p>

        <?php if ($order['payment_status'] != 'completed' && $order['payment_status'] != 'cancelled'): ?>
          <form method="post" onsubmit="return confirm('Are you sure you want to cancel this order?');">
            <input type="hidden" name="order_id" value="<?= $order['id'] ?>" />
            <label for="reason_<?= $order['id'] ?>">Select cancellation reason:</label><br />
            <select name="reason" id="reason_<?= $order['id'] ?>" required>
              <option value="" disabled selected>Select a reason</option>
              <option value="Ordered by mistake">Ordered by mistake</option>
              <option value="Found a better price elsewhere">Found a better price elsewhere</option>
              <option value="Shipping is too slow">Shipping is too slow</option>
              <option value="Need to change address">Need to change address</option>
              <option value="Other">Other</option>
            </select><br /><br />
            <button type="submit" name="cancel_order">Cancel Order</button>
          </form>
        <?php else: ?>
          <p><em>This order cannot be cancelled.</em></p>
        <?php endif; ?>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <p style="text-align:center; font-size:1.2rem;">You have no orders placed yet.</p>
  <?php endif; ?>
</section>

<?php @include 'footer.php'; ?>

</body>
</html>