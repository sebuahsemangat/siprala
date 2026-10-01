<?php
// Mulai sesi
session_start();

// 1. Hancurkan semua variabel sesi
$_SESSION = array();

// 2. Hancurkan cookie sesi (jika ada)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Hancurkan sesi
session_destroy();

// 4. Redirect ke halaman login (index.php)
header("Location: index.php");
exit();
?>