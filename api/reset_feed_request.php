<?php
require '../config/db.php';

try {
    $stmt = $db->prepare("UPDATE status_sistem SET manual_feed_request = 0 WHERE id = 1");
    $stmt->execute();
    echo "Reset OK";
} catch(PDOException $e) {
    echo "Error";
}
?>