<?php
@include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pid'], $_POST['name'], $_POST['price'], $_POST['image'])) {
    $user_id = $_SESSION['user_id'];
    $pid = $_POST['pid'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $image = $_POST['image'];
    $quantity = 1;

    // Direct insert into cart (no check for duplicates, no stock check)
    $stmt = $conn->prepare("INSERT INTO cart (user_id, pid, name, price, quantity, image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $pid, $name, $price, $quantity, $image]);

    $_SESSION['message'] = 'Item added to cart!';
    header('Location: home.php');
    exit;
} else {
    $_SESSION['message'] = 'Invalid request.';
    header('Location: home.php');
    exit;
}
