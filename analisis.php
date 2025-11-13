<?php
// analisis.php
require 'config/db.php';

// [MODIFIKASI] Fungsi helper untuk konversi jarak ke teks
function getFeedStatusText($jarak_cm) {
    if ($jarak_cm === null) return "N/A";
    
    $jarak = (float)$jarak_cm;
    
    if ($jarak >= 15) return "Habis";
    if ($jarak >= 13) return "Hampir Habis";
    if ($jarak >= 10) return "Sedang";
    if ($jarak >= 5) return "Masih Banyak";
    if ($jarak >= 0) return "Penuh";
    
    return "N/A";
}

// --- Ambil Data Statistik Ringkasan ---

// 1. Statistik Pakan (24 Jam Terakhir)
$stmt_pakan = $db->prepare("
    SELECT 
        COUNT(*) as jumlah_pakan, 
        SUM(berat_pakan) as total_berat 
    FROM log_pemberian_pakan 
    WHERE waktu >= NOW() - INTERVAL 1 DAY
");
$stmt_pakan->execute();
$stats_pakan = $stmt_pakan->fetch(PDO::FETCH_ASSOC);

// 2. Statistik Kekeruhan (Rata-rata 24 Jam Terakhir)
$stmt_air = $db->prepare("
    SELECT 
        AVG(nilai_kekeruhan) as rata_kekeruhan 
    FROM log_kekeruhan_air 
    WHERE waktu >= NOW() - INTERVAL 1 DAY
");
$stmt_air->execute();
$stats_air = $stmt_air->fetch(PDO::FETCH_ASSOC);

// 3. Statistik Jarak Pakan (Rata-rata 24 Jam Terakhir)
$stmt_jarak = $db->prepare("
    SELECT 
        AVG(jarak_cm) as rata_jarak 
    FROM log_jarak_pakan 
    WHERE waktu >= NOW() - INTERVAL 1 DAY
");
$stmt_jarak->execute();
$stats_jarak = $stmt_jarak->fetch(PDO::FETCH_ASSOC);

// [MODIFIKASI] Proses data rata-rata jarak
$rata_jarak_cm = $stats_jarak['rata_jarak'];
$rata_jarak_text = getFeedStatusText($rata_jarak_cm);

// Tentukan warna berdasarkan teks
$rata_jarak_color = 'text-green-600'; // Default
if ($rata_jarak_text == 'Habis') {
    $rata_jarak_color = 'text-red-600';
} else if ($rata_jarak_text == 'Hampir Habis') {
    $rata_jarak_color = 'text-yellow-600';
} else if ($rata_jarak_text == 'Sedang') {
    $rata_jarak_color = 'text-blue-600';
}

?>

<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analisis Data</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
    <header class="bg-white shadow-md">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
            <div class="text-2xl font-bold text-gray-800">
                Monitoring Pakan Ikan
            </div>
            <div class="flex space-x-4">
                <a href="index.php" class="px-3 py-2 rounded text-gray-700 hover:bg-gray-200 font-semibold">Dashboard</a>
                <a href="analisis.php" class="px-3 py-2 rounded bg-blue-600 text-white font-semibold">Analisis Data</a>
                <a href="laporan.php" class="px-3 py-2 rounded text-gray-700 hover:bg-gray-200 font-semibold">Laporan</a>
            </div>
        </nav>
    </header>

    <main class="container mx-auto p-6">

        <h2 class="text-2xl font-bold text-gray-800 mb-4">Ringkasan 24 Jam Terakhir</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            
            <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <h3 class="text-lg font-semibold text-gray-600 mb-2">Pemberian Pakan</h3>
                <div class="text-4xl font-bold text-blue-600">
                    <?php echo $stats_pakan['jumlah_pakan'] ?? 0; ?>
                </div>
                <div class="text-sm text-gray-500">Total (<?php echo number_format($stats_pakan['total_berat'] ?? 0, 1); ?> gr)</div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <h3 class="text-lg font-semibold text-gray-600 mb-2">Rata-rata Kekeruhan</h3>
                <div class="text-4xl font-bold text-yellow-700">
                    <?php echo number_format($stats_air['rata_kekeruhan'] ?? 0, 0); ?>
                </div>
                <div class="text-sm text-gray-500">Nilai Sensor</div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <h3 class="text-lg font-semibold text-gray-600 mb-2">Rata-rata Level Pakan</h3>
                <div class="text-4xl font-bold <?php echo $rata_jarak_color; ?>">
                    <?php echo $rata_jarak_text; ?>
                </div>
                <div class="text-sm text-gray-500">
                    (Rata-rata: <?php echo number_format($rata_jarak_cm ?? 0, 1); ?> cm)
                </div>
            </div>
        </div>

        <h2 class="text-2xl font-bold text-gray-800 mb-4">Grafik Data Historis (20 Data Terakhir)</h2>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <div class="bg-white p-6 rounded-lg shadow-md lg:col-span-1">
                <h3 class="text-xl font-semibold text-gray-700 mb-4 text-center">Grafik Jarak Pakan (cm)</h3>
                <canvas id="distanceChart"></canvas>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md lg:col-span-1">
                <h3 class="text-xl font-semibold text-gray-700 mb-4 text-center">Grafik Kekeruhan Air (Sensor)</h3>
                <canvas id="turbidityChart"></canvas>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md lg:col-span-1">
                <h3 class="text-xl font-semibold text-gray-700 mb-4 text-center">Histori Pemberian Pakan (Gram)</h3>
                <canvas id="feedingChart"></canvas>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            loadDistanceChart();
            loadTurbidityChart();
            loadFeedingChart();
        });

        async function loadDistanceChart() {
            try {
                const response = await fetch('api/get_data_jarak.php');
                const data = await response.json();
                if (data.error) throw new Error(data.error);
                new Chart(document.getElementById('distanceChart').getContext('2d'), {
                    type: 'line', data: { labels: data.labels, datasets: [{ label: 'Jarak (cm)', data: data.data, borderColor: 'rgba(239, 68, 68, 1)', backgroundColor: 'rgba(239, 68, 68, 0.1)', borderWidth: 2, tension: 0.1, fill: true }] }, options: { scales: { y: { beginAtZero: false } } }
                });
            } catch (error) { console.error('Gagal memuat data grafik jarak:', error); }
        }

        async function loadTurbidityChart() {
            try {
                const response = await fetch('api/get_data_kekeruhan.php');
                const data = await response.json();
                if (data.error) throw new Error(data.error);
                new Chart(document.getElementById('turbidityChart').getContext('2d'), {
                    type: 'line', data: { labels: data.labels, datasets: [{ label: 'Nilai Sensor Kekeruhan', data: data.data, borderColor: 'rgba(240, 173, 78, 1)', backgroundColor: 'rgba(240, 173, 78, 0.1)', borderWidth: 2, tension: 0.1, fill: true }] }, options: { scales: { y: { beginAtZero: false } } }
                });
            } catch (error) { console.error('Gagal memuat data grafik kekeruhan:', error); }
        }

        async function loadFeedingChart() {
            try {
                const response = await fetch('api/get_data_grafik.php');
                const data = await response.json();
                if (data.error) throw new Error(data.error);
                new Chart(document.getElementById('feedingChart').getContext('2d'), {
                    type: 'bar', data: { labels: data.labels, datasets: [{ label: 'Berat Pakan (gram)', data: data.data, backgroundColor: 'rgba(59, 130, 246, 0.7)', borderColor: 'rgba(59, 130, 246, 1)', borderWidth: 1 }] }, options: { scales: { y: { beginAtZero: true } } }
                });
            } catch (error) { console.error('Gagal memuat data grafik pakan:', error); }
        }
    </script>
</body>
</html>