<?php
header('Content-Type: application/json');
require '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

$action = $_POST['action'] ?? 'save';

try {
    switch ($action) {
        case 'save':
            $id = $_POST['id'] ?? null;
            $jam = $_POST['jam'];
            $berat_pakan = (float)$_POST['berat_pakan'];
            $aktif = (int)$_POST['aktif'];

            if ($id) {
                // Update
                $stmt = $db->prepare("UPDATE jadwal_pakan SET jam = ?, berat_pakan = ?, aktif = ? WHERE id = ?");
                $stmt->execute([$jam, $berat_pakan, $aktif, $id]);
                $message = 'Jadwal berhasil diupdate!';
            } else {
                // Insert
                $stmt = $db->prepare("INSERT INTO jadwal_pakan (jam, berat_pakan, aktif) VALUES (?, ?, ?)");
                $stmt->execute([$jam, $berat_pakan, $aktif]);
                $message = 'Jadwal berhasil ditambahkan!';
            }

            echo json_encode(['status' => 'success', 'message' => $message]);
            break;

        case 'toggle':
            $id = (int)$_POST['id'];
            $aktif = (int)$_POST['aktif'];
            
            $stmt = $db->prepare("UPDATE jadwal_pakan SET aktif = ? WHERE id = ?");
            $stmt->execute([$aktif, $id]);
            
            echo json_encode(['status' => 'success', 'message' => 'Status jadwal berhasil diubah!']);
            break;

        case 'delete':
            $id = (int)$_POST['id'];
            
            $stmt = $db->prepare("DELETE FROM jadwal_pakan WHERE id = ?");
            $stmt->execute([$id]);
            
            echo json_encode(['status' => 'success', 'message' => 'Jadwal berhasil dihapus!']);
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    }

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>