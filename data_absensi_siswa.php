<?php
session_start();
// Pastikan koneksi.php hanya berisi kode koneksi PDO tanpa output apapun di luarnya!
include 'koneksi.php'; 
date_default_timezone_set('Asia/Jakarta');

// Harus menjadi output pertama, tanpa ada spasi atau baris kosong di atasnya
header('Content-Type: application/json');

// Fungsi untuk mengirimkan respons error dalam format JSON
function sendErrorResponse($message) {
    echo json_encode(['error' => $message, 'data' => []]);
    exit();
}

// 1. Cek Sesi
if (!isset($_SESSION['id_siswa']) || $_SESSION['logged_in'] !== true) {
    sendErrorResponse('Akses ditolak. Silakan login kembali.');
}

$id_siswa = $_SESSION['id_siswa'];

// --- Server-Side Processing Input ---
$draw = $_POST['draw'] ?? 1;
$start = $_POST['start'] ?? 0;
$length = $_POST['length'] ?? 10;
$search_value = $_POST['search']['value'] ?? '';

// Kolom yang akan ditampilkan (harus sesuai dengan nama kolom di DB)
$columns = [
    'tanggal_absensi', 
    'jam_masuk', 
    'status',
    'keterangan',
    'lokasi_masuk', 
    'foto_bukti'
];

try {
    
    // --- 2. Kondisi WHERE (Filter) ---
    $where_conditions = ["id_siswa = :id_siswa"];
    $params = [':id_siswa' => $id_siswa];

    // Tambahkan pencarian jika ada
    if (!empty($search_value)) {
        $search_query = [];
        // Gunakan parameter terikat untuk mencegah SQL Injection
        foreach ($columns as $column) {
            $search_query[] = "$column LIKE :search";
        }
        $where_conditions[] = '(' . implode(' OR ', $search_query) . ')';
        $params[':search'] = '%' . $search_value . '%';
    }

    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);

    // --- 3. Query Total Data (untuk recordsTotal dan recordsFiltered) ---
    $sql_total = "SELECT COUNT(id_absensi) FROM absensi $where_clause";
    
    // Untuk recordsTotal
    $stmt_total = $pdo->prepare($sql_total);
    $stmt_total->execute($params);
    $recordsTotal = $stmt_total->fetchColumn();

    // Untuk recordsFiltered (sama dengan Total karena filter hanya id_siswa dan search)
    $recordsFiltered = $recordsTotal; 
    
    // --- 4. Query Data yang Ditampilkan (Paging, Sorting, dan Filtering) ---

    // Sorting
    $order_clause = "ORDER BY id_absensi DESC"; // Default
    if (isset($_POST['order'])) {
        $column_index = $_POST['order'][0]['column'];
        $column_name = $columns[$column_index];
        $sort_dir = $_POST['order'][0]['dir'];
        $order_clause = "ORDER BY $column_name $sort_dir";
    } 

    // Paging: Gunakan LIMIT
    // PERHATIAN: LIMIT harus menggunakan integer, tidak boleh string terikat PDO untuk parameter kedua
    $start = (int)$start; 
    $length = (int)$length;
    $limit_clause = "LIMIT $start, $length"; 

    $sql = "SELECT tanggal_absensi, jam_masuk, status, keterangan, lokasi_masuk, foto_bukti 
            FROM absensi 
            $where_clause 
            $order_clause 
            $limit_clause";
    
    // Eksekusi query data
    // Hapus parameter LIMIT dari array params karena sudah dimasukkan langsung ke query
    // Array params yang digunakan untuk data query sama dengan params pencarian
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params); 
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format data untuk menangani NULL pada keterangan
    $data = [];
    foreach ($results as $row) {
        $data[] = [
            'tanggal_absensi' => $row['tanggal_absensi'],
            'jam_masuk' => $row['jam_masuk'],
            'status' => $row['status'],
            'keterangan' => $row['keterangan'] ?? '', // NULL menjadi empty string
            'lokasi_masuk' => $row['lokasi_masuk'],
            'foto_bukti' => $row['foto_bukti']
        ];
    }

    // --- 5. Format Output JSON DataTables ---
    $response = [
        "draw" => (int)$draw,
        "recordsTotal" => (int)$recordsTotal,
        "recordsFiltered" => (int)$recordsFiltered, 
        "data" => $data
    ];

    echo json_encode($response);

} catch (PDOException $e) {
    // Tangani semua error database
    sendErrorResponse('Database Error: Gagal mengambil data absensi. (' . $e->getMessage() . ')');
}
// Pastikan tidak ada kode atau baris kosong di luar tag PHP penutup.
?>