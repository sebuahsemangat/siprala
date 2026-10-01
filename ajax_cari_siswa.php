<?php
// ajax_cari_siswa.php - Endpoint pencarian nama siswa & verifikasi NIS untuk reset password
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/koneksi.php';

$action = $_GET['action'] ?? $_POST['action'] ?? 'search';

if ($action === 'verify') {
    // Verifikasi NIS milik siswa yang dipilih
    $id_siswa = intval($_POST['id_siswa'] ?? $_GET['id_siswa'] ?? 0);
    $nis = trim($_POST['nis'] ?? $_GET['nis'] ?? '');

    if ($id_siswa <= 0 || empty($nis)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'ID Siswa dan NIS wajib diisi.'
        ]);
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT id_siswa, nis, nama_siswa, kelas FROM siswa WHERE id_siswa = ? AND nis = ?");
        $stmt->execute([$id_siswa, $nis]);
        $siswa = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($siswa) {
            echo json_encode([
                'status' => 'success',
                'message' => 'NIS valid dan terverifikasi.',
                'data' => $siswa
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'NIS yang Anda masukkan tidak sesuai dengan siswa yang dipilih.'
            ]);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Terjadi kesalahan sistem saat memverifikasi NIS.'
        ]);
    }
    exit;
}

// Default action: pencarian nama siswa
// PENTING: Jangan tampilkan NIS di hasil pencarian agar tidak disalahgunakan orang lain
$keyword = trim($_GET['q'] ?? $_POST['q'] ?? '');

if (mb_strlen($keyword) < 2) {
    echo json_encode([
        'status' => 'success',
        'data' => []
    ]);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id_siswa, nama_siswa, kelas FROM siswa WHERE nama_siswa LIKE :q ORDER BY nama_siswa ASC LIMIT 10");
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
