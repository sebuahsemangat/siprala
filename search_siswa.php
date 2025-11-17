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
$stmt = $koneksi->prepare("SELECT id_siswa, nis, nama_siswa, kelas, kontak_siswa FROM siswa WHERE nama_siswa LIKE ? OR nis LIKE ? LIMIT 10");
$stmt->bind_param("ss", $search_param, $search_param);
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