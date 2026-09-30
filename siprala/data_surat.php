<?php
// data_surat.php - Konten Data Surat PKL (Client-Side Datatables)

include 'koneksi.php'; // 1. Include koneksi database

// 2. Ambil Semua Data Surat dengan JOIN ke tabel tempat_pkl
$query_surat = "SELECT s.id_surat, s.no_surat, s.perihal, s.tanggal, s.status_balasan, t.nama_tempat, s.id_tempat_pkl
                FROM surat s
                LEFT JOIN tempat_pkl t ON s.id_tempat_pkl = t.id_tempat
                ORDER BY s.id_surat DESC";
$result_surat = $koneksi->query($query_surat);

$data_surat = [];
if ($result_surat) {
    while ($row = $result_surat->fetch_assoc()) {
        $data_surat[] = $row;
    }
}

// 3. Ambil data siswa yang terdaftar dalam masing-masing surat
$query_siswa = "SELECT ss.id_surat, s.id_siswa, s.nis, s.nama_siswa, s.kelas, s.kontak_siswa, ss.status, ss.catatan
                FROM siswa_surat ss
                JOIN siswa s ON ss.id_siswa = s.id_siswa
                ORDER BY s.nama_siswa ASC";
$result_siswa = $koneksi->query($query_siswa);

$siswa_by_surat = [];
if ($result_siswa) {
    while ($row_s = $result_siswa->fetch_assoc()) {
        $siswa_by_surat[$row_s['id_surat']][] = $row_s;
    }
}

$koneksi->close();
?>

<style>
    /* Styling Badge & Tombol Toggle Siswa (Clean & Lightweight) */
    .toggle-siswa-btn {
        font-size: 0.76rem;
        border-radius: 20px;
        padding: 2px 10px;
        transition: all 0.2s ease-in-out;
        background-color: #f8fafc;
        border: 1px solid #cbd5e1;
        color: #334155;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .toggle-siswa-btn:hover {
        background-color: #e2e8f0;
        border-color: #94a3b8;
        color: #0f172a;
    }
    .toggle-siswa-btn.active {
        background-color: #0d6efd !important;
        border-color: #0d6efd !important;
        color: #ffffff !important;
        box-shadow: 0 2px 4px rgba(13, 110, 253, 0.25);
    }
    .toggle-siswa-btn .toggle-icon {
        transition: transform 0.2s ease;
        font-size: 0.68rem;
    }
    .toggle-siswa-btn.active .toggle-icon {
        transform: rotate(180deg);
        color: #ffffff !important;
    }
    /* Styling Child Row Container */
    .child-row-wrapper {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 14px 18px;
        margin: 4px 0;
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.02);
    }
    .child-row-wrapper .table {
        border-radius: 6px;
        overflow: hidden;
    }
</style>

