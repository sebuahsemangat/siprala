<?php
// data_tempat_pkl.php - Konten Data Tempat PKL (Client-Side Datatables)

include 'koneksi.php'; // 1. Include koneksi database

// 2. Ambil Data Tempat PKL, Nama Pembimbing, dan Jumlah Siswa (Efisien dengan JOIN dan GROUP BY)
$query_tempat = "
    SELECT 
        tp.id_tempat, 
        tp.nama_tempat, 
        tp.alamat,
        tp.kota,
        tp.no_telepon,
        tp.catatan,
        tp.kapasitas,
        tp.id_pembimbing,
        p.nama_pembimbing,
        COUNT(s.id_siswa) AS jumlah_siswa
    FROM 
        tempat_pkl tp
    LEFT JOIN 
        pembimbing p ON tp.id_pembimbing = p.id_pembimbing
    LEFT JOIN 
        siswa s ON tp.id_tempat = s.id_tempat
    GROUP BY
        tp.id_tempat, tp.nama_tempat, tp.alamat, tp.kota, tp.no_telepon, tp.catatan, tp.kapasitas, tp.id_pembimbing, p.nama_pembimbing
    ORDER BY 
        tp.nama_tempat ASC
";

$result_tempat = $koneksi->query($query_tempat);

$data_tempat = [];
if ($result_tempat) {
    while ($row = $result_tempat->fetch_assoc()) {
        $data_tempat[] = $row;
    }
}

// 3. Ambil Daftar Pembimbing untuk Dropdown Modal
$query_pembimbing = "SELECT id_pembimbing, nama_pembimbing FROM pembimbing ORDER BY nama_pembimbing ASC";
$result_pembimbing = $koneksi->query($query_pembimbing);

$data_pembimbing = [];
if ($result_pembimbing) {
    while ($row = $result_pembimbing->fetch_assoc()) {
        $data_pembimbing[] = $row;
    }
}

$koneksi->close();
?>

