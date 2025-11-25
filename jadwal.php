<?php
require 'config/db.php';

// Ambil semua jadwal dari database untuk ditampilkan
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
        * { font-family: 'Inter', sans-serif; }
        .gradient-blue { background: linear-gradient(135deg, #004bd4 0%, #007bff 100%); }
        .gradient-blue-soft { background: linear-gradient(135deg, #e0f2fe 0%, #dbeafe 100%); }
        .card-hover { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 20px 25px -5px rgba(0, 75, 212, 0.1); }
        .nav-link { position: relative; transition: all 0.3s ease; }
        .nav-link::after { content: ''; position: absolute; bottom: -2px; left: 0; width: 0; height: 3px; background: linear-gradient(90deg, #004bd4, #007bff); transition: width 0.3s ease; border-radius: 3px; }
        .nav-link:hover::after, .nav-link.active::after { width: 100%; }
        .modal-overlay { backdrop-filter: blur(4px); }
        
        /* Toggle Switch Custom Style */
        .toggle-switch { position: relative; display: inline-block; width: 48px; height: 24px; }
        .toggle-switch input { opacity: 0; width: 0; height: 0; }
        .toggle-slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #cbd5e1; transition: 0.4s; border-radius: 24px; }
        .toggle-slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: 0.4s; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        input:checked + .toggle-slider { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
        input:checked + .toggle-slider:before { transform: translateX(24px); }
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

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Jadwal Pemberian Pakan</h1>
                <p class="text-gray-600">Atur waktu otomatis untuk memberi makan ikan</p>
            </div>
            <button onclick="openAddModal()" class="gradient-blue text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl hover:-translate-y-1 transition flex items-center justify-center space-x-2">
                <i class="ri-add-line text-xl"></i>
                <span>Tambah Jadwal</span>
            </button>
        </div>

        <div class="mb-8 bg-blue-50 border border-blue-100 rounded-2xl p-6 flex items-start space-x-4">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="ri-timer-flash-line text-blue-600 text-2xl"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-blue-900 mb-1">Otomatisasi Sistem</h3>
                <p class="text-sm text-blue-700">
                    Sistem IoT akan mengecek database setiap saat. Jika waktu server cocok dengan jadwal yang 
                    <span class="font-bold bg-green-100 text-green-700 px-1 rounded">Aktif</span>, 
                    maka pakan akan diberikan sesuai berat yang diatur.
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            
            <?php foreach ($jadwal_list as $row): 
                // Tentukan warna card berdasarkan jam (Pagi/Siang/Sore/Malam)
                $jam = (int)substr($row['jam'], 0, 2);
                $theme = 'blue';
                $icon = 'ri-moon-line';
                if ($jam >= 5 && $jam < 11) { $theme = 'green'; $icon = 'ri-sun-line'; }
                elseif ($jam >= 11 && $jam < 15) { $theme = 'yellow'; $icon = 'ri-sun-fill'; }
                elseif ($jam >= 15 && $jam < 19) { $theme = 'orange'; $icon = 'ri-sunset-line'; }
                
                $borderClass = "border-{$theme}-500";
                $bgIconClass = "bg-{$theme}-100";
                $textIconClass = "text-{$theme}-600";
            ?>
            <div class="bg-white rounded-2xl shadow-lg p-6 card-hover border-l-4 <?php echo $borderClass; ?> relative overflow-hidden">
                <div class="flex items-start justify-between mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 <?php echo $bgIconClass; ?> rounded-xl flex items-center justify-center">
                            <i class="<?php echo $icon; ?> text-2xl <?php echo $textIconClass; ?>"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Pukul <?php echo substr($row['jam'], 0, 5); ?></h3>
                            <p class="text-xs text-gray-500">Jadwal Harian</p>
                        </div>
                    </div>
                    
                    <label class="toggle-switch" title="Aktifkan/Nonaktifkan">
                        <input type="checkbox" onchange="toggleSchedule(<?php echo $row['id']; ?>, this)" <?php echo ($row['aktif'] == 1) ? 'checked' : ''; ?>>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
                
                <div class="space-y-3 mb-6">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm text-gray-600 flex items-center"><i class="ri-scales-3-line mr-2"></i>Berat Pakan</span>
                        <span class="font-bold text-gray-900"><?php echo $row['berat_pakan']; ?> gram</span>
                    </div>
                </div>
                
                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                    <span class="status-badge text-xs font-bold px-3 py-1 rounded-full <?php echo ($row['aktif'] == 1) ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'; ?>">
                        <?php echo ($row['aktif'] == 1) ? 'AKTIF' : 'NON-AKTIF'; ?>
                    </span>
                    <div class="flex space-x-2">
                        <button onclick='editSchedule(<?php echo json_encode($row); ?>)' class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 flex items-center justify-center transition">
                            <i class="ri-pencil-line"></i>
                        </button>
                        <button onclick="deleteSchedule(<?php echo $row['id']; ?>)" class="w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 flex items-center justify-center transition">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>

        </div>
    </main>

    <div id="scheduleModal" class="hidden fixed inset-0 bg-black/60 modal-overlay z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 transform transition-all scale-100">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 gradient-blue rounded-xl flex items-center justify-center shadow-lg">
                        <i class="ri-calendar-event-line text-white text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900" id="modalTitle">Tambah Jadwal</h3>
                </div>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg p-1 transition">
                    <i class="ri-close-line text-2xl"></i>
                </button>
            </div>
            
            <form id="scheduleForm" class="space-y-5">
                <input type="hidden" id="scheduleId" name="id">
                <input type="hidden" name="action" value="save">
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Waktu Pemberian</label>
                    <input type="time" id="jamInput" name="jam" required 
                           class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none transition">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Berat Pakan (gram)</label>
                    <div class="relative">
                        <input type="number" id="beratInput" name="berat_pakan" value="50" min="1" required 
                               class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none transition">
                        <span class="absolute right-4 top-3.5 text-gray-400 text-sm font-semibold">gr</span>
                    </div>
                </div>
                
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-100">
                    <div>
                        <p class="text-sm font-bold text-gray-900">Status Awal</p>
                        <p class="text-xs text-gray-500">Aktifkan jadwal ini segera</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" id="aktifInput" name="aktif" value="1" checked>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
                
                <div class="flex space-x-3 pt-2">
                    <button type="button" onclick="closeModal()" class="flex-1 px-4 py-3 border border-gray-200 text-gray-600 font-semibold rounded-xl hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 gradient-blue text-white px-4 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <footer class="bg-white border-t border-gray-100 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-sm text-gray-500">© 2025 Monitoring Pakan Ikan - Universitas Lampung</p>
                <p class="text-sm text-gray-500 mt-2 md:mt-0">Powered by IoT Technology</p>
            </div>
        </div>
    </footer>

    <script>
        // Modal Logic
        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Tambah Jadwal';
            document.getElementById('scheduleId').value = '';
            document.getElementById('jamInput').value = '';
            document.getElementById('beratInput').value = '50';
            document.getElementById('aktifInput').checked = true;
            document.getElementById('scheduleModal').classList.remove('hidden');
        }
        
        function editSchedule(data) {
            document.getElementById('modalTitle').textContent = 'Edit Jadwal';
            document.getElementById('scheduleId').value = data.id;
            document.getElementById('jamInput').value = data.jam; // Format HH:mm:ss is accepted by time input usually, or slice it
            document.getElementById('beratInput').value = data.berat_pakan;
            document.getElementById('aktifInput').checked = (data.aktif == 1);
            document.getElementById('scheduleModal').classList.remove('hidden');
        }
        
        function closeModal() {
            document.getElementById('scheduleModal').classList.add('hidden');
        }

        // Form Submit (Save/Edit)
        document.getElementById('scheduleForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            // Checkbox fix: if unchecked, it doesn't send value. We force set it.
            formData.set('aktif', document.getElementById('aktifInput').checked ? '1' : '0');

            try {
                const res = await fetch('api/kelola_jadwal.php', { method: 'POST', body: formData });
                const result = await res.json();
                if(result.status === 'success') {
                    location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (err) {
                console.error(err);
                alert('Gagal menyimpan data.');
            }
        });

        // Toggle Status Logic
        async function toggleSchedule(id, checkbox) {
            const formData = new FormData();
            formData.append('action', 'toggle');
            formData.append('id', id);
            formData.append('aktif', checkbox.checked ? 1 : 0);

            try {
                const res = await fetch('api/kelola_jadwal.php', { method: 'POST', body: formData });
                const result = await res.json();
                if(result.status !== 'success') {
                    alert('Gagal update status: ' + result.message);
                    checkbox.checked = !checkbox.checked; // Revert
                } else {
                    location.reload(); // Refresh to update visuals perfectly
                }
            } catch(err) {
                checkbox.checked = !checkbox.checked;
                alert('Koneksi error');
            }
        }

        // Delete Logic
        async function deleteSchedule(id) {
            if(!confirm('Yakin ingin menghapus jadwal ini?')) return;

            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('id', id);

            try {
                const res = await fetch('api/kelola_jadwal.php', { method: 'POST', body: formData });
                const result = await res.json();
                if(result.status === 'success') {
                    location.reload();
                } else {
                    alert('Gagal menghapus: ' + result.message);
                }
            } catch(err) {
                alert('Koneksi error');
            }
        }
    </script>
</body>
</html>