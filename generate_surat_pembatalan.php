<?php
// generate_surat_pembatalan.php
require 'vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

include 'koneksi.php';

$base_path = __DIR__;

function imageToBase64($imagePath)
{
    if (!file_exists($imagePath)) {
        return ''; // Or a placeholder
    }
    $imageData = file_get_contents($imagePath);
    $imageInfo = getimagesize($imagePath);
    $mimeType = $imageInfo['mime'];
    return 'data:' . $mimeType . ';base64,' . base64_encode($imageData);
}

// Data sekolah (diambil dari settings)
$query_settings = "SELECT * FROM settings LIMIT 1";
$res_settings = $koneksi->query($query_settings);
$settings = $res_settings->fetch_assoc();

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

// Data Pengajuan
$data_pengajuan = [
    'nomor_surat' => $_POST['nomor_surat'] ?? '',
    'tanggal_surat' => date('d F Y', strtotime($_POST['tanggal_surat'])),
    'perihal' => $_POST['perihal'] ?? 'Pemberitahuan Pembatalan Siswa PKL',
    'no_surat_referensi' => $_POST['no_surat_referensi'] ?? '',
    'tanggal_surat_referensi' => date('d F Y', strtotime($_POST['tanggal_surat_referensi'] ?? date('Y-m-d')))
];

// Data Perusahaan
$nama_perusahaan = $_POST['nama_perusahaan'] ?? '';
$id_tempat_pkl = $_POST['id_tempat_pkl'] ?? 0;

// Ambil info lengkap perusahaan dari DB jika perlu, atau gunakan yang dari form
$query_tempat = "SELECT * FROM tempat_pkl WHERE id_tempat = $id_tempat_pkl";
$res_tempat = $koneksi->query($query_tempat);
$data_t = $res_tempat->fetch_assoc();

$data_perusahaan = [
    'tujuan' => $nama_perusahaan,
    'yth' => 'Pimpinan',
    'alamat_tujuan' => $_POST['alamat_perusahaan'] ?? 'Alamat Perusahaan',
    'kota_tujuan' => $_POST['kota_perusahaan'] ?? 'Kota',
];

// Data Siswa yang dibatalkan
$siswa_batal_ids = $_POST['siswa_batal'] ?? [];
$siswa_details = $_POST['siswa_detail'] ?? [];
$data_siswa = [];

foreach ($siswa_batal_ids as $id_siswa) {
    if (isset($siswa_details[$id_siswa])) {
        $data_siswa[] = [
            'nama' => $siswa_details[$id_siswa]['nama'],
            'kelas' => $siswa_details[$id_siswa]['kelas'],
            'nis' => $siswa_details[$id_siswa]['nis']
        ];
    }
}

// Simpan surat pembatalan ke database
$stmt = $koneksi->prepare("INSERT INTO surat (no_surat, perihal, id_tempat_pkl, tanggal) VALUES (?, ?, ?, ?)");
$tgl_surat_db = $_POST['tanggal_surat'];
$stmt->bind_param("ssis", $data_pengajuan['nomor_surat'], $data_pengajuan['perihal'], $id_tempat_pkl, $tgl_surat_db);
$stmt->execute();
$id_surat_baru = $koneksi->insert_id;
$stmt->close();

// Simpan detail siswa ke siswa_surat (untuk riwayat surat pembatalan)
$stmt_ss = $koneksi->prepare("INSERT INTO siswa_surat (id_siswa, id_surat) VALUES (?, ?)");
foreach ($siswa_batal_ids as $id_siswa) {
    $stmt_ss->bind_param("ii", $id_siswa, $id_surat_baru);
    $stmt_ss->execute();
}
$stmt_ss->close();

// --- LOGIKA BARU: Hapus siswa dari surat referensi sebelumnya & Reset status tempat ---
$id_surat_ref = isset($_POST['id_surat_ref']) ? intval($_POST['id_surat_ref']) : 0;
if ($id_surat_ref > 0) {
    // 1. Hapus data siswa tersebut dari surat pengantar sebelumnya agar tidak terdaftar lagi
    $stmt_del = $koneksi->prepare("DELETE FROM siswa_surat WHERE id_siswa = ?");

    // 2. Reset id_tempat di tabel siswa agar mereka bisa dipilih lagi untuk surat baru
    $stmt_upd = $koneksi->prepare("UPDATE siswa SET id_tempat = 0 WHERE id_siswa = ?");

    foreach ($siswa_batal_ids as $id_siswa) {
        $id_siswa_int = intval($id_siswa);

        // Hapus dari riwayat surat pengantar sebelumnya
        $stmt_del->bind_param("i", $id_siswa_int);
        $stmt_del->execute();

        // Reset status tempat di tabel master siswa
        $stmt_upd->bind_param("i", $id_siswa_int);
        $stmt_upd->execute();
    }
    $stmt_del->close();
    $stmt_upd->close();
}

$koneksi->close();

// Generate PDF
$options = new Options();
$options->set('defaultFont', 'Times New Roman');
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);

ob_start();
include 'template_surat_pembatalan.php';
$html = ob_get_clean();

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$filename = "Surat_Pembatalan_PKL_" . date('Ymd') . "_" . str_replace(' ', '_', $nama_perusahaan) . ".pdf";
$dompdf->stream($filename, ["Attachment" => 0]);
