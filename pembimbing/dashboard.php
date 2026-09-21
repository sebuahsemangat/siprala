<?php
session_start();
include '../koneksi.php';

// --- 1. Pengamanan (Cek Login & Pembimbing) ---
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    $_SESSION['login_error'] = 'Anda harus login untuk mengakses halaman ini.';
    header('Location: index.php');
    exit();
}

if (!isset($_SESSION['id_pembimbing'])) {
    die('Akses ditolak. Halaman ini hanya untuk pembimbing.');
}

$nama_pembimbing = $_SESSION['nama_pembimbing'] ?? 'Pembimbing';
$password_status = $_SESSION['password_status'] ?? '1';

if ($password_status == 0) {
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        html, body { height: 100%; }
        body { display: flex; flex-direction: column; background-color: #f8f9fa; }

        /* Navbar Modern */
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            padding: 0.8rem 0;
        }
        .navbar-brand { font-weight: 700; font-size: 1.5rem; color: white !important; }
        
        /* Active state untuk menu navbar */
        .nav-link { transition: all 0.3s ease; border-radius: 5px; padding: 8px 15px !important; }
        .nav-link:hover { background: rgba(255,255,255,0.1); }
        .nav-link.active { background: rgba(255,255,255,0.2); font-weight: 600; }

        .main-content { flex-grow: 1; margin-top: 25px; margin-bottom: 25px; }
        .footer { background-color: #343a40; color: white; padding: 15px 0; text-align: center; }

        /* Loading Spinner Container */
        #loading-overlay { display: none; text-align: center; padding: 50px; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid container">
            <a class="navbar-brand" href="#">Dashboard Pembimbing</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-2">
                    <li class="nav-item">
                        <a class="nav-link text-white active" href="#" onclick="loadPage(this, 'absensi_siswa.php')">
                            <i class="fas fa-user-clock me-1"></i> Absensi Siswa
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="#" onclick="loadPage(this, 'absensi_mingguan.php')">
                            <i class="fas fa-calendar-week me-1"></i> Absensi Mingguan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="#" onclick="loadPage(this, 'nilai_pkl.php')">
                            <i class="fas fa-file-invoice me-1"></i> Nilai PKL
                        </a>
                    </li>

                    <li class="nav-item d-none d-lg-block mx-2 text-white-50">|</li>

                    <li class="nav-item">
                        <span class="navbar-text text-white me-3">
                            Halo, <strong><?= htmlspecialchars($nama_pembimbing) ?></strong>
                        </span>
                    </li>
                    <li class="nav-item">
                        <a href="logout.php" class="btn btn-warning btn-sm px-3">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container main-content">
        <div id="loading-overlay">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2 text-muted">Memuat data...</p>
        </div>
        
        <div id="dynamic-content"></div>
    </div>

    <footer class="footer mt-auto">
        <div class="container">
            <small><i class="fas fa-copyright me-1"></i> <?= date('Y') ?> SMK Informatika Sumedang. Developed by Tefa Ifsu</small>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>

    <script>
        // Fungsi untuk memuat halaman dinamis
        function loadPage(element, url) {
            // 1. Atur state aktif pada navbar
            if(element) {
                $('.nav-link').removeClass('active');
                $(element).addClass('active');
            }

            // 2. Tampilkan loading, sembunyikan konten lama
            $('#dynamic-content').fadeOut(100, function() {
                $('#loading-overlay').show();

                // 3. Request AJAX
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $('#loading-overlay').hide();
                        $('#dynamic-content').html(response).fadeIn(300);
                    },
                    error: function(xhr, status, error) {
                        $('#loading-overlay').hide();
                        $('#dynamic-content').html(`
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i> Gagal memuat konten: ${error}
                            </div>
                        `).fadeIn();
                    }
                });
            });
        }

        $(document).ready(function() {
            // Load default page (Absensi Siswa) saat pertama kali dibuka
            loadPage(null, 'absensi_siswa.php');
        });
    </script>
</body>
</html>