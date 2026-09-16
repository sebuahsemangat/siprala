<?php
// login.php - Halaman Login SIPRALA
session_start();

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';
if (isset($_SESSION['login_error'])) {
    $error = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
}
$success = '';
if (isset($_SESSION['logout_success'])) {
    $success = $_SESSION['logout_success'];
    unset($_SESSION['logout_success']);
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIPRALA SMK Informatika Sumedang</title>
    <meta name="description"
        content="Halaman login Sistem Informasi Praktik Kerja Lapangan (SIPRALA) SMK Informatika Sumedang.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="style/style.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-color: #f4f6f9;
        }

        .login-card {
            border: 1px solid #e3e6f0;
            border-radius: 12px;
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.08);
            background-color: #ffffff;
        }

        .login-header-icon {
            width: 60px;
            height: 60px;
            background-color: #007bff;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(0, 123, 255, 0.3);
        }

        .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.2);
        }

        #passwordToggle {
            border-color: #dee2e6;
            background-color: #ffffff;
        }

        #passwordToggle:hover {
            background-color: #f8f9fa;
            color: #495057 !important;
        }

        .input-group:focus-within .form-control,
        .input-group:focus-within .input-group-text,
        .input-group:focus-within #passwordToggle {
            border-color: #80bdff;
        }
    </style>
</head>

<body>

    <!-- Konten utama rata tengah (Vertikal & Horizontal) -->
    <main class="flex-grow-1 d-flex align-items-center justify-content-center p-3 my-4">
        <div class="w-100" style="max-width: 420px;">

            <!-- Logo & Judul Header -->
            <div class="text-center mb-4">
                <div class="login-header-icon mb-3">
                    <i class="fas fa-graduation-cap text-white fs-3"></i>
                </div>
                <h2 class="h4 fw-bold text-dark mb-1">SIPRALA</h2>
                <p class="text-muted small mb-0">SMK Informatika Sumedang</p>
            </div>

            <!-- Alert Error -->
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm"
                    role="alert" style="border-radius:8px; border-left:4px solid #dc3545;">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div class="small"><?= htmlspecialchars($error) ?></div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Alert Success (setelah logout) -->
            <?php if (!empty($success)): ?>
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm"
                    role="alert" style="border-radius:8px; border-left:4px solid #198754;">
                    <i class="fas fa-check-circle"></i>
                    <div class="small"><?= htmlspecialchars($success) ?></div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Card Form Login -->
            <div class="card login-card">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <span
                            class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 mb-2 fw-semibold"
                            style="font-size: 0.75rem; letter-spacing: 0.5px;">
                            LOGIN ADMIN
                        </span>
                        <p class="text-muted small mb-0">Silakan masukkan username dan password Anda</p>
                    </div>

                    <form action="proses_login.php" method="POST" id="loginForm" novalidate>

                        <!-- Username -->
                        <div class="mb-3">
                            <label for="username" class="form-label fw-semibold text-dark small">
                                <i class="fas fa-user me-1 text-primary"></i> Username
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted">
                                    <i class="fas fa-at"></i>
                                </span>
                                <input type="text" id="username" name="username"
                                    class="form-control border-start-0 ps-0" placeholder="Masukkan username"
                                    value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" autocomplete="username"
                                    required>
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="mb-4">
                            <label for="password" class="form-label fw-semibold text-dark small">
                                <i class="fas fa-lock me-1 text-primary"></i> Password
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted">
                                    <i class="fas fa-key"></i>
                                </span>
                                <input type="password" id="password" name="password"
                                    class="form-control border-start-0 border-end-0 ps-0"
                                    placeholder="Masukkan password" autocomplete="current-password" required>
                                <button class="btn border border-start-0 text-muted" type="button"
                                    id="passwordToggle" title="Tampilkan/sembunyikan password">
                                    <i class="fas fa-eye" id="eyeIcon"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Tombol Submit -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary py-2 fw-semibold" id="btnLogin">
                                <span class="spinner-border spinner-border-sm me-2 d-none" id="btnSpinner" role="status"
                                    aria-hidden="true"></span>
                                <i class="fas fa-sign-in-alt me-2" id="btnIcon"></i>
                                <span id="btnText">Masuk ke Sistem</span>
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </main>

    <!-- Footer bawah — sama persis dengan index.php -->
    <footer class="footer mt-auto py-3 bg-dark">
        <div class="container-fluid text-center">
            <span class="text-white-50">&copy; <?= date('Y') ?> SIPRALA SMK Informatika Sumedang. All rights
                reserved.</span>
            <p class="text-white-50 mb-0 small">Developed by <a href="http://instagram.com/sebuahsemangat"
                    class="text-white-50">Apep Wahyudin</a></p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle tampil/sembunyikan password
        const passwordToggle = document.getElementById('passwordToggle');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        passwordToggle.addEventListener('click', function () {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.className = 'fas fa-eye-slash';
                this.title = 'Sembunyikan password';
            } else {
                passwordInput.type = 'password';
                eyeIcon.className = 'fas fa-eye';
                this.title = 'Tampilkan password';
            }
        });

        // Loading state saat submit
        const loginForm = document.getElementById('loginForm');
        const btnLogin = document.getElementById('btnLogin');
        const btnSpinner = document.getElementById('btnSpinner');
        const btnText = document.getElementById('btnText');
        const btnIcon = document.getElementById('btnIcon');

        loginForm.addEventListener('submit', function (e) {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();
            if (!username || !password) {
                e.preventDefault();
                return;
            }
            btnLogin.disabled = true;
            btnSpinner.classList.remove('d-none');
            btnIcon.classList.add('d-none');
            btnText.textContent = 'Memverifikasi...';
        });

        // Focus otomatis ke field username
        document.getElementById('username').focus();
    </script>
</body>

</html>