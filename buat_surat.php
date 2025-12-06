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
// Format: 001/PAN-PKL/SMK-IF/YPS/X/2025
$nomor_surat_baru = sprintf('%03d', $nomor_urut_baru) . $format_nomor_surat;

// 4. Ambil Tanggal Hari Ini dalam format YYYY-MM-DD
$tanggal_hari_ini = date('Y-m-d');

$koneksi->close(); // Tutup koneksi setelah selesai mengambil data
?>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i> Form Pembuatan Surat Pengantar PKL</h5>
    </div>
    <div class="card-body container-form">
        <form action="generate_surat.php" method="POST" target="_blank">

            <fieldset class="mb-4 p-3 border rounded">
                <legend class="float-none w-auto px-2 fs-6 text-primary">Informasi Surat</legend>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="nomor_surat" class="form-label">Nomor Surat Otomatis</label>
                        <input type="text" class="form-control" id="nomor_surat_display" value="<?php echo $nomor_surat_baru; ?>" disabled>
                        <input type="hidden" name="nomor_surat" value="<?php echo $nomor_surat_baru; ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="tanggal_surat" class="form-label">Tanggal Surat</label>
                        <input type="date" class="form-control" id="tanggal_surat" name="tanggal_surat" value="<?php echo $tanggal_hari_ini; ?>" required>
                    </div>
                </div>

                <div class="row" style="display:none;">
                    <div class="col-md-6">
                        <label for="tgl_mulai_display" class="form-label">Tanggal Mulai PKL</label>
                        <input type="text" class="form-control" id="tgl_mulai_display" value="<?php echo $settings['tgl_mulai']; ?>" disabled>
                        <input type="hidden" name="tgl_mulai" value="<?php echo $settings['tgl_mulai']; ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="tgl_selesai_display" class="form-label">Tanggal Selesai PKL</label>
                        <input type="text" class="form-control" id="tgl_selesai_display" value="<?php echo $settings['tgl_selesai']; ?>" disabled>
                        <input type="hidden" name="tgl_selesai" value="<?php echo $settings['tgl_selesai']; ?>">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="perihal" class="form-label">Perihal</label>
                        <select name="perihal" id="perihal" class="form-control">
                            <option value="Pengajuan Tempat Praktik Kerja Lapangan (PKL)">Pengajuan Tempat Praktik Kerja Lapangan (PKL)</option>
                            <option value="Penambahan Siswa Praktik Kerja Lapangan (PKL)">Penambahan Siswa Praktik Kerja Lapangan (PKL)</option>
                            <option value="Pembatalan Siswa Praktik Kerja Lapangan (PKL)">Pembatalan Siswa Praktik Kerja Lapangan (PKL)</option>
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
                        <input type="text" class="form-control bg-light" id="display_no_surat_referensi" readonly placeholder="Otomatis terisi...">
                        <input type="hidden" name="no_surat_referensi" id="val_no_surat_referensi">
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label text-danger">Tanggal Surat Sebelumnya</label>
                        <input type="text" class="form-control bg-light" id="display_tanggal_referensi" readonly placeholder="Otomatis terisi...">
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
                    <input type="text" class="form-control" id="nama_perusahaan" name="nama_perusahaan" list="datalistOptions" placeholder="Ketik untuk mencari atau menambahkan perusahaan baru..." required>
                    <datalist id="datalistOptions">
                        </datalist>
                </div>
                <div class="mb-3">
                    <label for="tujuan_departemen" class="form-label">Yth. Tujuan (Contoh: HRD/Pimpinan/Direktur)</label>
                    <input type="text" class="form-control" id="tujuan_departemen" name="tujuan_departemen" value="Pimpinan" required>
                </div>
                <div class="mb-3">
                    <label for="alamat_perusahaan" class="form-label">Alamat Lengkap</label>
                    <textarea class="form-control" id="alamat_perusahaan" name="alamat_perusahaan" rows="2" placeholder="Masukkan alamat lengkap perusahaan" required></textarea>
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
                        <button type="button" class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#studentSearchModal"><i class="fas fa-search"></i> Cari & Tambah Siswa</button>
                    </div>
                    <div class="col-auto ms-auto">
                        <button type="button" class="btn btn-sm btn-danger" id="removeStudentBtn" disabled><i class="fas fa-user-minus"></i> Hapus Siswa Terakhir</button>
                    </div>
                </div>
                
                <div class="student-list-container">
                    <div id="student-list">
                        </div>
                </div>

            </fieldset>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-file-pdf me-2"></i> Generate & Simpan Surat</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="studentSearchModal" tabindex="-1" aria-labelledby="studentSearchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="studentSearchModalLabel">Cari Data Siswa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <input type="text" class="form-control" id="studentSearchInput" placeholder="Ketik NIS atau Nama Siswa (min. 2 karakter)">
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
$(document).ready(function() {
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

    // Load datalist tempat PKL (untuk input manual)
    function loadTempatPkl() {
        $.ajax({
            url: 'get_tempat_pkl.php',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                const datalist = $('#datalistOptions');
                datalist.empty();
                if(data && data.length > 0) {
                    data.forEach(function(nama) {
                        datalist.append(`<option value="${nama}">`);
                    });
                }
            }
        });
    }
    loadTempatPkl();

    // Fungsi Tambah Baris Siswa
    window.addStudentRow = function(id = null, name = '', className = '', phone = '') {
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

        const row = `
            <div class="row student-row-group" id="student-row-${currentId}">
                <div class="col-md-1 d-flex align-items-center justify-content-center"><strong class="text-primary fs-5"></strong>${id !== null ? `<input type="hidden" name="siswa[${id}][id]" value="${id}">` : ''}</div>
                <div class="col-md-3"><label class="form-label">Nama Siswa</label><input type="text" class="form-control" name="siswa[${currentId}][nama]" value="${name}" required></div>
                <div class="col-md-4"><label class="form-label">Kelas</label><input type="text" class="form-control" name="siswa[${currentId}][kelas]" value="${className}" required></div>
                <div class="col-md-3"><label class="form-label">No. Handphone</label><input type="text" class="form-control" name="siswa[${currentId}][hp]" value="${phone}" required></div>
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
        $('#student-list .student-row-group strong').each(function(index) {
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
            success: function(data) {
                searchResults.empty();
                if (data.length > 0) {
                    data.forEach(siswa => {
                        const isSelected = selectedStudents.has(parseInt(siswa.id_siswa));
                        const disabledClass = isSelected ? 'disabled' : '';
                        const item = `<a href="#" class="list-group-item list-group-item-action ${disabledClass}" onclick="addStudentRow(${siswa.id_siswa}, '${siswa.nama_siswa}', '${siswa.kelas}', '${siswa.kontak_siswa}'); return false;"><strong>${siswa.nis} - ${siswa.nama_siswa}</strong><br><small>${siswa.kelas}</small></a>`;
                        searchResults.append(item);
                    });
                } else {
                    searchResults.html('<div class="alert alert-warning m-0">Siswa tidak ditemukan.</div>');
                }
            }
        });
    }

    // Event Listeners Siswa
    $('#removeStudentBtn').on('click', function() {
        $('#student-list .student-row-group').last().find('.remove-custom-btn').click();
    });
    $(document).on('click', '.remove-custom-btn', function() {
        removeStudentRow($(this).data('id'));
    });
    studentSearchInput.on('keyup', function() {
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
            success: function(data) {
                console.log("Data diterima:", data); // DEBUG: Cek console browser (F12)
                referensiDataCache = data;
                populateReferensiDropdown(data);
            },
            error: function(xhr, status, error) {
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

        data.forEach(function(item) {
            // PERUBAHAN DISINI: Tambahkan data-tanggal="${item.tanggal}"
            selectRef.append(`<option value="${item.id_tempat}" 
                data-nosurat="${item.no_surat}" 
                data-nama="${item.nama_tempat}" 
                data-tanggal="${item.tanggal}">
                ${item.nama_tempat} (Surat: ${item.no_surat})
            </option>`);
        });
    }

    // Event Delegate: Gunakan $(document).on agar aman untuk elemen dinamis
    $(document).on('change', '#perihal', function() {
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

    $(document).on('change', '#id_referensi_surat', function() {
        const selectedOption = $(this).find(':selected');
        
        // Ambil data dari atribut
        const noSurat = selectedOption.data('nosurat');
        const namaTempat = selectedOption.data('nama');
        const tanggalSurat = selectedOption.data('tanggal'); // Ambil tanggal
        const idTempat = $(this).val();

        if (idTempat) {
            // Isi Nomor Surat
            $('#display_no_surat_referensi').val(noSurat);
            $('#val_no_surat_referensi').val(noSurat);
            
            // Isi Tanggal Surat (BARU)
            $('#display_tanggal_referensi').val(tanggalSurat);
            $('#val_tanggal_referensi').val(tanggalSurat);

            // Auto fill nama perusahaan
            $('#nama_perusahaan').val(namaTempat);
        } else {
            // Kosongkan jika tidak ada yang dipilih
            $('#display_no_surat_referensi').val('');
            $('#val_no_surat_referensi').val('');
            
            $('#display_tanggal_referensi').val('');
            $('#val_tanggal_referensi').val('');
            
            $('#nama_perusahaan').val('');
        }
    });

});
</script>