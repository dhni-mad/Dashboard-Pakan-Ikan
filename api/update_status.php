<?php
// api/update_status.php
require '../config/db.php';

// Ambil data (kita gunakan 'jarak' sebagai nilai cm mentah)
$jarak = isset($_GET['jarak']) ? (int)$_GET['jarak'] : null;
$kekeruhan = isset($_GET['kekeruhan']) ? (int)$_GET['kekeruhan'] : null;

// --- Logika Konversi Jarak (sesuai permintaan Anda) ---
$feed_status_text = "Penuh"; // Default
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
// --- Akhir Logika Jarak ---

$water_status_text = "Jernih";
if ($kekeruhan !== null && $kekeruhan > 700) {
    $water_status_text = "Keruh";
}

try {
    // 1. Update status terkini (sekarang termasuk jarak_cm)
    $stmt_status = $db->prepare(
        "UPDATE status_sistem SET 
            feed_status = ?, 
            water_status = ?, 
            jarak_cm = ? 
        WHERE id = 1"
    );
    $stmt_status->execute([$feed_status_text, $water_status_text, $jarak]);
    
    // 2. Simpan log kekeruhan (jika ada)
    if ($kekeruhan !== null) {
        $stmt_log_kekeruhan = $db->prepare("INSERT INTO log_kekeruhan_air (nilai_kekeruhan) VALUES (?)");
        $stmt_log_kekeruhan->execute([$kekeruhan]);
    }

    // 3. [BARU] Simpan log jarak (jika ada)
    if ($jarak !== null) {
        $stmt_log_jarak = $db->prepare("INSERT INTO log_jarak_pakan (jarak_cm) VALUES (?)");
        $stmt_log_jarak->execute([$jarak]);
    }
    
    echo "Update OK";

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>