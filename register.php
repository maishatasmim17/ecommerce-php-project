<?php
@include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name     = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
    $email    = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $pass     = md5($_POST['pass']);
    $cpass    = md5($_POST['cpass']);

    $message = [];

    // ছবি আপলোডের জন্য
    $image = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];

    // ছবি নাম ইউনিক করা
    $image_ext = pathinfo($image, PATHINFO_EXTENSION);
    $new_image_name = uniqid('profile_', true) . '.' . $image_ext;
    $upload_dir = 'uploaded_img/';

    // ইউজার ইমেইল চেক
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        $message[] = "⚠️ User already exists!";
    } else {
        if ($pass !== $cpass) {
            $message[] = "❌ Passwords do not match!";
        } else {
            // ছবি আপলোড করলে ফাইল সার্ভারে সেভ কর
            if (!empty($image)) {
                if (move_uploaded_file($image_tmp, $upload_dir . $new_image_name)) {
                    $image_to_save = $new_image_name;
                } else {
                    $image_to_save = null;
                    $message[] = "❌ Failed to upload image.";
                }
            } else {
                $image_to_save = null; // বা ডিফল্ট ছবি নাম দিতে পারো
            }

            // ইনসার্ট ডেটা
            $user_type = 'user';

            $insert = $conn->prepare("INSERT INTO users(name, email, password, user_type, image) VALUES(?, ?, ?, ?, ?)");
            $insert->execute([$name, $email, $pass, $user_type, $image_to_save]);

            if ($insert) {
                $message[] = "✅ Registered successfully!";
            } else {
                $message[] = "❌ Registration failed!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0" />
   <title>Register</title>
   <link rel="stylesheet" href="css/components.css" />
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
</head>
<body>

<section class="form-container">
   <form action="" method="POST" enctype="multipart/form-data">
      <h3>Register Now</h3>

      <?php
      if (!empty($message)) {
         foreach ($message as $msg) {
            echo '<p style="color:red;">' . $msg . '</p>';
         }
      }
      ?>

      <input type="text" name="name" class="box" placeholder="Enter your name" required>
      <input type="email" name="email" class="box" placeholder="Enter your email" required>
      <input type="password" name="pass" class="box" placeholder="Enter your password" required>
      <input type="password" name="cpass" class="box" placeholder="Confirm your password" required>
      <input type="file" name="image" class="box" accept="image/png, image/jpeg">

      <input type="submit" name="submit" value="Register Now" class="btn">

      <p>Already have an account? <a href="login.php">Login now</a></p>
   </form>
</section>

</body>
</html>
