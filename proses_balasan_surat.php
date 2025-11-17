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

if (!$stmt_siswa) {
    die("Error preparing statement: " . $koneksi->error);
}

$stmt_siswa->bind_param("i", $id_surat);
$stmt_siswa->execute();
$result_siswa = $stmt_siswa->get_result();

// Proses form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $siswa_data = isset($_POST['siswa']) ? $_POST['siswa'] : [];
    
    if (empty($siswa_data)) {
        $error_msg = "Tidak ada siswa yang dipilih!";
    } else {
        $koneksi->begin_transaction();
        
        try {
            $ada_yang_diterima = false;
            
            foreach ($siswa_data as $id_siswa => $data) {
                $status = $data['status'];
                $catatan = isset($data['catatan']) ? trim($data['catatan']) : '';
                
                // Update data siswa_surat
                $query_update = "UPDATE siswa_surat 
                                SET status = ?, catatan = ? 
                                WHERE id_surat = ? AND id_siswa = ?";
                $stmt_update = $koneksi->prepare($query_update);
                
                if (!$stmt_update) {
                    throw new Exception("Error preparing update statement: " . $koneksi->error);
                }
                
                $stmt_update->bind_param("ssii", $status, $catatan, $id_surat, $id_siswa);
                
                if (!$stmt_update->execute()) {
                    throw new Exception("Error updating siswa_surat: " . $stmt_update->error);
                }
                
                // Jika diterima, update id_tempat di tabel siswa
                if ($status == 'diterima') {
                    $ada_yang_diterima = true;
                    $id_tempat = $data_surat['id_tempat'];
                    $query_update_siswa = "UPDATE siswa SET id_tempat = ? WHERE id_siswa = ?";
                    $stmt_update_siswa = $koneksi->prepare($query_update_siswa);
                    
                    if (!$stmt_update_siswa) {
                        throw new Exception("Error preparing update siswa: " . $koneksi->error);
                    }
                    
                    $stmt_update_siswa->bind_param("ii", $id_tempat, $id_siswa);
                    
                    if (!$stmt_update_siswa->execute()) {
                        throw new Exception("Error updating siswa: " . $stmt_update_siswa->error);
                    }
                } elseif ($status == 'ditolak') {
                    // Reset id_tempat jika ditolak
                    $query_reset = "UPDATE siswa SET id_tempat = 0 WHERE id_siswa = ?";
                    $stmt_reset = $koneksi->prepare($query_reset);
                    
                    if (!$stmt_reset) {
                        throw new Exception("Error preparing reset: " . $koneksi->error);
                    }
                    
                    $stmt_reset->bind_param("i", $id_siswa);
                    
                    if (!$stmt_reset->execute()) {
                        throw new Exception("Error resetting siswa: " . $stmt_reset->error);
                    }
                }
            }
            
            // Update status balasan surat
            $status_balasan = 'Sudah Dibalas';
            $query_update_surat = "UPDATE surat SET status_balasan = ? WHERE id_surat = ?";
            $stmt_update_surat = $koneksi->prepare($query_update_surat);
            
            if (!$stmt_update_surat) {
                throw new Exception("Error preparing update surat: " . $koneksi->error);
            }
            
            $stmt_update_surat->bind_param("si", $status_balasan, $id_surat);
            
            if (!$stmt_update_surat->execute()) {
                throw new Exception("Error updating surat: " . $stmt_update_surat->error);
            }
            
            $koneksi->commit();
            $success_msg = "Balasan surat berhasil diproses!";
            
            // Redirect ke halaman data surat
            header("Location: index.php?page=data_surat&success=1");
            exit();
            
        } catch (Exception $e) {
            $koneksi->rollback();
            $error_msg = "Terjadi kesalahan: " . $e->getMessage();
        }
    }
}
?>

    <div class="card shadow-lg">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-reply me-2"></i> Proses Balasan Surat PKL</h5>
        </div>
        <div class="card-body">
            
            <?php if (isset($error_msg)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo $error_msg; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if (isset($success_msg)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i><?php echo $success_msg; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <!-- Info Surat -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <p><strong>No. Surat:</strong> <?php echo htmlspecialchars($data_surat['no_surat']); ?></p>
                    <p><strong>Tanggal:</strong> <?php echo date('d-m-Y', strtotime($data_surat['tanggal'])); ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Tempat PKL:</strong> <?php echo htmlspecialchars($data_surat['nama_tempat']); ?></p>
                    <p><strong>Status:</strong> 
                        <?php if ($data_surat['status_balasan'] == 'Sudah Dibalas'): ?>
                            <span class="badge bg-success">Sudah Dibalas</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Belum Dibalas</span>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
            
            <hr>
            
            <!-- Form Balasan -->
            <form method="POST" id="formBalasan">
                <h6 class="mb-3"><i class="fas fa-users me-2"></i> Daftar Siswa yang Diajukan</h6>
                
                <?php if ($result_siswa->num_rows == 0): ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Belum ada siswa yang ditambahkan ke surat ini. 
                        <a href="kelola_siswa_surat.php?id=<?php echo $id_surat; ?>" class="alert-link">
                            Klik di sini untuk menambah siswa
                        </a>
                    </div>
                <?php else: ?>
                
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">No.</th>
                                <th>NIS</th>
                                <th>Nama Siswa</th>
                                <th style="width: 200px;">Status Penerimaan</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            while ($siswa = $result_siswa->fetch_assoc()): 
                            ?>
                            <tr>
                                <td class="text-center"><?php echo $no++; ?></td>
                                <td><?php echo htmlspecialchars($siswa['nis']); ?></td>
                                <td><?php echo htmlspecialchars($siswa['nama_siswa']); ?></td>
                                <td>
                                    <select name="siswa[<?php echo $siswa['id_siswa']; ?>][status]" 
                                            class="form-select form-select-sm" required>
                                        <option value="pending" <?php echo ($siswa['status'] == 'pending') ? 'selected' : ''; ?>>
                                            Menunggu
                                        </option>
                                        <option value="diterima" <?php echo ($siswa['status'] == 'diterima') ? 'selected' : ''; ?>>
                                            Diterima
                                        </option>
                                        <option value="ditolak" <?php echo ($siswa['status'] == 'ditolak') ? 'selected' : ''; ?>>
                                            Ditolak
                                        </option>
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
                
                <div class="d-flex justify-content-between mt-4">
                    <!--<a href="kelola_siswa_surat.php?id=<?php echo $id_surat; ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Kembali
                    </a>-->
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-2"></i> Simpan Balasan
                    </button>
                </div>
                
                <?php endif; ?>
            </form>
            
        </div>
    </div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Konfirmasi sebelum submit
document.getElementById('formBalasan')?.addEventListener('submit', function(e) {
    const confirmed = confirm('Apakah Anda yakin data balasan sudah benar? Proses ini akan mengubah status surat dan penempatan siswa.');
    if (!confirmed) {
        e.preventDefault();
    }
});
</script>

<?php
$koneksi->close();
?>