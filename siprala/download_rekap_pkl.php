<?php
// download_rekap_pkl.php - Download rekap bimbingan PKL (1 sheet, per pembimbing)
session_start();
require_once 'koneksi.php';
require_once 'vendor/autoload.php';

use Shuchkin\SimpleXLSXGen;

if (!isset($_SESSION['admin_id']) && !isset($_SESSION['logged_in_admin'])) {
    die("Akses ditolak. Silakan login terlebih dahulu.");
}

// ── 1. Ambil semua data ────────────────────────────────────────────────────
$sql = "
    SELECT
        p.id_pembimbing,
        p.nama_pembimbing,
        tp.id_tempat,
        tp.nama_tempat,
        tp.no_telepon    AS no_telepon_tempat,
        s.nis,
        s.nama_siswa,
        s.kontak_siswa
    FROM pembimbing p
    LEFT JOIN siswa s       ON s.id_pembimbing = p.id_pembimbing
    LEFT JOIN tempat_pkl tp ON s.id_tempat     = tp.id_tempat
    ORDER BY p.nama_pembimbing ASC,
             tp.nama_tempat    ASC,
             s.nama_siswa      ASC
";

$result = $koneksi->query($sql);
if (!$result) {
    die("Query gagal: " . $koneksi->error);
}

// ── 2. Kelompokkan: pembimbing → tempat → siswa ───────────────────────────
$peta = [];
while ($row = $result->fetch_assoc()) {
    $id_p = $row['id_pembimbing'];
    $id_t = $row['id_tempat'] ?? 'none';

    if (!isset($peta[$id_p])) {
        $peta[$id_p] = [
            'nama' => $row['nama_pembimbing'],
            'tempat' => []
        ];
    }

    if ($row['nis'] === null)
        continue;

    if (!isset($peta[$id_p]['tempat'][$id_t])) {
        $peta[$id_p]['tempat'][$id_t] = [
            'nama_tempat' => $row['nama_tempat'] ?? 'Belum Ditentukan',
            'no_telepon_tempat' => $row['no_telepon_tempat'] ?? '-',
            'siswa' => []
        ];
    }

    $peta[$id_p]['tempat'][$id_t]['siswa'][] = [
        'nis' => $row['nis'],
        'nama_siswa' => $row['nama_siswa'],
        'kontak_siswa' => $row['kontak_siswa'] ?? '-'
    ];
}
$koneksi->close();

// ── 3. Fungsi bantu style ─────────────────────────────────────────────────
function s_judul_pembimbing(string $teks): string
{
    return "<style font-size=\"12\"><b><middle><left>{$teks}</left></middle></b></style>";
}
function s_header_kolom(string $teks): string
{
    return "<style border=\"thin\"><b><middle><center>{$teks}</center></middle></b></style>";
}
function s_tempat(string $teks): string
{
    return "<style border=\"thin\"><middle>{$teks}</middle></style>";
}
function s_no(string $n): string
{
    return "<style border=\"thin\"><middle><center>{$n}</center></middle></style>";
}
function s_cell(string $teks, bool $center = false): string
{
    $inner = $center ? "<center>{$teks}</center>" : $teks;
    return "<style border=\"thin\"><middle>{$inner}</middle></style>";
}
function s_kosong(): string
{
    return "<style border=\"none\"></style>";
}

// ── 4. Bangun baris (rows) satu sheet ─────────────────────────────────────
$rows = [];
$merges = [];  // [ 'A5:A8', ... ]
$is_first = true;

// Judul dokumen (baris 1-2)
$rows[] = [
    "<style font-size=\"14\"><b><middle><center>REKAP BIMBINGAN PKL – SMK INFORMATIKA SUMEDANG</center></middle></b></style>",
    null,
    null,
    null,
    null,
    null
];
$rows[] = [
    "<style><middle><center>Tahun Ajaran " . date('Y') . "/" . (date('Y') + 1) . " | Dicetak: " . date('d F Y') . "</center></middle></style>",
    null,
    null,
    null,
    null,
    null
];
$merges[] = 'A1:F1';
$merges[] = 'A2:F2';

$current_row = 3; // baris selanjutnya (1-indexed)

