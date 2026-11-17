<?php
// statistik.php - Halaman Statistik Penempatan PKL
// File ini hanya berisi konten, tanpa HTML/Head/Body wrapper.
// Dimuat via AJAX dari index.php, sama seperti dashboard.php.
//
// Sumber data: tabel siswa + tempat_pkl + siswa_surat.
// Definisi status mengikuti data_siswa.php dan file Dashboard PKL:
//   - Diterima           : siswa.id_tempat sudah terisi (nama tempat tampil)
//   - Surat Pending      : id_tempat kosong, status surat terakhir = pending
//   - Belum Diajukan     : id_tempat kosong dan belum pernah ada surat
//   - Surat Ditolak      : id_tempat kosong, status surat terakhir = ditolak
//   - Surat Dibatalkan   : id_tempat kosong, status surat terakhir = dibatalkan
//   - Menunggu penempatan: id_tempat kosong, status surat terakhir = diterima.
//                          Dihitung sebagai Belum ditempatkan (tempat belum terisi),
//                          tapi ditampilkan sebagai baris sendiri supaya ketahuan
//                          siapa saja mereka.
// Sektor tempat PKL sengaja tidak ditampilkan (tidak ada kolomnya di database).
// Kolom Kontak Siswa dan Pembimbing sengaja tidak ditampilkan di halaman ini.
session_start();
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    exit('Akses ditolak. Silakan login terlebih dahulu.');
}

require_once 'koneksi.php';

// ---------- 1. Ambil semua siswa + status surat terakhirnya ----------
// Satu query saja; semua angka di halaman ini dihitung dari hasil yang sama
// supaya tidak ada selisih antar bagian.
$query = "
    SELECT
        s.id_siswa,
        s.nis,
        s.nama_siswa,
        s.kelas,
        s.id_tempat,
        tp.nama_tempat,
        (
            SELECT ss.status
            FROM siswa_surat ss
            WHERE ss.id_siswa = s.id_siswa
            ORDER BY ss.id_surat_siswa DESC
            LIMIT 1
        ) AS status_terakhir,
        (
            SELECT tp2.nama_tempat
            FROM siswa_surat ss2
            JOIN surat su2 ON su2.id_surat = ss2.id_surat
            LEFT JOIN tempat_pkl tp2 ON tp2.id_tempat = su2.id_tempat_pkl
            WHERE ss2.id_siswa = s.id_siswa
            ORDER BY ss2.id_surat_siswa DESC
            LIMIT 1
        ) AS tempat_diajukan
    FROM siswa s
    LEFT JOIN tempat_pkl tp ON tp.id_tempat = s.id_tempat
    ORDER BY s.kelas ASC, s.nama_siswa ASC
";

$result = $koneksi->query($query);
$siswa = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $siswa[] = $row;
    }
}

// Total tempat PKL yang terdaftar (untuk kartu Mitra).
$totalTempatTerdaftar = 0;
$resTempat = $koneksi->query("SELECT COUNT(*) AS total FROM tempat_pkl");
if ($resTempat) {
    $totalTempatTerdaftar = (int) $resTempat->fetch_assoc()['total'];
}

// ---------- 2. Klasifikasi + agregasi (satu sumber kebenaran) ----------
function kategori_siswa($row)
{
    if ((int) $row['id_tempat'] > 0) {
        return 'diterima';
    }
    $st = strtolower(trim((string) ($row['status_terakhir'] ?? '')));
    if ($st === 'pending') {
        return 'pending';
    }
    if ($st === 'ditolak') {
        return 'ditolak';
    }
    if ($st === 'dibatalkan') {
        return 'dibatalkan';
    }
    if ($st === 'diterima') {
        return 'menunggu'; // surat diterima, tempat belum diisi
    }
    return 'belum'; // belum pernah diajukan
}

function jurusan_dari_kelas($kelas)
{
    $kelas = trim((string) $kelas);
    if (preg_match('/\b(RPL|DKV|TKJ|MM|OTKP|AKL|BDP)\b/i', $kelas, $m)) {
        return strtoupper($m[1]);
    }
    return 'Lainnya';
}

