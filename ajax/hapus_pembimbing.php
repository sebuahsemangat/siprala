<?php
// ajax/hapus_pembimbing.php - Menghapus data pembimbing secara aman
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
    echo json_encode(['status' => 'error', 'message' => 'ID Pembimbing tidak valid.']);
    exit;
}

// Mulai Transaksi
$koneksi->begin_transaction();

try {
    // 1. Kosongkan penugasan di tabel tempat_pkl
    $stmt1 = $koneksi->prepare("UPDATE tempat_pkl SET id_pembimbing = 0 WHERE id_pembimbing = ?");
    $stmt1->bind_param("i", $id_pembimbing);
    $stmt1->execute();
    $stmt1->close();

    // 2. Kosongkan penugasan di tabel siswa
    $stmt2 = $koneksi->prepare("UPDATE siswa SET id_pembimbing = 0 WHERE id_pembimbing = ?");
    $stmt2->bind_param("i", $id_pembimbing);
    $stmt2->execute();
    $stmt2->close();

    // 3. Hapus dari tabel pembimbing
    $stmt3 = $koneksi->prepare("DELETE FROM pembimbing WHERE id_pembimbing = ?");
    $stmt3->bind_param("i", $id_pembimbing);
    $stmt3->execute();
    $affected = $stmt3->affected_rows;
    $stmt3->close();

    if ($affected === 0) {
        throw new Exception("Data pembimbing tidak ditemukan atau sudah dihapus.");
    }

    $koneksi->commit();
    echo json_encode(['status' => 'success', 'message' => 'Data pembimbing berhasil dihapus.']);

} catch (Exception $e) {
    $koneksi->rollback();
    error_log("Gagal menghapus pembimbing: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus data: ' . $e->getMessage()]);
}

$koneksi->close();
?>
