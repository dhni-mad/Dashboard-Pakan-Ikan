<?php
// api/simpan_pengaturan.php
require '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $batas_pakan = (int)$_POST['batas_pakan'];
    $batas_air = (int)$_POST['batas_air'];

    try {
        $stmt = $db->prepare("UPDATE pengaturan SET batas_pakan_habis = ?, batas_air_keruh = ? WHERE id = 1");
        $stmt->execute([$batas_pakan, $batas_air]);
        
        // Redirect kembali ke halaman pengaturan dengan sukses
        echo "<script>alert('Pengaturan berhasil disimpan!'); window.location.href='../pengaturan.php';</script>";
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>