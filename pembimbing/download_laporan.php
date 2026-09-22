<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../koneksi.php';

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

// Format nama bulan untuk judul & identitas
$bulan_indo = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];

function formatTanggalIndo($tanggal) {
    if (empty($tanggal) || $tanggal === '0000-00-00') return '-';
    $bulan_map = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    $ts = strtotime($tanggal);
    if (!$ts) return $tanggal;
    $d = date('j', $ts);
    $m = (int)date('n', $ts);
    $y = date('Y', $ts);
    return $d . ' ' . ($bulan_map[$m] ?? '') . ' ' . $y;
}

$label_bulan = "Semua Periode";
if ($bulan !== 'all' && preg_match('/^(\d{4})-(\d{2})$/', $bulan, $m)) {
    $thn = $m[1];
    $bln_num = (int)$m[2];
    $label_bulan = ($bulan_indo[$bln_num] ?? 'Bulan ' . $bln_num) . ' ' . $thn;
}

$label_tempat = "Semua Tempat PKL";
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

$filename_clean = 'Laporan_Perjalanan_Dinas_' . preg_replace('/[^A-Za-z0-9_]/', '_', $label_tempat) . '_' . date('Ymd_His');
$tanggal_cetak = date('j') . ' ' . ($bulan_indo[(int)date('n')] ?? '') . ' ' . date('Y');

