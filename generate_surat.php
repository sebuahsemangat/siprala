<?php
// generate_surat.php - FINAL DENGAN TRANSAKSI DATABASE LENGKAP

// 1. Load Composer Autoload & Library
require 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// --- KRITIS: INCLUDE KONEKSI DATABASE ---
include 'koneksi.php';

// Tangani koneksi tidak stabil agar PHP tetap menuntaskan pekerjaan di server
ignore_user_abort(true);
set_time_limit(120);

// Deteksi apakah request dikirim melalui AJAX
$is_ajax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
    || (isset($_POST['is_ajax']) && $_POST['is_ajax'] == '1');

// **DEFINISIKAN BASE PATH ABSOLUT**
$base_path = __DIR__;

// **FUNGSI UNTUK CONVERT GAMBAR KE BASE64**
function imageToBase64($imagePath)
{
    if (!file_exists($imagePath)) {
        return '';
    }
    $imageData = file_get_contents($imagePath);
    if ($imageData === false) {
        return '';
    }
    $imageInfo = @getimagesize($imagePath);
    $mimeType = $imageInfo ? $imageInfo['mime'] : 'image/jpeg';
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

// **FUNGSI UNTUK NORMALISASI NOMOR HANDPHONE (DIAWALI 08)**
function formatNomorHP($hp)
{
    // Bersihkan karakter selain angka
    $clean = preg_replace('/[^0-9]/', '', (string)$hp);
    if (empty($clean)) {
        return '';
    }

    // Jika diawali 628, ganti jadi 08
    if (strpos($clean, '62') === 0) {
        $clean = '0' . substr($clean, 2);
    } elseif (strpos($clean, '8') === 0) {
        // Jika diawali 8 langsung, tambahkan 0
        $clean = '0' . $clean;
    }

    return $clean;
}

// Data Siswa (Diproses dari array dinamis)
// Structure: $_POST['siswa'][id_siswa][nama/kelas/hp]
$data_siswa_raw = $_POST['siswa'] ?? [];
$data_siswa = [];
$id_siswa_list = []; // List untuk INSERT ke siswa_surat

foreach ($data_siswa_raw as $id_siswa => $siswa) {
    if (is_array($siswa) && !empty($siswa['nama']) && !empty($siswa['kelas']) && !empty($siswa['hp'])) {
        $hp_formatted = formatNomorHP($siswa['hp']);
        $data_siswa[] = [
            'nama' => htmlspecialchars($siswa['nama']),
            'kelas' => htmlspecialchars($siswa['kelas']),
            'hp' => htmlspecialchars($hp_formatted ?: $siswa['hp']),
        ];
        // Tambahkan id_siswa ke list untuk INSERT ke DB
        $id_siswa_list[] = intval($id_siswa);
    }
}

if (empty($data_siswa)) {
    if ($is_ajax) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'error', 'message' => 'Daftar siswa tidak boleh kosong.']);
        exit(0);
    }
    die("ERROR: Daftar siswa tidak boleh kosong.");
}


// =================================================================================
// --- TRANSAKSI DATABASE DAN GENERATE PDF SECARA ATOMIK ---
// =================================================================================
$id_tempat_pkl = null;
$id_surat = null;
$arsip_file_path = null;

// Mulai transaksi database
$koneksi->begin_transaction();

