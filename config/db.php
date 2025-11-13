<?php
// config/db.php

$host = '127.0.0.1'; // atau 'localhost'
$db_name = 'db_pakan_ikan';
$username = 'root'; // Default username Laragon
$password = ''; // Default password Laragon kosong

try {
    $db = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Koneksi database gagal: " . $e->getMessage();
    die();
}
?>