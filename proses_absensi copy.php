<?php
session_start();
include 'koneksi.php'; // Pastikan file koneksi.php tersedia
date_default_timezone_set('Asia/Jakarta');

// Cek Sesi dan POST
if (!isset($_SESSION['id_siswa']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('<div class="alert alert-danger">Akses ditolak atau sesi berakhir.</div>');
}

$id_siswa = $_SESSION['id_siswa'];
$tanggal_absensi = date('d-M-Y'); // Format Y-m-d aman untuk DB dan File
$jam_masuk = date('H:i:s');
$status = $_POST['status'] ?? 'Hadir';
$lokasi_masuk = $_POST['lokasi_masuk'] ?? 'Tidak diketahui';

// =========================================================================
// FUNGSI BANTU UNTUK SANITASI NAMA FOLDER/FILE
// =========================================================================
function sanitizeFileName($filename) {
    // 1. Hilangkan karakter non-alphanumeric, kecuali spasi, underscore, dan hyphen.
    $filename = preg_replace('/[^\w\s\-\.]/', '', $filename);
    // 2. Ganti spasi, underscore, dan hyphen menjadi underscore tunggal
    $filename = preg_replace('/[\s\-\_]+/', '_', $filename);
    // 3. Konversi ke huruf kecil dan hapus spasi di awal/akhir
    $filename = strtolower(trim($filename));
    return $filename;
}

// =========================================================================
// 1. AMBIL NAMA SISWA & NAMA TEMPAT PKL DARI DATABASE
// =========================================================================
try {
    $stmt_data = $pdo->prepare("
        SELECT 
            s.nama_siswa, 
            tp.nama_tempat 
        FROM 
            siswa s
        JOIN 
            tempat_pkl tp ON s.id_tempat = tp.id_tempat
        WHERE 
            s.id_siswa = ?
    ");
    $stmt_data->execute([$id_siswa]);
    $data_siswa = $stmt_data->fetch(PDO::FETCH_ASSOC);

    if (!$data_siswa) {
        die('<div class="alert alert-danger">Data siswa atau tempat PKL tidak ditemukan.</div>');
    }

    $nama_siswa = $data_siswa['nama_siswa'];
    $nama_tempat = $data_siswa['nama_tempat'];

} catch (PDOException $e) {
    die('<div class="alert alert-danger">Gagal mengambil data dari database: ' . $e->getMessage() . '</div>');
}


// 2. Cek Duplikasi Absensi Hari Ini (Penting!)
$stmt_check = $pdo->prepare("SELECT COUNT(*) FROM absensi WHERE id_siswa = ? AND tanggal_absensi = ?");
$stmt_check->execute([$id_siswa, $tanggal_absensi]);
if ($stmt_check->fetchColumn() > 0) {
    die('<div class="alert alert-warning">Anda sudah melakukan absensi hari ini.</div>');
}


// =========================================================================
// 3. KONSTRUKSI PATH DAN NAMA FILE BARU
// =========================================================================

// Tentukan Folder Tujuan (menggunakan nama tempat yang telah disanitasi)
$folder_name_safe = sanitizeFileName($nama_tempat);
$target_dir = "uploads/absensi/" . $folder_name_safe . "/";

// Tentukan Nama File Baru: nama_siswa_tanggal_absensi.[ext]
$nama_siswa_safe = sanitizeFileName($nama_siswa);
$file_ext = pathinfo($_FILES["foto_bukti"]["name"], PATHINFO_EXTENSION);

// Jika ekstensi kosong (karena kompresi menghasilkan blob/file tanpa nama asli)
if (empty($file_ext) || $file_ext == 'blob') {
    $file_ext = 'jpg'; // Asumsi kompresi client-side menghasilkan JPG
}

$file_name = $nama_siswa_safe . "_" . $tanggal_absensi . "." . $file_ext;
$target_file = $target_dir . $file_name;


// 4. Proses Upload Foto
if (!is_dir($target_dir)) {
    // Buat folder jika belum ada (rekursif)
    if (!mkdir($target_dir, 0777, true)) {
        die('<div class="alert alert-danger">Gagal membuat folder penyimpanan: ' . htmlspecialchars($target_dir) . '</div>');
    }
}

if (move_uploaded_file($_FILES["foto_bukti"]["tmp_name"], $target_file)) {
    
    // 5. Insert Data ke Database
    try {
        $sql = "INSERT INTO absensi (id_siswa, tanggal_absensi, jam_masuk, status, lokasi_masuk, foto_bukti) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $id_siswa, 
            $tanggal_absensi, 
            $jam_masuk, 
            $status, 
            $lokasi_masuk, 
            $target_file // Simpan path lengkap (termasuk folder tempat PKL)
        ]);
        
        // Output Sukses ke AJAX
        echo '<div class="alert alert-success">Absensi berhasil dicatat!</div>';
        
    } catch (PDOException $e) {
        // Hapus file jika insert gagal
        if (file_exists($target_file)) unlink($target_file);
        echo '<div class="alert alert-danger">Gagal menyimpan data ke database: ' . $e->getMessage() . '</div>';
    }
} else {
    // Menangani error upload (misalnya ukuran terlalu besar sebelum kompresi)
    $error_code = $_FILES["foto_bukti"]["error"];
    $upload_error = "Gagal mengupload file foto. Kode Error: $error_code";
    
    if ($error_code == UPLOAD_ERR_INI_SIZE || $error_code == UPLOAD_ERR_FORM_SIZE) {
        $upload_error .= " (Ukuran file melebihi batas upload server. Pastikan kompresi client-side berjalan normal).";
    }
    echo '<div class="alert alert-danger">' . $upload_error . '</div>';
}
?>