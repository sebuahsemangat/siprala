<?php
session_start();
include '../koneksi.php'; // Pastikan file koneksi.php sudah tersedia

// Pengamanan: Cek Login
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

$id_pembimbing = $_SESSION['id_pembimbing'];
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// 1. Validasi Input
if (empty($new_password) || empty($confirm_password)) {
    $_SESSION['status_message'] = 'Semua kolom harus diisi.';
    $_SESSION['status_type'] = 'danger';
    header('Location: ganti_password.php');
    exit();
}

if ($new_password !== $confirm_password) {
    $_SESSION['status_message'] = 'Konfirmasi password tidak cocok.';
    $_SESSION['status_type'] = 'danger';
    header('Location: ganti_password.php');
    exit();
}

if (strlen($new_password) < 6) {
    $_SESSION['status_message'] = 'Password minimal 6 karakter.';
    $_SESSION['status_type'] = 'danger';
    header('Location: ganti_password.php');
    exit();
}

try {
    // 2. Hash Password Baru
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // 3. Update Database
    // Update kolom password dan set password_status menjadi 1
    $sql = "UPDATE pembimbing SET password = ?, password_status = 1 WHERE id_pembimbing = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$hashed_password, $id_pembimbing]);

    // 4. Update Session Status
    $_SESSION['password_status'] = 1;

    // 5. Beri Pesan Sukses dan Redirect ke halaman absen
    $_SESSION['status_message'] = 'Password berhasil diubah. Sekarang Anda dapat melanjutkan ke absensi.';
    $_SESSION['status_type'] = 'success';
    header('Location: dashboard.php');
    exit();

} catch (PDOException $e) {
    // Tangani error database
    $_SESSION['status_message'] = 'Terjadi kesalahan database: ' . $e->getMessage();
    $_SESSION['status_type'] = 'danger';
    header('Location: ganti_password.php');
    exit();
}
?>