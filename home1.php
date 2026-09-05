<?php
session_start();

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0" />
   <title>Crafty's Shelf - Home</title>
   <link rel="stylesheet" href="css/style.css" />
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
</head>
<body>
    

<!-- ✅ Include the fixed header -->
<?php include 'header.php'; ?>
<img class="bg" src="images/shelf.jpg" alt="shopping">

<!-- Message for guests -->
<div class="message">
   <span>Welcome to Crafty's Shelf! Please login to interact.</span>
   <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
</div>

<!-- Home Banner -->
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

<!-- Shop by Category -->
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

<!-- Latest Products -->
<section class="products">
   <h1 class="title">latest products</h1>
   <div class="box-container">

      <div class="box">
         <img src="img/s1.webp" alt="Silk Saree" />
         <div class="price">1500tk</div>
         <div class="name"><h1>Silk Saree</h1></div>
         <p class="description"><h2>Pure silk saree with elegant zari border.</h2></p>
         <p class="stock"><h3>Available: 20 pieces</h3></p>
         <a href="login.php" class="option-btn">Add to Wishlist</a>
         <a href="login.php" class="btn">Add to Cart</a>
      </div>

      <div class="box">
         <img src="img/j2.webp" alt="Gold Necklace" />
         <div class="price">500tk</div>
         <div class="name"><h1>Gold Necklace</h1></div>
         <p class="description"><h2>Necklace with intricate design.</h2></p>
         <p class="stock"><h3>Available: 15 pieces</h3></p>
         <a href="login.php" class="option-btn">Add to Wishlist</a>
         <a href="login.php" class="btn">Add to Cart</a>
      </div>

      <div class="box">
         <img src="img/b1.jpg" alt="Jute Tote Bag" />
         <div class="price">250tk</div>
         <div class="name"><h1>Jute Tote Bag</h1></div>
         <p class="description"><h2>Eco-friendly handcrafted jute tote bag.</h2></p>
         <p class="stock"><h3>Available: 25 pieces</h3></p>
         <a href="login.php" class="option-btn">Add to Wishlist</a>
         <a href="login.php" class="btn">Add to Cart</a>
      </div>

      <div class="box">
         <img src="img/p2.webp" alt="Clay Vase" />
         <div class="price">400tk</div>
         <div class="name"><h1>Clay Vase</h1></div>
         <p class="description"><h2>Hand-thrown terracotta vase for home decor.</h2></p>
         <p class="stock"><h3>Available: 10 pieces</h3></p>
         <a href="login.php" class="option-btn">Add to Wishlist</a>
         <a href="login.php" class="btn">Add to Cart</a>
      </div>

      <div class="box">
         <img src="img/p1.jpg" alt="Terracotta Plate" />
         <div class="price">200tk</div>
         <div class="name"><h1>Terracotta Plate</h1></div>
         <p class="description"><h2>Handmade terracotta dinner plate, eco-friendly and durable.</h2></p>
         <p class="stock"><h3>Available: 18 pieces</h3></p>
         <a href="login.php" class="option-btn">Add to Wishlist</a>
         <a href="login.php" class="btn">Add to Cart</a>
      </div>

      <div class="box">
         <img src="img/b3.jpg" alt="Beaded Handbag" />
         <div class="price">700tk</div>
         <div class="name"><h1>Beaded Handbag</h1></div>
         <p class="description"><h2>Stylish handwoven handbag decorated with colorful beads.</h2></p>
         <p class="stock"><h3>Available: 14 pieces</h3></p>
         <a href="login.php" class="option-btn">Add to Wishlist</a>
         <a href="login.php" class="btn">Add to Cart</a>
      </div>

   </div>
</section>

<!-- Footer -->
<div id="footer-placeholder"></div>
<?php include 'footer.php'; ?>

</body>
</html>
