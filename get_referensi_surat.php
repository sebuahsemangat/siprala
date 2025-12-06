<?php
// get_referensi_surat.php
include 'koneksi.php';

header('Content-Type: application/json');

// Perbaikan: Hapus t.alamat dan t.kota jika kolom tersebut tidak ada di tabel tempat_pkl
$query = "
    SELECT 
        s.id_surat, 
        s.no_surat,
        s.tanggal, 
        t.id_tempat, 
        t.nama_tempat
    FROM surat s
    JOIN tempat_pkl t ON s.id_tempat_pkl = t.id_tempat
    WHERE s.perihal LIKE '%Pengajuan Tempat%' 
    ORDER BY s.id_surat DESC
";

$result = $koneksi->query($query);
$data = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
} else {
    // Opsional: Debugging jika kosong (bisa dihapus nanti)
    // $data[] = ['error' => 'Data kosong atau Query Gagal: ' . $koneksi->error];
}

echo json_encode($data);
$koneksi->close();
?>