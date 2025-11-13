<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - Monitoring Pakan Ikan</title>
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
                <a href="analisis.php" class="px-3 py-2 rounded text-gray-700 hover:bg-gray-200 font-semibold">Analisis Data</a>
                <a href="laporan.php" class="px-3 py-2 rounded bg-blue-600 text-white font-semibold">Laporan</a>
            </div>
        </nav>
    </header>

    <main class="container mx-auto p-6">
        <!-- Header dan Filter -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Laporan Data Sistem</h1>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Filter Periode -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Periode Laporan</label>
                    <select id="periodePicker" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="harian">Harian</option>
                        <option value="mingguan">Mingguan</option>
                        <option value="bulanan">Bulanan</option>
                    </select>
                </div>
                
                <!-- Filter Tanggal -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Tanggal</label>
                    <input type="date" id="tanggalPicker" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <!-- Tombol Generate -->
                <div class="flex items-end">
                    <button id="generateBtn" class="w-full bg-blue-600 text-white font-bold py-2 px-6 rounded-lg hover:bg-blue-700 transition duration-300">
                        Generate Laporan
                    </button>
                </div>
            </div>
            
            <!-- Info Periode -->
            <div id="infoPeriode" class="mt-4 p-4 bg-blue-50 border-l-4 border-blue-500 text-blue-700 hidden">
                <p class="font-semibold">Periode: <span id="periodeText"></span></p>
                <p class="text-sm">Dari: <span id="startDate"></span> sampai <span id="endDate"></span></p>
            </div>
        </div>

        <!-- Loading Indicator -->
        <div id="loadingIndicator" class="hidden text-center py-8">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
            <p class="mt-4 text-gray-600">Memuat data...</p>
        </div>

        <!-- Konten Laporan -->
        <div id="laporanContent" class="hidden">
            
            <!-- Ringkasan Statistik -->
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Ringkasan Statistik</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                
                <!-- Card Total Pemberian Pakan -->
                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <h3 class="text-lg font-semibold text-gray-600 mb-2">Total Pemberian</h3>
                    <div class="text-4xl font-bold text-blue-600" id="totalPemberian">0</div>
                    <div class="text-sm text-gray-500">kali</div>
                </div>

                <!-- Card Total Berat Pakan -->
                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <h3 class="text-lg font-semibold text-gray-600 mb-2">Total Pakan</h3>
                    <div class="text-4xl font-bold text-green-600" id="totalBerat">0</div>
                    <div class="text-sm text-gray-500">gram</div>
                </div>

                <!-- Card Rata-rata Berat -->
                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <h3 class="text-lg font-semibold text-gray-600 mb-2">Rata-rata/Pemberian</h3>
                    <div class="text-4xl font-bold text-purple-600" id="rataBerat">0</div>
                    <div class="text-sm text-gray-500">gram</div>
                </div>

                <!-- Card Status Air -->
                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <h3 class="text-lg font-semibold text-gray-600 mb-2">Air Keruh</h3>
                    <div class="text-4xl font-bold text-yellow-700" id="jumlahKeruh">0</div>
                    <div class="text-sm text-gray-500">kejadian</div>
                </div>
            </div>

            <!-- Detail Statistik -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                
                <!-- Detail Pakan -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-xl font-semibold text-gray-700 mb-4">Detail Pakan</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between border-b pb-2">
                            <span class="text-gray-600">Berat Minimum:</span>
                            <span class="font-semibold text-gray-800" id="minBerat">-</span>
                        </div>
                        <div class="flex justify-between border-b pb-2">
                            <span class="text-gray-600">Berat Maksimum:</span>
                            <span class="font-semibold text-gray-800" id="maxBerat">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Rata-rata Berat:</span>
                            <span class="font-semibold text-blue-600" id="avgBerat">-</span>
                        </div>
                    </div>
                </div>

                <!-- Detail Kekeruhan -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-xl font-semibold text-gray-700 mb-4">Detail Kekeruhan</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between border-b pb-2">
                            <span class="text-gray-600">Nilai Minimum:</span>
                            <span class="font-semibold text-gray-800" id="minKekeruhan">-</span>
                        </div>
                        <div class="flex justify-between border-b pb-2">
                            <span class="text-gray-600">Nilai Maksimum:</span>
                            <span class="font-semibold text-gray-800" id="maxKekeruhan">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Rata-rata:</span>
                            <span class="font-semibold text-yellow-700" id="avgKekeruhan">-</span>
                        </div>
                    </div>
                </div>

                <!-- Detail Level Pakan -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-xl font-semibold text-gray-700 mb-4">Detail Level Pakan</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between border-b pb-2">
                            <span class="text-gray-600">Jarak Minimum:</span>
                            <span class="font-semibold text-gray-800" id="minJarak">-</span>
                        </div>
                        <div class="flex justify-between border-b pb-2">
                            <span class="text-gray-600">Jarak Maksimum:</span>
                            <span class="font-semibold text-gray-800" id="maxJarak">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Rata-rata:</span>
                            <span class="font-semibold text-green-600" id="avgJarak">-</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grafik -->
            <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                <h3 class="text-xl font-semibold text-gray-700 mb-4">Grafik Pemberian Pakan</h3>
                <canvas id="grafikLaporan"></canvas>
            </div>

            <!-- Tabel Detail Pemberian Pakan -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold text-gray-700">Riwayat Pemberian Pakan</h3>
                    <button id="exportBtn" class="bg-green-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-green-700 transition duration-300">
                        Export ke CSV
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Berat Pakan (gram)</th>
                            </tr>
                        </thead>
                        <tbody id="tabelDetailPakan" class="bg-white divide-y divide-gray-200">
                            <!-- Data akan diisi oleh JavaScript -->
                        </tbody>
                    </table>
                </div>
                <div id="noDataMessage" class="text-center py-8 text-gray-500 hidden">
                    Tidak ada data pemberian pakan pada periode ini.
                </div>
            </div>
        </div>
    </main>

    <script>
        let currentData = null;
        let currentChart = null;

        document.addEventListener('DOMContentLoaded', () => {
            // Set tanggal hari ini sebagai default
            document.getElementById('tanggalPicker').value = new Date().toISOString().split('T')[0];
            
            // Event listener untuk tombol generate
            document.getElementById('generateBtn').addEventListener('click', generateLaporan);
            
            // Event listener untuk export
            document.getElementById('exportBtn').addEventListener('click', exportToCSV);
        });

        async function generateLaporan() {
            const periode = document.getElementById('periodePicker').value;
            const tanggal = document.getElementById('tanggalPicker').value;
            
            if (!tanggal) {
                alert('Silakan pilih tanggal terlebih dahulu');
                return;
            }
            
            // Tampilkan loading
            document.getElementById('loadingIndicator').classList.remove('hidden');
            document.getElementById('laporanContent').classList.add('hidden');
            
            try {
                const response = await fetch(`api/get_laporan.php?periode=${periode}&tanggal=${tanggal}`);
                const data = await response.json();
                
                if (data.error) {
                    throw new Error(data.error);
                }
                
                currentData = data;
                displayLaporan(data);
                
            } catch (error) {
                console.error('Error:', error);
                alert('Gagal memuat data laporan: ' + error.message);
            } finally {
                document.getElementById('loadingIndicator').classList.add('hidden');
            }
        }

        function displayLaporan(data) {
            // Tampilkan konten
            document.getElementById('laporanContent').classList.remove('hidden');
            
            // Update info periode
            const infoPeriode = document.getElementById('infoPeriode');
            infoPeriode.classList.remove('hidden');
            document.getElementById('periodeText').textContent = data.info.periode.charAt(0).toUpperCase() + data.info.periode.slice(1);
            document.getElementById('startDate').textContent = formatDate(data.info.start_date);
            document.getElementById('endDate').textContent = formatDate(data.info.end_date);
            
            // Update statistik utama
            document.getElementById('totalPemberian').textContent = data.pakan.jumlah_pemberian || 0;
            document.getElementById('totalBerat').textContent = parseFloat(data.pakan.total_berat || 0).toFixed(1);
            document.getElementById('rataBerat').textContent = parseFloat(data.pakan.rata_rata_berat || 0).toFixed(1);
            document.getElementById('jumlahKeruh').textContent = data.kekeruhan.jumlah_keruh || 0;
            
            // Update detail pakan
            document.getElementById('minBerat').textContent = (data.pakan.min_berat ? parseFloat(data.pakan.min_berat).toFixed(1) + ' gr' : '-');
            document.getElementById('maxBerat').textContent = (data.pakan.max_berat ? parseFloat(data.pakan.max_berat).toFixed(1) + ' gr' : '-');
            document.getElementById('avgBerat').textContent = (data.pakan.rata_rata_berat ? parseFloat(data.pakan.rata_rata_berat).toFixed(1) + ' gr' : '-');
            
            // Update detail kekeruhan
            document.getElementById('minKekeruhan').textContent = (data.kekeruhan.min_nilai || '-');
            document.getElementById('maxKekeruhan').textContent = (data.kekeruhan.max_nilai || '-');
            document.getElementById('avgKekeruhan').textContent = (data.kekeruhan.rata_rata ? parseFloat(data.kekeruhan.rata_rata).toFixed(0) : '-');
            
            // Update detail jarak
            document.getElementById('minJarak').textContent = (data.jarak.min_jarak ? parseFloat(data.jarak.min_jarak).toFixed(1) + ' cm' : '-');
            document.getElementById('maxJarak').textContent = (data.jarak.max_jarak ? parseFloat(data.jarak.max_jarak).toFixed(1) + ' cm' : '-');
            document.getElementById('avgJarak').textContent = (data.jarak.rata_rata ? parseFloat(data.jarak.rata_rata).toFixed(1) + ' cm' : '-');
            
            // Update grafik
            updateChart(data.grafik_pakan);
            
            // Update tabel
            updateTable(data.detail_pakan);
        }

        function updateChart(grafikData) {
            const ctx = document.getElementById('grafikLaporan').getContext('2d');
            
            // Destroy chart lama jika ada
            if (currentChart) {
                currentChart.destroy();
            }
            
            const labels = grafikData.map(item => item.label);
            const dataValues = grafikData.map(item => parseFloat(item.total));
            
            currentChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total Berat Pakan (gram)',
                        data: dataValues,
                        backgroundColor: 'rgba(59, 130, 246, 0.7)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Berat (gram)'
                            }
                        }
                    }
                }
            });
        }

        function updateTable(detailData) {
            const tbody = document.getElementById('tabelDetailPakan');
            const noDataMsg = document.getElementById('noDataMessage');
            
            tbody.innerHTML = '';
            
            if (detailData.length === 0) {
                noDataMsg.classList.remove('hidden');
                tbody.closest('table').classList.add('hidden');
                return;
            }
            
            noDataMsg.classList.add('hidden');
            tbody.closest('table').classList.remove('hidden');
            
            detailData.forEach((item, index) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${index + 1}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.waktu_format}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-blue-600">${parseFloat(item.berat_pakan).toFixed(1)}</td>
                `;
                tbody.appendChild(row);
            });
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            const options = { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' };
            return date.toLocaleDateString('id-ID', options);
        }

        function exportToCSV() {
            if (!currentData || !currentData.detail_pakan || currentData.detail_pakan.length === 0) {
                alert('Tidak ada data untuk diekspor');
                return;
            }
            
            // Header CSV
            let csv = 'No,Waktu,Berat Pakan (gram)\n';
            
            // Data rows
            currentData.detail_pakan.forEach((item, index) => {
                csv += `${index + 1},"${item.waktu_format}",${item.berat_pakan}\n`;
            });
            
            // Tambahkan ringkasan di akhir
            csv += '\n\nRINGKASAN\n';
            csv += `Periode,${currentData.info.periode}\n`;
            csv += `Total Pemberian,${currentData.pakan.jumlah_pemberian}\n`;
            csv += `Total Berat,${parseFloat(currentData.pakan.total_berat || 0).toFixed(1)} gram\n`;
            csv += `Rata-rata per Pemberian,${parseFloat(currentData.pakan.rata_rata_berat || 0).toFixed(1)} gram\n`;
            
            // Download file
            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            const periode = currentData.info.periode;
            const tanggal = currentData.info.tanggal_request;
            
            link.setAttribute('href', url);
            link.setAttribute('download', `laporan_pakan_${periode}_${tanggal}.csv`);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
</body>
</html>