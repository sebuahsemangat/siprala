<?php
// template_surat.php - VERSI PERBAIKAN
// PASTIKAN TIDAK ADA SPASI/ENTER SEBELUM <?php
// Menggunakan variabel yang didefinisikan di generate_surat.php

function make_address_row($data)
{
    return $data['alamat_sekolah'] . ' Telp. ' . $data['telp_sekolah'] .
        '<br>E-mail : ' . $data['email_sekolah'] . ' Website : ' . $data['website_sekolah'];
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title><?php echo $data_pengajuan['perihal']; ?></title>
    <style>
        @page {
            margin: 1cm;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
        }

        /* --- HEADER (KOP SURAT) --- */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .header-table td {
            border: none;
            padding: 0;
            vertical-align: top;
        }

        .header-table .logo {
            width: 15%;
            text-align: center;
        }

        .header-table .info {
            width: 70%;
            text-align: center;
            padding: 0 10px;
        }

        .info h3 {
            margin: 0;
            font-size: 14pt;
        }

        .info p {
            margin: 2px 0;
            font-size: 10pt;
        }

        .info hr {
            border: 1px solid black;
            margin-top: 5px;
            margin-bottom: 5px;
        }

        /* --- ISI SURAT --- */
        .content {
            margin-top: 15px;
        }

        .content p {
            text-align: justify;
            text-indent: 0.5cm;
            line-height: 1.5;
            margin: 5px 0;
        }

        .no-indent {
            text-indent: 0 !important;
        }

        /* --- TANDA TANGAN --- */
        .tanda-tangan {
            width: 40%;
            float: right;
            margin-top: 30px;
        }

        .tanda-tangan p {
            margin: 2px 0;
            text-indent: 0 !important;
        }

        /* --- LAMPIRAN --- */
        .lampiran {
            page-break-before: always;
        }

        .lampiran-header {
            text-align: center;
            margin-bottom: 15px;
        }

        .lampiran-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .lampiran-table th,
        .lampiran-table td {
            border: 1px solid black;
            padding: 6px;
            font-size: 11pt;
            text-align: center;
        }

        .lampiran-table th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>

    <table class="header-table">
        <tr>
            <!-- LOGO SMK - MENGGUNAKAN BASE64 -->
            <td class="logo">
                <?php if ($data_sekolah['logo_smk']): ?>
                    <img src="<?php echo $data_sekolah['logo_smk']; ?>" width="113">
                <?php endif; ?>
            </td>

            <td class="info">
                <p style="font-size: 12pt; font-weight: bold;">YAYASAN PENDIDIKAN SUMEDANG</p>
                <p style="font-size: 18pt; font-weight: bold;">SEKOLAH MENENGAH KEJURUAN</p>
                <p style="font-size: 14pt; font-weight: bold;"><?php echo $data_sekolah['nama_sekolah']; ?></p>
                <p style="font-size: 10pt; font-weight: bold;">PROGRAM KEAHLIAN:</p>
                <p style="font-size: 9pt; font-weight: bold;">REKAYASA PERANGKAT LUNAK (TERAKREDITASI A) 02.06/275/BAP-SM/SK/X/2016</p>
                <p style="font-size: 9pt; font-weight: bold;">MULTIMEDIA (TERAKREDITASI A) 02.10/275/BAP-SM/SK/X/2016</p>
                <p><?php echo make_address_row($data_sekolah); ?></p>
            </td>

            <!-- LOGO YPS - MENGGUNAKAN BASE64 -->
            <td class="logo">
                <?php if ($data_sekolah['logo_yps']): ?>
                    <img src="<?php echo $data_sekolah['logo_yps']; ?>" width="113">
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td colspan="3" style="width: 100%; height: 4px; background-color: #000;"></td>
        </tr>
        <tr>
            <td colspan="3" style="padding-top: 1px;">
                <div style="width: 100%; height: 1px; background-color: #000;"></div>
            </td>
        </tr>
    </table>

    <table style="width: 50%; border-collapse: collapse; font-size: 11pt;">
        <tr>
            <td style="width: 30%; border: none;">Nomor</td>
            <td style="width: 5%; border: none;">:</td>
            <td style="width: 65%; border: none;"><?php echo $data_pengajuan['nomor_surat']; ?></td>
        </tr>
        <tr>
            <td style="border: none;">Lampiran</td>
            <td style="border: none;">:</td>
            <td style="border: none;"><?php echo $data_pengajuan['lampiran']; ?></td>
        </tr>
        <tr>
            <td style="border: none;">Perihal</td>
            <td style="border: none;">:</td>
            <td style="border: none;"><strong><?php echo $data_pengajuan['perihal']; ?></strong></td>
        </tr>
    </table>

    <div style="margin-top: 20px;">
        <p class="no-indent">Kepada,</p>
        <p class="no-indent"><strong><?php echo $data_perusahaan['yth_tujuan']; ?></strong></p>
        <p class="no-indent"><?php echo $data_perusahaan['alamat_tujuan']; ?></p>
        <p class="no-indent" style="margin-top: -5px;">Di</p>
        <p class="no-indent" style="margin-left: 0.5cm;"><strong><?php echo $data_perusahaan['kota_tujuan']; ?></strong></p>
    </div>

    <div class="content">
        <p class="no-indent">Dengan hormat,</p>
        <p>Sehubungan dengan pelaksanaan Praktik Kerja Lapangan (PKL) di Dunia Usaha, Dunia Industri, dan Dunia Kerja (DUDIKA) yang merupakan salah satu kegiatan yang harus ditempuh oleh siswa-siswi <?php echo $data_sekolah['nama_sekolah']; ?>, maka dengan ini kami sampaikan permohonan izin melaksanakan Praktik Kerja tersebut di Perusahaan / Instansi yang Bapak/Ibu pimpin.</p>
        <p>Adapun pelaksanaan Praktik Kerja Lapangan (PKL) tersebut akan dilaksanakan mulai <strong>tanggal <?php echo $data_pengajuan['tanggal_mulai_pkl']; ?> s.d. <?php echo $data_pengajuan['tanggal_selesai_pkl']; ?></strong>.</p>
        <p>(Data siswa terlampir)</p>
        <p>Besar harapan kami, apabila Bapak/Ibu dapat mengabulkan permohonan kami. Atas perhatian dan kerjasamanya kami sampaikan terima kasih.</p>
    </div>

    <div class="tanda-tangan">
        <p><?php echo $data_perusahaan['kota_tujuan']; ?>, <?php echo $data_pengajuan['tanggal_surat']; ?></p>
        <p>Kepala Sekolah,</p>
        <div style="height: 60px;"></div>
        <p><strong><?php echo $data_sekolah['kepala_sekolah']; ?></strong></p>
    </div>


    <div class="lampiran">
        <div class="lampiran-header">
            <h3>LAMPIRAN SURAT</h3>
            <p>Nomor : <?php echo $data_pengajuan['nomor_surat']; ?></p>
            <p>Perihal : Praktik Kerja Lapangan (PKL)</p>
            <h4 style="margin-top: 15px;">NAMA PESERTA PRAKTIK KERJA LAPANGAN (PKL)</h4>
        </div>

        <table class="lampiran-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No.</th>
                    <th style="width: 35%;">Nama Siswa</th>
                    <th style="width: 30%;">Kelas</th>
                    <th style="width: 30%;">No. Handphone</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1;
                foreach ($data_siswa as $siswa): ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td style="text-align: left;"><?php echo $siswa['nama']; ?></td>
                        <td><?php echo $siswa['kelas']; ?></td>
                        <td><?php echo $siswa['hp']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="tanda-tangan">
            <p><?php echo $data_perusahaan['kota_tujuan']; ?>, <?php echo $data_pengajuan['tanggal_surat']; ?></p>
            <p>Kepala Sekolah,</p>
            <div style="height: 60px;"></div>
            <p><strong><?php echo $data_sekolah['kepala_sekolah']; ?></strong></p>
        </div>

        <div style="clear: both; margin-top: 150px;">
            <h4 style="text-align: center; margin-bottom: 20px;">PERNYATAAN IZIN PENERIMAAN SISWA PRAKTIK KERJA LAPANGAN (PKL)</h4>
            <table style="width: 80%; margin: 0 auto; border: none;">
                <tr>
                    <td style="width: 40%; border: none;">Yang bertanda tangan di bawah ini:</td>
                    <td style="width: 60%; border: none;"></td>
                </tr>
                <tr>
                    <td style="border: none;">Nama</td>
                    <td style="border: none;">: .........................................................................</td>
                </tr>
                <tr>
                    <td style="border: none;">Jabatan</td>
                    <td style="border: none;">: .........................................................................</td>
                </tr>
                <tr>
                    <td style="border: none;">Nama Instansi/DUDI</td>
                    <td style="border: none;">: .........................................................................</td>
                </tr>
                <tr>
                    <td style="border: none;">Alamat</td>
                    <td style="border: none;">: .........................................................................</td>
                </tr>
                <tr>
                    <td style="border: none;"></td>
                    <td style="border: none;"> .........................................................................</td>
                </tr>
            </table>

            <p style="text-align: center; margin-top: 20px; text-indent: 0;">Menyatakan <strong>Memberi / Tidak Memberi *</strong> izin kepada siswa SMK Informatika Sumedang untuk melaksanakan Praktik Kerja Lapangan (PKL) di tempat kami.</p>
            <p style="text-align: center; margin-top: 5px; text-indent: 0;">Jumlah siswa yang diberi izin : ....... Orang.</p>

            <p style="margin-top: 30px; text-align: center; text-indent: 0;">Demikian pernyataan ini dibuat untuk digunakan sebagaimana mestinya.</p>

            <div style="text-align: right; margin-top: 50px;">
                <div style="margin-right: 20%;">
                    <p style="margin: 0;">.......................................................</p>
                    <p style="margin: 0; margin-top: 60px;">......................................................</p>
                </div>
            </div>
            <p style="margin-top: 20px; font-size: 10pt; text-indent: 0;">*Coret yang tidak perlu.</p>
        </div>
    </div>

</body>

</html>