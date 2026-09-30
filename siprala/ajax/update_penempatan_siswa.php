<?php
// ajax/update_penempatan_siswa.php - Update pembimbing dan tempat PKL siswa
header('Content-Type: application/json');

include '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Metode request tidak diizinkan.']);
    exit;
}

$id_siswa      = intval($_POST['id_siswa'] ?? 0);
$id_pembimbing = intval($_POST['id_pembimbing'] ?? 0);
$id_tempat     = intval($_POST['id_tempat'] ?? 0);

if ($id_siswa <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'ID siswa tidak valid.']);
    exit;
}

// Pastikan siswa ada
$stmt_check = $koneksi->prepare("SELECT id_siswa FROM siswa WHERE id_siswa = ? LIMIT 1");
if (!$stmt_check) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyiapkan query: ' . $koneksi->error]);
    exit;
}
$stmt_check->bind_param("i", $id_siswa);
$stmt_check->execute();
$res_check = $stmt_check->get_result();
if ($res_check->num_rows === 0) {
    $stmt_check->close();
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'Data siswa tidak ditemukan.']);
    exit;
}
$stmt_check->close();

// Update kolom id_pembimbing dan id_tempat
$stmt = $koneksi->prepare("UPDATE siswa SET id_pembimbing = ?, id_tempat = ? WHERE id_siswa = ?");
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyiapkan query: ' . $koneksi->error]);
    exit;
}
$stmt->bind_param("iii", $id_pembimbing, $id_tempat, $id_siswa);

if ($stmt->execute()) {
    echo json_encode([
        'status'  => 'success',
        'message' => 'Penempatan PKL siswa berhasil diperbarui.'
    ]);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui data: ' . $stmt->error]);
}

$stmt->close();
$koneksi->close();
?>
