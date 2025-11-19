<?php
require '../config/db.php';

$jarak = isset($_GET['jarak']) ? (int)$_GET['jarak'] : null;
$kekeruhan = isset($_GET['kekeruhan']) ? (int)$_GET['kekeruhan'] : null;

$feed_status_text = "Penuh";
if ($jarak === null) {
    $feed_status_text = "N/A";
} else if ($jarak >= 15) {
    $feed_status_text = "Habis";
} else if ($jarak >= 13) {
    $feed_status_text = "Hampir Habis";
} else if ($jarak >= 10) {
    $feed_status_text = "Sedang";
} else if ($jarak >= 5) {
    $feed_status_text = "Masih Banyak";
} else if ($jarak >= 1) {
    $feed_status_text = "Penuh";
}

$water_status_text = "Jernih";
if ($kekeruhan !== null && $kekeruhan > 700) {
    $water_status_text = "Keruh";
}

try {
    $stmt_status = $db->prepare(
        "UPDATE status_sistem SET 
            feed_status = ?, 
            water_status = ?, 
            jarak_cm = ? 
        WHERE id = 1"
    );
    $stmt_status->execute([$feed_status_text, $water_status_text, $jarak]);
    
    if ($kekeruhan !== null) {
        $stmt_log_kekeruhan = $db->prepare("INSERT INTO log_kekeruhan_air (nilai_kekeruhan) VALUES (?)");
        $stmt_log_kekeruhan->execute([$kekeruhan]);
    }

    if ($jarak !== null) {
        $stmt_log_jarak = $db->prepare("INSERT INTO log_jarak_pakan (jarak_cm) VALUES (?)");
        $stmt_log_jarak->execute([$jarak]);
    }
    
    echo "Update OK";

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>