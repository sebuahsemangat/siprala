<?php
// dashboard.php - Konten Dashboard Utama
// File ini hanya berisi konten, tanpa HTML/Head/Body wrapper.
require_once 'koneksi.php';

// 1. Data Siswa
$querySiswa = "SELECT COUNT(*) as total FROM siswa";
$resSiswa = $koneksi->query($querySiswa);
$totalSiswa = $resSiswa->fetch_assoc()['total'];

$querySiswaDiterima = "SELECT COUNT(*) as total FROM siswa WHERE id_tempat > 0";
$resSiswaDiterima = $koneksi->query($querySiswaDiterima);
$totalSiswaDiterima = $resSiswaDiterima->fetch_assoc()['total'];

// 2. Data Tempat PKL
$queryTempat = "SELECT COUNT(*) as total FROM tempat_pkl";
$resTempat = $koneksi->query($queryTempat);
$totalTempat = $resTempat->fetch_assoc()['total'];

$queryTempatTerisi = "SELECT COUNT(DISTINCT id_tempat) as total FROM siswa WHERE id_tempat > 0";
$resTempatTerisi = $koneksi->query($queryTempatTerisi);
$totalTempatTerisi = $resTempatTerisi->fetch_assoc()['total'];

// 3. Data Surat Keluar
$querySurat = "SELECT COUNT(*) as total FROM surat";
$resSurat = $koneksi->query($querySurat);
$totalSurat = $resSurat->fetch_assoc()['total'];

$queryPengajuan = "SELECT COUNT(*) as total FROM surat WHERE perihal LIKE '%Pengajuan%'";
$totalPengajuan = $koneksi->query($queryPengajuan)->fetch_assoc()['total'];

$queryPenambahan = "SELECT COUNT(*) as total FROM surat WHERE perihal LIKE '%Penambahan%'";
$totalPenambahan = $koneksi->query($queryPenambahan)->fetch_assoc()['total'];

$queryPembatalan = "SELECT COUNT(*) as total FROM surat WHERE perihal LIKE '%Pembatalan%'";
$totalPembatalan = $koneksi->query($queryPembatalan)->fetch_assoc()['total'];

// Tambahan: Total Pembimbing
$queryPembimbing = "SELECT COUNT(*) as total FROM pembimbing";
$totalPembimbing = $koneksi->query($queryPembimbing)->fetch_assoc()['total'];
?>
<h1 class="mt-4">Dashboard Utama <i class="fas fa-tachometer-alt text-primary"></i></h1>
<p class="lead">Selamat datang kembali di Sistem Informasi PKL SMK Informatika Sumedang.</p>

<div class="row">
    <!-- Card Jumlah Siswa -->
    <div class="col-md-4 mb-4">
        <div class="card text-white bg-primary shadow-sm h-100">
            <div class="card-body">
                <div class="row">
                    <div class="col-8">
                        <h2 class="card-title"><?= $totalSiswa ?></h2>
                        <p class="card-text mb-0">Total Siswa PKL</p>
                        <small class="text-white-50"><?= $totalSiswaDiterima ?> Telah diterima di tempat PKL</small>
                    </div>
                    <div class="col-4 text-end">
                        <i class="fas fa-user-graduate fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Tempat PKL -->
    <div class="col-md-4 mb-4">
        <div class="card text-white bg-info shadow-sm h-100">
            <div class="card-body">
                <div class="row">
                    <div class="col-8">
                        <h2 class="card-title"><?= $totalTempat ?></h2>
                        <p class="card-text mb-0">Total Tempat PKL</p>
                        <small class="text-white-50"><?= $totalTempatTerisi ?> Tempat telah terisi siswa</small>
                    </div>
                    <div class="col-4 text-end">
                        <i class="fas fa-building fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Surat Keluar -->
    <div class="col-md-4 mb-4">
        <div class="card text-white bg-success shadow-sm h-100">
            <div class="card-body">
                <div class="row">
                    <div class="col-8">
                        <h2 class="card-title"><?= $totalSurat ?></h2>
                        <p class="card-text mb-0">Total Surat Keluar</p>
                        <div class="small text-white-50" style="line-height: 1.2;">
                            P: <?= $totalPengajuan ?> | T: <?= $totalPenambahan ?> | B: <?= $totalPembatalan ?>
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <i class="fas fa-envelope-open-text fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Card Tambahan: Pembimbing -->
    <div class="col-md-4 mb-4">
        <div class="card text-white bg-warning shadow-sm h-100">
            <div class="card-body">
                <div class="row">
                    <div class="col-8">
                        <h2 class="card-title text-dark"><?= $totalPembimbing ?></h2>
                        <p class="card-text text-dark mb-0">Total Pembimbing</p>
                    </div>
                    <div class="col-4 text-end text-dark">
                        <i class="fas fa-chalkboard-teacher fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer text-dark clearfix small-box-footer d-flex justify-content-between align-items-center">
                <span>Pembimbing Sekolah</span>
            </div>
        </div>
    </div>
</div>
