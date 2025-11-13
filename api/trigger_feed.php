<?php
// api/trigger_feed.php
require '../config/db.php';

// Pastikan ini adalah request POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Set flag 'manual_feed_request' menjadi 1
        $stmt = $db->prepare("UPDATE status_sistem SET manual_feed_request = 1 WHERE id = 1");
        $stmt->execute();
        
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Permintaan pakan manual terkirim.']);
        
    } catch(PDOException $e) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    // Jika bukan POST, tolak
    header("HTTP/1.1 405 Method Not Allowed");
    echo "Method Not Allowed";
}
?>