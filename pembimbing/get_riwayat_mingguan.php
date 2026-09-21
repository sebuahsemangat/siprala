<?php
session_start();
include '../koneksi.php';
header('Content-Type: application/json');

// Cek Sesi
if (!isset($_SESSION['id_pembimbing'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized', 'data' => []]);
    exit;
}

try {
    $id_pembimbing = $_SESSION['id_pembimbing'];

    // Query: Gabungkan tabel absensi_mingguan dengan tempat_pkl
    // Agar kita bisa menampilkan Nama Tempat, bukan ID-nya
    $sql = "SELECT 
                am.id_absensi_mingguan,
                am.minggu_ke,
                am.tanggal_monitoring,
                am.platform,
                am.platform_lainnya,
                am.catatan,
                am.foto_bukti,
                tp.nama_tempat
            FROM 
                absensi_mingguan am
            JOIN 
                tempat_pkl tp ON am.id_tempat = tp.id_tempat
            WHERE 
                am.id_pembimbing = ?
            ORDER BY 
                am.tanggal_monitoring DESC, am.id_absensi_mingguan DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_pembimbing]);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format Tanggal agar lebih enak dibaca (opsional, bisa juga di JS)
    foreach ($data as &$row) {
        $row['tanggal_display'] = date('d-m-Y', strtotime($row['tanggal_monitoring']));
    }

    echo json_encode(['success' => true, 'data' => $data]);

} catch (PDOException $e) {
    error_log("Error Get Riwayat: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database Error', 'data' => []]);
}
?>