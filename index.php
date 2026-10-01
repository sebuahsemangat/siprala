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
    <title>Absensi Harian Siswa PKL - SMK Informatika Sumedang</title>
    <link rel="shortcut icon" href="siprala/img/logo_ifsu.ico" type="image/x-icon">
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

        .form-control,
        .form-select {
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
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
                        <i class="fas fa-sign-in-alt"></i>
                        Login Absensi PKL
                    </h4>
                </div>
                <div class="card-body">
                    <div class="logo-container">
                        <img src="siprala/img/logo_ifsu.png" alt="Logo SMK Informatika Sumedang" class="logo-img">
                    </div>

                    <?php if ($error_message): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($error_message) ?>
                        </div>
                    <?php endif; ?>

                    <form action="proses_login.php" method="POST">

                        <div class="mb-3">
                            <label for="nis" class="form-label">
                                <i class="fas fa-id-card me-2"></i>NIS (Nomor Induk Siswa)
                            </label>
                            <input type="text" class="form-control" id="nis" name="nis" placeholder="Masukkan NIS Anda"
                                required autofocus>
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
                                    <input type="text" class="form-control h-100" id="captcha_input"
                                        name="captcha_input" placeholder="Ketik angka" required maxlength="4"
                                        inputmode="numeric">
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

                    <!-- Link Reset Password -->
                    <div class="text-center mt-3 pt-2 border-top">
                        <a href="javascript:void(0)"
                            class="text-decoration-none small text-muted d-inline-flex align-items-center gap-1"
                            id="btnOpenResetModal" data-bs-toggle="modal" data-bs-target="#resetPasswordModal">
                            <i class="fas fa-key text-warning"></i> Lupa Password? <span
                                class="fw-semibold text-primary">Reset di sini</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Reset Password -->
    <div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 16px; overflow: hidden;">
                <div class="modal-header text-white"
                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h5 class="modal-title fs-6 fw-bold m-0" id="resetPasswordModalLabel">
                        <i class="fas fa-unlock-alt me-2"></i>Permintaan Reset Password
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Alert Cooldown -->
                    <div id="cooldownAlert" class="alert alert-warning py-2 px-3 mb-3 small d-none" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-hourglass-half me-2 fs-5 text-warning"></i>
                            <div>
                                <strong>Fitur Sedang Cooldown</strong><br>
                                Harap tunggu <span id="cooldownTimer" class="fw-bold text-danger">10:00</span> sebelum
                                mengirimkan permintaan baru.
                            </div>
                        </div>
                    </div>

                    <!-- Input Pencarian Siswa -->
                    <div class="mb-3">
                        <label for="inputCariNama" class="form-label">
                            <i class="fas fa-search me-1 text-primary"></i> Cari Nama Siswa
                        </label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="inputCariNama" placeholder="Ketik nama siswa..."
                                autocomplete="off">
                            <span class="input-group-text bg-white" id="searchSpinner" style="display: none;">
                                <span class="spinner-border spinner-border-sm text-primary" role="status"></span>
                            </span>
                        </div>
                        <div class="form-text">
                            Ketik minimal 2 huruf nama untuk mencari data Anda.
                        </div>

                        <!-- Daftar Hasil Pencarian -->
                        <div id="hasilPencarian" class="list-group mt-2 shadow-sm d-none"
                            style="max-height: 220px; overflow-y: auto;">
                        </div>
                    </div>

                    <!-- Card Informasi Siswa Terpilih -->
                    <div id="detailSiswaCard"
                        class="card border border-2 border-primary-subtle bg-light p-3 d-none mb-3"
                        style="border-radius: 12px;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-primary px-2 py-1">
                                <i class="fas fa-user-check me-1"></i> Siswa Terpilih
                            </span>
                            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" id="btnBatalPilih"
                                title="Ganti Siswa">
                                <i class="fas fa-times me-1"></i>Ganti
                            </button>
                        </div>
                        <div class="small">
                            <div class="row py-1 border-bottom">
                                <div class="col-4 text-muted">NIS</div>
                                <div class="col-8 fw-semibold text-dark" id="displayNis">-</div>
                            </div>
                            <div class="row py-1 border-bottom">
                                <div class="col-4 text-muted">Nama</div>
                                <div class="col-8 fw-semibold text-dark" id="displayNama">-</div>
                            </div>
                            <div class="row py-1">
                                <div class="col-4 text-muted">Kelas</div>
                                <div class="col-8 fw-semibold text-dark" id="displayKelas">-</div>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Request Reset Password -->
                    <button type="button" id="btnKirimResetWa" class="btn btn-success w-100 py-2 fw-semibold shadow-sm"
                        disabled style="border-radius: 10px;">
                        <i class="fab fa-whatsapp me-2 fs-5 align-middle"></i>Request Reset Password
                    </button>
                </div>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const COOLDOWN_KEY = 'siprala_reset_pwd_cooldown';
            const COOLDOWN_DURATION_MS = 10 * 60 * 1000; // 10 menit
            const WA_PHONE = '082214820486';
            const WA_PHONE_INTL = '6282214820486';

            const inputCariNama = document.getElementById('inputCariNama');
            const searchSpinner = document.getElementById('searchSpinner');
            const hasilPencarian = document.getElementById('hasilPencarian');
            const detailSiswaCard = document.getElementById('detailSiswaCard');
            const displayNis = document.getElementById('displayNis');
            const displayNama = document.getElementById('displayNama');
            const displayKelas = document.getElementById('displayKelas');
            const btnBatalPilih = document.getElementById('btnBatalPilih');
            const btnKirimResetWa = document.getElementById('btnKirimResetWa');
            const cooldownAlert = document.getElementById('cooldownAlert');
            const cooldownTimer = document.getElementById('cooldownTimer');
            const resetPasswordModal = document.getElementById('resetPasswordModal');

            let selectedSiswa = null;
            let searchTimeout = null;
            let cooldownInterval = null;

            // Helper escape html
            function escapeHtml(text) {
                if (!text) return '';
                const map = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                };
                return text.replace(/[&<>"']/g, function (m) { return map[m]; });
            }

            // Cek status cooldown
            function updateCooldownUI() {
                const expiryTime = parseInt(localStorage.getItem(COOLDOWN_KEY) || '0', 10);
                const now = Date.now();

                if (expiryTime > now) {
                    const remainingSeconds = Math.ceil((expiryTime - now) / 1000);
                    const minutes = Math.floor(remainingSeconds / 60);
                    const seconds = remainingSeconds % 60;
                    const formatted = String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');

                    cooldownTimer.textContent = formatted;
                    cooldownAlert.classList.remove('d-none');
                    btnKirimResetWa.disabled = true;
                    btnKirimResetWa.innerHTML = '<i class="fas fa-hourglass-half me-2"></i>Tunggu ' + formatted;

                    if (!cooldownInterval) {
                        cooldownInterval = setInterval(updateCooldownUI, 1000);
                    }
                    return true;
                } else {
                    if (cooldownInterval) {
                        clearInterval(cooldownInterval);
                        cooldownInterval = null;
                    }
                    localStorage.removeItem(COOLDOWN_KEY);
                    cooldownAlert.classList.add('d-none');
                    btnKirimResetWa.innerHTML = '<i class="fab fa-whatsapp me-2 fs-5 align-middle"></i>Request Reset Password';

                    if (selectedSiswa) {
                        btnKirimResetWa.disabled = false;
                    } else {
                        btnKirimResetWa.disabled = true;
                    }
                    return false;
                }
            }

            // Set Cooldown
            function startCooldown() {
                const expiry = Date.now() + COOLDOWN_DURATION_MS;
                localStorage.setItem(COOLDOWN_KEY, expiry.toString());
                updateCooldownUI();
            }

            // Event saat modal dibuka
            resetPasswordModal.addEventListener('shown.bs.modal', function () {
                updateCooldownUI();
                if (!updateCooldownUI() && !selectedSiswa) {
                    inputCariNama.focus();
                }
            });

            // Input pencarian nama (live search dengan debounce)
            inputCariNama.addEventListener('input', function () {
                const keyword = this.value.trim();

                clearTimeout(searchTimeout);
                if (keyword.length < 2) {
                    hasilPencarian.innerHTML = '';
                    hasilPencarian.classList.add('d-none');
                    searchSpinner.style.display = 'none';
                    return;
                }

                searchSpinner.style.display = 'inline-block';
                searchTimeout = setTimeout(function () {
                    fetch('ajax_cari_siswa.php?q=' + encodeURIComponent(keyword))
                        .then(response => {
                            if (!response.ok) throw new Error('Network error');
                            return response.json();
                        })
                        .then(data => {
                            searchSpinner.style.display = 'none';
                            hasilPencarian.innerHTML = '';

                            if (data.status === 'success' && data.data && data.data.length > 0) {
                                data.data.forEach(siswa => {
                                    const a = document.createElement('a');
                                    a.href = 'javascript:void(0)';
                                    a.className = 'list-group-item list-group-item-action py-2';
                                    a.innerHTML = `
                                        <div class="fw-semibold text-dark">${escapeHtml(siswa.nama_siswa)}</div>
                                        <div class="small text-muted">NIS: <span class="text-primary">${escapeHtml(siswa.nis)}</span> &bull; Kelas: ${escapeHtml(siswa.kelas)}</div>
                                    `;
                                    a.addEventListener('click', function () {
                                        pilihSiswa(siswa);
                                    });
                                    hasilPencarian.appendChild(a);
                                });
                                hasilPencarian.classList.remove('d-none');
                            } else {
                                hasilPencarian.innerHTML = `
                                    <div class="list-group-item text-muted small text-center py-3">
                                        <i class="fas fa-user-slash me-1"></i> Nama tidak ditemukan
                                    </div>
                                `;
                                hasilPencarian.classList.remove('d-none');
                            }
                        })
                        .catch(err => {
                            searchSpinner.style.display = 'none';
                            console.error('Pencarian gagal:', err);
                            hasilPencarian.innerHTML = `
                                <div class="list-group-item text-danger small text-center py-2">
                                    Gagal memuat data siswa.
                                </div>
                            `;
                            hasilPencarian.classList.remove('d-none');
                        });
                }, 300);
            });

            // Pilih siswa dari hasil pencarian
            function pilihSiswa(siswa) {
                selectedSiswa = siswa;
                displayNis.textContent = siswa.nis;
                displayNama.textContent = siswa.nama_siswa;
                displayKelas.textContent = siswa.kelas;

                detailSiswaCard.classList.remove('d-none');
                hasilPencarian.classList.add('d-none');
                inputCariNama.value = siswa.nama_siswa;

                if (!updateCooldownUI()) {
                    btnKirimResetWa.disabled = false;
                }
            }

            // Batal pilih siswa
            btnBatalPilih.addEventListener('click', function () {
                selectedSiswa = null;
                detailSiswaCard.classList.add('d-none');
                inputCariNama.value = '';
                btnKirimResetWa.disabled = true;
                hasilPencarian.innerHTML = '';
                hasilPencarian.classList.add('d-none');
                inputCariNama.focus();
            });

            // Tombol Request Reset Password ditekan
            btnKirimResetWa.addEventListener('click', function () {
                if (updateCooldownUI()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Mohon Tunggu',
                        text: 'Anda baru saja mengajukan reset password. Silakan coba lagi beberapa saat.',
                        confirmButtonColor: '#667eea'
                    });
                    return;
                }

                if (!selectedSiswa) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pilih Siswa',
                        text: 'Silakan cari dan pilih data siswa terlebih dahulu.',
                        confirmButtonColor: '#667eea'
                    });
                    return;
                }

                // Munculkan konfirmasi apakah data sudah benar
                Swal.fire({
                    title: 'Konfirmasi Data',
                    html: `
                        <div class="text-start">
                            <p class="mb-2 text-muted">Apakah data yang Anda pilih sudah benar?</p>
                            <div class="p-3 bg-light rounded border small">
                                <div class="mb-1"><strong>NIS:</strong> ${escapeHtml(selectedSiswa.nis)}</div>
                                <div class="mb-1"><strong>Nama:</strong> ${escapeHtml(selectedSiswa.nama_siswa)}</div>
                                <div><strong>Kelas:</strong> ${escapeHtml(selectedSiswa.kelas)}</div>
                            </div>
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#25D366',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fab fa-whatsapp me-1"></i> Ya, Sudah Benar',
                    cancelButtonText: 'Batal'
                }).then(result => {
                    if (result.isConfirmed) {
                        // Susun pesan WhatsApp sesuai permintaan
                        const pesanWa = `Permintaan Reset Password Absensi PKL.\nNIS: ${selectedSiswa.nis}\nNama: ${selectedSiswa.nama_siswa}\nKelas: ${selectedSiswa.kelas}`;
                        const urlWa = `https://wa.me/${WA_PHONE_INTL}?text=${encodeURIComponent(pesanWa)}`;

                        // Aktifkan cooldown 10 menit
                        startCooldown();

                        // Buka WhatsApp di tab baru
                        window.open(urlWa, '_blank');

                        // Tutup modal
                        const modalInstance = bootstrap.Modal.getInstance(resetPasswordModal);
                        if (modalInstance) {
                            modalInstance.hide();
                        }
                    }
                });
            });

            // Periksa cooldown saat awal halaman dimuat
            updateCooldownUI();
        });
    </script>
</body>

</html>