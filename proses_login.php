<?php
session_start();
include 'koneksi.php'; // Pastikan file koneksi.php sudah dibuat

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nis = $_POST['nis'] ?? '';
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
    
    // 2. Query data siswa
    $stmt = $pdo->prepare("SELECT id_siswa, password, nama_siswa, kelas, password_status FROM siswa WHERE nis = ?");
    $stmt->execute([$nis]);
    $siswa = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($siswa) {
        // 3. Verifikasi Password dengan Hash
        if (password_verify($password, $siswa['password'])) {
            
            // Login Berhasil: Daftarkan session
            $_SESSION['logged_in'] = true;
            $_SESSION['id_siswa'] = $siswa['id_siswa'];
            $_SESSION['nama_siswa'] = $siswa['nama_siswa'];
            $_SESSION['kelas_siswa'] = $siswa['kelas'];
            $_SESSION['password_status'] = $siswa['password_status']; // Simpan status password
            
            // 4. Cek Status Password
            if ($siswa['password_status'] == 0) {
                header('Location: ganti_password.php');
                exit();
            } else {
                header('Location: absen.php');
                exit();
            }
        } else {
            // Password Salah
            $_SESSION['login_error'] = 'NIS atau Password salah.';
            header('Location: index.php');
            exit();
        }
    } else {
        // NIS Tidak Ditemukan
        $_SESSION['login_error'] = 'NIS atau Password salah.';
        header('Location: index.php');
        exit();
    }
} else {
    // Akses langsung ke proses_login.php tanpa POST
    header('Location: index.php');
    exit();
}
?>