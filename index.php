<?php
require 'config/db.php';

$stmt = $db->prepare("SELECT * FROM status_sistem WHERE id = 1");
$stmt->execute();
$status = $stmt->fetch(PDO::FETCH_ASSOC);

// === FITUR BARU: CEK STATUS ONLINE/OFFLINE ===
$last_update = strtotime($status['last_update']);
$now = time();
$selisih_detik = $now - $last_update;
$batas_offline = 120; // 2 menit = 120 detik

$is_online = $selisih_detik <= $batas_offline;
$status_text = $is_online ? 'ONLINE' : 'OFFLINE';
$status_color = $is_online ? 'bg-green-500' : 'bg-red-500';
$status_icon = $is_online ? '🟢' : '🔴';
// ============================================

$jarak_status_text = $status['feed_status'];
$jarak_color_class = 'text-green-600'; 
if ($jarak_status_text == 'Habis') {
    $jarak_color_class = 'text-red-600';
} else if ($jarak_status_text == 'Hampir Habis') {
    $jarak_color_class = 'text-yellow-600';
} else if ($jarak_status_text == 'Sedang') {
    $jarak_color_class = 'text-blue-600';
}

$water_color_class = ($status['water_status'] == 'Keruh') ? 'text-yellow-700' : 'text-blue-600';

$stmt_log_pakan = $db->prepare("SELECT waktu, berat_pakan FROM log_pemberian_pakan ORDER BY waktu DESC LIMIT 5");
$stmt_log_pakan->execute();
$log_pakan = $stmt_log_pakan->fetchAll(PDO::FETCH_ASSOC);

