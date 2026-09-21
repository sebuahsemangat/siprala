<?php
session_start();
require_once '../koneksi.php';
header('Content-Type: application/json; charset=utf-8');

// Cek autentikasi admin
if (!isset($_SESSION['admin_id']) && !isset($_SESSION['logged_in_admin'])) {
    echo json_encode(['success' => false, 'message' => 'Akses ditolak. Silakan login terlebih dahulu.']);
    exit();
}

try {
    $stmt = $pdo->query("
        SELECT 
            s.id_siswa,
            s.nis,
            s.nama_siswa,
            s.kelas,
            n.nilai_pembimbing_sekolah,
            n.nilai_pembimbing_dudi,
            n.nilai_sidang,
            n.nilai_akhir
        FROM 
            siswa s
        LEFT JOIN
            nilai_pkl n ON s.id_siswa = n.id_siswa
        ORDER BY 
            s.kelas ASC, s.nama_siswa ASC
    ");
    
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'data' => $data]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Gagal mengambil data nilai: ' . $e->getMessage()]);
}
?>