<div class="card shadow-lg mb-4">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0"><i class="fas fa-building me-2"></i> Data Tempat PKL</h5>
    </div>
    <div class="card-body container-form">

        <!-- Tombol Aksi Atas -->
        <div class="d-flex flex-wrap gap-2 mb-4">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                data-bs-target="#tambahTempatPklModal" id="btnTambahTempatPkl">
                <i class="fas fa-plus me-2"></i> Tambah Tempat PKL
            </button>
        </div>

        <div class="table-responsive">
            <table id="tempatPklTable" class="table table-hover align-middle" style="width:100%">
                <thead>
                    <tr>
                        <th style="width: 40px;">No.</th>
                        <th>Nama Tempat PKL</th>
                        <th style="width: 140px;">Alamat</th>
                        <th style="width: 90px;">Kota</th>
                        <th style="width: 110px;">No. Telepon</th>
                        <th style="width: 130px;">Catatan</th>
                        <th style="width: 80px;" class="text-center">Kapasitas</th>
                        <th style="width: 140px;">Nama Pembimbing</th>
                        <th style="width: 100px;" class="text-center">Jumlah Siswa</th>
                        <th style="width: 130px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($data_tempat as $tempat): ?>
                        <tr>
                            <td class="text-center"><?php echo $no++; ?></td>
                            <td><strong><?php echo htmlspecialchars($tempat['nama_tempat'] ?? ''); ?></strong></td>
                            <td><?php if (empty($tempat['alamat']))
                                echo '-';
                            else
                                echo htmlspecialchars($tempat['alamat'] ?? ''); ?>
                            </td>
                            <td><?php if (empty($tempat['kota']))
                                echo '-';
                            else
                                echo htmlspecialchars($tempat['kota'] ?? ''); ?>
                            </td>
                            <td>
                                <?php if (!empty($tempat['no_telepon'])): ?>
                                    <?php echo htmlspecialchars($tempat['no_telepon']); ?>
                                <?php else: ?>
                                    <span class="text-muted fst-italic small">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($tempat['catatan'])): ?>
                                    <?php 
                                        $catatan_raw = $tempat['catatan'];
                                        if (mb_strlen($catatan_raw) > 20): 
                                            $catatan_short = mb_substr($catatan_raw, 0, 20);
                                    ?>
                                        <span class="catatan-wrapper">
                                            <span class="catatan-short"><?php echo htmlspecialchars($catatan_short); ?>...</span>
                                            <span class="catatan-full d-none"><?php echo nl2br(htmlspecialchars($catatan_raw)); ?></span>
                                            <a href="javascript:void(0);" class="toggle-catatan text-primary text-decoration-none ms-1 small fw-semibold">Baca Selengkapnya</a>
                                        </span>
                                    <?php else: ?>
                                        <span><?php echo nl2br(htmlspecialchars($catatan_raw)); ?></span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted fst-italic small">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if (!empty($tempat['kapasitas']) && $tempat['kapasitas'] > 0): ?>
                                    <span class="badge bg-primary fs-6"><?php echo (int)$tempat['kapasitas']; ?></span>
                                <?php else: ?>
                                    <span class="badge bg-light text-muted border">0 (Bebas)</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                if ($tempat['id_pembimbing'] != 0 && !empty($tempat['nama_pembimbing'])) {
                                    echo htmlspecialchars($tempat['nama_pembimbing'] ?? '');
                                } else {
                                    echo '-';
                                }
                                ?>
                            </td>
                            <td class="text-center">
                                <?php
                                if ($tempat['jumlah_siswa'] > 0) {
                                    $badge_class = 'bg-success';
                                    if (!empty($tempat['kapasitas']) && $tempat['kapasitas'] > 0 && $tempat['jumlah_siswa'] >= $tempat['kapasitas']) {
                                        $badge_class = 'bg-danger';
                                    }
                                    echo '<span class="badge ' . $badge_class . '">' . $tempat['jumlah_siswa'] . '</span>';
                                } else {
                                    echo '<span class="badge bg-secondary">Belum Terisi</span>';
                                }
                                ?>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-info text-white" data-bs-toggle="modal"
                                        data-bs-target="#tugaskanPembimbingModal"
                                        data-id="<?php echo $tempat['id_tempat']; ?>"
                                        data-tempat="<?php echo htmlspecialchars($tempat['nama_tempat'] ?? ''); ?>"
                                        data-pembimbing-id="<?php echo $tempat['id_pembimbing']; ?>"
                                        title="Tugaskan/Ubah Pembimbing">
                                        <i class="fas fa-user-tie me-1"></i>
                                    </button>
                                    <button class="btn btn-sm btn-warning text-white" data-bs-toggle="modal"
                                        data-bs-target="#editTempatPklModal" data-id="<?php echo $tempat['id_tempat']; ?>"
                                        data-tempat="<?php echo htmlspecialchars($tempat['nama_tempat'] ?? ''); ?>"
                                        data-alamat="<?php echo htmlspecialchars($tempat['alamat'] ?? ''); ?>"
                                        data-kota="<?php echo htmlspecialchars($tempat['kota'] ?? ''); ?>"
                                        data-telepon="<?php echo htmlspecialchars($tempat['no_telepon'] ?? ''); ?>"
                                        data-catatan="<?php echo htmlspecialchars($tempat['catatan'] ?? ''); ?>"
                                        data-kapasitas="<?php echo (int)($tempat['kapasitas'] ?? 0); ?>"
                                        title="Edit Tempat PKL">
                                        <i class="fas fa-edit me-1"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger text-white btn-hapus-tempat" data-bs-toggle="modal"
                                        data-bs-target="#hapusTempatPklModal" data-id="<?php echo $tempat['id_tempat']; ?>"
                                        data-tempat="<?php echo htmlspecialchars($tempat['nama_tempat'] ?? ''); ?>"
                                        data-siswa="<?php echo (int)($tempat['jumlah_siswa'] ?? 0); ?>"
                                        title="Hapus Tempat PKL">
                                        <i class="fas fa-trash me-1"></i>
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

<div class="modal fade" id="tugaskanPembimbingModal" tabindex="-1" aria-labelledby="tugaskanPembimbingModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="tugaskanPembimbingModalLabel">Tugaskan Pembimbing</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form id="formTugaskanPembimbing">
                <div class="modal-body">
                    <input type="hidden" id="modal_id_tempat" name="id_tempat">
                    <div class="mb-3">
                        <label for="modal_nama_tempat" class="form-label">Tempat PKL</label>
                        <input type="text" class="form-control" id="modal_nama_tempat" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="modal_id_pembimbing" class="form-label">Pilih Pembimbing</label>
                        <select class="form-select" id="modal_id_pembimbing" name="id_pembimbing" required>
                            <option value="0">-- Belum Ditugaskan --</option>
                            <?php foreach ($data_pembimbing as $pembimbing): ?>
                                <option value="<?php echo $pembimbing['id_pembimbing']; ?>">
                                    <?php echo htmlspecialchars($pembimbing['nama_pembimbing'] ?? ''); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnTugaskan">Tugaskan Pembimbing</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tambah Tempat PKL -->
