<?php
require 'config/db.php';

// Ambil status sistem
$stmt = $db->prepare("SELECT * FROM status_sistem WHERE id = 1");
$stmt->execute();
$status = $stmt->fetch(PDO::FETCH_ASSOC);

// === FITUR: CEK STATUS ONLINE/OFFLINE ===
$last_update = strtotime($status['last_update']);
$now = time();
$selisih_detik = $now - $last_update;
$batas_offline = 120; // 2 menit = 120 detik

$is_online = $selisih_detik <= $batas_offline;
$status_text = $is_online ? 'ONLINE' : 'OFFLINE';
$status_color = $is_online ? 'bg-green-500' : 'bg-red-500';
$status_icon = $is_online ? '🟢' : '🔴';
$alert_class = $is_online ? 'gradient-blue-soft border-green-500' : 'bg-red-50 border-red-500';

// === LOGIC WARNA STATUS PAKAN ===
$jarak_status_text = $status['feed_status'];
$jarak_color_class = 'text-green-600'; 
$badge_pakan = 'bg-green-100 text-green-700';

if ($jarak_status_text == 'Habis') {
    $jarak_color_class = 'text-red-600';
    $badge_pakan = 'bg-red-100 text-red-700';
} else if ($jarak_status_text == 'Hampir Habis') {
    $jarak_color_class = 'text-yellow-600';
    $badge_pakan = 'bg-yellow-100 text-yellow-700';
} else if ($jarak_status_text == 'Sedang') {
    $jarak_color_class = 'text-blue-600';
    $badge_pakan = 'bg-blue-100 text-blue-700';
}

// === LOGIC WARNA STATUS AIR ===
$water_color_class = ($status['water_status'] == 'Keruh') ? 'text-yellow-700' : 'text-blue-600';
$badge_air = ($status['water_status'] == 'Keruh') ? 'bg-yellow-100 text-yellow-700' : 'bg-blue-100 text-blue-700';

// === AMBIL LOG PAKAN TERBARU ===
$stmt_log_pakan = $db->prepare("SELECT waktu, berat_pakan FROM log_pemberian_pakan ORDER BY waktu DESC LIMIT 5");
$stmt_log_pakan->execute();
$log_pakan = $stmt_log_pakan->fetchAll(PDO::FETCH_ASSOC);

// === AMBIL LOG AIR KERUH ===
$stmt_log_air = $db->prepare("SELECT waktu, nilai_kekeruhan FROM log_kekeruhan_air WHERE nilai_kekeruhan < 500 ORDER BY waktu DESC LIMIT 5");
$stmt_log_air->execute();
$log_air_keruh = $stmt_log_air->fetchAll(PDO::FETCH_ASSOC);

