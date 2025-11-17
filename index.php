<?php
// index.php - Halaman Utama Dashboard dengan Sidebar
// Include file koneksi (diperlukan jika ada logika PHP yang berjalan di sini)
// include 'koneksi.php'; 
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard SIPRALA - SMK Informatika Sumedang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style/style.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>

    <div class="d-flex" id="wrapper">

        <div id="sidebar-wrapper">
            <div class="sidebar-heading" style="text-align: center;"><strong>SIPRALA</strong></div>
            <div class="list-group list-group-flush">
                <a href="index.php" class="list-group-item list-group-item-action py-3 active" data-content="dashboard.php">
                    <i class="fas fa-tachometer-alt fa-fw me-2"></i> Dashboard
                </a>
                <a href="#" class="list-group-item list-group-item-action py-3" data-content="buat_surat.php">
                    <i class="fas fa-file-alt fa-fw me-2"></i> Buat Surat
                </a>
                <a href="#" class="list-group-item list-group-item-action py-3" data-content="data_surat.php">
                    <i class="fas fa-clipboard-list fa-fw me-2"></i> Data Surat Keluar
                </a>
                <a href="#" class="list-group-item list-group-item-action py-3" data-content="data_siswa.php">
                    <i class="fas fa-user-graduate fa-fw me-2"></i> Data Siswa
                </a>
                <a href="#" class="list-group-item list-group-item-action py-3">
                    <i class="fas fa-building fa-fw me-2"></i> Data Tempat PKL
                </a>
                <a href="#" class="list-group-item list-group-item-action py-3" data-content="settings.php">
                    <i class="fas fa-cog fa-fw me-2"></i> Pengaturan
                </a>
            </div>
        </div>
        <div id="page-content-wrapper">

            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm">
                <div class="container-fluid">
                    <button class="btn btn-primary" id="sidebarToggle">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <span class="navbar-text ms-3 text-dark d-none d-sm-inline">
                        Sistem Informasi Praktik Kerja Lapangan
                    </span>
                    <div class="dropdown ms-auto">
                        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user"></i> Admin
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                        </ul>
                    </div>
                </div>
            </nav>

            <div class="container-fluid main-container" id="main-content-area">

                <h1 class="mt-4">Dashboard Utama</h1>
                <p class="lead">Selamat datang di Sistem Informasi PKL. Silakan pilih menu di sebelah kiri.</p>
                <div class="alert alert-info" role="alert">
                    Klik menu **Buat Surat** di Sidebar Kiri untuk melihat form yang telah kita modifikasi.
                </div>

            </div>
        </div>
    </div>
    <footer class="footer mt-auto py-3 bg-dark">
        <div class="container-fluid text-center">
            <span class="text-white-50">© <?php echo date('Y'); ?> SIPRALA SMK Informatika Sumedang. All rights reserved.</span>
            <p class="text-white-50">Developed by <a href="http://instagram.com/sebuahsemangat">Apep Wahyudin</a></p>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

    <script>
        // Toggle Sidebar
        document.getElementById("sidebarToggle").addEventListener("click", event => {
            event.preventDefault();
            document.getElementById("wrapper").classList.toggle("toggled");
        });

        // Fungsi umum untuk memuat konten
        function loadContent(contentFile, setActive = true) {
            if (!contentFile) return;

            // Tampilkan loading sebelum memuat konten
            $('#main-content-area').html('<div class="text-center mt-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2">Memuat halaman...</p></div>');

            // CLEANUP: Hapus variabel global dari halaman sebelumnya untuk mencegah konflik
            // Ini penting untuk mencegah error "Identifier already declared"
            delete window.studentCount;
            delete window.selectedStudents;

            // Muat konten menggunakan AJAX
            $.ajax({
                url: contentFile,
                method: 'GET',
                success: function(data) {
                    $('#main-content-area').html(data);

                    // Opsional: Sembunyikan sidebar di mobile setelah klik
                    if (setActive && $(window).width() < 768) {
                        document.getElementById("wrapper").classList.remove("toggled");
                    }
                },
                error: function() {
                    $('#main-content-area').html('<div class="alert alert-danger mt-3">Gagal memuat konten. Pastikan file ' + contentFile + ' tersedia.</div>');
                }
            });
        }


        $(document).ready(function() {
            // --- 1. Load Konten Default (Dashboard) ---
            // Panggil fungsi untuk memuat dashboard.php saat halaman pertama kali diakses.
            loadContent('dashboard.php', false); // false agar tidak mengganggu status toggled

            // --- 2. Event Listener untuk Sidebar ---
            $('.list-group-item').on('click', function(e) {
                e.preventDefault();

                // Hapus kelas 'active' dari semua item dan tambahkan ke item yang diklik
                $('.list-group-item').removeClass('active');
                $(this).addClass('active');

                // Ambil nama file dari data-content
                var contentFile = $(this).data('content');

                loadContent(contentFile);
            });

        });
    </script>

</body>

</html>