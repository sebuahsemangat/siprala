<?php
// ajax/import_pembimbing.php - Proses import data pembimbing dari file Excel (.xlsx)
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

// 1. Deteksi index kolom dari header (baris pertama)
$header = $rows[0];
$namaIdx = -1;
$usernameIdx = -1;
$kontakIdx = -1;

foreach ($header as $idx => $colName) {
    $colClean = strtolower(strip_tags(trim((string)$colName)));
    if (strpos($colClean, 'nama') !== false) {
        $namaIdx = $idx;
    } elseif (strpos($colClean, 'user') !== false) {
        $usernameIdx = $idx;
    } elseif (strpos($colClean, 'kontak') !== false || strpos($colClean, 'hp') !== false || strpos($colClean, 'telp') !== false) {
        $kontakIdx = $idx;
    }
}

// Fallback jika header tidak terdeteksi otomatis
if ($namaIdx === -1) $namaIdx = 0;
if ($usernameIdx === -1) $usernameIdx = 1;
if ($kontakIdx === -1) $kontakIdx = 2;

// 2. Siapkan password default 'pklifsu'
$default_password_hash = password_hash('pklifsu', PASSWORD_DEFAULT);

// 3. Ambil semua username yang sudah ada untuk pengecekan cepat
$existing_usernames = [];
$res_user = $koneksi->query("SELECT username FROM pembimbing");
if ($res_user) {
    while ($r = $res_user->fetch_assoc()) {
        $existing_usernames[strtolower($r['username'])] = true;
    }
}

$koneksi->begin_transaction();

$stmt_insert = $koneksi->prepare("
    INSERT INTO pembimbing (username, nama_pembimbing, kontak_pembimbing, password, password_status)
    VALUES (?, ?, ?, ?, 0)
");
$stmt_update = $koneksi->prepare("
    UPDATE pembimbing 
    SET nama_pembimbing = ?, kontak_pembimbing = ?
    WHERE username = ?
");

$successCount = 0;
$updateCount = 0;
$skipCount = 0;

for ($i = 1; $i < count($rows); $i++) {
    $row = $rows[$i];

    $nama = isset($row[$namaIdx]) ? trim((string)$row[$namaIdx]) : '';
    $user = ($usernameIdx !== -1 && isset($row[$usernameIdx])) ? trim((string)$row[$usernameIdx]) : '';
    $kontak = ($kontakIdx !== -1 && isset($row[$kontakIdx])) ? trim((string)$row[$kontakIdx]) : '';

    if (empty($nama)) {
        $skipCount++;
        continue;
    }

    // Normalisasi kontak (diawali 08)
    $clean_kontak = preg_replace('/[^0-9]/', '', $kontak);
    if (!empty($clean_kontak)) {
        if (strpos($clean_kontak, '62') === 0) {
            $clean_kontak = '0' . substr($clean_kontak, 2);
        } elseif (strpos($clean_kontak, '8') === 0) {
            $clean_kontak = '0' . $clean_kontak;
        }
    }

    // Jika username kosong, generate otomatis
    if (empty($user)) {
        $clean_name = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', explode(',', $nama)[0]));
        if (empty($clean_name)) {
            $clean_name = 'pembimbing';
        }
        $base = substr($clean_name, 0, 15);
        $user = $base;
        $counter = 1;
        while (isset($existing_usernames[strtolower($user)])) {
            $user = substr($base, 0, 15) . $counter;
            $counter++;
        }
    }

    $user_lower = strtolower($user);

    if (isset($existing_usernames[$user_lower])) {
        // Username sudah ada, update data nama & kontak
        $stmt_update->bind_param("sss", $nama, $clean_kontak, $user);
        $stmt_update->execute();
        $updateCount++;
    } else {
        // Pembimbing baru, insert dengan password default 'pklifsu'
        $stmt_insert->bind_param("ssss", $user, $nama, $clean_kontak, $default_password_hash);
        if ($stmt_insert->execute()) {
            $existing_usernames[$user_lower] = true;
            $successCount++;
        } else {
            $skipCount++;
        }
    }
}

$stmt_insert->close();
$stmt_update->close();

$koneksi->commit();
$koneksi->close();

$msg = "Import selesai. Berhasil menambahkan $successCount pembimbing baru";
if ($updateCount > 0) {
    $msg .= ", memperbarui $updateCount pembimbing yang sudah ada";
}
if ($skipCount > 0) {
    $msg .= ", dan melewatkan $skipCount baris kosong/tidak valid";
}
$msg .= ". Password default pembimbing baru: pklifsu";

echo json_encode([
    'status' => 'success',
    'message' => $msg,
    'total_inserted' => $successCount,
    'total_updated' => $updateCount
]);
?>
