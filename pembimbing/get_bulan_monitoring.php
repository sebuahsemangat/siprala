<?php
session_start();
include '../koneksi.php';
header('Content-Type: application/json');

if (!isset($_SESSION['id_pembimbing'])) {
    echo json_encode(['success' => false, 'message' => 'Sesi habis']);
    exit;
}

try {
    $id_pembimbing = $_SESSION['id_pembimbing'];

    // Ambil daftar bulan unik yang ada di tabel absensi_mingguan untuk pembimbing yang login
    $stmt = $pdo->prepare("
        SELECT 
            DATE_FORMAT(tanggal_monitoring, '%Y-%m') AS periode,
            MONTH(tanggal_monitoring) AS bulan_num,
            YEAR(tanggal_monitoring) AS tahun
        FROM absensi_mingguan
        WHERE id_pembimbing = ?
        GROUP BY periode, bulan_num, tahun
        ORDER BY periode DESC
    ");
    $stmt->execute([$id_pembimbing]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $bulan_indo = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];

    $data = [];
    foreach ($rows as $r) {
        $nama_bulan = $bulan_indo[(int)$r['bulan_num']] ?? 'Bulan ' . $r['bulan_num'];
        $data[] = [
            'periode' => $r['periode'],
            'label' => $nama_bulan . ' ' . $r['tahun']
        ];
    }

    echo json_encode(['success' => true, 'data' => $data]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
