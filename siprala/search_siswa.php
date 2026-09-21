<?php
// search_siswa.php
include 'koneksi.php';

header('Content-Type: application/json');

$query = $_GET['query'] ?? '';

if (empty($query) || strlen($query) < 2) {
    echo json_encode([]);
    $koneksi->close();
    exit;
}

// Gunakan LIKE untuk mencari di nama atau NIS
$search_param = "%" . $query . "%";
// Status yang harus dikecualikan
$status_pending = 'pending';
$status_diterima = 'diterima';

/*
 * PERBAIKAN: Gunakan LEFT JOIN untuk memeriksa status siswa di tabel siswa_surat.
 * Kita mencari siswa yang:
 * 1. Belum ada di tabel siswa_surat (ss.id_surat IS NULL)
 * ATAU
 * 2. Sudah ada di siswa_surat, tetapi status terakhirnya BUKAN 'pending' dan BUKAN 'diterima'.
 * Karena siswa bisa memiliki banyak riwayat, kita perlu GROUP BY dan mengambil status terakhir
 * atau cukup memastikan TIDAK ada status 'pending'/'diterima' yang aktif.
 *
 * Pendekatan yang lebih aman: Gunakan NOT EXISTS untuk mengecualikan siswa yang memiliki status 'pending' atau 'diterima'.
 */

$sql = "
    SELECT s.id_siswa, s.nis, s.nama_siswa, s.kelas, s.kontak_siswa 
    FROM siswa s
    WHERE 
        (s.nama_siswa LIKE ? OR s.nis LIKE ?)
        AND 
        NOT EXISTS (
            SELECT 1 
            FROM siswa_surat ss
            WHERE ss.id_siswa = s.id_siswa
            AND ss.status IN (?, ?)
        )
    LIMIT 10
";

$stmt = $koneksi->prepare($sql);

if (!$stmt) {
    // Handle error prepared statement
    http_response_code(500);
    echo json_encode(['error' => 'Gagal mempersiapkan query: ' . $koneksi->error]);
    $koneksi->close();
    exit;
}

$stmt->bind_param("ssss", $search_param, $search_param, $status_pending, $status_diterima);
$stmt->execute();
$result = $stmt->get_result();

$siswa_list = [];
while ($row = $result->fetch_assoc()) {
    $siswa_list[] = $row;
}

echo json_encode($siswa_list);

$stmt->close();
$koneksi->close();
?>