<?php
// ajax/edit_siswa.php - Memperbarui data siswa
header('Content-Type: application/json');

include '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Metode request tidak diizinkan.']);
    exit;
}

$id_siswa = intval($_POST['id_siswa'] ?? 0);
$nis = trim($_POST['nis'] ?? '');
$nama_siswa = trim($_POST['nama_siswa'] ?? '');
$kelas = trim($_POST['kelas'] ?? '');
$kontak = trim($_POST['kontak_siswa'] ?? '');
$id_pembimbing = intval($_POST['id_pembimbing'] ?? 0);
$password_baru = trim($_POST['password'] ?? '');

if ($id_siswa <= 0 || empty($nis) || empty($nama_siswa) || empty($kelas)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'ID, NIS, Nama Siswa, dan Kelas wajib diisi.']);
    exit;
}

// 1. Normalisasi nomor kontak (diawali 08 jika ada)
$clean_kontak = preg_replace('/[^0-9]/', '', $kontak);
if (!empty($clean_kontak)) {
    if (strpos($clean_kontak, '62') === 0) {
        $clean_kontak = '0' . substr($clean_kontak, 2);
    } elseif (strpos($clean_kontak, '8') === 0) {
        $clean_kontak = '0' . $clean_kontak;
    }
}

// 2. Cek keunikan NIS (tidak boleh sama dengan siswa lain)
$stmt_check = $koneksi->prepare("SELECT id_siswa FROM siswa WHERE nis = ? AND id_siswa != ? LIMIT 1");
if (!$stmt_check) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyiapkan query: ' . $koneksi->error]);
    exit;
}
$stmt_check->bind_param("si", $nis, $id_siswa);
$stmt_check->execute();
$res_check = $stmt_check->get_result();

if ($res_check->num_rows > 0) {
    $stmt_check->close();
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => "NIS '$nis' sudah digunakan oleh siswa lain."]);
    exit;
}
$stmt_check->close();

// 3. Update query (apakah ganti password atau tidak)
if (!empty($password_baru)) {
    $password_hashed = password_hash($password_baru, PASSWORD_DEFAULT);
    $stmt = $koneksi->prepare("
        UPDATE siswa 
        SET nis = ?, nama_siswa = ?, kelas = ?, kontak_siswa = ?, id_pembimbing = ?, password = ?
        WHERE id_siswa = ?
    ");
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyiapkan query: ' . $koneksi->error]);
        exit;
    }
    $stmt->bind_param("ssssisi", $nis, $nama_siswa, $kelas, $clean_kontak, $id_pembimbing, $password_hashed, $id_siswa);
} else {
    $stmt = $koneksi->prepare("
        UPDATE siswa 
        SET nis = ?, nama_siswa = ?, kelas = ?, kontak_siswa = ?, id_pembimbing = ?
        WHERE id_siswa = ?
    ");
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyiapkan query: ' . $koneksi->error]);
        exit;
    }
    $stmt->bind_param("ssssii", $nis, $nama_siswa, $kelas, $clean_kontak, $id_pembimbing, $id_siswa);
}

if ($stmt->execute()) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Data siswa berhasil diperbarui.'
    ]);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui data siswa: ' . $stmt->error]);
}

$stmt->close();
$koneksi->close();
?>
