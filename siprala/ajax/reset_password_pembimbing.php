<?php
// ajax/reset_password_pembimbing.php - Reset password pembimbing ke default "pklifsu"
header('Content-Type: application/json');

include '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Metode request tidak diizinkan.']);
    exit;
}

$id_pembimbing = intval($_POST['id_pembimbing'] ?? 0);

if ($id_pembimbing <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'ID pembimbing tidak valid.']);
    exit;
}

// Pastikan pembimbing ada
$stmt_check = $koneksi->prepare("SELECT id_pembimbing FROM pembimbing WHERE id_pembimbing = ? LIMIT 1");
if (!$stmt_check) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyiapkan query: ' . $koneksi->error]);
    exit;
}
$stmt_check->bind_param("i", $id_pembimbing);
$stmt_check->execute();
if ($stmt_check->get_result()->num_rows === 0) {
    $stmt_check->close();
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'Data pembimbing tidak ditemukan.']);
    exit;
}
$stmt_check->close();

// Hash password default
$password_default  = 'pklifsu';
$password_hashed   = password_hash($password_default, PASSWORD_DEFAULT);

$stmt = $koneksi->prepare("UPDATE pembimbing SET password = ?, password_status = '0' WHERE id_pembimbing = ?");
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyiapkan query: ' . $koneksi->error]);
    exit;
}
$stmt->bind_param("si", $password_hashed, $id_pembimbing);

if ($stmt->execute()) {
    echo json_encode([
        'status'  => 'success',
        'message' => 'Password berhasil direset ke default (pklifsu).'
    ]);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Gagal mereset password: ' . $stmt->error]);
}

$stmt->close();
$koneksi->close();
?>
