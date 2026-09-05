<?php
@include 'config.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;

// Add to wishlist
if (isset($_GET['wishlist'])) {
    if (!$user_id) {
        header("Location: login.php");
        exit;
    }
    $pid = intval($_GET['wishlist']);
    $check = $conn->prepare("SELECT * FROM wishlist WHERE user_id = ? AND pid = ?");
    $check->execute([$user_id, $pid]);
    if ($check->rowCount() === 0) {
        $insert = $conn->prepare("INSERT INTO wishlist (user_id, pid) VALUES (?, ?)");
        $insert->execute([$user_id, $pid]);
    }
    header("Location: pottery.php");
    exit;
}

// Add to cart
if (isset($_GET['cart'])) {
    if (!$user_id) {
        header("Location: login.php");
        exit;
    }
    $pid = intval($_GET['cart']);
    $check = $conn->prepare("SELECT * FROM cart WHERE user_id = ? AND pid = ?");
    $check->execute([$user_id, $pid]);
    if ($check->rowCount() === 0) {
        $insert = $conn->prepare("INSERT INTO cart (user_id, pid, quantity) VALUES (?, ?, 1)");
        $insert->execute([$user_id, $pid]);
    }
    header("Location: pottery.php");
    exit;
}

// Fetch approved pottery products
$stmt = $conn->prepare("SELECT * FROM product WHERE category = ? AND status = 'approved' ORDER BY id DESC");
$stmt->execute(['pottery']);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8" />
   <meta name="viewport" content="width=device-width, initial-scale=1" />
   <title>Pottery - Crafty's Shelf</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
   <link rel="stylesheet" href="css/style.css" />
</head>
<body>

<!-- Header -->
<?php include 'header.php'; ?>

<section class="products">
   <h1 class="title">Artisan Pottery</h1>

   <div class="box-container">
      <?php if (count($products) > 0): ?>
         <?php foreach ($products as $product): ?>
            <div class="box">
               <div class="price"><?= htmlspecialchars($product['price']); ?>tk</div>
               <img src="images/<?= htmlspecialchars($product['image']); ?>" alt="<?= htmlspecialchars($product['name']); ?>">
               <div class="name"><?= htmlspecialchars($product['name']); ?></div>
               <p class="description"><?= htmlspecialchars($product['details']); ?></p>
               <?php if (isset($product['stock'])): ?>
                  <p class="stock">Available: <?= htmlspecialchars($product['stock']); ?> pieces</p>
               <?php endif; ?>
               <a href="pottery.php?wishlist=<?= $product['id']; ?>" class="option-btn">Add to Wishlist</a>
               <a href="pottery.php?cart=<?= $product['id']; ?>" class="btn">Add to Cart</a>
            </div>
         <?php endforeach; ?>
      <?php else: ?>
         <p class="empty">No pottery products available right now!</p>
      <?php endif; ?>
   </div>
</section>

<!-- Footer -->
<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>
