<?php
// kelola_siswa_surat.php - Halaman untuk mengelola siswa dalam surat

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

// Handle AJAX request untuk tambah siswa
if (isset($_POST['action']) && $_POST['action'] == 'tambah_siswa') {
    $id_siswa = intval($_POST['id_siswa']);
    
    // Cek apakah siswa sudah ada di surat ini
    $query_check = "SELECT id_surat_siswa FROM siswa_surat WHERE id_surat = ? AND id_siswa = ?";
    $stmt_check = $koneksi->prepare($query_check);
    $stmt_check->bind_param("ii", $id_surat, $id_siswa);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    
    if ($result_check->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Siswa sudah ada dalam surat ini!']);
    } else {
        $query_insert = "INSERT INTO siswa_surat (id_surat, id_siswa, status) VALUES (?, ?, 'pending')";
        $stmt_insert = $koneksi->prepare($query_insert);
        $stmt_insert->bind_param("ii", $id_surat, $id_siswa);
        
        if ($stmt_insert->execute()) {
            echo json_encode(['success' => true, 'message' => 'Siswa berhasil ditambahkan!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menambahkan siswa!']);
        }
    }
    exit();
}

// Handle AJAX request untuk hapus siswa
if (isset($_POST['action']) && $_POST['action'] == 'hapus_siswa') {
    $id_siswa = intval($_POST['id_siswa']);
    
    $query_delete = "DELETE FROM siswa_surat WHERE id_surat = ? AND id_siswa = ?";
    $stmt_delete = $koneksi->prepare($query_delete);
    $stmt_delete->bind_param("ii", $id_surat, $id_siswa);
    
    if ($stmt_delete->execute()) {
        echo json_encode(['success' => true, 'message' => 'Siswa berhasil dihapus!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menghapus siswa!']);
    }
    exit();
}

// Ambil siswa yang sudah ditambahkan ke surat
$query_siswa_surat = "SELECT s.id_siswa, s.nis, s.nama_siswa, ss.status
                      FROM siswa_surat ss
                      JOIN siswa s ON ss.id_siswa = s.id_siswa
                      WHERE ss.id_surat = ?
                      ORDER BY s.nama_siswa ASC";
$stmt_siswa_surat = $koneksi->prepare($query_siswa_surat);
$stmt_siswa_surat->bind_param("i", $id_surat);
$stmt_siswa_surat->execute();
$result_siswa_surat = $stmt_siswa_surat->get_result();

// Ambil semua siswa yang belum punya tempat PKL (untuk dropdown)
$query_siswa_tersedia = "SELECT s.id_siswa, s.nis, s.nama_siswa
                         FROM siswa s
                         WHERE s.id_tempat = 0
                         AND s.id_siswa NOT IN (
                             SELECT id_siswa FROM siswa_surat WHERE id_surat = ?
                         )
                         ORDER BY s.nama_siswa ASC";
$stmt_tersedia = $koneksi->prepare($query_siswa_tersedia);
$stmt_tersedia->bind_param("i", $id_surat);
$stmt_tersedia->execute();
$result_tersedia = $stmt_tersedia->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Siswa dalam Surat PKL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

