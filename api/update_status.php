<?php
// api/update_status.php
require '../config/db.php';

// Ambil parameter dari ESP8266
$jarak = isset($_GET['jarak']) ? (int)$_GET['jarak'] : null;
$kekeruhan = isset($_GET['kekeruhan']) ? (int)$_GET['kekeruhan'] : null;

// Ambil pengaturan batas dari database
try {
    $stmt_setting = $db->prepare("SELECT * FROM pengaturan WHERE id = 1");
    $stmt_setting->execute();
    $setting = $stmt_setting->fetch(PDO::FETCH_ASSOC);

    $BATAS_HABIS = $setting['batas_pakan_habis']; 
    $BATAS_KERUH = $setting['batas_air_keruh'];
} catch(PDOException $e) {
    // Nilai default jika gagal ambil db
    $BATAS_HABIS = 15;
    $BATAS_KERUH = 700;
}

// 1. LOGIKA STATUS PAKAN (Jarak)
$feed_status_text = "Penuh";
if ($jarak === null) {
    $feed_status_text = "N/A";
} else if ($jarak >= $BATAS_HABIS) {
    $feed_status_text = "Habis";
} else if ($jarak >= ($BATAS_HABIS - 2)) {
    $feed_status_text = "Hampir Habis";
} else if ($jarak >= 5) {
    $feed_status_text = "Sedang";
} else {
    $feed_status_text = "Penuh";
}

// 2. LOGIKA STATUS AIR (Kekeruhan)
// Sesuaikan logika (< atau >) dengan sensor Anda nanti
$water_status_text = "Jernih";
if ($kekeruhan !== null) {
    // Jika nilai sensor LEBIH KECIL dari batas, maka Keruh
    if ($kekeruhan < $BATAS_KERUH) {
        $water_status_text = "Keruh";
    }
}

try {
    // A. UPDATE STATUS TERKINI (Untuk Halaman Dashboard Utama)
    $stmt_status = $db->prepare("UPDATE status_sistem SET feed_status = ?, water_status = ?, jarak_cm = ? WHERE id = 1");
    $stmt_status->execute([$feed_status_text, $water_status_text, $jarak]);
    
    // B. INSERT LOG HISTORIS (INI YANG HILANG SEBELUMNYA - Untuk Grafik)
    
    // Simpan Log Jarak
    if ($jarak !== null) {
        $stmt_log_jarak = $db->prepare("INSERT INTO log_jarak_pakan (jarak_cm) VALUES (?)");
        $stmt_log_jarak->execute([$jarak]);
    }

    // Simpan Log Kekeruhan
    if ($kekeruhan !== null) {
        $stmt_log_keruh = $db->prepare("INSERT INTO log_kekeruhan_air (nilai_kekeruhan) VALUES (?)");
        $stmt_log_keruh->execute([$kekeruhan]);
    }
    
    echo "Update Status & Log Sukses";

} catch(PDOException $e) {
    echo "Error Database: " . $e->getMessage();
}
?>