// === STATISTIK 24 JAM ===
$stmt_stats = $db->prepare("
    SELECT 
        COUNT(*) as jumlah_pakan, 
        SUM(berat_pakan) as total_berat 
    FROM log_pemberian_pakan 
    WHERE waktu >= NOW() - INTERVAL 1 DAY
");
$stmt_stats->execute();
$stats_pakan = $stmt_stats->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Monitoring Pakan Ikan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        * { font-family: 'Inter', sans-serif; }
        .gradient-blue { background: linear-gradient(135deg, #004bd4 0%, #007bff 100%); }
        .gradient-blue-soft { background: linear-gradient(135deg, #e0f2fe 0%, #dbeafe 100%); }
        .card-hover { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 20px 25px -5px rgba(0, 75, 212, 0.1); }
        .status-pulse { animation: pulse-ring 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
        @keyframes pulse-ring { 0%, 100% { opacity: 1; } 50% { opacity: 0.7; } }
        .btn-primary { background: linear-gradient(135deg, #004bd4 0%, #007bff 100%); transition: all 0.3s ease; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(0, 75, 212, 0.3); }
        .icon-box { background: linear-gradient(135deg, #e0f2fe 0%, #dbeafe 100%); }
        .nav-link { position: relative; transition: all 0.3s ease; }
        .nav-link::after { content: ''; position: absolute; bottom: -2px; left: 0; width: 0; height: 3px; background: linear-gradient(90deg, #004bd4, #007bff); transition: width 0.3s ease; border-radius: 3px; }
        .nav-link:hover::after, .nav-link.active::after { width: 100%; }
        .stat-card { background: white; border-left: 4px solid transparent; transition: all 0.3s ease; }
        .stat-card:hover { border-left-color: #007bff; }
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
                    <a href="index.php" class="nav-link active px-4 py-2 text-sm font-medium text-blue-600">
                        <i class="ri-dashboard-line mr-1"></i> Dashboard
                    </a>
                    <a href="analisis.php" class="nav-link px-4 py-2 text-sm font-medium text-gray-600 hover:text-blue-600">
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
        
        <!-- Status Pengumuman -->
        <div class="mb-6">
            <div class="<?php echo $alert_class; ?> rounded-2xl p-6 border-l-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="<?php echo $status_color; ?> rounded-full h-3 w-3 <?php echo $is_online ? 'status-pulse' : ''; ?>"></div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 flex items-center">
                                <i class="ri-wifi-line mr-2 text-blue-600"></i>
                                Status Perangkat
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">
                                <?php echo $is_online ? 'Sistem terhubung dan berjalan normal' : 'Perangkat tidak merespons'; ?>
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center px-4 py-2 rounded-lg <?php echo $is_online ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?> text-sm font-semibold">
                            <i class="<?php echo $is_online ? 'ri-checkbox-circle-fill' : 'ri-close-circle-fill'; ?> mr-2"></i>
                            <?php echo $status_text; ?>
                        </span>
                        <p class="text-xs text-gray-500 mt-2">
                            Update: <?php echo date('d M Y, H:i:s', strtotime($status['last_update'])); ?>
                        </p>
                        <?php if (!$is_online): ?>
                            <p class="text-xs text-red-600 mt-1">
                                (<?php echo round($selisih_detik / 60); ?> menit yang lalu)
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            
            <!-- Card Status Pakan -->
            <div class="stat-card card-hover bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="icon-box w-14 h-14 rounded-xl flex items-center justify-center">
                        <i class="ri-shopping-bag-3-line text-3xl text-blue-600"></i>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold <?php echo $badge_pakan; ?>">
                        <?php echo $jarak_status_text; ?>
                    </span>
                </div>
                <h3 class="text-sm font-medium text-gray-500 mb-1">Status Pakan</h3>
                <p class="text-3xl font-bold <?php echo $jarak_color_class; ?> mb-2">
                    <?php echo htmlspecialchars($jarak_status_text); ?>
                </p>
                <div class="flex items-center text-sm text-gray-600">
                    <i class="ri-ruler-line mr-1"></i>
                    <span>Jarak: <?php echo $status['jarak_cm']; ?> cm</span>
                </div>
            </div>

            <!-- Card Status Air -->
            <div class="stat-card card-hover bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="icon-box w-14 h-14 rounded-xl flex items-center justify-center">
                        <i class="ri-drop-line text-3xl text-blue-600"></i>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold <?php echo $badge_air; ?>">
                        <?php echo $status['water_status']; ?>
                    </span>
                </div>
                <h3 class="text-sm font-medium text-gray-500 mb-1">Kualitas Air</h3>
                <p class="text-3xl font-bold <?php echo $water_color_class; ?> mb-2">
                    <?php echo htmlspecialchars($status['water_status']); ?>
                </p>
                <div class="flex items-center text-sm text-gray-600">
                    <i class="ri-contrast-line mr-1"></i>
                    <span>Status Normal</span>
                </div>
            </div>

            <!-- Card Total Pemberian -->
            <div class="stat-card card-hover bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="icon-box w-14 h-14 rounded-xl flex items-center justify-center">
                        <i class="ri-restaurant-line text-3xl text-blue-600"></i>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">
                        24 Jam
                    </span>
                </div>
                <h3 class="text-sm font-medium text-gray-500 mb-1">Total Pemberian</h3>
                <p class="text-3xl font-bold text-gray-900 mb-2">
                    <?php echo $stats_pakan['jumlah_pakan'] ?? 0; ?> Kali
                </p>
                <div class="flex items-center text-sm text-gray-600">
                    <i class="ri-scales-3-line mr-1"></i>
                    <span><?php echo number_format($stats_pakan['total_berat'] ?? 0, 1); ?> gram</span>
                </div>
            </div>

            <!-- Card Rata-rata -->
            <div class="stat-card card-hover bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="icon-box w-14 h-14 rounded-xl flex items-center justify-center">
                        <i class="ri-pulse-line text-3xl text-blue-600"></i>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-700">
                        Harian
                    </span>
                </div>
                <h3 class="text-sm font-medium text-gray-500 mb-1">Rata-rata</h3>
                <p class="text-3xl font-bold text-gray-900 mb-2">
                    <?php 
                    $rata = ($stats_pakan['jumlah_pakan'] > 0) 
                        ? $stats_pakan['total_berat'] / $stats_pakan['jumlah_pakan'] 
                        : 0;
                    echo number_format($rata, 0);
                    ?>g
                </p>
                <div class="flex items-center text-sm text-gray-600">
                    <i class="ri-line-chart-line mr-1"></i>
                    <span>Per pemberian</span>
                </div>
            </div>
        </div>

        <!-- Action & Monitoring Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            
            <!-- Kontrol Manual -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-lg p-6 card-hover">
                    <div class="flex items-center mb-4">
                        <div class="icon-box w-12 h-12 rounded-xl flex items-center justify-center mr-3">
                            <i class="ri-remote-control-line text-2xl text-blue-600"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Kontrol Manual</h3>
                            <p class="text-sm text-gray-500">Beri pakan sekarang</p>
                        </div>
                    </div>
                    
                    <button id="manualFeedButton" class="w-full btn-primary text-white font-semibold py-4 px-6 rounded-xl shadow-lg flex items-center justify-center space-x-2">
                        <i class="ri-play-circle-fill text-xl"></i>
                        <span>Beri Pakan Sekarang</span>
                    </button>
                    
                    <div id="feedStatus" class="mt-4 text-sm text-center text-gray-500"></div>
                    
                    <div class="mt-4 p-4 bg-gray-50 rounded-xl">
                        <p class="text-sm text-gray-600 text-center">
                            <i class="ri-information-line mr-1"></i>
                            Tombol ini akan memberikan 50 gram pakan secara otomatis
                        </p>
                    </div>
                </div>
            </div>

            <!-- Aktivitas Terbaru -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-lg p-6 card-hover">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <div class="icon-box w-12 h-12 rounded-xl flex items-center justify-center mr-3">
                                <i class="ri-time-line text-2xl text-blue-600"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Aktivitas Terbaru</h3>
                                <p class="text-sm text-gray-500">5 aktivitas terakhir</p>
                            </div>
                        </div>
                        <button onclick="location.reload()" class="text-blue-600 text-sm font-semibold hover:text-blue-700">
                            <i class="ri-refresh-line mr-1"></i> Refresh
                        </button>
                    </div>
                    
                    <div class="space-y-4">
                        <?php if (empty($log_pakan)): ?>
                            <div class="text-center py-8 text-gray-500">
                                <i class="ri-inbox-line text-4xl mb-2"></i>
                                <p>Belum ada aktivitas pemberian pakan</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($log_pakan as $log): ?>
                            <div class="flex items-center p-4 bg-gray-50 rounded-xl hover:bg-blue-50 transition">
                                <div class="w-10 h-10 gradient-blue rounded-lg flex items-center justify-center mr-4">
                                    <i class="ri-check-line text-white"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-900">Pemberian Pakan</p>
                                    <p class="text-xs text-gray-500">
                                        <?php echo date('d M Y, H:i', strtotime($log['waktu'])); ?>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-bold text-blue-600">
                                        <?php echo number_format($log['berat_pakan'], 1); ?> gram
                                    </p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Warning Section -->
        <?php if (!empty($log_air_keruh)): ?>
        <div class="mb-8">
            <div class="bg-white rounded-2xl shadow-lg p-6 card-hover">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center mr-3">
                        <i class="ri-alert-line text-2xl text-yellow-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Peringatan Kekeruhan Air</h3>
                        <p class="text-sm text-gray-500">Deteksi air keruh terbaru</p>
                    </div>
                </div>
                <div class="space-y-3">
                    <?php foreach ($log_air_keruh as $log): ?>
                    <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg border-l-4 border-yellow-500">
                        <span class="text-sm text-gray-700">
                            <?php echo date('d M Y, H:i', strtotime($log['waktu'])); ?>
                        </span>
                        <span class="text-sm font-bold text-yellow-700">
                            Nilai: <?php echo $log['nilai_kekeruhan']; ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-100 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-sm text-gray-500">
                    © 2025 Monitoring Pakan Ikan - Universitas Lampung
                </p>
                <p class="text-sm text-gray-500 mt-2 md:mt-0">
                    Powered by IoT Technology
                </p>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('manualFeedButton').addEventListener('click', triggerManualFeed);
            
            // Auto refresh setiap 30 detik
            setInterval(() => {
                location.reload();
            }, 30000);
        });

        async function triggerManualFeed() {
            const button = document.getElementById('manualFeedButton');
            const statusDiv = document.getElementById('feedStatus');
            
            button.disabled = true;
            button.innerHTML = '<i class="ri-loader-4-line animate-spin mr-2"></i> Mengirim...';
            statusDiv.textContent = '';
            
            try {
                const response = await fetch('api/trigger_feed.php', { method: 'POST' });
                const result = await response.json();
                
                if (result.status === 'success') {
                    statusDiv.className = 'mt-4 text-sm text-center text-green-600 font-semibold';
                    statusDiv.textContent = '✓ Permintaan terkirim! Pakan akan diberikan.';
                    button.classList.replace('btn-primary', 'bg-gray-400');
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                statusDiv.className = 'mt-4 text-sm text-center text-red-600 font-semibold';
                statusDiv.textContent = '✗ Gagal mengirim: ' + error.message;
                button.disabled = false;
                button.innerHTML = '<i class="ri-play-circle-fill text-xl"></i><span>Beri Pakan Sekarang</span>';
            }
            
            setTimeout(() => {
                button.disabled = false;
                button.innerHTML = '<i class="ri-play-circle-fill text-xl mr-2"></i><span>Beri Pakan Sekarang</span>';
                button.className = 'w-full btn-primary text-white font-semibold py-4 px-6 rounded-xl shadow-lg flex items-center justify-center space-x-2';
                statusDiv.textContent = '';
            }, 5000);
        }
    </script>

</body>
</html>