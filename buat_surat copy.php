<?php
// buat_surat.php - MODIFIKASI UNTUK NOMOR SURAT OTOMATIS DAN INPUT DATABASE

include 'koneksi.php'; // Include file koneksi

// --- LOGIKA GENERASI NOMOR SURAT OTOMATIS ---

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

// 2. Hitung Nomor Urut Selanjutnya
$next_number = 1;
$query_count = "SELECT COUNT(*) AS total_surat FROM surat";
$result_count = $koneksi->query($query_count);

if ($result_count && $result_count->num_rows > 0) {
    $row = $result_count->fetch_assoc();
    $next_number = $row['total_surat'] + 1;
}

$nomor_urut_formatted = sprintf("%02d", $next_number);
$nomor_surat_otomatis = $nomor_urut_formatted . $format_nomor_surat;

// 3. Ambil Daftar Tempat PKL untuk Datalist
$tempat_pkl_list = [];
$query_tempat = "SELECT nama_tempat FROM tempat_pkl ORDER BY nama_tempat ASC";
$result_tempat = $koneksi->query($query_tempat);
if ($result_tempat) {
    while ($row = $result_tempat->fetch_assoc()) {
        $tempat_pkl_list[] = $row['nama_tempat'];
    }
}

$koneksi->close(); // Tutup koneksi setelah selesai mengambil data
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi PKL - SMK Informatika Sumedang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container { max-width: 900px; margin-top: 30px; margin-bottom: 50px; }
        .student-row-group { border: 1px solid #ddd; padding: 15px; border-radius: 5px; margin-bottom: 10px; background-color: #f9f9f9; }
        .static-data { background-color: #e9ecef; border: 1px dashed #ccc; padding: 8px; border-radius: 4px; }
        .search-results { max-height: 300px; overflow-y: auto; border: 1px solid #eee; border-radius: 5px; }
        .search-results .list-group-item { cursor: pointer; }
        .search-results .list-group-item:hover { background-color: #f8f9fa; }
    </style>
</head>
<body>

<div class="container">
    <h3 class="mb-4 text-center">Formulir Pembuatan Surat PKL (Otomatis & Database)</h3>
    <hr>
    
    <form action="generate_surat.php" method="POST">
        
        <div style="display: none;">
            <input type="hidden" name="nama_sekolah" value="<?php echo htmlspecialchars($settings['nama_sekolah']); ?>">
            <input type="hidden" name="tgl_mulai" value="<?php echo htmlspecialchars($settings['tgl_mulai']); ?>">
            <input type="hidden" name="tgl_selesai" value="<?php echo htmlspecialchars($settings['tgl_selesai']); ?>">
            <input type="hidden" name="nama_kepsek" value="<?php echo htmlspecialchars($settings['nama_kepsek']); ?>">
            
            <input type="hidden" name="nomor_surat" value="<?php echo $nomor_surat_otomatis; ?>">
            <input type="hidden" name="nomor_urut" value="<?php echo $nomor_urut_formatted; ?>">
        </div>

        <h4 class="mt-5 mb-3 text-primary">I. Data Surat & Tujuan</h4>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">1. Nomor Surat (Otomatis)</label>
                <div class="static-data fw-bold text-danger"><?php echo $nomor_surat_otomatis; ?></div>
            </div>
            <div class="col-md-6">
                <label for="tanggal_surat" class="form-label">2. Tanggal Surat Dibuat</label>
                <input type="date" class="form-control" id="tanggal_surat" name="tanggal_surat" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
        </div>
        
        <div class="row g-3 mt-2">
            <div class="col-md-3">
                <label for="tujuan_departemen" class="form-label">3. Tujuan Yth.</label>
                <select class="form-select" id="tujuan_departemen" name="tujuan_departemen" required>
                    <option value="HRD" selected>HRD</option>
                    <option value="Kepala">Kepala</option>
                    <option value="Ketua">Ketua</option>
                    <option value="Pimpinan">Pimpinan</option>
                </select>
            </div>
            <div class="col-md-9">
                <label for="nama_perusahaan" class="form-label">4. Nama Perusahaan (Ketik untuk mencari/memasukkan baru)</label>
                <input type="text" class="form-control" id="nama_perusahaan" name="nama_perusahaan" list="tempat_pkl_list" placeholder="Contoh: PT. Sawala Inovasi Indonesia" required>
                <datalist id="tempat_pkl_list">
                    <?php foreach ($tempat_pkl_list as $nama_tempat): ?>
                        <option value="<?php echo htmlspecialchars($nama_tempat); ?>">
                    <?php endforeach; ?>
                </datalist>
            </div>
        </div>
        <div class="row g-3 mt-2">
            <div class="col-md-6">
                <label for="alamat_perusahaan" class="form-label">5. Alamat Perusahaan</label>
                <input type="text" class="form-control" id="alamat_perusahaan" name="alamat_perusahaan" required>
            </div>
            <div class="col-md-6">
                <label for="kota_perusahaan" class="form-label">6. Kota Perusahaan</label>
                <input type="text" class="form-control" id="kota_perusahaan" name="kota_perusahaan" required>
            </div>
        </div>


        <h4 class="mt-5 mb-3 text-primary">II. Daftar Siswa (Min. 1 Siswa - Pilih dari Database)</h4>
        <div id="student-list">
            </div>

        <div class="d-flex gap-2 mb-4">
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#searchStudentModal">
                <i class="fas fa-search"></i> Cari & Tambah Siswa
            </button>
            <button type="button" class="btn btn-danger" id="remove-student-btn" disabled>
                <i class="fas fa-minus-circle"></i> Kurangi Siswa Terakhir
            </button>
        </div>
        
        <hr>
        <div class="d-grid">
            <button type="submit" class="btn btn-primary btn-lg">Generate Surat PKL</button>
        </div>
    </form>
</div>

<div class="modal fade" id="searchStudentModal" tabindex="-1" aria-labelledby="searchStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="searchStudentModalLabel">Cari Siswa Berdasarkan NIS/Nama</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="text" class="form-control mb-3" id="studentSearchInput" placeholder="Ketik NIS atau Nama Siswa...">
                <div id="searchResults" class="search-results list-group">
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    const studentList = document.getElementById('student-list');
    const removeStudentBtn = document.getElementById('remove-student-btn');
    const searchInput = document.getElementById('studentSearchInput');
    const searchResults = $('#searchResults');
    let studentCount = 0;
    let selectedStudents = new Set(); // Set untuk melacak id_siswa yang sudah dipilih

    function addStudentRow(id_siswa, nama, kelas, hp) {
        if (selectedStudents.has(id_siswa)) {
            alert("Siswa ini sudah ditambahkan!");
            return;
        }

        studentCount++;
        selectedStudents.add(id_siswa);

        const row = document.createElement('div');
        row.className = 'row g-3 student-row-group';
        row.id = `student-row-${studentCount}`;
        row.dataset.idsiswa = id_siswa;
        
        row.innerHTML = `
            <div class="col-md-1 d-flex align-items-center justify-content-center">
                <strong class="text-center">${studentCount}.</strong>
            </div>
            <div class="col-md-4">
                <label class="form-label">Nama Siswa</label>
                <input type="text" class="form-control" name="siswa[${id_siswa}][nama]" value="${nama}" readonly>
            </div>
            <div class="col-md-3">
                <label class="form-label">Kelas</label>
                <input type="text" class="form-control" name="siswa[${id_siswa}][kelas]" value="${kelas}" readonly>
            </div>
            <div class="col-md-4">
                <label class="form-label">No. Handphone</label>
                <input type="text" class="form-control" name="siswa[${id_siswa}][hp]" value="${hp}" readonly>
                <input type="hidden" name="siswa[${id_siswa}][id]" value="${id_siswa}">
            </div>
        `;
        
        studentList.appendChild(row);
        updateStudentButtons();
        // Tutup modal setelah berhasil menambahkan
        $('#searchStudentModal').modal('hide');
        searchInput.value = ''; // Kosongkan input pencarian
        searchResults.empty(); // Kosongkan hasil
    }

    function removeStudentRow() {
        if (studentCount >= 1) {
            const lastRow = studentList.lastElementChild;
            if (lastRow) {
                const id_siswa = parseInt(lastRow.dataset.idsiswa);
                selectedStudents.delete(id_siswa); // Hapus dari Set
                lastRow.remove();
                studentCount--;
                updateStudentButtons();
                // Perbarui penomoran setelah penghapusan
                const numberElements = studentList.querySelectorAll('.student-row-group strong');
                numberElements.forEach((el, index) => {
                    el.textContent = `${index + 1}.`;
                });
            }
        }
    }

    function updateStudentButtons() {
        removeStudentBtn.disabled = studentCount < 1;
    }

    // Event Listener untuk tombol Kurangi Siswa
    removeStudentBtn.addEventListener('click', removeStudentRow);

    // Default: Siswa tidak ditambahkan secara default. Pengguna harus memilih.
    updateStudentButtons(); 

    // --- AJAX/JQUERY UNTUK PENCARIAN SISWA (Live Search) ---
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        if (query.length < 2) {
            searchResults.empty();
            return;
        }

        $.ajax({
            url: 'search_siswa.php', // FILE BARU YANG HARUS DIBUAT
            method: 'GET',
            data: { query: query },
            dataType: 'json',
            success: function(data) {
                searchResults.empty();
                if (data.length > 0) {
                    data.forEach(siswa => {
                        // Cek apakah siswa sudah dipilih
                        const disabledClass = selectedStudents.has(siswa.id_siswa) ? ' disabled' : '';
                        const alertText = selectedStudents.has(siswa.id_siswa) ? ' (Sudah dipilih)' : ' (Klik untuk tambah)';
                        
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
                    searchResults.html('<div class="alert alert-warning m-0">Siswa tidak ditemukan.</div>');
                }
            }
        });
    });

    // Event Listener untuk memilih siswa dari hasil pencarian
    searchResults.on('click', '.list-group-item:not(.disabled)', function(e) {
        e.preventDefault();
        const id = parseInt($(this).data('id'));
        const nama = $(this).data('nama');
        const kelas = $(this).data('kelas');
        const hp = $(this).data('hp');
        
        addStudentRow(id, nama, kelas, hp);
    });

</script>

</body>
</html>