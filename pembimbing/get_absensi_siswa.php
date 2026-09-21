<?php
session_start();
include '../koneksi.php';
header('Content-Type: application/json');

// Validasi login dan role pembimbing
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_SESSION['id_pembimbing'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Akses ditolak. Silakan login kembali.'
    ]);
    exit();
}

// Validasi method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Method tidak valid.'
    ]);
    exit();
}

$id_pembimbing = $_SESSION['id_pembimbing'];
$id_siswa = $_POST['id_siswa'] ?? '';

// Validasi input
if (empty($id_siswa)) {
    echo json_encode([
        'success' => false,
        'message' => 'ID Siswa harus diisi.'
    ]);
    exit();
}

try {
    // Validasi bahwa siswa adalah siswa bimbingan pembimbing yang login
    $stmt_check = $pdo->prepare("
        SELECT COUNT(*) 
        FROM siswa 
        WHERE id_siswa = ? AND id_pembimbing = ?
    ");
    $stmt_check->execute([$id_siswa, $id_pembimbing]);
    
    if ($stmt_check->fetchColumn() == 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Siswa tidak ditemukan dalam daftar bimbingan Anda.'
        ]);
        exit();
    }
    
    // Query untuk mengambil informasi siswa (kontak dan tempat PKL)
    $stmt_info = $pdo->prepare("
        SELECT 
            s.kontak_siswa,
            tp.nama_tempat
        FROM 
            siswa s
        LEFT JOIN 
            tempat_pkl tp ON s.id_tempat = tp.id_tempat
        WHERE 
            s.id_siswa = ?
    ");
    $stmt_info->execute([$id_siswa]);
    $info_siswa = $stmt_info->fetch(PDO::FETCH_ASSOC);
    
    // Query untuk mengambil SEMUA data absensi siswa (tanpa filter tanggal)
    $stmt = $pdo->prepare("
        SELECT 
            tanggal_absensi,
            jam_masuk,
            status,
            lokasi_masuk,
            foto_bukti
        FROM 
            absensi
        WHERE 
            id_siswa = ?
        ORDER BY 
            tanggal_absensi DESC,
            jam_masuk DESC
    ");
    
    $stmt->execute([$id_siswa]);
    $absensi = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format data untuk DataTables
    $formatted_data = [];
    foreach ($absensi as $row) {
        $formatted_data[] = [
            'tanggal_absensi' => $row['tanggal_absensi'],
            'tanggal_display' => date('d-m-Y', strtotime($row['tanggal_absensi'])),
            'jam_masuk' => $row['jam_masuk'],
            'status' => $row['status'],
            'lokasi_masuk' => $row['lokasi_masuk'],
            'foto_bukti' => '../' . $row['foto_bukti'] // Path relatif dari folder pembimbing
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $formatted_data,
        'total' => count($formatted_data),
        'info_siswa' => [
            'kontak_siswa' => $info_siswa['kontak_siswa'] ?? null,
            'nama_tempat' => $info_siswa['nama_tempat'] ?? null
        ]
    ]);
    
} catch (PDOException $e) {
    error_log("Error di get_absensi_siswa.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Gagal mengambil data absensi dari database.'
    ]);
}
?>