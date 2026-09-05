<?php
@include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
   header('Location: login.php');
   exit;
}

$user_id = $_SESSION['user_id'];
$cart_items = [];
$cart_total = 0;
$total_products_text = "";

// Fetch cart items
$stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($cart_items as $item) {
   $product_total = $item['price'] * $item['quantity'];
   $cart_total += $product_total;
   $total_products_text .= $item['name'] . " (" . $item['price'] . " x " . $item['quantity'] . ") - ";
}

// Handle order submission
if (isset($_POST['order'])) {
   if (empty($cart_items)) {
      $message[] = 'Your cart is empty!';
   } else {
      $name = $_POST['name'];
      $number = $_POST['number'];
      $email = $_POST['email'];
      $method = $_POST['method'];
      $address = $_POST['address'];
      $placed_on = date('Y-m-d');
      $payment_status = 'pending';

      // Insert order
      $insert_order = $conn->prepare("INSERT INTO orders(user_id, name, number, email, method, address, total_products, total_price, placed_on, payment_status) VALUES(?,?,?,?,?,?,?,?,?,?)");
      $insert_order->execute([$user_id, $name, $number, $email, $method, $address, $total_products_text, $cart_total, $placed_on, $payment_status]);

      // Clear cart after placing order
      $delete_cart = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
      $delete_cart->execute([$user_id]);

      $message[] = 'Order placed successfully and is pending admin approval!';
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Checkout</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style1.css">
</head>
<body>

<?php @include 'header1.php'; ?>

<section class="display-orders">
   <?php if (!empty($cart_items)): ?>
      <?php foreach ($cart_items as $item): ?>
         <p><?= $item['name']; ?> <span>(<?= $item['price']; ?>/- x <?= $item['quantity']; ?>)</span></p>
      <?php endforeach; ?>
      <div class="grand-total">grand total : <?= $cart_total; ?>/-</div>
   <?php else: ?>
      <p class="empty">Your cart is empty!</p>
   <?php endif; ?>
</section>

<section class="checkout-orders">
   <form action="" method="POST">
      <h3>place your order</h3>
      <div class="flex">
         <div class="inputBox">
            <span>your name :</span>
            <input type="text" name="name" placeholder="enter your name" class="box" required>
         </div>
         <div class="inputBox">
            <span>your Phone Number :</span>
            <input type="text" name="number" placeholder="enter your Phone Number" class="box" required>
         </div>
         <div class="inputBox">
            <span>your email :</span>
            <input type="email" name="email" placeholder="enter your email" class="box" required>
         </div>
         <div class="inputBox">
            <span>payment method :</span>
            <select name="method" class="box" required>
               <option value="cash on delivery">cash on delivery</option>
               <option value="credit card">credit card</option>
            </select>
         </div>
         <div class="inputBox">
            <span>your address :</span>
            <input type="text" name="address" placeholder="e.g. flat no., street, city, country" class="box" required>
         </div>
      </div>

      <input type="submit" name="order" class="btn <?= empty($cart_items) ? 'disabled' : ''; ?>" value="place order" <?= empty($cart_items) ? 'disabled' : ''; ?>>
   </form>
</section>

<?php @include 'footer.php'; ?>

</body>
</html>
