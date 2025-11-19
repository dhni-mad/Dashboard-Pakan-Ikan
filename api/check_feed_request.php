<?php
require '../config/db.php';

try {
    $stmt = $db->prepare("SELECT manual_feed_request FROM status_sistem WHERE id = 1");
    $stmt->execute();
    $status = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo $status['manual_feed_request'];

} catch(PDOException $e) {
    echo "0"; 
}
?>