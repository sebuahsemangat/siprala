<?php
// get_tempat_pkl.php
include 'koneksi.php'; // Pastikan koneksi.php tersedia

header('Content-Type: application/json');

$query = "
    SELECT 
        tp.id_tempat, 
        tp.nama_tempat, 
        tp.alamat, 
        tp.kota, 
        tp.no_telepon, 
        tp.catatan, 
        tp.kapasitas,
        COUNT(s.id_siswa) AS jumlah_siswa
    FROM tempat_pkl tp
    LEFT JOIN siswa s ON tp.id_tempat = s.id_tempat
    GROUP BY tp.id_tempat, tp.nama_tempat, tp.alamat, tp.kota, tp.no_telepon, tp.catatan, tp.kapasitas
    ORDER BY tp.nama_tempat ASC
";
$result = $koneksi->query($query);

$tempat_pkl = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $tempat_pkl[] = [
            'id_tempat' => $row['id_tempat'],
            'nama_tempat' => $row['nama_tempat'],
            'alamat' => $row['alamat'] ?? '',
            'kota' => $row['kota'] ?? '',
            'no_telepon' => $row['no_telepon'] ?? '',
            'catatan' => $row['catatan'] ?? '',
            'kapasitas' => (int)($row['kapasitas'] ?? 0),
            'jumlah_siswa' => (int)($row['jumlah_siswa'] ?? 0)
        ];
    }
}

echo json_encode($tempat_pkl);

$koneksi->close();
?>