try {
    // 1. Cek atau INSERT Tempat PKL Baru
    $alamat_perusahaan_db = trim($_POST['alamat_perusahaan'] ?? '');
    $kota_perusahaan_db = trim($_POST['kota_perusahaan'] ?? '');

    $stmt = $koneksi->prepare("SELECT id_tempat, alamat, kota FROM tempat_pkl WHERE nama_tempat = ?");
    if (!$stmt) {
        throw new Exception("Gagal mempersiapkan query tempat PKL: " . $koneksi->error);
    }
    $stmt->bind_param("s", $nama_perusahaan_db);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Tempat PKL sudah ada
        $row = $result->fetch_assoc();
        $id_tempat_pkl = $row['id_tempat'];

        // Jika alamat atau kota di database masih kosong tapi di form diisi, update data
        if ((empty($row['alamat']) && !empty($alamat_perusahaan_db)) || (empty($row['kota']) && !empty($kota_perusahaan_db))) {
            $update_alamat = !empty($row['alamat']) ? $row['alamat'] : $alamat_perusahaan_db;
            $update_kota = !empty($row['kota']) ? $row['kota'] : $kota_perusahaan_db;
            $stmt_update = $koneksi->prepare("UPDATE tempat_pkl SET alamat = ?, kota = ? WHERE id_tempat = ?");
            if ($stmt_update) {
                $stmt_update->bind_param("ssi", $update_alamat, $update_kota, $id_tempat_pkl);
                $stmt_update->execute();
                $stmt_update->close();
            }
        }
    } else {
        // Tempat PKL BARU: INSERT dan dapatkan ID-nya
        $stmt_insert = $koneksi->prepare("INSERT INTO tempat_pkl (nama_tempat, alamat, kota) VALUES (?, ?, ?)");
        if (!$stmt_insert) {
            throw new Exception("Gagal mempersiapkan insert tempat PKL: " . $koneksi->error);
        }
        $stmt_insert->bind_param("sss", $nama_perusahaan_db, $alamat_perusahaan_db, $kota_perusahaan_db);
        if (!$stmt_insert->execute()) {
            throw new Exception("Gagal menyimpan data tempat PKL: " . $stmt_insert->error);
        }
        $id_tempat_pkl = $koneksi->insert_id;
        $stmt_insert->close();
    }
    $stmt->close();

    if (empty($id_tempat_pkl)) {
        throw new Exception("Gagal mendapatkan ID Tempat PKL.");
    }

    // 2. INSERT Data Surat ke tabel 'surat'
    $stmt = $koneksi->prepare("INSERT INTO surat (no_surat, perihal, id_tempat_pkl, tanggal) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        throw new Exception("Gagal mempersiapkan query surat: " . $koneksi->error);
    }
    $stmt->bind_param("ssis", $data_pengajuan['nomor_surat'], $data_pengajuan['perihal'], $id_tempat_pkl, $tanggal_surat_db);
    if (!$stmt->execute()) {
        throw new Exception("Gagal menyimpan data surat: " . $stmt->error);
    }
    $id_surat = $koneksi->insert_id;
    $stmt->close();

    if (empty($id_surat)) {
        throw new Exception("Gagal mendapatkan ID Surat yang baru dibuat.");
    }

    // 3. INSERT Data Siswa ke tabel 'siswa_surat'
    $stmt = $koneksi->prepare("INSERT INTO siswa_surat (id_siswa, id_surat) VALUES (?, ?)");
    if (!$stmt) {
        throw new Exception("Gagal mempersiapkan query siswa_surat: " . $koneksi->error);
    }
    foreach ($id_siswa_list as $id_siswa) {
        $stmt->bind_param("ii", $id_siswa, $id_surat);
        if (!$stmt->execute()) {
            throw new Exception("Gagal memasukkan siswa ID $id_siswa ke surat: " . $stmt->error);
        }
    }
    $stmt->close();

    // 4. Sinkronisasi / Update Nomor Handphone ke tabel 'siswa'
    $stmt_update_kontak = $koneksi->prepare("
        UPDATE siswa 
        SET kontak_siswa = ? 
        WHERE id_siswa = ? 
          AND (kontak_siswa IS NULL OR kontak_siswa = '' OR kontak_siswa != ?)
    ");

    if ($stmt_update_kontak) {
        foreach ($data_siswa_raw as $id_siswa_key => $siswa_item) {
            $id_siswa_val = intval($id_siswa_key);
            if ($id_siswa_val > 0 && !empty($siswa_item['hp'])) {
                $formatted_hp = formatNomorHP($siswa_item['hp']);
                if (!empty($formatted_hp) && strpos($formatted_hp, '08') === 0) {
                    $stmt_update_kontak->bind_param("sis", $formatted_hp, $id_siswa_val, $formatted_hp);
                    $stmt_update_kontak->execute();
                }
            }
        }
        $stmt_update_kontak->close();
    }

    // 5. RENDER PDF MENGGUNAKAN DOMPDF
    $options = new Options();
    $options->set('defaultFont', 'Calibri');
    $options->set('defaultFontSize', 12);
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', false); // Aset kop/ttd sudah Base64 lokal, matikan agar tidak hang jika jaringan bermasalah

    $dompdf = new Dompdf($options);

    ob_start();
    if ($data_pengajuan['perihal'] == "Pengajuan Tempat Praktik Kerja Lapangan (PKL)") {
        include 'template_surat.php';
    } else if ($data_pengajuan['perihal'] == "Penambahan Siswa Praktik Kerja Lapangan (PKL)") {
        include 'template_surat_penambahan.php';
    } else if ($data_pengajuan['perihal'] == "Pembatalan Siswa Praktik Kerja Lapangan (PKL)") {
        include 'template_surat_pembatalan.php';
    } else {
        include 'template_surat.php';
    }
    $html = ob_get_clean();

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $pdf_output = $dompdf->output();
    if (empty($pdf_output)) {
        throw new Exception("Hasil render PDF kosong.");
    }

    // 6. SIMPAN ARSIP PDF KE DISK SERVER
    $arsip_dir = __DIR__ . '/arsip_surat';
    if (!is_dir($arsip_dir)) {
        if (!mkdir($arsip_dir, 0777, true)) {
            throw new Exception("Gagal membuat direktori arsip_surat.");
        }
    }

    $arsip_file_path = $arsip_dir . '/surat_' . $id_surat . '.pdf';
    $bytes_written = file_put_contents($arsip_file_path, $pdf_output);
    if ($bytes_written === false || $bytes_written <= 0) {
        throw new Exception("Gagal menyimpan file PDF arsip di server.");
    }

    // 7. JIKA SAMPAI TAHAP INI SEMUA SUKSES (DB + PDF), LAKUKAN COMMIT!
    $koneksi->commit();

} catch (Exception $e) {
    // ROLLBACK DATABASE JIKA TERJADI KESALAHAN!
    // Ini memastikan tabel surat & siswa_surat tetap bersih dan siswa TIDAK TERKUNCI!
    $koneksi->rollback();

    // Hapus file parsial jika ada
    if (!empty($arsip_file_path) && file_exists($arsip_file_path)) {
        @unlink($arsip_file_path);
    }

    $error_message = "Terjadi kesalahan saat memproses surat: " . $e->getMessage();
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

// Tutup koneksi database setelah commit berhasil
$koneksi->close();

// =================================================================================
// --- RESPONS KE KLIEN ---
// =================================================================================

// A. JIKA REQUEST VIA AJAX: Kembalikan JSON Ringan
if ($is_ajax) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'status' => 'success',
        'message' => 'Surat berhasil dibuat dan diarsipkan di server.',
        'id_surat' => $id_surat,
        'no_surat' => $data_pengajuan['nomor_surat'],
        'perihal' => $data_pengajuan['perihal'],
        'print_url' => 'cetak_surat.php?id=' . $id_surat
    ]);
    exit(0);
}

// B. JIKA SUBMIT LANGSUNG NON-AJAX: Streaming PDF seperti biasa
if ($data_pengajuan['perihal'] == "Pengajuan Tempat Praktik Kerja Lapangan (PKL)") {
    $filename = "Surat_PKL_" . date('Ymd') . "_" . str_replace(' ', '_', $nama_perusahaan_db) . ".pdf";
} else {
    $filename = "Surat_Penambahan Siswa_PKL_" . date('Ymd') . "_" . str_replace(' ', '_', $nama_perusahaan_db) . ".pdf";
}
$dompdf->stream($filename, ["Attachment" => 0]);
exit(0);
