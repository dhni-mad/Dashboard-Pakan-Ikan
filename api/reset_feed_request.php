<?php
// api/reset_feed_request.php
require '../config/db.php';

try {
    // Reset flag ke 0
    $stmt = $db->prepare("UPDATE status_sistem SET manual_feed_request = 0 WHERE id = 1");
    $stmt->execute();
    echo "Reset OK";
} catch(PDOException $e) {
    echo "Error";
}
?>