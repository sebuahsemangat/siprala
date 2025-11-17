<?php
// generate_surat.php - VERSI PERBAIKAN
// PASTIKAN TIDAK ADA SPASI/ENTER SEBELUM <?php

// 1. Load Composer Autoload
require 'vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

// **DEFINISIKAN BASE PATH ABSOLUT**
$base_path = __DIR__;

// **FUNGSI UNTUK CONVERT GAMBAR KE BASE64**
function imageToBase64($imagePath) {
    if (!file_exists($imagePath)) {
        error_log("File tidak ditemukan: " . $imagePath);
        return false;
    }
    
    $imageData = file_get_contents($imagePath);
    if ($imageData === false) {
        error_log("Gagal membaca file: " . $imagePath);
        return false;
    }
    
    $imageInfo = getimagesize($imagePath);
    $mimeType = $imageInfo['mime'];
    
    return 'data:' . $mimeType . ';base64,' . base64_encode($imageData);
}

// --- DATA DINAMIS DARI INPUT PENGGUNA/DATABASE ---
$data_sekolah = [
    'nama_sekolah' => 'Sekolah Menengah Kejuruan (SMK) INFORMATIKA SUMEDANG',
    'alamat_sekolah' => 'Jalan Angkrek Situ No.19 Sumedang 45323',
    'telp_sekolah' => '(0261) 202767',
    'email_sekolah' => 'info@smkifsu.sch.id',
    'website_sekolah' => 'www.smkifsu.sch.id',
    'kepala_sekolah' => 'Tatang Suryana, S.Ag., M.Pd',
    // Convert ke Base64 untuk kompatibilitas maksimal
    // 'logo_smk' => imageToBase64($base_path . '/logo_ifsu.png'), 
    // 'logo_yps' => imageToBase64($base_path . '/logo_yps.png'),
    'kop' => imageToBase64($base_path . '/kop.jpg'),
];

// Validasi logo
// if ($data_sekolah['logo_smk'] === false || $data_sekolah['logo_yps'] === false) {
//     die("ERROR: File logo tidak ditemukan. Pastikan logo_ifsu.png dan logo_yps.png ada di folder yang sama dengan script ini.");
// }

$data_pengajuan = [
    'nomor_surat' => '104/PAN-PKL/SMK-IF/YPS/X/2025',
    'lampiran' => '1 Lampiran',
    'perihal' => 'Praktik Kerja Lapangan (PKL)',
    'tanggal_surat' => '06 Oktober 2025',
    'tanggal_mulai_pkl' => '01 Desember 2025',
    'tanggal_selesai_pkl' => '31 Maret 2026',
];

$data_perusahaan = [
    'yth_tujuan' => 'Yth. HRD PT. Sawala Inovasi Indonesia',
    'alamat_tujuan' => 'Jl.R.A Kartini No.28, Regol Wetan, Sumedang Selatan 45311',
    'kota_tujuan' => 'Sumedang',
];

$data_siswa = [
    ['nama' => 'Muhammad Aprizal Sanjaya', 'kelas' => 'XII-RPL 1', 'hp' => '081313011934'],
    ['nama' => 'Ripan Nugraha', 'kelas' => 'XII-RPL 1', 'hp' => '085861556201'],
];

// 2. Set Dompdf Options
$options = new Options();
$options->set('defaultFont', 'Arial');
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
// MATIKAN DEBUG - INI YANG MENYEBABKAN OUTPUT!
$options->set('debugPng', false);
$options->set('debugKeepTemp', false);
$options->set('debugCss', false);

$dompdf = new Dompdf($options);

// 3. Tangkap Output HTML dari template 
ob_start();
include 'template_surat.php'; 
$html = ob_get_clean();

// 4. Load HTML, Atur Kertas, dan Render
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// 5. Output PDF
$filename = "Surat_PKL_" . date('Ymd') . ".pdf";
$dompdf->stream($filename, ["Attachment" => 1]);

exit(0);