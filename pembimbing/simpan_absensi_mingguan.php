<?php
session_start();
include '../koneksi.php';

// Atur header json
header('Content-Type: application/json');

// 1. Cek Login
if (!isset($_SESSION['id_pembimbing'])) {
    echo json_encode(['success' => false, 'message' => 'Akses ditolak. Silakan login kembali.']);
    exit;
}

// 2. Cek Method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method invalid']);
    exit;
}

$id_pembimbing = $_SESSION['id_pembimbing'];
$id_tempat = $_POST['id_tempat'] ?? '';
$minggu_ke = $_POST['minggu_ke'] ?? '';
$tanggal = $_POST['tanggal_monitoring'] ?? '';
$platform = $_POST['platform'] ?? '';
$platform_lainnya = $_POST['platform_lainnya'] ?? null;
$catatan = $_POST['catatan'] ?? '';

// 3. Validasi Input Dasar
if (empty($id_tempat) || empty($minggu_ke) || empty($tanggal) || empty($platform) || empty($catatan)) {
    echo json_encode(['success' => false, 'message' => 'Mohon lengkapi semua field wajib.']);
    exit;
}

// 4. Proses Upload Foto
if (!isset($_FILES['foto_bukti']) || $_FILES['foto_bukti']['error'] != 0) {
    echo json_encode(['success' => false, 'message' => 'Foto bukti wajib diupload.']);
    exit;
}

$allowed_ext = ['jpg', 'jpeg', 'png'];
$file_name = $_FILES['foto_bukti']['name'];
$file_tmp = $_FILES['foto_bukti']['tmp_name'];
$file_size = $_FILES['foto_bukti']['size'];
$file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

// Validasi Ekstensi
if (!in_array($file_ext, $allowed_ext)) {
    echo json_encode(['success' => false, 'message' => 'Format file harus JPG, JPEG, atau PNG.']);
    exit;
}

// Validasi Ukuran (Max 2MB)
if ($file_size > 2 * 1024 * 1024) {
    echo json_encode(['success' => false, 'message' => 'Ukuran foto maksimal 2MB.']);
    exit;
}

// Generate nama file baru & Pindahkan
// Pastikan folder 'uploads/absensi_mingguan' sudah ada
$upload_dir = '../uploads/absensi_mingguan/';
if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

$new_file_name = 'absen_minggu_' . time() . '_' . uniqid() . '.' . $file_ext;
$destination = $upload_dir . $new_file_name;
// Simpan path relative untuk database (hilangkan '../')
$db_path = 'uploads/absensi_mingguan/' . $new_file_name; 

if (!move_uploaded_file($file_tmp, $destination)) {
    echo json_encode(['success' => false, 'message' => 'Gagal mengupload gambar.']);
    exit;
}

try {
    // 5. Simpan ke Database
    $sql = "INSERT INTO absensi_mingguan 
            (id_pembimbing, id_tempat, minggu_ke, tanggal_monitoring, platform, platform_lainnya, catatan, foto_bukti) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $id_pembimbing, 
        $id_tempat, 
        $minggu_ke,
        $tanggal, 
        $platform, 
        $platform_lainnya, 
        $catatan, 
        $db_path
    ]);

    echo json_encode(['success' => true, 'message' => 'Laporan mingguan berhasil disimpan!']);

} catch (PDOException $e) {
    // Hapus file jika database gagal
    if (file_exists($destination)) unlink($destination);
    
    error_log("Error Absensi Mingguan: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Gagal menyimpan ke database.']);
}
?>