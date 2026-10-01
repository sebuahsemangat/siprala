<?php
session_start();
// Pastikan file koneksi.php tersedia untuk di-include jika dibutuhkan di masa depan, 
// namun saat ini data siswa diambil dari session.
// include 'koneksi.php'; 

// 1. Pengamanan (Cek Login)
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    $_SESSION['login_error'] = 'Anda harus login untuk mengakses halaman ini.';
    header('Location: index.php');
    exit();
}

// Ambil data siswa dari session
$nama_siswa = $_SESSION['nama_siswa'] ?? 'Siswa';
$kelas_siswa = $_SESSION['kelas_siswa'] ?? '';
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
    <title>Halaman Absensi Siswa PKL</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">

    <style>
        /* CSS untuk Sticky Footer */
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
            margin-top: auto; 
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid container">
            <a class="navbar-brand" href="#">Absensi PKL</a>
            <div class="d-flex flex-column flex-sm-row align-items-sm-center">
                <span class="navbar-text me-3 text-white">
                    Halo, <strong><?= htmlspecialchars($nama_siswa) ?></strong>
                </span>
                <span class="navbar-text me-3 text-white">
                    Kelas: <?= htmlspecialchars($kelas_siswa) ?>
                </span>
                <a href="logout.php" class="btn btn-warning btn-sm mt-2 mt-sm-0">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container main-content">
        
        <div class="card shadow mb-4">
            <div class="card-header bg-success text-white">
                <h4>Form Absensi Harian</h4>
            </div>
            <div class="card-body">
                <form id="formAbsensi" method="POST" enctype="multipart/form-data"> 
                    
                    <div class="mb-3">
                        <label for="jam_masuk" class="form-label">Jam Masuk</label>
                        <input type="text" class="form-control" id="jam_masuk" name="jam_masuk" readonly placeholder="Sedang mengambil waktu server...">
                    </div>
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Status Kehadiran</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="Hadir">Hadir</option>
                            <option value="Sakit">Sakit</option>
                            <option value="Izin">Izin</option>
                        </select>
                    </div>

                    <input type="hidden" id="lokasi_masuk" name="lokasi_masuk">
                    
                    <!-- Status GPS Terpisah -->
                    <div class="mb-3">
                        <div id="statusGPS" class="form-text"></div>
                    </div>

                    <div class="mb-3">
                        <label for="foto_bukti" class="form-label">Foto Bukti (Selfie/Aktivitas)</label>
                        <input class="form-control" type="file" id="foto_bukti" name="foto_bukti_original" accept="image/*" required> 
                        
                        <div class="mt-2">
                            <div class="form-text text-muted" id="kompresiInfo">Pilih foto untuk melihat status kompresi.</div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100" id="submitButton" disabled>
                        Absen Sekarang (Tunggu Kompresi & Lokasi)
                    </button>
                    
                    <div class="mt-3" id="responseMessage">
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white">
                <h4>Riwayat Absensi Saya</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="dataAbsensiSiswa" class="table table-striped table-bordered dt-responsive nowrap" style="width:100%">
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
            &copy; <?= date('Y') ?> Aplikasi Absensi PKL.
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

    <script>
        let compressedFile = null;
        let dataTableAbsensi; // Variabel DataTables
        let locationObtained = false; // Flag untuk tracking status GPS

        // =================================================================
        // 1. FUNGSI GPS (GEOLOCATION)
        // =================================================================
        function showPosition(position) {
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;
            $('#lokasi_masuk').val(latitude + "," + longitude);
            locationObtained = true;
            
            // Aktifkan tombol submit jika kompresi sudah OK
            if (compressedFile) {
                $('#submitButton').prop('disabled', false).text('Absen Sekarang');
            } else {
                $('#submitButton').text('Pilih Foto Absensi');
            }
            
            // Tampilkan status GPS di tempat terpisah (tidak menimpa responseMessage)
            $('#statusGPS').html('<small class="text-success">✓ Lokasi (GPS) berhasil didapatkan.</small>');
        }

        function showError(error) {
            let errorMsg = "Gagal mengambil lokasi. ";
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    errorMsg += "Pengguna menolak izin lokasi. (Pastikan izin browser diset Izinkan).";
                    break;
                case error.POSITION_UNAVAILABLE:
                    errorMsg += "Informasi lokasi tidak tersedia.";
                    break;
                case error.TIMEOUT:
                    errorMsg += "Waktu permintaan lokasi habis. Coba muat ulang halaman.";
                    break;
                case error.UNKNOWN_ERROR:
                    errorMsg += "Terjadi kesalahan yang tidak diketahui.";
                    break;
            }
            
            // Tampilkan error GPS di tempat terpisah
            $('#statusGPS').html('<small class="text-danger">✗ ' + errorMsg + '</small>');
            $('#responseMessage').html('<div class="alert alert-danger mt-3">Absensi tidak dapat diproses tanpa lokasi.</div>');
            
            // Jika gagal, pastikan tombol tetap disabled
            $('#submitButton').prop('disabled', true).text('Absensi Diblokir (Perlu Lokasi)');
        }

        function getLocation() {
            // Nonaktifkan tombol saat mencoba mengambil lokasi
            $('#submitButton').prop('disabled', true).text('Mencari Lokasi GPS...'); 
            $('#statusGPS').html('<small class="text-info">⏳ Sedang mengambil lokasi GPS...</small>');
            
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showPosition, showError, {
                    enableHighAccuracy: true,
                    timeout: 8000, 
                    maximumAge: 0
                });
            } else {
                $('#statusGPS').html('<small class="text-danger">✗ Geolocation tidak didukung oleh browser Anda.</small>');
                $('#responseMessage').html('<div class="alert alert-danger mt-3">Geolocation tidak didukung oleh browser Anda.</div>');
            }
        }

        // =================================================================
        // 2. FUNGSI KOMPRESI GAMBAR (CLIENT-SIDE)
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

                        // Logika resize
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

                        // Kompresi ke Blob (JPEG) dengan kualitas 70%
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
        // 3. FUNGSI DATA TABLES (RIWAYAT ABSENSI)
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
                    { data: 'lokasi_masuk' },
                    { data: 'foto_bukti' } 
                ],
                columnDefs: [
                    // Render Lokasi menjadi link Google Maps
                    {
                        targets: 3,
                        render: function (data, type, row) {
                            if (type === 'display' && data && data.includes(',')) {
                                // Buat link Google Maps
                                return `<a href="https://www.google.com/maps/search/?api=1&query=${data}" target="_blank" class="btn btn-sm btn-info text-white">Lihat Peta</a>`;
                            }
                            return data;
                        }
                    },
                    // Render Foto Bukti menjadi tombol lihat
                    {
                        targets: 4,
                        render: function (data, type, row) {
                            if (type === 'display' && data) {
                                return `<button type="button" class="btn btn-sm btn-secondary" onclick="window.open('${data}', '_blank')">Lihat Foto</button>`;
                            }
                            return '-';
                        }
                    },
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
            
            // 1. Load Data
            loadAbsensiData();
            getLocation(); 

            // 2. Otomatis isi Jam Masuk (Waktu Lokal)
            // Bisa diganti dengan panggilan AJAX ke server untuk waktu lebih akurat
            function updateTimeDisplay() {
                const now = new Date();
                const timeString = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                $('#jam_masuk').val(timeString);
            }
            updateTimeDisplay();


            // 3. Tangani Event Pilihan File (Kompresi)
            $('#foto_bukti').on('change', function(e) {
                const file = e.target.files[0];
                const submitButton = $('#submitButton');

                if (!file) {
                    compressedFile = null;
                    submitButton.prop('disabled', true).text('Pilih Foto Absensi');
                    $('#kompresiInfo').html('Pilih foto untuk melihat status kompresi.');
                    return;
                }
                
                // Nonaktifkan tombol saat kompresi berjalan
                submitButton.prop('disabled', true).text('⏳ Sedang Mengompresi Gambar...');
                $('#kompresiInfo').html('<span class="text-warning">⏳ Sedang memproses dan mengompresi gambar...</span>');

                compressImage(file).then(compressedBlob => {
                    // Ubah Blob menjadi File untuk dikirim ke server
                    compressedFile = new File([compressedBlob], "absensi_compressed.jpg", {
                        type: 'image/jpeg',
                        lastModified: Date.now()
                    });

                    const originalSize = (file.size / 1024).toFixed(1); // KB
                    const compressedSize = (compressedFile.size / 1024).toFixed(1); // KB
                    
                    $('#kompresiInfo').html(`✅ **Kompresi berhasil!** Ukuran asli: ${originalSize} KB. Ukuran terkompresi: <strong>${compressedSize} KB</strong>.`);
                    
                    // Aktifkan tombol submit hanya jika lokasi juga sudah didapat
                    if (locationObtained && $('#lokasi_masuk').val()) {
                        submitButton.prop('disabled', false).text('Absen Sekarang');
                    } else {
                        submitButton.text('Menunggu Lokasi...');
                    }
                    
                }).catch(error => {
                    $('#kompresiInfo').html('❌ Gagal kompresi. Coba lagi atau gunakan file lain.');
                    compressedFile = null;
                    submitButton.prop('disabled', true).text('Gagal Kompresi');
                });
            });

            // 4. Logika Submit AJAX
            $('#formAbsensi').on('submit', function(e) {
                e.preventDefault();

                // Final check lokasi dan kompresi
                if (!$('#lokasi_masuk').val() || !compressedFile) {
                    $('#responseMessage').html('<div class="alert alert-danger">Mohon tunggu proses pengambilan lokasi dan kompresi foto selesai.</div>');
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
                        $('#responseMessage').html('<div class="alert alert-info">Memproses absensi... Mohon tunggu.</div>');
                        $('#submitButton').prop('disabled', true).text('Memproses...');
                    },
                    success: function(response) {
                        // Tampilkan response dari server (termasuk pesan duplikasi)
                        $('#responseMessage').html(response);
                        
                        // Reset form
                        $('#formAbsensi')[0].reset(); 
                        compressedFile = null;
                        $('#kompresiInfo').html('Pilih foto untuk melihat status kompresi.');
                        
                        // Cek apakah absensi berhasil atau duplikasi
                        // Jika sukses, aktifkan tombol. Jika duplikasi/error, tetap bisa coba lagi
                        if (response.includes('berhasil')) {
                            $('#submitButton').prop('disabled', true).text('Pilih Foto untuk Absen Lagi');
                        } else {
                            // Untuk error/duplikasi, biarkan user bisa coba lagi jika sudah siap
                            if (locationObtained && $('#lokasi_masuk').val()) {
                                $('#submitButton').prop('disabled', false).text('Pilih Foto Absensi');
                            } else {
                                $('#submitButton').prop('disabled', true).text('Pilih Foto Absensi');
                            }
                        }
                        
                        // Muat ulang data tabel setelah submit (berhasil atau tidak)
                        dataTableAbsensi.ajax.reload(null, false); 
                    },
                    error: function(xhr, status, error) {
                        $('#responseMessage').html('<div class="alert alert-danger">Gagal memproses absensi: ' + error + '</div>');
                        
                        // Kembalikan status tombol
                        if (locationObtained && compressedFile) {
                            $('#submitButton').prop('disabled', false).text('Absen Sekarang');
                        } else {
                            $('#submitButton').prop('disabled', true).text('Pilih Foto Absensi');
                        }
                    }
                });
            });
        });
    </script>
</body>

</html>