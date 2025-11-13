<?php
// api/get_data_grafik.php
header('Content-Type: application/json'); // Penting!
require '../config/db.php';

try {
    // Ambil 20 data log pakan terakhir
    $stmt = $db->prepare("SELECT waktu, berat_pakan FROM log_pemberian_pakan ORDER BY waktu DESC LIMIT 20");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $labels = [];
    $data = [];

    // Kita balik urutannya agar di grafik tampil dari lama ke baru
    foreach (array_reverse($result) as $row) {
        // Format waktu agar lebih mudah dibaca di grafik
        $labels[] = date('d M H:i', strtotime($row['waktu']));
        $data[] = $row['berat_pakan'];
    }

    echo json_encode(['labels' => $labels, 'data' => $data]);

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>