<?php
session_start();
include '../koneksi.php';
header('Content-Type: application/json');

// Validasi login dan role pembimbing
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_SESSION['id_pembimbing'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Akses ditolak.'
    ]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Metode request tidak valid.']);
    exit();
}

$id_pembimbing = $_SESSION['id_pembimbing'];
$id_siswa = $_POST['id_siswa'] ?? null;
$nilai_sekolah = $_POST['nilai_sekolah'] ?? null;
$nilai_dudi = $_POST['nilai_dudi'] ?? null;

if (!$id_siswa) {
    echo json_encode(['success' => false, 'message' => 'ID Siswa diperlukan.']);
    exit();
}

if ($nilai_sekolah === "" || $nilai_dudi === "" || $nilai_sekolah === null || $nilai_dudi === null) {
    echo json_encode(['success' => false, 'message' => 'Semua inputan nilai harus diisi.']);
    exit();
}

try {
    // 1. Verifikasi apakah siswa tersebut milik pembimbing yang sedang login
    $stmtVerif = $pdo->prepare("SELECT id_siswa FROM siswa WHERE id_siswa = ? AND id_pembimbing = ?");
    $stmtVerif->execute([$id_siswa, $id_pembimbing]);

    if (!$stmtVerif->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Anda tidak memiliki hak akses untuk menginput nilai siswa ini.']);
        exit();
    }

    // 2. Ambil nilai sidang jika sudah ada untuk hitung rata-rata
    $stmtSidang = $pdo->prepare("SELECT nilai_sidang FROM nilai_pkl WHERE id_siswa = ?");
    $stmtSidang->execute([$id_siswa]);
    $row = $stmtSidang->fetch(PDO::FETCH_ASSOC);
    $nsidang = $row ? (int) $row['nilai_sidang'] : 0;

    // Pastikan nilai dikonversi ke int atau NULL
    $valSekolah = ($nilai_sekolah === "" || $nilai_sekolah === null) ? null : (int) $nilai_sekolah;
    $valDudi = ($nilai_dudi === "" || $nilai_dudi === null) ? null : (int) $nilai_dudi;

    // Hitung rata-rata
    $ns_calc = $valSekolah ?? 0;
    $nd_calc = $valDudi ?? 0;
    $nilai_akhir = ($ns_calc + $nd_calc + $nsidang) / 3;
    $nilai_akhir = round($nilai_akhir, 2);

    // 3. Simpan atau Update Nilai
    $stmtSave = $pdo->prepare("
        INSERT INTO nilai_pkl (id_siswa, nilai_pembimbing_sekolah, nilai_pembimbing_dudi, nilai_akhir)
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            nilai_pembimbing_sekolah = VALUES(nilai_pembimbing_sekolah),
            nilai_pembimbing_dudi = VALUES(nilai_pembimbing_dudi),
            nilai_akhir = VALUES(nilai_akhir)
    ");

    $stmtSave->execute([$id_siswa, $valSekolah, $valDudi, $nilai_akhir]);

    echo json_encode([
        'success' => true,
        'message' => 'Data nilai berhasil disimpan.'
    ]);

} catch (PDOException $e) {
    error_log("Error di simpan_nilai_pkl.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan saat menyimpan data ke database.'
    ]);
}
?>