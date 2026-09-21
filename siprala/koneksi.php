<?php
// koneksi.php - File Koneksi Database

// Konfigurasi Database
define('DB_SERVER', 'localhost');
define('DB_USER', 'root'); // Ganti dengan username database Anda
define('DB_PASSWORD', ''); // Ganti dengan password database Anda
define('DB_NAME', 'siprala'); // Nama database sesuai permintaan Anda

// Buat Koneksi MySQLi
$koneksi = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME);

// Cek Koneksi MySQLi
if ($koneksi->connect_error) {
    die("Koneksi database gagal: " . $koneksi->connect_error);
}

// Set karakter encoding ke UTF-8 (Penting untuk Dompdf)
$koneksi->set_charset("utf8");

// Buat Koneksi PDO untuk kompatibilitas modul admin
try {
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASSWORD, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    // Log error tanpa menghentikan jika mysqli masih berjalan
    error_log("Koneksi PDO gagal: " . $e->getMessage());
}

// Catatan: Variabel $koneksi (MySQLi) dan $pdo (PDO) siap digunakan.
?>