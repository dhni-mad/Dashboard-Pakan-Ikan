<?php
require '../config/db.php';

$berat = isset($_GET['berat']) ? (float)$_GET['berat'] : 0;

if ($berat > 0) {
    try {
        $stmt = $db->prepare("INSERT INTO log_pemberian_pakan (berat_pakan) VALUES (?)");
        $stmt->execute([$berat]);
        
        echo "Log pakan seberat $berat gram berhasil disimpan.";

    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Berat pakan tidak valid.";
}
?>