// Label, warna badge, dan warna bar untuk tiap kategori.
$metaKategori = [
    'diterima'   => ['label' => 'Diterima', 'badge' => 'bg-success', 'bar' => '#198754'],
    'pending'    => ['label' => 'Surat Pending', 'badge' => 'bg-warning text-dark', 'bar' => '#ffc107'],
    'belum'      => ['label' => 'Belum Diajukan', 'badge' => 'bg-secondary', 'bar' => '#6c757d'],
    'ditolak'    => ['label' => 'Surat Ditolak', 'badge' => 'bg-danger', 'bar' => '#dc3545'],
    'dibatalkan' => ['label' => 'Surat Dibatalkan', 'badge' => 'bg-dark', 'bar' => '#212529'],
    'menunggu'   => ['label' => 'Surat Diterima — menunggu penempatan', 'badge' => 'bg-info text-white', 'bar' => '#0dcaf0'],
];
// Urutan tampil di grafik/tabel status (yang penting dulu).
$urutanStatus = ['diterima', 'pending', 'belum', 'ditolak', 'dibatalkan', 'menunggu'];

$totalSiswa = count($siswa);
$jumlahStatus = array_fill_keys(array_keys($metaKategori), 0);
$perKelas = [];    // kelas => [kategori => n, 'total' => n]
$perJurusan = [];  // jurusan => ['diterima' => n, 'belum' => n, 'total' => n]
$perTempat = [];   // nama tempat => ['RPL'=>n, 'DKV'=>n, 'Lainnya'=>n, 'total'=>n]
$belumDitempatkan = [];

foreach ($siswa as $row) {
    $kat = kategori_siswa($row);
    $row['_kategori'] = $kat;
    $row['_jurusan'] = jurusan_dari_kelas($row['kelas']);
    $jumlahStatus[$kat]++;

    $kelas = trim((string) $row['kelas']);
    if (!isset($perKelas[$kelas])) {
        $perKelas[$kelas] = array_merge(array_fill_keys(array_keys($metaKategori), 0), ['total' => 0]);
    }
    $perKelas[$kelas][$kat]++;
    $perKelas[$kelas]['total']++;

    $jur = $row['_jurusan'];
    if (!isset($perJurusan[$jur])) {
        $perJurusan[$jur] = ['diterima' => 0, 'belum' => 0, 'total' => 0];
    }
    if ($kat === 'diterima') {
        $perJurusan[$jur]['diterima']++;
    } else {
        $perJurusan[$jur]['belum']++;
    }
    $perJurusan[$jur]['total']++;

    if ($kat === 'diterima') {
        $tempat = trim((string) $row['nama_tempat']);
        if ($tempat === '') {
            $tempat = '(Tanpa nama tempat)';
        }
        if (!isset($perTempat[$tempat])) {
            $perTempat[$tempat] = ['RPL' => 0, 'DKV' => 0, 'Lainnya' => 0, 'total' => 0];
        }
        $perTempat[$tempat][$jur]++;
        $perTempat[$tempat]['total']++;
    } else {
        $belumDitempatkan[] = $row;
    }
}

ksort($perKelas);
ksort($perJurusan);

$totalDiterima = $jumlahStatus['diterima'];
$totalBelum = $totalSiswa - $totalDiterima;
$mitraTerisi = count($perTempat);
$rataPerTempat = ($mitraTerisi > 0) ? round($totalDiterima / $mitraTerisi, 1) : 0;
$terbanyakTempat = 0;
foreach ($perTempat as $t) {
    if ($t['total'] > $terbanyakTempat) {
        $terbanyakTempat = $t['total'];
    }
}
$persenDiterima = ($totalSiswa > 0) ? round($totalDiterima / $totalSiswa * 100, 1) : 0;
$persenBelum = ($totalSiswa > 0) ? round($totalBelum / $totalSiswa * 100, 1) : 0;

