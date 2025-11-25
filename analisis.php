<?php
require 'config/db.php';

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

// Statistik Pakan 24 Jam
$stmt_pakan = $db->prepare("
    SELECT 
        COUNT(*) as jumlah_pakan, 
        SUM(berat_pakan) as total_berat 
    FROM log_pemberian_pakan 
    WHERE waktu >= NOW() - INTERVAL 1 DAY
");
$stmt_pakan->execute();
$stats_pakan = $stmt_pakan->fetch(PDO::FETCH_ASSOC);

// Statistik Air 24 Jam
$stmt_air = $db->prepare("
    SELECT 
        AVG(nilai_kekeruhan) as rata_kekeruhan 
    FROM log_kekeruhan_air 
    WHERE waktu >= NOW() - INTERVAL 1 DAY
");
$stmt_air->execute();
$stats_air = $stmt_air->fetch(PDO::FETCH_ASSOC);

// Statistik Jarak 24 Jam
$stmt_jarak = $db->prepare("
    SELECT 
        AVG(jarak_cm) as rata_jarak 
    FROM log_jarak_pakan 
    WHERE waktu >= NOW() - INTERVAL 1 DAY
");
$stmt_jarak->execute();
$stats_jarak = $stmt_jarak->fetch(PDO::FETCH_ASSOC);

$rata_jarak_cm = $stats_jarak['rata_jarak'];
$rata_jarak_text = getFeedStatusText($rata_jarak_cm);

$rata_jarak_color = 'text-green-600';
if ($rata_jarak_text == 'Habis') {
    $rata_jarak_color = 'text-red-600';
} else if ($rata_jarak_text == 'Hampir Habis') {
    $rata_jarak_color = 'text-yellow-600';
} else if ($rata_jarak_text == 'Sedang') {
    $rata_jarak_color = 'text-blue-600';
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analisis Data - Monitoring Pakan Ikan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        * { font-family: 'Inter', sans-serif; }
        .gradient-blue { background: linear-gradient(135deg, #004bd4 0%, #007bff 100%); }
        .gradient-blue-soft { background: linear-gradient(135deg, #e0f2fe 0%, #dbeafe 100%); }
        .card-hover { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 20px 25px -5px rgba(0, 75, 212, 0.1); }
        .nav-link { position: relative; transition: all 0.3s ease; }
        .nav-link::after { content: ''; position: absolute; bottom: -2px; left: 0; width: 0; height: 3px; background: linear-gradient(90deg, #004bd4, #007bff); transition: width 0.3s ease; border-radius: 3px; }
        .nav-link:hover::after, .nav-link.active::after { width: 100%; }
        .icon-box { background: linear-gradient(135deg, #e0f2fe 0%, #dbeafe 100%); }
        .stat-badge { background: linear-gradient(135deg, rgba(0, 75, 212, 0.1) 0%, rgba(0, 123, 255, 0.1) 100%); }
    </style>
</head>
<body class="bg-gray-50">
    
    <!-- Modern Navbar -->
    <nav class="bg-white shadow-sm border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-3">
                    <div class="gradient-blue w-10 h-10 rounded-xl flex items-center justify-center">
                        <i class="ri-fish-line text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-gray-900">Monitoring Pakan Ikan</h1>
                        <p class="text-xs text-gray-500">IoT Dashboard System</p>
                    </div>
                </div>
                
                <div class="hidden md:flex items-center space-x-1">
                    <a href="index.php" class="nav-link px-4 py-2 text-sm font-medium text-gray-600 hover:text-blue-600">
                        <i class="ri-dashboard-line mr-1"></i> Dashboard
                    </a>
                    <a href="analisis.php" class="nav-link active px-4 py-2 text-sm font-medium text-blue-600">
                        <i class="ri-bar-chart-line mr-1"></i> Analisis
                    </a>
                    <a href="laporan.php" class="nav-link px-4 py-2 text-sm font-medium text-gray-600 hover:text-blue-600">
                        <i class="ri-file-text-line mr-1"></i> Laporan
                    </a>
                    <a href="jadwal.php" class="nav-link px-4 py-2 text-sm font-medium text-gray-600 hover:text-blue-600">
                        <i class="ri-calendar-line mr-1"></i> Jadwal
                    </a>
                    <a href="pengaturan.php" class="nav-link px-4 py-2 text-sm font-medium text-gray-600 hover:text-blue-600">
                        <i class="ri-settings-3-line mr-1"></i> Pengaturan
                    </a>
                </div>
                
                <div class="flex items-center space-x-3">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-semibold text-gray-900">Admin User</p>
                        <p class="text-xs text-gray-500">admin@unila.ac.id</p>
                    </div>
                    <div class="w-10 h-10 gradient-blue rounded-full flex items-center justify-center text-white font-semibold">
                        AU
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Analisis Data</h1>
                    <p class="text-gray-600">Statistik dan grafik data sistem monitoring 24 jam terakhir</p>
                </div>
                <button onclick="location.reload()" class="gradient-blue text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition flex items-center space-x-2">
                    <i class="ri-refresh-line"></i>
                    <span>Refresh Data</span>
                </button>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            
            <!-- Total Pemberian Pakan -->
            <div class="bg-white rounded-2xl shadow-lg p-6 card-hover">
                <div class="flex items-start justify-between mb-4">
                    <div class="icon-box w-14 h-14 rounded-xl flex items-center justify-center">
                        <i class="ri-restaurant-2-line text-3xl text-blue-600"></i>
                    </div>
                    <div class="stat-badge px-3 py-1 rounded-full">
                        <span class="text-xs font-semibold text-blue-600">24 Jam</span>
                    </div>
                </div>
                <h3 class="text-sm font-medium text-gray-500 mb-1">Pemberian Pakan</h3>
                <div class="flex items-baseline space-x-2 mb-2">
                    <p class="text-4xl font-bold text-gray-900">
                        <?php echo $stats_pakan['jumlah_pakan'] ?? 0; ?>
                    </p>
                    <span class="text-lg text-gray-500">kali</span>
                </div>
                <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                    <span class="text-sm text-gray-600">Total Berat</span>
                    <span class="text-sm font-bold text-blue-600">
                        <?php echo number_format($stats_pakan['total_berat'] ?? 0, 1); ?> gr
                    </span>
                </div>
            </div>

            <!-- Rata-rata Kekeruhan -->
            <div class="bg-white rounded-2xl shadow-lg p-6 card-hover">
                <div class="flex items-start justify-between mb-4">
                    <div class="icon-box w-14 h-14 rounded-xl flex items-center justify-center">
                        <i class="ri-contrast-2-line text-3xl text-yellow-600"></i>
                    </div>
                    <div class="stat-badge px-3 py-1 rounded-full">
                        <span class="text-xs font-semibold text-blue-600">Rata-rata</span>
                    </div>
                </div>
                <h3 class="text-sm font-medium text-gray-500 mb-1">Kekeruhan Air</h3>
                <div class="flex items-baseline space-x-2 mb-2">
                    <p class="text-4xl font-bold text-gray-900">
                        <?php echo number_format($stats_air['rata_kekeruhan'] ?? 0, 0); ?>
                    </p>
                    <span class="text-lg text-gray-500">nilai</span>
                </div>
                <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                    <span class="text-sm text-gray-600">Status</span>
                    <span class="text-sm font-bold <?php echo ($stats_air['rata_kekeruhan'] > 700) ? 'text-yellow-600' : 'text-green-600'; ?> flex items-center">
                        <i class="<?php echo ($stats_air['rata_kekeruhan'] > 700) ? 'ri-alert-line' : 'ri-checkbox-circle-line'; ?> mr-1"></i>
                        <?php echo ($stats_air['rata_kekeruhan'] > 700) ? 'Keruh' : 'Jernih'; ?>
                    </span>
                </div>
            </div>

            <!-- Level Pakan -->
            <div class="bg-white rounded-2xl shadow-lg p-6 card-hover">
                <div class="flex items-start justify-between mb-4">
                    <div class="icon-box w-14 h-14 rounded-xl flex items-center justify-center">
                        <i class="ri-database-2-line text-3xl text-green-600"></i>
                    </div>
                    <div class="stat-badge px-3 py-1 rounded-full">
                        <span class="text-xs font-semibold text-blue-600">Status</span>
                    </div>
                </div>
                <h3 class="text-sm font-medium text-gray-500 mb-1">Level Pakan</h3>
                <div class="flex items-baseline space-x-2 mb-2">
                    <p class="text-4xl font-bold <?php echo $rata_jarak_color; ?>">
                        <?php echo $rata_jarak_text; ?>
                    </p>
                </div>
                <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                    <span class="text-sm text-gray-600">Rata-rata Jarak</span>
                    <span class="text-sm font-bold text-blue-600">
                        <?php echo number_format($rata_jarak_cm ?? 0, 1); ?> cm
                    </span>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="mb-8">
            <div class="flex items-center mb-6">
                <div class="icon-box w-12 h-12 rounded-xl flex items-center justify-center mr-3">
                    <i class="ri-line-chart-line text-2xl text-blue-600"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Grafik Historis</h2>
                    <p class="text-sm text-gray-500">20 data terakhir dari setiap sensor</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Chart 1: Jarak Pakan -->
            <div class="bg-white rounded-2xl shadow-lg p-6 card-hover">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Jarak Pakan</h3>
                        <p class="text-sm text-gray-500">Sensor Ultrasonik (cm)</p>
                    </div>
                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="ri-ruler-line text-red-600 text-xl"></i>
                    </div>
                </div>
                <div class="h-64">
                    <canvas id="distanceChart"></canvas>
                </div>
            </div>

            <!-- Chart 2: Kekeruhan Air -->
            <div class="bg-white rounded-2xl shadow-lg p-6 card-hover">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Kekeruhan Air</h3>
                        <p class="text-sm text-gray-500">Sensor LDR</p>
                    </div>
                    <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="ri-contrast-line text-yellow-600 text-xl"></i>
                    </div>
                </div>
                <div class="h-64">
                    <canvas id="turbidityChart"></canvas>
                </div>
            </div>

            <!-- Chart 3: Pemberian Pakan -->
            <div class="bg-white rounded-2xl shadow-lg p-6 card-hover">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Pemberian Pakan</h3>
                        <p class="text-sm text-gray-500">Histori (gram)</p>
                    </div>
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="ri-scales-3-line text-blue-600 text-xl"></i>
                    </div>
                </div>
                <div class="h-64">
                    <canvas id="feedingChart"></canvas>
                </div>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-100 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-sm text-gray-500">© 2025 Monitoring Pakan Ikan - Universitas Lampung</p>
                <p class="text-sm text-gray-500 mt-2 md:mt-0">Powered by IoT Technology</p>
            </div>
        </div>
    </footer>

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
                
                const ctx = document.getElementById('distanceChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Jarak (cm)',
                            data: data.data,
                            borderColor: '#ef4444',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true,
                            pointRadius: 4,
                            pointBackgroundColor: '#ef4444'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: false, grid: { color: '#f1f5f9' } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            } catch (error) {
                console.error('Gagal memuat data grafik jarak:', error);
            }
        }

        async function loadTurbidityChart() {
            try {
                const response = await fetch('api/get_data_kekeruhan.php');
                const data = await response.json();
                if (data.error) throw new Error(data.error);
                
                const ctx = document.getElementById('turbidityChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Nilai Sensor Kekeruhan',
                            data: data.data,
                            borderColor: '#f59e0b',
                            backgroundColor: 'rgba(245, 158, 11, 0.1)',
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true,
                            pointRadius: 4,
                            pointBackgroundColor: '#f59e0b'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: false, grid: { color: '#f1f5f9' } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            } catch (error) {
                console.error('Gagal memuat data grafik kekeruhan:', error);
            }
        }

        async function loadFeedingChart() {
            try {
                const response = await fetch('api/get_data_grafik.php');
                const data = await response.json();
                if (data.error) throw new Error(data.error);
                
                const ctx = document.getElementById('feedingChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Berat Pakan (gram)',
                            data: data.data,
                            backgroundColor: 'rgba(59, 130, 246, 0.8)',
                            borderColor: '#3b82f6',
                            borderWidth: 1,
                            borderRadius: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            } catch (error) {
                console.error('Gagal memuat data grafik pakan:', error);
            }
        }
    </script>

</body>
</html>