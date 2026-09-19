<?php
// ajax/hapus_tempat_pkl.php - Menghapus data tempat PKL secara aman
header('Content-Type: application/json');

require_once __DIR__ . '/../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Metode request tidak diizinkan.']);
    exit;
}

$id_tempat = intval($_POST['id_tempat'] ?? 0);
if ($id_tempat <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'ID Tempat PKL tidak valid.']);
    exit;
}

// Mulai Transaksi
$koneksi->begin_transaction();

try {
    // 1. Kosongkan penempatan di tabel siswa
    $stmt1 = $koneksi->prepare("UPDATE siswa SET id_tempat = 0 WHERE id_tempat = ?");
    $stmt1->bind_param("i", $id_tempat);
    $stmt1->execute();
    $stmt1->close();

    // 2. Kosongkan relasi surat jika ada
    $stmt2 = $koneksi->prepare("UPDATE surat SET id_tempat_pkl = 0 WHERE id_tempat_pkl = ?");
    $stmt2->bind_param("i", $id_tempat);
    $stmt2->execute();
    $stmt2->close();

    // 3. Hapus absensi mingguan jika ada
    $stmt3 = $koneksi->prepare("DELETE FROM absensi_mingguan WHERE id_tempat = ?");
    $stmt3->bind_param("i", $id_tempat);
    $stmt3->execute();
    $stmt3->close();

    // 4. Hapus dari tabel tempat_pkl
    $stmt4 = $koneksi->prepare("DELETE FROM tempat_pkl WHERE id_tempat = ?");
    $stmt4->bind_param("i", $id_tempat);
    $stmt4->execute();
    $affected = $stmt4->affected_rows;
    $stmt4->close();

    if ($affected === 0) {
        throw new Exception("Data tempat PKL tidak ditemukan atau sudah dihapus.");
    }

    $koneksi->commit();
    echo json_encode(['status' => 'success', 'message' => 'Data tempat PKL berhasil dihapus.']);

} catch (Exception $e) {
    $koneksi->rollback();
    error_log("Gagal menghapus tempat PKL: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus data: ' . $e->getMessage()]);
}

$koneksi->close();
?>
