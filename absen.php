<?php
session_start();
include 'koneksi.php';

// 1. Pengamanan (Cek Login)
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    $_SESSION['login_error'] = 'Anda harus login untuk mengakses halaman ini.';
    header('Location: index.php');
    exit();
}

// Ambil data siswa dari session
$id_siswa = $_SESSION['id_siswa'] ?? 0;
$nama_siswa = $_SESSION['nama_siswa'] ?? 'Siswa';
$kelas_siswa = $_SESSION['kelas_siswa'] ?? '';
$password_status = $_SESSION['password_status'] ?? '1';

// 2. Validasi Password Status
if ($password_status == 0) {
    header('Location: ganti_password.php');
    exit();
}

// 3. Ambil data lengkap siswa termasuk tempat PKL dan pembimbing
try {
    $stmt = $pdo->prepare("
        SELECT 
            s.nama_siswa,
            s.kelas,
            s.kontak_siswa,
            tp.nama_tempat,
            p.nama_pembimbing,
            p.kontak_pembimbing
        FROM 
            siswa s
        LEFT JOIN 
            tempat_pkl tp ON s.id_tempat = tp.id_tempat
        LEFT JOIN 
            pembimbing p ON s.id_pembimbing = p.id_pembimbing
        WHERE 
            s.id_siswa = ?
    ");
    $stmt->execute([$id_siswa]);
    $data_siswa = $stmt->fetch(PDO::FETCH_ASSOC);

    // Set default jika data tidak ditemukan
    $nama_tempat = $data_siswa['nama_tempat'] ?? 'Belum ditentukan';
    $nama_pembimbing = $data_siswa['nama_pembimbing'] ?? 'Belum ditentukan';
    $kontak_pembimbing = $data_siswa['kontak_pembimbing'] ?? '-';
    $kontak_siswa = $data_siswa['kontak_siswa'] ?? '-';
} catch (PDOException $e) {
    error_log("Error mengambil data siswa: " . $e->getMessage());
    $nama_tempat = 'Error';
    $nama_pembimbing = 'Error';
    $kontak_pembimbing = '-';
    $kontak_siswa = '-';
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi Harian Siswa PKL - SMK Informatika Sumedang</title>
    <link rel="shortcut icon" href="img/logo_ifsu.ico" type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
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
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
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

        .profile-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
        }

        .profile-item {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            padding: 14px 16px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .profile-item-label {
            font-size: 12px;
            opacity: 0.85;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .profile-item-value {
            font-size: 15px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .profile-item-value i {
            opacity: 0.8;
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

        .form-control[readonly] {
            background-color: #f7fafc;
            cursor: not-allowed;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }

        /* Keterangan Field - Hidden by default */
        #keteranganWrapper {
            display: none;
            animation: slideDown 0.3s ease-out;
        }

        #keteranganWrapper.show {
            display: block;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
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

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
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

        /* Status Indicators */
        .status-indicator {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }

        .status-success {
            background: #d1fae5;
            color: #065f46;
        }

        .status-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .status-info {
            background: #dbeafe;
            color: #1e40af;
        }

        /* DataTables Styling */
        .table {
            font-size: 14px;
        }

        .table thead th {
            background-color: #f7fafc;
            color: #4a5568;
            font-weight: 600;
            border: none;
            padding: 12px;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table tbody td {
            padding: 12px;
            vertical-align: middle;
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

            .profile-details {
                grid-template-columns: 1fr;
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
        <div class="card profile-card">
            <div class="profile-header">
                <div class="profile-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="profile-info">
                    <h4><?= htmlspecialchars($nama_siswa) ?></h4>
                    <p><i class="fas fa-graduation-cap me-2"></i><?= htmlspecialchars($kelas_siswa) ?></p>
                </div>
            </div>

            <div class="profile-details">
                <div class="profile-item">
                    <div class="profile-item-label">
                        <i class="fas fa-building me-1"></i> Tempat PKL
                    </div>
                    <div class="profile-item-value">
                        <?= htmlspecialchars($nama_tempat) ?>
                    </div>
                </div>

                <div class="profile-item">
                    <div class="profile-item-label">
                        <i class="fas fa-user-tie me-1"></i> Pembimbing
                    </div>
                    <div class="profile-item-value">
                        <?= htmlspecialchars($nama_pembimbing) ?>
                    </div>
                </div>

                <div class="profile-item">
                    <div class="profile-item-label">
                        <i class="fas fa-phone me-1"></i> Kontak Pembimbing
                    </div>
                    <div class="profile-item-value">
                        <?php if ($kontak_pembimbing != '-'): ?>
                            <a href="tel:<?= htmlspecialchars($kontak_pembimbing) ?>" class="text-white text-decoration-none">
                                <?= htmlspecialchars($kontak_pembimbing) ?>
                            </a>
                        <?php else: ?>
                            <?= htmlspecialchars($kontak_pembimbing) ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Absensi -->
        <div class="card">
            <div class="card-header text-white">
                <h4>
                    <i class="fas fa-calendar-check"></i>
                    Form Absensi Harian
                </h4>
            </div>
            <div class="card-body">
                <form id="formAbsensi" method="POST" enctype="multipart/form-data">

                    <div class="mb-3">
                        <label for="jam_masuk" class="form-label">
                            <i class="fas fa-clock me-2"></i>Jam Masuk
                        </label>
                        <input type="text" class="form-control" id="jam_masuk" name="jam_masuk" readonly placeholder="Sedang mengambil waktu...">
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">
                            <i class="fas fa-list-check me-2"></i>Status Kehadiran
                        </label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="Hadir">Hadir</option>
                            <option value="Sakit">Sakit</option>
                            <option value="Izin">Izin</option>
                        </select>
                    </div>

                    <!-- Keterangan Field (Conditional) -->
                    <div class="mb-3" id="keteranganWrapper">
                        <label for="keterangan" class="form-label">
                            <i class="fas fa-comment-dots me-2"></i>Keterangan <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="3" placeholder="Jelaskan alasan sakit/izin Anda..."></textarea>
                        <div class="form-text">Wajib diisi jika status Sakit atau Izin</div>
                    </div>

                    <input type="hidden" id="lokasi_masuk" name="lokasi_masuk">

                    <!-- Status GPS -->
                    <div class="mb-3">
                        <div id="statusGPS"></div>
                    </div>

                    <div class="mb-3">
                        <label for="foto_bukti" class="form-label">
                            <i class="fas fa-camera me-2"></i>Foto Bukti (Selfie/Aktivitas)
                        </label>
                        <input class="form-control" type="file" id="foto_bukti" name="foto_bukti_original" accept="image/*" required>

                        <div class="mt-2">
                            <div class="form-text text-muted" id="kompresiInfo">Pilih foto untuk melihat status kompresi.</div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100" id="submitButton" disabled>
                        <i class="fas fa-paper-plane me-2"></i>
                        Absen Sekarang (Tunggu Kompresi & Lokasi)
                    </button>

                    <div class="mt-3" id="responseMessage"></div>
                </form>
            </div>
        </div>

        <!-- Riwayat Absensi -->
        <div class="card">
            <div class="card-header text-white">
                <h4>
                    <i class="fas fa-history"></i>
                    Riwayat Absensi Saya
                </h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="dataAbsensiSiswa" class="table table-hover dt-responsive nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jam Masuk</th>
                                <th>Status</th>
                                <th>Keterangan</th>
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
        <i class="fas fa-copyright me-1"></i> <?= date('Y') ?> SMK Informatika Sumedang. All rights reserved.
        <p>Developed by Tefa Ifsu</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>

    <script>
        let compressedFile = null;
        let dataTableAbsensi;
        let locationObtained = false;

        // =================================================================
        // 1. FUNGSI GPS (GEOLOCATION)
        // =================================================================
        function showPosition(position) {
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;
            $('#lokasi_masuk').val(latitude + "," + longitude);
            locationObtained = true;

            if (compressedFile) {
                $('#submitButton').prop('disabled', false).html('<i class="fas fa-paper-plane me-2"></i>Absen Sekarang');
            } else {
                $('#submitButton').html('<i class="fas fa-camera me-2"></i>Pilih Foto Absensi');
            }

            $('#statusGPS').html('<div class="status-indicator status-success"><i class="fas fa-check-circle"></i>Lokasi GPS berhasil didapatkan</div>');
        }

        function showError(error) {
            let errorMsg = "Gagal mengambil lokasi. ";
            switch (error.code) {
                case error.PERMISSION_DENIED:
                    errorMsg += "Izin lokasi ditolak.";
                    break;
                case error.POSITION_UNAVAILABLE:
                    errorMsg += "Lokasi tidak tersedia.";
                    break;
                case error.TIMEOUT:
                    errorMsg += "Waktu habis.";
                    break;
                case error.UNKNOWN_ERROR:
                    errorMsg += "Error tidak diketahui.";
                    break;
            }

            $('#statusGPS').html('<div class="status-indicator status-warning"><i class="fas fa-exclamation-triangle"></i>' + errorMsg + '</div>');
            $('#responseMessage').html('<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>Absensi memerlukan akses lokasi GPS.</div>');
            $('#submitButton').prop('disabled', true).html('<i class="fas fa-ban me-2"></i>Perlu Akses Lokasi');
        }

        function getLocation() {
            $('#submitButton').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Mencari Lokasi GPS...');
            $('#statusGPS').html('<div class="status-indicator status-info"><i class="fas fa-satellite-dish"></i>Mengambil lokasi GPS...</div>');

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showPosition, showError, {
                    enableHighAccuracy: true,
                    timeout: 8000,
                    maximumAge: 0
                });
            } else {
                $('#statusGPS').html('<div class="status-indicator status-warning"><i class="fas fa-times-circle"></i>Browser tidak mendukung GPS</div>');
                $('#responseMessage').html('<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>Browser tidak mendukung geolocation.</div>');
            }
        }

        // =================================================================
        // 2. FUNGSI KOMPRESI GAMBAR
        // =================================================================
        function compressImage(file) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.readAsDataURL(file);

                reader.onload = event => {
                    const img = new Image();
                    img.src = event.target.result;
                    img.onload = () => {
                        const canvas = document.createElement('canvas');
                        const MAX_SIZE = 1024;
                        let width = img.width;
                        let height = img.height;

                        if (width > height) {
                            if (width > MAX_SIZE) {
                                height *= MAX_SIZE / width;
                                width = MAX_SIZE;
                            }
                        } else {
                            if (height > MAX_SIZE) {
                                width *= MAX_SIZE / height;
                                height = MAX_SIZE;
                            }
                        }

                        canvas.width = width;
                        canvas.height = height;

                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, width, height);

                        canvas.toBlob(blob => {
                            resolve(blob);
                        }, 'image/jpeg', 0.7);
                    };
                    img.onerror = reject;
                };
                reader.onerror = reject;
            });
        }

        // =================================================================
        // 3. FUNGSI TOGGLE KETERANGAN
        // =================================================================
        function toggleKeterangan() {
            const status = $('#status').val();
            const keteranganWrapper = $('#keteranganWrapper');
            const keteranganField = $('#keterangan');

            if (status === 'Sakit' || status === 'Izin') {
                keteranganWrapper.addClass('show');
                keteranganField.prop('required', true);
            } else {
                keteranganWrapper.removeClass('show');
                keteranganField.prop('required', false);
                keteranganField.val(''); // Clear value when hidden
            }
        }

        // =================================================================
        // 4. FUNGSI DATA TABLES
        // =================================================================
        function loadAbsensiData() {
            if (dataTableAbsensi) {
                dataTableAbsensi.destroy();
            }

            dataTableAbsensi = $('#dataAbsensiSiswa').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: 'data_absensi_siswa.php',
                    type: 'POST'
                },
                columns: [
                    { data: 'tanggal_absensi' },
                    { data: 'jam_masuk' },
                    { data: 'status' },
                    { data: 'keterangan' },
                    { data: 'lokasi_masuk' },
                    { data: 'foto_bukti' }
                ],
                columnDefs: [
                    {
                        targets: 3,
                        render: function(data, type, row) {
                            if (type === 'display') {
                                if (data && data.trim() !== '') {
                                    return '<span class="text-muted">' + data + '</span>';
                                }
                                return '<span class="text-muted fst-italic">-</span>';
                            }
                            return data;
                        }
                    },
                    {
                        targets: 4,
                        render: function(data, type, row) {
                            if (type === 'display' && data && data.includes(',')) {
                                return `<a href="https://www.google.com/maps/search/?api=1&query=${data}" target="_blank" class="btn btn-sm btn-info text-white"><i class="fas fa-map-marker-alt me-1"></i>Lihat Peta</a>`;
                            }
                            return data;
                        }
                    },
                    {
                        targets: 5,
                        render: function(data, type, row) {
                            if (type === 'display' && data) {
                                return `<button type="button" class="btn btn-sm btn-secondary" onclick="window.open('${data}', '_blank')"><i class="fas fa-image me-1"></i>Lihat Foto</button>`;
                            }
                            return '-';
                        }
                    }
                ],
                order: [[0, 'desc']],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json"
                }
            });
        }

        // =================================================================
        // MAIN READY FUNCTION
        // =================================================================
        $(document).ready(function() {
            loadAbsensiData();
            getLocation();

            // Update time display
            function updateTimeDisplay() {
                const now = new Date();
                const timeString = now.toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });
                $('#jam_masuk').val(timeString);
            }
            updateTimeDisplay();

            // Toggle keterangan field based on status
            $('#status').on('change', toggleKeterangan);
            toggleKeterangan(); // Initial check

            // Handle file upload
            $('#foto_bukti').on('change', function(e) {
                const file = e.target.files[0];
                const submitButton = $('#submitButton');

                if (!file) {
                    compressedFile = null;
                    submitButton.prop('disabled', true).html('<i class="fas fa-camera me-2"></i>Pilih Foto Absensi');
                    $('#kompresiInfo').html('Pilih foto untuk melihat status kompresi.');
                    $('#responseMessage').html('');
                    return;
                }

                // =============================================================
                // VALIDASI TIMESTAMP FILE (CLIENT-SIDE)
                // =============================================================
                const fileDate = new Date(file.lastModified);
                const today = new Date();
                const diffTime = Math.abs(today - fileDate);
                const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
                
                // Toleransi 1 hari
                if (diffDays > 1) {
                    const fileDateStr = fileDate.toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric'
                    });
                    
                    $('#responseMessage').html(`<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i><strong>Foto Ditolak!</strong><br><small>Foto yang dipilih terlalu lama (tanggal: ${fileDateStr}). Gunakan foto yang baru diambil hari ini untuk absensi.</small></div>`);
                    $(this).val(''); // Clear input file
                    $('#kompresiInfo').html('Pilih foto untuk melihat status kompresi.');
                    compressedFile = null;
                    submitButton.prop('disabled', true).html('<i class="fas fa-camera me-2"></i>Pilih Foto Absensi');
                    return;
                }

                // Simpan timestamp asli untuk dipertahankan setelah kompresi
                const originalLastModified = file.lastModified;

                submitButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Mengompresi Gambar...');
                $('#kompresiInfo').html('<span class="text-warning"><i class="fas fa-cog fa-spin me-2"></i>Sedang memproses dan mengompresi gambar...</span>');
                $('#responseMessage').html('');

                compressImage(file).then(compressedBlob => {
                    // Pertahankan timestamp asli file
                    compressedFile = new File([compressedBlob], "absensi_compressed.jpg", {
                        type: 'image/jpeg',
                        lastModified: originalLastModified  // Gunakan timestamp asli, bukan Date.now()
                    });

                    const originalSize = (file.size / 1024).toFixed(1);
                    const compressedSize = (compressedFile.size / 1024).toFixed(1);

                    $('#kompresiInfo').html(`<span class="text-success"><i class="fas fa-check-circle me-2"></i><strong>Kompresi berhasil!</strong> Ukuran asli: ${originalSize} KB → Terkompresi: <strong>${compressedSize} KB</strong></span>`);

                    if (locationObtained && $('#lokasi_masuk').val()) {
                        submitButton.prop('disabled', false).html('<i class="fas fa-paper-plane me-2"></i>Absen Sekarang');
                    } else {
                        submitButton.html('<i class="fas fa-hourglass-half me-2"></i>Menunggu Lokasi...');
                    }

                }).catch(error => {
                    $('#kompresiInfo').html('<span class="text-danger"><i class="fas fa-exclamation-circle me-2"></i>Gagal kompresi. Coba lagi atau gunakan file lain.</span>');
                    compressedFile = null;
                    submitButton.prop('disabled', true).html('<i class="fas fa-times me-2"></i>Gagal Kompresi');
                });
            });

            // Form submission
            $('#formAbsensi').on('submit', function(e) {
                e.preventDefault();

                if (!$('#lokasi_masuk').val() || !compressedFile) {
                    $('#responseMessage').html('<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>Mohon tunggu proses pengambilan lokasi dan kompresi foto selesai.</div>');
                    return;
                }

                // Validate keterangan if required
                const status = $('#status').val();
                const keterangan = $('#keterangan').val().trim();
                if ((status === 'Sakit' || status === 'Izin') && keterangan === '') {
                    $('#responseMessage').html('<div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-2"></i>Keterangan wajib diisi untuk status Sakit atau Izin.</div>');
                    $('#keterangan').focus();
                    return;
                }

                const formData = new FormData(this);
                formData.delete('foto_bukti_original');
                formData.append('foto_bukti', compressedFile);

                $.ajax({
                    url: 'proses_absensi.php',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        $('#responseMessage').html('<div class="alert alert-info"><i class="fas fa-spinner fa-spin me-2"></i>Memproses absensi... Mohon tunggu.</div>');
                        $('#submitButton').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Memproses...');
                    },
                    success: function(response) {
                        $('#responseMessage').html(response);

                        $('#formAbsensi')[0].reset();
                        compressedFile = null;
                        $('#kompresiInfo').html('Pilih foto untuk melihat status kompresi.');
                        toggleKeterangan(); // Reset keterangan visibility

                        if (response.includes('berhasil')) {
                            $('#submitButton').prop('disabled', true).html('<i class="fas fa-camera me-2"></i>Pilih Foto untuk Absen Lagi');
                        } else {
                            if (locationObtained && $('#lokasi_masuk').val()) {
                                $('#submitButton').prop('disabled', false).html('<i class="fas fa-camera me-2"></i>Pilih Foto Absensi');
                            } else {
                                $('#submitButton').prop('disabled', true).html('<i class="fas fa-camera me-2"></i>Pilih Foto Absensi');
                            }
                        }

                        dataTableAbsensi.ajax.reload(null, false);
                    },
                    error: function(xhr, status, error) {
                        $('#responseMessage').html('<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>Gagal memproses absensi: ' + error + '</div>');

                        if (locationObtained && compressedFile) {
                            $('#submitButton').prop('disabled', false).html('<i class="fas fa-paper-plane me-2"></i>Absen Sekarang');
                        } else {
                            $('#submitButton').prop('disabled', true).html('<i class="fas fa-camera me-2"></i>Pilih Foto Absensi');
                        }
                    }
                });
            });
        });
    </script>
</body>

</html>