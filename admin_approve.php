<?php
@include 'config.php';
session_start();

// ✅ Admin Access Check
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// ✅ Approve Request
if (isset($_GET['approve'])) {
    $id = intval($_GET['approve']);
    
    // 1. Update product status
    $stmt = $conn->prepare("UPDATE product SET status = 'approved' WHERE id = ?");
    $stmt->execute([$id]);
    
    // 2. Get product details and seller ID
    $product_stmt = $conn->prepare("SELECT name, user_id FROM product WHERE id = ?");
    $product_stmt->execute([$id]);
    $product = $product_stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($product) {
        $product_name = $product['name'];
        $seller_id = $product['user_id'];
        
        // 3. Create notification
        $message = "Your product '{$product_name}' has been approved and is now live!";
        $notif_stmt = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
        $notif_stmt->execute([$seller_id, $message]);
        
        // 4. (Optional) Send email notification
        $seller_stmt = $conn->prepare("SELECT email FROM users WHERE id = ?");
        $seller_stmt->execute([$seller_id]);
        $seller = $seller_stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($seller && !empty($seller['email'])) {
            $to = $seller['email'];
            $subject = "Product Approved - Crafty's Shelf";
            $headers = "From: Crafty's Shelf <noreply@craftyshelf.com>\r\n";
            mail($to, $subject, $message, $headers);
        }
    }
    
    header("Location: admin_approve.php");
    exit();
}

// ✅ Deny Request
if (isset($_GET['deny'])) {
    $id = intval($_GET['deny']);
    $stmt = $conn->prepare("UPDATE product SET status = 'rejected' WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: admin_approve.php");
    exit();
}

// ✅ Fetch Pending Products
$stmt = $conn->prepare("SELECT * FROM product WHERE status = 'pending' ORDER BY id DESC");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8" />
   <meta http-equiv="X-UA-Compatible" content="IE=edge" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0" />
   <title>Admin Approvals</title>

   <!-- Font Awesome -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />

   <!-- Custom CSS -->
   <link rel="stylesheet" href="css/admin_style1.css" />
</head>
<body>

<!-- ✅ Admin Header -->
<header class="header">
   <div class="flex">
      <div class="logo">Admin <span>Panel</span></div>
      <nav class="navbar">
         <a href="main_admin_page.html">Dashboard</a>
         <a href="admin_users.html">Users</a>
         <a href="admin_approve.php" class="active">Approvals</a>
         <a href="logout.php" class="logout">Logout</a>
      </nav>
   </div>
</header>

<!-- ✅ Approve/Deny Products Section -->
<section class="show-products">
   <h1 class="title">Approve or Deny Uploaded Products</h1>

   <div class="box-container">
      <?php if (empty($products)): ?>
         <p class="empty">No pending products found.</p>
      <?php else: ?>
         <?php foreach ($products as $product): ?>
            <div class="box">
               <div class="price"><?= htmlspecialchars($product['price']) ?>৳</div>
               <img src="uploaded_img/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" />
               <div class="name"><?= htmlspecialchars($product['name']) ?></div>
               <div class="cat">Category: <?= htmlspecialchars($product['category']) ?></div>
               <div class="details"><?= htmlspecialchars($product['details']) ?></div>
               <div class="quantity">Quantity: <?= htmlspecialchars($product['stock'] ?? 'N/A') ?></div>
               <div class="flex-btn">
                  <a href="admin_approve.php?approve=<?= $product['id'] ?>" class="option-btn" onclick="return confirm('Are you sure you want to APPROVE this product?');">Approve</a>
                  <a href="admin_approve.php?deny=<?= $product['id'] ?>" class="delete-btn" onclick="return confirm('Are you sure you want to DENY this product?');">Deny</a>
               </div>
            </div>
         <?php endforeach; ?>
      <?php endif; ?>
   </div>
</section>

<script src="js/script.js" defer></script>
</body>
</html>