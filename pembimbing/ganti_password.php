<?php
session_start();
// Pastikan file koneksi.php sudah tersedia
include '../koneksi.php'; 

// Pengamanan: Cek Login
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: index.php');
    exit();
}

// Cek Status Password: Jika sudah diubah (1), alihkan ke absen.php
if (isset($_SESSION['password_status']) && $_SESSION['password_status'] == 1) {
    header('Location: dashboard.php');
    exit();
}

// Ambil data siswa dari session
$nama_pembimbing = $_SESSION['nama_pembimbing'] ?? 'Pembimbing';

// Ambil pesan status/error
$status_message = '';
$status_type = '';
if (isset($_SESSION['status_message'])) {
    $status_message = $_SESSION['status_message'];
    $status_type = $_SESSION['status_type'];
    unset($_SESSION['status_message']);
    unset($_SESSION['status_type']);
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ganti Password - Siswa PKL</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        html, body {
            height: 100%;
        }
        body {
            display: flex;
            flex-direction: column;
            background-color: #f8f9fa;
        }
        /* Navbar Modern */
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            padding: 1rem 0;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            letter-spacing: -0.5px;
            color: white !important;
        }

        .main-content {
            flex-grow: 1;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .form-label {
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .form-control, .form-select {
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            outline: none;
        }
        
        .form-text {
            color: #718096;
            font-size: 13px;
            margin-top: 6px;
        }
        
        /* Button Styling */
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 14px 24px;
            font-weight: 600;
            letter-spacing: 0.3px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }
        
        /* Alert Styling */
        .alert {
            border-radius: 10px;
            border: none;
            padding: 14px 18px;
            font-size: 14px;
        }
        
        .alert-danger {
            background-color: #fee2e2;
            color: #991b1b;
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
        
        /* Card Informasi Siswa */
        .info-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            padding: 20px;
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            margin-bottom: 24px;
        }
        
        .info-card .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 15px;
            border-bottom: 2px solid rgba(255, 255, 255, 0.3);
            padding-bottom: 10px;
        }
        
        .info-item {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            padding: 12px 16px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            margin-bottom: 10px;
            gap: 16px;
        }
        
        .info-item:last-child {
            margin-bottom: 0;
        }
        
        .info-label {
            font-size: 0.85rem;
            opacity: 0.85;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 500;
        }
        
        .info-value {
            font-size: 1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .info-value a {
            color: white;
            text-decoration: none;
            transition: opacity 0.3s;
        }
        
        .info-value a:hover {
            opacity: 0.8;
        }
        
        .info-icon {
            opacity: 0.9;
            font-size: 1.1rem;
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 20px 24px;
        }

        .card-header h4 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            color: white;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid container">
            <a class="navbar-brand" href="#">Absensi PKL</a>
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
        
        <div class="card shadow mb-4 mx-auto" style="max-width: 500px;">
            <div class="card-header bg-danger text-white">
                <h4>⚠️ Perhatian: Ganti Password Wajib!</h4>
            </div>
            <div class="card-body">
                <p class="alert alert-danger">
                    Anda menggunakan <strong>default</strong>. Demi keamanan akun, Anda wajib mengubah password Anda sekarang.
                </p>

                <?php if ($status_message): // Tampilkan Alert Status ?>
                    <div class="alert alert-<?= htmlspecialchars($status_type) ?> alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($status_message) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form action="proses_ganti_password.php" method="POST">
                    
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Password Baru</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" 
                               placeholder="Masukkan password baru" required minlength="6">
                        <div class="form-text">Minimal 6 karakter.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                               placeholder="Ulangi password baru" required minlength="6">
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 mt-3">
                        <i class="fas fa-save"></i> Simpan Password Baru
                    </button>
                </form>
            </div>
        </div>

    </div>

    <footer class="footer">
        <div class="container">
            <i class="fas fa-copyright me-1"></i> <?= date('Y') ?> SMK Informatika Sumedang. All rights reserved.
            <p>Developed by Tefa Ifsu</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

</body>
</html>