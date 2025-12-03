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
$koneksi->close();
?>

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
                        <th style="width: 170px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($data_surat as $surat): ?>
                        <tr>
                            <td class="text-center"><?php echo $no++; ?></td>
                            <td><strong><?php echo htmlspecialchars($surat['no_surat']); ?></strong></td>
                            <td><?php $perihal = htmlspecialchars($surat['perihal']);
                            $singkat_perihal = explode(" ", $perihal);
                            echo htmlspecialchars($singkat_perihal[0]);
                            ?></td>
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
                                    <button class="btn btn-sm btn-warning edit-btn"
                                        data-id="<?php echo $surat['id_surat']; ?>" title="Input Balasan">
                                        Input Balasan
                                    </button>
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
        // Inisialisasi DataTables dengan konfigurasi clean
        var table = $('#siswaTable').DataTable({
            // Urutan default: Berdasarkan data yang sudah diurutkan dari query (DESC)
            "order": [], // Kosongkan agar mengikuti urutan dari database
            // Definisi kolom
            "columnDefs": [{
                "orderable": false,
                "searchable": false,
                "targets": [0, 4, 5] // Kolom 'Aksi' di index 5 juga dibuat non-sortable/searchable
            },
            {
                "className": "text-center",
                "targets": [0, 4, 5]
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
                    columns: [0, 1, 2, 3] // Export semua kecuali kolom Status Balasan dan Aksi (index 4 dan 5)
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


        // Event handler tombol Edit (Input Balasan)
        $('#siswaTable tbody').on('click', '.edit-btn', function () {
            var id = $(this).data('id');
            // Arahkan ke halaman proses balasan surat
            loadContent('proses_balasan_surat.php?id=' + id);
        });

        // Event handler untuk tombol Hapus (AJAX)
        $('#siswaTable tbody').on('click', '.delete-btn', function () {
            var id_surat = $(this).data('id');
            var no_surat = $(this).data('no-surat');
            var $row = $(this).closest('tr'); // Ambil baris tabel untuk dihapus

            if (confirm('Anda yakin ingin menghapus Surat:\n' + no_surat + '\n\nPerhatian! Tindakan ini juga akan menghapus semua data siswa yang terkait dengan surat ini.')) {
                // Kirim permintaan AJAX ke skrip penghapusan
                $.ajax({
                    url: 'ajax/hapus_surat.php', // Buat file ini (Lihat bagian di bawah)
                    type: 'POST',
                    data: {
                        id_surat: id_surat
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            // Hapus baris dari DataTables tanpa me-reload halaman
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