<div class="modal fade" id="tambahTempatPklModal" tabindex="-1" aria-labelledby="tambahTempatPklModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="tambahTempatPklModalLabel"><i class="fas fa-plus-circle me-2"></i>Tambah Tempat PKL
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form id="formTambahTempatPkl">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="tambah_nama_tempat" class="form-label">Nama Tempat PKL <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="tambah_nama_tempat" name="nama_tempat" required
                            placeholder="Contoh: PT Telkom Indonesia">
                    </div>
                    <div class="mb-3">
                        <label for="tambah_alamat" class="form-label">Alamat</label>
                        <textarea class="form-control" id="tambah_alamat" name="alamat" rows="3"
                            placeholder="Masukkan alamat tempat PKL..."></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tambah_kota" class="form-label">Kota</label>
                            <input type="text" class="form-control" id="tambah_kota" name="kota"
                                placeholder="Contoh: Sumedang">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tambah_kapasitas" class="form-label">Kapasitas Siswa</label>
                            <input type="number" class="form-control" id="tambah_kapasitas" name="kapasitas" min="0" value="0"
                                placeholder="0 = Bebas / Tanpa batas">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="tambah_no_telepon" class="form-label">No. Telepon</label>
                        <input type="tel" class="form-control" id="tambah_no_telepon" name="no_telepon"
                            placeholder="Contoh: (0261) 201234 atau 08123456789">
                    </div>
                    <div class="mb-3">
                        <label for="tambah_id_pembimbing" class="form-label">Pembimbing Sekolah</label>
                        <select class="form-select" id="tambah_id_pembimbing" name="id_pembimbing">
                            <option value="0">-- Belum Ditugaskan --</option>
                            <?php foreach ($data_pembimbing as $pembimbing): ?>
                                <option value="<?php echo $pembimbing['id_pembimbing']; ?>">
                                    <?php echo htmlspecialchars($pembimbing['nama_pembimbing'] ?? ''); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="tambah_catatan" class="form-label">Catatan</label>
                        <textarea class="form-control" id="tambah_catatan" name="catatan" rows="3"
                            placeholder="Tambahkan catatan khusus terkait tempat PKL ini..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSimpanTambah">
                        <i class="fas fa-save me-1"></i> Simpan Tempat PKL
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Tempat PKL -->
<div class="modal fade" id="editTempatPklModal" tabindex="-1" aria-labelledby="editTempatPklModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="editTempatPklModalLabel"><i class="fas fa-edit me-2"></i>Edit Tempat PKL
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form id="formEditTempatPkl">
                <div class="modal-body">
                    <input type="hidden" id="edit_id_tempat" name="id_tempat">
                    <div class="mb-3">
                        <label for="edit_nama_tempat" class="form-label">Nama Tempat PKL <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_nama_tempat" name="nama_tempat" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_alamat" class="form-label">Alamat</label>
                        <textarea class="form-control" id="edit_alamat" name="alamat" rows="3"
                            placeholder="Masukkan alamat tempat PKL..."></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_kota" class="form-label">Kota</label>
                            <input type="text" class="form-control" id="edit_kota" name="kota"
                                placeholder="Contoh: Sumedang">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_kapasitas" class="form-label">Kapasitas Siswa</label>
                            <input type="number" class="form-control" id="edit_kapasitas" name="kapasitas" min="0"
                                placeholder="0 = Bebas / Tanpa batas">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_no_telepon" class="form-label">No. Telepon</label>
                        <input type="tel" class="form-control" id="edit_no_telepon" name="no_telepon"
                            placeholder="Contoh: (0261) 201234 atau 08123456789">
                    </div>
                    <div class="mb-3">
                        <label for="edit_catatan" class="form-label">Catatan</label>
                        <textarea class="form-control" id="edit_catatan" name="catatan" rows="3"
                            placeholder="Tambahkan catatan khusus terkait tempat PKL ini..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning text-white" id="btnSimpanEdit">Simpan
                        Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Hapus Tempat PKL -->
