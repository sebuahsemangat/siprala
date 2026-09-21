<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['admin_id']) && !isset($_SESSION['logged_in_admin'])) {
    die("Akses ditolak. Silakan login terlebih dahulu.");
}

// Set Header untuk Excel
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=Rekap_Nilai_PKL_" . date('Y-m-d') . ".xls");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Pragma: public");

try {
    $stmt = $pdo->query("
        SELECT 
            s.nis,
            s.nama_siswa,
            s.kelas,
            tp.nama_tempat,
            p.nama_pembimbing,
            n.nilai_pembimbing_sekolah,
            n.nilai_pembimbing_dudi,
            n.nilai_sidang,
            n.nilai_akhir
        FROM 
            siswa s
        LEFT JOIN
            nilai_pkl n ON s.id_siswa = n.id_siswa
        LEFT JOIN 
            pembimbing p ON s.id_pembimbing = p.id_pembimbing
        LEFT JOIN 
            tempat_pkl tp ON s.id_tempat = tp.id_tempat
        ORDER BY 
            s.kelas ASC, s.nama_siswa ASC
    ");
    $all_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<table border='1'>";

    $current_class = "";
    $no = 1;

    foreach ($all_data as $row) {
        if ($current_class != $row['kelas']) {
            if ($current_class != "") {
                // Beri baris kosong antar kelas
                echo "<tr><td colspan='9' style='border:none;'>&nbsp;</td></tr>";
                echo "<tr><td colspan='9' style='border:none;'>&nbsp;</td></tr>";
            }

            $current_class = $row['kelas'];
            $no = 1; // Reset nomor untuk kelas baru

            // Judul Kelas
            echo "<tr><th colspan='9' style='background-color: #1a365d; color: #ffffff; font-size: 14pt; padding: 10px;'>REKAPITULASI NILAI PKL - KELAS " . htmlspecialchars($current_class) . "</th></tr>";

            // Header Tabel
            echo "<tr>
                    <th style='background-color: #e2e8f0;'>No</th>
                    <th style='background-color: #e2e8f0;'>NIS</th>
                    <th style='background-color: #e2e8f0;'>Nama Siswa</th>
                    <th style='background-color: #e2e8f0;'>Tempat PKL</th>
                    <th style='background-color: #e2e8f0;'>Pembimbing</th>
                    <th style='background-color: #e2e8f0;'>N. Pemb. Sekolah</th>
                    <th style='background-color: #e2e8f0;'>N. Pemb. DU/DI</th>
                    <th style='background-color: #e2e8f0;'>N. Sidang</th>
                    <th style='background-color: #e2e8f0;'>N. Akhir</th>
                  </tr>";
        }

        echo "<tr>";
        echo "<td align='center'>" . $no++ . "</td>";
        echo "<td align='center'>'" . htmlspecialchars($row['nis']) . "</td>";
        echo "<td>" . htmlspecialchars($row['nama_siswa']) . "</td>";
        echo "<td>" . htmlspecialchars($row['nama_tempat'] ?? 'Belum Ditentukan') . "</td>";
        echo "<td>" . htmlspecialchars($row['nama_pembimbing'] ?? 'Belum Ditentukan') . "</td>";
        echo "<td align='center'>" . ($row['nilai_pembimbing_sekolah'] ?? 0) . "</td>";
        echo "<td align='center'>" . ($row['nilai_pembimbing_dudi'] ?? 0) . "</td>";
        echo "<td align='center'>" . ($row['nilai_sidang'] ?? 0) . "</td>";
        echo "<td align='center'><strong>" . ($row['nilai_akhir'] ?? 0) . "</strong></td>";
        echo "</tr>";
    }

    echo "</table>";

} catch (PDOException $e) {
    die("Gagal mengekspor data: " . $e->getMessage());
}
?>
