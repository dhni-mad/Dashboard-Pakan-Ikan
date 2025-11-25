<?php
require 'config/db.php';

// Ambil semua jadwal
$stmt = $db->prepare("SELECT * FROM jadwal_pakan ORDER BY jam ASC");
$stmt->execute();
$jadwal_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Pakan - Monitoring Pakan Ikan</title>
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
            box-shadow: 0 20px 25px -5px rgba(0, 75, 212, 0.1);
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
        
        .icon-box {
            background: linear-gradient(135deg, #e0f2fe 0%, #dbeafe 100%);
        }
        
        .modal-overlay {
            backdrop-filter: blur(4px);
        }
        
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 48px;
            height: 24px;
        }
        
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #cbd5e1;
            transition: 0.4s;
            border-radius: 24px;
        }
        
        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: 0.4s;
            border-radius: 50%;
        }
        
        input:checked + .toggle-slider {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        
        input:checked + .toggle-slider:before {
            transform: translateX(24px);
        }
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
                    <a href="analisis.php" class="nav-link px-4 py-2 text-sm font-medium text-gray-600 hover:text-blue-600">
                        <i class="ri-bar-chart-line mr-1"></i> Analisis
                    </a>
                    <a href="laporan.php" class="nav-link px-4 py-2 text-sm font-medium text-gray-600 hover:text-blue-600">
                        <i class="ri-file-text-line mr-1"></i> Laporan
                    </a>
                    <a href="jadwal.php" class="nav-link active px-4 py-2 text-sm font-medium text-blue-600">
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
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Jadwal Pemberian Pakan</h1>
                    <p class="text-gray-600">Kelola jadwal otomatis pemberian pakan ikan</p>
                </div>
                <button onclick="openAddModal()" class="gradient-blue text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition flex items-center space-x-2">
                    <i class="ri-add-circle-line text-xl"></i>
                    <span>Tambah Jadwal</span>
                </button>
            </div>
        </div>

        <!-- Info Banner -->
        <div class="mb-6 gradient-blue-soft rounded-2xl p-6 border-l-4 border-blue-500">
            <div class="flex items-start space-x-4">
                <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="ri-information-line text-white text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Cara Kerja Jadwal Otomatis</h3>
                    <p class="text-sm text-gray-700">
                        Perangkat IoT akan mengecek jadwal setiap menit. Ketika waktu sesuai dengan jadwal yang aktif, 
                        sistem akan memberikan pakan secara otomatis dengan berat yang telah ditentukan.
                    </p>
                </div>
            </div>
        </div>

        <!-- Schedule Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            
            <!-- Jadwal 1: Pagi -->
            <div class="bg-white rounded-2xl shadow-lg p-6 card-hover border-l-4 border-green-500">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                            <i class="ri-sun-line text-2xl text-green-600"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Pagi</h3>
                            <p class="text-sm text-gray-500">Morning Feed</p>
                        </div>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" checked>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
                
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Waktu</span>
                        <span class="text-2xl font-bold text-gray-900">07:00</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Berat Pakan</span>
                        <span class="text-lg font-bold text-green-600">50 gram</span>
                    </div>
                </div>
                
                <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                        <i class="ri-checkbox-circle-fill mr-1"></i>
                        Aktif
                    </span>
                    <div class="flex space-x-2">
                        <button onclick="editSchedule(1)" class="text-blue-600 hover:text-blue-700 text-sm font-semibold">
                            <i class="ri-edit-line"></i> Edit
                        </button>
                        <button class="text-red-600 hover:text-red-700 text-sm font-semibold">
                            <i class="ri-delete-bin-line"></i> Hapus
                        </button>
                    </div>
                </div>
            </div>

            <!-- Jadwal 2: Siang -->
            <div class="bg-white rounded-2xl shadow-lg p-6 card-hover border-l-4 border-yellow-500">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                            <i class="ri-sun-fill text-2xl text-yellow-600"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Siang</h3>
                            <p class="text-sm text-gray-500">Noon Feed</p>
                        </div>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" checked>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
                
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Waktu</span>
                        <span class="text-2xl font-bold text-gray-900">12:00</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Berat Pakan</span>
                        <span class="text-lg font-bold text-yellow-600">50 gram</span>
                    </div>
                </div>
                
                <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                        <i class="ri-checkbox-circle-fill mr-1"></i>
                        Aktif
                    </span>
                    <div class="flex space-x-2">
                        <button onclick="editSchedule(2)" class="text-blue-600 hover:text-blue-700 text-sm font-semibold">
                            <i class="ri-edit-line"></i> Edit
                        </button>
                        <button class="text-red-600 hover:text-red-700 text-sm font-semibold">
                            <i class="ri-delete-bin-line"></i> Hapus
                        </button>
                    </div>
                </div>
            </div>

            <!-- Jadwal 3: Sore -->
            <div class="bg-white rounded-2xl shadow-lg p-6 card-hover border-l-4 border-orange-500">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                            <i class="ri-moon-line text-2xl text-orange-600"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Sore</h3>
                            <p class="text-sm text-gray-500">Evening Feed</p>
                        </div>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" checked>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
                
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Waktu</span>
                        <span class="text-2xl font-bold text-gray-900">16:00</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Berat Pakan</span>
                        <span class="text-lg font-bold text-orange-600">50 gram</span>
                    </div>
                </div>
                
                <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                        <i class="ri-checkbox-circle-fill mr-1"></i>
                        Aktif
                    </span>
                    <div class="flex space-x-2">
                        <button onclick="editSchedule(3)" class="text-blue-600 hover:text-blue-700 text-sm font-semibold">
                            <i class="ri-edit-line"></i> Edit
                        </button>
                        <button class="text-red-600 hover:text-red-700 text-sm font-semibold">
                            <i class="ri-delete-bin-line"></i> Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </script>
</body>
</html>