<?php
@include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
   header('Location: login.php');
   exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Order summary
$select_orders = $conn->prepare("SELECT * FROM orders WHERE user_id = ?");
$select_orders->execute([$user_id]);
$ordered = $select_orders->rowCount();

$select_delivered = $conn->prepare("SELECT * FROM orders WHERE user_id = ? AND payment_status = 'completed'");
$select_delivered->execute([$user_id]);
$delivered = $select_delivered->rowCount();

$select_pending = $conn->prepare("SELECT * FROM orders WHERE user_id = ? AND payment_status != 'completed'");
$select_pending->execute([$user_id]);
$pending = $select_pending->rowCount();
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8" />
   <meta name="viewport" content="width=device-width, initial-scale=1" />
   <title>User Profile | Crafty's Shelf</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
   <link rel="stylesheet" href="css/style.css" />
   <style>
      /* Base styles */
      body {
         background-color: #f8f9fa;
         font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
         color: #333;
         line-height: 1.6;
      }
      
      /* Profile container */
      .profile-container {
         max-width: 800px;
         margin: 3rem auto;
         padding: 2rem;
         background: #fff;
         border-radius: 12px;
         box-shadow: 0 8px 25px rgba(0,0,0,0.1);
         text-align: center;
      }
      
      /* Header */
      .profile-header {
         margin-bottom: 2rem;
      }
      
      .profile-header h2 {
         font-size: 2.2rem;
         color: #2c3e50;
         margin-bottom: 0.5rem;
         position: relative;
         display: inline-block;
      }
      
      .profile-header h2::after {
         content: '';
         display: block;
         width: 70px;
         height: 3px;
         background: #27ae60;
         margin: 10px auto;
         border-radius: 3px;
      }
      
      .profile-header p {
         color: #7f8c8d;
         font-size: 1.1rem;
      }
      
      /* Profile content */
      .profile-content {
         display: flex;
         flex-wrap: wrap;
         gap: 30px;
         margin-bottom: 2rem;
      }
      
      /* Profile image section */
      .profile-image-section {
         flex: 1;
         min-width: 280px;
      }
      
      .profile-image {
         width: 200px;
         height: 200px;
         margin: 0 auto 25px;
         border-radius: 50%;
         border: 5px solid #ecf0f1;
         overflow: hidden;
         display: flex;
         align-items: center;
         justify-content: center;
         background-color: #e0e0e0;
         box-shadow: 0 5px 15px rgba(0,0,0,0.1);
         font-size: 60px;
         color: #555;
         font-weight: 700;
      }
      
      .profile-image img {
         width: 100%;
         height: 100%;
         object-fit: cover;
         display: block;
      }
      
      /* User details */
      .user-details {
         background: #f8f9fa;
         border-radius: 10px;
         padding: 25px;
         text-align: left;
      }
      
      .detail-item {
         margin-bottom: 15px;
         font-size: 1.1rem;
      }
      
      .detail-label {
         font-weight: 600;
         color: #2c3e50;
         display: inline-block;
         width: 120px;
      }
      
      .detail-value {
         color: #34495e;
      }
      
      /* Stats section */
      .stats-section {
         flex: 1;
         min-width: 280px;
      }
      
      .stats-title {
         font-size: 1.3rem;
         color: #2c3e50;
         margin-bottom: 1.5rem;
         text-align: center;
         padding-bottom: 10px;
         border-bottom: 2px solid #ecf0f1;
      }
      
      .stats-container {
         display: grid;
         grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
         gap: 20px;
         margin-bottom: 1.5rem;
      }
      
      .stat-card {
         background: #fff;
         border-radius: 10px;
         padding: 20px 15px;
         text-align: center;
         box-shadow: 0 5px 15px rgba(0,0,0,0.05);
         border: 1px solid #ecf0f1;
         transition: all 0.3s ease;
      }
      
      .stat-card:hover {
         transform: translateY(-5px);
         box-shadow: 0 8px 20px rgba(0,0,0,0.1);
      }
      
      .stat-value {
         font-size: 2.2rem;
         font-weight: 700;
         color: #27ae60;
         margin-bottom: 5px;
      }
      
      .stat-label {
         font-size: 1.1rem;
         color: #7f8c8d;
      }
      
      /* Buttons */
      .btn-group {
         display: flex;
         justify-content: center;
         gap: 1.5rem;
         margin-top: 2rem;
         flex-wrap: wrap;
      }
      
      .btn-group a {
         padding: 1rem 2rem;
         font-size: 1.2rem;
         border-radius: 8px;
         text-decoration: none;
         display: flex;
         align-items: center;
         justify-content: center;
         gap: 8px;
         transition: all 0.3s ease;
         min-width: 200px;
      }
      
      .btn-update {
         background: #27ae60;
         color: white;
         border: 2px solid #27ae60;
      }
      
      .btn-update:hover {
         background: #219653;
         border-color: #219653;
      }
      
      .btn-logout {
         background: #fff;
         color: #e74c3c;
         border: 2px solid #e74c3c;
      }
      
      .btn-logout:hover {
         background: #e74c3c;
         color: white;
      }
      
      /* Responsive adjustments */
      @media (max-width: 768px) {
         .profile-content {
            flex-direction: column;
         }
         
         .profile-image {
            width: 180px;
            height: 180px;
         }
         
         .btn-group {
            flex-direction: column;
            align-items: center;
         }
         
         .btn-group a {
            width: 100%;
            max-width: 300px;
         }
      }
      
      @media (max-width: 480px) {
         .profile-container {
            padding: 1.5rem;
         }
         
         .profile-image {
            width: 150px;
            height: 150px;
            font-size: 50px;
         }
         
         .stats-container {
            grid-template-columns: 1fr;
         }
      }
   </style>
