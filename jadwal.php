<?php
require 'config/db.php';

// Ambil semua jadwal
$stmt = $db->prepare("SELECT * FROM jadwal_pakan ORDER BY jam ASC");
$stmt->execute();
$jadwal_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Pakan - Monitoring Pakan Ikan</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
                <a href="laporan.php" class="px-3 py-2 rounded text-gray-700 hover:bg-gray-200 font-semibold">Laporan</a>
                <a href="jadwal.php" class="px-3 py-2 rounded bg-blue-600 text-white font-semibold">Jadwal Pakan</a>
                <a href="pengaturan.php" class="px-3 py-2 rounded text-gray-700 hover:bg-gray-200 font-semibold">Pengaturan</a>
            </div>
        </nav>
    </header>

    <main class="container mx-auto p-6">
        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-800">📅 Jadwal Pemberian Pakan</h1>
                <button onclick="showAddModal()" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                    + Tambah Jadwal
                </button>
            </div>

            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                <p class="text-blue-700">
                    <strong>ℹ️ Cara Kerja:</strong> Perangkat IoT akan mengecek jadwal setiap menit. Ketika waktu sesuai dengan jadwal yang aktif, pakan akan diberikan secara otomatis.
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Berat Pakan (gram)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($jadwal_list)): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                    Belum ada jadwal pakan. Klik "Tambah Jadwal" untuk menambahkan.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($jadwal_list as $jadwal): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-lg font-bold text-gray-900">
                                            <?php echo date('H:i', strtotime($jadwal['jam'])); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900"><?php echo $jadwal['berat_pakan']; ?> gram</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if ($jadwal['aktif'] == 1): ?>
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                🟢 Aktif
                                            </span>
                                        <?php else: ?>
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                ⚪ Nonaktif
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button onclick='editJadwal(<?php echo json_encode($jadwal); ?>)' class="text-blue-600 hover:text-blue-900 mr-3">Edit</button>
                                        <button onclick="toggleJadwal(<?php echo $jadwal['id']; ?>, <?php echo $jadwal['aktif']; ?>)" class="text-yellow-600 hover:text-yellow-900 mr-3">
                                            <?php echo $jadwal['aktif'] == 1 ? 'Nonaktifkan' : 'Aktifkan'; ?>
                                        </button>
                                        <button onclick="deleteJadwal(<?php echo $jadwal['id']; ?>)" class="text-red-600 hover:text-red-900">Hapus</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal Tambah/Edit Jadwal -->
    <div id="jadwalModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 id="modalTitle" class="text-lg font-bold text-gray-900 mb-4">Tambah Jadwal Pakan</h3>
                <form id="jadwalForm" onsubmit="submitJadwal(event)">
                    <input type="hidden" id="jadwalId" name="id">
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Jam</label>
                        <input type="time" id="jamInput" name="jam" required 
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Berat Pakan (gram)</label>
                        <input type="number" id="beratInput" name="berat_pakan" value="50" min="1" max="500" required 
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <div class="flex items-center mb-4">
                        <input type="checkbox" id="aktifInput" name="aktif" checked class="mr-2">
                        <label for="aktifInput" class="text-sm text-gray-700">Jadwal Aktif</label>
                    </div>

                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="closeModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                            Batal
                        </button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showAddModal() {
            document.getElementById('modalTitle').textContent = 'Tambah Jadwal Pakan';
            document.getElementById('jadwalForm').reset();
            document.getElementById('jadwalId').value = '';
            document.getElementById('aktifInput').checked = true;
            document.getElementById('jadwalModal').classList.remove('hidden');
        }

        function editJadwal(jadwal) {
            document.getElementById('modalTitle').textContent = 'Edit Jadwal Pakan';
            document.getElementById('jadwalId').value = jadwal.id;
            document.getElementById('jamInput').value = jadwal.jam.substring(0, 5);
            document.getElementById('beratInput').value = jadwal.berat_pakan;
            document.getElementById('aktifInput').checked = jadwal.aktif == 1;
            document.getElementById('jadwalModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('jadwalModal').classList.add('hidden');
        }

        async function submitJadwal(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            formData.set('aktif', document.getElementById('aktifInput').checked ? '1' : '0');

            try {
                const response = await fetch('api/kelola_jadwal.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                
                if (result.status === 'success') {
                    alert(result.message);
                    location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                alert('Terjadi kesalahan: ' + error.message);
            }
        }

        async function toggleJadwal(id, currentStatus) {
            const newStatus = currentStatus == 1 ? 0 : 1;
            const formData = new FormData();
            formData.append('action', 'toggle');
            formData.append('id', id);
            formData.append('aktif', newStatus);

            try {
                const response = await fetch('api/kelola_jadwal.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                
                if (result.status === 'success') {
                    location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                alert('Terjadi kesalahan: ' + error.message);
            }
        }

        async function deleteJadwal(id) {
            if (!confirm('Yakin ingin menghapus jadwal ini?')) return;

            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('id', id);

            try {
                const response = await fetch('api/kelola_jadwal.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                
                if (result.status === 'success') {
                    alert(result.message);
                    location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                alert('Terjadi kesalahan: ' + error.message);
            }
        }
    </script>
</body>
</html>