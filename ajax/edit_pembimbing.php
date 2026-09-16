<?php
// ajax/edit_pembimbing.php - Memperbarui data pembimbing
header('Content-Type: application/json');

include '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Metode request tidak diizinkan.']);
    exit;
}

$id_pembimbing = intval($_POST['id_pembimbing'] ?? 0);
$nama_pembimbing = trim($_POST['nama_pembimbing'] ?? '');
$username = trim($_POST['username'] ?? '');
$kontak = trim($_POST['kontak_pembimbing'] ?? '');
$password_baru = trim($_POST['password'] ?? '');

if ($id_pembimbing <= 0 || empty($nama_pembimbing) || empty($username)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'ID, Nama Pembimbing, dan Username wajib diisi.']);
    exit;
}

// 1. Normalisasi nomor handphone (diawali 08)
$clean_kontak = preg_replace('/[^0-9]/', '', $kontak);
if (!empty($clean_kontak)) {
    if (strpos($clean_kontak, '62') === 0) {
        $clean_kontak = '0' . substr($clean_kontak, 2);
    } elseif (strpos($clean_kontak, '8') === 0) {
        $clean_kontak = '0' . $clean_kontak;
    }
}

// 2. Cek keunikan username (tidak boleh bentrok dengan ID lain)
$stmt_check = $koneksi->prepare("SELECT id_pembimbing FROM pembimbing WHERE username = ? AND id_pembimbing != ? LIMIT 1");
$stmt_check->bind_param("si", $username, $id_pembimbing);
$stmt_check->execute();
if ($stmt_check->get_result()->num_rows > 0) {
    $stmt_check->close();
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => "Username '$username' sudah digunakan oleh pembimbing lain."]);
    exit;
}
$stmt_check->close();

// 3. Update query (apakah ganti password atau tidak)
if (!empty($password_baru)) {
    $password_hashed = password_hash($password_baru, PASSWORD_DEFAULT);
    $stmt = $koneksi->prepare("
        UPDATE pembimbing 
        SET nama_pembimbing = ?, username = ?, kontak_pembimbing = ?, password = ?
        WHERE id_pembimbing = ?
    ");
    $stmt->bind_param("ssssi", $nama_pembimbing, $username, $clean_kontak, $password_hashed, $id_pembimbing);
} else {
    $stmt = $koneksi->prepare("
        UPDATE pembimbing 
        SET nama_pembimbing = ?, username = ?, kontak_pembimbing = ?
        WHERE id_pembimbing = ?
    ");
    $stmt->bind_param("sssi", $nama_pembimbing, $username, $clean_kontak, $id_pembimbing);
}

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyiapkan query: ' . $koneksi->error]);
    exit;
}

if ($stmt->execute()) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Data pembimbing berhasil diperbarui.'
    ]);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui data: ' . $stmt->error]);
}

$stmt->close();
$koneksi->close();
?>
