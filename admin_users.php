<?php
@include 'config.php';
session_start();

if (!isset($_SESSION['user_id'], $_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
   header('Location: login.php');
   exit;
}

// Search logic
$search = $_GET['search'] ?? '';
$sql = "SELECT * FROM users WHERE name LIKE ? OR email LIKE ?";
$stmt = $conn->prepare($sql);
$stmt->execute(["%$search%", "%$search%"]);
$users = $stmt->fetchAll();

function getOrderStats($conn, $user_id) {
   $total = $conn->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
   $total->execute([$user_id]);
   $ordered = $total->fetchColumn();

   $completed = $conn->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ? AND payment_status = 'completed'");
   $completed->execute([$user_id]);
   $delivered = $completed->fetchColumn();

   $pending = $ordered - $delivered;

   return [$ordered, $delivered, $pending];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <title>Admin - User Search</title>
   <link rel="stylesheet" href="css/style.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
   <style>
      body {
         font-family: Arial, sans-serif;
         background: #f4f4f4;
         padding: 20px;
      }
      .search-box {
         text-align: center;
         margin-bottom: 20px;
      }
      input[type="text"] {
         padding: 10px;
         width: 300px;
         font-size: 16px;
         border-radius: 5px;
         border: 1px solid #ccc;
      }
      .user-card {
         background: white;
         padding: 20px;
         border-radius: 10px;
         box-shadow: 0 0 10px rgba(0,0,0,0.1);
         margin-bottom: 20px;
         max-width: 600px;
         margin-left: auto;
         margin-right: auto;
      }
      .user-row {
         display: flex;
         align-items: center;
      }
      .user-img {
         width: 80px;
         height: 80px;
         border-radius: 50%;
         object-fit: cover;
         margin-right: 20px;
      }
      .user-info p {
         margin: 6px 0;
         font-size: 16px;
      }
   </style>
</head>
<body>

<div class="search-box">
   <form method="GET" action="">
      <input type="text" name="search" placeholder="Search by name or email" value="<?= htmlspecialchars($search) ?>">
      <button type="submit">Search</button>
   </form>
</div>

<?php if (empty($users)): ?>
   <p style="text-align:center;">No users found.</p>
<?php else: ?>
   <?php foreach ($users as $user): 
      [$ordered, $delivered, $pending] = getOrderStats($conn, $user['id']);
   ?>
      <div class="user-card">
         <div class="user-row">
            <img src="uploaded_img/<?= htmlspecialchars($user['image']) ?>" alt="<?= htmlspecialchars($user['name']) ?>" class="user-img">
            <div class="user-info">
               <p><strong>Name:</strong> <?= htmlspecialchars($user['name']) ?></p>
               <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
               <p><strong>Total Orders:</strong> <?= $ordered ?></p>
               <p><strong>Delivered:</strong> <?= $delivered ?></p>
               <p><strong>Pending:</strong> <?= $pending ?></p>
            </div>
         </div>
      </div>
   <?php endforeach; ?>
<?php endif; ?>

</body>
</html>