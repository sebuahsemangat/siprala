<?php
// get_tempat_pkl.php
include 'koneksi.php'; // Pastikan koneksi.php tersedia

header('Content-Type: application/json');

$query = "SELECT nama_tempat FROM tempat_pkl ORDER BY nama_tempat ASC";
$result = $koneksi->query($query);

$tempat_pkl = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $tempat_pkl[] = $row['nama_tempat'];
    }
}

echo json_encode($tempat_pkl);

$koneksi->close();
?>