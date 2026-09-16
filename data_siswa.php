<?php
// data_siswa.php - Konten Data Siswa (Client-Side Datatables)

include 'koneksi.php'; // 1. Include koneksi database

// 2. Ambil Semua Data Siswa dengan logika Status Penempatan (Best Practice SQL)
$query_siswa = "
    SELECT 
        s.id_siswa, 
        s.nis, 
        s.nama_siswa, 
        s.kelas, 
        s.kontak_siswa, 
        s.id_tempat,
        tp.nama_tempat,
        -- Subquery untuk mengambil status terakhir (yang paling baru)
        (
            SELECT ss.status
            FROM siswa_surat ss
            WHERE ss.id_siswa = s.id_siswa
            ORDER BY ss.id_surat_siswa DESC -- Asumsi ID yang lebih tinggi adalah status terbaru
            LIMIT 1
        ) AS status_pengajuan_terakhir
    FROM 
        siswa s
    LEFT JOIN 
        tempat_pkl tp ON s.id_tempat = tp.id_tempat
    ORDER BY 
        s.kelas ASC, s.nama_siswa ASC
";

$result_siswa = $koneksi->query($query_siswa);

$data_siswa = [];
if ($result_siswa) {
    while ($row = $result_siswa->fetch_assoc()) {
        $data_siswa[] = $row;
    }
}
$koneksi->close();
?>
<div class="card shadow-lg mb-4">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0"><i class="fas fa-user-graduate me-2"></i> Data Siswa Peserta PKL</h5>
    </div>
    <div class="card-body container-form">

        <div class="d-flex flex-wrap gap-2 mb-4">
            <a href="#" id="tambahSiswaBtn" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> Tambah Siswa Baru
            </a>
            <button type="button" class="btn btn-success text-white" data-bs-toggle="modal" data-bs-target="#importSiswaModal">
                <i class="fas fa-file-excel me-2"></i> Import Siswa
            </button>
            <a href="download_template_siswa.php" class="btn btn-outline-success">
                <i class="fas fa-download me-2"></i> Download Template
            </a>
        </div>

        <div class="table-responsive">
            <table id="siswaTable" class="table table-hover align-middle" style="width:100%">
                <thead>
                    <tr>
                        <th style="width: 50px;">No.</th>
                        <th style="width: 80px;">NIS</th>
                        <th>Nama Siswa</th>
                        <th style="width: 100px;">Kelas</th>
                        <th style="width: 150px;">Kontak Siswa</th>
                        <th style="width: 80px;">Penempatan PKL</th> <th style="width: 150px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($data_siswa as $siswa): ?>
                        <tr>
                            <td class="text-center"><?php echo $no++; ?></td>
                            <td><?php echo htmlspecialchars($siswa['nis']); ?></td>
                            <td><strong><?php echo htmlspecialchars($siswa['nama_siswa']); ?></strong></td>
                            <td><?php echo htmlspecialchars($siswa['kelas']); ?></td>
                            <td><?php echo htmlspecialchars($siswa['kontak_siswa']); ?></td>
                            
                            <td>
                                <?php 
                                    $status_display = '<span class="badge bg-secondary">Belum Diajukan</span>';

                                    if ($siswa['id_tempat'] != 0) {
                                        // KONDISI 1: Sudah Diterima dan Ditempatkan (id_tempat != 0)
                                        $status_display = '<span class="badge bg-success" title="Ditempatkan di ' . htmlspecialchars($siswa['nama_tempat']) . '">' . htmlspecialchars($siswa['nama_tempat']) . '</span>';
                                    } elseif (!empty($siswa['status_pengajuan_terakhir'])) {
                                        // KONDISI 2: Belum Ditempatkan, tampilkan status pengajuan terakhir
                                        $status = strtolower($siswa['status_pengajuan_terakhir']);
                                        
                                        $badge_class = 'bg-secondary';
                                        if ($status == 'diterima') {
                                            $badge_class = 'bg-info text-white'; // Diterima tapi belum ada id_tempat (pending penempatan/update)
                                        } elseif ($status == 'ditolak') {
                                            $badge_class = 'bg-danger';
                                        } elseif ($status == 'pending') {
                                            $badge_class = 'bg-warning text-dark';
                                        }
                                        
                                        $status_display = '<span class="badge ' . $badge_class . '">Status Surat: ' . ucfirst($status) . '</span>';
                                    }
                                    
                                    echo $status_display;
                                ?>
                            </td>
                            <td>
                                <div class="" role="group">
                                    <button class="btn btn-sm btn-warning edit-btn" data-id="<?php echo $siswa['id_siswa']; ?>" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-btn" data-id="<?php echo $siswa['id_siswa']; ?>" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<script>
    $(document).ready(function() {
        // Inisialisasi DataTables
        var table = $('#siswaTable').DataTable({
            "order": [
                [3, 'asc'],
                [2, 'asc']
            ], // Urutkan berdasarkan Kelas dan Nama
            "columnDefs": [{
                    "orderable": false,
                    "searchable": false,
                    "targets": [6] // Kolom No (0), Status Penempatan (5), dan Aksi (6) non-sortable/searchable
                },
                {
                    "className": "text-center",
                    "targets": [0, 6]
                }
            ],
            // Bahasa Indonesia
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
            },
            // Konfigurasi Buttons (Export Excel)
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>B<"row"<"col-sm-12"tr>><"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            buttons: [{
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel me-2"></i> Export ke Excel',
                titleAttr: 'Export Data ke Excel',
                className: 'btn btn-success',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5] // Export semua kolom data (termasuk status baru)
                },
                title: 'Data Siswa PKL SMK Informatika Sumedang',
                filename: 'Data_Siswa_PKL_' + new Date().toISOString().slice(0, 10)
            }],
            // Paging dan tampilan
            "pageLength": 10,
            "lengthMenu": [
                [10, 25, 50, -1],
                [10, 25, 50, "Semua"]
            ],
            "responsive": true
        });

        // Event handler untuk tombol Tambah
        $('#tambahSiswaBtn').on('click', function(e) {
            e.preventDefault();
            alert("Aksi Tambah Siswa akan diarahkan ke form input.");
            // loadContent('form_tambah_siswa.php'); // Contoh penggunaan loadContent
        });

        // Event handler untuk tombol Edit
        $('#siswaTable tbody').on('click', '.edit-btn', function() {
            var id = $(this).data('id');
            alert('Aksi Edit Siswa ID: ' + id + ' (Akan diarahkan ke halaman edit).');
            // loadContent('form_edit_siswa.php?id=' + id); // Contoh penggunaan loadContent
        });

        // Event handler untuk tombol Hapus
        $('#siswaTable tbody').on('click', '.delete-btn', function() {
            var id = $(this).data('id');
            if (confirm('Anda yakin ingin menghapus data siswa ini? Tindakan ini tidak dapat dibatalkan.')) {
                alert('Aksi Hapus Siswa ID: ' + id + ' (AJAX call to delete_siswa.php).');
                // Implementasi AJAX call untuk penghapusan
            }
        });

        // Event handler: preview nama file saat dipilih
        $('#fileExcelInput').on('change', function() {
            const file = this.files[0];
            if (file) {
                $('#fileNameDisplay').text(file.name);
                $('#importAlert').addClass('d-none');
            } else {
                $('#fileNameDisplay').text('Belum ada file dipilih...');
            }
        });

        // Event handler: Submit form import siswa
        $('#formImportSiswa').on('submit', function(e) {
            e.preventDefault();

            const fileInput = $('#fileExcelInput')[0];
            if (!fileInput.files.length) {
                showImportAlert('danger', 'Silakan pilih file Excel (.xlsx) terlebih dahulu.');
                return;
            }

            const formData = new FormData();
            formData.append('file_excel', fileInput.files[0]);

            const submitBtn = $('#btnSubmitImport');
            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> Memproses...');

            $.ajax({
                url: 'ajax/import_siswa.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        showImportAlert('success', '<i class="fas fa-check-circle me-2"></i>' + response.message);
                        // Reset form
                        fileInput.value = '';
                        $('#fileNameDisplay').text('Belum ada file dipilih...');
                        // Reload table data setelah sukses
                        setTimeout(function() {
                            $('#importSiswaModal').modal('hide');
                            loadContent('data_siswa.php');
                        }, 2000);
                    } else {
                        showImportAlert('danger', '<i class="fas fa-times-circle me-2"></i>' + response.message);
                    }
                },
                error: function(xhr) {
                    let msg = 'Terjadi kesalahan saat menghubungi server.';
                    try {
                        const resp = JSON.parse(xhr.responseText);
                        if (resp.message) msg = resp.message;
                    } catch (e) {}
                    showImportAlert('danger', '<i class="fas fa-times-circle me-2"></i>' + msg);
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html('<i class="fas fa-upload me-2"></i> Import Sekarang');
                }
            });
        });

        // Reset modal saat ditutup
        $('#importSiswaModal').on('hidden.bs.modal', function() {
            $('#fileExcelInput').val('');
            $('#fileNameDisplay').text('Belum ada file dipilih...');
            $('#importAlert').addClass('d-none').html('');
        });

        function showImportAlert(type, message) {
            $('#importAlert')
                .removeClass('d-none alert-success alert-danger alert-warning alert-info')
                .addClass('alert-' + type)
                .html(message);
        }
    });
