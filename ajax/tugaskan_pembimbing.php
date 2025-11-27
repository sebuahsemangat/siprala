<?php
header('Content-Type: application/json');

// 1. Include koneksi database
include '../koneksi.php'; // Sesuaikan path jika berbeda dari root

// 2. Cek apakah request adalah POST dan data yang dibutuhkan dikirim
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id_tempat']) || !isset($_POST['id_pembimbing'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid request data.']);
    exit;
}

// 3. Ambil dan sanitasi data
$id_tempat = filter_var($_POST['id_tempat'], FILTER_SANITIZE_NUMBER_INT);
$id_pembimbing = filter_var($_POST['id_pembimbing'], FILTER_SANITIZE_NUMBER_INT);

if (!is_numeric($id_tempat) || !is_numeric($id_pembimbing)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid ID format.']);
    exit;
}

// =========================================================
// 4. Proses Update Transaksional (Best Practice)
// =========================================================

$koneksi->begin_transaction();

try {
    // A. Update id_pembimbing di tabel tempat_pkl
    $query_update_tempat = "UPDATE tempat_pkl SET id_pembimbing = ? WHERE id_tempat = ?";
    $stmt_update_tempat = $koneksi->prepare($query_update_tempat);
    
    if (!$stmt_update_tempat) {
        throw new Exception("Error preparing statement (tempat_pkl): " . $koneksi->error);
    }
    
    $stmt_update_tempat->bind_param("ii", $id_pembimbing, $id_tempat);
    if (!$stmt_update_tempat->execute()) {
        throw new Exception("Error updating tempat_pkl: " . $stmt_update_tempat->error);
    }
    $stmt_update_tempat->close();


    // B. Update id_pembimbing di tabel siswa
    // HANYA siswa yang id_tempat-nya sesuai DENGAN tempat PKL ini.
    $query_update_siswa = "UPDATE siswa SET id_pembimbing = ? WHERE id_tempat = ?";
    $stmt_update_siswa = $koneksi->prepare($query_update_siswa);
    
    if (!$stmt_update_siswa) {
        throw new Exception("Error preparing statement (siswa): " . $koneksi->error);
    }
    
    $stmt_update_siswa->bind_param("ii", $id_pembimbing, $id_tempat);
    if (!$stmt_update_siswa->execute()) {
        throw new Exception("Error updating siswa: " . $stmt_update_siswa->error);
    }
    $rows_updated = $stmt_update_siswa->affected_rows;
    $stmt_update_siswa->close();
    
    // Commit transaksi jika semua berhasil
    $koneksi->commit();

    // Berikan respons sukses
    $message = "Pembimbing berhasil ditugaskan. Sebanyak $rows_updated siswa berhasil diperbarui pembimbingnya.";
    echo json_encode(['status' => 'success', 'message' => $message]);

} catch (Exception $e) {
    // Rollback transaksi jika terjadi kesalahan
    $koneksi->rollback();
    error_log("Transaction failed: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data: ' . $e->getMessage()]);
}

$koneksi->close();
?>