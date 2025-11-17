<?php
// data_surat.php - Konten Data Surat PKL (Client-Side Datatables)

include 'koneksi.php'; // 1. Include koneksi database

// 2. Ambil Semua Data Surat dengan JOIN ke tabel tempat_pkl
$query_surat = "SELECT s.id_surat, s.no_surat, s.tanggal, t.nama_tempat, s.id_tempat_pkl
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
                        <th>Tempat PKL</th>
                        <th style="width: 150px;">Tanggal</th>
                        <th style="width: 120px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($data_surat as $surat): ?>
                    <tr>
                        <td class="text-center"><?php echo $no++; ?></td>
                        <td><strong><?php echo htmlspecialchars($surat['no_surat']); ?></strong></td>
                        <td><?php echo htmlspecialchars($surat['nama_tempat']); ?></td>
                        <td><?php echo date('d-m-Y', strtotime($surat['tanggal'])); ?></td>
                        <td>
                            <div class="btn-action-group">
                                <button class="btn btn-sm btn-warning edit-btn" data-id="<?php echo $surat['id_surat']; ?>" title="Edit">
                                    <i class="fa-solid fa-magnifying-glass"></i>
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
    // Inisialisasi DataTables dengan konfigurasi clean
    $('#siswaTable').DataTable({
        // Urutan default: Berdasarkan data yang sudah diurutkan dari query (DESC)
        "order": [], // Kosongkan agar mengikuti urutan dari database
        // Definisi kolom
        "columnDefs": [
            { "orderable": false, "searchable": false, "targets": [0, 4] },
            { "className": "text-center", "targets": [0, 4] }
        ],
        // Bahasa Indonesia
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
        },
        // Konfigurasi Buttons (Export Excel)
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>B<"row"<"col-sm-12"tr>><"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel me-2"></i> Export ke Excel',
                titleAttr: 'Export Data ke Excel',
                className: 'btn btn-success',
                exportOptions: {
                    columns: [0, 1, 2, 3] // Export semua kecuali kolom Aksi
                },
                title: 'Data Surat PKL SMK Informatika Sumedang',
                filename: 'Data_Surat_PKL_' + new Date().toISOString().slice(0,10)
            }
        ],
        // Paging dan tampilan
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
        "responsive": true
    });

      
    // Event handler untuk tombol Edit
    $('#siswaTable tbody').on('click', '.edit-btn', function() {
        var id = $(this).data('id');
        alert('Aksi Edit Surat ID: ' + id + ' (Akan diarahkan ke halaman edit).');
    });

    // Event handler untuk tombol Hapus
    $('#siswaTable tbody').on('click', '.delete-btn', function() {
        var id = $(this).data('id');
        if (confirm('Anda yakin ingin menghapus Surat ID: ' + id + '?')) {
            alert('Aksi Hapus Surat ID: ' + id + ' (Akan memproses penghapusan).');
        }
    });
});
</script>