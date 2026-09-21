<?php
// ajax/tambah_pembimbing.php - Menambahkan pembimbing baru
header('Content-Type: application/json');

include '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Metode request tidak diizinkan.']);
    exit;
}

$nama_pembimbing = trim($_POST['nama_pembimbing'] ?? '');
$username = trim($_POST['username'] ?? '');
$kontak = trim($_POST['kontak_pembimbing'] ?? '');
$password_input = trim($_POST['password'] ?? '');

if (empty($nama_pembimbing)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Nama Pembimbing wajib diisi.']);
    exit;
}

// 1. Normalisasi nomor handphone (diawali 08)
$clean_kontak = preg_replace('/[^0-9]/', '', $kontak);
if (!empty($clean_kontak)) {
    if (strpos($clean_kontak, '62') === 0) {
        $clean_kontak = '0' . substr($clean_kontak, 2);
    } elseif (strpos($clean_kontak, '8') === 0) {
        $clean_kontak = '0' . $clean_kontak;
    }
}

// 2. Generate Username jika kosong
if (empty($username)) {
    // Ambil kata pertama atau nama tanpa gelar
    $clean_name = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', explode(',', $nama_pembimbing)[0]));
    if (empty($clean_name)) {
        $clean_name = 'pembimbing';
    }
    $base_username = substr($clean_name, 0, 15);
    $username = $base_username;
    $counter = 1;

    // Pastikan username unik
    $stmt_check = $koneksi->prepare("SELECT id_pembimbing FROM pembimbing WHERE username = ? LIMIT 1");
    while (true) {
        $stmt_check->bind_param("s", $username);
        $stmt_check->execute();
        $res = $stmt_check->get_result();
        if ($res->num_rows === 0) {
            break;
        }
        $username = substr($base_username, 0, 15) . $counter;
        $counter++;
    }
    $stmt_check->close();
} else {
    // Cek apakah username sudah dipakai
    $stmt_check = $koneksi->prepare("SELECT id_pembimbing FROM pembimbing WHERE username = ? LIMIT 1");
    $stmt_check->bind_param("s", $username);
    $stmt_check->execute();
    if ($stmt_check->get_result()->num_rows > 0) {
        $stmt_check->close();
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => "Username '$username' sudah digunakan oleh pembimbing lain."]);
        exit;
    }
    $stmt_check->close();
}

// 3. Password: jika tidak diisi, gunakan default 'pklifsu'
$raw_password = !empty($password_input) ? $password_input : 'pklifsu';
$password_hashed = password_hash($raw_password, PASSWORD_DEFAULT);
$password_status = 0;

// 4. Simpan ke database
$stmt = $koneksi->prepare("
    INSERT INTO pembimbing (username, nama_pembimbing, kontak_pembimbing, password, password_status)
    VALUES (?, ?, ?, ?, ?)
");

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyiapkan query: ' . $koneksi->error]);
    exit;
}

$stmt->bind_param("ssssi", $username, $nama_pembimbing, $clean_kontak, $password_hashed, $password_status);

if ($stmt->execute()) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Data pembimbing berhasil ditambahkan dengan username: ' . $username . ' (Password: ' . $raw_password . ')'
    ]);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data pembimbing: ' . $stmt->error]);
}

$stmt->close();
$koneksi->close();
?>
