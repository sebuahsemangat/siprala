<?php
// proses_login.php - Memproses autentikasi login admin
session_start();
require_once 'koneksi.php';

// Tolak akses langsung (bukan dari form POST)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit();
}

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

// Validasi input kosong
if (empty($username) || empty($password)) {
    $_SESSION['login_error'] = 'Username dan password tidak boleh kosong.';
    header("Location: login.php");
    exit();
}

// Cari admin berdasarkan username menggunakan prepared statement
$stmt = $koneksi->prepare("SELECT id_admin, nama_lengkap, username, password, email FROM admin WHERE username = ? LIMIT 1");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $admin = $result->fetch_assoc();

    // Verifikasi password
    // Mendukung dua skenario:
    // 1. Password tersimpan sebagai hash (password_hash / bcrypt) — lebih aman
    // 2. Password tersimpan sebagai plain text — untuk kompatibilitas awal
    $passwordValid = false;

    if (password_verify($password, $admin['password'])) {
        // Hash bcrypt/argon2 — metode yang aman
        $passwordValid = true;
    } elseif ($password === $admin['password']) {
        // Plain text — fallback untuk data lama (sebaiknya di-hash ulang)
        $passwordValid = true;
    }

    if ($passwordValid) {
        // Login berhasil — set session
        session_regenerate_id(true); // Cegah session fixation

        $_SESSION['admin_id']       = $admin['id_admin'];
        $_SESSION['admin_username'] = $admin['username'];
        $_SESSION['admin_nama']     = $admin['nama_lengkap'];
        $_SESSION['admin_email']    = $admin['email'];
        $_SESSION['login_time']     = time();

        $stmt->close();
        header("Location: index.php");
        exit();
    }
}

$stmt->close();

// Login gagal
$_SESSION['login_error'] = 'Username atau password yang Anda masukkan salah.';
header("Location: login.php");
exit();
?>
