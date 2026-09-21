<?php
// generate_surat_pembatalan.php
require 'vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

include 'koneksi.php';

// Tangani koneksi tidak stabil agar PHP tetap menuntaskan pekerjaan di server
ignore_user_abort(true);
set_time_limit(120);

// Deteksi apakah request dikirim melalui AJAX
$is_ajax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
    || (isset($_POST['is_ajax']) && $_POST['is_ajax'] == '1');

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
$alamat_perusahaan = trim($_POST['alamat_perusahaan'] ?? '');
$kota_perusahaan = trim($_POST['kota_perusahaan'] ?? '');

$query_tempat = "SELECT * FROM tempat_pkl WHERE id_tempat = $id_tempat_pkl";
$res_tempat = $koneksi->query($query_tempat);
$data_t = $res_tempat ? $res_tempat->fetch_assoc() : null;

if ($id_tempat_pkl > 0 && $data_t) {
    if ((empty($data_t['alamat']) && !empty($alamat_perusahaan)) || (empty($data_t['kota']) && !empty($kota_perusahaan))) {
        $update_alamat = !empty($data_t['alamat']) ? $data_t['alamat'] : $alamat_perusahaan;
        $update_kota = !empty($data_t['kota']) ? $data_t['kota'] : $kota_perusahaan;
        $stmt_up = $koneksi->prepare("UPDATE tempat_pkl SET alamat = ?, kota = ? WHERE id_tempat = ?");
        $stmt_up->bind_param("ssi", $update_alamat, $update_kota, $id_tempat_pkl);
        $stmt_up->execute();
        $stmt_up->close();
    }
}

$data_perusahaan = [
    'tujuan' => $nama_perusahaan,
    'yth' => 'Pimpinan',
    'alamat_tujuan' => !empty($alamat_perusahaan) ? $alamat_perusahaan : ($data_t['alamat'] ?? 'Alamat Perusahaan'),
    'kota_tujuan' => !empty($kota_perusahaan) ? $kota_perusahaan : ($data_t['kota'] ?? 'Kota'),
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

if (empty($siswa_batal_ids)) {
    if ($is_ajax) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'error', 'message' => 'Pilih minimal satu siswa yang akan dibatalkan.']);
        exit(0);
    }
    die("ERROR: Pilih minimal satu siswa yang akan dibatalkan.");
}

// =================================================================================
// --- TRANSAKSI DATABASE DAN GENERATE PDF ATOMIK ---
// =================================================================================
$id_surat_baru = null;
$arsip_file_path = null;

$koneksi->begin_transaction();

try {
    // 1. Simpan surat pembatalan ke database
    $stmt = $koneksi->prepare("INSERT INTO surat (no_surat, perihal, id_tempat_pkl, tanggal) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        throw new Exception("Gagal mempersiapkan query surat pembatalan: " . $koneksi->error);
    }
    $tgl_surat_db = $_POST['tanggal_surat'];
    $stmt->bind_param("ssis", $data_pengajuan['nomor_surat'], $data_pengajuan['perihal'], $id_tempat_pkl, $tgl_surat_db);
    if (!$stmt->execute()) {
        throw new Exception("Gagal menyimpan data surat pembatalan: " . $stmt->error);
    }
    $id_surat_baru = $koneksi->insert_id;
    $stmt->close();

    if (empty($id_surat_baru)) {
        throw new Exception("Gagal mendapatkan ID Surat Pembatalan baru.");
    }

    // 2. Simpan detail siswa ke siswa_surat (untuk riwayat surat pembatalan)
    $stmt_ss = $koneksi->prepare("INSERT INTO siswa_surat (id_siswa, id_surat) VALUES (?, ?)");
    if (!$stmt_ss) {
        throw new Exception("Gagal mempersiapkan query siswa surat: " . $koneksi->error);
    }
    foreach ($siswa_batal_ids as $id_siswa) {
        $stmt_ss->bind_param("ii", $id_siswa, $id_surat_baru);
        if (!$stmt_ss->execute()) {
            throw new Exception("Gagal mencatat siswa ID $id_siswa ke surat pembatalan: " . $stmt_ss->error);
        }
    }
    $stmt_ss->close();

    // 3. Logika: Hapus siswa dari surat referensi sebelumnya & Reset status tempat
    $id_surat_ref = isset($_POST['id_surat_ref']) ? intval($_POST['id_surat_ref']) : 0;
    if ($id_surat_ref > 0) {
        // Hapus hanya dari surat referensi sebelumnya agar riwayat surat pembatalan tetap ada
        $stmt_del = $koneksi->prepare("DELETE FROM siswa_surat WHERE id_siswa = ? AND id_surat = ?");
        $stmt_upd = $koneksi->prepare("UPDATE siswa SET id_tempat = 0 WHERE id_siswa = ?");

        foreach ($siswa_batal_ids as $id_siswa) {
            $id_siswa_int = intval($id_siswa);

            if ($stmt_del) {
                $stmt_del->bind_param("ii", $id_siswa_int, $id_surat_ref);
                $stmt_del->execute();
            }

            if ($stmt_upd) {
                $stmt_upd->bind_param("i", $id_siswa_int);
                $stmt_upd->execute();
            }
        }
        if ($stmt_del) $stmt_del->close();
        if ($stmt_upd) $stmt_upd->close();
    }

    // 4. Render PDF
    $options = new Options();
    $options->set('defaultFont', 'Calibri');
    $options->set('defaultFontSize', 12);
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', false);

    $dompdf = new Dompdf($options);

    ob_start();
    include 'template_surat_pembatalan.php';
    $html = ob_get_clean();

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $pdf_output = $dompdf->output();
    if (empty($pdf_output)) {
        throw new Exception("Hasil render PDF pembatalan kosong.");
    }

    // 5. Simpan Arsip PDF ke Folder Server
    $arsip_dir = __DIR__ . '/arsip_surat';
    if (!is_dir($arsip_dir)) {
        if (!mkdir($arsip_dir, 0777, true)) {
            throw new Exception("Gagal membuat direktori arsip_surat.");
        }
    }

    $arsip_file_path = $arsip_dir . '/surat_' . $id_surat_baru . '.pdf';
    $bytes_written = file_put_contents($arsip_file_path, $pdf_output);
    if ($bytes_written === false || $bytes_written <= 0) {
        throw new Exception("Gagal menyimpan file PDF arsip di server.");
    }

    // 6. Commit transaksi
    $koneksi->commit();

} catch (Exception $e) {
    $koneksi->rollback();

    if (!empty($arsip_file_path) && file_exists($arsip_file_path)) {
        @unlink($arsip_file_path);
    }

    $error_message = "Terjadi kesalahan saat memproses pembatalan: " . $e->getMessage();
    error_log($error_message);
    $koneksi->close();

    if ($is_ajax) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'status' => 'error',
            'message' => $error_message
        ]);
        exit(0);
    } else {
        die($error_message);
    }
}

$koneksi->close();

// A. Jika AJAX: Kembalikan JSON
if ($is_ajax) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'status' => 'success',
        'message' => 'Surat pembatalan berhasil dibuat dan disimpan.',
        'id_surat' => $id_surat_baru,
        'no_surat' => $data_pengajuan['nomor_surat'],
        'print_url' => 'cetak_surat.php?id=' . $id_surat_baru
    ]);
    exit(0);
}

// B. Non-AJAX Stream Fallback
$filename = "Surat_Pembatalan_PKL_" . date('Ymd') . "_" . str_replace(' ', '_', $nama_perusahaan) . ".pdf";
$dompdf->stream($filename, ["Attachment" => 0]);
exit(0);

