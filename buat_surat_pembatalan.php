<?php
// buat_surat_pembatalan.php - Form Generate Surat Pembatalan PKL
include 'koneksi.php';

// Ambil id surat referensi (jika ada dari URL)
$id_surat_ref = isset($_GET['id_surat_ref']) ? intval($_GET['id_surat_ref']) : 0;
$ref_no_surat = '';
$ref_tanggal = '';
$ref_nama_tempat = '';
$ref_id_tempat = 0;
$siswa_list = [];

if ($id_surat_ref > 0) {
    // Ambil data surat dan tempat PKL
    $query_surat = "SELECT s.no_surat, s.tanggal, s.id_tempat_pkl, t.nama_tempat 
                    FROM surat s 
                    LEFT JOIN tempat_pkl t ON s.id_tempat_pkl = t.id_tempat 
                    WHERE s.id_surat = $id_surat_ref";
    $res_surat = $koneksi->query($query_surat);
    if ($res_surat && $res_surat->num_rows > 0) {
        $data_surat = $res_surat->fetch_assoc();
        $ref_no_surat = $data_surat['no_surat'];
        $ref_tanggal = $data_surat['tanggal'];
        $ref_id_tempat = $data_surat['id_tempat_pkl'];
        $ref_nama_tempat = $data_surat['nama_tempat'];
        
        // Ambil data siswa yang terlampir pada surat tersebut
        $query_siswa = "SELECT s.id_siswa, s.nis, s.nama_siswa, s.kelas 
                        FROM siswa_surat ss 
                        JOIN siswa s ON ss.id_siswa = s.id_siswa 
                        WHERE ss.id_surat = $id_surat_ref";
        $res_siswa = $koneksi->query($query_siswa);
        if ($res_siswa) {
            while ($row = $res_siswa->fetch_assoc()) {
                $siswa_list[] = $row;
            }
        }
    }
}

// Ambil Data Settings
$settings = [];
$format_nomor_surat = '/PAN-PKL/SMK-IF/YPS/X/2025';
$query_settings = "SELECT nama_sekolah, tgl_mulai, tgl_selesai, nama_kepsek, format_nomor_surat FROM settings LIMIT 1";
$result_settings = $koneksi->query($query_settings);

if ($result_settings && $result_settings->num_rows > 0) {
    $settings = $result_settings->fetch_assoc();
    $format_nomor_surat = $settings['format_nomor_surat'];
}

