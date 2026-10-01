<?php
// ajax_cari_siswa.php - Endpoint untuk pencarian nama siswa saat request reset password
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/koneksi.php';

$keyword = trim($_GET['q'] ?? $_POST['q'] ?? '');

if (mb_strlen($keyword) < 2) {
    echo json_encode([
        'status' => 'success',
        'data' => []
    ]);
    exit;
}

try {
    // Cari siswa berdasarkan nama
    $stmt = $pdo->prepare("SELECT nis, nama_siswa, kelas FROM siswa WHERE nama_siswa LIKE :q ORDER BY nama_siswa ASC LIMIT 10");
    $stmt->execute(['q' => '%' . $keyword . '%']);
    $siswa = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'success',
        'data' => $siswa
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Terjadi kesalahan sistem saat mengambil data siswa.'
    ]);
}
