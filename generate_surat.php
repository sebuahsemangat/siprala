<?php
// generate_surat.php - FINAL DENGAN TRANSAKSI DATABASE LENGKAP

// 1. Load Composer Autoload & Library
require 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// --- KRITIS: INCLUDE KONEKSI DATABASE ---
include 'koneksi.php';

// **DEFINISIKAN BASE PATH ABSOLUT**
$base_path = __DIR__;

// **FUNGSI UNTUK CONVERT GAMBAR KE BASE64**
function imageToBase64($imagePath)
{
    if (!file_exists($imagePath)) {
        die("ERROR: File gambar tidak ditemukan di path: " . $imagePath);
    }
    $imageData = file_get_contents($imagePath);
    $imageInfo = getimagesize($imagePath);
    $mimeType = $imageInfo['mime'];
    return 'data:' . $mimeType . ';base64,' . base64_encode($imageData);
}


// --- DATA DINAMIS DARI FORM POST ---
// Data sekolah statis (diambil dari hidden input form yang datanya sudah dari DB)
$data_sekolah = [
    'nama_sekolah' => $_POST['nama_sekolah'] ?? 'NAMA SEKOLAH TIDAK DITEMUKAN',
    'alamat_sekolah' => 'Jalan Angkrek Situ No.19 Sumedang 45323', // Data tetap
    'telp_sekolah' => '(0261) 202767', // Data tetap
    'email_sekolah' => 'info@smkifsu.sch.id', // Data tetap
    'website_sekolah' => 'www.smkifsu.sch.id', // Data tetap
    'kepala_sekolah' => $_POST['nama_kepsek'] ?? 'NAMA KEPALA SEKOLAH TIDAK DITEMUKAN',
    'kop' => imageToBase64($base_path . '/img/kop.jpg'),
    'ttd' => imageToBase64($base_path . '/img/ttd.png')
];

// Data Pengajuan dari input dinamis
$tgl_mulai_db = $_POST['tgl_mulai'] ?? '2025-01-01';
$tgl_selesai_db = $_POST['tgl_selesai'] ?? '2025-01-01';
$tanggal_surat_db = $_POST['tanggal_surat'] ?? date('Y-m-d'); // YYYY-MM-DD
$no_surat_referensi = $_POST['no_surat_referensi'] ?? '';
$tanggal_surat_referensi = $_POST['tanggal_surat_referensi'] ?? '';

$data_pengajuan = [
    'nomor_surat' => $_POST['nomor_surat'] ?? '000/000/000',
    'lampiran' => '1 Lampiran',
    'perihal' => $_POST['perihal'],
    'tanggal_surat' => date('d F Y', strtotime($tanggal_surat_db)),
    'tanggal_mulai_pkl' => $tgl_mulai_db,
    'tanggal_selesai_pkl' => $tgl_selesai_db,
    'no_surat_referensi' => $no_surat_referensi,
    'tanggal_surat_referensi' => date('d F Y', strtotime($tanggal_surat_referensi))
];

// Data Perusahaan dari input dinamis
$nama_perusahaan_db = $_POST['nama_perusahaan'] ?? 'Perusahaan Tidak Diketahui';
$data_perusahaan = [
    'yth' => 'Yth. ' . ($_POST['tujuan_departemen'] ?? 'Pimpinan'),
    'tujuan' => $nama_perusahaan_db,
    'alamat_tujuan' => $_POST['alamat_perusahaan'] ?? 'Alamat Perusahaan',
    'kota_tujuan' => $_POST['kota_perusahaan'] ?? 'KOTA',
];

// Data Siswa (Diproses dari array dinamis)
// Structure: $_POST['siswa'][id_siswa][nama/kelas/hp]
$data_siswa_raw = $_POST['siswa'] ?? [];
$data_siswa = [];
$id_siswa_list = []; // List untuk INSERT ke siswa_surat

