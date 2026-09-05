<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    // Not logged in or not admin, redirect to login page
    header('Location: login.php');
    exit();
}

// Get admin name and image from session (set these at login)
$admin_name = $_SESSION['name'] ?? 'Admin';
$admin_image = $_SESSION['image'] ?? 'images/profile.jpeg';  // fallback image path

?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0" />
   <title>Admin Panel Header</title>

   <!-- Your admin panel CSS -->
   <link rel="stylesheet" href="css/style2.css" />

   <!-- Font Awesome for icons -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

   <!-- Google Fonts -->
   <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600&display=swap" rel="stylesheet" />
</head>
<body>

<!-- ===== HEADER START ===== -->
<header class="header">
   <div class="flex">
      <a href="admin_page.php" class="logo">Admin<span>Panel</span></a>

      <nav class="navbar">
         <a href="admin_page.php">Home</a>
         <a href="admin_products.php">Products</a>
         <a href="admin_orders.php">Orders</a>
         <a href="admin_messages.php">Messages</a>
         <a href="admin_users.php">Users</a>
      </nav>

      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="user-btn" class="fas fa-user"></div>
      </div>

      <div class="profile">
         <img src="<?= htmlspecialchars($admin_image) ?>" alt="Admin Profile" />
         <p><?= htmlspecialchars($admin_name) ?></p>
         <a href="admin_update_profile.php" class="btn">Update Profile</a>
         <a href="logout.php" class="delete-btn">Logout</a>
      </div>
   </div>
</header>
<!-- ===== HEADER END ===== -->

<script src="js/script.js"></script>
</body>
</html>
