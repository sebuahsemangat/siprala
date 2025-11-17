<?php
// data_siswa.php - Konten Data Siswa (Client-Side Datatables)

include 'koneksi.php'; // 1. Include koneksi database

// 2. Ambil Semua Data Siswa
$query_siswa = "SELECT id_siswa, nis, nama_siswa, kelas, kontak_siswa FROM siswa ORDER BY kelas ASC, nama_siswa ASC";
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
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th style="width: 100px;">Kelas</th>
                        <th>Kontak Siswa</th>
                        <th style="width: 120px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($data_siswa as $siswa): ?>
                        <tr>
                            <td class="text-center"><?php echo $no++; ?></td>
                            <td><strong><?php echo htmlspecialchars($siswa['nis']); ?></strong></td>
                            <td><?php echo htmlspecialchars($siswa['nama_siswa']); ?></td>
                            <td><span class="badge-kelas"><?php echo htmlspecialchars($siswa['kelas']); ?></span></td>
                            <td><?php echo htmlspecialchars($siswa['kontak_siswa']); ?></td>
                            <td>
                                <div class="btn-action-group">
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
        // Inisialisasi DataTables dengan konfigurasi clean
        $('#siswaTable').DataTable({
            // Urutan default: Kolom Kelas (index 3), lalu Nama Siswa (index 2)
            "order": [
                [3, "asc"], // Kelas
                [2, "asc"] // Nama Siswa
            ],
            // Definisi kolom
            "columnDefs": [{
                    "orderable": false,
                    "searchable": false,
                    "targets": [0, 5]
                },
                {
                    "className": "text-center",
                    "targets": [0, 5]
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
                    columns: [0, 1, 2, 3, 4] // Export semua kecuali kolom Aksi
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
        });

        // Event handler untuk tombol Edit
        $('#siswaTable tbody').on('click', '.edit-btn', function() {
            var id = $(this).data('id');
            alert('Aksi Edit Siswa ID: ' + id + ' (Akan diarahkan ke halaman edit).');
        });

        // Event handler untuk tombol Hapus
        $('#siswaTable tbody').on('click', '.delete-btn', function() {
            var id = $(this).data('id');
            if (confirm('Anda yakin ingin menghapus Siswa ID: ' + id + '?')) {
                alert('Aksi Hapus Siswa ID: ' + id + ' (Akan memproses penghapusan).');
            }
        });
    });
</script>