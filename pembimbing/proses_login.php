<?php
session_start();
include '../koneksi.php'; // Pastikan file koneksi.php sudah dibuat

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $captcha_input = $_POST['captcha_input'] ?? '';
    $captcha_session = $_SESSION['captcha'] ?? '';
    
    // Hapus captcha sesi untuk pengamanan
    unset($_SESSION['captcha']);
    
    // Buat captcha baru untuk percobaan berikutnya
    $_SESSION['captcha'] = rand(1000, 9999);
    
    // 1. Validasi Captcha
    if (empty($captcha_input) || $captcha_input != $captcha_session) {
        $_SESSION['login_error'] = 'Kode Captcha salah.';
        header('Location: index.php');
        exit();
    }
    
    // 2. Query data pembimbing
    $stmt = $pdo->prepare("SELECT id_pembimbing, username, password, nama_pembimbing, kontak_pembimbing, password_status FROM pembimbing WHERE username = ?");
    $stmt->execute([$username]);
    $pembimbing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($pembimbing) {
        // 3. Verifikasi Password dengan Hash
        if (password_verify($password, $pembimbing['password'])) {
            
            // Login Berhasil: Daftarkan session
            $_SESSION['logged_in'] = true;
            $_SESSION['id_pembimbing'] = $pembimbing['id_pembimbing'];
            $_SESSION['nama_pembimbing'] = $pembimbing['nama_pembimbing'];
            $_SESSION['password_status'] = $pembimbing['password_status']; // Simpan status password
            
            // 4. Cek Status Password
            if ($pembimbing['password_status'] == 0) {
                header('Location: ganti_password.php');
                exit();
            } else {
                header('Location: dashboard.php');
                exit();
            }
        } else {
            // Password Salah
            $_SESSION['login_error'] = 'Username atau Password salah.';
            header('Location: index.php');
            exit();
        }
    } else {
        // Username Tidak Ditemukan
        $_SESSION['login_error'] = 'Username atau Password salah.';
        header('Location: index.php');
        exit();
    }
} else {
    // Akses langsung ke proses_login.php tanpa POST
    header('Location: index.php');
    exit();
}
?>