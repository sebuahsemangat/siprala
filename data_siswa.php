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

        <div class="mb-4">
            <a href="#" id="tambahSiswaBtn" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> Tambah Siswa Baru
            </a>
        </div>

        <div class="table-responsive">
            <table id="siswaTable" class="table table-hover align-middle" style="width:100%">
                <thead>
                    <tr>
                        <th style="width: 50px;">No.</th>
                        <th style="width: 150px;">NIS</th>
                        <th>Nama Siswa</th>
                        <th style="width: 100px;">Kelas</th>
                        <th style="width: 150px;">Kontak Siswa</th>
                        <th style="width: 250px;">Status Penempatan PKL</th> <th style="width: 150px;" class="text-center">Aksi</th>
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
    });
</script>