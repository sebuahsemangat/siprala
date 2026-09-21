<?php
// data_pembimbing.php - Halaman Pengelolaan Data Pembimbing (Client-Side DataTables)
include 'koneksi.php';

// 1. Ambil data pembimbing dan hitung jumlah siswa yang dibimbing
$query_pembimbing = "
    SELECT 
        p.id_pembimbing,
        p.username,
        p.nama_pembimbing,
        p.kontak_pembimbing,
        COUNT(s.id_siswa) AS jumlah_siswa
    FROM 
        pembimbing p
    LEFT JOIN 
        siswa s ON p.id_pembimbing = s.id_pembimbing
    GROUP BY 
        p.id_pembimbing, p.username, p.nama_pembimbing, p.kontak_pembimbing
    ORDER BY 
        p.nama_pembimbing ASC
";

$result = $koneksi->query($query_pembimbing);
$data_pembimbing = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $data_pembimbing[] = $row;
    }
}
$koneksi->close();
?>

<div class="card shadow-lg mb-4">
    <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold"><i class="fas fa-chalkboard-teacher me-2"></i> Data Pembimbing Sekolah</h5>
        <span class="badge bg-dark text-white"><?= count($data_pembimbing) ?> Pembimbing</span>
    </div>
    <div class="card-body container-form">

        <!-- Tombol Aksi Atas -->
        <div class="d-flex flex-wrap gap-2 mb-4">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                data-bs-target="#tambahPembimbingModal">
                <i class="fas fa-plus me-2"></i> Tambah Pembimbing Baru
            </button>
            <button type="button" class="btn btn-success text-white" data-bs-toggle="modal"
                data-bs-target="#importPembimbingModal">
                <i class="fas fa-file-excel me-2"></i> Import Excel
            </button>
            <a href="download_template_pembimbing.php" class="btn btn-outline-success">
                <i class="fas fa-download me-2"></i> Download Template
            </a>
        </div>

        <!-- Tabel DataTables -->
        <div class="table-responsive">
            <table id="pembimbingTable" class="table table-hover align-middle" style="width:100%">
                <thead>
                    <tr>
                        <th style="width: 50px;">No.</th>
                        <th>Nama Pembimbing</th>
                        <th style="width: 140px;">Username</th>
                        <th style="width: 150px;">Kontak</th>
                        <th style="width: 160px;" class="text-center">Siswa Bimbingan</th>
                        <th style="width: 120px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($data_pembimbing as $p): ?>
                        <tr>
                            <td class="text-center"><?= $no++; ?></td>
                            <td><strong><?= htmlspecialchars($p['nama_pembimbing']); ?></strong></td>
                            <td><span
                                    class="badge bg-light text-dark border"><?= htmlspecialchars($p['username']); ?></span>
                            </td>
                            <td>
                                <?php if (!empty($p['kontak_pembimbing'])): ?>
                                    <?= htmlspecialchars($p['kontak_pembimbing']); ?>
                                <?php else: ?>
                                    <span class="text-muted fst-italic small">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if ($p['jumlah_siswa'] > 0): ?>
                                    <span class="badge bg-success fs-6"><?= $p['jumlah_siswa']; ?> Siswa</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">0 Siswa</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-warning text-white btn-edit-pembimbing"
                                        data-id="<?= $p['id_pembimbing']; ?>"
                                        data-nama="<?= htmlspecialchars($p['nama_pembimbing']); ?>"
                                        data-username="<?= htmlspecialchars($p['username']); ?>"
                                        data-kontak="<?= htmlspecialchars($p['kontak_pembimbing']); ?>"
                                        title="Edit Pembimbing">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger text-white btn-hapus-pembimbing"
                                        data-id="<?= $p['id_pembimbing']; ?>"
                                        data-nama="<?= htmlspecialchars($p['nama_pembimbing']); ?>"
                                        data-siswa="<?= $p['jumlah_siswa']; ?>" title="Hapus Pembimbing">
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

