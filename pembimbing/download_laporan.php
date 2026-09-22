<?php
session_start();
require_once '../koneksi.php';

// 1. Cek Login
if (!isset($_SESSION['id_pembimbing'])) {
    die("Akses ditolak. Silakan login kembali.");
}

$id_pembimbing = $_SESSION['id_pembimbing'];
$nama_pembimbing = $_SESSION['nama_pembimbing'] ?? 'Pembimbing';

// 2. Tangkap Parameter Filter
$id_tempat = $_GET['id_tempat'] ?? 'all';
$bulan = $_GET['bulan'] ?? 'all';
$format = strtolower($_GET['format'] ?? 'pdf');

// Format nama bulan untuk judul
$bulan_indo = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];

$label_bulan = "Semua Periode";
if ($bulan !== 'all' && preg_match('/^(\d{4})-(\d{2})$/', $bulan, $m)) {
    $thn = $m[1];
    $bln_num = (int)$m[2];
    $label_bulan = ($bulan_indo[$bln_num] ?? 'Bulan ' . $bln_num) . ' ' . $thn;
}

$label_tempat = "Semua Tempat PKL Binaan";
if ($id_tempat !== 'all' && is_numeric($id_tempat)) {
    $stmt_tp = $pdo->prepare("SELECT nama_tempat FROM tempat_pkl WHERE id_tempat = ?");
    $stmt_tp->execute([$id_tempat]);
    $tp = $stmt_tp->fetch(PDO::FETCH_ASSOC);
    if ($tp) {
        $label_tempat = $tp['nama_tempat'];
    }
}

// 3. Query Data Absensi Mingguan
$query = "SELECT am.*, tp.nama_tempat 
          FROM absensi_mingguan am
          JOIN tempat_pkl tp ON am.id_tempat = tp.id_tempat
          WHERE am.id_pembimbing = :id_pembimbing";
$params = [':id_pembimbing' => $id_pembimbing];

if ($id_tempat !== 'all' && !empty($id_tempat)) {
    $query .= " AND am.id_tempat = :id_tempat";
    $params[':id_tempat'] = $id_tempat;
}

if ($bulan !== 'all' && !empty($bulan)) {
    $query .= " AND DATE_FORMAT(am.tanggal_monitoring, '%Y-%m') = :bulan";
    $params[':bulan'] = $bulan;
}

$query .= " ORDER BY am.tanggal_monitoring ASC, am.minggu_ke ASC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$data_riwayat = $stmt->fetchAll(PDO::FETCH_ASSOC);

$filename_clean = 'Laporan_Monitoring_PKL_' . preg_replace('/[^A-Za-z0-9_]/', '_', $label_tempat) . '_' . date('Ymd_His');

