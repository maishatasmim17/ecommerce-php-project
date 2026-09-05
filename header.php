<?php
if (session_status() === PHP_SESSION_NONE) {
   session_start();
}

$is_logged_in = isset($_SESSION['user_id']);
?>

<header class="header">
   <div class="flex">

      <a href="<?= $is_logged_in ? 'home.php' : 'login.php' ?>" class="logo">Crafty's Shelf<span>.</span></a>

      <nav class="navbar">
         <a href="<?= $is_logged_in ? 'home.php' : 'login.php' ?>">home</a>
         <a href="<?= $is_logged_in ? 'about.php' : 'login.php' ?>">about</a>
         <a href="<?= $is_logged_in ? 'contact.php' : 'login.php' ?>">contact</a>
      </nav>

      <div class="icons">
         <a href="<?= $is_logged_in ? 'profile.php' : 'login.php' ?>" class="fas fa-user"></a>
         <a href="<?= $is_logged_in ? 'search_page.php' : 'login.php' ?>" class="fas fa-search"></a>
         <a href="<?= $is_logged_in ? 'wishlist.php' : 'login.php' ?>"><i class="fas fa-heart"></i><span>(0)</span></a>
         <a href="<?= $is_logged_in ? 'cart.php' : 'login.php' ?>"><i class="fas fa-shopping-cart"></i><span>(0)</span></a>
      </div>

      <div class="profile">
         <img src="images/user.webp" alt="User" />
         <p><?= $is_logged_in ? htmlspecialchars($_SESSION['user_name']) : 'Guest'; ?></p>

         <div class="flex-btn">
            <?php if ($is_logged_in): ?>
               <a href="logout.php" class="option-btn">logout</a>
            <?php else: ?>
               <a href="login.php" class="option-btn">login</a>
               <a href="register.php" class="option-btn">register</a>
            <?php endif; ?>
         </div>
      </div>

   </div>
</header>
