<?php
// get_tempat_pkl.php
include 'koneksi.php'; // Pastikan koneksi.php tersedia

header('Content-Type: application/json');

$query = "SELECT id_tempat, nama_tempat, alamat, kota, no_telepon, catatan FROM tempat_pkl ORDER BY nama_tempat ASC";
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
            'catatan' => $row['catatan'] ?? ''
        ];
    }
}

echo json_encode($tempat_pkl);

$koneksi->close();
?>