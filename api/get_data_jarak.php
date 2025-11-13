<?php
// api/get_data_jarak.php
header('Content-Type: application/json');
require '../config/db.php';

try {
    // Ambil 20 data log jarak terakhir
    $stmt = $db->prepare("SELECT waktu, jarak_cm FROM log_jarak_pakan ORDER BY waktu DESC LIMIT 20");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $labels = [];
    $data = [];

    foreach (array_reverse($result) as $row) {
        $labels[] = date('d M H:i', strtotime($row['waktu']));
        $data[] = (int)$row['jarak_cm'];
    }

    echo json_encode(['labels' => $labels, 'data' => $data]);

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>