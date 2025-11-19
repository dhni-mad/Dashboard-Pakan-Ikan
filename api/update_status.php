<?php
// api/update_status.php
require '../config/db.php';

$jarak = isset($_GET['jarak']) ? (int)$_GET['jarak'] : null;
$kekeruhan = isset($_GET['kekeruhan']) ? (int)$_GET['kekeruhan'] : null;

// --- [MODIFIKASI] Ambil nilai batas dari database ---
$stmt_setting = $db->prepare("SELECT * FROM pengaturan WHERE id = 1");
$stmt_setting->execute();
$setting = $stmt_setting->fetch(PDO::FETCH_ASSOC);

$BATAS_HABIS = $setting['batas_pakan_habis']; // Ambil dari DB (misal: 15)
$BATAS_KERUH = $setting['batas_air_keruh'];   // Ambil dari DB (misal: 700)
// ---------------------------------------------------

// Logika Konversi Jarak (Dinamis)
$feed_status_text = "Penuh";
if ($jarak === null) {
    $feed_status_text = "N/A";
} else if ($jarak >= $BATAS_HABIS) {
    $feed_status_text = "Habis";
} else if ($jarak >= ($BATAS_HABIS - 2)) { // Logika relatif (misal: 15-2 = 13)
    $feed_status_text = "Hampir Habis";
} else if ($jarak >= ($BATAS_HABIS - 5)) { // (misal: 15-5 = 10)
    $feed_status_text = "Sedang";
} else if ($jarak >= 5) {
    $feed_status_text = "Masih Banyak";
} else {
    $feed_status_text = "Penuh";
}

// Logika Air (Dinamis)
$water_status_text = "Jernih";
if ($kekeruhan !== null && $kekeruhan > $BATAS_KERUH) {
    $water_status_text = "Keruh";
}

// ... (Sisa kode simpan ke database sama seperti sebelumnya) ...
try {
    $stmt_status = $db->prepare("UPDATE status_sistem SET feed_status = ?, water_status = ?, jarak_cm = ? WHERE id = 1");
    $stmt_status->execute([$feed_status_text, $water_status_text, $jarak]);
    
    // ... (Kode log kekeruhan & jarak tetap sama) ...
    // Pastikan blok INSERT log ada di sini
    
    echo "Update OK";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>