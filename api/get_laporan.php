<?php
header('Content-Type: application/json');
require '../config/db.php';

$periode = isset($_GET['periode']) ? $_GET['periode'] : 'harian';
$tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');

try {
    $response = [];
    
    switch($periode) {
        case 'harian':
            $start_date = $tanggal . ' 00:00:00';
            $end_date = $tanggal . ' 23:59:59';
            break;
            
        case 'mingguan':
            $start_date = date('Y-m-d 00:00:00', strtotime('monday this week', strtotime($tanggal)));
            $end_date = date('Y-m-d 23:59:59', strtotime('sunday this week', strtotime($tanggal)));
            break;
            
        case 'bulanan':
            $start_date = date('Y-m-01 00:00:00', strtotime($tanggal));
            $end_date = date('Y-m-t 23:59:59', strtotime($tanggal));
            break;
            
        default:
            throw new Exception('Periode tidak valid');
    }
    
    $stmt_pakan = $db->prepare("
        SELECT 
            COUNT(*) as jumlah_pemberian,
            SUM(berat_pakan) as total_berat,
            AVG(berat_pakan) as rata_rata_berat,
            MIN(berat_pakan) as min_berat,
            MAX(berat_pakan) as max_berat
        FROM log_pemberian_pakan 
        WHERE waktu BETWEEN ? AND ?
    ");
    $stmt_pakan->execute([$start_date, $end_date]);
    $response['pakan'] = $stmt_pakan->fetch(PDO::FETCH_ASSOC);
    

    $stmt_air = $db->prepare("
        SELECT 
            COUNT(*) as jumlah_data,
            AVG(nilai_kekeruhan) as rata_rata,
            MIN(nilai_kekeruhan) as min_nilai,
            MAX(nilai_kekeruhan) as max_nilai,
            SUM(CASE WHEN nilai_kekeruhan < 500 THEN 1 ELSE 0 END) as jumlah_keruh
        FROM log_kekeruhan_air 
        WHERE waktu BETWEEN ? AND ?
    ");
    $stmt_air->execute([$start_date, $end_date]);
    $response['kekeruhan'] = $stmt_air->fetch(PDO::FETCH_ASSOC);
    
    $stmt_jarak = $db->prepare("
        SELECT 
            COUNT(*) as jumlah_data,
            AVG(jarak_cm) as rata_rata,
            MIN(jarak_cm) as min_jarak,
            MAX(jarak_cm) as max_jarak
        FROM log_jarak_pakan 
        WHERE waktu BETWEEN ? AND ?
    ");
    $stmt_jarak->execute([$start_date, $end_date]);
    $response['jarak'] = $stmt_jarak->fetch(PDO::FETCH_ASSOC);
    
    $stmt_detail = $db->prepare("
        SELECT 
            DATE_FORMAT(waktu, '%d %b %Y %H:%i:%s') as waktu_format,
            berat_pakan
        FROM log_pemberian_pakan 
        WHERE waktu BETWEEN ? AND ?
        ORDER BY waktu DESC
    ");
    $stmt_detail->execute([$start_date, $end_date]);
    $response['detail_pakan'] = $stmt_detail->fetchAll(PDO::FETCH_ASSOC);
    
    if ($periode == 'harian') {
        $stmt_grafik = $db->prepare("
            SELECT 
                DATE_FORMAT(waktu, '%H:00') as label,
                SUM(berat_pakan) as total
            FROM log_pemberian_pakan 
            WHERE waktu BETWEEN ? AND ?
            GROUP BY label
            ORDER BY label ASC
        ");
    } else {
        $stmt_grafik = $db->prepare("
            SELECT 
                DATE_FORMAT(MIN(waktu), '%d %b') as label,
                SUM(berat_pakan) as total
            FROM log_pemberian_pakan 
            WHERE waktu BETWEEN ? AND ?
            GROUP BY DATE_FORMAT(waktu, '%Y-%m-%d')
            ORDER BY MIN(waktu) ASC
        ");
    }
    $stmt_grafik->execute([$start_date, $end_date]);
    $response['grafik_pakan'] = $stmt_grafik->fetchAll(PDO::FETCH_ASSOC);
    
    $response['info'] = [
        'periode' => $periode,
        'start_date' => $start_date,
        'end_date' => $end_date,
        'tanggal_request' => $tanggal
    ];
    
    echo json_encode($response);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>