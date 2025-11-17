<?php
// koneksi.php - File Koneksi Database

// Konfigurasi Database
define('DB_SERVER', 'localhost');
define('DB_USER', 'root'); // Ganti dengan username database Anda
define('DB_PASSWORD', ''); // Ganti dengan password database Anda
define('DB_NAME', 'surat_pkl'); // Nama database sesuai permintaan Anda

// Buat Koneksi
$koneksi = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME);

// Cek Koneksi
if ($koneksi->connect_error) {
    // Berhenti dan tampilkan error yang jelas
    die("Koneksi database gagal: " . $koneksi->connect_error);
}

// Set karakter encoding ke UTF-8 (Penting untuk Dompdf)
$koneksi->set_charset("utf8");

// Catatan: Variabel $koneksi siap digunakan di file yang meng-include ini.
?>