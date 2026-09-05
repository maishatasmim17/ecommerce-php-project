<?php
@include 'config.php';
session_start();

// Optional: Admin login check
// if (!isset($_SESSION['admin_id'])) {
//     header('Location: admin_login.php');
//     exit;
// }

// Handle payment status update
if (isset($_POST['update_order'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['update_payment'];

    $update = $conn->prepare("UPDATE orders SET payment_status = ? WHERE id = ?");
    $update->execute([$new_status, $order_id]);

    header('Location: admin_orders.php');
    exit;
}

// Handle order deletion
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];

    $delete = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $delete->execute([$delete_id]);

    header('Location: admin_orders.php');
    exit;
}

// Fetch all orders
$orders = $conn->prepare("SELECT * FROM orders ORDER BY placed_on DESC");
$orders->execute();

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8" />
   <meta name="viewport" content="width=device-width, initial-scale=1" />
   <title>Admin Orders</title>

   <link rel="stylesheet" href="css/style2.css" />
   <link rel="stylesheet" href="css/admin_style1.css" />
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
   <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600&display=swap" rel="stylesheet" />
</head>
<body>

<?php @include 'admin_header.php'; ?>

<section class="placed-orders">
   <h1 class="title">Placed Orders</h1>

   <div class="box-container">

      <?php if ($orders->rowCount() > 0): ?>
         <?php while ($row = $orders->fetch(PDO::FETCH_ASSOC)): ?>
            <div class="box">
               <p> user id : <span><?= htmlspecialchars($row['user_id']); ?></span> </p>
               <p> placed on : <span><?= htmlspecialchars($row['placed_on']); ?></span> </p>
               <p> name : <span><?= htmlspecialchars($row['name']); ?></span> </p>
               <p> email : <span><?= htmlspecialchars($row['email']); ?></span> </p>
               <p> number : <span><?= htmlspecialchars($row['number']); ?></span> </p>
               <p> address : <span><?= htmlspecialchars($row['address']); ?></span> </p>
               <p> total products : <span><?= htmlspecialchars($row['total_products']); ?></span> </p>
               <p> total price : <span><?= htmlspecialchars($row['total_price']); ?>/-</span> </p>
               <p> payment method : <span><?= htmlspecialchars($row['method']); ?></span> </p>
               <form action="" method="POST">
                  <input type="hidden" name="order_id" value="<?= $row['id']; ?>" />
                  <select name="update_payment" class="drop-down" required>
                     <option value="" disabled <?= $row['payment_status'] == '' ? 'selected' : ''; ?>>Select status</option>
                     <option value="pending" <?= $row['payment_status'] == 'pending' ? 'selected' : ''; ?>>pending</option>
                     <option value="completed" <?= $row['payment_status'] == 'completed' ? 'selected' : ''; ?>>completed</option>
                     <option value="denied" <?= $row['payment_status'] == 'denied' ? 'selected' : ''; ?>>denied</option>
                  </select>
                  <div class="flex-btn">
                     <input type="submit" name="update_order" class="option-btn" value="update" />
                     <a href="admin_orders.php?delete=<?= $row['id']; ?>" class="delete-btn" onclick="return confirm('Delete this order?');">delete</a>
                  </div>
               </form>
            </div>
         <?php endwhile; ?>
      <?php else: ?>
         <p class="empty">No orders placed yet.</p>
      <?php endif; ?>

   </div>
</section>

<?php @include 'footer.php'; ?>

<script src="js/script.js"></script>
</body>
</html>
