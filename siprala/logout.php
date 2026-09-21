<?php
// logout.php - Proses logout admin
session_start();

// Hapus semua data session
$_SESSION = [];

// Hapus cookie session jika ada
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

session_destroy();

// Mulai session baru untuk menyimpan pesan sukses
session_start();
$_SESSION['logout_success'] = 'Anda telah berhasil keluar dari sistem.';

header("Location: login.php");
exit();
?>
