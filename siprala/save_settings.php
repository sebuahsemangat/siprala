<?php
// save_settings.php - Proses Update Settings via AJAX
header('Content-Type: application/json');

include 'koneksi.php'; // Pastikan koneksi.php tersedia

$response = ['success' => false, 'message' => 'Gagal memperbarui pengaturan.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil dan bersihkan data POST
    $id_settings = $_POST['id_settings'] ?? 1; // Default ke ID 1
    $format_nomor_surat = $_POST['format_nomor_surat'] ?? '';
    $nama_sekolah = $_POST['nama_sekolah'] ?? '';
    $tgl_mulai = $_POST['tgl_mulai'] ?? date('Y-m-d');
    $tgl_selesai = $_POST['tgl_selesai'] ?? date('Y-m-d');
    $nama_kepsek = $_POST['nama_kepsek'] ?? '';
    
    // Pastikan ID yang diupdate adalah 1 (atau sesuai kebutuhan)
    if ($id_settings != 1) {
        $response['message'] = 'ID Settings tidak valid.';
        echo json_encode($response);
        exit;
    }

    // Query UPDATE
    $sql = "UPDATE settings SET 
                format_nomor_surat = ?,
                nama_sekolah = ?,
                tgl_mulai = ?,
                tgl_selesai = ?,
                nama_kepsek = ?
            WHERE id_settings = ?";
            
    $stmt = $koneksi->prepare($sql);
    
    // Bind parameter
    $stmt->bind_param("sssssi", 
        $format_nomor_surat, 
        $nama_sekolah, 
        $tgl_mulai, 
        $tgl_selesai, 
        $nama_kepsek,
        $id_settings // Bind ID 1
    );

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Pengaturan berhasil diperbarui!';
    } else {
        $response['message'] = 'Gagal menjalankan query: ' . $stmt->error;
    }

    $stmt->close();
}

$koneksi->close();
echo json_encode($response);
?>