$stmt_log_air = $db->prepare("SELECT waktu, nilai_kekeruhan FROM log_kekeruhan_air WHERE nilai_kekeruhan > 700 ORDER BY waktu DESC LIMIT 5");
$stmt_log_air->execute();
$log_air_keruh = $stmt_log_air->fetchAll(PDO::FETCH_ASSOC);

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
        
        * {
            font-family: 'Inter', sans-serif;
        }
        
        .gradient-blue {
            background: linear-gradient(135deg, #004bd4 0%, #007bff 100%);
        }
        
        .gradient-blue-soft {
            background: linear-gradient(135deg, #e0f2fe 0%, #dbeafe 100%);
        }
        
        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 75, 212, 0.1), 0 10px 10px -5px rgba(0, 75, 212, 0.04);
        }
        
        .status-pulse {
            animation: pulse-ring 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        @keyframes pulse-ring {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #004bd4 0%, #007bff 100%);
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 75, 212, 0.3);
        }
        
        .icon-box {
            background: linear-gradient(135deg, #e0f2fe 0%, #dbeafe 100%);
        }
        
        .nav-link {
            position: relative;
            transition: all 0.3s ease;
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 3px;
            background: linear-gradient(90deg, #004bd4, #007bff);
            transition: width 0.3s ease;
            border-radius: 3px;
        }
        
        .nav-link:hover::after,
        .nav-link.active::after {
            width: 100%;
        }
        
        .stat-card {
            background: white;
            border-left: 4px solid transparent;
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            border-left-color: #007bff;
        }
    </style>
</head>
<body class="bg-gray-50">
    
    <!-- Modern Navbar -->
    <nav class="bg-white shadow-sm border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo & Brand -->
                <div class="flex items-center space-x-3">
                    <div class="gradient-blue w-10 h-10 rounded-xl flex items-center justify-center">
                        <i class="ri-fish-line text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-gray-900">Monitoring Pakan Ikan</h1>
                        <p class="text-xs text-gray-500">IoT Dashboard System</p>
                    </div>
                </div>
                
                <!-- Navigation Links -->
                <div class="hidden md:flex items-center space-x-1">
                    <a href="#" class="nav-link active px-4 py-2 text-sm font-medium text-blue-600">
                        <i class="ri-dashboard-line mr-1"></i> Dashboard
                    </a>
                    <a href="#" class="nav-link px-4 py-2 text-sm font-medium text-gray-600 hover:text-blue-600">
                        <i class="ri-bar-chart-line mr-1"></i> Analisis
                    </a>
                    <a href="#" class="nav-link px-4 py-2 text-sm font-medium text-gray-600 hover:text-blue-600">
                        <i class="ri-file-text-line mr-1"></i> Laporan
                    </a>
                    <a href="#" class="nav-link px-4 py-2 text-sm font-medium text-gray-600 hover:text-blue-600">
                        <i class="ri-calendar-line mr-1"></i> Jadwal
                    </a>
                    <a href="#" class="nav-link px-4 py-2 text-sm font-medium text-gray-600 hover:text-blue-600">
                        <i class="ri-settings-3-line mr-1"></i> Pengaturan
                    </a>
                </div>
                
                <!-- User Profile -->
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
            <div class="gradient-blue-soft rounded-2xl p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="bg-green-500 rounded-full h-3 w-3 status-pulse"></div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 flex items-center">
                                <i class="ri-wifi-line mr-2 text-blue-600"></i>
                                Status Perangkat
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">Sistem terhubung dan berjalan normal</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center px-4 py-2 rounded-lg bg-green-100 text-green-700 text-sm font-semibold">
                            <i class="ri-checkbox-circle-fill mr-2"></i>
                            ONLINE
                        </span>
                        <p class="text-xs text-gray-500 mt-2">Update: 25 Nov 2025, 14:30</p>
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
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                        Normal
                    </span>
                </div>
                <h3 class="text-sm font-medium text-gray-500 mb-1">Status Pakan</h3>
                <p class="text-3xl font-bold text-gray-900 mb-2">Penuh</p>
                <div class="flex items-center text-sm text-gray-600">
                    <i class="ri-ruler-line mr-1"></i>
                    <span>Jarak: 3 cm</span>
                </div>
            </div>

            <!-- Card Status Air -->
            <div class="stat-card card-hover bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="icon-box w-14 h-14 rounded-xl flex items-center justify-center">
                        <i class="ri-drop-line text-3xl text-blue-600"></i>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                        Jernih
                    </span>
                </div>
                <h3 class="text-sm font-medium text-gray-500 mb-1">Kualitas Air</h3>
                <p class="text-3xl font-bold text-gray-900 mb-2">Jernih</p>
                <div class="flex items-center text-sm text-gray-600">
                    <i class="ri-contrast-line mr-1"></i>
                    <span>Sensor: 450</span>
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
                <p class="text-3xl font-bold text-gray-900 mb-2">8 Kali</p>
                <div class="flex items-center text-sm text-gray-600">
                    <i class="ri-scales-3-line mr-1"></i>
                    <span>400 gram</span>
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
                <p class="text-3xl font-bold text-gray-900 mb-2">50g</p>
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
                    
                    <button class="w-full btn-primary text-white font-semibold py-4 px-6 rounded-xl shadow-lg flex items-center justify-center space-x-2">
                        <i class="ri-play-circle-fill text-xl"></i>
                        <span>Beri Pakan Sekarang</span>
                    </button>
                    
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
                        <button class="text-blue-600 text-sm font-semibold hover:text-blue-700">
                            Lihat Semua <i class="ri-arrow-right-line"></i>
                        </button>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex items-center p-4 bg-gray-50 rounded-xl hover:bg-blue-50 transition">
                            <div class="w-10 h-10 gradient-blue rounded-lg flex items-center justify-center mr-4">
                                <i class="ri-check-line text-white"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-900">Pemberian Pakan Otomatis</p>
                                <p class="text-xs text-gray-500">25 Nov 2025, 12:00</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-blue-600">50 gram</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center p-4 bg-gray-50 rounded-xl hover:bg-blue-50 transition">
                            <div class="w-10 h-10 gradient-blue rounded-lg flex items-center justify-center mr-4">
                                <i class="ri-check-line text-white"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-900">Pemberian Pakan Otomatis</p>
                                <p class="text-xs text-gray-500">25 Nov 2025, 07:00</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-blue-600">50 gram</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center p-4 bg-gray-50 rounded-xl hover:bg-blue-50 transition">
                            <div class="w-10 h-10 bg-yellow-500 rounded-lg flex items-center justify-center mr-4">
                                <i class="ri-alert-line text-white"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-900">Deteksi Air Keruh</p>
                                <p class="text-xs text-gray-500">24 Nov 2025, 18:30</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-yellow-600">750</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats & Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Jadwal Hari Ini -->
            <div class="bg-white rounded-2xl shadow-lg p-6 card-hover">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <div class="icon-box w-12 h-12 rounded-xl flex items-center justify-center mr-3">
                            <i class="ri-calendar-check-line text-2xl text-blue-600"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Jadwal Hari Ini</h3>
                            <p class="text-sm text-gray-500">3 jadwal aktif</p>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-4 bg-green-50 border-l-4 border-green-500 rounded-xl">
                        <div class="flex items-center">
                            <i class="ri-checkbox-circle-fill text-green-500 text-xl mr-3"></i>
                            <div>
                                <p class="font-semibold text-gray-900">07:00</p>
                                <p class="text-sm text-gray-500">Pagi</p>
                            </div>
                        </div>
                        <span class="text-sm font-bold text-green-700">50g - Selesai</span>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-green-50 border-l-4 border-green-500 rounded-xl">
                        <div class="flex items-center">
                            <i class="ri-checkbox-circle-fill text-green-500 text-xl mr-3"></i>
                            <div>
                                <p class="font-semibold text-gray-900">12:00</p>
                                <p class="text-sm text-gray-500">Siang</p>
                            </div>
                        </div>
                        <span class="text-sm font-bold text-green-700">50g - Selesai</span>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-blue-50 border-l-4 border-blue-500 rounded-xl">
                        <div class="flex items-center">
                            <i class="ri-time-line text-blue-500 text-xl mr-3"></i>
                            <div>
                                <p class="font-semibold text-gray-900">16:00</p>
                                <p class="text-sm text-gray-500">Sore</p>
                            </div>
                        </div>
                        <span class="text-sm font-bold text-blue-700">50g - Menunggu</span>
                    </div>
                </div>
            </div>

            <!-- Info Sistem -->
            <div class="bg-white rounded-2xl shadow-lg p-6 card-hover">
                <div class="flex items-center mb-6">
                    <div class="icon-box w-12 h-12 rounded-xl flex items-center justify-center mr-3">
                        <i class="ri-information-line text-2xl text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Informasi Sistem</h3>
                        <p class="text-sm text-gray-500">Detail perangkat IoT</p>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Status Koneksi</span>
                        <span class="text-sm font-semibold text-green-600 flex items-center">
                            <i class="ri-wifi-line mr-1"></i> Terhubung
                        </span>
                    </div>
                    
                    <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Update Terakhir</span>
                        <span class="text-sm font-semibold text-gray-900">25 Nov, 14:30</span>
                    </div>
                    
                    <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Sensor Ultrasonik</span>
                        <span class="text-sm font-semibold text-green-600 flex items-center">
                            <i class="ri-checkbox-circle-line mr-1"></i> Normal
                        </span>
                    </div>
                    
                    <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Sensor LDR</span>
                        <span class="text-sm font-semibold text-green-600 flex items-center">
                            <i class="ri-checkbox-circle-line mr-1"></i> Normal
                        </span>
                    </div>
                    
                    <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Servo Motor</span>
                        <span class="text-sm font-semibold text-green-600 flex items-center">
                            <i class="ri-checkbox-circle-line mr-1"></i> Siap
                        </span>
                    </div>
                    
                    <div class="mt-4 p-4 gradient-blue-soft rounded-xl">
                        <p class="text-sm text-blue-900 font-medium text-center">
                            <i class="ri-shield-check-line mr-1"></i>
                            Semua sistem berjalan normal
                        </p>
                    </div>
                </div>
            </div>
        </div>
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

</body>
</html>