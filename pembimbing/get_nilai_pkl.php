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
    // Query untuk mengambil daftar siswa yang dibimbing beserta nilainya
    $stmt = $pdo->prepare("
        SELECT 
            s.id_siswa,
            s.nis,
            s.nama_siswa,
            s.kelas,
            n.nilai_pembimbing_sekolah,
            n.nilai_pembimbing_dudi
        FROM 
            siswa s
        LEFT JOIN
            nilai_pkl n ON s.id_siswa = n.id_siswa
        WHERE 
            s.id_pembimbing = ?
        ORDER BY 
            s.kelas ASC, s.nama_siswa ASC
    ");
    
    $stmt->execute([$id_pembimbing]);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $data
    ]);
    
} catch (PDOException $e) {
    error_log("Error di get_nilai_pkl.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Gagal mengambil data dari database.'
    ]);
}
?>
