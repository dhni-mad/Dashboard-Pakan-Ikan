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
<body class="bg-gray-100">

    <header class="bg-white shadow-md">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
            <div class="text-2xl font-bold text-gray-800">Monitoring Pakan Ikan</div>
            <div class="flex space-x-4">
                <a href="index.php" class="px-3 py-2 rounded text-gray-700 hover:bg-gray-200 font-semibold">Dashboard</a>
                <a href="analisis.php" class="px-3 py-2 rounded text-gray-700 hover:bg-gray-200 font-semibold">Analisis Data</a>
                <a href="laporan.php" class="px-3 py-2 rounded text-gray-700 hover:bg-gray-200 font-semibold">Laporan</a>
                <a href="jadwal.php" class="px-3 py-2 rounded text-gray-700 hover:bg-gray-200 font-semibold">Jadwal Pakan</a>
                <a href="pengaturan.php" class="px-3 py-2 rounded bg-blue-600 text-white font-semibold">Pengaturan</a>
            </div>
        </nav>
    </header>

    <main class="container mx-auto p-6">
        <div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow-md">
            <h1 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-4">Pengaturan Ambang Batas Sensor</h1>
            
            <form action="api/simpan_pengaturan.php" method="POST">
                
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        Batas Jarak Pakan Habis (cm)
                    </label>
                    <input type="number" name="batas_pakan" value="<?php echo $setting['batas_pakan_habis']; ?>" 
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <p class="text-sm text-gray-500 mt-1">Jika sensor ultrasonik membaca jarak lebih dari angka ini, status menjadi "Habis".</p>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        Batas Nilai Sensor Kekeruhan
                    </label>
                    <input type="number" name="batas_air" value="<?php echo $setting['batas_air_keruh']; ?>" 
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <p class="text-sm text-gray-500 mt-1">Jika nilai sensor LDR lebih tinggi dari angka ini, status menjadi "Keruh".</p>
                </div>

                <div class="flex items-center justify-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>