<div class="modal fade" id="hapusTempatPklModal" tabindex="-1" aria-labelledby="hapusTempatPklModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="hapusTempatPklModalLabel"><i
                        class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Hapus Tempat PKL</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="hapus_id_tempat">
                <p class="mb-2">Apakah Anda yakin ingin menghapus tempat PKL berikut?</p>
                <div class="p-3 bg-light rounded border mb-3">
                    <strong class="text-danger fs-5" id="hapus_nama_tempat"></strong>
                </div>
                <div class="alert alert-warning small mb-0" id="hapus_warning_tempat">
                    <i class="fas fa-info-circle me-1"></i>
                    Penempatan siswa pada tempat PKL ini akan dinetralkan secara otomatis.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="btnConfirmHapusTempat">
                    <span class="spinner-border spinner-border-sm me-1 d-none" id="spinnerHapusTempat"></span>
                    <i class="fas fa-trash me-1" id="iconHapusTempat"></i> Hapus Tempat PKL
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Inisialisasi DataTables
        var table = $('#tempatPklTable').DataTable({
            "order": [
                [1, 'asc']
            ], // Urutkan berdasarkan Nama Tempat
            "columnDefs": [{
                "orderable": false,
                "searchable": false,
                "targets": [0, 9]
            },
            {
                "className": "text-center",
                "targets": [0, 6, 8, 9]
            }
            ],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
            },
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>B<"row"<"col-sm-12"tr>><"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            buttons: [{
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel me-2"></i> Export ke Excel',
                titleAttr: 'Export Data ke Excel',
                className: 'btn btn-success',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8],
                    format: {
                        body: function (data, row, column, node) {
                            if (column === 5) {
                                var full = $(node).find('.catatan-full');
                                if (full.length) {
                                    return full.text().trim();
                                }
                            }
                            return $(node).text().trim();
                        }
                    }
                },
                title: 'Data Tempat PKL SMK Informatika Sumedang',
                filename: 'Data_Tempat_PKL_' + new Date().toISOString().slice(0, 10)
            }],
            "pageLength": 10,
            "lengthMenu": [
                [10, 25, 50, -1],
                [10, 25, 50, "Semua"]
            ],
            "responsive": true
        });

        // Event handler: Toggle Baca Selengkapnya / Tutup pada Catatan
        $('#tempatPklTable tbody').on('click', '.toggle-catatan', function (e) {
            e.preventDefault();
            var wrapper = $(this).closest('.catatan-wrapper');
            var shortText = wrapper.find('.catatan-short');
            var fullText = wrapper.find('.catatan-full');

            if (fullText.hasClass('d-none')) {
                shortText.addClass('d-none');
                fullText.removeClass('d-none');
                $(this).text('Tutup');
            } else {
                fullText.addClass('d-none');
                shortText.removeClass('d-none');
                $(this).text('Baca Selengkapnya');
            }
        });

        // Event handler untuk menampilkan data di modal saat tombol diklik
        $('#tugaskanPembimbingModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Tombol yang memicu modal
            var id_tempat = button.data('id');
            var nama_tempat = button.data('tempat');
            var pembimbing_id = button.data('pembimbing-id');

            var modal = $(this);
            modal.find('#modal_id_tempat').val(id_tempat);
            modal.find('#modal_nama_tempat').val(nama_tempat);
            modal.find('#modal_id_pembimbing').val(pembimbing_id); // Memilih pembimbing yang sudah ada
        });

        // Event handler untuk submit form penugasan pembimbing
        $('#formTugaskanPembimbing').on('submit', function (e) {
            e.preventDefault();

            const id_tempat = $('#modal_id_tempat').val();
            const id_pembimbing = $('#modal_id_pembimbing').val();
            const nama_tempat = $('#modal_nama_tempat').val();
            const submitBtn = $('#btnTugaskan');

            // Nonaktifkan tombol
            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> Menyimpan...');

            $.ajax({
                url: 'ajax/tugaskan_pembimbing.php',
                type: 'POST',
                data: {
                    id_tempat: id_tempat,
                    id_pembimbing: id_pembimbing
                },
                dataType: 'json',
                success: function (response) {
                    $('#tugaskanPembimbingModal').modal('hide');

                    if (response.status === 'success') {
                        alert(response.message);
                        loadContent('data_tempat_pkl.php');
                    } else {
                        alert('Gagal: ' + response.message);
                    }
                },
                error: function (xhr, status, error) {
                    $('#tugaskanPembimbingModal').modal('hide');
                    console.error("AJAX Error:", status, error, xhr.responseText);
                    alert('Terjadi kesalahan saat menghubungi server: Silakan cek log atau detail error.');
                },
                complete: function () {
                    submitBtn.prop('disabled', false).html('Tugaskan Pembimbing');
                }
            });
        });

        // Event handler untuk menampilkan data di modal Edit saat tombol diklik
        $('#editTempatPklModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var id_tempat = button.data('id');
            var nama_tempat = button.data('tempat');
            var alamat = button.data('alamat') || '';
            var kota = button.data('kota') || '';
            var telepon = button.data('telepon') || '';
            var catatan = button.data('catatan') || '';
            var kapasitas = button.data('kapasitas') !== undefined ? button.data('kapasitas') : 0;

            var modal = $(this);
            modal.find('#edit_id_tempat').val(id_tempat);
            modal.find('#edit_nama_tempat').val(nama_tempat);
            modal.find('#edit_alamat').val(alamat);
            modal.find('#edit_kota').val(kota);
            modal.find('#edit_no_telepon').val(telepon);
            modal.find('#edit_catatan').val(catatan);
            modal.find('#edit_kapasitas').val(kapasitas);
        });

        // Event handler untuk submit form Edit Tempat PKL
        $('#formEditTempatPkl').on('submit', function (e) {
            e.preventDefault();

            const submitBtn = $('#btnSimpanEdit');
            const originalText = submitBtn.html();

            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> Menyimpan...');

            $.ajax({
                url: 'ajax/edit_tempat_pkl.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (response) {
                    $('#editTempatPklModal').modal('hide');

                    if (response.status === 'success') {
                        // alert(response.message);
                        loadContent('data_tempat_pkl.php');
                    } else {
                        alert('Gagal: ' + response.message);
                    }
                },
                error: function (xhr, status, error) {
                    $('#editTempatPklModal').modal('hide');
                    console.error("AJAX Error:", status, error, xhr.responseText);
                    alert('Terjadi kesalahan saat menghubungi server: Silakan cek log atau detail error.');
                },
                complete: function () {
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });

        // Event handler saat modal Tambah Tempat PKL dibuka
        $('#tambahTempatPklModal').on('show.bs.modal', function () {
            $('#formTambahTempatPkl')[0].reset();
            $('#tambah_kapasitas').val(0);
            $('#tambah_id_pembimbing').val(0);
        });

        // Event handler untuk submit form Tambah Tempat PKL
        $('#formTambahTempatPkl').on('submit', function (e) {
            e.preventDefault();

            const submitBtn = $('#btnSimpanTambah');
            const originalText = submitBtn.html();

            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> Menyimpan...');

            $.ajax({
                url: 'ajax/tambah_tempat_pkl.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (response) {
                    $('#tambahTempatPklModal').modal('hide');

                    if (response.status === 'success') {
                        loadContent('data_tempat_pkl.php');
                    } else {
                        alert('Gagal: ' + response.message);
                    }
                },
                error: function (xhr, status, error) {
                    $('#tambahTempatPklModal').modal('hide');
                    console.error("AJAX Error:", status, error, xhr.responseText);
                    alert('Terjadi kesalahan saat menghubungi server: Silakan cek log atau detail error.');
                },
                complete: function () {
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });

        // Event handler saat modal Hapus Tempat PKL dibuka
        $('#hapusTempatPklModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var nama = button.data('tempat');
            var siswa = parseInt(button.data('siswa')) || 0;

            $('#hapus_id_tempat').val(id);
            $('#hapus_nama_tempat').text(nama);

            if (siswa > 0) {
                $('#hapus_warning_tempat').html('<i class="fas fa-exclamation-triangle me-1"></i> <strong>Perhatian:</strong> Tempat PKL ini saat ini memiliki <strong>' + siswa + ' siswa</strong> yang terdaftar. Menghapus tempat PKL akan mereset status penempatan siswa tersebut menjadi belum ditempatkan.');
            } else {
                $('#hapus_warning_tempat').html('<i class="fas fa-info-circle me-1"></i> Data tempat PKL ini belum memiliki siswa yang ditempatkan.');
            }
        });

        // Eksekusi Hapus Tempat PKL
        $('#btnConfirmHapusTempat').on('click', function () {
            var id = $('#hapus_id_tempat').val();
            var btn = $(this);
            var spinner = $('#spinnerHapusTempat');
            var icon = $('#iconHapusTempat');

            btn.prop('disabled', true);
            spinner.removeClass('d-none');
            icon.addClass('d-none');

            $.ajax({
                url: 'ajax/hapus_tempat_pkl.php',
                type: 'POST',
                data: { id_tempat: id },
                dataType: 'json',
                success: function (res) {
                    $('#hapusTempatPklModal').modal('hide');
                    if (res.status === 'success') {
                        loadContent('data_tempat_pkl.php');
                    } else {
                        alert('Gagal menghapus: ' + res.message);
                    }
                },
                error: function (xhr) {
                    $('#hapusTempatPklModal').modal('hide');
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
    });
</script>