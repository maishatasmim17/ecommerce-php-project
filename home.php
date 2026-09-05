<?php
@include 'config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
   header('location:login.php');
   exit();
}

// Fetch latest 6 products from DB
$stmt = $conn->prepare("SELECT * FROM product WHERE status = 'approved' ORDER BY id DESC LIMIT 6");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Show session message if any
if (isset($_SESSION['message'])) {
   $message = $_SESSION['message'];
   unset($_SESSION['message']);
}
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
<body>
  
<?php include 'header1.php'; ?>


<?php if(isset($message)): ?>
   <div class="message">
      <span><?= htmlspecialchars($message) ?></span>
      <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
   </div>
<?php endif; ?>





<div class="home-bg">
   <section class="home">
      <div class="content">
         <span>Crafted with Heart, Delivered with Care.</span>
         <h3>Handmade is the New Luxury</h3>
         <p>Buy Handmade. Support Dreams.</p>
         <a href="about.php" class="btn">about us</a>
      </div>
   </section>
</div>

<section class="home-category">
   <h1 class="title">shop by category</h1>
   <div class="box-container">
      <div class="box">
         <img src="img/pure cotton-saree.webp" alt="Saree" />
         <a href="saree.php" class="btn">Saree</a>
      </div>
      <div class="box">
         <img src="img/j3.webp" alt="Jewellery" />
         <a href="jewellery.php" class="btn">Jewellery</a>
      </div>
      <div class="box">
         <img src="b2.jpg" alt="Bag" />
         <a href="bag.php" class="btn">Bag</a>
      </div>
      <div class="box">
         <img src="img/p1.jpg" alt="Pottery" />
         <a href="pottery.php" class="btn">Pottery</a>
      </div>
   </div>
</section>

<section class="products">
   <h1 class="title">Latest Products</h1>
   <div class="box-container">
      <?php if(count($products) > 0): ?>
         <?php foreach($products as $product): ?>
            <div class="box">
               <img src="uploaded_img/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" />
               <div class="price"><?= htmlspecialchars($product['price']) ?>tk</div>
               <div class="name"><h1><?= htmlspecialchars($product['name']) ?></h1></div>
               <p class="description"><h2><?= htmlspecialchars($product['details']) ?></h2></p>
               <p class="stock"><h3>Available: <?= htmlspecialchars($product['stock'] ?? 'N/A') ?> pieces</h3></p>

               <form action="add_to_cart.php" method="post">
                  <input type="hidden" name="pid" value="<?= $product['id'] ?>">
                  <input type="hidden" name="name" value="<?= htmlspecialchars($product['name']) ?>">
                  <input type="hidden" name="price" value="<?= $product['price'] ?>">
                  <input type="hidden" name="image" value="<?= htmlspecialchars($product['image']) ?>">
                  <button type="submit" class="btn">Add to Cart</button>
               </form>

               <a href="wishlist.php?add=<?= $product['id'] ?>" class="option-btn">Add to Wishlist</a>
            </div>
         <?php endforeach; ?>
      <?php else: ?>
         <p class="empty">No approved products available.</p>
      <?php endif; ?>
   </div>
</section>

<div id="footer-placeholder"></div>
<?php include 'footer.php'; ?>
</body>
</html>



