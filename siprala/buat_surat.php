<?php
// buat_surat_content.php - Form Konten Dinamis untuk dimuat via AJAX
// Hapus semua tag HTML, Head, Body, dan link CSS/JS eksternal.

include 'koneksi.php'; // Include file koneksi

// --- LOGIKA GENERASI NOMOR SURAT OTOMATIS (FIXED) ---

// 1. Ambil Data Settings (termasuk format_nomor_surat)
$settings = [];
$format_nomor_surat = '/PAN-PKL/SMK-IF/YPS/X/2025'; // Default jika gagal
$query_settings = "SELECT nama_sekolah, tgl_mulai, tgl_selesai, nama_kepsek, format_nomor_surat FROM settings LIMIT 1";
$result_settings = $koneksi->query($query_settings);

if ($result_settings && $result_settings->num_rows > 0) {
    $settings = $result_settings->fetch_assoc();
    $format_nomor_surat = $settings['format_nomor_surat'];
} else {
    // Data default jika gagal koneksi atau tabel kosong
    $settings = [
        'nama_sekolah' => 'SMK INFORMATIKA SUMEDANG',
        'tgl_mulai' => '2025-12-01',
        'tgl_selesai' => '2026-03-31',
        'nama_kepsek' => 'Tatang Suryana, S.Ag., M.Pd',
    ];
}

// 2. Ambil Nomor Urut Surat Terakhir (LOGIKA PERBAIKAN)
$nomor_urut_terakhir = 0;

/* * Query di bawah mengambil 3 digit pertama dari no_surat,
 * mengubahnya menjadi angka (CAST), dan mencari nilai terbesar.
 */
$query_last_number = "
    SELECT 
        CAST(SUBSTR(no_surat, 1, 3) AS UNSIGNED) AS nomor_urut_tertinggi
    FROM surat
    ORDER BY nomor_urut_tertinggi DESC
    LIMIT 1
";
$result_last_number = $koneksi->query($query_last_number);

if ($result_last_number && $result_last_number->num_rows > 0) {
    $row = $result_last_number->fetch_assoc();
    // Jika ada data, ambil nomor urut tertinggi
    $nomor_urut_terakhir = $row['nomor_urut_tertinggi'];
}
// Jika tidak ada data, $nomor_urut_terakhir tetap 0.


// 3. Hitung Nomor Surat Baru
$nomor_urut_baru = $nomor_urut_terakhir + 1;

// Bulan Romawi
$bulan_angka = date('n');

// Membuat array mapping angka ke Romawi
$romawi = [
    1 => 'I',
    2 => 'II',
    3 => 'III',
    4 => 'IV',
    5 => 'V',
    6 => 'VI',
    7 => 'VII',
    8 => 'VIII',
    9 => 'IX',
    10 => 'X',
    11 => 'XI',
    12 => 'XII'
];

// Menampilkan bulan dalam format Romawi
$bulan_romawi = $romawi[$bulan_angka];

// Format: 001/PAN-PKL/SMK-IF/YPS/X/2025
$nomor_surat_baru = sprintf('%03d', $nomor_urut_baru) . $format_nomor_surat . $bulan_romawi . "/" . date('Y');

// 4. Ambil Tanggal Hari Ini dalam format YYYY-MM-DD
$tanggal_hari_ini = date('Y-m-d');

