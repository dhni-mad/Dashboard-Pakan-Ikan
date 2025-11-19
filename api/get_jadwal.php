<?php
header('Content-Type: application/json');
require '../config/db.php';

try {
    // Ambil hanya jadwal yang aktif, diurutkan berdasarkan jam
    $stmt = $db->prepare("SELECT jam, berat_pakan FROM jadwal_pakan WHERE aktif = 1 ORDER BY jam ASC");
    $stmt->execute();
    $jadwal_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format data untuk mudah diproses oleh Arduino/ESP
    $response = [
        'status' => 'success',
        'count' => count($jadwal_list),
        'jadwal' => []
    ];

    foreach ($jadwal_list as $jadwal) {
        $response['jadwal'][] = [
            'jam' => date('H:i', strtotime($jadwal['jam'])),
            'berat' => (float)$jadwal['berat_pakan']
        ];
    }

    echo json_encode($response);

} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>