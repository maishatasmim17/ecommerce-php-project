<?php
@include 'config.php';
session_start();

// Allow only logged-in users (seller)
if (!isset($_SESSION['user_id'])) {
   header('location: login.php');
   exit();
}

$user_id = $_SESSION['user_id'];

// Handle product upload
if (isset($_POST['add_product'])) {
   $name = $_POST['name'];
   $category = $_POST['category'];
   $details = $_POST['details'];
   $price = $_POST['price'];
   $stock = $_POST['stock']; // ✅ New

   $image_name = $_FILES['image']['name'];
   $image_tmp = $_FILES['image']['tmp_name'];
   $image_path = 'uploaded_img/' . $image_name;

   // Save product with stock
   $stmt = $conn->prepare("INSERT INTO product (name, category, details, price, stock, image, status, user_id) VALUES (?, ?, ?, ?, ?, ?, 'pending', ?)");
   $stmt->execute([$name, $category, $details, $price, $stock, $image_name, $user_id]);

   move_uploaded_file($image_tmp, $image_path);

   $message = "✅ Product uploaded successfully and is awaiting admin approval.";
}

// Fetch only this user's products
$stmt = $conn->prepare("SELECT * FROM product WHERE user_id = ? ORDER BY id DESC");
$stmt->execute([$user_id]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <title>Upload Product</title>
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="stylesheet" href="css/admin_style1.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</head>
<body>

<?php @include 'header1.php'; ?>

<?php if (isset($message)): ?>
<div class="message">
   <span><?= htmlspecialchars($message) ?></span>
   <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
</div>
<?php endif; ?>

<section class="add-products">
   <h1 class="title">Upload Your Product</h1>
   <form action="" method="POST" enctype="multipart/form-data">
      <div class="flex">
         <div class="inputBox">
            <input type="text" name="name" class="box" required placeholder="Enter product name">
            <select name="category" class="box" required>
               <option value="" disabled selected>Select category</option>
               <option value="Saree">Saree</option>
               <option value="Jewellery">Jewellery</option>
               <option value="Bag">Bag</option>
            </select>
         </div>
         <div class="inputBox">
   <input type="file" name="image" class="box file-input" required accept="image/jpg, image/jpeg, image/png">
</div>
<div class="inputBox">
   <input type="number" name="price" min="0" class="box" required placeholder="Enter product price">
   <input type="number" name="stock" min="0" class="box" required placeholder="Enter stock quantity">
</div>

      </div>
      <textarea name="details" class="box" required placeholder="Enter product details" cols="30" rows="10"></textarea>
      <input type="submit" name="add_product" value="Upload Product" class="btn">
   </form>
</section>

<section class="show-products">
   <h1 class="title">Your Uploaded Products</h1>
   <div class="box-container">

   <?php foreach ($products as $product): ?>
      <div class="box">
         <div class="price"><?= htmlspecialchars($product['price']) ?> TK/-</div>
         <img src="uploaded_img/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
         <div class="name"><?= htmlspecialchars($product['name']) ?></div>
         <div class="cat"><?= htmlspecialchars($product['category']) ?></div>
         <div class="details"><?= htmlspecialchars($product['details']) ?></div>
         <div class="stock">Stock: <strong><?= htmlspecialchars($product['stock']) ?></strong></div> <!-- ✅ Display stock -->
         <div class="status">Status: <strong><?= ucfirst($product['status']) ?></strong></div>
         <div class="flex-btn">
            <a href="#" class="option-btn">Update</a>
            <a href="#" class="delete-btn" onclick="return confirm('Delete this product?');">Delete</a>
         </div>
      </div>
   <?php endforeach; ?>

   </div>
</section>

<?php include 'footer.php'; ?>
<script src="js/script.js"></script>

</body>
</html>
