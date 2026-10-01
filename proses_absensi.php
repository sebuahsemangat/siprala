<?php
session_start();
include 'koneksi.php'; // Pastikan file koneksi.php tersedia
date_default_timezone_set('Asia/Jakarta');

// =========================================================================
// 1. CEK SESI DAN VALIDASI REQUEST
// =========================================================================
if (!isset($_SESSION['id_siswa']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('<div class="alert alert-danger">Akses ditolak atau sesi berakhir. Silakan login kembali.</div>');
}

// =========================================================================
// 2. AMBIL DAN VALIDASI DATA POST
// =========================================================================
$id_siswa = $_SESSION['id_siswa'];
$tanggal_absensi = date('Y-m-d'); // Format standar database: Y-m-d
$jam_masuk = date('H:i:s');
$status = $_POST['status'] ?? 'Hadir';
$lokasi_masuk = $_POST['lokasi_masuk'] ?? '';
$keterangan = $_POST['keterangan'] ?? '';

// Validasi status kehadiran
$status_valid = ['Hadir', 'Sakit', 'Izin'];
if (!in_array($status, $status_valid)) {
    die('<div class="alert alert-danger">Status kehadiran tidak valid.</div>');
}

// Validasi keterangan untuk status Sakit dan Izin
if (($status === 'Sakit' || $status === 'Izin') && trim($keterangan) === '') {
    die('<div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-2"></i>Keterangan wajib diisi untuk status Sakit atau Izin.</div>');
}

// Sanitasi keterangan
$keterangan = trim($keterangan);
if (strlen($keterangan) > 500) {
    die('<div class="alert alert-danger">Keterangan terlalu panjang. Maksimal 500 karakter.</div>');
}

// Set keterangan NULL jika status Hadir
if ($status === 'Hadir') {
    $keterangan = null;
}

// Validasi lokasi (harus ada dan format lat,long)
if (empty($lokasi_masuk) || !preg_match('/^-?\d+(\.\d+)?,-?\d+(\.\d+)?$/', $lokasi_masuk)) {
    die('<div class="alert alert-danger">Lokasi GPS tidak valid. Pastikan izin lokasi browser diaktifkan.</div>');
}

// Validasi file upload
if (!isset($_FILES['foto_bukti']) || $_FILES['foto_bukti']['error'] !== UPLOAD_ERR_OK) {
    $error_code = $_FILES['foto_bukti']['error'] ?? 'unknown';
    $error_messages = [
        UPLOAD_ERR_INI_SIZE => 'Ukuran file melebihi batas maksimum server (upload_max_filesize).',
        UPLOAD_ERR_FORM_SIZE => 'Ukuran file melebihi batas maksimum form.',
        UPLOAD_ERR_PARTIAL => 'File hanya terupload sebagian.',
        UPLOAD_ERR_NO_FILE => 'Tidak ada file yang diupload.',
        UPLOAD_ERR_NO_TMP_DIR => 'Folder temporary tidak ditemukan di server.',
        UPLOAD_ERR_CANT_WRITE => 'Gagal menulis file ke disk server.',
        UPLOAD_ERR_EXTENSION => 'Upload dihentikan oleh ekstensi PHP.'
    ];
    $error_msg = $error_messages[$error_code] ?? 'Error tidak diketahui (' . $error_code . ').';
    die('<div class="alert alert-danger">Gagal upload foto: ' . $error_msg . '</div>');
}

// Validasi tipe file (hanya gambar)
$allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
$file_type = $_FILES['foto_bukti']['type'];
if (!in_array($file_type, $allowed_types)) {
    die('<div class="alert alert-danger">Tipe file tidak valid. Hanya file gambar (JPG, PNG, GIF, WEBP) yang diperbolehkan.</div>');
}

// =========================================================================
// 3. FUNGSI BANTU UNTUK SANITASI NAMA FOLDER/FILE
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
// 4. AMBIL NAMA SISWA & NAMA TEMPAT PKL DARI DATABASE
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
        die('<div class="alert alert-danger">Data siswa atau tempat PKL tidak ditemukan di database.</div>');
    }

    $nama_siswa = $data_siswa['nama_siswa'];
    $nama_tempat = $data_siswa['nama_tempat'];

} catch (PDOException $e) {
    error_log("Database error di proses_absensi.php: " . $e->getMessage());
    die('<div class="alert alert-danger">Gagal mengambil data dari database. Silakan coba lagi.</div>');
}

