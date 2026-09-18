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

// 4. Progress Nilai Sidang
$queryNilaiSidang = "SELECT COUNT(id_siswa) as total FROM nilai_pkl WHERE nilai_sidang IS NOT NULL";
$resNilaiSidang = $koneksi->query($queryNilaiSidang);
$totalNilaiSidang = ($resNilaiSidang) ? (int)$resNilaiSidang->fetch_assoc()['total'] : 0;
$persenSidang = ($totalSiswa > 0) ? round(($totalNilaiSidang / $totalSiswa) * 100) : 0;

// 5. Progress Pembimbing (Sekolah & DU/DI)
$queryProgressPb = "
    SELECT 
        p.id_pembimbing,
        p.nama_pembimbing,
        COUNT(s.id_siswa) as total_bimbingan,
        SUM(CASE WHEN n.nilai_pembimbing_sekolah != 0 AND n.nilai_pembimbing_dudi != 0 THEN 1 ELSE 0 END) as nilai_diinput
    FROM 
        pembimbing p
    LEFT JOIN 
        siswa s ON p.id_pembimbing = s.id_pembimbing
    LEFT JOIN 
        nilai_pkl n ON s.id_siswa = n.id_siswa
    GROUP BY 
        p.id_pembimbing, p.nama_pembimbing
    ORDER BY 
        p.nama_pembimbing ASC
";
$resProgressPb = $koneksi->query($queryProgressPb);
$progressPembimbing = [];
if ($resProgressPb) {
    while ($row = $resProgressPb->fetch_assoc()) {
        $progressPembimbing[] = $row;
    }
}
?>
<h1 class="mt-4">Dashboard Utama <i class="fas fa-tachometer-alt text-primary"></i></h1>
<p class="lead">Selamat datang kembali di Sistem Informasi PKL SMK Informatika Sumedang.</p>

<!-- Baris Kartu Ringkasan Utama (4 Kolom Responsif) -->
<div class="row">
    <!-- Card Jumlah Siswa -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card text-white bg-primary shadow-sm h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h2 class="card-title mb-1"><?= $totalSiswa ?></h2>
                        <p class="card-text mb-0 fw-semibold">Total Siswa PKL</p>
                        <small class="text-white-50"><?= $totalSiswaDiterima ?> Telah diterima PKL</small>
                    </div>
                    <div class="col-4 text-end">
                        <i class="fas fa-user-graduate fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Tempat PKL -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card text-white bg-info shadow-sm h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h2 class="card-title mb-1"><?= $totalTempat ?></h2>
                        <p class="card-text mb-0 fw-semibold">Total Tempat PKL</p>
                        <small class="text-white-50"><?= $totalTempatTerisi ?> Tempat terisi siswa</small>
                    </div>
                    <div class="col-4 text-end">
                        <i class="fas fa-building fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Surat Keluar -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card text-white bg-success shadow-sm h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h2 class="card-title mb-1"><?= $totalSurat ?></h2>
                        <p class="card-text mb-0 fw-semibold">Total Surat Keluar</p>
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

    <!-- Card Pembimbing -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card text-white bg-warning shadow-sm h-100">
            <div class="card-body text-dark">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h2 class="card-title mb-1 text-dark"><?= $totalPembimbing ?></h2>
                        <p class="card-text mb-0 fw-semibold text-dark">Total Pembimbing</p>
                        <small class="text-muted">Pembimbing Sekolah</small>
                    </div>
                    <div class="col-4 text-end text-dark">
                        <i class="fas fa-chalkboard-teacher fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<hr class="my-4 opacity-25">

<!-- ========================================================================= -->
<!-- --- SEKSI PROGRESS INPUT NILAI --- -->
<!-- ========================================================================= -->
<div class="row">
    <div class="col-12">
        <h4 class="mb-4 text-primary fw-bold"><i class="fas fa-tasks me-2"></i> Progress Input Nilai</h4>
    </div>
</div>

<div class="row">
    <!-- Progress Nilai Sidang -->
    <div class="col-12 mb-4">
        <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 38px; height: 38px;">
                    <i class="fas fa-gavel"></i>
                </div>
                <div>
                    <h6 class="m-0 font-weight-bold text-primary">Progress Nilai Sidang PKL</h6>
                    <small class="text-muted">Persentase pengisian nilai sidang oleh penguji / admin</small>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small font-weight-bold text-dark text-uppercase">Total Siswa Dinilai</span>
                    <strong class="text-primary fs-6"><?= $totalNilaiSidang ?> / <?= $totalSiswa ?> Siswa (<?= $persenSidang ?>%)</strong>
                </div>
                <div class="progress" style="height: 14px; border-radius: 7px; background-color: #e9ecef;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: <?= $persenSidang ?>%" aria-valuenow="<?= $persenSidang ?>" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Progress Pembimbing Sekolah & DU/DI -->
    <div class="col-12 mb-4">
        <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center">
                <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 38px; height: 38px;">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div>
                    <h6 class="m-0 font-weight-bold text-info">Progress Nilai Pembimbing (Sekolah & DU/DI)</h6>
                    <small class="text-muted">Rekapitulasi kelengkapan nilai dari pembimbing sekolah dan instansi DU/DI</small>
                </div>
            </div>
            <div class="card-body p-4 bg-light">
                <div class="row">
                <?php if (empty($progressPembimbing)): ?>
                    <div class="col-12 text-center text-muted py-4">
                        <i class="fas fa-inbox fa-3x mb-3 text-secondary opacity-50"></i>
                        <p class="mb-0">Belum ada data pembimbing.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($progressPembimbing as $pb): ?>
                        <?php
                            $totalBimbingan = (int)$pb['total_bimbingan'];
                            $nilaiDiinput = (int)($pb['nilai_diinput'] ?? 0);
                            $persenPb = ($totalBimbingan > 0) ? round(($nilaiDiinput / $totalBimbingan) * 100) : 0;
                            
                            // Pewarnaan progress bar dinamis
                            if ($persenPb == 100) $bgClass = 'bg-success';
                            elseif ($persenPb >= 50) $bgClass = 'bg-info';
                            elseif ($persenPb > 0) $bgClass = 'bg-warning text-dark';
                            else $bgClass = 'bg-danger';
                        ?>
                        <div class="col-xl-6 mb-3">
                            <div class="bg-white p-3 rounded shadow-sm border h-100" style="transition: transform 0.2s, box-shadow 0.2s;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white <?= $bgClass ?> shadow-sm flex-shrink-0" style="width: 44px; height: 44px;">
                                            <span class="small fw-bold"><?= $persenPb ?>%</span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold text-dark"><?= htmlspecialchars($pb['nama_pembimbing']) ?></h6>
                                            <small class="text-muted"><i class="fas fa-user-graduate me-1 text-primary"></i><?= $totalBimbingan ?> Siswa Bimbingan</small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-light text-dark border p-2">
                                            <i class="fas fa-check-circle text-success me-1"></i><?= $nilaiDiinput ?> Dinilai
                                        </span>
                                    </div>
                                </div>
                                <div class="progress mt-2" style="height: 10px; border-radius: 5px; background-color: #f1f3f5;">
                                    <div class="progress-bar <?= $bgClass ?>" role="progressbar" style="width: <?= $persenPb ?>%" aria-valuenow="<?= $persenPb ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
