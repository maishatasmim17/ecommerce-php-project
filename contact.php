<?php
@include 'config.php';
session_start();

if(isset($_POST['send'])) {
    $name    = $_POST['name'];
    $email   = $_POST['email'];
    $number  = $_POST['number'];
    $msg     = $_POST['msg'];

    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;

    try {
        $stmt = $conn->prepare("INSERT INTO message (user_id, name, email, number, message) VALUES (:user_id, :name, :email, :number, :msg)");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':number', $number);
        $stmt->bindParam(':msg', $msg);

        $stmt->execute();

        echo "<script>alert('Message sent successfully!'); window.location.href='contact.php';</script>";
    } catch (PDOException $e) {
        echo "<script>alert('Message failed to send: " . $e->getMessage() . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Contact</title>

   <!-- Font Awesome CDN -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- Your External CSS -->
   <link rel="stylesheet" href="css/style1.css">
</head>
<body>

<section class="contact">
   <h1 class="title">Feedback</h1>

   <form action="" method="POST">
      <input type="text" name="name" class="box" required placeholder="Enter your name">
      <input type="email" name="email" class="box" required placeholder="Enter your email">
      <input type="text" name="number" class="box" required placeholder="Enter your phone number">
      <textarea name="msg" class="box" required placeholder="Enter your message" cols="30" rows="10"></textarea>
      <input type="submit" value="Send Message" class="btn" name="send">
   </form>
</section>

</body>
</html>