</head>
<body>

<?php @include 'header1.php'; ?>

<section class="profile-container">
   <div class="profile-header">
      <h2>My Profile</h2>
      <p>Manage your account information and order history</p>
   </div>
   
   <div class="profile-content">
      <div class="profile-image-section">
         <div class="profile-image">
            <?php if (!empty($user['image']) && file_exists("uploaded_img/" . $user['image'])): ?>
               <img src="uploaded_img/<?= htmlspecialchars($user['image']) ?>" alt="Profile Image" />
            <?php else: ?>
               <?php
               $initials = '';
               if (!empty($user['name'])) {
                  $words = explode(' ', $user['name']);
                  $initials .= strtoupper($words[0][0] ?? '');
                  $initials .= strtoupper($words[1][0] ?? '');
               }
               echo htmlspecialchars($initials ?: 'U');
               ?>
            <?php endif; ?>
         </div>
         
         <div class="user-details">
            <div class="detail-item">
               <span class="detail-label">ID:</span>
               <span class="detail-value"><?= htmlspecialchars($user['id']); ?></span>
            </div>
            <div class="detail-item">
               <span class="detail-label">Name:</span>
               <span class="detail-value"><?= htmlspecialchars($user['name']); ?></span>
            </div>
            <div class="detail-item">
               <span class="detail-label">Email:</span>
               <span class="detail-value"><?= htmlspecialchars($user['email']); ?></span>
            </div>
         </div>
      </div>
      
      <div class="stats-section">
         <h3 class="stats-title">Order Statistics</h3>
         
         <div class="stats-container">
            <div class="stat-card">
               <div class="stat-value"><?= $ordered ?></div>
               <div class="stat-label">Total Ordered</div>
            </div>
            
            <div class="stat-card">
               <div class="stat-value"><?= $delivered ?></div>
               <div class="stat-label">Delivered</div>
            </div>
            
            <div class="stat-card">
               <div class="stat-value"><?= $pending ?></div>
               <div class="stat-label">Pending</div>
            </div>
         </div>
         
         <div class="info-box">
            <p><i class="fas fa-info-circle"></i> Your order history and status</p>
         </div>
      </div>
   </div>
          <div class="btn-group">
   <a href="user_profile_update.php" class="btn-update">
      <i class="fas fa-user-edit"></i> Update Profile
   </a>
   <a href="seller_orders.php" class="btn-update" style="background:#ffb300; border-color:#ffb300;">
      <i class="fas fa-box"></i> Seller Orders
   </a>
   <a href="logout.php" class="btn-logout" onclick="return confirm('Are you sure you want to logout?');">
      <i class="fas fa-sign-out-alt"></i> Logout
   </a>
</div>

   
</section>

<?php @include 'footer.php'; ?>

</body>
</html>