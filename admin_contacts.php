<?php
@include 'config.php';
session_start();

// Delete message if requested
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $delete_stmt = $conn->prepare("DELETE FROM message WHERE id = :id");
    $delete_stmt->bindParam(':id', $delete_id);
    $delete_stmt->execute();
    header('Location: admin_contacts.php');
    exit;
}

// Fetch all messages
$stmt = $conn->prepare("SELECT * FROM message ORDER BY id DESC");
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8" />
   <meta http-equiv="X-UA-Compatible" content="IE=edge" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0" />
   <title>Messages</title>

   <!-- Font Awesome CDN -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />

   <!-- Custom CSS -->
   <link rel="stylesheet" href="css/admin_style1.css" />
</head>
<body>

<header class="header">
  <div class="flex">
    <a href="admin_page.php" class="logo">Admin<span>Panel</span></a>

    <nav class="navbar">
      <a href="home.php">Home</a>
      <a href="main_admin_page.php">Dashboard</a>
      <a href="admin_approve.php">Approved Order</a>
      <a href="admin_contacts.php">Messages</a>
    </nav>

    <div class="icons">
      <div id="menu-btn" class="fas fa-bars"></div>
      <div id="user-btn" class="fas fa-user"></div>
    </div>

    <div class="profile">
      <img src="images/profile.jpeg" alt="Profile Image" />
      <p>Admin Name</p>
      <a href="admin_update_profile.php" class="btn">Update Profile</a>
      <a href="logout.php" class="delete-btn">Logout</a>
      <div class="flex-btn">
        <a href="login.php" class="option-btn">Login</a>
        <a href="register.php" class="option-btn">Register</a>
      </div>
    </div>
  </div>
</header>

<section class="messages">
   <h1 class="title">Messages</h1>

   <div class="box-container">
      <?php if (count($messages) > 0): ?>
         <?php foreach ($messages as $msg): ?>
            <article class="box" data-id="<?= $msg['id']; ?>">
               <div class="content">
                  <p>User ID: <span><?= $msg['user_id']; ?></span></p>
                  <p>Name: <span><?= htmlspecialchars($msg['name']); ?></span></p>
                  <p>Number: <span><?= htmlspecialchars($msg['number']); ?></span></p>
                  <p>Email: <span><?= htmlspecialchars($msg['email']); ?></span></p>
                  <p class="message-text">Message: <span><?= nl2br(htmlspecialchars($msg['message'])); ?></span></p>
               </div>
               <a href="admin_contacts.php?delete=<?= $msg['id']; ?>" class="delete-btn" onclick="return confirm('Delete this message?');">Delete</a>
            </article>
         <?php endforeach; ?>
      <?php else: ?>
         <p class="empty">You have no messages!</p>
      <?php endif; ?>
   </div>
</section>

<!-- Optional JS -->
<script src="js/script.js"></script>

</body>
</html>
