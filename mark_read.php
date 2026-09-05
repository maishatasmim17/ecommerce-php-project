<?php
// mark_read.php
@include 'config.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    try {
        $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE id = ?");
        $stmt->execute([$id]);
        echo "OK";
    } catch (PDOException $e) {
        error_log("Notification update error: " . $e->getMessage());
    }
}
?>