<?php
@include 'config.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Add to wishlist
if (isset($_GET['wishlist'])) {
    $pid = intval($_GET['wishlist']);

    $check = $conn->prepare("SELECT * FROM wishlist WHERE user_id = ? AND pid = ?");
    $check->execute([$user_id, $pid]);

    if ($check->rowCount() == 0) {
        $insert = $conn->prepare("INSERT INTO wishlist (user_id, pid) VALUES (?, ?)");
        $insert->execute([$user_id, $pid]);
    }

    header("Location: bags.php");
    exit;
}

// Add to cart
if (isset($_GET['cart'])) {
    $pid = intval($_GET['cart']);

    $check = $conn->prepare("SELECT * FROM cart WHERE user_id = ? AND pid = ?");
    $check->execute([$user_id, $pid]);

    if ($check->rowCount() == 0) {
        $insert = $conn->prepare("INSERT INTO cart (user_id, pid, quantity) VALUES (?, ?, 1)");
        $insert->execute([$user_id, $pid]);
    }

    header("Location: bags.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0" />
   <title>Bags - Crafty's Shelf</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
   <link rel="stylesheet" href="css/style.css" />
</head>
<body>

<?php include 'header.php'; ?>

<section class="products">
   <h1 class="title">Handcrafted Bags</h1>

   <div class="box-container">
   <?php
   $select_products = $conn->prepare("SELECT * FROM product WHERE category = 'Bag' AND status = 'approved'");
   $select_products->execute();

   if ($select_products->rowCount() > 0) {
      while ($row = $select_products->fetch(PDO::FETCH_ASSOC)) {
   ?>
      <div class="box">
         <div class="price"><?php echo htmlspecialchars($row['price']); ?> tk</div>
         <img src="images/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
         <div class="name"><?php echo htmlspecialchars($row['name']); ?></div>
         <p class="description"><?php echo htmlspecialchars($row['details']); ?></p>
         <p class="stock">Available: <?php echo (int)$row['stock']; ?> pieces</p>

         <a href="bags.php?wishlist=<?php echo $row['id']; ?>" class="option-btn">Add to Wishlist</a>
         <a href="bags.php?cart=<?php echo $row['id']; ?>" class="btn">Add to Cart</a>
      </div>
   <?php
      }
   } else {
      echo "<p class='empty'>No bags found!</p>";
   }
   ?>
   </div>
</section>

<?php include 'footer.php'; ?>

</body>
</html>
