<?php
// ajax/import_siswa.php - Proses import data siswa dari file Excel (.xlsx)
header('Content-Type: application/json');

require '../vendor/autoload.php';
include '../koneksi.php';

use Shuchkin\SimpleXLSX;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Metode request tidak diizinkan.']);
    exit;
}

if (!isset($_FILES['file_excel']) || $_FILES['file_excel']['error'] !== UPLOAD_ERR_OK) {
    $errorMsg = 'Silakan pilih file Excel (.xlsx) untuk diunggah.';
    if (isset($_FILES['file_excel']['error'])) {
        switch ($_FILES['file_excel']['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $errorMsg = 'Ukuran file melebihi batas maksimal yang diizinkan.';
                break;
            case UPLOAD_ERR_NO_FILE:
                $errorMsg = 'Tidak ada file yang dipilih.';
                break;
        }
    }
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => $errorMsg]);
    exit;
}

$fileName = $_FILES['file_excel']['name'];
$fileTmp = $_FILES['file_excel']['tmp_name'];
$fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

if ($fileExt !== 'xlsx') {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Format file tidak didukung. Harap unggah file dengan format .xlsx (Excel).']);
    exit;
}

$xlsx = SimpleXLSX::parse($fileTmp);
if (!$xlsx) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Gagal membaca file Excel: ' . SimpleXLSX::parseError()]);
    exit;
}

$rows = $xlsx->rows();
if (count($rows) < 2) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'File Excel kosong atau tidak memiliki baris data.']);
    exit;
}

// Deteksi index kolom dari header (baris pertama)
$header = $rows[0];
$nisIdx = -1;
$namaIdx = -1;
$kelasIdx = -1;
$kontakIdx = -1;

foreach ($header as $idx => $colName) {
    $colClean = strtolower(strip_tags(trim((string)$colName)));
    if (strpos($colClean, 'nis') !== false) {
        $nisIdx = $idx;
    } elseif (strpos($colClean, 'nama') !== false) {
        $namaIdx = $idx;
    } elseif (strpos($colClean, 'kelas') !== false) {
        $kelasIdx = $idx;
    } elseif (strpos($colClean, 'kontak') !== false || strpos($colClean, 'hp') !== false || strpos($colClean, 'telepon') !== false || strpos($colClean, 'telp') !== false) {
        $kontakIdx = $idx;
    }
}

// Fallback jika nama header tidak terdeteksi: gunakan urutan default 0, 1, 2, 3
if ($nisIdx === -1) $nisIdx = 0;
if ($namaIdx === -1) $namaIdx = 1;
if ($kelasIdx === -1) $kelasIdx = 2;
if ($kontakIdx === -1) $kontakIdx = 3;

$stmt_check = $koneksi->prepare("SELECT id_siswa FROM siswa WHERE nis = ?");
$stmt_insert = $koneksi->prepare("INSERT INTO siswa (nis, nama_siswa, kelas, kontak_siswa, id_pembimbing, id_tempat, password, password_status) VALUES (?, ?, ?, ?, 0, 0, ?, 0)");
$stmt_update = $koneksi->prepare("UPDATE siswa SET nama_siswa = ?, kelas = ?, kontak_siswa = ? WHERE nis = ?");

$insertedCount = 0;
$updatedCount = 0;
$skippedCount = 0;

$koneksi->begin_transaction();

try {
    for ($i = 1; $i < count($rows); $i++) {
        $row = $rows[$i];

        $nis = isset($row[$nisIdx]) ? trim((string)$row[$nisIdx]) : '';
        $nama = isset($row[$namaIdx]) ? trim((string)$row[$namaIdx]) : '';
        $kelas = isset($row[$kelasIdx]) ? trim((string)$row[$kelasIdx]) : '';
        $kontak = isset($row[$kontakIdx]) ? trim((string)$row[$kontakIdx]) : '';

        // Abaikan jika seluruh baris kosong
        if ($nis === '' && $nama === '') {
            continue;
        }

        if ($nis === '' || $nama === '') {
            $skippedCount++;
            continue;
        }

        // Cek apakah siswa sudah terdaftar berdasarkan NIS
        $stmt_check->bind_param("s", $nis);
        $stmt_check->execute();
        $res_check = $stmt_check->get_result();

        if ($res_check->num_rows > 0) {
            // Update siswa yang sudah ada
            $stmt_update->bind_param("ssss", $nama, $kelas, $kontak, $nis);
            $stmt_update->execute();
            $updatedCount++;
        } else {
            // Siswa baru: password default = hash(NIS)
            $defaultPassword = password_hash($nis, PASSWORD_DEFAULT);
            $stmt_insert->bind_param("sssss", $nis, $nama, $kelas, $kontak, $defaultPassword);
            $stmt_insert->execute();
            $insertedCount++;
        }
    }

    $koneksi->commit();

    $stmt_check->close();
    $stmt_insert->close();
    $stmt_update->close();

    $message = "Import berhasil! Menambahkan $insertedCount siswa baru dan memperbarui $updatedCount siswa.";
    if ($skippedCount > 0) {
        $message .= " ($skippedCount baris dilewati karena data NIS/Nama kosong).";
    }

    echo json_encode([
        'status' => 'success',
        'message' => $message,
        'inserted' => $insertedCount,
        'updated' => $updatedCount,
        'skipped' => $skippedCount
    ]);

} catch (Exception $e) {
    $koneksi->rollback();
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Terjadi kesalahan saat memproses data: ' . $e->getMessage()
    ]);
}

$koneksi->close();
?>
