<?php
session_start();
include '../koneksi.php';

// 1. Pengamanan (Cek Login)
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_SESSION['id_pembimbing'])) {
    die('Akses ditolak.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pembimbing = $_SESSION['id_pembimbing'];
    $id_siswa = $_POST['id_siswa'] ?? null;
    $nilai_sekolah = intval($_POST['nilai_sekolah'] ?? 0);
    $nilai_dudi = intval($_POST['nilai_dudi'] ?? 0);

    if (!$id_siswa) {
        $_SESSION['error_msg'] = "Identitas siswa tidak ditemukan.";
        header('Location: nilai_pkl.php');
        exit();
    }

    try {
        // 2. Validasi Hak Akses: Cek apakah siswa tersebut dibimbing oleh pembimbing ini
        $stmt_check = $pdo->prepare("SELECT id_pembimbing FROM siswa WHERE id_siswa = ?");
        $stmt_check->execute([$id_siswa]);
        $siswa = $stmt_check->fetch(PDO::FETCH_ASSOC);

        if (!$siswa || $siswa['id_pembimbing'] != $id_pembimbing) {
            $_SESSION['error_msg'] = "Anda tidak memiliki akses untuk menginput nilai siswa ini.";
            header('Location: nilai_pkl.php');
            exit();
        }

        // 3. Simpan Nilai (Insert atau Update)
        $sql = "INSERT INTO nilai_pkl (id_siswa, nilai_pembimbing_sekolah, nilai_pembimbing_dudi) 
                VALUES (?, ?, ?) 
                ON DUPLICATE KEY UPDATE 
                nilai_pembimbing_sekolah = VALUES(nilai_pembimbing_sekolah), 
                nilai_pembimbing_dudi = VALUES(nilai_pembimbing_dudi)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_siswa, $nilai_sekolah, $nilai_dudi]);

        $_SESSION['success_msg'] = "Nilai berhasil disimpan.";
        header('Location: nilai_pkl.php');
        exit();

    } catch (PDOException $e) {
        error_log("Error di proses_input_nilai.php: " . $e->getMessage());
        $_SESSION['error_msg'] = "Gagal menyimpan nilai ke database: " . $e->getMessage();
        header('Location: nilai_pkl.php');
        exit();
    }
} else {
    header('Location: nilai_pkl.php');
    exit();
}
?>