foreach ($data_siswa_raw as $id_siswa => $siswa) {
    if (is_array($siswa) && !empty($siswa['nama']) && !empty($siswa['kelas']) && !empty($siswa['hp'])) {
        $data_siswa[] = [
            'nama' => htmlspecialchars($siswa['nama']),
            'kelas' => htmlspecialchars($siswa['kelas']),
            'hp' => htmlspecialchars($siswa['hp']),
        ];
        // Tambahkan id_siswa ke list untuk INSERT ke DB
        $id_siswa_list[] = intval($id_siswa);
    }
}

if (empty($data_siswa)) {
    die("ERROR: Daftar siswa tidak boleh kosong.");
}


// =================================================================================
// --- TRANSAKSI DATABASE KRITIS ---
// =================================================================================
$id_tempat_pkl = null;
$id_surat = null;

try {
    // 1. Cek atau INSERT Tempat PKL Baru
    $stmt = $koneksi->prepare("SELECT id_tempat FROM tempat_pkl WHERE nama_tempat = ?");
    $stmt->bind_param("s", $nama_perusahaan_db);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Tempat PKL sudah ada
        $row = $result->fetch_assoc();
        $id_tempat_pkl = $row['id_tempat'];
    } else {
        // Tempat PKL BARU: INSERT dan dapatkan ID-nya
        $stmt_insert = $koneksi->prepare("INSERT INTO tempat_pkl (nama_tempat) VALUES (?)");
        $stmt_insert->bind_param("s", $nama_perusahaan_db);
        $stmt_insert->execute();
        $id_tempat_pkl = $koneksi->insert_id;
        $stmt_insert->close();
    }
    $stmt->close();

    if (is_null($id_tempat_pkl)) {
        throw new Exception("Gagal mendapatkan ID Tempat PKL.");
    }

    // 2. INSERT Data Surat ke tabel 'surat'
    $stmt = $koneksi->prepare("INSERT INTO surat (no_surat, perihal, id_tempat_pkl, tanggal) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $data_pengajuan['nomor_surat'], $data_pengajuan['perihal'], $id_tempat_pkl, $tanggal_surat_db);
    $stmt->execute();
    $id_surat = $koneksi->insert_id;
    $stmt->close();

    if (is_null($id_surat)) {
        throw new Exception("Gagal mendapatkan ID Surat yang baru dibuat.");
    }

    // 3. INSERT Data Siswa ke tabel 'siswa_surat'
    $stmt = $koneksi->prepare("INSERT INTO siswa_surat (id_siswa, id_surat) VALUES (?, ?)");
    foreach ($id_siswa_list as $id_siswa) {
        $stmt->bind_param("ii", $id_siswa, $id_surat);
        if (!$stmt->execute()) {
            // Jika gagal (misal duplikat), log error tapi jangan hentikan proses
            error_log("Gagal memasukkan siswa ID $id_siswa ke surat ID $id_surat: " . $stmt->error);
        }
    }
    $stmt->close();
} catch (Exception $e) {
    // Handle error (misalnya koneksi gagal, nomor surat duplikat)
    $koneksi->close();
    die("Transaksi Database Gagal: " . $e->getMessage() . " Silakan cek log error.");
}

// Tutup koneksi setelah semua transaksi selesai
$koneksi->close();


// =================================================================================
// --- LANJUT KE GENERATE PDF ---
// =================================================================================

// 4. Set Dompdf Options
$options = new Options();
$options->set('defaultFont', 'Times New Roman');
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);

// 5. Tangkap Output HTML dari template
ob_start();
if ($data_pengajuan['perihal'] == "Pengajuan Tempat Praktik Kerja Lapangan (PKL)") {
    include 'template_surat.php';
} else {
    include 'template_surat_penambahan.php';
}
$html = ob_get_clean();

// 6. Load HTML, Atur Kertas, dan Render
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// 7. Output PDF
if ($data_pengajuan['perihal'] == "Pengajuan Tempat Praktik Kerja Lapangan (PKL)") {
$filename = "Surat_PKL_" . date('Ymd') . "_" . str_replace(' ', '_', $nama_perusahaan_db) . ".pdf";
}
else {
    $filename = "Surat_Penambahan Siswa_PKL_" . date('Ymd') . "_" . str_replace(' ', '_', $nama_perusahaan_db) . ".pdf";

}
$dompdf->stream($filename, ["Attachment" => 1]);

exit(0);
