<?php
// pengaturan.php
require 'config/db.php';

// Ambil data pengaturan saat ini dari database
$stmt = $db->prepare("SELECT * FROM pengaturan WHERE id = 1");
$stmt->execute();
$setting = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Sistem</title>
    <script src="https://cdn.tailwindcss.com"></script>
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


    <main class="container mx-auto p-6">
        <div id="pengaturan" class="mt-12">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Pengaturan Sistem</h2>
                <p class="text-gray-600">Konfigurasi threshold sensor dan parameter sistem</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- Pengaturan Sensor Pakan -->
                <div class="bg-white rounded-2xl shadow-lg p-6 card-hover">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center mr-3">
                            <i class="ri-ruler-line text-2xl text-red-600"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Sensor Jarak Pakan</h3>
                            <p class="text-sm text-gray-500">Batas deteksi pakan habis</p>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Jarak Pakan Habis (cm)
                            </label>
                            <input type="number" value="15" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none transition">
                            <p class="text-xs text-gray-500 mt-2">
                                <i class="ri-information-line mr-1"></i>
                                Jika jarak ≥ 15 cm, status akan berubah menjadi "Habis"
                            </p>
                        </div>
                        
                        <div class="bg-gray-50 rounded-xl p-4">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3">Status Levels:</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600">≥ 15 cm</span>
                                    <span class="px-2 py-1 bg-red-100 text-red-700 rounded font-semibold">Habis</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600">13-14 cm</span>
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded font-semibold">Hampir Habis</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600">10-12 cm</span>
                                    <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded font-semibold">Sedang</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600">0-9 cm</span>
                                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded font-semibold">Penuh</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pengaturan Sensor Air -->
                <div class="bg-white rounded-2xl shadow-lg p-6 card-hover">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center mr-3">
                            <i class="ri-contrast-line text-2xl text-yellow-600"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Sensor Kekeruhan Air</h3>
                            <p class="text-sm text-gray-500">Batas deteksi air keruh</p>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Nilai Batas Kekeruhan
                            </label>
                            <input type="number" value="700" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none transition">
                            <p class="text-xs text-gray-500 mt-2">
                                <i class="ri-information-line mr-1"></i>
                                Jika nilai sensor > 700, status akan berubah menjadi "Keruh"
                            </p>
                        </div>
                        
                        <div class="bg-gray-50 rounded-xl p-4">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3">Status Levels:</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600">≤ 700</span>
                                    <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded font-semibold">Jernih</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600">> 700</span>
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded font-semibold">Keruh</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div class="mt-6 flex justify-end">
                <button class="gradient-blue text-white px-8 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transition flex items-center space-x-2">
                    <i class="ri-save-line text-xl"></i>
                    <span>Simpan Pengaturan</span>
                </button>
            </div>
        </div>

    </main>

    <!-- Modal Add/Edit Schedule -->
    <div id="scheduleModal" class="hidden fixed inset-0 bg-black bg-opacity-50 modal-overlay z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 transform transition-all">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 gradient-blue rounded-xl flex items-center justify-center">
                        <i class="ri-calendar-line text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Tambah Jadwal Baru</h3>
                </div>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="ri-close-line text-2xl"></i>
                </button>
            </div>
            
            <form class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="ri-time-line mr-1"></i>
                        Waktu Pemberian
                    </label>
                    <input type="time" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none transition">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="ri-scales-3-line mr-1"></i>
                        Berat Pakan (gram)
                    </label>
                    <input type="number" value="50" min="1" max="500" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none transition">
                </div>
                
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                    <div>
                        <p class="text-sm font-semibold text-gray-900">Status Jadwal</p>
                        <p class="text-xs text-gray-500">Aktifkan jadwal ini</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" checked>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
                
                <div class="flex space-x-3 pt-4">
                    <button type="button" onclick="closeModal()" class="flex-1 px-6 py-3 border-2 border-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 gradient-blue text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

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
        function openAddModal() {
            document.getElementById('scheduleModal').classList.remove('hidden');
        }
        
        function closeModal() {
            document.getElementById('scheduleModal').classList.add('hidden');
        }
        
        function editSchedule(id) {
            // Populate modal with existing data
            openAddModal();
        }
    </script>

</body>
</html>