<?php
// download_template_siswa.php - Download template file Excel (.xlsx) untuk import siswa
require 'vendor/autoload.php';

use Shuchkin\SimpleXLSXGen;

$data = [
    ['<b>NIS</b>', '<b>Nama Siswa</b>', '<b>Kelas</b>', '<b>Kontak Siswa</b>'],
    ['23241001', 'Ahmad Fauzi', 'XII-RPL 1', '081234567890'],
    ['23241002', 'Budi Santoso', 'XII-RPL 2', '081234567891'],
    ['23241003', 'Citra Lestari', 'XII-DKV 1', '081234567892']
];

$xlsx = SimpleXLSXGen::fromArray($data);
$xlsx->downloadAs('template_import_siswa.xlsx');
exit;