// Helper image to base64
function toBase64($relPath) {
    if (empty($relPath)) return '';
    $cleanPath = ltrim(str_replace('../', '', $relPath), '/\\');
    $fullPath = realpath(__DIR__ . '/../' . $cleanPath);
    if (!$fullPath || !file_exists($fullPath)) {
        $altPath = realpath(__DIR__ . '/' . $relPath);
        if ($altPath && file_exists($altPath)) {
            $fullPath = $altPath;
        } else {
            return '';
        }
    }
    $data = @file_get_contents($fullPath);
    if ($data === false) return '';
    $info = @getimagesize($fullPath);
    $mime = $info ? $info['mime'] : 'image/jpeg';
    return 'data:' . $mime . ';base64,' . base64_encode($data);
}

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
            body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; }
            .title { font-size: 14pt; font-weight: bold; text-align: center; margin-bottom: 20px; }
            .identitas-table { border-collapse: collapse; margin-bottom: 20px; font-size: 12pt; }
            .identitas-table td { border: none; padding: 4px 6px; }
            .data-table { border-collapse: collapse; width: 100%; margin-top: 15px; font-size: 12pt; }
            .data-table th, .data-table td { border: 1px solid #000; padding: 8px; vertical-align: top; font-size: 12pt; }
            .data-table th { background-color: #f2f2f2; text-align: center; font-weight: bold; }
            .ttd-table { border-collapse: collapse; width: 100%; margin-top: 35px; font-size: 12pt; }
            .ttd-table td { border: none; text-align: center; }
        </style>
    </head>
    <body>
        <div class="title">LAPORAN PERJALANAN DINAS</div>
        <table class="identitas-table">
            <tr><td>1.</td><td>Dasar</td><td>:</td><td>Surat Tugas Pembimbing PKL Tahun 2027</td></tr>
            <tr><td>2.</td><td>Nama Petugas</td><td>:</td><td><?= htmlspecialchars($nama_pembimbing) ?></td></tr>
            <tr><td>3.</td><td>Unit Kerja</td><td>:</td><td>SMK Informatika Sumedang</td></tr>
            <tr><td>4.</td><td>Bulan</td><td>:</td><td><?= htmlspecialchars($label_bulan) ?></td></tr>
            <tr><td>5.</td><td>Kegiatan</td><td>:</td><td>Monitoring &amp; Bimbingan Praktik Kerja Lapangan</td></tr>
            <tr><td>6.</td><td>Tempat PKL</td><td>:</td><td><?= htmlspecialchars($label_tempat) ?></td></tr>
            <tr><td>7.</td><td>Hasil</td><td>:</td><td></td></tr>
        </table>

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 40px;">No</th>
                    <th style="width: 100px;">Minggu ke</th>
                    <th style="width: 120px;">Tanggal</th>
                    <th style="width: 130px;">Platform</th>
                    <th>Catatan Bimbingan</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($data_riwayat)): ?>
                    <tr><td colspan="5" style="text-align: center; font-style: italic;">Tidak ada data absensi bimbingan untuk periode yang dipilih.</td></tr>
                <?php else: ?>
                    <?php $no = 1; foreach ($data_riwayat as $row): 
                        $platform_str = $row['platform'];
                        if ($platform_str === 'Lainnya' && !empty($row['platform_lainnya'])) {
                            $platform_str = $row['platform_lainnya'];
                        }
                    ?>
                    <tr>
                        <td style="text-align: center;"><?= $no++ ?></td>
                        <td style="text-align: center;"><?= htmlspecialchars($row['minggu_ke']) ?></td>
                        <td style="text-align: center;"><?= date('d/m/Y', strtotime($row['tanggal_monitoring'])) ?></td>
                        <td style="text-align: center;"><?= htmlspecialchars($platform_str) ?></td>
                        <td><?= nl2br(htmlspecialchars($row['catatan'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <table class="ttd-table">
            <tr>
                <td style="width: 60%;"></td>
                <td style="width: 40%;">
                    <p style="margin: 0 0 5px 0;">Sumedang, <?= $tanggal_cetak ?></p>
                    <p style="margin: 0 0 60px 0;">Petugas,</p>
                    <p style="margin: 0;"><strong><?= htmlspecialchars($nama_pembimbing) ?></strong></p>
                </td>
            </tr>
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

// Siapkan data foto bukti bimbingan
$daftar_foto = [];
foreach ($data_riwayat as $item) {
    if (!empty($item['foto_bukti'])) {
        $b64 = toBase64($item['foto_bukti']);
        if (!empty($b64)) {
            $daftar_foto[] = [
                'foto' => $b64,
                'nama_tempat' => $item['nama_tempat'],
                'minggu_ke' => $item['minggu_ke'],
                'tanggal' => $item['tanggal_monitoring']
            ];
        }
    }
}

// Render HTML template untuk PDF
ob_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Perjalanan Dinas</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 1.5cm 2cm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            color: #000000;
            line-height: 1.35;
        }
        .header-title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 25px;
            text-transform: uppercase;
        }
        .identitas-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 12pt;
        }
        .identitas-table td {
            border: none;
            padding: 3px 0;
            vertical-align: top;
            font-size: 12pt;
        }
        .identitas-table .col-no {
            width: 25px;
        }
        .identitas-table .col-label {
            width: 140px;
        }
        .identitas-table .col-sep {
            width: 15px;
            text-align: center;
        }
        .identitas-table .col-val {
            /* Nilai teks identitas */
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 12pt;
        }
        .data-table th, .data-table td {
            border: 1px solid #000000;
            padding: 6px 8px;
            vertical-align: top;
            font-size: 12pt;
        }
        .data-table th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }
        .ttd-table {
            width: 100%;
            margin-top: 35px;
            border: none;
            border-collapse: collapse;
            page-break-inside: avoid;
        }
        .ttd-table td {
            border: none;
            vertical-align: top;
            font-size: 12pt;
        }
        .page-break {
            page-break-before: always;
        }
        .title-halaman-2 {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 25px;
        }
        .foto-box {
            text-align: center;
            page-break-inside: avoid;
        }
        .foto-box-single {
            margin-bottom: 30px;
        }
        .foto-box-double {
            margin-bottom: 22px;
        }
        .foto-wrapper {
            text-align: center;
            margin-bottom: 8px;
        }
        .img-single {
            max-width: 90%;
            max-height: 380px;
            border: 1px solid #cccccc;
            border-radius: 3px;
        }
        .img-double {
            max-width: 85%;
            max-height: 220px;
            border: 1px solid #cccccc;
            border-radius: 3px;
        }
        .foto-caption {
            width: 85%;
            margin: 0 auto;
            border-collapse: collapse;
            text-align: left;
            font-size: 12pt;
        }
        .foto-caption td {
            border: none;
            padding: 2px 4px;
            vertical-align: top;
            font-size: 12pt;
        }
        .foto-caption .caption-label {
            width: 170px;
        }
        .foto-caption .caption-sep {
            width: 15px;
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- HALAMAN 1: LAPORAN PERJALANAN DINAS -->
    <div class="header-title">
        LAPORAN PERJALANAN DINAS
    </div>

    <!-- Tabel Identitas (Tanpa Border, Kolom Paling Kiri Nomor Urut) -->
    <table class="identitas-table">
        <tr>
            <td class="col-no">1.</td>
            <td class="col-label">Dasar</td>
            <td class="col-sep">:</td>
            <td class="col-val">Surat Tugas Pembimbing PKL Tahun 2027</td>
        </tr>
        <tr>
            <td class="col-no">2.</td>
            <td class="col-label">Nama Petugas</td>
            <td class="col-sep">:</td>
            <td class="col-val"><?= htmlspecialchars($nama_pembimbing) ?></td>
        </tr>
        <tr>
            <td class="col-no">3.</td>
            <td class="col-label">Unit Kerja</td>
            <td class="col-sep">:</td>
            <td class="col-val">SMK Informatika Sumedang</td>
        </tr>
        <tr>
            <td class="col-no">4.</td>
            <td class="col-label">Bulan</td>
            <td class="col-sep">:</td>
            <td class="col-val"><?= htmlspecialchars($label_bulan) ?></td>
        </tr>
        <tr>
            <td class="col-no">5.</td>
            <td class="col-label">Kegiatan</td>
            <td class="col-sep">:</td>
            <td class="col-val">Monitoring &amp; Bimbingan Praktik Kerja Lapangan</td>
        </tr>
        <tr>
            <td class="col-no">6.</td>
            <td class="col-label">Tempat PKL</td>
            <td class="col-sep">:</td>
            <td class="col-val"><?= htmlspecialchars($label_tempat) ?></td>
        </tr>
        <tr>
            <td class="col-no">7.</td>
            <td class="col-label">Hasil</td>
            <td class="col-sep">:</td>
            <td class="col-val"></td>
        </tr>
    </table>

    <!-- Tabel Hasil Absensi Mingguan -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 35px; text-align: center;">No</th>
                <th style="width: 85px; text-align: center;">Minggu ke</th>
                <th style="width: 100px; text-align: center;">Tanggal</th>
                <th style="width: 105px; text-align: center;">Platform</th>
                <th>Catatan Bimbingan</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($data_riwayat)): ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 20px; font-style: italic;">
                        Tidak ada data absensi bimbingan untuk periode yang dipilih.
                    </td>
                </tr>
            <?php else: ?>
                <?php $no = 1; foreach ($data_riwayat as $item): 
                    $p_str = $item['platform'];
                    if ($p_str === 'Lainnya' && !empty($item['platform_lainnya'])) {
                        $p_str = $item['platform_lainnya'];
                    }
                ?>
                <tr>
                    <td style="text-align: center;"><?= $no++ ?></td>
                    <td style="text-align: center;"><?= htmlspecialchars($item['minggu_ke']) ?></td>
                    <td style="text-align: center;"><?= date('d/m/Y', strtotime($item['tanggal_monitoring'])) ?></td>
                    <td style="text-align: center;"><?= htmlspecialchars($p_str) ?></td>
                    <td><?= nl2br(htmlspecialchars($item['catatan'])) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Tanda Tangan Bagian Kanan -->
    <table class="ttd-table">
        <tr>
            <td style="width: 55%;"></td>
            <td style="width: 45%; text-align: center;">
                <p style="margin: 0 0 5px 0;">Sumedang, <?= $tanggal_cetak ?></p>
                <p style="margin: 0 0 70px 0;">Petugas,</p>
                <p style="margin: 0;"><strong><?= htmlspecialchars($nama_pembimbing) ?></strong></p>
            </td>
        </tr>
    </table>

    <!-- HALAMAN KEDUA (DAN SETERUSNYA): FOTO KEGIATAN -->
    <?php if (empty($daftar_foto)): ?>
        <div class="page-break"></div>
        <div class="title-halaman-2">Foto Kegiatan</div>
        <p style="text-align: center; margin-top: 40px; font-style: italic; font-size: 12pt; color: #555;">
            Tidak ada bukti foto bimbingan yang diunggah untuk periode ini.
        </p>
    <?php else: ?>
        <?php 
        $foto_pages = array_chunk($daftar_foto, 2);
        foreach ($foto_pages as $page_idx => $fotos_in_page):
            $is_single = (count($fotos_in_page) === 1);
        ?>
        <div class="page-break"></div>
        <div class="title-halaman-2">Foto Kegiatan</div>

        <?php foreach ($fotos_in_page as $f): ?>
            <div class="foto-box <?= $is_single ? 'foto-box-single' : 'foto-box-double' ?>">
                <div class="foto-wrapper">
                    <img src="<?= $f['foto'] ?>" class="<?= $is_single ? 'img-single' : 'img-double' ?>" alt="Bukti Foto">
                </div>
                <table class="foto-caption">
                    <tr>
                        <td class="caption-label">Nama Tempat PKL</td>
                        <td class="caption-sep">:</td>
                        <td><?= htmlspecialchars($f['nama_tempat']) ?></td>
                    </tr>
                    <tr>
                        <td class="caption-label">Minggu ke</td>
                        <td class="caption-sep">:</td>
                        <td><?= htmlspecialchars($f['minggu_ke']) ?></td>
                    </tr>
                    <tr>
                        <td class="caption-label">Tanggal Bimbingan</td>
                        <td class="caption-sep">:</td>
                        <td><?= formatTanggalIndo($f['tanggal']) ?></td>
                    </tr>
                </table>
            </div>
        <?php endforeach; ?>
        <?php endforeach; ?>
    <?php endif; ?>

</body>
</html>
<?php
$html = ob_get_clean();

if ($autoload_found && class_exists('Dompdf\Dompdf')) {
    $options = new \Dompdf\Options();
    $options->set('defaultFont', 'Times-Roman');
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
