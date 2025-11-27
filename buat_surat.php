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

                <div class="row">
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
                        <button type="button" class="btn btn-sm btn-success" id="addStudentBtn"><i class="fas fa-user-plus"></i> Tambah Siswa Manual</button>
                    </div>
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
(function() {
    'use strict';
    
    // Inisialisasi variabel - gunakan window untuk persistence dan mencegah error deklarasi ulang
    if (typeof window.studentCount === 'undefined') {
        window.studentCount = 0;
    }
    if (typeof window.selectedStudents === 'undefined') {
        window.selectedStudents = new Map();
    } else {
        // Reset selectedStudents jika halaman dimuat ulang
        window.selectedStudents.clear();
    }
    
    // Alias untuk kemudahan penggunaan
    let studentCount = window.studentCount;
    const selectedStudents = window.selectedStudents;

    const studentList = document.getElementById('student-list');
    const addStudentBtn = document.getElementById('addStudentBtn');
    const removeStudentBtn = document.getElementById('removeStudentBtn');
    const studentSearchInput = $('#studentSearchInput');
    const searchResults = $('#searchResults');


    // Fungsi untuk memuat data Tempat PKL via AJAX
    function loadTempatPkl() {
        $.ajax({
            url: 'get_tempat_pkl.php',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                const datalist = $('#datalistOptions');
                datalist.empty();
                data.forEach(function(nama) {
                    datalist.append(`<option value="${nama}">`);
                });
            },
            error: function() {
                console.error('Gagal memuat data tempat PKL');
            }
        });
    }

    // Fungsi untuk menambah baris siswa (digunakan manual dan dari hasil search)
    function addStudentRow(id = null, name = '', className = '', phone = '') {
        // Jika siswa sudah ada (dari search), jangan tambahkan
        if (id !== null && selectedStudents.has(id)) {
            alert(`Siswa ${name} sudah ada dalam daftar.`);
            return;
        }

        window.studentCount++;
        studentCount = window.studentCount;
        const currentId = id === null ? `manual_${studentCount}` : id;
        
        // Tandai siswa sudah dipilih (hanya jika ada ID dari DB)
        if (id !== null) {
            selectedStudents.set(id, name);
            window.selectedStudents = selectedStudents;
            // Refresh hasil pencarian di modal
            triggerStudentSearch(); 
        }

        const row = document.createElement('div');
        row.className = 'row student-row-group';
        row.id = `student-row-${currentId}`;
        
        row.innerHTML = `
            <div class="col-md-1 d-flex align-items-center justify-content-center">
                <strong class="text-primary fs-5"></strong>
                ${id !== null ? `<input type="hidden" name="siswa[${id}][id]" value="${id}">` : ''}
            </div>
            <div class="col-md-3">
                <label for="siswa_nama_${currentId}" class="form-label">Nama Siswa</label>
                <input type="text" class="form-control" name="siswa[${currentId}][nama]" value="${name}" required>
            </div>
            <div class="col-md-4">
                <label for="siswa_kelas_${currentId}" class="form-label">Kelas</label>
                <input type="text" class="form-control" name="siswa[${currentId}][kelas]" value="${className}" required>
            </div>
            <div class="col-md-3">
                <label for="siswa_hp_${currentId}" class="form-label">No. Handphone</label>
                <input type="text" class="form-control" name="siswa[${currentId}][hp]" value="${phone}" required>
            </div>
            <div class="col-md-1 d-flex align-items-center justify-content-center">
                <button type="button" class="btn btn-sm btn-danger remove-custom-btn" data-id="${currentId}">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        studentList.appendChild(row);
        updateStudentButtons();

        // Scroll to the bottom of the list
        studentList.scrollTop = studentList.scrollHeight;

        // Tutup modal jika penambahan berhasil dari modal
        if (id !== null) {
            $('#studentSearchModal').modal('hide');
            studentSearchInput.val('');
            searchResults.empty();
        }
    }

    // Fungsi untuk menghapus baris siswa
    function removeStudentRow(rowId) {
        const row = document.getElementById(`student-row-${rowId}`);
        if (row) {
            // Cek apakah ini siswa dari DB (punya ID numerik)
            const dbIdMatch = rowId.toString().match(/^(\d+)$/);
            if (dbIdMatch) {
                const idToRemove = parseInt(dbIdMatch[1]);
                if (selectedStudents.has(idToRemove)) {
                    selectedStudents.delete(idToRemove);
                    window.selectedStudents = selectedStudents;
                    // Refresh hasil pencarian di modal setelah dihapus
                    triggerStudentSearch(); 
                }
            }

            row.remove();
            window.studentCount--;
            studentCount = window.studentCount;
            updateStudentButtons();
        }
    }

    // Fungsi untuk menghapus baris terakhir (via tombol Hapus Siswa Terakhir)
    function removeLastStudentRow() {
        const rows = studentList.querySelectorAll('.student-row-group');
        if (rows.length > 0) {
            const lastRow = rows[rows.length - 1];
            const rowId = lastRow.id.replace('student-row-', '');
            removeStudentRow(rowId);
        }
    }


    function updateStudentButtons() {
        // Tombol 'Hapus Siswa Terakhir' aktif jika ada siswa
        removeStudentBtn.disabled = studentList.childElementCount === 0;
        
        // Memastikan penomoran siswa (1., 2., dst.) tetap benar
        const numberElements = studentList.querySelectorAll('.student-row-group strong');
        numberElements.forEach((el, index) => {
            el.textContent = `${index + 1}.`;
        });
    }

    // Fungsi pemicu pencarian siswa
    // Fungsi pemicu pencarian siswa
    function triggerStudentSearch() {
        const query = studentSearchInput.val();
        if (query.length < 2) {
            searchResults.html('<div class="alert alert-info m-0">Ketik minimal 2 karakter untuk mencari siswa.</div>');
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
                        // Cek apakah siswa sudah dipilih di front-end (Map)
                        const isSelected = selectedStudents.has(parseInt(siswa.id_siswa));
                        // Karena backend sudah memfilter siswa 'pending'/'diterima',
                        // siswa yang ditampilkan di sini dipastikan eligible.
                        const disabledClass = isSelected ? 'disabled' : '';
                        const alertText = isSelected ? ' (Sudah ada di daftar surat)' : ' (Klik untuk tambah)';
                        
                        const item = `<a href="#" class="list-group-item list-group-item-action ${disabledClass}" 
                                         data-id="${siswa.id_siswa}" 
                                         data-nama="${siswa.nama_siswa}" 
                                         data-kelas="${siswa.kelas}" 
                                         data-hp="${siswa.kontak_siswa}">
                                         <strong>${siswa.nis} - ${siswa.nama_siswa}</strong><br>
                                         <small>${siswa.kelas} - HP: ${siswa.kontak_siswa}${alertText}</small>
                                      </a>`;
                        searchResults.append(item);
                    });
                } else {
                    // Pesan Umpan Balik yang Lebih Baik (UI/UX Friendly)
                    searchResults.html('<div class="alert alert-warning m-0">Siswa tidak ditemukan, atau <strong>sedang dalam status pengajuan PKL aktif (Pending/Diterima)</strong>.</div>');
                }
            },
            error: function() {
                searchResults.html('<div class="alert alert-danger m-0">Gagal terhubung ke server pencarian.</div>');
            }
        });
    }

    // --- Event Listeners ---
    
    // Load tempat pkl saat form dimuat
    loadTempatPkl();

    // Event Listener untuk tombol Tambah Siswa Manual
    addStudentBtn.addEventListener('click', () => addStudentRow());
    
    // Event Listener untuk tombol Hapus Siswa Terakhir
    removeStudentBtn.addEventListener('click', removeLastStudentRow);

    // Event Listener untuk tombol Hapus Custom per Baris
    $(studentList).on('click', '.remove-custom-btn', function() {
        const rowId = $(this).data('id');
        removeStudentRow(rowId);
    });

    // Event Listener untuk input pencarian siswa di modal
    studentSearchInput.on('keyup', debounce(triggerStudentSearch, 300));

    // Event Listener untuk memilih siswa dari hasil pencarian (di modal)
    searchResults.on('click', '.list-group-item:not(.disabled)', function(e) {
        e.preventDefault();
        const id = parseInt($(this).data('id'));
        const nama = $(this).data('nama');
        const kelas = $(this).data('kelas');
        const hp = $(this).data('hp');
        
        addStudentRow(id, nama, kelas, hp);
    });
    
    // Utility debounce function
    function debounce(func, delay) {
        let timeout;
        return function(...args) {
            const context = this;
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(context, args), delay);
        };
    }

    // Default: Tambahkan dua siswa awal (opsional, bisa dihapus jika tidak mau ada siswa default)
    // if (studentCount === 0) {
    //     addStudentRow(null, 'Muhammad Aprizal Sanjaya', 'XII-RPL 1', '081313011934');
    //     addStudentRow(null, 'Ripan Nugraha', 'XII-RPL 1', '085861556201');
    // }

    updateStudentButtons(); // Panggil pertama kali
    
})();
</script>