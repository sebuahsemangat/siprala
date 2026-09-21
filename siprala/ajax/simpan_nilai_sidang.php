<?php
session_start();
require_once '../koneksi.php';
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['admin_id']) && !isset($_SESSION['logged_in_admin'])) {
    echo json_encode(['success' => false, 'message' => 'Akses ditolak. Silakan login terlebih dahulu.']);
    exit();
}

$id_siswa = isset($_POST['id_siswa']) ? intval($_POST['id_siswa']) : 0;
$nilai_sidang = isset($_POST['nilai_sidang']) ? trim($_POST['nilai_sidang']) : null;

if ($id_siswa <= 0 || $nilai_sidang === "" || $nilai_sidang === null) {
    echo json_encode(['success' => false, 'message' => 'ID Siswa dan Nilai Sidang harus diisi.']);
    exit();
}

$nsidang = floatval($nilai_sidang);
if ($nsidang < 0 || $nsidang > 100) {
    echo json_encode(['success' => false, 'message' => 'Nilai Sidang harus berada di antara 0 sampai 100.']);
    exit();
}

try {
    // 1. Ambil nilai lainnya untuk hitung rata-rata
    $stmt = $pdo->prepare("SELECT nilai_pembimbing_sekolah, nilai_pembimbing_dudi FROM nilai_pkl WHERE id_siswa = ?");
    $stmt->execute([$id_siswa]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $ns = $row ? (float)$row['nilai_pembimbing_sekolah'] : 0;
    $nd = $row ? (float)$row['nilai_pembimbing_dudi'] : 0;
    
    // Hitung rata-rata: (N.Sekolah + N.DU/DI + Nilai Sidang) / 3
    $nilai_akhir = round(($ns + $nd + $nsidang) / 3, 2);

    // 2. Simpan atau Update
    $stmtSave = $pdo->prepare("
        INSERT INTO nilai_pkl (id_siswa, nilai_sidang, nilai_akhir)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            nilai_sidang = VALUES(nilai_sidang),
            nilai_akhir = VALUES(nilai_akhir)
    ");
    
    $stmtSave->execute([$id_siswa, $nsidang, $nilai_akhir]);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Nilai sidang berhasil disimpan.',
        'nilai_sidang' => $nsidang,
        'nilai_akhir' => $nilai_akhir
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error database: ' . $e->getMessage()]);
}
?>