// 10 tempat dengan siswa terbanyak.
uasort($perTempat, function ($a, $b) {
    if ($a['total'] === $b['total']) {
        return 0;
    }
    return ($a['total'] > $b['total']) ? -1 : 1;
});
$topTempat = array_slice($perTempat, 0, 10, true);

// Daftar kelas untuk filter tabel bawah.
$daftarKelas = array_keys($perKelas);

function persen($n, $total)
{
    if ($total <= 0) {
        return 0;
    }
    return round($n / $total * 100, 1);
}
function fmt_persen($n, $total)
{
    return number_format(persen($n, $total), 1, ',', '.') . '%';
}
?>
<style>
    .stat-bar-track { background: #e9ecef; border-radius: .375rem; overflow: hidden; }
    .stat-bar-fill { height: 100%; border-radius: .375rem; }
    .stat-stack { display: flex; height: 14px; border-radius: .375rem; overflow: hidden; background: #e9ecef; }
    .stat-stack > div { height: 100%; }
    .stat-legend-dot { display: inline-block; width: 10px; height: 10px; border-radius: 2px; margin-right: 4px; vertical-align: middle; }
</style>

<h1 class="mt-4">Statistik Penempatan PKL <i class="fas fa-chart-bar text-primary"></i></h1>
<p class="lead">Ringkasan penempatan PKL siswa kelas XII SMK Informatika Sumedang.</p>
<p class="text-muted small">Diterima = tempat PKL sudah terisi. Status lain berarti belum ditempatkan. Semua angka dihitung langsung dari database saat halaman dibuka.</p>

<!-- Baris Kartu Ringkasan Utama -->
<div class="row">
    <div class="col-xl col-md-6 mb-4">
        <div class="card text-white bg-primary shadow-sm h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h2 class="card-title mb-1"><?= $totalSiswa ?></h2>
                        <p class="card-text mb-0 fw-semibold">Total Siswa PKL</p>
                        <small class="text-white-50"><?= ($perJurusan['RPL']['total'] ?? 0) ?> RPL &middot; <?= ($perJurusan['DKV']['total'] ?? 0) ?> DKV</small>
                    </div>
                    <div class="col-4 text-end"><i class="fas fa-user-graduate fa-3x opacity-50"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl col-md-6 mb-4">
        <div class="card text-white bg-success shadow-sm h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h2 class="card-title mb-1"><?= $totalDiterima ?></h2>
                        <p class="card-text mb-0 fw-semibold">Sudah Diterima</p>
                        <small class="text-white-50"><?= number_format($persenDiterima, 1, ',', '.') ?>% dari seluruh siswa</small>
                    </div>
                    <div class="col-4 text-end"><i class="fas fa-check-circle fa-3x opacity-50"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl col-md-6 mb-4">
        <div class="card text-white bg-warning shadow-sm h-100">
            <div class="card-body text-dark">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h2 class="card-title mb-1 text-dark"><?= $totalBelum ?></h2>
                        <p class="card-text mb-0 fw-semibold text-dark">Belum Ditempatkan</p>
                        <small class="text-muted"><?= number_format($persenBelum, 1, ',', '.') ?>% dari seluruh siswa</small>
                    </div>
                    <div class="col-4 text-end text-dark"><i class="fas fa-clock fa-3x opacity-50"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl col-md-6 mb-4">
        <div class="card text-white bg-info shadow-sm h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h2 class="card-title mb-1"><?= $mitraTerisi ?></h2>
                        <p class="card-text mb-0 fw-semibold">Mitra Tempat PKL</p>
                        <small class="text-white-50">dari <?= $totalTempatTerdaftar ?> tempat terdaftar</small>
                    </div>
                    <div class="col-4 text-end"><i class="fas fa-building fa-3x opacity-50"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl col-md-6 mb-4">
        <div class="card text-white bg-secondary shadow-sm h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h2 class="card-title mb-1"><?= number_format($rataPerTempat, 1, ',', '.') ?></h2>
                        <p class="card-text mb-0 fw-semibold">Rata-rata Siswa / Tempat</p>
                        <small class="text-white-50">Terbanyak: <?= $terbanyakTempat ?> siswa</small>
                    </div>
                    <div class="col-4 text-end"><i class="fas fa-users fa-3x opacity-50"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status penempatan + per jurusan -->
<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white fw-semibold"><i class="fas fa-chart-bar me-2 text-primary"></i>Status penempatan seluruh siswa</div>
            <div class="card-body">
                <?php foreach ($urutanStatus as $kat): ?>
                    <?php $n = $jumlahStatus[$kat]; ?>
                    <?php if ($kat === 'menunggu' && $n === 0) continue; ?>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span><?= htmlspecialchars($metaKategori[$kat]['label']) ?></span>
                            <span class="fw-semibold"><?= $n ?> siswa (<?= fmt_persen($n, $totalSiswa) ?>)</span>
                        </div>
                        <div class="stat-bar-track" style="height: 18px;">
                            <div class="stat-bar-fill" style="width: <?= persen($n, $totalSiswa) ?>%; background: <?= $metaKategori[$kat]['bar'] ?>;"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <table class="table table-sm table-bordered mt-3 mb-0">
                    <thead class="table-light">
                        <tr><th>Status</th><th class="text-center">Jumlah</th><th class="text-center">% siswa</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($urutanStatus as $kat): ?>
                            <?php if ($kat === 'menunggu' && $jumlahStatus[$kat] === 0) continue; ?>
                            <tr>
                                <td><?= htmlspecialchars($metaKategori[$kat]['label']) ?></td>
                                <td class="text-center"><?= $jumlahStatus[$kat] ?></td>
                                <td class="text-center"><?= fmt_persen($jumlahStatus[$kat], $totalSiswa) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="fw-semibold table-light">
                            <td>Total</td>
                            <td class="text-center"><?= $totalSiswa ?></td>
                            <td class="text-center">100,0%</td>
                        </tr>
                    </tbody>
                </table>
                <?php if ($jumlahStatus['menunggu'] > 0): ?>
                    <p class="text-muted small mt-2 mb-0">* "Menunggu penempatan" = surat sudah diterima tetapi tempat PKL belum diisi. Mereka dihitung sebagai Belum ditempatkan.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white fw-semibold"><i class="fas fa-graduation-cap me-2 text-primary"></i>Diterima dan belum ditempatkan per jurusan</div>
            <div class="card-body">
                <table class="table table-sm table-bordered">
                    <thead class="table-light">
                        <tr><th>Jurusan</th><th class="text-center">Diterima</th><th class="text-center">Belum</th><th class="text-center">Total</th><th class="text-center">% diterima</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($perJurusan as $jur => $j): ?>
                            <tr>
                                <td><?= htmlspecialchars($jur) ?></td>
                                <td class="text-center"><?= $j['diterima'] ?></td>
                                <td class="text-center"><?= $j['belum'] ?></td>
                                <td class="text-center"><?= $j['total'] ?></td>
                                <td class="text-center"><?= fmt_persen($j['diterima'], $j['total']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="fw-semibold table-light">
                            <td>Total</td>
                            <td class="text-center"><?= $totalDiterima ?></td>
                            <td class="text-center"><?= $totalBelum ?></td>
                            <td class="text-center"><?= $totalSiswa ?></td>
                            <td class="text-center"><?= fmt_persen($totalDiterima, $totalSiswa) ?></td>
                        </tr>
                    </tbody>
                </table>
                <?php foreach ($perJurusan as $jur => $j): ?>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span><?= htmlspecialchars($jur) ?></span>
                            <span><?= $j['diterima'] ?> diterima &middot; <?= $j['belum'] ?> belum</span>
                        </div>
                        <div class="stat-stack">
                            <div style="width: <?= persen($j['diterima'], $j['total']) ?>%; background: #198754;"></div>
                            <div style="width: <?= persen($j['belum'], $j['total']) ?>%; background: #ffc107;"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <p class="text-muted small mb-0"><span class="stat-legend-dot" style="background:#198754;"></span>Diterima &nbsp; <span class="stat-legend-dot" style="background:#ffc107;"></span>Belum ditempatkan</p>
            </div>
        </div>
    </div>
</div>

<!-- Status per kelas -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white fw-semibold"><i class="fas fa-school me-2 text-primary"></i>Status per kelas</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-bordered table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Kelas</th>
                        <th class="text-center">Diterima</th>
                        <th class="text-center">Surat Pending</th>
                        <th class="text-center">Belum Diajukan</th>
                        <th class="text-center">Surat Ditolak</th>
                        <th class="text-center">Surat Dibatalkan</th>
                        <?php if ($jumlahStatus['menunggu'] > 0): ?><th class="text-center">Menunggu</th><?php endif; ?>
                        <th class="text-center">Total</th>
                        <th class="text-center">% diterima</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($perKelas as $kelas => $k): ?>
                        <tr>
                            <td><?= htmlspecialchars($kelas) ?></td>
                            <td class="text-center"><?= $k['diterima'] ?></td>
                            <td class="text-center"><?= $k['pending'] ?></td>
                            <td class="text-center"><?= $k['belum'] ?></td>
                            <td class="text-center"><?= $k['ditolak'] ?></td>
                            <td class="text-center"><?= $k['dibatalkan'] ?></td>
                            <?php if ($jumlahStatus['menunggu'] > 0): ?><td class="text-center"><?= $k['menunggu'] ?></td><?php endif; ?>
                            <td class="text-center fw-semibold"><?= $k['total'] ?></td>
                            <td class="text-center"><?= fmt_persen($k['diterima'], $k['total']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="table-light fw-semibold">
                    <tr>
                        <td>Total</td>
                        <td class="text-center"><?= $jumlahStatus['diterima'] ?></td>
                        <td class="text-center"><?= $jumlahStatus['pending'] ?></td>
                        <td class="text-center"><?= $jumlahStatus['belum'] ?></td>
                        <td class="text-center"><?= $jumlahStatus['ditolak'] ?></td>
                        <td class="text-center"><?= $jumlahStatus['dibatalkan'] ?></td>
                        <?php if ($jumlahStatus['menunggu'] > 0): ?><td class="text-center"><?= $jumlahStatus['menunggu'] ?></td><?php endif; ?>
                        <td class="text-center"><?= $totalSiswa ?></td>
                        <td class="text-center"><?= fmt_persen($totalDiterima, $totalSiswa) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <h6 class="mt-4 mb-3">Komposisi status per kelas (100%)</h6>
        <?php foreach ($perKelas as $kelas => $k): ?>
            <div class="row align-items-center mb-2">
                <div class="col-3 col-md-2 small text-end"><?= htmlspecialchars($kelas) ?></div>
                <div class="col-7 col-md-8">
                    <div class="stat-stack">
                        <?php foreach ($urutanStatus as $kat): ?>
                            <?php if ($k[$kat] > 0): ?>
                                <div style="width: <?= persen($k[$kat], $k['total']) ?>%; background: <?= $metaKategori[$kat]['bar'] ?>;" title="<?= htmlspecialchars($metaKategori[$kat]['label']) ?>: <?= $k[$kat] ?>"></div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="col-2 small fw-semibold"><?= fmt_persen($k['diterima'], $k['total']) ?> diterima</div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- 10 tempat terbanyak -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white fw-semibold"><i class="fas fa-building me-2 text-primary"></i>10 tempat PKL dengan siswa terbanyak</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-bordered table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Tempat PKL</th><th class="text-center">RPL</th><th class="text-center">DKV</th><th class="text-center">Total</th><th class="text-center">% dari diterima</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($topTempat as $nama => $t): ?>
                        <tr>
                            <td><?= htmlspecialchars($nama) ?></td>
                            <td class="text-center"><?= $t['RPL'] ?></td>
                            <td class="text-center"><?= $t['DKV'] ?></td>
                            <td class="text-center fw-semibold"><?= $t['total'] ?></td>
                            <td class="text-center"><?= fmt_persen($t['total'], $totalDiterima) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($topTempat)): ?>
                        <tr><td colspan="5" class="text-center text-muted">Belum ada siswa yang ditempatkan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <p class="text-muted small mt-2 mb-0">Daftar lengkap tempat ada di menu Data Tempat PKL.</p>
    </div>
</div>

<!-- Daftar siswa yang belum ditempatkan -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white fw-semibold"><i class="fas fa-user-clock me-2 text-primary"></i>Daftar siswa yang belum ditempatkan (<?= $totalBelum ?> siswa)</div>
    <div class="card-body">
        <div class="row g-2 mb-3">
            <div class="col-md-3">
                <select id="filterKelasStatistik" class="form-select form-select-sm">
                    <option value="">Semua kelas</option>
                    <?php foreach ($daftarKelas as $kls): ?>
                        <option value="<?= htmlspecialchars($kls) ?>"><?= htmlspecialchars($kls) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <select id="filterStatusStatistik" class="form-select form-select-sm">
                    <option value="">Semua status</option>
                    <option value="Pending">Surat Pending</option>
                    <option value="Belum Diajukan">Belum Diajukan</option>
                    <option value="Ditolak">Surat Ditolak</option>
                    <option value="Dibatalkan">Surat Dibatalkan</option>
                    <?php if ($jumlahStatus['menunggu'] > 0): ?><option value="menunggu penempatan">Surat Diterima — menunggu penempatan</option><?php endif; ?>
                </select>
            </div>
        </div>
        <div class="table-responsive">
            <table id="tabelBelumStatistik" class="table table-striped table-hover table-bordered align-middle" style="width:100%">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40px;" class="text-center">No</th>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Status</th>
                        <th>Diajukan ke</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($belumDitempatkan as $row): ?>
                        <?php $kat = $row['_kategori']; ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['nis']) ?></td>
                            <td><strong><?= htmlspecialchars($row['nama_siswa']) ?></strong></td>
                            <td><?= htmlspecialchars($row['kelas']) ?></td>
                            <td><span class="badge <?= $metaKategori[$kat]['badge'] ?>"><?= htmlspecialchars($metaKategori[$kat]['label']) ?></span></td>
                            <td><?= !empty($row['tempat_diajukan']) ? htmlspecialchars($row['tempat_diajukan']) : '<span class="text-muted fst-italic small">—</span>' ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        var tabelBelum = $('#tabelBelumStatistik').DataTable({
            "order": [
                [3, 'asc'],
                [2, 'asc']
            ],
            "columnDefs": [{
                "orderable": false,
                "searchable": false,
                "targets": [0]
            },
            {
                "className": "text-center",
                "targets": [0]
            }],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
            },
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>B<"row"<"col-sm-12"tr>><"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            buttons: [{
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel me-2"></i> Export ke Excel',
                titleAttr: 'Export daftar siswa yang belum ditempatkan ke Excel',
                className: 'btn btn-success',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5]
                },
                title: 'Siswa Belum Ditempatkan PKL SMK Informatika Sumedang',
                filename: 'Siswa_Belum_Ditempatkan_PKL_' + new Date().toISOString().slice(0, 10)
            }],
            "pageLength": 10,
            "lengthMenu": [
                [10, 25, 50, -1],
                [10, 25, 50, "Semua"]
            ],
            "responsive": true
        });

        // Filter kelas (kolom 3) dan status (kolom 4).
        $('#filterKelasStatistik').on('change', function () {
            var val = $(this).val();
            tabelBelum.column(3).search(val ? '^' + $.fn.dataTable.util.escapeRegex(val) + '$' : '', true, false).draw();
        });
        $('#filterStatusStatistik').on('change', function () {
            tabelBelum.column(4).search($(this).val()).draw();
        });
    });
</script>