$koneksi->close(); // Tutup koneksi setelah selesai mengambil data
?>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i> Form Pembuatan Surat Pengantar PKL</h5>
    </div>
    <div class="card-body container-form">
        <form id="formBuatSurat" action="generate_surat.php" method="POST">

            <fieldset class="mb-4 p-3 border rounded">
                <legend class="float-none w-auto px-2 fs-6 text-primary">Informasi Surat</legend>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="nomor_surat" class="form-label">Nomor Surat Otomatis</label>
                        <input type="text" class="form-control" id="nomor_surat_display"
                            value="<?php echo $nomor_surat_baru; ?>" disabled>
                        <input type="hidden" name="nomor_surat" value="<?php echo $nomor_surat_baru; ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="tanggal_surat" class="form-label">Tanggal Surat</label>
                        <input type="date" class="form-control" id="tanggal_surat" name="tanggal_surat"
                            value="<?php echo $tanggal_hari_ini; ?>" required>
                    </div>
                </div>

                <div class="row" style="display:none;">
                    <div class="col-md-6">
                        <label for="tgl_mulai_display" class="form-label">Tanggal Mulai PKL</label>
                        <input type="text" class="form-control" id="tgl_mulai_display"
                            value="<?php echo $settings['tgl_mulai']; ?>" disabled>
                        <input type="hidden" name="tgl_mulai" value="<?php echo $settings['tgl_mulai']; ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="tgl_selesai_display" class="form-label">Tanggal Selesai PKL</label>
                        <input type="text" class="form-control" id="tgl_selesai_display"
                            value="<?php echo $settings['tgl_selesai']; ?>" disabled>
                        <input type="hidden" name="tgl_selesai" value="<?php echo $settings['tgl_selesai']; ?>">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="perihal" class="form-label">Perihal</label>
                        <select name="perihal" id="perihal" class="form-control">
                            <option value="Pengajuan Tempat Praktik Kerja Lapangan (PKL)">Pengajuan Tempat Praktik Kerja
                                Lapangan (PKL)</option>
                            <option value="Penambahan Siswa Praktik Kerja Lapangan (PKL)">Penambahan Siswa Praktik Kerja
                                Lapangan (PKL)</option>
                            <!-- <option value="Pemberitahuan Pembatalan Siswa Praktik Kerja Lapangan (PKL)">Pemberitahuan Pembatalan Siswa Praktik Kerja Lapangan (PKL)</option> -->
                        </select>
                    </div>

                    <div class="col-md-6" id="container_referensi" style="display: none;">
                        <label for="id_referensi_surat" class="form-label">Pilih Tempat PKL (Referensi Surat)</label>
                        <select id="id_referensi_surat" class="form-select">
                            <option value="">-- Pilih Tempat --</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3" id="container_no_surat_ref" style="display: none;">
                    <div class="col-md-6">
                        <label class="form-label text-danger">Nomor Surat Sebelumnya</label>
                        <input type="text" class="form-control bg-light" id="display_no_surat_referensi" readonly
                            placeholder="Otomatis terisi...">
                        <input type="hidden" name="no_surat_referensi" id="val_no_surat_referensi">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-danger">Tanggal Surat Sebelumnya</label>
                        <input type="text" class="form-control bg-light" id="display_tanggal_referensi" readonly
                            placeholder="Otomatis terisi...">
                        <input type="hidden" name="tanggal_surat_referensi" id="val_tanggal_referensi">
                    </div>
                </div>
                <input type="hidden" name="nama_sekolah" value="<?php echo $settings['nama_sekolah']; ?>">
                <input type="hidden" name="nama_kepsek" value="<?php echo $settings['nama_kepsek']; ?>">
            </fieldset>

            <fieldset class="mb-4 p-3 border rounded">
                <legend class="float-none w-auto px-2 fs-6 text-info">Data Tempat PKL</legend>

                <div class="mb-3">
                    <label for="nama_perusahaan" class="form-label">Nama Perusahaan / Instansi</label>
                    <input type="text" class="form-control" id="nama_perusahaan" name="nama_perusahaan"
                        list="datalistOptions" placeholder="Ketik untuk mencari atau menambahkan perusahaan baru..."
                        required>
                    <datalist id="datalistOptions">
                    </datalist>

                    <!-- Alert Catatan Tempat PKL -->
                    <div id="alert_catatan_perusahaan" class="alert alert-warning mt-2 d-none" role="alert">
                        <div class="d-flex">
                            <i class="fas fa-exclamation-triangle mt-1 me-2 flex-shrink-0 text-warning"></i>
                            <div>
                                <strong class="d-block mb-1">Catatan Tempat PKL:</strong>
                                <span id="isi_catatan_perusahaan" style="white-space: pre-line;"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Alert Kapasitas Tempat PKL -->
                    <div id="alert_kapasitas_perusahaan" class="alert alert-info mt-2 d-none" role="alert">
                        <div class="d-flex">
                            <i id="icon_kapasitas_perusahaan" class="fas fa-users mt-1 me-2 flex-shrink-0 text-info"></i>
                            <div>
                                <strong class="d-block mb-1">Kapasitas Siswa Tempat PKL:</strong>
                                <span id="isi_kapasitas_perusahaan"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="tujuan_departemen" class="form-label">Yth. Tujuan (Contoh:
                        HRD/Pimpinan/Direktur)</label>
                    <input type="text" class="form-control" id="tujuan_departemen" name="tujuan_departemen"
                        value="Pimpinan" required>
                </div>
                <div class="mb-3">
                    <label for="alamat_perusahaan" class="form-label">Alamat Lengkap</label>
                    <textarea class="form-control" id="alamat_perusahaan" name="alamat_perusahaan" rows="2"
                        placeholder="Masukkan alamat lengkap perusahaan" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="kota_perusahaan" class="form-label">Kota Tujuan Surat</label>
                    <input type="text" class="form-control" id="kota_perusahaan" name="kota_perusahaan" required>
                </div>
            </fieldset>

            <fieldset class="mb-4 p-3 border rounded">
                <legend class="float-none w-auto px-2 fs-6 text-success">Daftar Peserta PKL</legend>

                <div class="row mb-3">
                    <div class="col-auto">
                        <button type="button" class="btn btn-sm btn-info text-white" data-bs-toggle="modal"
                            data-bs-target="#studentSearchModal"><i class="fas fa-search"></i> Cari & Tambah
                            Siswa</button>
                    </div>
                    <div class="col-auto ms-auto">
                        <button type="button" class="btn btn-sm btn-danger" id="removeStudentBtn" disabled><i
                                class="fas fa-user-minus"></i> Hapus Siswa Terakhir</button>
                    </div>
                </div>

                <div class="student-list-container">
                    <div id="student-list">
                    </div>
                </div>

            </fieldset>

            <div class="d-grid">
                <button type="submit" id="btnSubmitSurat" class="btn btn-primary btn-lg">
                    <i class="fas fa-file-pdf me-2"></i> Generate & Simpan Surat
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Konfirmasi Sukses Generate Surat -->
<div class="modal fade" id="modalSuksesSurat" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="modalSuksesSuratLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalSuksesSuratLabel"><i class="fas fa-check-circle me-2"></i> Surat
                    Berhasil Dibuat</h5>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-3 text-success">
                    <i class="fas fa-file-circle-check fa-4x"></i>
                </div>
                <h5 class="fw-bold mb-2">Surat Berhasil Dibuat dan Diarsipkan!</h5>
                <p class="text-muted mb-2">Nomor Surat: <strong id="suksesNoSurat" class="text-dark"></strong></p>
                <div class="alert alert-info py-2 px-3 small text-start">
                    <i class="fas fa-info-circle me-1"></i> Data dan file fisik PDF telah aman tersimpan di server. Anda
                    dapat membuka/mencetak surat sekarang atau kapan saja melalui menu <strong>Data Surat
                        Keluar</strong>.
                </div>
            </div>
            <div class="modal-footer justify-content-center gap-2">
                <a href="#" id="btnDownloadPdf" target="_blank" class="btn btn-primary">
                    <i class="fas fa-print me-1"></i> Buka / Cetak PDF
                </a>
                <button type="button" id="btnLihatDataSurat" class="btn btn-outline-success">
                    <i class="fas fa-clipboard-list me-1"></i> Lihat Data Surat
                </button>
                <button type="button" id="btnBuatSuratLagi" class="btn btn-secondary">
                    <i class="fas fa-plus me-1"></i> Buat Surat Lain
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="studentSearchModal" tabindex="-1" aria-labelledby="studentSearchModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="studentSearchModalLabel">Cari Data Siswa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <input type="text" class="form-control" id="studentSearchInput"
                        placeholder="Ketik NIS atau Nama Siswa (min. 2 karakter)">
                </div>
                <div id="searchResults" class="list-group search-results">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        'use strict';

        // --- LOGIKA SISWA (KODE LAMA DIPERBAIKI SCOPE-NYA) ---
        // Menggunakan window object agar variabel tetap tersimpan meski script di-reload
        if (typeof window.studentCount === 'undefined') window.studentCount = 0;
        if (typeof window.selectedStudents === 'undefined') window.selectedStudents = new Map();
        else window.selectedStudents.clear();

        let studentCount = window.studentCount;
        const selectedStudents = window.selectedStudents;

        const studentList = document.getElementById('student-list');
        const removeStudentBtn = document.getElementById('removeStudentBtn');
        const studentSearchInput = $('#studentSearchInput');
        const searchResults = $('#searchResults');

        let tempatPklList = [];

        // Load datalist tempat PKL (untuk input manual & auto-fill)
        function loadTempatPkl() {
            $.ajax({
                url: 'get_tempat_pkl.php',
                method: 'GET',
                dataType: 'json',
                success: function (data) {
                    tempatPklList = data || [];
                    const datalist = $('#datalistOptions');
                    datalist.empty();
                    if (tempatPklList.length > 0) {
                        tempatPklList.forEach(function (item) {
                            datalist.append(`<option value="${item.nama_tempat}">`);
                        });
                    }
                }
            });
        }
        loadTempatPkl();

        // Fungsi helper untuk update alert catatan perusahaan
        function updateCatatanPerusahaan(catatanText) {
            if (catatanText && catatanText.trim() !== '') {
                $('#isi_catatan_perusahaan').text(catatanText.trim());
                $('#alert_catatan_perusahaan').removeClass('d-none');
            } else {
                $('#alert_catatan_perusahaan').addClass('d-none');
                $('#isi_catatan_perusahaan').text('');
            }
        }

        // Fungsi helper untuk update alert kapasitas tempat PKL
        function updateKapasitasPerusahaan(tempat) {
            const alertEl = $('#alert_kapasitas_perusahaan');
            const isiEl = $('#isi_kapasitas_perusahaan');
            const iconEl = $('#icon_kapasitas_perusahaan');

            if (!tempat) {
                alertEl.addClass('d-none');
                isiEl.empty();
                return;
            }

            const kapasitas = parseInt(tempat.kapasitas) || 0;
            const terisi = parseInt(tempat.jumlah_siswa) || 0;

            // Reset class alert dan icon
            alertEl.removeClass('alert-info alert-warning alert-danger alert-secondary d-none');
            iconEl.removeClass('text-info text-warning text-danger text-secondary fas fa-users fa-exclamation-triangle fa-info-circle');

            if (kapasitas > 0) {
                const sisa = kapasitas - terisi;
                if (terisi >= kapasitas) {
                    alertEl.addClass('alert-danger');
                    iconEl.addClass('fas fa-exclamation-triangle text-danger');
                    isiEl.html(`Kapasitas kuota: <strong>${kapasitas} Siswa</strong> &bull; <span class="badge bg-danger">Kuota Penuh</span> (Sudah ditempati: <strong>${terisi}</strong> siswa, sisa kuota: <strong>0</strong> siswa).`);
                } else {
                    alertEl.addClass('alert-info');
                    iconEl.addClass('fas fa-users text-info');
                    isiEl.html(`Kapasitas kuota: <strong>${kapasitas} Siswa</strong> (Sudah ditempati: <strong>${terisi}</strong> siswa, sisa kuota tersedia: <strong>${sisa}</strong> siswa).`);
                }
            } else {
                alertEl.addClass('alert-secondary');
                iconEl.addClass('fas fa-info-circle text-secondary');
                isiEl.html(`Kapasitas kuota: <strong>Tidak Dibatasi</strong> (Bebas / Tanpa batas kuota, terisi saat ini: <strong>${terisi}</strong> siswa).`);
            }
        }

        // Auto-fill alamat, kota, catatan, dan kapasitas ketika nama_perusahaan dipilih dari datalist atau diketik
        $(document).on('input change', '#nama_perusahaan', function () {
            const val = $(this).val().trim();
            if (!val) {
                updateCatatanPerusahaan('');
                updateKapasitasPerusahaan(null);
                return;
            }

            const found = tempatPklList.find(function (item) {
                return item.nama_tempat.toLowerCase() === val.toLowerCase();
            });

            if (found) {
                if (found.alamat) {
                    $('#alamat_perusahaan').val(found.alamat);
                }
                if (found.kota) {
                    $('#kota_perusahaan').val(found.kota);
                }
                updateCatatanPerusahaan(found.catatan);
                updateKapasitasPerusahaan(found);
            } else {
                updateCatatanPerusahaan('');
                updateKapasitasPerusahaan(null);
            }
        });

        // Fungsi Tambah Baris Siswa
        window.addStudentRow = function (id = null, name = '', className = '', phone = '') {
            if (id !== null && selectedStudents.has(id)) {
                alert(`Siswa ${name} sudah ada dalam daftar.`);
                return;
            }

            window.studentCount++;
            studentCount = window.studentCount;
            const currentId = id === null ? `manual_${studentCount}` : id;

            if (id !== null) {
                selectedStudents.set(id, name);
                triggerStudentSearch();
            }

            // Normalisasi nomor jika ada (diawali 08)
            let formattedPhone = phone ? phone.toString().replace(/[^0-9]/g, '') : '';
            if (formattedPhone.startsWith('62')) {
                formattedPhone = '0' + formattedPhone.slice(2);
            } else if (formattedPhone.startsWith('8')) {
                formattedPhone = '0' + formattedPhone;
            }

            const row = `
            <div class="row student-row-group" id="student-row-${currentId}">
                <div class="col-md-1 d-flex align-items-center justify-content-center"><strong class="text-primary fs-5"></strong>${id !== null ? `<input type="hidden" name="siswa[${id}][id]" value="${id}">` : ''}</div>
                <div class="col-md-3"><label class="form-label">Nama Siswa</label><input type="text" class="form-control" name="siswa[${currentId}][nama]" value="${name}" required></div>
                <div class="col-md-4"><label class="form-label">Kelas</label><input type="text" class="form-control" name="siswa[${currentId}][kelas]" value="${className}" required></div>
                <div class="col-md-3"><label class="form-label">No. Handphone</label><input type="tel" class="form-control" name="siswa[${currentId}][hp]" value="${formattedPhone}" placeholder="08xxxxxxxxxx" pattern="^08[0-9]{8,13}$" title="Nomor handphone harus diawali 08 (10-15 digit)" required></div>
                <div class="col-md-1 d-flex align-items-center justify-content-center"><button type="button" class="btn btn-sm btn-danger remove-custom-btn" data-id="${currentId}"><i class="fas fa-times"></i></button></div>
            </div>`;

            $('#student-list').append(row);
            updateStudentButtons();
            $('#student-list').scrollTop($('#student-list')[0].scrollHeight);

            if (id !== null) {
                $('#studentSearchModal').modal('hide');
                studentSearchInput.val('');
                searchResults.empty();
            }
        };

        function removeStudentRow(rowId) {
            const row = document.getElementById(`student-row-${rowId}`);
            if (row) {
                const dbIdMatch = rowId.toString().match(/^(\d+)$/);
                if (dbIdMatch) {
                    const idToRemove = parseInt(dbIdMatch[1]);
                    selectedStudents.delete(idToRemove);
                }
                row.remove();
                updateStudentButtons();
            }
        }

        function updateStudentButtons() {
            $('#removeStudentBtn').prop('disabled', $('#student-list').children().length === 0);
            $('#student-list .student-row-group strong').each(function (index) {
                $(this).text(`${index + 1}.`);
            });
        }

        function triggerStudentSearch() {
            const query = studentSearchInput.val();
            if (query.length < 2) {
                searchResults.html('<div class="alert alert-info m-0">Ketik minimal 2 karakter.</div>');
                return;
            }
            searchResults.html('<div class="text-center p-3"><span class="spinner-border spinner-border-sm"></span> Mencari...</div>');
            $.ajax({
                url: 'search_siswa.php',
                method: 'GET',
                data: { query: query },
                dataType: 'json',
                success: function (data) {
                    searchResults.empty();
                    if (data.length > 0) {
                        data.forEach(siswa => {
                            const isSelected = selectedStudents.has(parseInt(siswa.id_siswa));
                            const disabledClass = isSelected ? 'disabled' : '';
                            const item = $('<a>', {
                                href: '#',
                                class: `list-group-item list-group-item-action ${disabledClass}`,
                                html: `<strong>${siswa.nis} - ${siswa.nama_siswa}</strong><br><small>${siswa.kelas}</small>`
                            }).attr({
                                'data-id': siswa.id_siswa,
                                'data-nama': siswa.nama_siswa,
                                'data-kelas': siswa.kelas,
                                'data-kontak': siswa.kontak_siswa
                            }).on('click', function (e) {
                                e.preventDefault();
                                if (!$(this).hasClass('disabled')) {
                                    addStudentRow(
                                        $(this).data('id'),
                                        $(this).data('nama'),
                                        $(this).data('kelas'),
                                        $(this).data('kontak')
                                    );
                                }
                            });
                            searchResults.append(item);
                        });
                    } else {
                        searchResults.html('<div class="alert alert-warning m-0">Siswa tidak ditemukan.</div>');
                    }
                }
            });
        }

        // Event Listeners Siswa
        $('#removeStudentBtn').on('click', function () {
            $('#student-list .student-row-group').last().find('.remove-custom-btn').click();
        });
        $(document).on('click', '.remove-custom-btn', function () {
            removeStudentRow($(this).data('id'));
        });
        studentSearchInput.on('keyup', function () {
            clearTimeout(window.searchTimeout);
            window.searchTimeout = setTimeout(triggerStudentSearch, 300);
        });

        // --- LOGIKA BARU: REFERENSI SURAT (FIXED) ---

        // Variabel Cache
        let referensiDataCache = [];

        function loadReferensiSurat() {
            // Jika sudah ada cache, pakai itu
            if (referensiDataCache.length > 0) {
                populateReferensiDropdown(referensiDataCache);
                return;
            }

            // Tampilkan loading di console untuk debug
            console.log("Mengambil data referensi surat...");

            $.ajax({
                url: 'get_referensi_surat.php',
                method: 'GET',
                dataType: 'json',
                success: function (data) {
                    console.log("Data diterima:", data); // DEBUG: Cek console browser (F12)
                    referensiDataCache = data;
                    populateReferensiDropdown(data);
                },
                error: function (xhr, status, error) {
                    console.error("AJAX Error:", error);
                    console.log("Response:", xhr.responseText);
                    alert('Gagal mengambil data referensi surat. Cek console untuk detail.');
                }
            });
        }

        function populateReferensiDropdown(data) {
            const selectRef = $('#id_referensi_surat');
            selectRef.empty();
            selectRef.append('<option value="">-- Pilih Tempat PKL --</option>');

            if (data.length === 0) {
                selectRef.append('<option value="" disabled>Tidak ada riwayat pengajuan ditemukan</option>');
                return;
            }

            data.forEach(function (item) {
                selectRef.append(`<option value="${item.id_tempat}" 
                data-nosurat="${item.no_surat}" 
                data-nama="${item.nama_tempat}" 
                data-alamat="${item.alamat || ''}" 
                data-kota="${item.kota || ''}" 
                data-catatan="${item.catatan || ''}"
                data-kapasitas="${item.kapasitas || 0}"
                data-tanggal="${item.tanggal}">
                ${item.nama_tempat} (Surat: ${item.no_surat})
            </option>`);
            });
        }

        // Event Delegate: Gunakan $(document).on agar aman untuk elemen dinamis
        $(document).on('change', '#perihal', function () {
            const selected = $(this).val();
            const containerRef = $('#container_referensi');
            const containerNoSuratRef = $('#container_no_surat_ref');

            if (selected === 'Penambahan Siswa Praktik Kerja Lapangan (PKL)') {
                containerRef.show();
                containerNoSuratRef.show();
                loadReferensiSurat(); // Panggil fungsi load
            } else if (selected === 'Pembatalan Siswa Praktik Kerja Lapangan (PKL)') {
                alert("Pembatalan Siswa Dipilih");
                containerRef.hide();
                containerNoSuratRef.hide();
                $('#id_referensi_surat').val('');
                $('#display_no_surat_referensi').val('');
            } else {
                containerRef.hide();
                containerNoSuratRef.hide();
                $('#id_referensi_surat').val('');
                $('#display_no_surat_referensi').val('');
            }
        });

        $(document).on('change', '#id_referensi_surat', function () {
            const selectedOption = $(this).find(':selected');

            // Ambil data dari atribut
            const noSurat = selectedOption.data('nosurat');
            const namaTempat = selectedOption.data('nama');
            const tanggalSurat = selectedOption.data('tanggal'); // Ambil tanggal
            const alamat = selectedOption.data('alamat');
            const kota = selectedOption.data('kota');
            const idTempat = $(this).val();

            if (idTempat) {
                // Isi Nomor Surat
                $('#display_no_surat_referensi').val(noSurat);
                $('#val_no_surat_referensi').val(noSurat);

                // Isi Tanggal Surat (BARU)
                $('#display_tanggal_referensi').val(tanggalSurat);
                $('#val_tanggal_referensi').val(tanggalSurat);

                // Auto fill nama perusahaan, alamat, dan kota
                $('#nama_perusahaan').val(namaTempat).trigger('change');

                if (alamat) {
                    $('#alamat_perusahaan').val(alamat);
                }
                if (kota) {
                    $('#kota_perusahaan').val(kota);
                }

                // Jika referensi surat tidak membawa alamat/kota/catatan, coba cari di tempatPklList
                const found = tempatPklList.find(function (item) {
                    return item.id_tempat == idTempat || item.nama_tempat.toLowerCase() === (namaTempat || '').toLowerCase();
                });
                if (found) {
                    if (!alamat && found.alamat) $('#alamat_perusahaan').val(found.alamat);
                    if (!kota && found.kota) $('#kota_perusahaan').val(found.kota);
                    updateCatatanPerusahaan(selectedOption.data('catatan') || found.catatan);
                    updateKapasitasPerusahaan(found);
                } else if (selectedOption.data('catatan') || selectedOption.data('kapasitas') !== undefined) {
                    updateCatatanPerusahaan(selectedOption.data('catatan') || '');
                    updateKapasitasPerusahaan({
                        kapasitas: selectedOption.data('kapasitas') || 0,
                        jumlah_siswa: 0
                    });
                }
            } else {
                // Kosongkan jika tidak ada yang dipilih
                $('#display_no_surat_referensi').val('');
                $('#val_no_surat_referensi').val('');

                $('#display_tanggal_referensi').val('');
                $('#val_tanggal_referensi').val('');

                $('#nama_perusahaan').val('').trigger('change');
                $('#alamat_perusahaan').val('');
                $('#kota_perusahaan').val('');
            }
        });

        // =========================================================================
        // --- SUBMIT FORM VIA AJAX (ANTI DATA KORUP / INTERNET TIDAK STABIL) ---
        // =========================================================================
        $('#formBuatSurat').on('submit', function (e) {
            e.preventDefault();

            // Validasi: minimal ada satu baris siswa
            if ($('#student-list .student-row-group').length === 0) {
                alert('Peringatan: Harap tambahkan minimal satu siswa sebelum membuat surat.');
                return false;
            }

            // Validasi nomor surat dan perihal
            const perihal = $('#perihal').val();
            if (!perihal) {
                alert('Peringatan: Silakan pilih perihal surat.');
                return false;
            }

            const namaPerusahaan = $('#nama_perusahaan').val().trim();
            if (!namaPerusahaan) {
                alert('Peringatan: Silakan isi atau pilih nama tempat PKL / perusahaan.');
                $('#nama_perusahaan').focus();
                return false;
            }

            const $btn = $('#btnSubmitSurat');
            const originalBtnHtml = $btn.html();

            // Kunci tombol submit & tampilkan loading spinner
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Menyimpan Data & Membuat PDF...');

            const formData = new FormData(this);
            formData.append('is_ajax', '1');

            $.ajax({
                url: 'generate_surat.php',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (res) {
                    if (res.status === 'success') {
                        // Pasang nomor surat & URL cetak
                        $('#suksesNoSurat').text(res.no_surat);
                        $('#btnDownloadPdf').attr('href', res.print_url);

                        // Tampilkan modal sukses
                        const modalEl = document.getElementById('modalSuksesSurat');
                        const modalInstance = new bootstrap.Modal(modalEl);
                        modalInstance.show();
                    } else {
                        alert('Gagal Membuat Surat:\n' + (res.message || 'Terjadi kesalahan pada server.'));
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Submit Error:", error, xhr.responseText);
                    let pesan = "Terjadi gangguan koneksi internet atau server.";
                    try {
                        const json = JSON.parse(xhr.responseText);
                        if (json && json.message) pesan = json.message;
                    } catch (ex) { }

                    alert('PERINGATAN GANGGUAN KONEKSI:\n\n' + pesan + '\n\nDemi menjaga integritas data, seluruh proses telah dibatalkan secara otomatis (ROLLBACK). Siswa TIDAK terkunci dan Anda dapat mengulangi proses pengajuan setelah koneksi stabil.');
                },
                complete: function () {
                    $btn.prop('disabled', false).html(originalBtnHtml);
                }
            });
        });

        // Tombol Lihat Data Surat pada Modal
        $('#btnLihatDataSurat').on('click', function () {
            const modalEl = document.getElementById('modalSuksesSurat');
            const modalInstance = bootstrap.Modal.getInstance(modalEl);
            if (modalInstance) modalInstance.hide();
            loadContent('data_surat.php');
        });

        // Tombol Buat Surat Lain pada Modal
        $('#btnBuatSuratLagi').on('click', function () {
            const modalEl = document.getElementById('modalSuksesSurat');
            const modalInstance = bootstrap.Modal.getInstance(modalEl);
            if (modalInstance) modalInstance.hide();
            loadContent('buat_surat.php');
        });

    });
</script>