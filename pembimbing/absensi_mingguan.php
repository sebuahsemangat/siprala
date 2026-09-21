<style>
    .form-label { font-weight: 600; color: #4a5568; }
    .card-header-custom {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white; padding: 15px 20px; border-radius: 0.5rem 0.5rem 0 0;
    }
    .preview-image {
        max-width: 100%; max-height: 200px; border-radius: 8px;
        display: none; margin-top: 10px; border: 2px dashed #cbd5e0;
    }
    /* Badge custom untuk platform */
    .badge-platform { font-size: 0.85em; padding: 6px 10px; }
</style>

<div class="row fade-in">
    <div class="col-12 mb-4">
        <div class="card shadow">
            <div class="card-header-custom click-to-collapse" data-bs-toggle="collapse" data-bs-target="#collapseForm" style="cursor: pointer;">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Input Absensi Mingguan</h5>
                    <i class="fas fa-chevron-down"></i>
                </div>
            </div>
            <div class="collapse show" id="collapseForm">
                <div class="card-body">
                    <div id="alertArea"></div>
                    <form id="formAbsensiMingguan" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="id_tempat" class="form-label">Tempat PKL Binaan</label>
                                    <select class="form-select" name="id_tempat" id="id_tempat" required>
                                        <option value="" selected disabled>Memuat daftar tempat...</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="tanggal_monitoring" class="form-label">Tanggal Monitoring (Read Only)</label>
                                    <input type="date" readonly class="form-control" name="tanggal_monitoring" id="tanggal_monitoring" value="<?= date('Y-m-d') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="platform" class="form-label">Platform Monitoring</label>
                                    <select class="form-select" name="platform" id="platform" required>
                                        <option value="" selected disabled>-- Pilih Platform --</option>
                                        <option value="Whatsapp">Whatsapp Group/Call</option>
                                        <option value="Zoom/Gmeet">Zoom / Google Meet</option>
                                        <option value="Google Classroom">Google Classroom</option>
                                        <option value="Kunjungan Langsung">Kunjungan Langsung (Offline)</option>
                                        <option value="Lainnya">Lainnya...</option>
                                    </select>
                                </div>
                                <div class="mb-3" id="groupPlatformLainnya" style="display: none;">
                                    <label for="platform_lainnya" class="form-label text-primary">Sebutkan Platform Lainnya</label>
                                    <input type="text" class="form-control" name="platform_lainnya" id="platform_lainnya" placeholder="Contoh: Discord, Telegram">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="catatan" class="form-label">Catatan Perkembangan</label>
                            <textarea class="form-control" name="catatan" id="catatan" rows="3" placeholder="Catatan penting mengenai peserta didik..." required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="foto_bukti" class="form-label">Bukti Foto Kegiatan</label>
                            <input class="form-control" type="file" name="foto_bukti" id="foto_bukti" accept="image/*" required>
                            <img id="imgPreview" class="preview-image" src="#" alt="Preview Bukti">
                        </div>

                        <div class="d-flex justify-content-start">
                            <button type="submit" class="btn btn-primary px-4" id="btnSimpan">
                                <i class="fas fa-paper-plane me-2"></i> Kirim Absensi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card shadow">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0 text-primary"><i class="fas fa-history me-2"></i>Riwayat Absensi Mingguan</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tableRiwayat" class="table table-striped table-hover w-100">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Tempat PKL</th>
                                <th>Platform</th>
                                <th>Catatan</th>
                                <th>Bukti</th>
                            </tr>
                        </thead>
                        <tbody>
                            </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    let riwayatTable;

    // --- 1. LOAD DROPDOWN TEMPAT PKL ---
    function loadTempatPkl() {
        $.ajax({
            url: 'get_tempat_pkl.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                const select = $('#id_tempat');
                select.empty();
                if (response.success && response.data.length > 0) {
                    select.append('<option value="" selected disabled>-- Pilih Tempat PKL --</option>');
                    response.data.forEach(item => {
                        select.append(`<option value="${item.id_tempat}">${item.nama_tempat}</option>`);
                    });
                } else {
                    select.append('<option value="" disabled>Tidak ada tempat binaan</option>');
                }
            }
        });
    }

    // --- 2. LOGIKA PLATFORM LAINNYA ---
    $('#platform').change(function() {
        if ($(this).val() === 'Lainnya') {
            $('#groupPlatformLainnya').slideDown();
            $('#platform_lainnya').prop('required', true);
        } else {
            $('#groupPlatformLainnya').slideUp();
            $('#platform_lainnya').prop('required', false).val('');
        }
    });

    // --- 3. PREVIEW GAMBAR ---
    $('#foto_bukti').change(function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => $('#imgPreview').attr('src', e.target.result).show();
            reader.readAsDataURL(file);
        }
    });

    // --- 4. LOAD TABEL RIWAYAT (DATATABLES) ---
    function loadRiwayatMingguan() {
        $.ajax({
            url: 'get_riwayat_mingguan.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if ($.fn.DataTable.isDataTable('#tableRiwayat')) {
                    $('#tableRiwayat').DataTable().destroy();
                }

                riwayatTable = $('#tableRiwayat').DataTable({
                    responsive: true,
                    data: response.data,
                    columns: [
                        { 
                            data: 'tanggal_monitoring',
                            render: function(data, type, row) {
                                return type === 'display' ? row.tanggal_display : data;
                            }
                        },
                        { data: 'nama_tempat' },
                        { 
                            data: null,
                            render: function(data) {
                                let platform = data.platform;
                                if (platform === 'Lainnya' && data.platform_lainnya) {
                                    platform = data.platform_lainnya + ' (Lainnya)';
                                }
                                let badgeColor = platform.includes('Kunjungan') ? 'bg-success' : 'bg-info';
                                return `<span class="badge ${badgeColor} badge-platform">${platform}</span>`;
                            }
                        },
                        { 
                            data: 'catatan',
                            render: function(data) {
                                // Potong teks jika terlalu panjang
                                return data.length > 50 ? data.substr(0, 50) + '...' : data;
                            }
                        },
                        { 
                            data: 'foto_bukti',
                            render: function(data) {
                                // Path gambar dari database adalah relative (uploads/...), perlu tambah ../
                                return data ? `<button class="btn btn-sm btn-secondary" onclick="window.open('../${data}', '_blank')"><i class="fas fa-image"></i> Lihat</button>` : '-';
                            }
                        }
                    ],
                    dom: 'Bfrtip',
                    buttons: [
                        { 
                            extend: 'excel', 
                            text: '<i class="fas fa-file-excel"></i> Export Excel', 
                            className: 'btn btn-success btn-sm me-1',
                            title: 'Laporan_Mingguan_PKL'
                        },
                        { 
                            extend: 'pdf', 
                            text: '<i class="fas fa-file-pdf"></i> Export PDF', 
                            className: 'btn btn-danger btn-sm',
                            title: 'Laporan_Mingguan_PKL'
                        }
                    ],
                    order: [[0, 'desc']], // Urutkan berdasarkan tanggal terbaru
                    language: { url: "../admin/data_table_id.json" }
                });
            }
        });
    }

    // --- 5. HANDLE SUBMIT FORM ---
    $('#formAbsensiMingguan').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        $.ajax({
            url: 'simpan_absensi_mingguan.php',
            type: 'POST',
            data: formData,
            contentType: false, processData: false,
            beforeSend: function() {
                $('#btnSimpan').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Mengirim...');
                $('#alertArea').html('');
            },
            success: function(response) {
                const res = typeof response === 'string' ? JSON.parse(response) : response;
                if (res.success) {
                    $('#alertArea').html(`<div class="alert alert-success alert-dismissible fade show">${res.message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`);
                    $('#formAbsensiMingguan')[0].reset();
                    $('#imgPreview').hide();
                    $('#groupPlatformLainnya').hide();
                    
                    // Refresh tabel otomatis
                    loadRiwayatMingguan();
                } else {
                    $('#alertArea').html(`<div class="alert alert-danger">${res.message}</div>`);
                }
            },
            error: function() {
                $('#alertArea').html(`<div class="alert alert-danger">Terjadi kesalahan sistem.</div>`);
            },
            complete: function() {
                $('#btnSimpan').prop('disabled', false).html('<i class="fas fa-paper-plane me-2"></i> Kirim Laporan');
            }
        });
    });

    // Init Load
    loadTempatPkl();
    loadRiwayatMingguan();

})();
</script>