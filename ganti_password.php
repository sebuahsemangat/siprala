<?php
session_start();
// Pastikan file koneksi.php sudah tersedia
include 'koneksi.php';

// Pengamanan: Cek Login
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: index.php');
    exit();
}

// Cek Status Password: Jika sudah diubah (1), alihkan ke absen.php
if (isset($_SESSION['password_status']) && $_SESSION['password_status'] == 1) {
    header('Location: absen.php');
    exit();
}

// Ambil data siswa dari session
$nama_siswa = $_SESSION['nama_siswa'] ?? 'Siswa';
$kelas_siswa = $_SESSION['kelas_siswa'] ?? '';

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        html,
        body {
            height: 100%;
            background-color: #f5f7fa;
        }

        body {
            display: flex;
            flex-direction: column;
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
        }

        .main-content {
            flex-grow: 1;
            margin-top: 30px;
            margin-bottom: 30px;
        }

        /* Profile Card */
        .profile-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 16px;
            padding: 24px;
            color: white;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
            margin-bottom: 24px;
            border: none;
        }

        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 0;
        }

        .profile-avatar {
            width: 70px;
            height: 70px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            margin-right: 20px;
            backdrop-filter: blur(10px);
            border: 3px solid rgba(255, 255, 255, 0.3);
        }

        .profile-info h4 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .profile-info p {
            margin: 0;
            font-size: 14px;
            opacity: 0.9;
        }

        /* Alert Card */
        .alert-card {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            border-radius: 16px;
            padding: 24px;
            color: white;
            box-shadow: 0 10px 30px rgba(245, 158, 11, 0.3);
            margin-bottom: 24px;
            border: none;
        }

        .alert-card h5 {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-card p {
            margin: 0;
            font-size: 14px;
            opacity: 0.95;
            line-height: 1.6;
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            margin-bottom: 24px;
            overflow: hidden;
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
        }

        .card-body {
            padding: 24px;
        }

        /* Form Styling */
        .form-label {
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
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

        .btn-warning {
            background: #f59e0b;
            border: none;
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-warning:hover {
            background: #d97706;
            transform: translateY(-1px);
        }

        /* Alert Styling */
        .alert {
            border-radius: 10px;
            border: none;
            padding: 14px 18px;
            font-size: 14px;
        }

        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
        }

        .alert-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .alert-warning {
            background-color: #fef3c7;
            color: #92400e;
        }

        /* Password Strength Indicator */
        .password-strength {
            height: 4px;
            border-radius: 2px;
            margin-top: 8px;
            background-color: #e2e8f0;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .password-strength-bar {
            height: 100%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }

        .strength-weak {
            background: linear-gradient(90deg, #ef4444, #dc2626);
            width: 33%;
        }

        .strength-medium {
            background: linear-gradient(90deg, #f59e0b, #d97706);
            width: 66%;
        }

        .strength-strong {
            background: linear-gradient(90deg, #10b981, #059669);
            width: 100%;
        }

        .password-strength-text {
            font-size: 12px;
            margin-top: 4px;
            font-weight: 600;
        }

        /* Footer */
        .footer {
            background: linear-gradient(135deg, #2d3748 0%, #1a202c 100%);
            color: white;
            padding: 20px 0;
            text-align: center;
            margin-top: auto;
            font-size: 14px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .profile-header {
                flex-direction: column;
                text-align: center;
            }

            .profile-avatar {
                margin-right: 0;
                margin-bottom: 15px;
            }

            .navbar-text {
                font-size: 13px;
            }
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-clipboard-check me-2"></i>Absensi PKL
            </a>
            <div class="d-flex align-items-center">
                <a href="logout.php" class="btn btn-warning btn-sm">
                    <i class="fas fa-sign-out-alt me-1"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container main-content">

        <!-- Profile Card -->
        <div class="card profile-card" style="max-width: 600px; margin: 0 auto 24px;">
            <div class="profile-header">
                <div class="profile-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="profile-info">
                    <h4><?= htmlspecialchars($nama_siswa) ?></h4>
                    <p><i class="fas fa-graduation-cap me-2"></i><?= htmlspecialchars($kelas_siswa) ?></p>
                </div>
            </div>
        </div>

        <!-- Alert Card -->
        <div class="alert-card" style="max-width: 600px; margin: 0 auto 24px;">
            <h5>
                <i class="fas fa-exclamation-triangle"></i>
                Perhatian: Ganti Password Wajib!
            </h5>
            <p>
                Anda menggunakan password <strong>default</strong>. Demi keamanan akun, Anda wajib mengubah password Anda sekarang sebelum dapat melanjutkan ke sistem absensi.
            </p>
        </div>

        <!-- Status Message -->
        <?php if ($status_message): ?>
            <div class="alert alert-<?= htmlspecialchars($status_type) ?> alert-dismissible fade show"
                style="max-width: 600px; margin: 0 auto 24px;" role="alert">
                <i class="fas fa-<?= $status_type === 'success' ? 'check-circle' : ($status_type === 'danger' ? 'exclamation-circle' : 'info-circle') ?> me-2"></i>
                <?= htmlspecialchars($status_message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Form Ganti Password -->
        <div class="card" style="max-width: 600px; margin: 0 auto;">
            <div class="card-header text-white">
                <h4>
                    <i class="fas fa-key"></i>
                    Ubah Password Anda
                </h4>
            </div>
            <div class="card-body">
                <form action="proses_ganti_password.php" method="POST" id="formGantiPassword">

                    <div class="mb-3">
                        <label for="new_password" class="form-label">
                            <i class="fas fa-lock me-2"></i>Password Baru
                        </label>
                        <input type="password" class="form-control" id="new_password" name="new_password"
                            placeholder="Masukkan password baru" required minlength="6">
                        <div class="password-strength">
                            <div class="password-strength-bar" id="strengthBar"></div>
                        </div>
                        <div class="password-strength-text" id="strengthText"></div>
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Minimal 6 karakter. Gunakan kombinasi huruf, angka, dan simbol untuk password yang kuat.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">
                            <i class="fas fa-lock-open me-2"></i>Konfirmasi Password Baru
                        </label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                            placeholder="Ulangi password baru" required minlength="6">
                        <div class="form-text" id="matchText"></div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mt-3">
                        <i class="fas fa-save me-2"></i>Simpan Password Baru
                    </button>
                </form>
            </div>
        </div>

    </div>

    <footer class="footer">
        <i class="fas fa-copyright me-1"></i> <?= date('Y') ?> SMK Informatika Sumedang. All rights reserved.
        <p>Developed by Tefa Ifsu</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {
            // Password Strength Checker
            $('#new_password').on('input', function() {
                const password = $(this).val();
                const strengthBar = $('#strengthBar');
                const strengthText = $('#strengthText');

                if (password.length === 0) {
                    strengthBar.removeClass('strength-weak strength-medium strength-strong');
                    strengthText.text('');
                    return;
                }

                let strength = 0;

                // Check password strength
                if (password.length >= 6) strength++;
                if (password.length >= 10) strength++;
                if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
                if (/\d/.test(password)) strength++;
                if (/[^a-zA-Z\d]/.test(password)) strength++;

                strengthBar.removeClass('strength-weak strength-medium strength-strong');

                if (strength <= 2) {
                    strengthBar.addClass('strength-weak');
                    strengthText.html('<span style="color: #ef4444;"><i class="fas fa-exclamation-circle me-1"></i>Password lemah</span>');
                } else if (strength <= 4) {
                    strengthBar.addClass('strength-medium');
                    strengthText.html('<span style="color: #f59e0b;"><i class="fas fa-check-circle me-1"></i>Password sedang</span>');
                } else {
                    strengthBar.addClass('strength-strong');
                    strengthText.html('<span style="color: #10b981;"><i class="fas fa-check-circle me-1"></i>Password kuat</span>');
                }
            });

            // Password Match Checker
            $('#confirm_password').on('input', function() {
                const password = $('#new_password').val();
                const confirm = $(this).val();
                const matchText = $('#matchText');

                if (confirm.length === 0) {
                    matchText.html('');
                    return;
                }

                if (password === confirm) {
                    matchText.html('<span style="color: #10b981;"><i class="fas fa-check-circle me-1"></i>Password cocok</span>');
                } else {
                    matchText.html('<span style="color: #ef4444;"><i class="fas fa-times-circle me-1"></i>Password tidak cocok</span>');
                }
            });

            // Form Validation
            $('#formGantiPassword').on('submit', function(e) {
                const password = $('#new_password').val();
                const confirm = $('#confirm_password').val();

                if (password !== confirm) {
                    e.preventDefault();
                    alert('Password dan konfirmasi password tidak cocok!');
                    return false;
                }

                if (password.length < 6) {
                    e.preventDefault();
                    alert('Password minimal 6 karakter!');
                    return false;
                }
            });
        });
    </script>

</body>

</html>