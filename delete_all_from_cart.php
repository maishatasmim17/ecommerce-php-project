<?php
@include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

try {
    // Begin transaction
    $conn->beginTransaction();

    // Get all cart items to restore stock
    $stmt = $conn->prepare("
        SELECT c.quantity, c.pid, p.stock 
        FROM cart c
        JOIN product p ON c.pid = p.id
        WHERE c.user_id = ?
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Restore stock for each product
    foreach ($items as $item) {
        $newStock = $item['stock'] + $item['quantity'];
        $stmt = $conn->prepare("UPDATE product SET stock = ? WHERE id = ?");
        $stmt->execute([$newStock, $item['pid']]);
    }

    // Remove all items from cart
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);

    // Commit transaction
    $conn->commit();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>