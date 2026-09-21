<?php
// settings.php - Konten Form Settings (Dimuat via AJAX)
include 'koneksi.php'; // Pastikan file koneksi.php tersedia

// 1. Ambil Data Settings (id_settings = 1)
$settings = [];
$query_settings = "SELECT id_settings, format_nomor_surat, nama_sekolah, tgl_mulai, tgl_selesai, nama_kepsek FROM settings WHERE id_settings = 1 LIMIT 1";
$result_settings = $koneksi->query($query_settings);

if ($result_settings && $result_settings->num_rows > 0) {
    $settings = $result_settings->fetch_assoc();
} else {
    // Data default jika data tidak ditemukan (untuk menghindari error 'Undefined Index')
    $settings = [
        'id_settings' => 1,
        'format_nomor_surat' => '/PAN-PKL/SMK-IF/YPS/X/2025',
        'nama_sekolah' => 'SMK INFORMATIKA SUMEDANG',
        'tgl_mulai' => date('Y-m-d'),
        'tgl_selesai' => date('Y-m-d', strtotime('+3 months')),
        'nama_kepsek' => 'Tatang Suryana, S.Ag., M.Pd',
    ];
}
$koneksi->close();
?>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-danger text-white">
        <h5 class="mb-0"><i class="fas fa-cog me-2"></i> Pengaturan Sistem (Settings)</h5>
    </div>
    <div class="card-body container-form">
        <form id="settingsForm">

            <input type="hidden" name="id_settings" value="<?php echo $settings['id_settings']; ?>">

            <fieldset class="mb-4 p-3 border rounded">
                <legend class="float-none w-auto px-2 fs-6 text-primary">Informasi Sekolah</legend>

                <div class="mb-3">
                    <label for="nama_sekolah" class="form-label">Nama Sekolah</label>
                    <input type="text" class="form-control" id="nama_sekolah" name="nama_sekolah" value="<?php echo htmlspecialchars($settings['nama_sekolah']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="nama_kepsek" class="form-label">Nama Kepala Sekolah</label>
                    <input type="text" class="form-control" id="nama_kepsek" name="nama_kepsek" value="<?php echo htmlspecialchars($settings['nama_kepsek']); ?>" required>
                </div>
            </fieldset>

            <fieldset class="mb-4 p-3 border rounded">
                <legend class="float-none w-auto px-2 fs-6 text-info">Format Surat & Periode PKL</legend>

                <div class="mb-3">
                    <label for="format_nomor_surat" class="form-label">Format Nomor Surat</label>
                    <input type="text" class="form-control" id="format_nomor_surat" name="format_nomor_surat" value="<?php echo htmlspecialchars($settings['format_nomor_surat']); ?>" required>
                    <small class="form-text text-muted">Contoh: `/PAN-PKL/SMK-IF/YPS/X/2025`. Nomor urut (001) akan ditambahkan secara otomatis di depan.</small>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="tgl_mulai" class="form-label">Tanggal Mulai PKL</label>
                        <input type="text" class="form-control" id="tgl_mulai" name="tgl_mulai" value="<?php echo $settings['tgl_mulai']; ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="tgl_selesai" class="form-label">Tanggal Selesai PKL</label>
                        <input type="text" class="form-control" id="tgl_selesai" name="tgl_selesai" value="<?php echo $settings['tgl_selesai']; ?>" required>
                    </div>
                </div>

            </fieldset>

            <div id="alertMessage" class="mt-3">
                </div>
            
            <div class="d-grid mt-4">
                <button type="submit" class="btn btn-danger btn-lg"><i class="fas fa-save me-2"></i> Simpan Perubahan Pengaturan</button>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    // Script AJAX untuk mengirim data form
    $('#settingsForm').on('submit', function(e) {
        e.preventDefault(); // Mencegah submit form bawaan

        const form = $(this);
        const submitButton = form.find('button[type="submit"]');
        const originalButtonHtml = submitButton.html();
        const alertArea = $('#alertMessage');

        // Tampilkan loading
        submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> Menyimpan...');
        alertArea.empty();

        $.ajax({
            url: 'save_settings.php', // Target file PHP untuk memproses simpan
            method: 'POST',
            data: form.serialize(), // Mengambil semua data dari form
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alertArea.html('<div class="alert alert-success mt-3"><i class="fas fa-check-circle"></i> ' + response.message + '</div>');
                } else {
                    alertArea.html('<div class="alert alert-danger mt-3"><i class="fas fa-times-circle"></i> ' + response.message + '</div>');
                }
            },
            error: function(xhr, status, error) {
                alertArea.html('<div class="alert alert-danger mt-3"><i class="fas fa-exclamation-triangle"></i> Terjadi kesalahan jaringan: ' + status + '</div>');
                console.error("AJAX Error:", status, error);
            },
            complete: function() {
                // Kembalikan tombol ke keadaan semula setelah selesai
                submitButton.prop('disabled', false).html(originalButtonHtml);
                // Hilangkan pesan alert setelah beberapa detik
                setTimeout(() => alertArea.empty(), 5000);
            }
        });
    });
});
</script>