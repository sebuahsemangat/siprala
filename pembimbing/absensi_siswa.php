<style>
    .info-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        padding: 20px;
        color: white;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        margin-bottom: 24px;
    }

    .info-item {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        padding: 12px 16px;
        border-radius: 8px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        margin-bottom: 10px;
    }

    .card-header-custom {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 15px 20px;
        border-radius: 0.5rem 0.5rem 0 0;
    }
</style>

<div class="row fade-in">
    <div class="col-12">

        <div class="card shadow mb-4">
            <div class="card-header-custom">
                <h5 class="mb-0"><i class="fas fa-search me-2"></i>Pilih Siswa Bimbingan</h5>
            </div>
            <div class="card-body">
                <form id="formPilihSiswa">
                    <div class="row align-items-end">
                        <div class="col-md-9 mb-3 mb-md-0">
                            <label for="pilihSiswa" class="form-label fw-bold">Nama Siswa</label>
                            <select class="form-select" id="pilihSiswa" required>
                                <option value="" selected disabled>Memuat data siswa...</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100" id="btnTampilkan">
                                <i class="fas fa-eye me-2"></i> Tampilkan
                            </button>
                        </div>
                    </div>
                </form>
                <div id="messageArea" class="mt-3"></div>
            </div>
        </div>

        <div id="cardInfoSiswa" style="display:none;">
            <div class="info-card">
                <h5 class="card-title border-bottom pb-2 mb-3 border-white-50">
                    <i class="fas fa-user-circle me-2"></i> Informasi Siswa
                </h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item">
                            <div class="small text-uppercase opacity-75">Kontak Siswa</div>
                            <div class="fw-bold fs-5">
                                <i class="fas fa-mobile-alt me-2"></i>
                                <a href="#" id="kontakSiswaLink" class="text-white text-decoration-none">
                                    <span id="kontakSiswaText">-</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <div class="small text-uppercase opacity-75">Tempat PKL</div>
                            <div class="fw-bold fs-5">
                                <i class="fas fa-building me-2"></i> <span id="tempatPklText">-</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow" id="cardAbsensi" style="display:none;">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0 text-primary" id="judulAbsensi">Riwayat Absensi</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="dataAbsensi" class="table table-striped table-hover w-100">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jam Masuk</th>
                                <th>Status</th>
                                <th>Lokasi</th>
                                <th>Bukti</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    (function() {
        // Menggunakan IIFE (Immediately Invoked Function Expression) agar variabel tidak bocor ke global
        let absensiTable;

        // --- LOAD DATA SISWA SAAT KONTEN DI-RENDER ---
        function loadSiswaBimbingan() {
            $.ajax({
                url: 'get_siswa_bimbingan.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    const selectSiswa = $('#pilihSiswa');
                    selectSiswa.empty();
                    if (response.success && response.data.length > 0) {
                        selectSiswa.append('<option value="" selected disabled>-- Pilih Siswa --</option>');
                        response.data.forEach(siswa => {
                            selectSiswa.append(`<option value="${siswa.id_siswa}">${siswa.nis} - ${siswa.nama_siswa}</option>`);
                        });
                    } else {
                        selectSiswa.append('<option disabled>Tidak ada data siswa</option>');
                    }
                },
                error: () => $('#messageArea').html('<div class="alert alert-danger">Gagal memuat list siswa.</div>')
            });
        }

        // --- INIT DATATABLE ---
        function initializeDataTable(data, namaSiswa) {
            if ($.fn.DataTable.isDataTable('#dataAbsensi')) {
                $('#dataAbsensi').DataTable().destroy();
            }

            absensiTable = $('#dataAbsensi').DataTable({
                responsive: true,
                data: data,
                columns: [{
                        data: 'tanggal_absensi',
                        render: function(data, type, row) {
                            return type === 'display' ? row.tanggal_display : data;
                        }
                    },
                    {
                        data: 'jam_masuk'
                    },
                    {
                        data: 'status',
                        render: (data) => {
                            let color = data === 'Hadir' ? 'success' : (data === 'Sakit' ? 'warning' : 'info');
                            return `<span class="badge bg-${color}">${data}</span>`;
                        }
                    },
                    {
                        data: 'lokasi_masuk',
                        render: (data) => data ? `<a href="https://maps.google.com/?q=${data}" target="_blank" class="btn btn-xs btn-outline-primary"><i class="fas fa-map-pin"></i> Map</a>` : '-'
                    },
                    {
                        data: 'foto_bukti',
                        render: (data) => data ? `<button class="btn btn-sm btn-secondary" onclick="window.open('${data}','_blank')"><i class="fas fa-image"></i> Foto</button>` : '-'
                    }
                ],
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'excel',
                        className: 'btn btn-success btn-sm',
                        title: 'Absensi ' + namaSiswa
                    },
                    {
                        extend: 'pdf',
                        className: 'btn btn-danger btn-sm',
                        title: 'Absensi ' + namaSiswa
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-info btn-sm text-white'
                    }
                ],
                order: [
                    [0, 'desc']
                ]
            });
        }

        // --- LOAD DATA ABSENSI ---
        function loadAbsensiSiswa(idSiswa, namaSiswa) {
            $.ajax({
                url: 'get_absensi_siswa.php',
                type: 'POST',
                data: {
                    id_siswa: idSiswa
                },
                dataType: 'json',
                beforeSend: () => {
                    $('#btnTampilkan').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
                    $('#messageArea').html('');
                },
                success: (response) => {
                    if (response.success) {
                        // Update Info Card
                        if (response.info_siswa) {
                            $('#kontakSiswaText').text(response.info_siswa.kontak_siswa || '-');
                            $('#tempatPklText').text(response.info_siswa.nama_tempat || '-');
                            $('#kontakSiswaLink').attr('href', response.info_siswa.kontak_siswa ? 'tel:' + response.info_siswa.kontak_siswa : '#');
                            $('#cardInfoSiswa').slideDown();
                        }

                        // Update Table
                        if (response.data.length > 0) {
                            $('#judulAbsensi').html(`Riwayat: <strong>${namaSiswa}</strong>`);
                            initializeDataTable(response.data, namaSiswa);
                            $('#cardAbsensi').slideDown();
                        } else {
                            $('#cardAbsensi').slideUp();
                            $('#messageArea').html('<div class="alert alert-warning">Belum ada data absensi.</div>');
                        }
                    } else {
                        $('#messageArea').html(`<div class="alert alert-danger">${response.message}</div>`);
                    }
                },
                error: (xhr) => $('#messageArea').html(`<div class="alert alert-danger">Error: ${xhr.statusText}</div>`),
                complete: () => $('#btnTampilkan').prop('disabled', false).html('<i class="fas fa-eye me-2"></i> Tampilkan')
            });
        }

        // --- EVENT LISTENERS ---
        // Jalankan loadSiswa begitu script ini dimuat
        loadSiswaBimbingan();

        $('#formPilihSiswa').off('submit').on('submit', function(e) {
            e.preventDefault();
            const id = $('#pilihSiswa').val();
            const nama = $('#pilihSiswa option:selected').text();
            if (id) loadAbsensiSiswa(id, nama);
        });

    })();
</script>