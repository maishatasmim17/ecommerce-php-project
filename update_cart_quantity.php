<?php
@include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$itemId = $data['itemId'] ?? null;
$newQuantity = $data['quantity'] ?? null;

if (!$itemId || !$newQuantity) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

try {
    // Begin transaction
    $conn->beginTransaction();

    // Get current cart item and product stock
    $stmt = $conn->prepare("
        SELECT c.quantity AS old_quantity, p.stock, p.id AS product_id 
        FROM cart c
        JOIN product p ON c.pid = p.id
        WHERE c.id = ?
    ");
    $stmt->execute([$itemId]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$item) {
        throw new Exception('Item not found in cart');
    }

    $oldQuantity = $item['old_quantity'];
    $currentStock = $item['stock'];
    $productId = $item['product_id'];

    // Calculate quantity difference
    $quantityDiff = $newQuantity - $oldQuantity;

    // Check if enough stock is available
    if ($quantityDiff > $currentStock) {
        throw new Exception('Not enough stock available');
    }

    // Update cart quantity
    $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
    $stmt->execute([$newQuantity, $itemId]);

    // Update product stock
    $newStock = $currentStock - $quantityDiff;
    $stmt = $conn->prepare("UPDATE product SET stock = ? WHERE id = ?");
    $stmt->execute([$newStock, $productId]);

    // Commit transaction
    $conn->commit();

    // Get updated cart count
    $stmt = $conn->prepare("SELECT COUNT(*) FROM cart WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $cartCount = $stmt->fetchColumn();

    echo json_encode([
        'success' => true,
        'newStock' => $newStock,
        'cartCount' => $cartCount
    ]);
} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>