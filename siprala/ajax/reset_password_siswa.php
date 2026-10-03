<?php
// ajax/reset_password_siswa.php - Reset password siswa ke default (NIS)
header('Content-Type: application/json');

include '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Metode request tidak diizinkan.']);
    exit;
}

$id_siswa = intval($_POST['id_siswa'] ?? 0);

if ($id_siswa <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'ID siswa tidak valid.']);
    exit;
}

// Pastikan siswa ada dan ambil NIS serta nama siswa
$stmt_check = $koneksi->prepare("SELECT id_siswa, nis, nama_siswa FROM siswa WHERE id_siswa = ? LIMIT 1");
if (!$stmt_check) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyiapkan query: ' . $koneksi->error]);
    exit;
}
$stmt_check->bind_param("i", $id_siswa);
$stmt_check->execute();
$res = $stmt_check->get_result();

if ($res->num_rows === 0) {
    $stmt_check->close();
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'Data siswa tidak ditemukan.']);
    exit;
}

$siswa = $res->fetch_assoc();
$stmt_check->close();

$nis = trim($siswa['nis'] ?? '');
$nama = $siswa['nama_siswa'] ?? '';

if (empty($nis)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'NIS siswa tidak valid atau kosong.']);
    exit;
}

// Hash password default berupa NIS siswa
$password_hashed = password_hash($nis, PASSWORD_DEFAULT);

$stmt = $koneksi->prepare("UPDATE siswa SET password = ?, password_status = '0' WHERE id_siswa = ?");
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyiapkan query: ' . $koneksi->error]);
    exit;
}
$stmt->bind_param("si", $password_hashed, $id_siswa);

if ($stmt->execute()) {
    echo json_encode([
        'status'  => 'success',
        'message' => 'Password berhasil direset ke default (NIS: ' . $nis . ').',
        'nis'     => $nis,
        'nama'    => $nama
    ]);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Gagal mereset password: ' . $stmt->error]);
}

$stmt->close();
$koneksi->close();
?>