<div class="container my-5">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-users me-2"></i> Kelola Siswa dalam Surat PKL</h5>
        </div>
        <div class="card-body">
            
            <!-- Alert -->
            <div id="alertContainer"></div>
            
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
            
            <!-- Form Tambah Siswa -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-plus-circle me-2"></i> Tambah Siswa ke Surat</h6>
                </div>
                <div class="card-body">
                    <form id="formTambahSiswa">
                        <div class="row align-items-end">
                            <div class="col-md-8">
                                <label class="form-label">Pilih Siswa:</label>
                                <select class="form-select" id="selectSiswa" required>
                                    <option value="">-- Pilih Siswa --</option>
                                    <?php while ($siswa = $result_tersedia->fetch_assoc()): ?>
                                        <option value="<?php echo $siswa['id_siswa']; ?>">
                                            <?php echo htmlspecialchars($siswa['nis'] . ' - ' . $siswa['nama_siswa']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-plus me-2"></i> Tambah
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Daftar Siswa dalam Surat -->
            <h6 class="mb-3"><i class="fas fa-list me-2"></i> Daftar Siswa yang Diajukan</h6>
            
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="tableSiswa">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">No.</th>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            <th style="width: 150px;">Status</th>
                            <th style="width: 100px;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($result_siswa_surat->num_rows == 0): 
                        ?>
                        <tr id="emptyRow">
                            <td colspan="5" class="text-center text-muted">
                                <i class="fas fa-inbox me-2"></i> Belum ada siswa yang ditambahkan
                            </td>
                        </tr>
                        <?php else: 
                            $no = 1;
                            while ($siswa = $result_siswa_surat->fetch_assoc()): 
                        ?>
                        <tr data-id="<?php echo $siswa['id_siswa']; ?>">
                            <td class="text-center"><?php echo $no++; ?></td>
                            <td><?php echo htmlspecialchars($siswa['nis']); ?></td>
                            <td><?php echo htmlspecialchars($siswa['nama_siswa']); ?></td>
                            <td>
                                <?php
                                $badge_class = 'secondary';
                                $status_text = 'Menunggu';
                                if ($siswa['status'] == 'diterima') {
                                    $badge_class = 'success';
                                    $status_text = 'Diterima';
                                } elseif ($siswa['status'] == 'ditolak') {
                                    $badge_class = 'danger';
                                    $status_text = 'Ditolak';
                                }
                                ?>
                                <span class="badge bg-<?php echo $badge_class; ?>"><?php echo $status_text; ?></span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-danger btn-hapus" 
                                        data-id="<?php echo $siswa['id_siswa']; ?>"
                                        title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; endif; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-between mt-4">
                <a href="index.php?page=data_surat" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Kembali
                </a>
                <a href="proses_balasan_surat.php?id=<?php echo $id_surat; ?>" class="btn btn-success">
                    <i class="fas fa-reply me-2"></i> Proses Balasan Surat
                </a>
            </div>
            
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<script>
$(document).ready(function() {
    
    // Fungsi untuk menampilkan alert
    function showAlert(type, message) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        $('#alertContainer').html(alertHtml);
        
        // Auto hide after 3 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow', function() {
                $(this).remove();
            });
        }, 3000);
    }
    
    // Handle form tambah siswa
    $('#formTambahSiswa').on('submit', function(e) {
        e.preventDefault();
        
        const idSiswa = $('#selectSiswa').val();
        
        if (!idSiswa) {
            showAlert('warning', 'Silakan pilih siswa terlebih dahulu!');
            return;
        }
        
        $.ajax({
            url: 'kelola_siswa_surat.php?id=<?php echo $id_surat; ?>',
            type: 'POST',
            data: {
                action: 'tambah_siswa',
                id_siswa: idSiswa
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    showAlert('danger', response.message);
                }
            },
            error: function() {
                showAlert('danger', 'Terjadi kesalahan sistem!');
            }
        });
    });
    
    // Handle hapus siswa
    $(document).on('click', '.btn-hapus', function() {
        const idSiswa = $(this).data('id');
        const row = $(this).closest('tr');
        
        if (confirm('Apakah Anda yakin ingin menghapus siswa ini dari surat?')) {
            $.ajax({
                url: 'kelola_siswa_surat.php?id=<?php echo $id_surat; ?>',
                type: 'POST',
                data: {
                    action: 'hapus_siswa',
                    id_siswa: idSiswa
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showAlert('success', response.message);
                        row.fadeOut('slow', function() {
                            $(this).remove();
                            
                            // Cek apakah tabel kosong
                            if ($('#tableSiswa tbody tr').length === 0) {
                                $('#tableSiswa tbody').html(`
                                    <tr id="emptyRow">
                                        <td colspan="5" class="text-center text-muted">
                                            <i class="fas fa-inbox me-2"></i> Belum ada siswa yang ditambahkan
                                        </td>
                                    </tr>
                                `);
                            }
                        });
                        
                        // Reload setelah 1 detik untuk update dropdown
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        showAlert('danger', response.message);
                    }
                },
                error: function() {
                    showAlert('danger', 'Terjadi kesalahan sistem!');
                }
            });
        }
    });
    
});
</script>

</body>
</html>

<?php
$koneksi->close();
?>