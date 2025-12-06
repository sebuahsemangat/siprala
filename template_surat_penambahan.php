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
            text-align: center;
        }

        .tanda-tangan p {
            margin: 2px 0;
            text-indent: 0 !important;
        }

        /* --- LAMPIRAN --- */
        .lampiran {
            page-break-before: always;
            margin-top: 100px;
        }

        .lampiran-header {
            text-align: center;
            margin-bottom: 15px;
        }

        .lampiran-table {
            width: 85%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-left: auto;
            margin-right: auto;
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


    <img src="<?php echo $data_sekolah['kop']; ?>" width="100%">

    <table style="width: 100%; border-collapse: collapse; font-size: 11pt; margin-top: 10px; ">
        <tr>
            <td style="width: 8%; border: none;"></td>
            <td style="width: 12%; border: none;">Nomor</td>
            <td style="width: 1%; border: none;">:</td>
            <td style="border: none;"><?php echo $data_pengajuan['nomor_surat']; ?></td>
        </tr>
        <tr>
            <td style="border: none;"></td>
            <td style="border: none;">Lampiran</td>
            <td style="border: none;">:</td>
            <td style="border: none;">-</td>
        </tr>
        <tr>
            <td style="border: none;"></td>
            <td style="border: none;">Perihal</td>
            <td style="border: none;">:</td>
            <td style="border: none;"><strong><?php echo $data_pengajuan['perihal']; ?></strong></td>
        </tr>

        <tr>
            <td style="border: none;"></td>
            <td style="border: none;"></td>
            <td style="border: none;"></td>
            <td style="border: none;">
                <br>
                Kepada,<br>
                <strong><?php echo $data_perusahaan['yth'] . ' ' . $data_perusahaan['tujuan']; ?></strong><br>
                <?php echo $data_perusahaan['alamat_tujuan']; ?><br>
                Di<br>
                <strong><?php echo $data_perusahaan['kota_tujuan']; ?></strong>
            </td>
        </tr>
        <tr>
            <td style="border: none;"></td>
            <td style="border: none;"></td>
            <td style="border: none;"></td>
            <td style="border: none;">
                <br>
                Dengan hormat,
                <p style="text-align: justify; text-indent: 50px;">
                    Berikut ini kami lampirkan penambahan nama siswa peserta PKL (Praktik Kerja Lapangan) dari surat kami terdahulu dengan nomor <?php echo $data_pengajuan['no_surat_referensi']; ?> tanggal <?php echo $data_pengajuan['tanggal_surat_referensi']; ?> di <?php echo $data_perusahaan['tujuan'];?> yang Bapak/Ibu pimpin dan akan dilaksanakan pada <strong><?php echo $data_pengajuan['tanggal_mulai_pkl']; ?> s.d.
                        <?php echo $data_pengajuan['tanggal_selesai_pkl']; ?></strong>. Adapun tambahan siswa tersebut adalah:
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
                    <br>
                </p>
                <p style="text-align: justify; text-indent: 50px;">Demikian surat lampiran ini, atas perhatian dan kerjasama Bapak/Ibu kami ucapkan terima kasih.</p>
            </td>
        </tr>
    </table>

    <div class="tanda-tangan">
        <p>Sumedang, <?php echo $data_pengajuan['tanggal_surat']; ?></p>
        <p>Kepala Sekolah,</p>
        <div style="height: 60px;">
            <img src="<?php echo $data_sekolah['ttd']; ?>" alt="Tanda Tangan Kepala Sekolah" style="max-height: 50px;">
        </div>
        <p><strong><?php echo $data_sekolah['kepala_sekolah']; ?></strong></p>
    </div>

</body>

</html>