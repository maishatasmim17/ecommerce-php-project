<?php
@include 'config.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header('Location: login.php');
    exit;
}

// Fetch existing user data from DB
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$fetch_profile = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle profile update
if (isset($_POST['update_profile'])) {
    $name         = $_POST['name'];
    $email        = $_POST['email'];
    $update_pass  = $_POST['update_pass'];
    $new_pass     = $_POST['new_pass'];
    $confirm_pass = $_POST['confirm_pass'];
    $old_image    = $_POST['old_image'];

    // Handle image
    $image        = $_FILES['image']['name'];
    $image_tmp    = $_FILES['image']['tmp_name'];
    $image_folder = 'uploaded_img/' . $image;

    if (!empty($image)) {
        move_uploaded_file($image_tmp, $image_folder);
    } else {
        $image = $old_image;
    }

    // Validate password update
    if (!empty($update_pass) || !empty($new_pass) || !empty($confirm_pass)) {
        if (md5($update_pass) !== $fetch_profile['password']) {
            $message = '❌ পুরাতন পাসওয়ার্ড সঠিক নয়!';
        } elseif ($new_pass !== $confirm_pass) {
            $message = '❌ নতুন ও কনফার্ম পাসওয়ার্ড মিলছে না!';
        } else {
            $final_pass = md5($new_pass); // Hash new password
        }
    } else {
        $final_pass = $fetch_profile['password']; // Keep old password
    }

    // If everything is fine, update DB
    if (!isset($message)) {
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, password = ?, image = ? WHERE id = ?");
        $stmt->execute([$name, $email, $final_pass, $image, $user_id]);
        $message = '✅ প্রোফাইল সফলভাবে আপডেট হয়েছে!';

        // Reload updated data
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $fetch_profile = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>

<!-- HTML PART -->
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8" />
   <meta http-equiv="X-UA-Compatible" content="IE=edge" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0" />
   <title>Update Profile</title>
   <link rel="stylesheet" href="css/components1.css" />
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
</head>
<body>

<?php include 'header.php'; ?>

<section class="update-profile">
   <h1 class="title">Update Profile</h1>

   <?php if (isset($message)) : ?>
      <div class="message">
         <span><?= htmlspecialchars($message) ?></span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
   <?php endif; ?>

   <form action="" method="POST" enctype="multipart/form-data">
      <img src="uploaded_img/<?= htmlspecialchars($fetch_profile['image']) ?>" alt="profile" />
      <div class="flex">
         <div class="inputBox">
            <span>Username :</span>
            <input type="text" name="name" value="<?= htmlspecialchars($fetch_profile['name']) ?>" required class="box" />
            <span>Email :</span>
            <input type="email" name="email" value="<?= htmlspecialchars($fetch_profile['email']) ?>" required class="box" />
            <span>Update Picture :</span>
            <input type="file" name="image" accept="image/jpg, image/jpeg, image/png" class="box" />
            <input type="hidden" name="old_image" value="<?= htmlspecialchars($fetch_profile['image']) ?>" />
         </div>

         <div class="inputBox">
            <span>Old Password :</span>
            <input type="password" name="update_pass" placeholder="Enter your current password" class="box" />
            <span>New Password :</span>
            <input type="password" name="new_pass" placeholder="Enter new password" class="box" />
            <span>Confirm Password :</span>
            <input type="password" name="confirm_pass" placeholder="Confirm new password" class="box" />
         </div>
      </div>

      <div class="flex-btn">
         <input type="submit" class="btn" value="Update Profile" name="update_profile" />
         <a href="home.php" class="option-btn">Go Back</a>
      </div>
   </form>
</section>

<?php include 'footer.php'; ?>
<script src="js/script.js"></script>
</body>
</html>