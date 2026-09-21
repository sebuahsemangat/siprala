<?php
// download_template_pembimbing.php - Download template file Excel (.xlsx) untuk import pembimbing
require 'vendor/autoload.php';

use Shuchkin\SimpleXLSXGen;

$data = [
    ['<b>Nama Pembimbing</b>', '<b>Username (Opsional)</b>', '<b>Kontak Pembimbing</b>'],
    ['Drs. H. Maman Suratman, M.Pd.', 'mamans', '081234567890'],
    ['Siti Aminah, S.Kom., M.Kom.', 'sitia', '081234567891'],
    ['Eko Prasetyo, S.T.', '', '081234567892']
];

$xlsx = SimpleXLSXGen::fromArray($data);
$xlsx->downloadAs('template_import_pembimbing.xlsx');
exit;