// =========================================================================
// 5. CEK DUPLIKASI ABSENSI HARI INI
// =========================================================================
try {
    $stmt_check = $pdo->prepare("
        SELECT COUNT(*) 
        FROM absensi 
        WHERE id_siswa = ? AND tanggal_absensi = ?
    ");
    $stmt_check->execute([$id_siswa, $tanggal_absensi]);
    
    if ($stmt_check->fetchColumn() > 0) {
        die('<div class="alert alert-warning"><strong>Perhatian!</strong> Anda sudah melakukan absensi hari ini.</div>');
    }
} catch (PDOException $e) {
    error_log("Database error saat cek duplikasi: " . $e->getMessage());
    die('<div class="alert alert-danger">Gagal memeriksa data absensi. Silakan coba lagi.</div>');
}

// =========================================================================
// 6. KONSTRUKSI PATH DAN NAMA FILE
// =========================================================================

// Tentukan Folder Tujuan (menggunakan nama tempat yang telah disanitasi)
$folder_name_safe = sanitizeFileName($nama_tempat);
$nama_siswa_safe = sanitizeFileName($nama_siswa);
$target_dir = "uploads/absensi/" . $folder_name_safe . "/" . $nama_siswa_safe . "/";

// Ambil ekstensi dari tipe MIME (lebih reliable dari nama file)
$mime_to_ext = [
    'image/jpeg' => 'jpg',
    'image/jpg' => 'jpg',
    'image/png' => 'png',
    'image/gif' => 'gif',
    'image/webp' => 'webp'
];
$file_ext = $mime_to_ext[$file_type] ?? 'jpg';

// Format nama file: namasiswa_YYYYMMDD_HHiiss.ext (tambahkan timestamp untuk keunikan)
$timestamp = date('Ymd_His');
$file_name = $nama_siswa_safe . "_" . $timestamp . "." . $file_ext;
$target_file = $target_dir . $file_name;

// =========================================================================
// 7. PROSES UPLOAD FOTO (RACE-CONDITION SAFE)
// =========================================================================

// Buat folder jika belum ada - dengan penanganan race condition
if (!is_dir($target_dir)) {
    // Suppress warning karena folder mungkin dibuat oleh request lain secara bersamaan
    // @ operator mencegah error jika folder sudah dibuat oleh proses lain
    @mkdir($target_dir, 0755, true);
    
    // Double-check: Verifikasi ulang apakah folder berhasil dibuat
    // Ini menangani kasus di mana multiple requests membuat folder bersamaan
    if (!is_dir($target_dir)) {
        // Jika masih gagal setelah double-check, berarti ada masalah permission
        $parent_dir = dirname($target_dir);
        $error_msg = '<div class="alert alert-danger">';
        $error_msg .= '<i class="fas fa-exclamation-triangle me-2"></i><strong>Gagal membuat folder penyimpanan!</strong><br>';
        $error_msg .= '<small>Path: ' . htmlspecialchars($target_dir) . '</small><br>';
        $error_msg .= '<small>Parent Directory Writable: ' . (is_writable($parent_dir) ? 'Yes' : 'No') . '</small><br>';
        $error_msg .= '<small>Hubungi administrator untuk memeriksa permission folder.</small>';
        $error_msg .= '</div>';
        
        error_log("Failed to create directory after race-condition check: {$target_dir}");
        die($error_msg);
    }
}

// Verifikasi folder writable sebelum upload
if (!is_writable($target_dir)) {
    $error_msg = '<div class="alert alert-danger">';
    $error_msg .= '<i class="fas fa-exclamation-triangle me-2"></i><strong>Folder tidak dapat ditulis!</strong><br>';
    $error_msg .= '<small>Path: ' . htmlspecialchars($target_dir) . '</small><br>';
    $error_msg .= '<small>Hubungi administrator untuk memeriksa permission folder.</small>';
    $error_msg .= '</div>';
    
    error_log("Directory not writable: {$target_dir}");
    die($error_msg);
}

// Pindahkan file yang diupload
if (move_uploaded_file($_FILES["foto_bukti"]["tmp_name"], $target_file)) {
    
    // =========================================================================
    // 8. VALIDASI TANGGAL FOTO (Timestamp File)
    // =========================================================================
    $file_mtime = filemtime($target_file);
    $file_date = date('Y-m-d', $file_mtime);
    $today_date = date('Y-m-d');
    
    // Hitung selisih hari antara tanggal file dengan hari ini
    $date_diff = abs(strtotime($today_date) - strtotime($file_date)) / 86400;
    
    // Toleransi 1 hari (untuk foto yang diambil malam hari atau perbedaan timezone)
    if ($date_diff > 1) {
        // Hapus file karena tidak valid
        if (file_exists($target_file)) {
            unlink($target_file);
        }
        
        $error_msg = '<div class="alert alert-danger">';
        $error_msg .= '<i class="fas fa-exclamation-triangle me-2"></i><strong>Foto Ditolak!</strong><br>';
        $error_msg .= '<small>Foto yang diupload terlalu lama (tanggal: ' . date('d-m-Y', $file_mtime) . '). ';
        $error_msg .= 'Gunakan foto yang baru diambil hari ini untuk absensi.</small>';
        $error_msg .= '</div>';
        
        die($error_msg);
    }
    
    // =========================================================================
    // 9. INSERT DATA KE DATABASE
    // =========================================================================
    try {
        $sql = "INSERT INTO absensi (id_siswa, tanggal_absensi, jam_masuk, status, lokasi_masuk, foto_bukti, keterangan) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $id_siswa, 
            $tanggal_absensi, 
            $jam_masuk, 
            $status, 
            $lokasi_masuk, 
            $target_file,
            $keterangan // NULL untuk Hadir, atau string untuk Sakit/Izin
        ]);
        
        // Output Sukses ke AJAX dengan informasi keterangan jika ada
        $success_msg = '<div class="alert alert-success"><strong>Berhasil!</strong> Absensi Anda telah dicatat.<br>';
        $success_msg .= '<small>Tanggal: ' . date('d-m-Y') . ' | Jam: ' . $jam_masuk . ' | Status: ' . htmlspecialchars($status) . '</small>';
        if ($keterangan) {
            $success_msg .= '<br><small>Keterangan: ' . htmlspecialchars($keterangan) . '</small>';
        }
        $success_msg .= '</div>';
        
        echo $success_msg;
        
    } catch (PDOException $e) {
        // Hapus file jika insert gagal (rollback manual)
        if (file_exists($target_file)) {
            unlink($target_file);
        }
        error_log("Database error saat insert absensi: " . $e->getMessage());
        echo '<div class="alert alert-danger">Gagal menyimpan data absensi ke database. Silakan coba lagi.</div>';
    }
    
} else {
    // Menangani error upload
    $error_code = $_FILES["foto_bukti"]["error"];
    $upload_error = "Gagal mengupload file foto.";
    
    if ($error_code == UPLOAD_ERR_INI_SIZE || $error_code == UPLOAD_ERR_FORM_SIZE) {
        $upload_error .= " Ukuran file melebihi batas upload server. Pastikan kompresi client-side berjalan normal.";
    } else if ($error_code == UPLOAD_ERR_CANT_WRITE) {
        $upload_error .= " Server tidak dapat menulis file. Hubungi administrator.";
    }
    
    echo '<div class="alert alert-danger">' . $upload_error . '</div>';
}
?>