// Nomor Urut untuk Surat Pembatalan Baru
$nomor_urut_terakhir = 0;
$query_last_number = "SELECT CAST(SUBSTR(no_surat, 1, 3) AS UNSIGNED) AS nomor_urut_tertinggi FROM surat ORDER BY nomor_urut_tertinggi DESC LIMIT 1";
$result_last_number = $koneksi->query($query_last_number);
if ($result_last_number && $result_last_number->num_rows > 0) {
    $row = $result_last_number->fetch_assoc();
    $nomor_urut_terakhir = $row['nomor_urut_tertinggi'];
}
$nomor_surat_baru = sprintf('%03d', $nomor_urut_terakhir + 1) . $format_nomor_surat;
$tanggal_hari_ini = date('Y-m-d');
$koneksi->close();
?>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-danger text-white">
        <h5 class="mb-0"><i class="fas fa-ban me-2"></i> Form Pengajuan Surat Pembatalan PKL</h5>
    </div>
    <div class="card-body container-form">
        <form action="generate_surat_pembatalan.php" method="POST" target="_blank">

            <fieldset class="mb-4 p-3 border rounded">
                <legend class="float-none w-auto px-2 fs-6 text-danger">Informasi Surat Pembatalan</legend>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="nomor_surat" class="form-label">Nomor Surat Baru</label>
                        <input type="text" class="form-control" id="nomor_surat_display" value="<?php echo $nomor_surat_baru; ?>" disabled>
                        <input type="hidden" name="nomor_surat" value="<?php echo $nomor_surat_baru; ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="tanggal_surat" class="form-label">Tanggal Surat Baru</label>
                        <input type="date" class="form-control" id="tanggal_surat" name="tanggal_surat" value="<?php echo $tanggal_hari_ini; ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label for="perihal" class="form-label">Perihal</label>
                        <input type="text" name="perihal" id="perihal" class="form-control bg-light" value="Pemberitahuan Pembatalan Siswa Praktik Kerja Lapangan (PKL)" readonly>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label text-danger">Nomor Surat Sebelumnya</label>
                        <input type="text" class="form-control bg-light" id="display_no_surat_referensi" value="<?php echo htmlspecialchars($ref_no_surat); ?>" readonly>
                        <input type="hidden" name="no_surat_referensi" value="<?php echo htmlspecialchars($ref_no_surat); ?>">
                        <input type="hidden" name="id_surat_ref" value="<?php echo $id_surat_ref; ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-danger">Tanggal Surat Sebelumnya</label>
                        <input type="date" class="form-control bg-light" id="display_tanggal_referensi" value="<?php echo htmlspecialchars($ref_tanggal); ?>" readonly>
                        <input type="hidden" name="tanggal_surat_referensi" value="<?php echo htmlspecialchars($ref_tanggal); ?>">
                    </div>
                </div>
            </fieldset>

            <fieldset class="mb-4 p-3 border rounded">
                <legend class="float-none w-auto px-2 fs-6 text-info">Data Tempat PKL</legend>
                <div class="mb-3">
                    <label for="nama_perusahaan" class="form-label">Tempat PKL</label>
                    <input type="text" class="form-control bg-light" id="nama_perusahaan" name="nama_perusahaan" value="<?php echo htmlspecialchars($ref_nama_tempat); ?>" readonly>
                    <input type="hidden" name="id_tempat_pkl" value="<?php echo $ref_id_tempat; ?>">
                </div>
                <div class="mb-3">
                    <label for="alamat_perusahaan" class="form-label">Alamat Tempat PKL</label>
                    <textarea class="form-control" id="alamat_perusahaan" name="alamat_perusahaan" rows="2" placeholder="Masukkan alamat lengkap tempat PKL" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="kota_perusahaan" class="form-label">Kota Tujuan Surat</label>
                    <input type="text" class="form-control" id="kota_perusahaan" name="kota_perusahaan" placeholder="Contoh: Sumedang" required>
                </div>
            </fieldset>

            <fieldset class="mb-4 p-3 border rounded">
                <legend class="float-none w-auto px-2 fs-6 text-warning">Daftar Siswa (Pilih yang akan dibatalkan)</legend>
                
                <?php if (empty($siswa_list)): ?>
                    <div class="alert alert-warning">
                        Tidak ada siswa yang ditemukan pada surat sebelumnya. Silakan pilih surat yang valid.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 50px;">
                                        <input class="form-check-input" type="checkbox" id="checkAllStudents" checked>
                                    </th>
                                    <th>NIS</th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($siswa_list as $siswa): ?>
                                    <tr>
                                        <td class="text-center">
                                            <input class="form-check-input student-checkbox" type="checkbox" name="siswa_batal[]" value="<?php echo $siswa['id_siswa']; ?>" checked>
                                            <input type="hidden" name="siswa_detail[<?php echo $siswa['id_siswa']; ?>][nama]" value="<?php echo htmlspecialchars($siswa['nama_siswa']); ?>">
                                            <input type="hidden" name="siswa_detail[<?php echo $siswa['id_siswa']; ?>][kelas]" value="<?php echo htmlspecialchars($siswa['kelas']); ?>">
                                            <input type="hidden" name="siswa_detail[<?php echo $siswa['id_siswa']; ?>][nis]" value="<?php echo htmlspecialchars($siswa['nis']); ?>">
                                        </td>
                                        <td><?php echo htmlspecialchars($siswa['nis']); ?></td>
                                        <td><?php echo htmlspecialchars($siswa['nama_siswa']); ?></td>
                                        <td><?php echo htmlspecialchars($siswa['kelas']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

            </fieldset>

            <div class="d-grid">
                <button type="submit" class="btn btn-danger btn-lg" <?php echo empty($siswa_list) ? 'disabled' : ''; ?>>
                    <i class="fas fa-file-pdf me-2"></i> Generate Surat Pembatalan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    'use strict';

    // Checkbox pilih semua
    $('#checkAllStudents').on('change', function() {
        $('.student-checkbox').prop('checked', $(this).prop('checked'));
    });
    
    // Uncheck "Pilih Semua" jika ada siswa yang di-uncheck manual
    $(document).on('change', '.student-checkbox', function() {
        if (!$(this).prop('checked')) {
            $('#checkAllStudents').prop('checked', false);
        } else {
            if ($('.student-checkbox:checked').length === $('.student-checkbox').length) {
                $('#checkAllStudents').prop('checked', true);
            }
        }
    });
});
</script>
