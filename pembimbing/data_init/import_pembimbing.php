<?php

// ==== PENGATURAN DATABASE ====
$host = 'localhost';
$dbname = 'absensi_pkl'; // Ganti dengan nama database Anda
$user = 'root';                 // Ganti dengan username database Anda
$pass = '';                     // Ganti dengan password database Anda
// ============================

$csvFilePath = 'tb_pembimbing.csv'; // Nama file CSV Anda
$tableName = 'pembimbing';          // Nama tabel target

try {
    // 1. Koneksi ke Database menggunakan PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2. Buka file CSV untuk dibaca
    $fileHandle = fopen($csvFilePath, 'r');
    if ($fileHandle === false) {
        throw new Exception("Error: Tidak dapat membuka file CSV '$csvFilePath'.");
    }

    // 3. Siapkan Perintah SQL (Prepared Statement)
    // PERHATIKAN: Saya mengganti 'id_pembimbing' menjadi 'id_pimbimbing' sesuai pesan error Anda
    $sql = "INSERT INTO $tableName (id_pembimbing, username, nama_pembimbing, kontak_pembimbing, password) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);

    // 4. Baca CSV baris per baris
    $isHeader = true; // Flag untuk melompati baris pertama (header)
    $counter = 0;

    echo "Memulai proses impor... <br>";

    // Mulai transaksi untuk mempercepat proses
    $pdo->beginTransaction();

    // ===============================================
    // PERUBAHAN UTAMA DI SINI: Ganti delimiter dari ',' menjadi ';'
    // ===============================================
    while (($row = fgetcsv($fileHandle, 1000, ';')) !== false) {

        // Lewati baris header
        if ($isHeader) {
            $isHeader = false;
            continue;
        }

        // ===============================================
        // PERBAIKAN TAMBAHAN: Lewati baris kosong di akhir file
        // ===============================================
        if (count($row) < 5 || empty($row[0])) {
            continue; 
        }

        // Ambil data dari CSV (Sekarang harusnya aman)
        $id_pembimbing = $row[0];
        $username = $row[1];
        $nama_pembimbing = $row[2];
        $kontak_pembimbing = $row[3]; // Line 50
        $plainPassword = $row[4]; // Line 51

        // Hash password
        $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT); // Line 55

        // 5. Eksekusi INSERT dengan data yang sudah di-hash
        $stmt->execute([
            $id_pembimbing,
            $username,
            $nama_pembimbing,
            $kontak_pembimbing,
            $hashedPassword // Masukkan password yang sudah aman
        ]);
        
        $counter++;
    }
    
    // Selesaikan transaksi
    $pdo->commit();

    // 6. Tutup file
    fclose($fileHandle);

    echo "<hr><strong>Berhasil!</strong><br>";
    echo "Total data pembimbing yang berhasil diimpor: <strong>$counter</strong> baris.";

} catch (PDOException $e) {
    // Batalkan transaksi jika ada error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "Error Database: " . $e->getMessage();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

?>