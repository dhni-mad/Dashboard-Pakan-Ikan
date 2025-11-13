<?php
// api/get_laporan.php
header('Content-Type: application/json');
require '../config/db.php';

// Ambil parameter periode dari query string
$periode = isset($_GET['periode']) ? $_GET['periode'] : 'harian';
$tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');

try {
    $response = [];
    
    // Tentukan rentang waktu berdasarkan periode
    switch($periode) {
        case 'harian':
            $start_date = $tanggal . ' 00:00:00';
            $end_date = $tanggal . ' 23:59:59';
            break;
            
        case 'mingguan':
            // Ambil minggu dari tanggal yang dipilih
            $start_date = date('Y-m-d 00:00:00', strtotime('monday this week', strtotime($tanggal)));
            $end_date = date('Y-m-d 23:59:59', strtotime('sunday this week', strtotime($tanggal)));
            break;
            
        case 'bulanan':
            // Ambil bulan dari tanggal yang dipilih
            $start_date = date('Y-m-01 00:00:00', strtotime($tanggal));
            $end_date = date('Y-m-t 23:59:59', strtotime($tanggal));
            break;
            
        default:
            throw new Exception('Periode tidak valid');
    }
    
    // 1. Data Pemberian Pakan
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
    
    // 2. Data Kekeruhan Air
    $stmt_air = $db->prepare("
        SELECT 
            COUNT(*) as jumlah_data,
            AVG(nilai_kekeruhan) as rata_rata,
            MIN(nilai_kekeruhan) as min_nilai,
            MAX(nilai_kekeruhan) as max_nilai,
            SUM(CASE WHEN nilai_kekeruhan > 700 THEN 1 ELSE 0 END) as jumlah_keruh
        FROM log_kekeruhan_air 
        WHERE waktu BETWEEN ? AND ?
    ");
    $stmt_air->execute([$start_date, $end_date]);
    $response['kekeruhan'] = $stmt_air->fetch(PDO::FETCH_ASSOC);
    
    // 3. Data Jarak Pakan (Level Pakan)
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
    
    // 4. Detail Pemberian Pakan (untuk tabel)
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
    
    // 5. Grafik Harian Pakan (per jam untuk harian, per hari untuk mingguan/bulanan)
    if ($periode == 'harian') {
        $stmt_grafik = $db->prepare("
            SELECT 
                DATE_FORMAT(waktu, '%H:00') as label,
                SUM(berat_pakan) as total
            FROM log_pemberian_pakan 
            WHERE waktu BETWEEN ? AND ?
            GROUP BY DATE_FORMAT(waktu, '%H')
            ORDER BY waktu
        ");
    } else {
        $stmt_grafik = $db->prepare("
            SELECT 
                DATE_FORMAT(waktu, '%d %b') as label,
                SUM(berat_pakan) as total
            FROM log_pemberian_pakan 
            WHERE waktu BETWEEN ? AND ?
            GROUP BY DATE_FORMAT(waktu, '%Y-%m-%d')
            ORDER BY waktu
        ");
    }
    $stmt_grafik->execute([$start_date, $end_date]);
    $response['grafik_pakan'] = $stmt_grafik->fetchAll(PDO::FETCH_ASSOC);
    
    // Tambahkan info periode
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