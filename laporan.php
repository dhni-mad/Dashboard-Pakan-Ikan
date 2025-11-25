<?php
require 'config/db.php';
// Tidak perlu query PHP di sini karena data diambil via API (AJAX)
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - Monitoring Pakan Ikan</title>
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
                    <a href="laporan.php" class="nav-link active px-4 py-2 text-sm font-medium text-blue-600">
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

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
            <div class="mb-6 border-b border-gray-100 pb-4">
                <h1 class="text-2xl font-bold text-gray-900">Laporan Data Sistem</h1>
                <p class="text-gray-500 text-sm">Generate laporan historis berdasarkan periode</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Periode</label>
                    <div class="relative">
                        <i class="ri-calendar-event-line absolute left-4 top-3.5 text-gray-400"></i>
                        <select id="periodePicker" class="w-full pl-10 pr-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none transition appearance-none">
                            <option value="harian">Harian</option>
                            <option value="mingguan">Mingguan</option>
                            <option value="bulanan">Bulanan</option>
                        </select>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Referensi</label>
                    <div class="relative">
                        <i class="ri-calendar-line absolute left-4 top-3.5 text-gray-400"></i>
                        <input type="date" id="tanggalPicker" class="w-full pl-10 pr-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none transition">
                    </div>
                </div>
                
                <div class="flex items-end">
                    <button id="generateBtn" class="w-full gradient-blue text-white font-bold py-3 px-6 rounded-xl shadow-lg hover:shadow-xl transition flex items-center justify-center space-x-2">
                        <i class="ri-file-search-line text-xl"></i>
                        <span>Tampilkan Data</span>
                    </button>
                </div>
            </div>

            <div id="infoPeriode" class="hidden mt-6 p-4 bg-blue-50 rounded-xl border border-blue-100 flex items-start space-x-3">
                <i class="ri-information-fill text-blue-600 text-xl mt-0.5"></i>
                <div>
                    <p class="font-semibold text-blue-900">Periode Laporan: <span id="periodeText" class="capitalize"></span></p>
                    <p class="text-sm text-blue-700">Rentang data: <span id="startDate"></span> s/d <span id="endDate"></span></p>
                </div>
            </div>
        </div>

        <div id="loadingIndicator" class="hidden text-center py-12">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-blue-200 border-t-blue-600"></div>
            <p class="mt-4 text-gray-600 font-medium">Sedang memuat data...</p>
        </div>

        <div id="laporanContent" class="hidden space-y-8">
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white p-6 rounded-2xl shadow-lg card-hover">
                    <p class="text-sm text-gray-500 font-medium mb-1">Total Frekuensi</p>
                    <div class="flex items-baseline space-x-2">
                        <h3 class="text-3xl font-bold text-gray-900" id="totalPemberian">0</h3>
                        <span class="text-sm text-gray-500">kali</span>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-lg card-hover">
                    <p class="text-sm text-gray-500 font-medium mb-1">Total Pakan Keluar</p>
                    <div class="flex items-baseline space-x-2">
                        <h3 class="text-3xl font-bold text-blue-600" id="totalBerat">0</h3>
                        <span class="text-sm text-gray-500">gram</span>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-lg card-hover">
                    <p class="text-sm text-gray-500 font-medium mb-1">Rata-rata / Feed</p>
                    <div class="flex items-baseline space-x-2">
                        <h3 class="text-3xl font-bold text-purple-600" id="rataBerat">0</h3>
                        <span class="text-sm text-gray-500">gram</span>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-lg card-hover">
                    <p class="text-sm text-gray-500 font-medium mb-1">Insiden Air Keruh</p>
                    <div class="flex items-baseline space-x-2">
                        <h3 class="text-3xl font-bold text-yellow-600" id="jumlahKeruh">0</h3>
                        <span class="text-sm text-gray-500">kali</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-2xl shadow-lg card-hover">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                            <i class="ri-scales-3-line text-blue-600"></i>
                        </div>
                        <h3 class="font-bold text-gray-900">Statistik Berat</h3>
                    </div>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between py-2 border-b border-gray-50">
                            <span class="text-gray-500">Minimum</span>
                            <span class="font-semibold text-gray-900" id="minBerat">-</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-50">
                            <span class="text-gray-500">Maksimum</span>
                            <span class="font-semibold text-gray-900" id="maxBerat">-</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-lg card-hover">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-10 h-10 rounded-lg bg-yellow-100 flex items-center justify-center">
                            <i class="ri-drop-line text-yellow-600"></i>
                        </div>
                        <h3 class="font-bold text-gray-900">Statistik Kekeruhan</h3>
                    </div>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between py-2 border-b border-gray-50">
                            <span class="text-gray-500">Min Nilai</span>
                            <span class="font-semibold text-gray-900" id="minKekeruhan">-</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-50">
                            <span class="text-gray-500">Max Nilai</span>
                            <span class="font-semibold text-gray-900" id="maxKekeruhan">-</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-50">
                            <span class="text-gray-500">Rata-rata</span>
                            <span class="font-semibold text-yellow-600" id="avgKekeruhan">-</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-lg card-hover">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                            <i class="ri-ruler-line text-green-600"></i>
                        </div>
                        <h3 class="font-bold text-gray-900">Statistik Jarak Pakan</h3>
                    </div>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between py-2 border-b border-gray-50">
                            <span class="text-gray-500">Min Jarak</span>
                            <span class="font-semibold text-gray-900" id="minJarak">-</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-50">
                            <span class="text-gray-500">Max Jarak</span>
                            <span class="font-semibold text-gray-900" id="maxJarak">-</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-lg">
                <h3 class="font-bold text-gray-900 mb-6">Grafik Distribusi Pakan</h3>
                <div class="h-80">
                    <canvas id="grafikLaporan"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-bold text-gray-900">Riwayat Detail</h3>
                    <button id="exportBtn" class="flex items-center space-x-2 text-sm text-green-600 font-semibold hover:text-green-700 transition">
                        <i class="ri-file-excel-2-line text-lg"></i>
                        <span>Export CSV</span>
                    </button>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Waktu</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Berat Pakan</th>
                            </tr>
                        </thead>
                        <tbody id="tabelDetailPakan" class="divide-y divide-gray-100">
                            </tbody>
                    </table>
                </div>
                <div id="noDataMessage" class="hidden p-8 text-center text-gray-500">
                    <i class="ri-inbox-line text-4xl mb-2 block"></i>
                    Data tidak ditemukan untuk periode ini.
                </div>
            </div>

        </div>
    </main>

    <footer class="bg-white border-t border-gray-100 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-sm text-gray-500">© 2025 Monitoring Pakan Ikan - Universitas Lampung</p>
                <p class="text-sm text-gray-500 mt-2 md:mt-0">Powered by IoT Technology</p>
            </div>
        </div>
    </footer>

    <script>
        let currentData = null;
        let currentChart = null;

        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('tanggalPicker').value = new Date().toISOString().split('T')[0];
            document.getElementById('generateBtn').addEventListener('click', generateLaporan);
            document.getElementById('exportBtn').addEventListener('click', exportToCSV);
        });

        async function generateLaporan() {
            const periode = document.getElementById('periodePicker').value;
            const tanggal = document.getElementById('tanggalPicker').value;
            
            if (!tanggal) {
                alert('Silakan pilih tanggal terlebih dahulu');
                return;
            }
            
            document.getElementById('loadingIndicator').classList.remove('hidden');
            document.getElementById('laporanContent').classList.add('hidden');
            
            try {
                const response = await fetch(`api/get_laporan.php?periode=${periode}&tanggal=${tanggal}`);
                const data = await response.json();
                
                if (data.error) throw new Error(data.error);
                
                currentData = data;
                displayLaporan(data);
                
            } catch (error) {
                alert('Gagal memuat data: ' + error.message);
            } finally {
                document.getElementById('loadingIndicator').classList.add('hidden');
            }
        }

        function displayLaporan(data) {
            document.getElementById('laporanContent').classList.remove('hidden');
            document.getElementById('infoPeriode').classList.remove('hidden');
            
            // Info Header
            document.getElementById('periodeText').textContent = data.info.periode;
            document.getElementById('startDate').textContent = formatDate(data.info.start_date);
            document.getElementById('endDate').textContent = formatDate(data.info.end_date);
            
            // Summary Cards
            document.getElementById('totalPemberian').textContent = data.pakan.jumlah_pemberian || 0;
            document.getElementById('totalBerat').textContent = parseFloat(data.pakan.total_berat || 0).toFixed(1);
            document.getElementById('rataBerat').textContent = parseFloat(data.pakan.rata_rata_berat || 0).toFixed(1);
            document.getElementById('jumlahKeruh').textContent = data.kekeruhan.jumlah_keruh || 0;
            
            // Details
            const setTxt = (id, val, suffix = '') => {
                document.getElementById(id).textContent = val ? (parseFloat(val).toFixed(1) + suffix) : '-';
            };

            setTxt('minBerat', data.pakan.min_berat, ' gr');
            setTxt('maxBerat', data.pakan.max_berat, ' gr');
            
            setTxt('minKekeruhan', data.kekeruhan.min_nilai, '');
            setTxt('maxKekeruhan', data.kekeruhan.max_nilai, '');
            setTxt('avgKekeruhan', data.kekeruhan.rata_rata, '');
            
            setTxt('minJarak', data.jarak.min_jarak, ' cm');
            setTxt('maxJarak', data.jarak.max_jarak, ' cm');

            updateChart(data.grafik_pakan);
            updateTable(data.detail_pakan);
        }

        function updateChart(grafikData) {
            const ctx = document.getElementById('grafikLaporan').getContext('2d');
            if (currentChart) currentChart.destroy();
            
            currentChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: grafikData.map(item => item.label),
                    datasets: [{
                        label: 'Total Pakan (gram)',
                        data: grafikData.map(item => item.total),
                        backgroundColor: '#3b82f6',
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#f3f4f6' } },
                        x: { grid: { display: false } }
                    }
                }
            });
        }

        function updateTable(rows) {
            const tbody = document.getElementById('tabelDetailPakan');
            const noData = document.getElementById('noDataMessage');
            tbody.innerHTML = '';
            
            if (!rows || rows.length === 0) {
                noData.classList.remove('hidden');
                return;
            }
            
            noData.classList.add('hidden');
            rows.forEach((item, index) => {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-blue-50 transition';
                tr.innerHTML = `
                    <td class="px-6 py-4 text-sm text-gray-600">${index + 1}</td>
                    <td class="px-6 py-4 text-sm text-gray-900 font-medium">${item.waktu_format}</td>
                    <td class="px-6 py-4 text-sm font-bold text-blue-600">${parseFloat(item.berat_pakan).toFixed(1)} gr</td>
                `;
                tbody.appendChild(tr);
            });
        }

        function formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('id-ID', {
                day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'
            });
        }

        function exportToCSV() {
            if (!currentData || !currentData.detail_pakan.length) {
                alert('Tidak ada data untuk diekspor');
                return;
            }
            let csv = 'No,Waktu,Berat Pakan (gram)\n';
            currentData.detail_pakan.forEach((item, i) => {
                csv += `${i + 1},"${item.waktu_format}",${item.berat_pakan}\n`;
            });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(new Blob([csv], { type: 'text/csv;charset=utf-8;' }));
            link.download = `laporan_${currentData.info.periode}_${currentData.info.tanggal_request}.csv`;
            link.click();
        }
    </script>
</body>
</html>