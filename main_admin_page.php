<?php
@include 'config.php';
session_start();

// Redirect if not logged in or not admin
if (!isset($_SESSION['user_id'], $_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: login.php');
    exit();
}



// Initialize counts
$totalAccounts = $totalUsers = $totalAdmins = $productsAdded = $approvedProducts = 0;
$ordersPlaced = $productsDelivered = $pendingOrders = 0;
$pageReviews = $pageFeedback = $sellerMessages = 0;

try {
    // Using a helper function to get counts safely
    function getCount($conn, $query, $params = []) {
        $stmt = $conn->prepare($query);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    $totalAccounts = getCount($conn, "SELECT COUNT(*) FROM users");
    $totalUsers = getCount($conn, "SELECT COUNT(*) FROM users WHERE user_type = ?", ['user']);
    $totalAdmins = getCount($conn, "SELECT COUNT(*) FROM users WHERE user_type = ?", ['admin']);
    $productsAdded = getCount($conn, "SELECT COUNT(*) FROM product");
    $approvedProducts = getCount($conn, "SELECT COUNT(*) FROM product WHERE status = ?", ['approved']);
    $ordersPlaced = getCount($conn, "SELECT COUNT(*) FROM orders");
    $productsDelivered = getCount($conn, "SELECT COUNT(*) FROM orders WHERE status = ?", ['delivered']);
    $pendingOrders = getCount($conn, "SELECT COUNT(*) FROM orders WHERE status = ?", ['pending']);
    $pageReviews = getCount($conn, "SELECT COUNT(*) FROM reviews");
    $pageFeedback = getCount($conn, "SELECT COUNT(*) FROM feedback");
    $sellerMessages = getCount($conn, "SELECT COUNT(*) FROM seller_messages");

} catch (PDOException $e) {
    // Log error if needed, but avoid displaying sensitive info to users
    error_log("DB Error: " . $e->getMessage());
    // Optional: You can set default zeros or show a friendly message
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Main Admin Dashboard</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
    <!-- Your admin CSS -->
    <link rel="stylesheet" href="css/admin_style1.css" />
</head>
<body>

<header class="header">
  <div class="flex">
    <a href="main_admin_page.php" class="logo">Admin<span>Panel</span></a>

    <nav class="navbar">
      <a href="home.php">Home</a>
      <a href="admin_approve.php">Seller Request</a>
    </nav>

    <div class="icons">
      <div id="menu-btn" class="fas fa-bars"></div>
      <div id="user-btn" class="fas fa-user"></div>
    </div>

    <div class="profile">
      <img src="images/profile.jpeg" alt="Profile Image" />
      <p><?= htmlspecialchars($_SESSION['name'] ?? 'Admin Name') ?></p>
      <a href="admin_update_profile.php" class="btn" target="_blank" rel="noopener noreferrer">Update Profile</a>
      <a href="logout.php" class="delete-btn">Logout</a>
    </div>
  </div>
</header>

<section class="dashboard">
   <h1 class="title">Main Admin Dashboard</h1>

   <div class="box-container">

     
      <div class="box">
         <p>Total Users</p>
         <h3><?= $totalUsers ?></h3>
         <a href="admin_users.php" class="btn">See Users</a>
      </div>

   

      <div class="box">
         <p>Total Products Added</p>
         <h3><?= $productsAdded ?></h3>
         <a href="admin_products.php" class="btn">See Products</a>
      </div>

      <div class="box">
         <p>Total Approved Products</p>
         <h3><?= $approvedProducts ?></h3>
         <a href="approved_products.php" class="btn">View Approved</a>
      </div>

      <div class="box">
         <p>Total Orders Placed</p>
         <h3><?= $ordersPlaced ?></h3>
         <a href="admin_orders.php" class="btn">See Orders</a>
      </div>

      <div class="box">
         <p>Total Products Delivered</p>
         <h3><?= $productsDelivered ?></h3>
         <a href="delivered_orders.php" class="btn">Delivered Orders</a>
      </div>

      <div class="box">
         <p>Total Pending Orders</p>
         <h3><?= $pendingOrders ?></h3>
         <a href="pending_orders.php" class="btn">Pending Orders</a>
      </div>


      <div class="box">
         <p>Page Feedback</p>
         <h3><?= $pageFeedback ?></h3>
         <a href="page_feedback.php" class="btn">View Feedback</a>
      </div>

      <div class="box">
         <p>Messages from Sellers</p>
         <h3><?= $sellerMessages ?></h3>
         <a href="admin_contacts.php" class="btn">View Messages</a>
      </div>

   </div>
</section>

<script src="js/script.js"></script>

</body>
</html>
