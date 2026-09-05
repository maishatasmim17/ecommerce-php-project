<?php
@include 'config.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$itemId = $data['itemId'] ?? null;

if (!$itemId || !is_numeric($itemId)) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

try {
    // Begin transaction
    $conn->beginTransaction();

    // Get cart item and stock info
    $stmt = $conn->prepare("
        SELECT c.quantity, c.pid, p.stock 
        FROM cart c
        JOIN product p ON c.pid = p.id
        WHERE c.id = ? AND c.user_id = ?
    ");
    $stmt->execute([$itemId, $_SESSION['user_id']]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$item) {
        throw new Exception('Item not found in cart or does not belong to user');
    }

    $quantity = (int)$item['quantity'];
    $productId = (int)$item['pid'];
    $currentStock = (int)$item['stock'];

    // Remove item from cart
    $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $stmt->execute([$itemId, $_SESSION['user_id']]);

    // Restore product stock
    $newStock = $currentStock + $quantity;
    $stmt = $conn->prepare("UPDATE product SET stock = ? WHERE id = ?");
    $stmt->execute([$newStock, $productId]);

    // Commit transaction
    $conn->commit();

    // Get updated cart count
    $stmt = $conn->prepare("SELECT COUNT(*) FROM cart WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $cartCount = (int)$stmt->fetchColumn();

    echo json_encode([
        'success' => true,
        'cartCount' => $cartCount,
        'message' => 'Item removed and stock restored successfully'
    ]);
} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