<div class="card shadow-lg mb-4">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0"><i class="fas fa-envelope me-2"></i> Data Surat PKL</h5>
    </div>
    <div class="card-body container-form">

        <div class="mb-4">
        </div>

        <div class="table-responsive">
            <table id="siswaTable" class="table table-hover align-middle" style="width:100%">
                <thead>
                    <tr>
                        <th style="width: 50px;">No.</th>
                        <th>No. Surat</th>
                        <th>Perihal</th>
                        <th>Tempat PKL</th>
                        <th style="width: 150px;">Tanggal</th>
                        <th style="width: 150px;">Status Balasan</th>
                        <th style="width: 260px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    foreach ($data_surat as $surat): 
                        $list_siswa = $siswa_by_surat[$surat['id_surat']] ?? [];
                        
                        // Kumpulkan kata kunci pencarian (No. Surat, Nama Siswa, NIS, Kelas)
                        $search_keywords = [$surat['no_surat']];
                        foreach ($list_siswa as $sw) {
                            $search_keywords[] = $sw['nama_siswa'];
                            $search_keywords[] = $sw['nis'];
                            $search_keywords[] = $sw['kelas'];
                        }
                        $search_string = implode(' ', $search_keywords);
                    ?>
                        <tr data-siswa="<?php echo htmlspecialchars(json_encode($list_siswa), ENT_QUOTES, 'UTF-8'); ?>"
                            data-no-surat="<?php echo htmlspecialchars($surat['no_surat'], ENT_QUOTES, 'UTF-8'); ?>">
                            <td class="text-center"><?php echo $no++; ?></td>
                            <td data-search="<?php echo htmlspecialchars($search_string); ?>" data-filter="<?php echo htmlspecialchars($search_string); ?>">
                                <div class="d-flex flex-column align-items-start">
                                    <strong><?php echo htmlspecialchars($surat['no_surat']); ?></strong>
                                    <?php if (!empty($list_siswa)): ?>
                                        <button type="button" class="btn btn-sm toggle-siswa-btn mt-1" 
                                                title="Klik untuk melihat / menyembunyikan siswa terdaftar">
                                            <i class="fas fa-user-graduate text-primary"></i>
                                            <span class="fw-semibold"><?php echo count($list_siswa); ?> Siswa</span>
                                            <i class="fas fa-chevron-down toggle-icon text-muted"></i>
                                        </button>
                                    <?php else: ?>
                                        <span class="badge bg-light text-muted border mt-1" style="font-size: 0.72rem; font-weight: normal;">
                                            <i class="fas fa-user-slash me-1"></i> 0 Siswa
                                        </span>
                                    <?php endif; ?>
                                    <!-- Elemen tersembunyi agar pencarian nama siswa 100% terbaca DataTables -->
                                    <span class="visually-hidden"><?php echo htmlspecialchars($search_string); ?></span>
                                </div>
                            </td>
                            <td>
                                <?php
                                if ($surat['perihal'] == 'Pengajuan Tempat Praktik Kerja Lapangan (PKL)') {
                                    echo '<span class="badge bg-primary">Pengajuan</span>';
                                } else if ($surat['perihal'] == 'Penambahan Siswa Praktik Kerja Lapangan (PKL)') {
                                    echo '<span class="badge bg-warning">Penambahan</span>';
                                } else if ($surat['perihal'] == 'Pemberitahuan Pembatalan Siswa Praktik Kerja Lapangan (PKL)') {
                                    echo '<span class="badge bg-danger">Pembatalan</span>';
                                }
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($surat['nama_tempat']); ?></td>
                            <td><?php echo date('d-m-Y', strtotime($surat['tanggal'])); ?></td>
                            <td>
                                <?php
                                if ($surat['status_balasan'] == 'Sudah Dibalas') {
                                    echo '<span class="badge bg-success">Sudah Dibalas</span>';
                                } else {
                                    echo '<span class="badge bg-secondary">Belum Dibalas</span>';
                                } ?>
                            </td>
                            <td>
                                <div class="btn-action-group" aria-label="Aksi Surat">
                                    <a href="cetak_surat.php?id=<?php echo $surat['id_surat']; ?>" target="_blank"
                                        class="btn btn-sm btn-info text-white" title="Cetak / Buka Surat">
                                        <i class="fas fa-print me-1"></i> Cetak
                                    </a>
                                    <?php if ($surat['perihal'] != 'Pemberitahuan Pembatalan Siswa Praktik Kerja Lapangan (PKL)'): ?>
                                        <button class="btn btn-sm btn-danger batal-btn"
                                            data-id="<?php echo $surat['id_surat']; ?>" title="Ajukan Pembatalan">
                                            Ajukan Pembatalan
                                        </button>
                                    <?php endif; ?>
                                    <?php if ($surat['perihal'] != 'Pemberitahuan Pembatalan Siswa Praktik Kerja Lapangan (PKL)'): ?>
                                        <button class="btn btn-sm btn-warning edit-btn"
                                            data-id="<?php echo $surat['id_surat']; ?>" title="Input Balasan">
                                            Input Balasan
                                        </button>
                                    <?php endif; ?>
                                    <button class="btn btn-sm btn-danger delete-btn"
                                        data-id="<?php echo $surat['id_surat']; ?>"
                                        data-no-surat="<?php echo htmlspecialchars($surat['no_surat']); ?>" title="Hapus">
                                        <i class="fas fa-trash"></i> Hapus
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
        // Fungsi untuk merender konten Sub-Baris (Child Row) Data Siswa
        function formatChildRow(siswaList, noSurat) {
            if (!siswaList || siswaList.length === 0) {
                return '<div class="child-row-wrapper"><div class="text-muted"><i class="fas fa-info-circle me-1"></i> Tidak ada data siswa yang terhubung dengan surat ini.</div></div>';
            }

            var html = '<div class="child-row-wrapper">';
            html += '<div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom">';
            html += '  <div class="fw-bold text-dark"><i class="fas fa-user-graduate text-primary me-2"></i>Daftar Siswa Terdaftar — No. Surat: <span class="text-primary">' + $('<div>').text(noSurat).html() + '</span></div>';
            html += '  <span class="badge bg-primary rounded-pill">' + siswaList.length + ' Siswa</span>';
            html += '</div>';

            html += '<div class="table-responsive">';
            html += '<table class="table table-sm table-bordered bg-white mb-0 shadow-sm align-middle">';
            html += '  <thead class="table-light">';
            html += '    <tr>';
            html += '      <th style="width: 45px;" class="text-center">#</th>';
            html += '      <th style="width: 140px;">NIS</th>';
            html += '      <th>Nama Siswa</th>';
            html += '      <th style="width: 140px;">Kelas</th>';
            html += '      <th style="width: 150px;">Kontak</th>';
            html += '      <th style="width: 140px;" class="text-center">Status Siswa</th>';
            html += '    </tr>';
            html += '  </thead>';
            html += '  <tbody>';

            $.each(siswaList, function (index, s) {
                var statusBadge = '';
                var status = (s.status || '').toLowerCase();
                if (status === 'diterima') {
                    statusBadge = '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Diterima</span>';
                } else if (status === 'ditolak') {
                    statusBadge = '<span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>Ditolak</span>';
                } else if (status === 'pending') {
                    statusBadge = '<span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Pending</span>';
                } else {
                    statusBadge = '<span class="badge bg-secondary">-</span>';
                }

                var catatanHtml = '';
                if (s.catatan && s.catatan.trim() !== '') {
                    catatanHtml = '<small class="text-muted d-block mt-1"><i class="fas fa-comment-dots me-1"></i>' + $('<div>').text(s.catatan).html() + '</small>';
                }

                html += '    <tr>';
                html += '      <td class="text-center text-muted fw-bold">' + (index + 1) + '</td>';
                html += '      <td><code class="text-dark">' + $('<div>').text(s.nis || '-').html() + '</code></td>';
                html += '      <td><strong>' + $('<div>').text(s.nama_siswa || '-').html() + '</strong>' + catatanHtml + '</td>';
                html += '      <td>' + $('<div>').text(s.kelas || '-').html() + '</td>';
                html += '      <td>' + (s.kontak_siswa ? $('<div>').text(s.kontak_siswa).html() : '<span class="text-muted">-</span>') + '</td>';
                html += '      <td class="text-center">' + statusBadge + '</td>';
                html += '    </tr>';
            });

            html += '  </tbody>';
            html += '</table>';
            html += '</div>';
            html += '</div>';

            return html;
        }

        // Inisialisasi DataTables dengan konfigurasi clean
        var table = $('#siswaTable').DataTable({
            // Urutan default: Berdasarkan data yang sudah diurutkan dari query (DESC)
            "order": [],
            // Definisi kolom
            "columnDefs": [{
                "orderable": false,
                "searchable": false,
                "targets": [0, 6] // Kolom No dan Aksi non-orderable / non-searchable
            },
            {
                "className": "text-center",
                "targets": [0, 4, 5, 6]
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
                    columns: [0, 1, 2, 3, 4, 5],
                    format: {
                        body: function (data, row, column, node) {
                            if (column === 1) {
                                // Ekstrak hanya No. Surat saat diekspor ke Excel
                                var clone = $('<div>').html(data);
                                clone.find('button, .badge, .visually-hidden').remove();
                                return clone.text().trim();
                            }
                            return data;
                        }
                    }
                },
                title: 'Data Surat PKL SMK Informatika Sumedang',
                filename: 'Data_Surat_PKL_' + new Date().toISOString().slice(0, 10)
            }],
            // Paging dan tampilan
            "pageLength": 10,
            "lengthMenu": [
                [10, 25, 50, -1],
                [10, 25, 50, "Semua"]
            ],
            "responsive": true
        });

        // Event handler tombol Expand/Collapse Siswa (Child Row)
        $('#siswaTable tbody').on('click', '.toggle-siswa-btn', function (e) {
            e.stopPropagation();
            var $btn = $(this);
            var $tr = $btn.closest('tr');
            var row = table.row($tr);

            if (row.child.isShown()) {
                // Sembunyikan child row
                row.child.hide();
                $tr.removeClass('shown');
                $btn.removeClass('active');
            } else {
                // Tampilkan child row
                var siswaData = $tr.data('siswa');
                var noSurat = $tr.data('no-surat');
                row.child(formatChildRow(siswaData, noSurat)).show();
                $tr.addClass('shown');
                $btn.addClass('active');
            }
        });

        // Event handler tombol Edit (Input Balasan)
        $('#siswaTable tbody').on('click', '.edit-btn', function () {
            var id = $(this).data('id');
            loadContent('proses_balasan_surat.php?id=' + id);
        });

        // Event handler tombol Ajukan Pembatalan
        $('#siswaTable tbody').on('click', '.batal-btn', function () {
            var id = $(this).data('id');
            loadContent('buat_surat_pembatalan.php?id_surat_ref=' + id);
        });

        // Event handler untuk tombol Hapus (AJAX)
        $('#siswaTable tbody').on('click', '.delete-btn', function () {
            var id_surat = $(this).data('id');
            var no_surat = $(this).data('no-surat');
            var $row = $(this).closest('tr');

            if (confirm('Anda yakin ingin menghapus Surat:\n' + no_surat + '\n\nCatatan: Surat dan relasi pengajuannya akan dihapus dari sistem. Status siswa yang terdaftar pada surat ini akan otomatis bebas kembali sehingga dapat diajukan untuk surat baru.')) {
                $.ajax({
                    url: 'ajax/hapus_surat.php',
                    type: 'POST',
                    data: {
                        id_surat: id_surat
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            table.row($row).remove().draw(false);
                            alert(response.message);
                        } else {
                            alert('Gagal menghapus surat: ' + response.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("AJAX Error:", status, error);
                        alert('Terjadi kesalahan saat menghubungi server: ' + status);
                    }
                });
            }
        });
    });
</script>