<!-- ========================================== -->
<!-- 1. MODAL TAMBAH PEMBIMBING                -->
<!-- ========================================== -->
<div class="modal fade" id="tambahPembimbingModal" tabindex="-1" aria-labelledby="tambahPembimbingModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="tambahPembimbingModalLabel"><i class="fas fa-user-plus me-2"></i>Tambah
                    Pembimbing Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form id="formTambahPembimbing">
                <div class="modal-body">
                    <div id="alertTambahPembimbing" class="alert d-none"></div>

                    <div class="mb-3">
                        <label for="tambah_nama" class="form-label fw-semibold">Nama Lengkap Pembimbing <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="tambah_nama" name="nama_pembimbing"
                            placeholder="Contoh: Drs. H. Ahmad Dahlan, M.Pd." required>
                    </div>

                    <div class="mb-3">
                        <label for="tambah_username" class="form-label fw-semibold">Username <small
                                class="text-muted fw-normal">(Opsional)</small></label>
                        <input type="text" class="form-control" id="tambah_username" name="username"
                            placeholder="Kosongkan jika ingin dibuat otomatis">
                        <div class="form-text">Jika dikosongkan, sistem akan membuatkan username otomatis dari nama.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="tambah_kontak" class="form-label fw-semibold">No. Handphone / WhatsApp</label>
                        <input type="tel" class="form-control" id="tambah_kontak" name="kontak_pembimbing"
                            placeholder="08xxxxxxxxxx">
                    </div>

                    <div class="mb-3">
                        <label for="tambah_password" class="form-label fw-semibold">Password <small
                                class="text-muted fw-normal">(Opsional)</small></label>
                        <input type="text" class="form-control" id="tambah_password" name="password"
                            placeholder="Default: pklifsu">
                        <div class="form-text">Jika dikosongkan, password default adalah: <strong
                                class="text-primary">pklifsu</strong></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitTambah">
                        <span class="spinner-border spinner-border-sm me-1 d-none" id="spinnerTambah"></span>
                        <i class="fas fa-save me-1" id="iconTambah"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- 2. MODAL EDIT PEMBIMBING                  -->
<!-- ========================================== -->
<div class="modal fade" id="editPembimbingModal" tabindex="-1" aria-labelledby="editPembimbingModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="editPembimbingModalLabel"><i class="fas fa-edit me-2"></i>Edit Data
                    Pembimbing</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form id="formEditPembimbing">
                <div class="modal-body">
                    <div id="alertEditPembimbing" class="alert d-none"></div>
                    <input type="hidden" id="edit_id_pembimbing" name="id_pembimbing">

                    <div class="mb-3">
                        <label for="edit_nama" class="form-label fw-semibold">Nama Lengkap Pembimbing <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_nama" name="nama_pembimbing" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_username" class="form-label fw-semibold">Username <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_username" name="username" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_kontak" class="form-label fw-semibold">No. Handphone / WhatsApp</label>
                        <input type="tel" class="form-control" id="edit_kontak" name="kontak_pembimbing"
                            placeholder="08xxxxxxxxxx">
                    </div>

                    <div class="mb-3">
                        <label for="edit_password" class="form-label fw-semibold">Ganti Password <small
                                class="text-muted fw-normal">(Opsional)</small></label>
                        <input type="password" class="form-control" id="edit_password" name="password"
                            placeholder="Kosongkan jika tidak ingin mengubah password">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning text-white" id="btnSubmitEdit">
                        <span class="spinner-border spinner-border-sm me-1 d-none" id="spinnerEdit"></span>
                        <i class="fas fa-save me-1" id="iconEdit"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- 3. MODAL IMPORT PEMBIMBING (EXCEL)        -->
