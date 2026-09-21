<?php
session_start();
include '../koneksi.php'; 

// 1. Pengamanan (Cek Login)
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    $_SESSION['login_error'] = 'Anda harus login untuk mengakses halaman ini.';
    header('Location: index.php');
    exit();
}

// Cek apakah user adalah pembimbing
if (!isset($_SESSION['id_pembimbing'])) {
    die('Akses ditolak. Halaman ini hanya untuk pembimbing.');
}

// Ambil data pembimbing dari session
$id_pembimbing = $_SESSION['id_pembimbing'];
$nama_pembimbing = $_SESSION['nama_pembimbing'] ?? 'Pembimbing';
$password_status = $_SESSION['password_status'] ?? '1'; // Default aman

// 2. Validasi Password Status
if ($password_status == 0) {
    // Jika status 0, paksa ganti password
    header('Location: ganti_password.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pembimbing PKL</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
    
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">

    <style>
        html, body {
            height: 100%;
        }
        body {
            display: flex;
            flex-direction: column;
            background-color: #f8f9fa;
        }
        .main-content {
            flex-grow: 1;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .footer {
            background-color: #343a40;
            color: white;
            padding: 15px 0;
            text-align: center;
        }
        /* Style untuk tombol DataTables agar rapi */
        .dt-buttons {
            margin-bottom: 10px;
        }
        /* Loading spinner */
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
            border-width: 0.2em;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid container">
            <a class="navbar-brand" href="#">Dashboard Pembimbing</a>
            <div class="d-flex flex-column flex-sm-row align-items-sm-center">
                <span class="navbar-text me-3 text-white">
                    Halo, <strong><?= htmlspecialchars($nama_pembimbing) ?></strong>
                </span>
                <a href="logout.php" class="btn btn-warning btn-sm mt-2 mt-sm-0">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container main-content">
        
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white">
                <h4>Pilih Siswa - Tampilkan Riwayat Absensi</h4>
            </div>
            <div class="card-body">
                <form id="formPilihSiswa">
                    <div class="row align-items-end">
                        <div class="col-md-9 mb-3 mb-md-0">
                            <label for="pilihSiswa" class="form-label">Daftar Siswa Bimbingan</label>
                            <select class="form-select" id="pilihSiswa" required>
                                <option value="" selected disabled>Memuat data siswa...</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-success w-100" id="btnTampilkan">
                                <i class="fas fa-search"></i> Tampilkan Absensi
                            </button>
                        </div>
                    </div>
                </form>
                
                <div id="messageArea" class="mt-3"></div>
            </div>
        </div>

        <div class="card shadow" id="cardAbsensi" style="display:none;">
            <div class="card-header bg-info text-white">
                <h4 id="judulAbsensi">Riwayat Absensi Siswa:</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="dataAbsensi" class="table table-striped table-bordered dt-responsive nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jam Masuk</th>
                                <th>Status</th>
                                <th>Lokasi</th>
                                <th>Bukti Foto</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            &copy; <?= date('Y') ?> Dashboard Pembimbing PKL.
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>

    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

    <script>
        let absensiTable; // Variabel global untuk menyimpan instance DataTables

        // =================================================================
        // 1. LOAD DAFTAR SISWA BIMBINGAN SAAT HALAMAN DIMUAT
        // =================================================================
        function loadSiswaBimbingan() {
            $.ajax({
                url: 'get_siswa_bimbingan.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        const selectSiswa = $('#pilihSiswa');
                        selectSiswa.empty();
                        
                        if (response.data.length > 0) {
                            selectSiswa.append('<option value="" selected disabled>Pilih Siswa</option>');
                            
                            response.data.forEach(function(siswa) {
                                selectSiswa.append(
                                    `<option value="${siswa.id_siswa}">${siswa.nis} - ${siswa.nama_siswa} (${siswa.kelas})</option>`
                                );
                            });
                        } else {
                            selectSiswa.append('<option value="" disabled>Tidak ada siswa bimbingan</option>');
                            $('#btnTampilkan').prop('disabled', true);
                        }
                    } else {
                        $('#messageArea').html('<div class="alert alert-danger">' + response.message + '</div>');
                    }
                },
                error: function(xhr, status, error) {
                    $('#messageArea').html('<div class="alert alert-danger">Gagal memuat data siswa: ' + error + '</div>');
                }
            });
        }

        // =================================================================
        // 2. INISIALISASI DATATABLE DENGAN DATA ABSENSI
        // =================================================================
        function initializeDataTable(data, namaSiswa) {
            // Hancurkan instance yang sudah ada jika ada
            if ($.fn.DataTable.isDataTable('#dataAbsensi')) {
                absensiTable.destroy();
            }

            // Inisialisasi DataTables dengan data baru dan tombol export
            absensiTable = $('#dataAbsensi').DataTable({
                responsive: true,
                paging: true,
                searching: true,
                info: true,
                ordering: true,
                data: data,
                columns: [
                    { data: 'tanggal_absensi' },
                    { data: 'jam_masuk' },
                    { data: 'status' },
                    { data: 'lokasi_masuk' },
                    { data: 'foto_bukti' }
                ],
                columnDefs: [
                    // Render Status dengan badge
                    {
                        targets: 2,
                        render: function(data, type, row) {
                            if (type === 'display') {
                                let badgeClass = 'bg-success';
                                if (data === 'Sakit') badgeClass = 'bg-warning';
                                else if (data === 'Izin') badgeClass = 'bg-info';
                                return `<span class="badge ${badgeClass}">${data}</span>`;
                            }
                            return data;
                        }
                    },
                    // Render Lokasi menjadi link Google Maps
                    {
                        targets: 3,
                        render: function(data, type, row) {
                            if (type === 'display' && data && data.includes(',')) {
                                return `<a href="https://www.google.com/maps/search/?api=1&query=${data}" target="_blank" class="btn btn-sm btn-info text-white">Lihat Peta</a>`;
                            }
                            return data || '-';
                        }
                    },
                    // Render Foto Bukti menjadi tombol
                    {
                        targets: 4,
                        render: function(data, type, row) {
                            if (type === 'display' && data) {
                                return `<button type="button" class="btn btn-sm btn-secondary" onclick="window.open('${data}', '_blank')">Lihat Foto</button>`;
                            }
                            return '-';
                        }
                    }
                ],
                dom: 'Bfrtip',
                buttons: [
                    { 
                        extend: 'excelHtml5', 
                        title: 'Absensi_' + namaSiswa.replace(/\s+/g, '_'), 
                        text: '<i class="far fa-file-excel"></i> Export Excel', 
                        className: 'btn btn-success btn-sm me-2' 
                    },
                    { 
                        extend: 'pdfHtml5', 
                        title: 'Absensi_' + namaSiswa.replace(/\s+/g, '_'), 
                        text: '<i class="far fa-file-pdf"></i> Export PDF', 
                        className: 'btn btn-danger btn-sm me-2' 
                    },
                    { 
                        extend: 'print', 
                        title: 'Absensi ' + namaSiswa, 
                        text: '<i class="fas fa-print"></i> Print', 
                        className: 'btn btn-primary btn-sm' 
                    }
                ],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json"
                },
                order: [[0, 'desc']]
            });
        }

        // =================================================================
        // 3. LOAD DATA ABSENSI BERDASARKAN SISWA (SEMUA TANGGAL)
        // =================================================================
        function loadAbsensiSiswa(idSiswa, namaSiswa) {
            $.ajax({
                url: 'get_absensi_siswa.php',
                type: 'POST',
                data: {
                    id_siswa: idSiswa
                },
                dataType: 'json',
                beforeSend: function() {
                    $('#btnTampilkan').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Memuat...');
                    $('#messageArea').html('<div class="alert alert-info">Memuat data absensi...</div>');
                },
                success: function(response) {
                    if (response.success) {
                        if (response.data.length > 0) {
                            // Update judul
                            $('#judulAbsensi').html(`Riwayat Absensi: <strong>${namaSiswa}</strong>`);
                            
                            // Inisialisasi DataTable
                            initializeDataTable(response.data, namaSiswa);
                            
                            // Tampilkan card absensi
                            $('#cardAbsensi').slideDown();
                            $('#messageArea').html('<div class="alert alert-success">Data berhasil dimuat. Total: ' + response.data.length + ' record absensi.</div>');
                        } else {
                            $('#cardAbsensi').slideUp();
                            $('#messageArea').html('<div class="alert alert-warning">Siswa ini belum memiliki riwayat absensi.</div>');
                        }
                    } else {
                        $('#cardAbsensi').slideUp();
                        $('#messageArea').html('<div class="alert alert-danger">' + response.message + '</div>');
                    }
                },
                error: function(xhr, status, error) {
                    $('#cardAbsensi').slideUp();
                    $('#messageArea').html('<div class="alert alert-danger">Gagal memuat data absensi: ' + error + '</div>');
                },
                complete: function() {
                    $('#btnTampilkan').prop('disabled', false).html('<i class="fas fa-search"></i> Tampilkan Absensi');
                }
            });
        }

        // =================================================================
        // DOCUMENT READY
        // =================================================================
        $(document).ready(function() {
            // Load daftar siswa
            loadSiswaBimbingan();

            // Tangani submission form
            $('#formPilihSiswa').on('submit', function(e) {
                e.preventDefault();
                
                const idSiswa = $('#pilihSiswa').val();
                const namaSiswa = $('#pilihSiswa option:selected').text();
                
                if (!idSiswa) {
                    $('#messageArea').html('<div class="alert alert-warning">Mohon pilih siswa terlebih dahulu.</div>');
                    return;
                }
                
                // Load data absensi (semua tanggal)
                loadAbsensiSiswa(idSiswa, namaSiswa);
            });
        });
    </script>
</body>
</html>