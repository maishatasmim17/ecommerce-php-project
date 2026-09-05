<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
@include 'config.php';

// Fetch notifications if user is logged in
$unread_count = 0;
$notifications = [];

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    // Fetch notifications
    $notification_stmt = $conn->prepare("
        SELECT * FROM notifications 
        WHERE user_id = ? 
        ORDER BY created_at DESC 
        LIMIT 5
    ");
    $notification_stmt->execute([$user_id]);
    $notifications = $notification_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Count unread notifications
    foreach ($notifications as $notif) {
        if (!$notif['is_read']) $unread_count++;
    }
}
?>

<!-- Include FontAwesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

<!-- Header CSS -->
<style>
   .header {
      background-color: #fff;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      padding: 15px 20px;
      position: relative;
      z-index: 1000;
   }

   .header .flex {
      display: flex;
      align-items: center;
      justify-content: space-between;
      max-width: 1200px;
      margin: auto;
   }

   .header .logo {
      font-size: 24px;
      font-weight: bold;
      color: #333;
      text-decoration: none;
   }

   .navbar a {
      margin: 0 10px;
      font-size: 17px;
      color: #444;
      text-decoration: none;
      transition: color 0.3s;
   }

   .navbar a:hover {
      color: #007BFF;
   }

   .icons {
      display: flex;
      align-items: center;
      gap: 15px;
      position: relative;
   }

   .icons a, .icons i {
      color: #333;
      font-size: 18px;
      text-decoration: none;
   }

   .fa-user, .fa-bars {
      cursor: pointer;
   }

   #wishlist-count, #cart-count, .badge {
      font-size: 13px;
      color: #007BFF;
      margin-left: 3px;
   }

   /* Notification Styles */
   .notification-area {
      position: relative;
      display: inline-block;
   }
   
   .bell-icon {
      font-size: 20px;
      cursor: pointer;
      position: relative;
      color: #333;
      display: flex;
      align-items: center;
   }
   
   .badge {
      position: absolute;
      top: -8px;
      right: -8px;
      background: red;
      color: white;
      border-radius: 50%;
      width: 20px;
      height: 20px;
      text-align: center;
      font-size: 12px;
      line-height: 20px;
   }
   
   .notifications-box {
      display: none;
      position: absolute;
      right: 0;
      top: 100%;
      width: 350px;
      max-height: 400px;
      overflow-y: auto;
      background: white;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      border-radius: 4px;
      z-index: 1000;
      padding: 10px;
   }
   
   .notification {
      padding: 12px 15px;
      border-bottom: 1px solid #eee;
      cursor: pointer;
      font-size: 14px;
   }
   
   .notification.unread {
      background-color: #f8f9ff;
      font-weight: 500;
   }
   
   .notification small {
      display: block;
      color: #888;
      margin-top: 5px;
      font-size: 12px;
   }

   @media (max-width: 768px) {
      .navbar {
         display: none;
      }
      .icons {
         gap: 10px;
      }
      .notifications-box {
         width: 280px;
         right: -10px;
      }
   }

   .icon-btn {
   display: flex;
   align-items: center;
   justify-content: center;
   width: 40px;
   height: 40px;
   border-radius: 50%;
   background-color: #f3f3f3;
   color: #333;
   font-size: 18px;
   text-decoration: none;
   position: relative;
   transition: background-color 0.2s;
}

.icon-btn:hover {
   background-color: #e0e0e0;
   color: #007BFF;
}

.icon-btn span {
   font-size: 12px;
   position: absolute;
   top: -4px;
   right: -4px;
   background: red;
   color: white;
   border-radius: 50%;
   padding: 2px 5px;
   line-height: 1;
}

</style>

<!-- Header HTML -->
<header class="header">
   <div class="flex">
      <a href="home.php" class="logo">Crafty's Shelf</a>

      <nav class="navbar">
         <a href="home.php">home</a>
         <a href="orders.php">orders</a>
         <a href="about.php">about</a>
         <a href="contact.php">contact</a>
      </nav>

      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         
         <!-- Notification Bell -->
         <?php if (isset($_SESSION['user_id'])): ?>
         <div class="notification-area">
            <div class="bell-icon" onclick="toggleNotifications()">
               <i class="fas fa-bell"></i>
               <?php if ($unread_count > 0): ?>
                  <span class="badge"><?= $unread_count ?></span>
               <?php endif; ?>
            </div>
            
            <div class="notifications-box" id="notificationBox">
               <?php if (empty($notifications)): ?>
                  <div class="notification">No notifications</div>
               <?php else: ?>
                  <?php foreach ($notifications as $notif): ?>
                     <div class="notification <?= $notif['is_read'] ? 'read' : 'unread' ?>" 
                          onclick="markAsRead(<?= $notif['id'] ?>, this)">
                        <?= htmlspecialchars($notif['message']) ?>
                        <small><?= date('d M, h:i A', strtotime($notif['created_at'])) ?></small>
                     </div>
                  <?php endforeach; ?>
               <?php endif; ?>
            </div>
         </div>
         <?php endif; ?>
         
         <a href="user_profile.php" class="icon-btn" title="My Profile"><i class="fas fa-user"></i></a>
<a href="admin_products.php" class="icon-btn" title="Sell Product"><i class="fas fa-tags"></i></a>
<a href="search_page.php" class="icon-btn" title="Search"><i class="fas fa-search"></i></a>
<a href="wishlist.php" class="icon-btn" title="Wishlist">
   <i class="fas fa-heart"></i><span id="wishlist-count">(0)</span>
</a>
<a href="cart.php" class="icon-btn" title="Cart">
   <i class="fas fa-shopping-cart"></i><span id="cart-count">(0)</span>
</a>

      </div>
   </div>
</header>

<script>
function toggleNotifications() {
    const box = document.getElementById('notificationBox');
    box.style.display = box.style.display === 'block' ? 'none' : 'block';
}

function markAsRead(notifId, element) {
    // Optimistic UI update
    element.classList.remove('unread');
    element.classList.add('read');
    
    // Update badge count
    const badge = document.querySelector('.badge');
    if (badge) {
        const newCount = parseInt(badge.innerText) - 1;
        badge.innerText = newCount > 0 ? newCount : '';
        if (newCount <= 0) badge.remove();
    }
    
    // Send AJAX request to mark as read
    fetch('mark_read.php?id=' + notifId)
        .catch(error => console.error('Error:', error));
}

// Close notifications when clicking outside
document.addEventListener('click', (e) => {
    const notifArea = document.querySelector('.notification-area');
    if (notifArea && !notifArea.contains(e.target)) {
        document.getElementById('notificationBox').style.display = 'none';
    }
});
</script>