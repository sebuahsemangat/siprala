<?php
session_start();
include '../koneksi.php';
header('Content-Type: application/json');

// Validasi login dan role pembimbing
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_SESSION['id_pembimbing'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Akses ditolak. Silakan login kembali.'
    ]);
    exit();
}

$id_pembimbing = $_SESSION['id_pembimbing'];

try {
    // Query untuk mengambil daftar siswa yang dibimbing
    $stmt = $pdo->prepare("
        SELECT 
            id_siswa,
            nis,
            nama_siswa,
            kelas
        FROM 
            siswa
        WHERE 
            id_pembimbing = ?
        ORDER BY 
            nama_siswa ASC
    ");
    
    $stmt->execute([$id_pembimbing]);
    $siswa = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $siswa,
        'total' => count($siswa)
    ]);
    
} catch (PDOException $e) {
    error_log("Error di get_siswa_bimbingan.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Gagal mengambil data siswa dari database.'
    ]);
}
?>