<!-- ========================================== -->
<div class="modal fade" id="importPembimbingModal" tabindex="-1" aria-labelledby="importPembimbingModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="importPembimbingModalLabel"><i class="fas fa-file-excel me-2"></i>Import
                    Data Pembimbing</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form id="formImportPembimbing" enctype="multipart/form-data">
                <div class="modal-body">
                    <div id="alertImportPembimbing" class="alert d-none"></div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pilih File Excel (.xlsx)</label>
                        <input type="file" class="form-control" id="fileExcelPembimbing" name="file_excel"
                            accept=".xlsx" required>
                        <div class="form-text mt-2">
                            Pastikan format kolom sesuai dengan <a href="download_template_pembimbing.php"
                                class="text-success fw-semibold">Template Excel</a>.
                        </div>
                    </div>

                    <div class="alert alert-info small mb-0">
                        <i class="fas fa-info-circle me-1"></i>
                        <strong>Catatan:</strong>
                        <ul class="mb-0 ps-3 mt-1">
                            <li>Username opsional (otomatis dibuat jika kosong).</li>
                            <li>Password default untuk pembimbing baru adalah: <strong>pklifsu</strong></li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-success" id="btnSubmitImport">
                        <span class="spinner-border spinner-border-sm me-1 d-none" id="spinnerImport"></span>
                        <i class="fas fa-upload me-1" id="iconImport"></i> Upload & Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- 4. MODAL HAPUS PEMBIMBING                 -->
