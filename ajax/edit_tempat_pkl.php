<?php
header('Content-Type: application/json');

// 1. Include koneksi database
include '../koneksi.php';

// 2. Cek apakah request adalah POST dan data yang dibutuhkan dikirim
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id_tempat']) || !isset($_POST['nama_tempat'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap.']);
    exit;
}

// 3. Ambil dan sanitasi data
$id_tempat = filter_var($_POST['id_tempat'], FILTER_SANITIZE_NUMBER_INT);
$nama_tempat = trim($_POST['nama_tempat']);
$alamat = isset($_POST['alamat']) ? trim($_POST['alamat']) : '';
$kota = isset($_POST['kota']) ? trim($_POST['kota']) : '';
$no_telepon = isset($_POST['no_telepon']) ? trim($_POST['no_telepon']) : '';
$catatan = isset($_POST['catatan']) ? trim($_POST['catatan']) : '';

if (!is_numeric($id_tempat) || empty($id_tempat)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'ID Tempat tidak valid.']);
    exit;
}

if ($nama_tempat === '') {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Nama Tempat PKL tidak boleh kosong.']);
    exit;
}

// 4. Update data ke database
try {
    $query = "UPDATE tempat_pkl SET nama_tempat = ?, alamat = ?, kota = ?, no_telepon = ?, catatan = ? WHERE id_tempat = ?";
    $stmt = $koneksi->prepare($query);
    
    if (!$stmt) {
        throw new Exception("Error preparing statement: " . $koneksi->error);
    }
    
    $stmt->bind_param("sssssi", $nama_tempat, $alamat, $kota, $no_telepon, $catatan, $id_tempat);
    
    if (!$stmt->execute()) {
        throw new Exception("Error executing query: " . $stmt->error);
    }
    
    $stmt->close();
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Data tempat PKL berhasil diperbarui.'
    ]);
} catch (Exception $e) {
    error_log("Edit tempat PKL failed: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Gagal memperbarui data: ' . $e->getMessage()
    ]);
}

$koneksi->close();
?>
