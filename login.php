<?php
@include 'config.php';
session_start();

if (isset($_POST['submit'])) {
   $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
   $pass = md5(filter_var($_POST['pass'], FILTER_SANITIZE_STRING)); // Match with registration

   $sql = "SELECT * FROM users WHERE email = ? AND password = ?";
   $stmt = $conn->prepare($sql);
   $stmt->execute([$email, $pass]);
   $row = $stmt->fetch(PDO::FETCH_ASSOC);

   if ($row) {
      // Set session variables
      $_SESSION['user_id'] = $row['id'];
      $_SESSION['user_type'] = $row['user_type'];

      if ($row['user_type'] === 'admin') {
         header('Location: main_admin_page.php');
         exit;
      } else {
         header('Location: home.php');
         exit;
      }
   } else {
      $message = '❌ Incorrect email or password!';
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8" />
   <meta http-equiv="X-UA-Compatible" content="IE=edge" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0" />
   <title>Login</title>

   <link rel="stylesheet" href="css/components.css" />
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
</head>
<body>

<?php if(isset($message)): ?>
<div class="message">
   <span><?= htmlspecialchars($message) ?></span>
   <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
</div>
<?php endif; ?>

<section class="form-container">
   <form action="" method="POST">
      <h3>Login Now</h3>

      <input type="email" name="email" class="box" placeholder="Enter your email" required>
      <input type="password" name="pass" class="box" placeholder="Enter your password" required>

      <input type="submit" name="submit" value="Login Now" class="btn">

      <p>Don't have an account? <a href="register.php">Register now</a></p>
   </form>
</section>

</body>
</html>
