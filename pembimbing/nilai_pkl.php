<style>
    .card-header-custom {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 15px 20px;
        border-radius: 0.5rem 0.5rem 0 0;
    }
    .table-nilai input[type="number"] {
        width: 80px;
        text-align: center;
    }
</style>

<div class="row fade-in">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header-custom d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-file-invoice me-2"></i>Input Nilai PKL Siswa Bimbingan</h5>
            </div>
            <div class="card-body">
                <div id="messageArea" class="mb-3"></div>
                <div class="table-responsive">
                    <table id="tabelNilaiPkl" class="table table-striped table-hover w-100">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">NIS</th>
                                <th width="30%">Nama Siswa</th>
                                <th width="15%">Kelas</th>
                                <th width="15%">Nilai Pembimbing Sekolah</th>
                                <th width="15%">Nilai Pembimbing DU/DI</th>
                                <th width="5%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyNilaiPkl">
                            <!-- Data akan dimuat via Ajax -->
                            <tr>
                                <td colspan="7" class="text-center">Memuat data...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    let tabelNilai;

    function initializeDataTable() {
        if ($.fn.DataTable.isDataTable('#tabelNilaiPkl')) {
            $('#tabelNilaiPkl').DataTable().destroy();
        }

        tabelNilai = $('#tabelNilaiPkl').DataTable({
            responsive: true,
            pageLength: 25,
            order: [[3, 'asc'], [2, 'asc']],
            language: {
                search: "Cari Siswa:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ siswa",
                paginate: {
                    first: "Awal",
                    last: "Akhir",
                    next: "Lanjut",
                    previous: "Kembali"
                }
            },
            columnDefs: [
                { orderable: false, targets: [4, 5, 6] }
            ]
        });
    }

    function loadNilaiSiswa() {
        $.ajax({
            url: 'get_nilai_pkl.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const tbody = $('#tbodyNilaiPkl');
                    tbody.empty();
                    if (response.data.length > 0) {
                        response.data.forEach((siswa, index) => {
                            const hasNilai = siswa.nilai_pembimbing_sekolah !== null && siswa.nilai_pembimbing_dudi !== null;
                            tbody.append(`
                                <tr data-id-siswa="${siswa.id_siswa}">
                                    <td>${index + 1}</td>
                                    <td>${siswa.nis}</td>
                                    <td>${siswa.nama_siswa}</td>
                                    <td>${siswa.kelas}</td>
                                    <td>
                                        <span class="nilai-text-sekolah ${hasNilai ? '' : 'd-none'}">${siswa.nilai_pembimbing_sekolah || ''}</span>
                                        <input type="number" class="form-control form-control-sm nilai-sekolah ${hasNilai ? 'd-none' : ''}" 
                                               value="${siswa.nilai_pembimbing_sekolah !== null ? siswa.nilai_pembimbing_sekolah : ''}" 
                                               min="0" max="100" placeholder="0-100">
                                    </td>
                                    <td>
                                        <span class="nilai-text-dudi ${hasNilai ? '' : 'd-none'}">${siswa.nilai_pembimbing_dudi || ''}</span>
                                        <input type="number" class="form-control form-control-sm nilai-dudi ${hasNilai ? 'd-none' : ''}" 
                                               value="${siswa.nilai_pembimbing_dudi !== null ? siswa.nilai_pembimbing_dudi : ''}" 
                                               min="0" max="100" placeholder="0-100">
                                    </td>
                                    <td>
                                        <button class="btn btn-warning btn-sm btn-edit ${hasNilai ? '' : 'd-none'}" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-primary btn-sm btn-simpan ${hasNilai ? 'd-none' : ''}" title="Simpan">
                                            <i class="fas fa-save"></i>
                                        </button>
                                    </td>
                                </tr>
                            `);
                        });
                        
                        initializeDataTable();
                        
                        // Handler Edit (Menggunakan delegasi agar jalan di semua halaman DataTable)
                        $('#tbodyNilaiPkl').on('click', '.btn-edit', function() {
                            const tr = $(this).closest('tr');
                            tr.find('.nilai-text-sekolah, .nilai-text-dudi, .btn-edit').addClass('d-none');
                            tr.find('.nilai-sekolah, .nilai-dudi, .btn-simpan').removeClass('d-none');
                            
                            // Sinkronisasi dengan baris parent jika edit dilakukan di mode responsive (HP)
                            if (tr.hasClass('child')) {
                                const parentTr = tr.prev();
                                parentTr.find('.nilai-text-sekolah, .nilai-text-dudi, .btn-edit').addClass('d-none');
                                parentTr.find('.nilai-sekolah, .nilai-dudi, .btn-simpan').removeClass('d-none');
                            }
                        });

                        // Handler simpan (Menggunakan delegasi)
                        $('#tbodyNilaiPkl').on('click', '.btn-simpan', function() {
                            const tr = $(this).closest('tr');
                            
                            // Jika berjalan di mode responsive (HP), data-id-siswa ada di baris parent
                            const parentTr = tr.hasClass('child') ? tr.prev() : tr;
                            const idSiswa = parentTr.data('id-siswa');
                            
                            const nilaiSekolah = tr.find('.nilai-sekolah').val();
                            const nilaiDudi = tr.find('.nilai-dudi').val();
                            const btn = $(this);
                            
                            simpanNilai(idSiswa, nilaiSekolah, nilaiDudi, btn);
                        });
                    } else {
                        tbody.append('<tr><td colspan="7" class="text-center">Tidak ada siswa bimbingan.</td></tr>');
                    }
                } else {
                    $('#messageArea').html(`<div class="alert alert-danger">${response.message}</div>`);
                }
            },
            error: () => $('#messageArea').html('<div class="alert alert-danger">Gagal memuat data nilai.</div>')
        });
    }

    function simpanNilai(idSiswa, nilaiSekolah, nilaiDudi, btn) {
        // Validasi Input Kosong
        if (nilaiSekolah === "" || nilaiDudi === "") {
            alert("Semua inputan nilai harus diisi.");
            return;
        }

        // Validasi range 0-100
        if (nilaiSekolah < 0 || nilaiSekolah > 100 || nilaiDudi < 0 || nilaiDudi > 100) {
            alert("Nilai harus berada di antara 0 sampai 100.");
            return;
        }

        $.ajax({
            url: 'simpan_nilai_pkl.php',
            type: 'POST',
            data: {
                id_siswa: idSiswa,
                nilai_sekolah: nilaiSekolah,
                nilai_dudi: nilaiDudi
            },
            dataType: 'json',
            beforeSend: () => {
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
            },
            success: (response) => {
                if (response.success) {
                    const tr = btn.closest('tr');
                    
                    // Update tampilan ke mode teks
                    tr.find('.nilai-text-sekolah').text(nilaiSekolah).removeClass('d-none');
                    tr.find('.nilai-text-dudi').text(nilaiDudi).removeClass('d-none');
                    tr.find('.nilai-sekolah, .nilai-dudi, .btn-simpan').addClass('d-none');
                    // Perbarui value input juga agar saat edit lagi angkanya tetap ada
                    tr.find('.nilai-sekolah').val(nilaiSekolah);
                    tr.find('.nilai-dudi').val(nilaiDudi);
                    tr.find('.btn-edit').removeClass('d-none');

                    // Beri feedback visual pada baris
                    tr.addClass('table-success');
                    setTimeout(() => tr.removeClass('table-success'), 2000);
                    
                    // Jika simpan dari mode responsive (HP), sinkronisasikan juga ke parent DOM-nya
                    if (tr.hasClass('child')) {
                        const parentTr = tr.prev();
                        parentTr.find('.nilai-text-sekolah').text(nilaiSekolah).removeClass('d-none');
                        parentTr.find('.nilai-text-dudi').text(nilaiDudi).removeClass('d-none');
                        parentTr.find('.nilai-sekolah, .nilai-dudi, .btn-simpan').addClass('d-none');
                        parentTr.find('.nilai-sekolah').val(nilaiSekolah);
                        parentTr.find('.nilai-dudi').val(nilaiDudi);
                        parentTr.find('.btn-edit').removeClass('d-none');
                        
                        parentTr.addClass('table-success');
                        setTimeout(() => parentTr.removeClass('table-success'), 2000);
                    }

                    $('#messageArea').html(`
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i> ${response.message} (Siswa: ${tr.find('td:eq(2)').text()})
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `);
                } else {
                    $('#messageArea').html(`<div class="alert alert-danger">${response.message}</div>`);
                }
            },
            error: (xhr) => $('#messageArea').html(`<div class="alert alert-danger">Error: ${xhr.statusText}</div>`),
            complete: () => btn.prop('disabled', false).html('<i class="fas fa-save"></i>')
        });
    }

    // Load data saat script dimuat
    loadNilaiSiswa();
})();
</script>
