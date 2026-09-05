<?php
@include 'config.php';
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle remove from wishlist
$message = '';
if (isset($_GET['remove'])) {
    $remove_id = (int)$_GET['remove'];
    $delete_stmt = $conn->prepare("DELETE FROM wishlist WHERE id = ? AND user_id = ?");
    $delete_stmt->execute([$remove_id, $user_id]);
    $message = "Item removed from wishlist.";
}

// Fetch wishlist items with product info
$stmt = $conn->prepare("SELECT w.id AS wishlist_id, p.* 
                        FROM wishlist w 
                        JOIN product p ON w.pid = p.id 
                        WHERE w.user_id = ?");
$stmt->execute([$user_id]);
$wishlist_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8" />
   <meta name="viewport" content="width=device-width, initial-scale=1" />
   <title>Your Wishlist</title>
   <link rel="stylesheet" href="css/style.css" />
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
   
<style>
   :root {
      --box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      --main-color: crimson;
      --hover-color: darkred;
   }

   .wishlist-section {
      max-width: 1200px;
      margin: 2rem auto;
      padding: 0 1rem;
   }

   .title {
      text-align: center;
      margin-bottom: 1.5rem;
      font-size: 2rem;
      font-weight: 600;
      color: #222;
   }

   .box-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 1.5rem;
   }

   .box {
      background: #fff;
      border-radius: 0.5rem;
      box-shadow: var(--box-shadow);
      padding: 1rem;
      text-align: center;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
   }

   .box:hover {
      transform: translateY(-5px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
   }

   .box img {
      width: 100%;
      height: 180px;
      object-fit: cover;
      border-radius: 0.5rem;
   }

   .name {
      margin-top: 10px;
      font-size: 1.2rem;
      font-weight: 600;
      color: #333;
   }

   .price {
      font-size: 1.1rem;
      font-weight: 700;
      color: var(--main-color);
      margin: 10px 0;
      text-align: center;
   }

   .btn {
      background-color: var(--main-color);
      color: white;
      padding: 0.8rem 1.5rem;
      border: none;
      border-radius: 0.5rem;
      cursor: pointer;
      font-weight: 600;
      transition: background-color 0.3s ease;
      text-decoration: none;
      display: inline-block;
      font-size: 1rem;
      margin: 5px 3px;
   }

   .btn:hover {
      background-color: var(--hover-color);
   }

   .message {
      text-align: center;
      margin-bottom: 20px;
      color: green;
      font-weight: 600;
   }

   .wishlist-total {
      max-width: 600px;
      margin: 2rem auto;
      text-align: center;
   }

   .wishlist-total .total-box {
      background: #f0f0f0;
      padding: 1.5rem;
      border-radius: 0.5rem;
      font-size: 2rem;
      color: #333;
      margin-bottom: 1rem;
      box-shadow: var(--box-shadow);
   }

   .wishlist-total .btn-group {
      display: flex;
      justify-content: center;
      gap: 1rem;
      flex-wrap: wrap;
   }

   .wishlist-total .btn-group a {
      padding: 0.8rem 1.5rem;
      font-size: 1.4rem;
      border-radius: 0.5rem;
   }

   .wishlist .box .fa-eye {
      display: none;
   }
</style>

</head>
<body>

<?php include 'header1.php'; ?>

<section class="wishlist-section">
   <h1 class="title">Your Wishlist</h1>

   <?php if ($message): ?>
      <div class="message"><?= htmlspecialchars($message) ?></div>
   <?php endif; ?>

   <div class="box-container">
      <?php if ($wishlist_items): ?>
         <?php foreach ($wishlist_items as $item): ?>
            <div class="box">
               <img src="uploaded_img/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
               <div class="name"><?= htmlspecialchars($item['name']) ?></div>
               <div class="price">৳ <?= number_format($item['price'], 2) ?></div>
               <a href="wishlist.php?remove=<?= $item['wishlist_id'] ?>" class="btn" onclick="return confirm('Remove this item from wishlist?');">Remove</a>
            </div>
         <?php endforeach; ?>
      <?php else: ?>
         <p style="text-align:center; font-size: 1.2rem; color:#555;">Your wishlist is empty.</p>
      <?php endif; ?>
   </div>
</section>

<div id="footer-placeholder"></div>
<script>
   // Load footer dynamically (same as home.php)
   fetch('footer.php')
      .then(response => response.text())
      .then(html => {
         document.getElementById('footer-placeholder').innerHTML = html;
      });
</script>

</body>
</html>
