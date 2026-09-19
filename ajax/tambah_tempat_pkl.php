<?php
header('Content-Type: application/json');

// 1. Include koneksi database
require_once __DIR__ . '/../koneksi.php';

// 2. Cek apakah request adalah POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Metode request tidak diizinkan.']);
    exit;
}

// 3. Ambil dan sanitasi data
$nama_tempat = isset($_POST['nama_tempat']) ? trim($_POST['nama_tempat']) : '';
$alamat = isset($_POST['alamat']) ? trim($_POST['alamat']) : '';
$kota = isset($_POST['kota']) ? trim($_POST['kota']) : '';
$no_telepon = isset($_POST['no_telepon']) ? trim($_POST['no_telepon']) : '';
$catatan = isset($_POST['catatan']) ? trim($_POST['catatan']) : '';
$kapasitas = isset($_POST['kapasitas']) ? max(0, (int)$_POST['kapasitas']) : 0;
$id_pembimbing = isset($_POST['id_pembimbing']) ? (int)$_POST['id_pembimbing'] : 0;

if ($nama_tempat === '') {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Nama Tempat PKL wajib diisi.']);
    exit;
}

// 4. Simpan ke database
try {
    $query = "INSERT INTO tempat_pkl (nama_tempat, alamat, kota, no_telepon, catatan, kapasitas, id_pembimbing) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $koneksi->prepare($query);

    if (!$stmt) {
        throw new Exception("Error preparing statement: " . $koneksi->error);
    }

    $stmt->bind_param("sssssii", $nama_tempat, $alamat, $kota, $no_telepon, $catatan, $kapasitas, $id_pembimbing);

    if (!$stmt->execute()) {
        throw new Exception("Error executing query: " . $stmt->error);
    }

    $new_id = $koneksi->insert_id;
    $stmt->close();

    echo json_encode([
        'status' => 'success',
        'message' => 'Tempat PKL berhasil ditambahkan.',
        'id_tempat' => $new_id
    ]);
} catch (Exception $e) {
    error_log("Tambah tempat PKL failed: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Gagal menambahkan tempat PKL: ' . $e->getMessage()
    ]);
}

$koneksi->close();
?>
