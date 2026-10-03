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
        s.nama_siswa AS nama_siswa_display,
        s.id_pembimbing,
        p.nama_pembimbing,
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
        pembimbing p ON s.id_pembimbing = p.id_pembimbing
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

// 3. Ambil Daftar Pembimbing untuk pilihan di Modal Edit Siswa
$query_pembimbing_list = "SELECT id_pembimbing, nama_pembimbing FROM pembimbing ORDER BY nama_pembimbing ASC";
$res_pembimbing_list = $koneksi->query($query_pembimbing_list);
$list_pembimbing = [];
if ($res_pembimbing_list) {
    while ($p = $res_pembimbing_list->fetch_assoc()) {
        $list_pembimbing[] = $p;
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
            <button type="button" class="btn btn-success text-white" data-bs-toggle="modal"
                data-bs-target="#importSiswaModal">
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
                        <th style="width: 140px;">Kontak Siswa</th>
                        <th style="width: 180px;">Pembimbing</th>
                        <th style="width: 140px;">Penempatan PKL</th>
                        <th style="width: 160px;" class="text-center">Aksi</th>
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
                                if (!empty($siswa['nama_pembimbing'])) {
                                    echo htmlspecialchars($siswa['nama_pembimbing']);
                                } else {
                                    echo '<span class="text-muted fst-italic small">Belum Ditentukan</span>';
                                }
                                ?>
                            </td>
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
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-warning text-white edit-btn btn-edit-siswa"
                                        data-id="<?php echo $siswa['id_siswa']; ?>"
                                        data-nis="<?php echo htmlspecialchars($siswa['nis']); ?>"
                                        data-nama="<?php echo htmlspecialchars($siswa['nama_siswa']); ?>"
                                        data-kelas="<?php echo htmlspecialchars($siswa['kelas']); ?>"
                                        data-kontak="<?php echo htmlspecialchars($siswa['kontak_siswa']); ?>"
                                        data-pembimbing="<?php echo $siswa['id_pembimbing']; ?>" title="Edit Siswa">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-secondary text-white btn-reset-password"
                                        data-id="<?php echo $siswa['id_siswa']; ?>"
                                        data-nis="<?php echo htmlspecialchars($siswa['nis']); ?>"
                                        data-nama="<?php echo htmlspecialchars($siswa['nama_siswa']); ?>"
                                        title="Reset Password ke NIS">
                                        <i class="fas fa-key"></i>
                                    </button>
                                    <button class="btn btn-sm btn-info text-white btn-atur-penempatan"
                                        data-id="<?php echo $siswa['id_siswa']; ?>"
                                        data-nama="<?php echo htmlspecialchars($siswa['nama_siswa']); ?>"
                                        data-id-pembimbing="<?php echo (int) $siswa['id_pembimbing']; ?>"
                                        data-id-tempat="<?php echo (int) $siswa['id_tempat']; ?>"
                                        title="Atur Penempatan PKL">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-btn"
                                        data-id="<?php echo $siswa['id_siswa']; ?>" title="Hapus">
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
    $(document).ready(function () {
        // Inisialisasi DataTables
        var table = $('#siswaTable').DataTable({
            "order": [
                [3, 'asc'],
                [2, 'asc']
            ], // Urutkan berdasarkan Kelas dan Nama
            "columnDefs": [{
                "orderable": false,
                "searchable": false,
                "targets": [7] // Kolom Aksi (7) non-sortable/searchable
            },
            {
                "className": "text-center",
                "targets": [0, 7]
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
                    columns: [0, 1, 2, 3, 4, 5, 6] // Export semua kolom data (termasuk pembimbing dan status)
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
        $('#tambahSiswaBtn').on('click', function (e) {
            e.preventDefault();
            alert("Aksi Tambah Siswa akan diarahkan ke form input.");
            // loadContent('form_tambah_siswa.php'); // Contoh penggunaan loadContent
        });

        // Event handler untuk tombol Edit Siswa
        $('#siswaTable tbody').on('click', '.btn-edit-siswa, .edit-btn', function () {
            var id = $(this).data('id');
            var nis = $(this).data('nis');
            var nama = $(this).data('nama');
            var kelas = $(this).data('kelas');
            var kontak = $(this).data('kontak');
            var pembimbing = $(this).data('pembimbing') || 0;

            $('#edit_id_siswa').val(id);
            $('#edit_nis').val(nis);
            $('#edit_nama_siswa').val(nama);
            $('#edit_kelas').val(kelas);
            $('#edit_kontak_siswa').val(kontak);
            $('#alertEditSiswa').addClass('d-none').removeClass('alert-success alert-danger').html('');

            $('#editSiswaModal').modal('show');
        });

        // Event handler: Submit form edit siswa
        $('#formEditSiswa').on('submit', function (e) {
            e.preventDefault();
            var alertBox = $('#alertEditSiswa');
            var btn = $('#btnSubmitEditSiswa');
            var spinner = $('#spinnerEditSiswa');
            var icon = $('#iconEditSiswa');

            btn.prop('disabled', true);
            spinner.removeClass('d-none');
            icon.addClass('d-none');
            alertBox.addClass('d-none').removeClass('alert-success alert-danger');

            $.ajax({
                url: 'ajax/edit_siswa.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (res) {
                    if (res.status === 'success') {
                        alertBox.addClass('alert-success').html('<i class="fas fa-check-circle me-1"></i> ' + res.message).removeClass('d-none');
                        setTimeout(function () {
                            $('#editSiswaModal').modal('hide');
                            alertBox.addClass('d-none');
                            loadContent('data_siswa.php');
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

        // Event handler untuk tombol Hapus
        $('#siswaTable tbody').on('click', '.delete-btn', function () {
            var id = $(this).data('id');
            if (confirm('Anda yakin ingin menghapus data siswa ini? Tindakan ini tidak dapat dibatalkan.')) {
                alert('Aksi Hapus Siswa ID: ' + id + ' (AJAX call to delete_siswa.php).');
                // Implementasi AJAX call untuk penghapusan
            }
        });

        // ─── Event handler: Tombol Atur Penempatan PKL ───────────────────────
        $('#siswaTable tbody').on('click', '.btn-atur-penempatan', function () {
            var id = $(this).data('id');
            var nama = $(this).data('nama');
            var idPemb = $(this).data('id-pembimbing') || 0;
            var idTempat = $(this).data('id-tempat') || 0;

            // Set nilai form
            $('#penempatan_id_siswa').val(id);
            $('#penempatan_nama_siswa').text(nama);
            $('#penempatan_id_pembimbing').val(idPemb);
            $('#alertPenempatan').addClass('d-none').removeClass('alert-success alert-danger').html('');

            // Reset & populate select tempat PKL
            var $selectTempat = $('#penempatan_id_tempat');
            $selectTempat.html('<option value="0">⏳ Memuat data...</option>').prop('disabled', true);

            // Tampilkan modal dulu
            $('#aturPenempatanModal').modal('show');

            // Fetch daftar tempat PKL via AJAX
            $.getJSON('get_tempat_pkl.php', function (data) {
                $selectTempat.html('<option value="0">-- Belum Ditentukan --</option>');
                $.each(data, function (i, tempat) {
                    var label = tempat.nama_tempat;
                    if (tempat.kota) label += ' – ' + tempat.kota;
                    $selectTempat.append(
                        $('<option>').val(tempat.id_tempat).text(label)
                    );
                });
                $selectTempat.val(idTempat).prop('disabled', false);
            }).fail(function () {
                $selectTempat.html('<option value="0">⚠️ Gagal memuat data</option>').prop('disabled', false);
            });
        });

        // Event handler: Submit form Atur Penempatan
        $('#formAturPenempatan').on('submit', function (e) {
            e.preventDefault();
            var alertBox = $('#alertPenempatan');
            var btn = $('#btnSubmitPenempatan');
            var spinner = $('#spinnerPenempatan');
            var icon = $('#iconPenempatan');

            btn.prop('disabled', true);
            spinner.removeClass('d-none');
            icon.addClass('d-none');
            alertBox.addClass('d-none').removeClass('alert-success alert-danger');

            $.ajax({
                url: 'ajax/update_penempatan_siswa.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (res) {
                    if (res.status === 'success') {
                        alertBox.addClass('alert-success').html('<i class="fas fa-check-circle me-1"></i> ' + res.message).removeClass('d-none');
                        setTimeout(function () {
                            $('#aturPenempatanModal').modal('hide');
                            alertBox.addClass('d-none');
                            loadContent('data_siswa.php');
                        }, 1200);
                    } else {
                        alertBox.addClass('alert-danger').html('<i class="fas fa-times-circle me-1"></i> ' + res.message).removeClass('d-none');
                    }
                },
                error: function (xhr) {
                    var msg = 'Terjadi kesalahan pada server.';
                    try { var r = JSON.parse(xhr.responseText); if (r.message) msg = r.message; } catch (e) { }
                    alertBox.addClass('alert-danger').html('<i class="fas fa-times-circle me-1"></i> ' + msg).removeClass('d-none');
                },
                complete: function () {
                    btn.prop('disabled', false);
                    spinner.addClass('d-none');
                    icon.removeClass('d-none');
                }
            });
        });

        // Reset modal Atur Penempatan saat ditutup
        $('#aturPenempatanModal').on('hidden.bs.modal', function () {
            $('#alertPenempatan').addClass('d-none').html('');
        });

        // ─── Reset Password Siswa ───────────────────────────────────────────
        // 1. Trigger Modal Reset Password Siswa
        $('#siswaTable tbody').on('click', '.btn-reset-password', function () {
            var id = $(this).data('id');
            var nis = $(this).data('nis');
            var nama = $(this).data('nama');

            $('#reset_pw_id_siswa').val(id);
            $('#reset_pw_nis_siswa').val(nis);
            $('#reset_pw_nama_siswa').text(nama);
            $('#reset_pw_nis_display').text(nis);
            $('#reset_pw_nis_info').text(nis);
            $('#alertResetPassword').addClass('d-none').removeClass('alert-success alert-danger').html('');

            $('#resetPasswordModal').modal('show');
        });

        // 2. Eksekusi Reset Password Siswa
        $('#btnConfirmResetPassword').on('click', function () {
            var id = $('#reset_pw_id_siswa').val();
            var nisFallback = $('#reset_pw_nis_siswa').val();
            var btn = $(this);
            var spinner = $('#spinnerResetPw');
            var icon = $('#iconResetPw');
            var alertBox = $('#alertResetPassword');

            // Susun teks format login
            var textToCopy = "Reset Password berhasil. Silahkan login melalui:\r\nhttps://pkl.smkifsu.sch.id/\r\nNIS: " + nisFallback + "\r\nPassword: " + nisFallback;

            // Salin langsung saat event click aktif (menjamin user gesture valid dan tidak diblok browser)
            copyToClipboard(textToCopy);
            $('#reset_pw_copy_text').val(textToCopy);

            btn.prop('disabled', true);
            spinner.removeClass('d-none');
            icon.addClass('d-none');
            alertBox.addClass('d-none').removeClass('alert-success alert-danger');

            $.ajax({
                url: 'ajax/reset_password_siswa.php',
                type: 'POST',
                data: { id_siswa: id },
                dataType: 'json',
                success: function (res) {
                    if (res.status === 'success') {
                        var nis = res.nis || nisFallback;
                        var finalCopyText = "Reset Password berhasil. Silahkan login melalui:\r\nhttps://pkl.smkifsu.sch.id/\r\nNIS: " + nis + "\r\nPassword: " + nis;

                        // Salin ulang untuk memastikan NIS final dari server
                        copyToClipboard(finalCopyText);
                        $('#reset_pw_copy_text').val(finalCopyText);

                        alertBox.addClass('alert-success').html(
                            '<div class="d-flex align-items-center justify-content-between mb-2">' +
                            '<span><i class="fas fa-check-circle me-1"></i> ' + res.message + '</span>' +
                            '<button type="button" class="btn btn-sm btn-success text-white py-0 px-2" id="btnSalinUlang" style="font-size:0.8rem;">' +
                            '<i class="fas fa-copy me-1"></i> <span id="txtBtnSalin">Salin</span>' +
                            '</button>' +
                            '</div>' +
                            '<div class="p-2 bg-white rounded border text-dark font-monospace small" style="white-space: pre-wrap; user-select: all;">' +
                            escapeHtml(finalCopyText) +
                            '</div>' +
                            '<div class="mt-2 small text-success fw-semibold"><i class="fas fa-clipboard-check me-1"></i> Teks di atas otomatis disalin ke clipboard!</div>'
                        ).removeClass('d-none');

                        // Ganti tombol Batal menjadi Tutup & sembunyikan tombol konfirmasi
                        btn.addClass('d-none');
                        $('#btnBatalResetPw').text('Tutup').removeClass('btn-secondary').addClass('btn-primary');

                        setTimeout(function () {
                            $('#resetPasswordModal').modal('hide');
                        }, 4000);
                    } else {
                        alertBox.addClass('alert-danger').html('<i class="fas fa-times-circle me-1"></i> ' + res.message).removeClass('d-none');
                    }
                },
                error: function (xhr) {
                    var msg = 'Terjadi kesalahan pada server.';
                    try { var r = JSON.parse(xhr.responseText); if (r.message) msg = r.message; } catch (e) { }
                    alertBox.addClass('alert-danger').html('<i class="fas fa-times-circle me-1"></i> ' + msg).removeClass('d-none');
                },
                complete: function () {
                    btn.prop('disabled', false);
                    spinner.addClass('d-none');
                    icon.removeClass('d-none');
                }
            });
        });

        // 3. Reset modal Reset Password saat ditutup
        $('#resetPasswordModal').on('hidden.bs.modal', function () {
            $('#alertResetPassword').addClass('d-none').html('');
            $('#btnConfirmResetPassword').removeClass('d-none').prop('disabled', false);
            $('#btnBatalResetPw').text('Batal').removeClass('btn-primary').addClass('btn-secondary');
        });

        // Handler tombol salin manual jika user ingin mengklik ulang
        $(document).on('click', '#btnSalinUlang', function () {
            var txt = $('#reset_pw_copy_text').val();
            if (txt) {
                copyToClipboard(txt);
                $('#txtBtnSalin').text('Tersalin!');
                setTimeout(function () {
                    $('#txtBtnSalin').text('Salin Teks');
                }, 2000);
            }
        });

        // 4. Helper clipboard & escape HTML
        function copyToClipboard(text) {
            if (!text) return false;

            // Eksekusi fallback di dalam modal (bekerja di http, localhost, maupun HTTPS)
            var copied = fallbackCopyText(text);

            // Jika browser mendukung navigator.clipboard
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).catch(function () { });
            }

            return copied;
        }

        function fallbackCopyText(text) {
            var modalBody = document.querySelector('#resetPasswordModal .modal-body') || document.body;
            var textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.setAttribute("readonly", "");
            textArea.style.position = "absolute";
            textArea.style.left = "-9999px";
            textArea.style.top = "0";
            textArea.style.width = "200px";
            textArea.style.height = "100px";
            textArea.style.border = "none";
            textArea.style.outline = "none";
            textArea.style.background = "transparent";

            modalBody.appendChild(textArea);

            var successful = false;
            try {
                textArea.focus();
                textArea.select();
                textArea.setSelectionRange(0, text.length);
                successful = document.execCommand('copy');
            } catch (err) {
                console.error('Fallback execCommand error:', err);
                successful = false;
            }

            try {
                modalBody.removeChild(textArea);
            } catch (e) { }

            return successful;
        }

        function escapeHtml(str) {
            return $('<div>').text(str).html();
        }

        // Event handler: preview nama file saat dipilih
        $('#fileExcelInput').on('change', function () {
            const file = this.files[0];
            if (file) {
                $('#fileNameDisplay').text(file.name);
                $('#importAlert').addClass('d-none');
            } else {
                $('#fileNameDisplay').text('Belum ada file dipilih...');
            }
        });

        // Event handler: Submit form import siswa
        $('#formImportSiswa').on('submit', function (e) {
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
                success: function (response) {
                    if (response.status === 'success') {
                        showImportAlert('success', '<i class="fas fa-check-circle me-2"></i>' + response.message);
                        // Reset form
                        fileInput.value = '';
                        $('#fileNameDisplay').text('Belum ada file dipilih...');
                        // Reload table data setelah sukses
                        setTimeout(function () {
                            $('#importSiswaModal').modal('hide');
                            loadContent('data_siswa.php');
                        }, 2000);
                    } else {
                        showImportAlert('danger', '<i class="fas fa-times-circle me-2"></i>' + response.message);
                    }
                },
                error: function (xhr) {
                    let msg = 'Terjadi kesalahan saat menghubungi server.';
                    try {
                        const resp = JSON.parse(xhr.responseText);
                        if (resp.message) msg = resp.message;
                    } catch (e) { }
                    showImportAlert('danger', '<i class="fas fa-times-circle me-2"></i>' + msg);
                },
                complete: function () {
                    submitBtn.prop('disabled', false).html('<i class="fas fa-upload me-2"></i> Import Sekarang');
                }
            });
        });

        // Reset modal saat ditutup
        $('#importSiswaModal').on('hidden.bs.modal', function () {
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
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
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
                            <label class="input-group-text btn btn-outline-success" for="fileExcelInput"
                                style="cursor:pointer;">
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

<!-- Modal Edit Data Siswa -->
<div class="modal fade" id="editSiswaModal" tabindex="-1" aria-labelledby="editSiswaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="editSiswaModalLabel">
                    <i class="fas fa-user-edit me-2"></i> Edit Data Siswa
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form id="formEditSiswa">
                <div class="modal-body">
                    <div id="alertEditSiswa" class="alert d-none"></div>
                    <input type="hidden" id="edit_id_siswa" name="id_siswa">

                    <div class="mb-3">
                        <label for="edit_nis" class="form-label fw-semibold">NIS (Nomor Induk Siswa) <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_nis" name="nis" placeholder="Nomor Induk Siswa"
                            required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_nama_siswa" class="form-label fw-semibold">Nama Lengkap Siswa <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_nama_siswa" name="nama_siswa"
                            placeholder="Nama lengkap siswa" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_kelas" class="form-label fw-semibold">Kelas <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_kelas" name="kelas"
                                placeholder="Contoh: XII-RPL 1" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_kontak_siswa" class="form-label fw-semibold">No. HP / WA</label>
                            <input type="tel" class="form-control" id="edit_kontak_siswa" name="kontak_siswa"
                                placeholder="08xxxxxxxxxx">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning text-white" id="btnSubmitEditSiswa">
                        <span class="spinner-border spinner-border-sm me-1 d-none" id="spinnerEditSiswa"></span>
                        <i class="fas fa-save me-1" id="iconEditSiswa"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Atur Penempatan PKL -->
<div class="modal fade" id="aturPenempatanModal" tabindex="-1" aria-labelledby="aturPenempatanModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #0dcaf0 0%, #0a9abf 100%);">
                <h5 class="modal-title" id="aturPenempatanModalLabel">
                    <i class="fas fa-map-marker-alt me-2"></i> Atur Penempatan PKL
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form id="formAturPenempatan">
                <div class="modal-body">
                    <div id="alertPenempatan" class="alert d-none"></div>
                    <input type="hidden" id="penempatan_id_siswa" name="id_siswa">

                    <!-- Peringatan penggunaan -->
                    <div class="alert alert-warning d-flex gap-2 align-items-start py-2 mb-3">
                        <i class="fas fa-exclamation-triangle flex-shrink-0 mt-1"></i>
                        <div class="small">
                            <strong>Perhatian:</strong> Fitur ini hanya untuk koreksi manual. Penempatan PKL dan
                            penunjukan pembimbing sebaiknya dilakukan melalui <strong>proses balasan surat</strong> atau
                            menu <strong>Data Tempat PKL</strong>.
                        </div>
                    </div>

                    <!-- Info siswa -->
                    <div class="mb-4 p-3 rounded-3" style="background: #f0f9ff; border-left: 4px solid #0dcaf0;">
                        <div class="text-muted small fw-semibold mb-1"><i class="fas fa-user-graduate me-1"></i> Siswa
                            yang diatur:</div>
                        <div class="fw-bold fs-6" id="penempatan_nama_siswa">-</div>
                    </div>

                    <!-- Pilih Pembimbing -->
                    <div class="mb-3">
                        <label for="penempatan_id_pembimbing" class="form-label fw-semibold">
                            <i class="fas fa-chalkboard-teacher me-1 text-warning"></i> Guru Pembimbing
                        </label>
                        <select class="form-select" id="penempatan_id_pembimbing" name="id_pembimbing">
                            <option value="0">-- Belum Ditentukan --</option>
                            <?php foreach ($list_pembimbing as $pb): ?>
                                <option value="<?php echo $pb['id_pembimbing']; ?>">
                                    <?php echo htmlspecialchars($pb['nama_pembimbing']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Pilih Tempat PKL -->
                    <div class="mb-3">
                        <label for="penempatan_id_tempat" class="form-label fw-semibold">
                            <i class="fas fa-building me-1 text-info"></i> Tempat PKL
                        </label>
                        <select class="form-select" id="penempatan_id_tempat" name="id_tempat">
                            <option value="0">-- Belum Ditentukan --</option>
                        </select>
                        <div class="form-text">Pilih lokasi tempat siswa menjalani PKL.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info text-white" id="btnSubmitPenempatan">
                        <span class="spinner-border spinner-border-sm me-1 d-none" id="spinnerPenempatan"></span>
                        <i class="fas fa-save me-1" id="iconPenempatan"></i> Simpan Penempatan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Reset Password Siswa -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #6c757d 0%, #495057 100%);">
                <h5 class="modal-title" id="resetPasswordModalLabel">
                    <i class="fas fa-key me-2"></i> Reset Password Siswa
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="reset_pw_id_siswa">
                <input type="hidden" id="reset_pw_nis_siswa">
                <textarea id="reset_pw_copy_text" class="d-none"></textarea>
                <div id="alertResetPassword" class="alert d-none"></div>

                <p class="mb-2">Anda akan mereset password siswa berikut:</p>
                <div class="p-3 bg-light rounded border mb-3">
                    <strong class="fs-6" id="reset_pw_nama_siswa"></strong>
                    <div class="text-muted small mt-1">NIS: <span id="reset_pw_nis_display" class="fw-semibold"></span>
                    </div>
                </div>
                <div class="alert alert-info d-flex gap-2 align-items-start py-2 mb-0">
                    <i class="fas fa-info-circle flex-shrink-0 mt-1"></i>
                    <div class="small">
                        Password akan dikembalikan ke nilai default berupa NIS siswa: <strong class="text-primary"
                            id="reset_pw_nis_info">-</strong>.<br>
                        Setelah reset berhasil, info login akan otomatis disalin ke clipboard.
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="btnBatalResetPw"
                    data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-secondary text-white" id="btnConfirmResetPassword"
                    style="background:#495057;">
                    <span class="spinner-border spinner-border-sm me-1 d-none" id="spinnerResetPw"></span>
                    <i class="fas fa-key me-1" id="iconResetPw"></i> Reset Password
                </button>
            </div>
        </div>
    </div>
</div>