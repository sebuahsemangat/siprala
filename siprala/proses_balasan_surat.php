<?php
// proses_balasan_surat.php - Halaman untuk memproses balasan surat PKL

include 'koneksi.php';

// Ambil ID Surat dari parameter GET
$id_surat = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_surat == 0) {
    die("ID Surat tidak valid!");
}

// Ambil data surat
$query_surat = "SELECT s.*, t.nama_tempat, t.id_tempat 
                FROM surat s
                LEFT JOIN tempat_pkl t ON s.id_tempat_pkl = t.id_tempat
                WHERE s.id_surat = ?";
$stmt = $koneksi->prepare($query_surat);
$stmt->bind_param("i", $id_surat);
$stmt->execute();
$result_surat = $stmt->get_result();
$data_surat = $result_surat->fetch_assoc();
$stmt->close();

if (!$data_surat) {
    die("Data surat tidak ditemukan!");
}

// Ambil daftar siswa yang diajukan dalam surat ini
$query_siswa = "SELECT s.id_siswa, s.nis, s.nama_siswa, s.id_tempat,
                ss.status, ss.catatan
                FROM siswa s
                INNER JOIN siswa_surat ss ON s.id_siswa = ss.id_siswa
                WHERE ss.id_surat = ?
                ORDER BY s.nama_siswa ASC";
$stmt_siswa = $koneksi->prepare($query_siswa);
$stmt_siswa->bind_param("i", $id_surat);
$stmt_siswa->execute();
$result_siswa = $stmt_siswa->get_result();
?>

<div class="card shadow-lg mb-4">
    <div class="card-header bg-warning text-dark">
        <h5 class="mb-0"><i class="fas fa-reply me-2"></i> Proses Balasan Surat</h5>
    </div>
    <div class="card-body container-form">
        
        <form id="formBalasan">
            <input type="hidden" name="id_surat" value="<?php echo $id_surat; ?>">
            <input type="hidden" name="id_tempat" value="<?php echo $data_surat['id_tempat']; ?>">
            <fieldset class="mb-4 p-3 border rounded">
                <legend class="float-none w-auto px-2 fs-6 text-warning">Detail Surat</legend>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nomor Surat:</label>
                        <p class="form-control-static"><strong><?php echo htmlspecialchars($data_surat['no_surat']); ?></strong> | <?php echo htmlspecialchars($data_surat['perihal']); ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tujuan Tempat PKL:</label>
                        <p class="form-control-static"><strong><?php echo htmlspecialchars($data_surat['nama_tempat']); ?></strong></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Surat:</label>
                        <p class="form-control-static"><?php echo date('d-m-Y', strtotime($data_surat['tanggal'])); ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status Saat Ini:</label>
                        <p class="form-control-static"><span class="badge bg-secondary"><?php echo htmlspecialchars($data_surat['status_balasan']); ?></span></p>
                    </div>
                </div>
            </fieldset>

            <fieldset class="mb-4 p-3 border rounded">
                <legend class="float-none w-auto px-2 fs-6 text-success">Hasil Balasan Siswa</legend>
                
                <?php if ($result_siswa->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th style="width: 50px;">No.</th>
                                <th style="width: 150px;">NIS</th>
                                <th>Nama Siswa</th>
                                <th style="width: 150px;">Status Balasan</th>
                                <th>Catatan Tambahan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; while ($siswa = $result_siswa->fetch_assoc()): ?>
                            <tr>
                                <td class="text-center"><?php echo $no++; ?></td>
                                <td><?php echo htmlspecialchars($siswa['nis']); ?></td>
                                <td><strong><?php echo htmlspecialchars($siswa['nama_siswa']); ?></strong></td>
                                <td>
                                    <select class="form-select form-select-sm" name="siswa[<?php echo $siswa['id_siswa']; ?>][status]" required>
                                        <option value="pending" <?php echo ($siswa['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                        <option value="diterima" <?php echo ($siswa['status'] == 'diterima') ? 'selected' : ''; ?>>Diterima</option>
                                        <option value="ditolak" <?php echo ($siswa['status'] == 'ditolak') ? 'selected' : ''; ?>>Ditolak</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" 
                                           name="siswa[<?php echo $siswa['id_siswa']; ?>][catatan]" 
                                           class="form-control form-control-sm" 
                                           placeholder="Catatan (opsional)"
                                           value="<?php echo htmlspecialchars($siswa['catatan'] ?? ''); ?>">
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-2"></i> Simpan Balasan
                    </button>
                </div>
                
                <?php else: ?>
                    <div class="alert alert-info">Tidak ada siswa yang terdaftar dalam surat ini.</div>
                <?php endif; ?>
            </fieldset>
        </form>
        
    </div>
</div>


<script>
// Pastikan jQuery sudah dimuat sebelum skrip ini
if (typeof jQuery == 'undefined') {
    console.error('jQuery belum dimuat! Pastikan Anda memuat jQuery sebelum skrip ini.');
}

// Event handler untuk submit form AJAX
document.getElementById('formBalasan')?.addEventListener('submit', function(e) {
    e.preventDefault(); // Mencegah submit form default

    const confirmed = confirm('Apakah Anda yakin data balasan sudah benar? Proses ini akan mengubah status surat dan penempatan siswa.');
    
    if (confirmed) {
        const formData = $(this).serialize(); // Ambil semua data form
        const submitBtn = $(this).find('button[type="submit"]');

        // Nonaktifkan tombol submit selama proses
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> Menyimpan...');
        
        // Kirim data menggunakan AJAX
        $.ajax({
            url: 'ajax/input_balasan.php', // Harus dibuat file ini!
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Tampilkan pesan sukses dan arahkan kembali ke data surat
                    alert(response.message);
                    // Asumsi: 'loadContent' adalah fungsi navigasi dinamis Anda
                    loadContent('data_surat.php'); 
                } else {
                    alert('Gagal menyimpan: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error, xhr.responseText);
                // Menampilkan responseText sangat berguna untuk debugging error PHP
                alert('Terjadi kesalahan saat menghubungi server: ' + xhr.responseText);
            },
            complete: function() {
                // Aktifkan kembali tombol submit
                submitBtn.prop('disabled', false).html('<i class="fas fa-save me-2"></i> Simpan Balasan');
            }
        });
    }
});
</script>

<?php
$stmt_siswa->close();
$koneksi->close();
?>