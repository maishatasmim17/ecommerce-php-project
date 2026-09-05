<?php
@include 'config.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;

// Add to Cart
if (isset($_POST['add_to_cart'])) {
    if ($user_id) {
        $pid = $_POST['pid'];
        $name = $_POST['name'];
        $price = $_POST['price'];
        $image = $_POST['image'];

        $stmt = $conn->prepare("INSERT INTO cart (user_id, pid, name, price, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $pid, $name, $price, $image]);
    } else {
        header("Location: login.php");
        exit;
    }
}

// Add to Wishlist
if (isset($_POST['add_to_wishlist'])) {
    if ($user_id) {
        $pid = $_POST['pid'];
        $name = $_POST['name'];
        $price = $_POST['price'];
        $image = $_POST['image'];

        $stmt = $conn->prepare("INSERT INTO wishlist (user_id, pid, name, price, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $pid, $name, $price, $image]);
    } else {
        header("Location: login.php");
        exit;
    }
}

// Fetch products from 'Bag' category (case-sensitive)
$stmt = $conn->prepare("SELECT * FROM product WHERE category = 'Bag' ORDER BY id DESC");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0" />
   <title>Bag Collection</title>
   <link rel="stylesheet" href="css/style.css" />
</head>
<body>

<section class="products">
   <h1 class="title">Bag Collection</h1>
   <div class="box-container">

   <?php foreach ($products as $product): ?>
      <div class="box">
         <div class="price"><?= $product['price']; ?> tk</div>
         <img src="images/<?= htmlspecialchars($product['image']); ?>" alt="<?= htmlspecialchars($product['name']); ?>">
         <div class="name"><?= htmlspecialchars($product['name']); ?></div>
         <p class="description"><?= htmlspecialchars($product['details']); ?></p>

         <form method="post">
            <input type="hidden" name="pid" value="<?= $product['id']; ?>">
            <input type="hidden" name="name" value="<?= htmlspecialchars($product['name']); ?>">
            <input type="hidden" name="price" value="<?= $product['price']; ?>">
            <input type="hidden" name="image" value="<?= htmlspecialchars($product['image']); ?>">

            <button type="submit" name="add_to_wishlist" class="option-btn">Add to Wishlist</button>
            <button type="submit" name="add_to_cart" class="btn">Add to Cart</button>
         </form>
      </div>
   <?php endforeach; ?>

   </div>
</section>

</body>
</html>
