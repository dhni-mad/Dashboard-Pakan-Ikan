<?php

header('Content-Type: application/json'); 
require '../config/db.php';

try {
    $stmt = $db->prepare("SELECT waktu, berat_pakan FROM log_pemberian_pakan ORDER BY waktu DESC LIMIT 20");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $labels = [];
    $data = [];

    foreach (array_reverse($result) as $row) {
        $labels[] = date('d M H:i', strtotime($row['waktu']));
        $data[] = $row['berat_pakan'];
    }

    echo json_encode(['labels' => $labels, 'data' => $data]);

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>