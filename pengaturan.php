<?php
require 'config/db.php';

// Ambil data pengaturan saat ini
$stmt = $db->prepare("SELECT * FROM pengaturan WHERE id = 1");
$stmt->execute();
$setting = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Sistem - Monitoring Pakan Ikan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        * { font-family: 'Inter', sans-serif; }
        .gradient-blue { background: linear-gradient(135deg, #004bd4 0%, #007bff 100%); }
        .card-hover { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 20px 25px -5px rgba(0, 75, 212, 0.1); }
        .nav-link { position: relative; transition: all 0.3s ease; }
        .nav-link::after { content: ''; position: absolute; bottom: -2px; left: 0; width: 0; height: 3px; background: linear-gradient(90deg, #004bd4, #007bff); transition: width 0.3s ease; border-radius: 3px; }
        .nav-link:hover::after, .nav-link.active::after { width: 100%; }
        .icon-box { background: linear-gradient(135deg, #e0f2fe 0%, #dbeafe 100%); }
    </style>
</head>
<body class="bg-gray-50">
    
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
                    <a href="jadwal.php" class="nav-link px-4 py-2 text-sm font-medium text-gray-600 hover:text-blue-600">
                        <i class="ri-calendar-line mr-1"></i> Jadwal
                    </a>
                    <a href="pengaturan.php" class="nav-link active px-4 py-2 text-sm font-medium text-blue-600">
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

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Pengaturan Sistem</h2>
            <p class="text-gray-600">Konfigurasi batas sensor (threshold) untuk notifikasi dan status.</p>
        </div>

        <form action="api/simpan_pengaturan.php" method="POST">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <div class="bg-white rounded-2xl shadow-lg p-6 card-hover border-t-4 border-red-500">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center mr-3">
                            <i class="ri-ruler-line text-2xl text-red-600"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Sensor Jarak Pakan</h3>
                            <p class="text-sm text-gray-500">Kalibrasi wadah pakan</p>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Batas Pakan "Habis" (cm)
                            </label>
                            <input type="number" name="batas_pakan" value="<?php echo $setting['batas_pakan_habis']; ?>" 
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none transition">
                            <p class="text-xs text-gray-500 mt-2">
                                <i class="ri-information-line mr-1"></i>
                                Jika sensor membaca jarak ≥ nilai ini, status menjadi HABIS.
                            </p>
                        </div>
                        
                        <div class="bg-red-50 rounded-xl p-4 border border-red-100">
                            <h4 class="text-sm font-semibold text-red-900 mb-2">Logika Sistem:</h4>
                            <ul class="text-sm text-red-700 space-y-1 list-disc list-inside">
                                <li><strong>≥ <?php echo $setting['batas_pakan_habis']; ?> cm:</strong> Habis</li>
                                <li><strong><?php echo $setting['batas_pakan_habis']-2; ?> - <?php echo $setting['batas_pakan_habis']-1; ?> cm:</strong> Hampir Habis</li>
                                <li><strong>&lt; <?php echo $setting['batas_pakan_habis']-2; ?> cm:</strong> Aman/Penuh</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-lg p-6 card-hover border-t-4 border-yellow-500">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center mr-3">
                            <i class="ri-contrast-line text-2xl text-yellow-600"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Sensor Kekeruhan</h3>
                            <p class="text-sm text-gray-500">Kalibrasi kualitas air</p>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Batas Air "Keruh" (Nilai Analog)
                            </label>
                            <input type="number" name="batas_air" value="<?php echo $setting['batas_air_keruh']; ?>" 
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none transition">
                            <p class="text-xs text-gray-500 mt-2">
                                <i class="ri-information-line mr-1"></i>
                                Nilai sensor LDR (0-1024). Semakin tinggi biasanya semakin keruh/gelap.
                            </p>
                        </div>
                        
                        <div class="bg-yellow-50 rounded-xl p-4 border border-yellow-100">
                            <h4 class="text-sm font-semibold text-yellow-900 mb-2">Logika Sistem:</h4>
                            <ul class="text-sm text-yellow-700 space-y-1 list-disc list-inside">
                                <li><strong>> <?php echo $setting['batas_air_keruh']; ?>:</strong> Status Keruh</li>
                                <li><strong>≤ <?php echo $setting['batas_air_keruh']; ?>:</strong> Status Jernih</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="gradient-blue text-white px-8 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl hover:-translate-y-1 transition flex items-center space-x-2">
                    <i class="ri-save-3-line text-xl"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>

    </main>

    <footer class="bg-white border-t border-gray-100 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-sm text-gray-500">© 2025 Monitoring Pakan Ikan - Universitas Lampung</p>
                <p class="text-sm text-gray-500 mt-2 md:mt-0">Powered by IoT Technology</p>
            </div>
        </div>
    </footer>

</body>
</html>