</script>

<!-- Modal Import Siswa -->
<div class="modal fade" id="importSiswaModal" tabindex="-1" aria-labelledby="importSiswaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="importSiswaModalLabel">
                    <i class="fas fa-file-excel me-2"></i> Import Data Siswa dari Excel
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formImportSiswa" enctype="multipart/form-data">
                <div class="modal-body">

                    <div class="alert alert-info d-flex align-items-start gap-2 mb-4">
                        <i class="fas fa-info-circle mt-1 flex-shrink-0"></i>
                        <div>
                            <strong>Petunjuk Import:</strong>
                            <ul class="mb-0 mt-1">
                                <li>File harus berformat <strong>.xlsx</strong> (Excel).</li>
                                <li>Kolom yang diperlukan: <strong>NIS, Nama Siswa, Kelas, Kontak Siswa</strong>.</li>
                                <li>Baris pertama akan dianggap sebagai <strong>header</strong> dan dilewati.</li>
                                <li>Jika NIS sudah ada, data siswa akan <strong>diperbarui</strong>.</li>
                                <li>Siswa baru akan mendapatkan password default berupa <strong>NIS-nya</strong>.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">File Excel (.xlsx)</label>
                        <div class="input-group">
                            <label class="input-group-text btn btn-outline-success" for="fileExcelInput" style="cursor:pointer;">
                                <i class="fas fa-folder-open me-2"></i> Pilih File
                            </label>
                            <span class="form-control text-muted" id="fileNameDisplay">Belum ada file dipilih...</span>
                        </div>
                        <input type="file" id="fileExcelInput" name="file_excel" accept=".xlsx" class="d-none">
                        <div class="form-text">Hanya file .xlsx yang diterima. Maksimal ukuran file 10MB.</div>
                    </div>

                    <div id="importAlert" class="alert d-none" role="alert"></div>

                    <div class="d-flex align-items-center gap-2">
                        <a href="download_template_siswa.php" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-download me-1"></i> Download Template Excel
                        </a>
                        <span class="text-muted small">Gunakan template ini agar format sesuai</span>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success" id="btnSubmitImport">
                        <i class="fas fa-upload me-2"></i> Import Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>