<!-- ========================================== -->
<div class="modal fade" id="hapusPembimbingModal" tabindex="-1" aria-labelledby="hapusPembimbingModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="hapusPembimbingModalLabel"><i
                        class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Hapus</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="hapus_id_pembimbing">
                <p class="mb-2">Apakah Anda yakin ingin menghapus pembimbing berikut?</p>
                <div class="p-3 bg-light rounded border mb-3">
                    <strong class="text-danger fs-5" id="hapus_nama_pembimbing"></strong>
                </div>
                <div class="alert alert-warning small mb-0" id="hapus_warning_siswa">
                    <i class="fas fa-info-circle me-1"></i>
                    Penugasan pembimbing pada siswa dan tempat PKL terkait akan dinetralkan secara otomatis.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="btnConfirmHapus">
                    <span class="spinner-border spinner-border-sm me-1 d-none" id="spinnerHapus"></span>
                    <i class="fas fa-trash me-1" id="iconHapus"></i> Hapus Pembimbing
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Script Interaktif DataTables & AJAX -->
<script>
    $(document).ready(function () {
        // 1. Inisialisasi DataTables
        var table = $('#pembimbingTable').DataTable({
            "order": [[1, 'asc']], // Urutkan berdasarkan Nama Pembimbing
            "columnDefs": [
                {
                    "orderable": false,
                    "searchable": false,
                    "targets": [0, 5] // No dan Aksi
                },
                {
                    "className": "text-center",
                    "targets": [0, 4, 5]
                }
            ],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
            },
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>B<"row"<"col-sm-12"tr>><"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            buttons: [{
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel me-2"></i> Export ke Excel',
                titleAttr: 'Export Data Pembimbing ke Excel',
                className: 'btn btn-success',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4] // Export semua kolom kecuali Aksi
                },
                title: 'Data Pembimbing PKL SMK Informatika Sumedang',
                filename: 'Data_Pembimbing_PKL_' + new Date().toISOString().slice(0, 10)
            }],
            "pageLength": 10,
            "lengthMenu": [
                [10, 25, 50, -1],
                [10, 25, 50, "Semua"]
            ],
            "responsive": true
        });

        // 2. Submit Form Tambah Pembimbing
        $('#formTambahPembimbing').on('submit', function (e) {
            e.preventDefault();
            var alertBox = $('#alertTambahPembimbing');
            var btn = $('#btnSubmitTambah');
            var spinner = $('#spinnerTambah');
            var icon = $('#iconTambah');

            btn.prop('disabled', true);
            spinner.removeClass('d-none');
            icon.addClass('d-none');
            alertBox.addClass('d-none').removeClass('alert-success alert-danger');

            $.ajax({
                url: 'ajax/tambah_pembimbing.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (res) {
                    if (res.status === 'success') {
                        alertBox.addClass('alert-success').html('<i class="fas fa-check-circle me-1"></i> ' + res.message).removeClass('d-none');
                        setTimeout(function () {
                            $('#tambahPembimbingModal').modal('hide');
                            $('#formTambahPembimbing')[0].reset();
                            alertBox.addClass('d-none');
                            loadContent('data_pembimbing.php');
                        }, 1200);
                    } else {
                        alertBox.addClass('alert-danger').html('<i class="fas fa-times-circle me-1"></i> ' + res.message).removeClass('d-none');
                    }
                },
                error: function (xhr) {
                    var msg = 'Terjadi kesalahan pada server.';
                    try {
                        var r = JSON.parse(xhr.responseText);
                        if (r.message) msg = r.message;
                    } catch (e) { }
                    alertBox.addClass('alert-danger').html('<i class="fas fa-times-circle me-1"></i> ' + msg).removeClass('d-none');
                },
                complete: function () {
                    btn.prop('disabled', false);
                    spinner.addClass('d-none');
                    icon.removeClass('d-none');
                }
            });
        });

        // 3. Trigger Modal Edit
        $('#pembimbingTable').on('click', '.btn-edit-pembimbing', function () {
            var id = $(this).data('id');
            var nama = $(this).data('nama');
            var username = $(this).data('username');
            var kontak = $(this).data('kontak');

            $('#edit_id_pembimbing').val(id);
            $('#edit_nama').val(nama);
            $('#edit_username').val(username);
            $('#edit_kontak').val(kontak);
            $('#edit_password').val('');
            $('#alertEditPembimbing').addClass('d-none');

            $('#editPembimbingModal').modal('show');
        });

        // 4. Submit Form Edit Pembimbing
        $('#formEditPembimbing').on('submit', function (e) {
            e.preventDefault();
            var alertBox = $('#alertEditPembimbing');
            var btn = $('#btnSubmitEdit');
            var spinner = $('#spinnerEdit');
            var icon = $('#iconEdit');

            btn.prop('disabled', true);
            spinner.removeClass('d-none');
            icon.addClass('d-none');
            alertBox.addClass('d-none').removeClass('alert-success alert-danger');

            $.ajax({
                url: 'ajax/edit_pembimbing.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (res) {
                    if (res.status === 'success') {
                        alertBox.addClass('alert-success').html('<i class="fas fa-check-circle me-1"></i> ' + res.message).removeClass('d-none');
                        setTimeout(function () {
                            $('#editPembimbingModal').modal('hide');
                            alertBox.addClass('d-none');
                            loadContent('data_pembimbing.php');
                        }, 1200);
                    } else {
                        alertBox.addClass('alert-danger').html('<i class="fas fa-times-circle me-1"></i> ' + res.message).removeClass('d-none');
                    }
                },
                error: function (xhr) {
                    var msg = 'Terjadi kesalahan pada server.';
                    try {
                        var r = JSON.parse(xhr.responseText);
                        if (r.message) msg = r.message;
                    } catch (e) { }
                    alertBox.addClass('alert-danger').html('<i class="fas fa-times-circle me-1"></i> ' + msg).removeClass('d-none');
                },
                complete: function () {
                    btn.prop('disabled', false);
                    spinner.addClass('d-none');
                    icon.removeClass('d-none');
                }
            });
        });

        // 5. Trigger Modal Hapus
        $('#pembimbingTable').on('click', '.btn-hapus-pembimbing', function () {
            var id = $(this).data('id');
            var nama = $(this).data('nama');
            var siswa = $(this).data('siswa');

            $('#hapus_id_pembimbing').val(id);
            $('#hapus_nama_pembimbing').text(nama);
            if (siswa > 0) {
                $('#hapus_warning_siswa').html('<i class="fas fa-exclamation-triangle me-1"></i> <strong>Perhatian:</strong> Pembimbing ini saat ini membimbing <strong>' + siswa + ' siswa</strong>. Menghapus pembimbing akan mengosongkan pembimbing pada siswa-siswa tersebut.');
            } else {
                $('#hapus_warning_siswa').html('<i class="fas fa-info-circle me-1"></i> Pembimbing ini belum memiliki siswa bimbingan.');
            }

            $('#hapusPembimbingModal').modal('show');
        });

        // 6. Eksekusi Hapus Pembimbing
        $('#btnConfirmHapus').on('click', function () {
            var id = $('#hapus_id_pembimbing').val();
            var btn = $(this);
            var spinner = $('#spinnerHapus');
            var icon = $('#iconHapus');

            btn.prop('disabled', true);
            spinner.removeClass('d-none');
            icon.addClass('d-none');

            $.ajax({
                url: 'ajax/hapus_pembimbing.php',
                type: 'POST',
                data: { id_pembimbing: id },
                dataType: 'json',
                success: function (res) {
                    if (res.status === 'success') {
                        $('#hapusPembimbingModal').modal('hide');
                        loadContent('data_pembimbing.php');
                    } else {
                        alert('Gagal menghapus: ' + res.message);
                    }
                },
                error: function (xhr) {
                    var msg = 'Terjadi kesalahan saat menghapus data.';
                    try {
                        var r = JSON.parse(xhr.responseText);
                        if (r.message) msg = r.message;
                    } catch (e) { }
                    alert(msg);
                },
                complete: function () {
                    btn.prop('disabled', false);
                    spinner.addClass('d-none');
                    icon.removeClass('d-none');
                }
            });
        });

        // 7. Submit Form Import Pembimbing (Excel)
        $('#formImportPembimbing').on('submit', function (e) {
            e.preventDefault();
            var fileInput = $('#fileExcelPembimbing')[0];
            var alertBox = $('#alertImportPembimbing');
            var btn = $('#btnSubmitImport');
            var spinner = $('#spinnerImport');
            var icon = $('#iconImport');

            if (!fileInput.files.length) {
                alertBox.addClass('alert-danger').html('Silakan pilih file Excel (.xlsx) terlebih dahulu.').removeClass('d-none');
                return;
            }

            var formData = new FormData();
            formData.append('file_excel', fileInput.files[0]);

            btn.prop('disabled', true);
            spinner.removeClass('d-none');
            icon.addClass('d-none');
            alertBox.addClass('d-none').removeClass('alert-success alert-danger');

            $.ajax({
                url: 'ajax/import_pembimbing.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (res) {
                    if (res.status === 'success') {
                        alertBox.addClass('alert-success').html('<i class="fas fa-check-circle me-1"></i> ' + res.message).removeClass('d-none');
                        setTimeout(function () {
                            $('#importPembimbingModal').modal('hide');
                            $('#formImportPembimbing')[0].reset();
                            alertBox.addClass('d-none');
                            loadContent('data_pembimbing.php');
                        }, 2000);
                    } else {
                        alertBox.addClass('alert-danger').html('<i class="fas fa-times-circle me-1"></i> ' + res.message).removeClass('d-none');
                    }
                },
                error: function (xhr) {
                    var msg = 'Terjadi kesalahan saat mengunggah berkas.';
                    try {
                        var r = JSON.parse(xhr.responseText);
                        if (r.message) msg = r.message;
                    } catch (e) { }
                    alertBox.addClass('alert-danger').html('<i class="fas fa-times-circle me-1"></i> ' + msg).removeClass('d-none');
                },
                complete: function () {
                    btn.prop('disabled', false);
                    spinner.addClass('d-none');
                    icon.removeClass('d-none');
                }
            });
        });
    });
</script>