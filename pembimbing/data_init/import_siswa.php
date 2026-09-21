<?php

// ==== PENGATURAN DATABASE ====
$host = 'localhost';
$dbname = 'absensi_pkl'; // Ganti dengan nama database Anda
$user = 'root';                 // Ganti dengan username database Anda
$pass = '';                     // Ganti dengan password database Anda
// ============================

$csvFilePath = 'tb_siswa.csv'; // Nama file CSV Anda
$tableName = 'siswa';          // Nama tabel target

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
    // Struktur: id_siswa, nis, nama_siswa, kelas, kontak_siswa, id_pembimbing, id_tempat, password
    $sql = "INSERT INTO $tableName (id_siswa, nis, nama_siswa, kelas, kontak_siswa, id_pembimbing, id_tempat, password) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);

    // 4. Baca CSV baris per baris
    $isHeader = true; // Flag untuk melompati baris pertama (header)
    $counter = 0;

    echo "Memulai proses impor data Siswa... <br>";

    // Mulai transaksi untuk mempercepat proses
    $pdo->beginTransaction();

    // Gunakan delimiter ';'
    while (($row = fgetcsv($fileHandle, 1000, ';')) !== false) {
        
        // Lewati baris header
        if ($isHeader) {
            $isHeader = false;
            continue;
        }

        // Lewati baris kosong atau tidak lengkap
        if (count($row) < 8 || empty($row[0])) {
            continue; 
        }

        // Ambil data dari CSV
        $id_siswa = $row[0];
        $nis = $row[1];
        $nama_siswa = $row[2];
        $kelas = $row[3];
        $kontak_siswa = $row[4];
        $id_pembimbing = $row[5];
        $id_tempat = $row[6];
        $plainPassword = $row[7]; // Password mentah: "siswapkl"

        // === Hash password menggunakan fungsi PHP yang aman ===
        $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);
        // ====================================================

        // 5. Eksekusi INSERT dengan data yang sudah di-hash
        $stmt->execute([
            $id_siswa,
            $nis,
            $nama_siswa,
            $kelas,
            $kontak_siswa,
            $id_pembimbing,
            $id_tempat,
            $hashedPassword // Masukkan password yang sudah aman
        ]);
        
        $counter++;
    }
    
    // Selesaikan transaksi
    $pdo->commit();

    // 6. Tutup file
    fclose($fileHandle);

    echo "<hr><strong>Berhasil!</strong><br>";
    echo "Total data siswa yang berhasil diimpor: <strong>$counter</strong> baris.";

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