<?php
header('Content-Type: application/json');

// 1. Include koneksi database
include '../koneksi.php'; // Sesuaikan path jika berbeda dari root

// 2. Cek apakah request adalah POST dan id_surat dikirim
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id_surat']) || !isset($_POST['id_tempat']) || !isset($_POST['siswa'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid request data.']);
    exit;
}

// 3. Ambil dan sanitasi data
$id_surat = filter_var($_POST['id_surat'], FILTER_SANITIZE_NUMBER_INT);
$id_tempat = filter_var($_POST['id_tempat'], FILTER_SANITIZE_NUMBER_INT);
$siswa_data = $_POST['siswa']; // Array data siswa
$status_balasan_surat = 'Sudah Dibalas';

if (!$id_surat || !$id_tempat) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid Surat or Tempat ID.']);
    exit;
}

// =========================================================
// 4. Proses Update Transaksional
// =========================================================

$koneksi->begin_transaction();

try {
    // --- Persiapkan Statements ---

    // A. Update status dan catatan di tabel siswa_surat
    $query_update_ss = "UPDATE siswa_surat SET status = ?, catatan = ? WHERE id_surat = ? AND id_siswa = ?";
    $stmt_update_ss = $koneksi->prepare($query_update_ss);
    
    // B. Update id_tempat di tabel siswa (untuk status 'diterima')
    $query_update_siswa = "UPDATE siswa SET id_tempat = ? WHERE id_siswa = ?";
    $stmt_update_siswa = $koneksi->prepare($query_update_siswa);
    
    // C. Reset id_tempat di tabel siswa (untuk status 'ditolak')
    $query_reset_siswa = "UPDATE siswa SET id_tempat = 0 WHERE id_siswa = ?";
    $stmt_reset_siswa = $koneksi->prepare($query_reset_siswa);
    
    // D. Update status balasan di tabel surat
    $query_update_surat = "UPDATE surat SET status_balasan = ? WHERE id_surat = ?";
    $stmt_update_surat = $koneksi->prepare($query_update_surat);
    
    // Periksa keberhasilan persiapan statement
    if (!$stmt_update_ss || !$stmt_update_siswa || !$stmt_reset_siswa || !$stmt_update_surat) {
        throw new Exception("Error preparing statement: " . $koneksi->error);
    }
    
    // --- Eksekusi Update Siswa per Siswa ---
    foreach ($siswa_data as $id_siswa => $data) {
        $id_siswa_int = filter_var($id_siswa, FILTER_SANITIZE_NUMBER_INT);
        $status = $data['status'];
        $catatan = isset($data['catatan']) ? trim($data['catatan']) : '';
        
        // 1. Update siswa_surat
        $stmt_update_ss->bind_param("ssii", $status, $catatan, $id_surat, $id_siswa_int);
        if (!$stmt_update_ss->execute()) {
            throw new Exception("Error updating siswa_surat for ID $id_siswa_int: " . $stmt_update_ss->error);
        }

        // 2. Update/Reset id_tempat di tabel siswa
        if ($status == 'diterima') {
            $stmt_update_siswa->bind_param("ii", $id_tempat, $id_siswa_int);
            if (!$stmt_update_siswa->execute()) {
                throw new Exception("Error updating siswa id_tempat for ID $id_siswa_int: " . $stmt_update_siswa->error);
            }
        } elseif ($status == 'ditolak') {
            $stmt_reset_siswa->bind_param("i", $id_siswa_int);
            if (!$stmt_reset_siswa->execute()) {
                throw new Exception("Error resetting siswa id_tempat for ID $id_siswa_int: " . $stmt_reset_siswa->error);
            }
        }
    }
    
    // --- Eksekusi Update Surat ---
    $stmt_update_surat->bind_param("si", $status_balasan_surat, $id_surat);
    if (!$stmt_update_surat->execute()) {
        throw new Exception("Error updating surat status: " . $stmt_update_surat->error);
    }
    
    // Tutup statements
    $stmt_update_ss->close();
    $stmt_update_siswa->close();
    $stmt_reset_siswa->close();
    $stmt_update_surat->close();
    
    // Commit transaksi jika semua berhasil
    $koneksi->commit();
    echo json_encode(['status' => 'success', 'message' => 'Balasan surat berhasil diproses!']);

} catch (Exception $e) {
    // Rollback transaksi jika terjadi kesalahan
    $koneksi->rollback();
    error_log("Transaction failed: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data: ' . $e->getMessage()]);
}

$koneksi->close();
?>