<?php
session_start();
include '../koneksi.php';
header('Content-Type: application/json');

if (!isset($_SESSION['id_pembimbing'])) {
    echo json_encode(['success' => false, 'message' => 'Sesi habis']);
    exit;
}

try {
    $id_pembimbing = $_SESSION['id_pembimbing'];
    
    // Ambil id_tempat dan nama_tempat berdasarkan pembimbing yg login
    $stmt = $pdo->prepare("SELECT id_tempat, nama_tempat FROM tempat_pkl WHERE id_pembimbing = ? ORDER BY nama_tempat ASC");
    $stmt->execute([$id_pembimbing]);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $data]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>