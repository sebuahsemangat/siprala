<?php
session_start();

// Buat captcha sederhana jika belum ada
if (empty($_SESSION['captcha'])) {
    $captcha_num = rand(1000, 9999);
    $_SESSION['captcha'] = $captcha_num;
} else {
    $captcha_num = $_SESSION['captcha'];
}

// Ambil pesan error jika ada
$error_message = '';
if (isset($_SESSION['login_error'])) {
    $error_message = $_SESSION['login_error'];
    unset($_SESSION['login_error']); // Hapus pesan setelah ditampilkan
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Pembimbing PKL - SMK Informatika Sumedang</title>
    <link rel="shortcut icon" href="../img/logo_ifsu.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        html, body {
            height: 100%;
            background-color: #f5f7fa;
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .main-wrapper {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 450px;
        }

        .logo-container {
            margin-bottom: 20px;
            text-align: center;
        }

        .logo-img {
            max-width: 80px;
            height: auto;
        }

        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            margin-bottom: 0;
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
            color: white;
        }

        .card-body {
            padding: 24px;
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
        
        /* Captcha Box */
        .captcha-box {
            background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
            border: 2px solid #e2e8f0;
            text-align: center;
            padding: 12px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 24px;
            letter-spacing: 8px;
            color: #4a5568;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.06);
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
        @media (max-width: 576px) {
            .main-wrapper {
                padding: 15px;
            }
            
            .card-header h4 {
                font-size: 18px;
            }
            
            .card-body {
                padding: 20px;
            }
            
            .captcha-box {
                font-size: 20px;
                letter-spacing: 5px;
            }
        }
    </style>
    
</head>

<body>
    <div class="main-wrapper">
        <div class="login-container">
            <div class="card">
                <div class="card-header">
                    <h4>
                        <i class="fas fa-user-tie"></i>
                        Login Pembimbing PKL
                    </h4>
                </div>
                <div class="card-body">
                    <div class="logo-container">
                        <img src="../img/logo_ifsu.png" alt="Logo SMK Informatika Sumedang" class="logo-img">
                    </div>
                    
                    <?php if ($error_message): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($error_message) ?>
                        </div>
                    <?php endif; ?>

                    <form action="proses_login.php" method="POST">

                        <div class="mb-3">
                            <label for="username" class="form-label">
                                <i class="fas fa-user me-2"></i>Username
                            </label>
                            <input type="text" class="form-control" id="username" name="username"
                                placeholder="Masukkan Username Anda" required autofocus>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-2"></i>Password
                            </label>
                            <input type="password" class="form-control" id="password" name="password"
                                placeholder="Masukkan Password Anda" required>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Gunakan password yang telah Anda atur
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="captcha_input" class="form-label">
                                <i class="fas fa-shield-alt me-2"></i>Verifikasi Keamanan
                            </label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="captcha-box">
                                        <?= $captcha_num ?>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <input type="text" class="form-control h-100" id="captcha_input" name="captcha_input"
                                        placeholder="Ketik angka" required maxlength="4" inputmode="numeric">
                                </div>
                            </div>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Masukkan 4 angka di atas untuk verifikasi
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mt-3">
                            <i class="fas fa-sign-in-alt me-2"></i>Login Sekarang
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <i class="fas fa-copyright me-1"></i> <?= date('Y') ?> SMK Informatika Sumedang. All rights reserved.
            <p class="mb-0">Developed by Tefa Ifsu</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>