// ==========================================
// A. EXPORT EXCEL
// ==========================================
if ($format === 'excel') {
    header("Content-Type: application/vnd.ms-excel; charset=utf-8");
    header("Content-Disposition: attachment; filename=" . $filename_clean . ".xls");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Pragma: public");
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <style>
            table { border-collapse: collapse; width: 100%; }
            th, td { border: 1px solid #333333; padding: 8px; vertical-align: top; }
            th { background-color: #2b6cb0; color: #ffffff; text-align: center; }
            .header-info td { border: none; padding: 4px; font-weight: bold; }
        </style>
    </head>
    <body>
        <h2>LAPORAN MONITORING MINGGUAN PKL</h2>
        <table class="header-info" style="margin-bottom: 15px;">
            <tr><td>Pembimbing</td><td>: <?= htmlspecialchars($nama_pembimbing) ?></td></tr>
            <tr><td>Tempat PKL</td><td>: <?= htmlspecialchars($label_tempat) ?></td></tr>
            <tr><td>Periode Bulan</td><td>: <?= htmlspecialchars($label_bulan) ?></td></tr>
            <tr><td>Waktu Unduh</td><td>: <?= date('d-m-Y H:i') ?> WIB</td></tr>
        </table>
        <table>
            <thead>
                <tr>
                    <th style="width: 40px;">No</th>
                    <th>Tanggal Monitoring</th>
                    <th>Minggu Ke</th>
                    <th>Tempat PKL</th>
                    <th>Platform</th>
                    <th>Catatan Perkembangan</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($data_riwayat)): ?>
                    <tr><td colspan="6" style="text-align: center; font-style: italic;">Tidak ada data monitoring untuk filter yang dipilih.</td></tr>
                <?php else: ?>
                    <?php $no = 1; foreach ($data_riwayat as $row): 
                        $platform_str = $row['platform'];
                        if ($platform_str === 'Lainnya' && !empty($row['platform_lainnya'])) {
                            $platform_str = $row['platform_lainnya'] . ' (Lainnya)';
                        }
                    ?>
                    <tr>
                        <td style="text-align: center;"><?= $no++ ?></td>
                        <td style="text-align: center;"><?= date('d-m-Y', strtotime($row['tanggal_monitoring'])) ?></td>
                        <td style="text-align: center;">Minggu ke-<?= htmlspecialchars($row['minggu_ke']) ?></td>
                        <td><?= htmlspecialchars($row['nama_tempat']) ?></td>
                        <td><?= htmlspecialchars($platform_str) ?></td>
                        <td><?= nl2br(htmlspecialchars($row['catatan'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </body>
    </html>
    <?php
    exit;
}

// ==========================================
// B. EXPORT PDF (DOMPDF)
// ==========================================

// Autoload composer
$autoload_found = false;
if (file_exists(__DIR__ . '/../siprala/vendor/autoload.php')) {
    require_once __DIR__ . '/../siprala/vendor/autoload.php';
    $autoload_found = true;
} elseif (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
    $autoload_found = true;
}

// Helper image to base64
function toBase64($relPath) {
    if (empty($relPath)) return '';
    $fullPath = realpath(__DIR__ . '/../' . $relPath);
    if (!$fullPath || !file_exists($fullPath)) return '';
    $data = @file_get_contents($fullPath);
    if ($data === false) return '';
    $info = @getimagesize($fullPath);
    $mime = $info ? $info['mime'] : 'image/jpeg';
    return 'data:' . $mime . ';base64,' . base64_encode($data);
}

// Render HTML template untuk PDF
ob_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Monitoring PKL</title>
    <style>
        @page {
            margin: 1.2cm 1.5cm;
        }
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            font-size: 10pt;
            color: #2d3748;
            line-height: 1.4;
        }
        .header-title {
            text-align: center;
            border-bottom: 2px solid #2b6cb0;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        .header-title h2 {
            margin: 0;
            font-size: 14pt;
            color: #1a365d;
            text-transform: uppercase;
        }
        .header-title p {
            margin: 3px 0 0 0;
            font-size: 9pt;
            color: #718096;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 15px;
            font-size: 9.5pt;
        }
        .meta-table td {
            padding: 2px 0;
            vertical-align: top;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        .data-table th, .data-table td {
            border: 1px solid #cbd5e0;
            padding: 6px 8px;
            vertical-align: top;
            font-size: 9pt;
        }
        .data-table th {
            background-color: #ebf8ff;
            color: #2b6cb0;
            text-align: center;
            font-weight: bold;
        }
        .bukti-img {
            max-width: 100px;
            max-height: 75px;
            border-radius: 4px;
            border: 1px solid #e2e8f0;
        }
        .footer-ttd {
            margin-top: 30px;
            width: 100%;
        }
        .badge-minggu {
            background-color: #3182ce;
            color: white;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 8pt;
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <div class="header-title">
        <h2>LAPORAN MONITORING MINGGUAN PKL</h2>
        <p>SMK INFORMATIKA SUMEDANG</p>
    </div>

    <table class="meta-table">
        <tr>
            <td style="width: 18%;"><strong>Pembimbing</strong></td>
            <td style="width: 32%;">: <?= htmlspecialchars($nama_pembimbing) ?></td>
            <td style="width: 18%;"><strong>Periode Bulan</strong></td>
            <td style="width: 32%;">: <?= htmlspecialchars($label_bulan) ?></td>
        </tr>
        <tr>
            <td><strong>Tempat PKL</strong></td>
            <td>: <?= htmlspecialchars($label_tempat) ?></td>
            <td><strong>Dicetak Pada</strong></td>
            <td>: <?= date('d-m-Y H:i') ?> WIB</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="width: 75px;">Tanggal</th>
                <th style="width: 60px;">Minggu</th>
                <?php if ($id_tempat === 'all'): ?>
                    <th>Tempat PKL</th>
                <?php endif; ?>
                <th style="width: 80px;">Platform</th>
                <th>Catatan Perkembangan</th>
                <th style="width: 105px; text-align: center;">Dokumentasi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($data_riwayat)): ?>
                <tr>
                    <td colspan="<?= ($id_tempat === 'all') ? '7' : '6' ?>" style="text-align: center; padding: 20px; font-style: italic; color: #a0aec0;">
                        Tidak ada catatan monitoring mingguan untuk filter yang dipilih.
                    </td>
                </tr>
            <?php else: ?>
                <?php $no = 1; foreach ($data_riwayat as $item): 
                    $p_str = $item['platform'];
                    if ($p_str === 'Lainnya' && !empty($item['platform_lainnya'])) {
                        $p_str = $item['platform_lainnya'];
                    }
                    $base64_img = toBase64($item['foto_bukti']);
                ?>
                <tr>
                    <td style="text-align: center;"><?= $no++ ?></td>
                    <td style="text-align: center;"><?= date('d/m/Y', strtotime($item['tanggal_monitoring'])) ?></td>
                    <td style="text-align: center;">
                        <span class="badge-minggu">Ke-<?= htmlspecialchars($item['minggu_ke']) ?></span>
                    </td>
                    <?php if ($id_tempat === 'all'): ?>
                        <td><?= htmlspecialchars($item['nama_tempat']) ?></td>
                    <?php endif; ?>
                    <td><?= htmlspecialchars($p_str) ?></td>
                    <td><?= nl2br(htmlspecialchars($item['catatan'])) ?></td>
                    <td style="text-align: center;">
                        <?php if (!empty($base64_img)): ?>
                            <img src="<?= $base64_img ?>" class="bukti-img" alt="Foto">
                        <?php else: ?>
                            <span style="font-size: 8pt; color: #a0aec0;">-</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <table class="footer-ttd">
        <tr>
            <td style="width: 60%;"></td>
            <td style="width: 40%; text-align: center;">
                <p style="margin-bottom: 60px;">Sumedang, <?= date('d F Y') ?><br>Pembimbing PKL,</p>
                <p style="margin: 0; font-weight: bold; text-decoration: underline;"><?= htmlspecialchars($nama_pembimbing) ?></p>
            </td>
        </tr>
    </table>
</body>
</html>
<?php
$html = ob_get_clean();

if ($autoload_found && class_exists('Dompdf\Dompdf')) {
    $options = new \Dompdf\Options();
    $options->set('defaultFont', 'Helvetica');
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', false);

    $dompdf = new \Dompdf\Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream($filename_clean . ".pdf", ["Attachment" => 0]); // 0 = view/print di tab browser
    exit;
} else {
    // Fallback: Tampilkan halaman cetak HTML interaktif
    echo $html;
    echo "<script>window.print();</script>";
    exit;
}
