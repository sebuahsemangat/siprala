<?php
header('Content-Type: application/json');

// 1. Include koneksi database
include '../koneksi.php'; // Sesuaikan path jika berbeda

// 2. Cek apakah request adalah POST dan id_surat dikirim
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id_surat'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method or missing ID.']);
    exit;
}

// 3. Ambil dan sanitasi ID
$id_surat = filter_var($_POST['id_surat'], FILTER_SANITIZE_NUMBER_INT);

if (!$id_surat) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid surat ID.']);
    exit;
}

// =========================================================
// 4. Proses Penghapusan Transaksional (Best Practice)
// =========================================================

// Mulai Transaksi
$koneksi->begin_transaction();
$success = true;

try {
    // A. Hapus data terkait di tabel siswa_surat (Cascade Delete)
    $stmt_siswa_surat = $koneksi->prepare("DELETE FROM siswa_surat WHERE id_surat = ?");
    if (!$stmt_siswa_surat) {
        throw new Exception("Prepared statement failed (siswa_surat): " . $koneksi->error);
    }
    $stmt_siswa_surat->bind_param("i", $id_surat);
    if (!$stmt_siswa_surat->execute()) {
        throw new Exception("Execute failed (siswa_surat): " . $stmt_siswa_surat->error);
    }
    $stmt_siswa_surat->close();


    // B. Hapus data utama di tabel surat
    $stmt_surat = $koneksi->prepare("DELETE FROM surat WHERE id_surat = ?");
    if (!$stmt_surat) {
        throw new Exception("Prepared statement failed (surat): " . $koneksi->error);
    }
    $stmt_surat->bind_param("i", $id_surat);
    if (!$stmt_surat->execute()) {
        throw new Exception("Execute failed (surat): " . $stmt_surat->error);
    }
    $stmt_surat->close();

    // Commit transaksi jika semua berhasil
    $koneksi->commit();
    echo json_encode(['status' => 'success', 'message' => 'Surat dan data siswa terkait berhasil dihapus.']);

} catch (Exception $e) {
    // Rollback transaksi jika terjadi kesalahan
    $koneksi->rollback();
    error_log("Transaction failed: " . $e->getMessage()); // Log error ke server
    echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus data. ' . $e->getMessage()]);
}

$koneksi->close();
?>