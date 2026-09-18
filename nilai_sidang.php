<?php
// nilai_sidang.php - Halaman Manajemen dan Input Nilai Sidang PKL
// File ini dimuat secara dinamis via AJAX di index.php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['admin_id']) && !isset($_SESSION['logged_in_admin'])) {
    die('<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i> Akses ditolak. Silakan login terlebih dahulu.</div>');
}
?>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0">
            <i class="fas fa-file-signature me-2"></i> Input & Manajemen Nilai Sidang PKL
        </h5>
        <a href="download_excel_nilai.php" class="btn btn-success btn-sm shadow-sm" target="_blank">
            <i class="fas fa-file-excel me-1"></i> Download Rekap Excel
        </a>
    </div>
    <div class="card-body">
        <div class="alert alert-info py-2 px-3 mb-3 d-flex align-items-center">
            <i class="fas fa-info-circle fa-lg me-2"></i>
            <small>
                Nilai Akhir dihitung otomatis berdasarkan rumus: <strong>(Nilai Sekolah + Nilai DU/DI + Nilai Sidang) / 3</strong>.
                Klik tombol <span class="badge bg-warning text-dark"><i class="fas fa-edit"></i> Edit</span> untuk mengubah nilai sidang dan <span class="badge bg-primary"><i class="fas fa-save"></i> Simpan</span> untuk menyimpan perubahan.
            </small>
        </div>

        <div class="table-responsive">
            <table id="tabelNilaiSidang" class="table table-striped table-hover align-middle w-100">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 50px;">No</th>
                        <th style="width: 110px;">Kelas</th>
                        <th>Nama Siswa</th>
                        <th class="text-center" style="width: 120px;">N. Sekolah</th>
                        <th class="text-center" style="width: 120px;">N. DU/DI</th>
                        <th class="text-center" style="width: 140px;">Nilai Sidang</th>
                        <th class="text-center" style="width: 120px;">Nilai Akhir</th>
                        <th class="text-center" style="width: 90px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tbodyNilaiSidang">
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                            Memuat data nilai siswa...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    'use strict';

    let dataTableSidang = null;

    function formatBadgeNilaiAkhir(nilai) {
        if (nilai === null || nilai === undefined || nilai === '') {
            return '<span class="badge bg-secondary">-</span>';
        }
        const n = parseFloat(nilai);
        let bgClass = 'bg-secondary';
        if (n >= 85) bgClass = 'bg-success';
        else if (n >= 75) bgClass = 'bg-primary';
        else if (n >= 60) bgClass = 'bg-warning text-dark';
        else if (n > 0) bgClass = 'bg-danger';

        return `<span class="badge ${bgClass} px-2 py-1 fs-6 fw-normal">${n.toFixed(2)}</span>`;
    }

    function initDataTable() {
        if ($.fn.DataTable.isDataTable('#tabelNilaiSidang')) {
            $('#tabelNilaiSidang').DataTable().destroy();
        }

        dataTableSidang = $('#tabelNilaiSidang').DataTable({
            responsive: true,
            pageLength: 25,
            order: [[1, 'asc'], [2, 'asc']], // Urutkan berdasarkan Kelas lalu Nama
            language: {
                search: "Cari Siswa/Kelas:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ siswa",
                infoEmpty: "Tidak ada data siswa",
                infoFiltered: "(difilter dari _MAX_ total siswa)",
                zeroRecords: "Data siswa tidak ditemukan",
                paginate: {
                    first: "Awal",
                    last: "Akhir",
                    next: "Lanjut",
                    previous: "Kembali"
                }
            },
            columnDefs: [
                { orderable: false, targets: [5, 7] }, // Kolom input dan tombol aksi tidak bisa diurutkan
                { className: "text-center", targets: [0, 3, 4, 5, 6, 7] }
            ]
        });
    }

    function loadDataNilai() {
        $.ajax({
            url: 'ajax/get_all_nilai.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success && Array.isArray(response.data)) {
                    const $tbody = $('#tbodyNilaiSidang');
                    $tbody.empty();

                    if (response.data.length === 0) {
                        $tbody.html('<tr><td colspan="8" class="text-center text-muted py-3">Tidak ada data siswa.</td></tr>');
                        return;
                    }

                    response.data.forEach(function(siswa, index) {
                        const hasNilai = siswa.nilai_sidang !== null && siswa.nilai_sidang !== '';
                        const nSidangVal = hasNilai ? siswa.nilai_sidang : '';

                        const rowHtml = `
                            <tr data-id-siswa="${siswa.id_siswa}">
                                <td class="text-center fw-semibold text-muted">${index + 1}</td>
                                <td><span class="badge bg-light text-dark border">${siswa.kelas || '-'}</span></td>
                                <td>
                                    <strong>${siswa.nama_siswa || ''}</strong>
                                    <div class="small text-muted">NIS: ${siswa.nis || '-'}</div>
                                </td>
                                <td class="text-center text-secondary">${siswa.nilai_pembimbing_sekolah ?? 0}</td>
                                <td class="text-center text-secondary">${siswa.nilai_pembimbing_dudi ?? 0}</td>
                                <td class="text-center">
                                    <span class="nilai-text-sidang fw-bold text-primary ${hasNilai ? '' : 'd-none'}">
                                        ${hasNilai ? siswa.nilai_sidang : '-'}
                                    </span>
                                    <input type="number" 
                                           class="form-control form-control-sm text-center mx-auto nilai-sidang-input ${hasNilai ? 'd-none' : ''}" 
                                           style="max-width: 85px;" 
                                           value="${nSidangVal}" 
                                           min="0" 
                                           max="100" 
                                           step="1"
                                           placeholder="0-100">
                                </td>
                                <td class="text-center nilai-akhir-container">
                                    ${formatBadgeNilaiAkhir(siswa.nilai_akhir)}
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-warning btn-sm btn-edit ${hasNilai ? '' : 'd-none'}" title="Edit Nilai">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-primary btn-sm btn-simpan ${hasNilai ? 'd-none' : ''}" title="Simpan Nilai">
                                        <i class="fas fa-save"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                        $tbody.append(rowHtml);
                    });

                    initDataTable();
                } else {
                    $('#tbodyNilaiSidang').html(`<tr><td colspan="8" class="text-center text-danger py-3">Gagal memuat data: ${response.message || 'Kesalahan format server'}</td></tr>`);
                }
            },
            error: function(xhr, status, error) {
                console.error("Gagal load data nilai:", error);
                $('#tbodyNilaiSidang').html('<tr><td colspan="8" class="text-center text-danger py-3">Terjadi gangguan koneksi saat memuat data nilai siswa.</td></tr>');
            }
        });
    }

    // Delegasi Event Klik Tombol Edit
    $('#tbodyNilaiSidang').on('click', '.btn-edit', function() {
        const $tr = $(this).closest('tr');
        $tr.find('.nilai-text-sidang, .btn-edit').addClass('d-none');
        $tr.find('.nilai-sidang-input, .btn-simpan').removeClass('d-none');
        $tr.find('.nilai-sidang-input').focus().select();
    });

    // Delegasi Event Klik Tombol Simpan
    $('#tbodyNilaiSidang').on('click', '.btn-simpan', function() {
        const $btn = $(this);
        const $tr = $btn.closest('tr');
        const idSiswa = $tr.data('id-siswa');
        const $input = $tr.find('.nilai-sidang-input');
        const nilaiVal = $input.val().trim();

        if (nilaiVal === "") {
            alert("Peringatan: Nilai sidang harus diisi (angka 0 - 100).");
            $input.focus();
            return;
        }

        const numNilai = parseFloat(nilaiVal);
        if (isNaN(numNilai) || numNilai < 0 || numNilai > 100) {
            alert("Peringatan: Nilai sidang harus berada dalam rentang 0 sampai 100.");
            $input.focus();
            return;
        }

        // Jalankan AJAX simpan nilai
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            url: 'ajax/simpan_nilai_sidang.php',
            type: 'POST',
            data: {
                id_siswa: idSiswa,
                nilai_sidang: numNilai
            },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    $tr.find('.nilai-text-sidang').text(numNilai).removeClass('d-none');
                    $tr.find('.nilai-akhir-container').html(formatBadgeNilaiAkhir(res.nilai_akhir));
                    $tr.find('.nilai-sidang-input, .btn-simpan').addClass('d-none');
                    $tr.find('.btn-edit').removeClass('d-none');

                    // Efek visual sukses
                    $tr.addClass('table-success');
                    setTimeout(function() {
                        $tr.removeClass('table-success');
                    }, 1500);
                } else {
                    alert("Gagal Menyimpan: " + (res.message || "Terjadi kesalahan."));
                }
            },
            error: function(xhr, status, error) {
                console.error("Simpan Nilai Error:", error);
                alert("Gangguan Koneksi: Gagal menyimpan nilai sidang. Silakan periksa koneksi Anda.");
            },
            complete: function() {
                $btn.prop('disabled', false).html('<i class="fas fa-save"></i>');
            }
        });
    });

    // Jalankan pemuatan data
    loadDataNilai();
});
</script>
