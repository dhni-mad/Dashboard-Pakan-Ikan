<?php
require 'config/db.php';

$stmt = $db->prepare("SELECT * FROM status_sistem WHERE id = 1");
$stmt->execute();
$status = $stmt->fetch(PDO::FETCH_ASSOC);

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
<html lang="id" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pakan Ikan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    </head>
<body class="bg-gray-100">

    <header class="bg-white shadow-md">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
            <div class="text-2xl font-bold text-gray-800">
                Monitoring Pakan Ikan
            </div>
            <div class="flex space-x-4">
                <a href="index.php" class="px-3 py-2 rounded bg-blue-600 text-white font-semibold">Dashboard</a>
                <a href="analisis.php" class="px-3 py-2 rounded text-gray-700 hover:bg-gray-200 font-semibold">Analisis Data</a>
                <a href="laporan.php" class="px-3 py-2 rounded text-gray-700 hover:bg-gray-200 font-semibold">Laporan</a>
                <a href="pengaturan.php" class="px-3 py-2 rounded text-gray-700 hover:bg-gray-200 font-semibold">Pengaturan</a>
            </div>
        </nav>
    </header>

    <main class="container mx-auto p-6">

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-lg font-semibold text-gray-600 mb-3">Status Pakan</h2>
                <div class="text-4xl font-bold <?php echo $jarak_color_class; ?>">
                    <?php echo htmlspecialchars($jarak_status_text); ?>
                </div>
                <div class="text-sm text-gray-500 mt-2">
                    (Jarak: <?php echo $status['jarak_cm']; ?> cm)
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-lg font-semibold text-gray-600 mb-3">Status Air</h2>
                <div class="text-4xl font-bold <?php echo $water_color_class; ?>">
                    <?php echo htmlspecialchars($status['water_status']); ?>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-lg font-semibold text-gray-600 mb-3">Kontrol Manual</h2>
                <button id="manualFeedButton" class="w-full bg-green-500 text-white font-bold py-3 px-4 rounded-lg hover:bg-green-600 transition duration-300">
                    Beri Pakan Sekarang
                </button>
                <div id="feedStatus" class="text-sm text-gray-500 mt-2 text-center"></div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-lg font-semibold text-gray-600 mb-3">Informasi</h2>
                <div class="text-gray-700">
                    Update Terakhir:
                </div>
                <div class="text-lg font-semibold text-gray-900">
                    <?php echo date('d M Y, H:i:s', strtotime($status['last_update'])); ?>
                </div>
                <button onclick="location.reload()" class="w-full mt-3 bg-gray-200 text-gray-700 font-semibold py-2 px-4 rounded-lg hover:bg-gray-300 transition duration-300">
                    Refresh
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold text-gray-700 mb-4">Aktivitas Pakan Terbaru</h3>
                <ul class="divide-y divide-gray-200">
                    <?php if (empty($log_pakan)): ?>
                        <li class="py-3 text-gray-500">Belum ada data pemberian pakan.</li>
                    <?php else: ?>
                        <?php foreach ($log_pakan as $log): ?>
                            <li class="py-3 flex justify-between items-center">
                                <span class="text-gray-700"><?php echo date('d M Y, H:i', strtotime($log['waktu'])); ?></span>
                                <span class="font-semibold text-blue-600"><?php echo htmlspecialchars($log['berat_pakan']); ?> gram</span>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold text-gray-700 mb-4">Peringatan Kekeruhan Air</h3>
                <ul class="divide-y divide-gray-200">
                    <?php if (empty($log_air_keruh)): ?>
                        <li class="py-3 text-gray-500">Kualitas air terpantau jernih.</li>
                    <?php else: ?>
                        <?php foreach ($log_air_keruh as $log): ?>
                            <li class="py-3 flex justify-between items-center">
                                <span class="text-gray-700"><?php echo date('d M Y, H:i', strtotime($log['waktu'])); ?></span>
                                <span class="font-semibold text-yellow-700">Air Keruh (<?php echo htmlspecialchars($log['nilai_kekeruhan']); ?>)</span>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
            
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('manualFeedButton').addEventListener('click', triggerManualFeed);
        });

       
        async function triggerManualFeed() {
            const button = document.getElementById('manualFeedButton');
            const statusDiv = document.getElementById('feedStatus');
            button.disabled = true;
            button.textContent = 'Mengirim...';
            statusDiv.textContent = '';
            try {
                const response = await fetch('api/trigger_feed.php', { method: 'POST' });
                const result = await response.json();
                if (result.status === 'success') {
                    statusDiv.textContent = 'Permintaan terkirim!';
                    button.classList.replace('bg-green-500', 'bg-gray-400');
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                statusDiv.textContent = 'Gagal mengirim.';
                button.disabled = false;
            }
            setTimeout(() => {
                button.disabled = false;
                button.textContent = 'Beri Pakan Sekarang';
                button.classList.replace('bg-gray-400', 'bg-green-500');
                statusDiv.textContent = '';
            }, 5000);
        }
    </script>
</body>
</html>