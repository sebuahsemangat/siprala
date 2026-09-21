<?php

// ==== PENGATURAN DATABASE ====
$host = 'localhost';
$dbname = 'absensi_pkl'; // Ganti dengan nama database Anda
$user = 'root';                 // Ganti dengan username database Anda
$pass = '';                     // Ganti dengan password database Anda
// ============================

$csvFilePath = 'tempat_pkl.csv'; // Nama file CSV Anda
$tableName = 'tempat_pkl';       // Nama tabel target

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
    // Struktur: id_tempat, nama_tempat, id_pembimbing
    $sql = "INSERT INTO $tableName (id_tempat, nama_tempat, id_pembimbing) 
            VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);

    // 4. Baca CSV baris per baris
    $isHeader = true; // Flag untuk melompati baris pertama (header)
    $counter = 0;

    echo "Memulai proses impor data Tempat PKL... <br>";

    // Mulai transaksi untuk mempercepat proses
    $pdo->beginTransaction();

    // Gunakan delimiter ';'
    while (($row = fgetcsv($fileHandle, 1000, ';')) !== false) {
        
        // Lewati baris header
        if ($isHeader) {
            $isHeader = false;
            continue;
        }

        // Lewati baris kosong (jika ada di akhir file)
        // Cek jika jumlah kolom < 3 atau ID tempat kosong
        if (count($row) < 3 || empty($row[0])) {
            continue; 
        }

        // Ambil data dari CSV
        $id_tempat = $row[0];
        $nama_tempat = $row[1];
        $id_pembimbing = $row[2];

        // 5. Eksekusi INSERT (Tidak perlu hash)
        $stmt->execute([
            $id_tempat,
            $nama_tempat,
            $id_pembimbing
        ]);
        
        $counter++;
    }
    
    // Selesaikan transaksi
    $pdo->commit();

    // 6. Tutup file
    fclose($fileHandle);

    echo "<hr><strong>Berhasil!</strong><br>";
    echo "Total data tempat PKL yang berhasil diimpor: <strong>$counter</strong> baris.";

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