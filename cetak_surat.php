<?php
// cetak_surat.php - Handler untuk melihat dan mencetak ulang surat
session_start();

// Cek autentikasi
if (!isset($_SESSION['admin_id'])) {
    die("Akses ditolak. Silakan login terlebih dahulu.");
}

$id_surat = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id_surat <= 0) {
    die("ID Surat tidak valid.");
}

$arsip_dir = __DIR__ . '/arsip_surat';
$file_arsip = $arsip_dir . '/surat_' . $id_surat . '.pdf';

// 1. JIKA FILE ARSIP SUDAH ADA, LANGSUNG TAMPILKAN INLINE DI BROWSER
if (file_exists($file_arsip)) {
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="Surat_' . $id_surat . '.pdf"');
    header('Content-Length: ' . filesize($file_arsip));
    header('Cache-Control: private, max-age=0, must-revalidate');
    header('Pragma: public');
    readfile($file_arsip);
    exit(0);
}

// 2. JIKA FILE ARSIP BELUM ADA (SURAT LAMA), REGENERATE OTOMATIS DARI DATABASE
require 'vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

include 'koneksi.php';
$base_path = __DIR__;

function imageToBase64($imagePath)
{
    if (!file_exists($imagePath)) {
        return '';
    }
    $imageData = file_get_contents($imagePath);
    $imageInfo = getimagesize($imagePath);
    $mimeType = $imageInfo['mime'];
    return 'data:' . $mimeType . ';base64,' . base64_encode($imageData);
}

// Ambil data surat & tempat PKL
$stmt = $koneksi->prepare("
    SELECT s.id_surat, s.no_surat, s.perihal, s.tanggal, s.id_tempat_pkl,
           t.nama_tempat, t.alamat, t.kota
    FROM surat s
    LEFT JOIN tempat_pkl t ON s.id_tempat_pkl = t.id_tempat
    WHERE s.id_surat = ?
    LIMIT 1
");
$stmt->bind_param("i", $id_surat);
$stmt->execute();
$surat_res = $stmt->get_result();
$surat_data = $surat_res->fetch_assoc();
$stmt->close();

if (!$surat_data) {
    $koneksi->close();
    die("Data surat tidak ditemukan di database.");
}

// Ambil settings sekolah
$res_settings = $koneksi->query("SELECT * FROM settings LIMIT 1");
$settings = $res_settings ? $res_settings->fetch_assoc() : [];

$data_sekolah = [
    'nama_sekolah' => $settings['nama_sekolah'] ?? 'SMK Informatika Sumedang',
    'alamat_sekolah' => 'Jalan Angkrek Situ No.19 Sumedang 45323',
    'telp_sekolah' => '(0261) 202767',
    'email_sekolah' => 'info@smkifsu.sch.id',
    'website_sekolah' => 'www.smkifsu.sch.id',
    'kepala_sekolah' => $settings['nama_kepsek'] ?? 'Nama Kepala Sekolah',
    'kop' => imageToBase64($base_path . '/img/kop.jpg'),
    'ttd' => imageToBase64($base_path . '/img/ttd.png')
];

$tgl_surat_format = !empty($surat_data['tanggal']) ? date('d F Y', strtotime($surat_data['tanggal'])) : date('d F Y');
$data_pengajuan = [
    'nomor_surat' => $surat_data['no_surat'] ?? '',
    'lampiran' => '1 Lampiran',
    'perihal' => $surat_data['perihal'] ?? '',
    'tanggal_surat' => $tgl_surat_format,
    'tanggal_mulai_pkl' => $settings['tgl_mulai'] ?? '',
    'tanggal_selesai_pkl' => $settings['tgl_selesai'] ?? '',
    'no_surat_referensi' => '',
    'tanggal_surat_referensi' => $tgl_surat_format
];

$nama_tempat = $surat_data['nama_tempat'] ?? 'Perusahaan';
$data_perusahaan = [
    'yth' => 'Yth. Pimpinan',
    'tujuan' => $nama_tempat,
    'alamat_tujuan' => $surat_data['alamat'] ?? 'Alamat Tempat PKL',
    'kota_tujuan' => $surat_data['kota'] ?? 'Sumedang'
];

// Ambil daftar siswa yang terhubung dengan surat ini
$stmt_siswa = $koneksi->prepare("
    SELECT s.id_siswa, s.nis, s.nama_siswa, s.kelas, s.kontak_siswa
    FROM siswa_surat ss
    JOIN siswa s ON ss.id_siswa = s.id_siswa
    WHERE ss.id_surat = ?
");
$stmt_siswa->bind_param("i", $id_surat);
$stmt_siswa->execute();
$res_siswa = $stmt_siswa->get_result();

$data_siswa = [];
while ($row_s = $res_siswa->fetch_assoc()) {
    $data_siswa[] = [
        'nama' => htmlspecialchars($row_s['nama_siswa']),
        'kelas' => htmlspecialchars($row_s['kelas']),
        'hp' => htmlspecialchars($row_s['kontak_siswa']),
        'nis' => htmlspecialchars($row_s['nis'])
    ];
}
$stmt_siswa->close();
$koneksi->close();

// Setup Dompdf
$options = new Options();
$options->set('defaultFont', 'Calibri');
$options->set('defaultFontSize', 12);
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);

ob_start();
if ($data_pengajuan['perihal'] == "Pengajuan Tempat Praktik Kerja Lapangan (PKL)") {
    include 'template_surat.php';
} else if ($data_pengajuan['perihal'] == "Penambahan Siswa Praktik Kerja Lapangan (PKL)") {
    include 'template_surat_penambahan.php';
} else if (stripos($data_pengajuan['perihal'], 'Pembatalan') !== false) {
    include 'template_surat_pembatalan.php';
} else {
    include 'template_surat.php';
}
$html = ob_get_clean();

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Simpan hasil render ke folder arsip agar pemanggilan berikutnya langsung mengambil file fisik
if (!is_dir($arsip_dir)) {
    mkdir($arsip_dir, 0777, true);
}
file_put_contents($file_arsip, $dompdf->output());

// Tampilkan PDF di tab browser (inline preview)
$filename = "Surat_" . str_replace(['/', ' '], '_', $surat_data['no_surat']) . ".pdf";
$dompdf->stream($filename, ["Attachment" => 0]);
exit(0);
?>