foreach ($peta as $id_p => $data_p) {
    // ── Baris kosong pemisah (kecuali pembimbing pertama) ──
    if (!$is_first) {
        $rows[] = [s_kosong(), null, null, null, null, null];
        $merges[] = "A{$current_row}:F{$current_row}";
        $current_row++;
    }
    $is_first = false;

    // ── Baris nama pembimbing (hanya nama) ──
    $rows[] = [
        s_judul_pembimbing($data_p['nama']),
        null,
        null,
        null,
        null,
        null
    ];
    $merges[] = "A{$current_row}:F{$current_row}";
    $current_row++;

    // ── Baris header kolom ──
    $rows[] = [
        s_header_kolom('No.'),
        s_header_kolom('Nama Tempat PKL'),
        s_header_kolom('Telp. DU/DI'),
        s_header_kolom('NIS'),
        s_header_kolom('Nama Siswa'),
        s_header_kolom('No. HP/WA Siswa'),
    ];
    $current_row++;

    if (empty($data_p['tempat'])) {
        $rows[] = [
            s_no('-'),
            "<style border=\"thin\"><i>Belum ada siswa yang ditugaskan</i></style>",
            s_cell(''),
            s_cell(''),
            s_cell(''),
            s_cell('')
        ];
        $current_row++;
    } else {
        $no_siswa = 1;
        foreach ($data_p['tempat'] as $id_t => $data_t) {
            $jml = count($data_t['siswa']);
            $nama_tempat = $data_t['nama_tempat'];
            $no_telp = $data_t['no_telepon_tempat'];
            $start_blok = $current_row;

            foreach ($data_t['siswa'] as $idx => $siswa) {
                if ($idx === 0) {
                    $rows[] = [
                        s_no((string) $no_siswa++),
                        s_tempat($nama_tempat),
                        s_tempat($no_telp),
                        s_cell($siswa['nis'], true),
                        s_cell($siswa['nama_siswa']),
                        s_cell($siswa['kontak_siswa'], true),
                    ];
                } else {
                    $rows[] = [
                        s_no((string) $no_siswa++),
                        null, // merge ke atas
                        null,
                        s_cell($siswa['nis'], true),
                        s_cell($siswa['nama_siswa']),
                        s_cell($siswa['kontak_siswa'], true),
                    ];
                }
                $current_row++;
            }

            // Merge kolom Nama Tempat & No. Telepon jika ada > 1 siswa
            if ($jml > 1) {
                $end_blok = $start_blok + $jml - 1;
                $merges[] = "B{$start_blok}:B{$end_blok}";
                $merges[] = "C{$start_blok}:C{$end_blok}";
            }
        }
    }
}

// ── Baris kosong + catatan kaki ───────────────────────────────────────────
$rows[] = [s_kosong(), null, null, null, null, null];
$merges[] = "A{$current_row}:F{$current_row}";
$current_row++;
$rows[] = [
    "<style border=\"none\"><i>* Dokumen ini digenerate otomatis oleh Sistem Informasi PKL</i></style>",
    null,
    null,
    null,
    null,
    null
];
$merges[] = "A{$current_row}:F{$current_row}";

// ── 5. Buat dan konfigurasi workbook ──────────────────────────────────────
$xlsx = SimpleXLSXGen::fromArray($rows, 'Rekap Bimbingan PKL');
$xlsx->setDefaultFont('Times New Roman');

// Lebar kolom
$xlsx->setColWidth(1, 6);   // No.
$xlsx->setColWidth(2, 35);  // Nama Tempat PKL
$xlsx->setColWidth(3, 22);  // No. Telepon Tempat
$xlsx->setColWidth(4, 16);  // NIS
$xlsx->setColWidth(5, 38);  // Nama Siswa
$xlsx->setColWidth(6, 20);  // No. HP Siswa

// Terapkan semua merge
foreach ($merges as $range) {
    $xlsx->mergeCells($range);
}

// ── 6. Download ───────────────────────────────────────────────────────────
$filename = 'Rekap_Bimbingan_PKL_' . date('Y-m-d') . '.xlsx';
$xlsx->downloadAs($filename);
exit;
?>