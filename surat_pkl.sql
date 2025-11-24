-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 24, 2025 at 01:59 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `surat_pkl`
--

-- --------------------------------------------------------

--
-- Table structure for table `pembimbing`
--

CREATE TABLE `pembimbing` (
  `id_pembimbing` int(11) NOT NULL,
  `username` varchar(20) NOT NULL,
  `nama_pembimbing` varchar(70) NOT NULL,
  `kontak_pembimbing` varchar(15) NOT NULL,
  `password` varchar(100) NOT NULL,
  `password_status` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pembimbing`
--

INSERT INTO `pembimbing` (`id_pembimbing`, `username`, `nama_pembimbing`, `kontak_pembimbing`, `password`, `password_status`) VALUES
(1, 'tantansm', 'Tantan Srimulyani, M.Si', '081313011077', '$2y$10$kHtcgKUe7TlXfO/sbKVeuOuXaimKWw34tQlEunYSXYWuxhreApSWi', 0),
(2, 'rudi', 'Rudi Iskandar, S.E, M.M', '081321240759', '$2y$10$67Xb.9iQY7mRjJ39/UkdvO7t7WrpbWOPxv0IPqn0jOcD0Jnw6RURe', 0),
(3, 'tini', 'Tini Martini, S.Pd', '082318240234', '$2y$10$kwxBGbiGMB2V1U6CwqZ6S.S1MjxcVOgfftSKIGNJgx4b6TBk8MJ5.', 0),
(4, 'aioke', 'Ai Oke W.T, S.Pt', '082320123683', '$2y$10$9qymCeXnn.LPhoB1eE6YreC/mneoBRSpUQjCv0FQRS0NVILIbtNXi', 0),
(5, 'kiki', 'Kiki arion, S.Pd', '08112355000', '$2y$10$Z0zf/cRYmwjP/q6t5AE2pu2WNsK6Fa/lo0F4ZzS6EO0J96.Uw.E6G', 0),
(6, 'masruri', 'M. Masruri, S.Pd.I., M.Ag', '081214652099', '$2y$10$mbqUahKVjq7nIQospFjsgOdUX20wT/Le3MKQebuPccO4OeQzPIF8y', 0),
(7, 'ani', 'N. Ani Nuraini, S.S', '081220909361', '$2y$10$JCvFWdhG/f8GvhjGj9sg..X8rlIrv8nXXq2IW9QJfuYsfBFhD0GPu', 0),
(8, 'iim', 'Iim Ibrahim AK, S.E', '081223750537', '$2y$10$qPKKtjNU1LvQepbMBPyso.9g.ho00r4LzL5og2jvqzI8UIWzKsaFG', 0),
(9, 'nukki', 'Nukki Yus KY, S.E', '082111149078', '$2y$10$Lbjnf1oWy8h7IPxriVYKMOGcQqtRXNL0ovRBtynwDnsCakHZXpDQi', 0),
(10, 'nani', 'Nani Yulianah, S.Kom', '085222237801', '$2y$10$Uie9m9/JJnyOdhaZ1RkQ9uuAs0ixARud7gl7zqg6iwh8B.vZ2vBoC', 0),
(11, 'tia', 'Tia Patria Ayu, S.S', '081223924872', '$2y$10$U/gFjfDnD68B03ItX5pJ/OkXpamBNmcxEc8hDV.VlJYH.OjSqK24.', 0),
(12, 'acep', 'Acep Vevi Priatna, S.Pd.I', '082317567909', '$2y$10$8DhlW6B0LP.YEzn1cGVbSOKZhWBeuztFrLrK1A7hhBoIesG5YUr6K', 0),
(13, 'yogi', 'Yogi Pratama, S.Kom.', '085721211101', '$2y$10$XJV6BiiLfic50br1l7Vt5e8PLPtcRxcEq0CXA1F2n82QZWMGeD69e', 0),
(14, 'ana', 'Ana Tolirenisa, S.Kom.', '085222898622', '$2y$10$NeGRMMWINWZ0KBVGtEjtBONue1lrY/6zQY5lBvXsncy7lNcUVuWOm', 0),
(15, 'ipah', 'Ipah Pujiawati, S.Kom.', '082118325464', '$2y$10$Eu7Rmsc2Ud433nSUHMygpu5xDVl6vEEsd7P6006GhaKgaGsgCCnuK', 0),
(16, 'nenti', 'Nenti Oktriany, S.S.', '081324270558', '$2y$10$A5jRZpvrLoEQrV.c4WDQgOtIahvdYvh0.3j4PGfIrwnAvJRx6hexy', 0),
(17, 'tantanp', 'Tantan Purnama, S.Kom.', '082216477121', '$2y$10$lGgbm3XkuRFq6vm/yO1uDe/qFQjRmqi.cYY/BxFSnnel9SxYKxcT2', 0),
(18, 'taufik', 'Taufik Permana, S.Kom', '083890913639', '$2y$10$qBtzET2jSsl85JERNFtNnePOg3ytVt6ozeAyzN.uBUuI8qrjv0GTO', 0),
(19, 'rizal', 'Rizal Galuh Gumilang, S.Kom.', '081320216705', '$2y$10$l/VKQfVxMWTBTcy4jpnPcul5RNIYAKUPQwHfjmNY/HqQhkzcK0UpS', 0),
(20, 'fajri', 'Fajri Ahmad Syawaldi, S.Kom', '089510615519', '$2y$10$jawiY9krCFM8dS3qE5FcauVL3d64lNZA84H70DG6Jve/aOP8itoOu', 0),
(21, 'reni', 'Reni Siti Munawaroh, S.Pd', '081395273228', '$2y$10$orFpljpp4UDkTI1v72AFB./OsFKY/pwl2sqwsksBEAhhdS7fdv5L.', 0),
(22, 'ira', 'Ira Septiani, S.Ap', '08156447452', '$2y$10$bwY223bTK.z4HNEsMMnwouRCuyof9NnNp3ua7QyEjTi.xny4Nsne6', 0),
(23, 'otongd', 'Otong Daroji, S.Kom', '085324741883', '$2y$10$Pt3soaUsNrjSxequ7dxZdO8Q9kzEwvTCPSJEmb76YasjMxDEppQqe', 1),
(24, 'wahyudin', 'Wahyudin, S.Kom', '087726163340', '$2y$10$5YqPeLJEM319b0acMnjal.lOSNmNx34Ny2qj9FxgJEAebwOX9rmCq', 0),
(25, 'aprilia', 'Aprilia, S.Pd', '082214383419', '$2y$10$hdkZ43d6ryTu/OHRtMLufONJNXa1rDcuf/FG5H63F9UFRgee0t5hi', 0),
(26, 'lisna', 'Lisna Fujinny, S.Sos', '082240298346', '$2y$10$pBUKSp8ZU/ZPXkwciDgUXuI1tbt5P6jG9f6ZxEv21SoQ6Vtpolyxa', 0),
(27, 'hasan', 'Hasan Widianto, S.Kom', '081223317699', '$2y$10$PsFRWK2DkN8.7wR7rsFAAejM7VUTPGMFDg15OKbCRyC/weiVa2ZKK', 0),
(28, 'atep', 'Atep Suryana, S.Kom', '085320724442', '$2y$10$hCF2gCzb1UBFDkUhhleFsOUrkmOEMIm4aaak9JBmQuCJNhKvTFD/2', 0),
(29, 'nisrina', 'Nisrina Assyifa Aras, S,Kom', '081312863454', '$2y$10$yowN7K1Wp.yaDbarxhMtJeKWVlTAsqgzyBP68WAq.2RCjWCd6lk8C', 0),
(30, 'sitin', 'Siti Nurhayati, S.Kom', '085920364551', '$2y$10$BViyj5RUr9N4jhDajTV3O.86pytpbMm6bijx.xkPPdUncnFtoROZm', 0);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id_settings` int(11) NOT NULL,
  `format_nomor_surat` varchar(50) NOT NULL,
  `nama_sekolah` varchar(100) NOT NULL,
  `tgl_mulai` varchar(50) NOT NULL,
  `tgl_selesai` varchar(50) NOT NULL,
  `nama_kepsek` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id_settings`, `format_nomor_surat`, `nama_sekolah`, `tgl_mulai`, `tgl_selesai`, `nama_kepsek`) VALUES
(1, '/PAN-PKL/SMK-IF/YPS/XI/2025', 'SMK Informatika Sumedang', '04 Mei 2025', '12 Desember 2025', 'Tatang Suryana, S.Ag., M.Pd.');

-- --------------------------------------------------------

--
-- Table structure for table `siswa`
--

CREATE TABLE `siswa` (
  `id_siswa` int(11) NOT NULL,
  `nis` varchar(15) NOT NULL,
  `nama_siswa` varchar(75) NOT NULL,
  `kelas` varchar(10) NOT NULL,
  `kontak_siswa` varchar(15) NOT NULL,
  `id_pembimbing` int(11) NOT NULL,
  `id_tempat` int(11) NOT NULL,
  `password` varchar(255) NOT NULL,
  `password_status` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `siswa`
--

INSERT INTO `siswa` (`id_siswa`, `nis`, `nama_siswa`, `kelas`, `kontak_siswa`, `id_pembimbing`, `id_tempat`, `password`, `password_status`) VALUES
(1, '23241307', 'Nayla Atansa Z', 'XII-RPL 1', '081316629105', 0, 0, '$2y$10$3N/RDaERv75TKiZ32lmlzevMpU3fWdEBgMfamNqK11HIO4fyk5J86', 0),
(2, '23241023', 'Nazila Nur Haida', 'XII-RPL 1', '085318951133', 0, 0, '$2y$10$mPYkRXA2OGJ.wrSvFCRqse4v/D9B0xVkL9y6vp62lngNAHGc7N54.', 0),
(3, '23241031', 'Salsabila Chosyah', 'XII-RPL 1', '082263031014', 0, 0, '$2y$10$osTj09h/IdbGRM0.v2m5bewJceIyxQ8qo9N7HoHgJGPeOuSdSBQSu', 0),
(4, '23241159', 'Kaka Sujana', 'XII-RPL 5', '087711658309', 0, 0, '$2y$10$grG.NXF1Ot0geHB4vU/jg.FtUDnnGxlAbUB64tvMZBurLKnVmwbEi', 0),
(5, '23241261', 'Firasty Shafira I', 'XII-RPL 1', '081770057316', 0, 0, '$2y$10$X4OeP/TU5CQDbQYPuODNG.lfFFFfCcELTgj8LBhh16FWkguIPhC.i', 0),
(6, '23241044', 'Denis Ameraldi', 'XII-RPL 3', '081573399691', 0, 0, '$2y$10$je1fIGnrPlxjWztowQr4/uQZgzT.kgzIc/8Dm0c94tQFjJiu25Rra', 0),
(7, '23241277', 'Resya Setia Putri', 'XII-RPL 1', '081280843609', 0, 0, '$2y$10$Rtl56FI2KJPbwpIDAAx9Wenj5RRE4P5aziKAi93Qlw/25rmn8sxGC', 0),
(8, '23241013', 'Firtriani Khoirunnisa', 'XII-RPL 1', '081313662078', 0, 0, '$2y$10$K9/h5Q9tU6cGWYUfDtsinOq4BZvnAHFrsoTAFJmJGNOW4HT.VvHoG', 0),
(9, '23241358', 'Vio Alvionita Sukandar', 'XII-RPL 1', '083181096907', 0, 0, '$2y$10$.1nJc9mX9I9qO1tEgBJlHOka2zQ.iWrgCfHb.bnFswXHPt3WX/Zqq', 0),
(10, '23241251', 'Ahmad Fahri Pangestu', 'XII-RPL 7', '089657604141', 0, 0, '$2y$10$jPXJkm9b31I2yPRuE.wTGOLqY3A/wJrpX4evfraT0APkHkiql2qL.', 0),
(11, '23241142', 'Tiara Rohmah', 'XII-RPL 9', '081564630038', 0, 0, '$2y$10$QJ3t7uWu/Y4BzfxPYsDoouZijwaSBheEobDL6ZEhl3NgnsGIG47sW', 0),
(12, '23241110', 'Akbar Maulana Saputra', 'XII-RPL 9', '082119601361', 0, 0, '$2y$10$83oNwnMJ.klgOexaZynJRuOBmFVgKSUCizqeAuMteEr31TSXR3q7i', 0),
(13, '23241324', 'Ai Sri Mulyani', 'XII-RPL 6', '081553012411', 0, 0, '$2y$10$waRYYPPMwziChhTehSQAPushkJfnYSQamrYbjLWoiqOZmRq5YLE.u', 0),
(14, '23241344', 'Nayla Yaniar', 'XII-RPL 1', '081546465750', 0, 0, '$2y$10$so3sEkT7ikPiH.3h143Sjec9VUSmZOfafHrO96jlRxSTNpuv3Y60m', 0),
(15, '23241349', 'Reyhan Gustiana', 'XII-RPL 4', '081779902671', 0, 0, '$2y$10$EfhVVNewKpbQsGHQyG3Yu.BmlWB5Yr5LNHr0XRTwmfLP1/lLuHp9m', 0),
(16, '23241202', 'Puspita Zaura Finanda', 'XII-RPL 1', '082129115149', 0, 0, '$2y$10$DbhVODHvGYKeD75Di4WqFuWFO8odELq1DJGjAb3DzR3WMRkT9FvXe', 0),
(17, '23241117', 'Diva Nurmala', 'XII-RPL 3', '081770286499', 0, 0, '$2y$10$xXDZOgGsnx7wS9K88c0VA.M/PsL/OzR3PwFNEcMRfpd4KhS.YE5G6', 0),
(18, '23241338', 'Lina Nur Aulia', 'XII-RPL 1', '081809886097', 0, 0, '$2y$10$u53L2TSesxzt5IqQKLBhheq5Ua.ITD8laV9NOgYsKg9BrarcvXFmK', 0),
(19, '23241170', 'Ranty Anisya', 'XII-RPL 3', '082213163818', 0, 0, '$2y$10$9XTQBwHNp525z7bcY6qPPuxUTazVbbekbkUqjGI8DEEsJn8XXn9PG', 0),
(20, '23241096', 'Nisa Dina Fadila', 'XII-RPL 3', '08882219018', 0, 0, '$2y$10$ejDp.0JTJQCabu9obF7vUue51Tm52//gCPq8h1JzRMDFpp7eCDprC', 0),
(21, '23241264', 'Ipaldi', 'XII-RPL 3', '083122530289', 0, 0, '$2y$10$yWO/psC5SVZ17uFOXJuS9e7TTW6isX0uGPhwiGVomgjOzzG9R9suW', 0),
(22, '23241164', 'Muhammad Kurnia', 'XII-RPL 3', '083898459463', 0, 0, '$2y$10$V8rWMAuIPe8OzNDst8nmmuujW4yMpKLYrpqWkjTq9tgo1GhKFoLAC', 0),
(23, '23241330', 'Dewi Destianty Maulana', 'XII-RPL 3', '081220820295', 0, 0, '$2y$10$YuuuukbMbr6C6nC38bLbI.xzaZ3tqpUEGP.i/ZsiB99sGxKpioTo6', 0),
(24, '23241449', 'Nisail Akmaliyyah', 'XII-DKV 3', '085703127504', 0, 0, '$2y$10$pxJyfpcrO9hsdzyFaaSCIeCxKeh0wrQlTy7BJdpxwFYOAZeg8znuO', 0),
(25, '23241462', 'Zulfa Salsabila', 'XII-DKV 3', '085717664654', 0, 0, '$2y$10$/foiqbJhZ1eY.A6IU/jCu.Cq7/.xeThYvhKJhCfWA1l.IA7VpI0yq', 0),
(26, '23241430', 'Annisa Rahmawati', 'XII-DKV 3', '082240181490', 0, 0, '$2y$10$PSkTYePYlCLXE3yy3TEBoeOnoTAPQyjBEJUHFGzGeEeHxZLl/wDXe', 0),
(27, '23241365', 'Carissa Aurellia R', 'XII-DKV 3', '087810334084', 0, 0, '$2y$10$jsi0nkaAjP19szQvOTzAuOrpVYFalMq3grCf/AqsRT6/E2RZQ8bli', 0),
(28, '23241070', 'Talia Rahmawati', 'XII-RPL 2', '08812038178', 0, 0, '$2y$10$RGfXOCP6H6Qx6HSNa7Mzj.AEeXPY74ZUbVRiQmGADUjLgmsfK3.w.', 0),
(29, '23241326', 'Anisa Suci Fadilah', 'XII-RPL 2', '085759409236', 0, 0, '$2y$10$6sH1nbaP7UNoDUTUvVB.GelIpkP1M3Q6.maODiDxBo/FCZUkIPqfe', 0),
(30, '23241381', 'Pinanditha Bela Nindya Praja', 'XII-DKV 1', '085643324440', 0, 0, '$2y$10$iGwoJyhdA04LMMeU7HpMs.IbYE8C1ptV4wmVw2UMCcKubZDuq1X4G', 0),
(31, '23241178', 'Tina Agustini', 'XII-RPL 8', '085794866474', 0, 0, '$2y$10$7uhZdXPycpCt9kOrcqo3A.7QL/mdwnDJUK6a8XD5UJ8LioqyiREXS', 0),
(32, '23241258', 'Depi Mulyani', 'XII-RPL 8', '085860611219', 0, 0, '$2y$10$lQFtrSfTxTwni3lLhBpvdOpyumRfIPzqNVF7N3oabHYdbpteSkdkq', 0),
(33, '23241263', 'Indri Nuraini', 'XII-RPL 8', '081312326351', 0, 0, '$2y$10$OOiAFvClkIOgztx16dk7IehGT5fofCbDreQv.73HNUB5p2eaS0U3a', 0),
(34, '23241026', 'Raisya Alifa Ayu Andjani', 'XII-RPL 8', '081288627548', 0, 0, '$2y$10$ZLo/80pL2gNssAlJbuiEt.2jRRopzRBHv68Ie0ZbigxJH1sEjtHC.', 0),
(35, '23241161', 'Meylani Penata', 'XII-RPL 6', '0882001933325', 0, 0, '$2y$10$0r0EyhBfL/gyH3L3kO9.h.5Z8s1TiIz9zmutLTUbdkPfwCwSKIOFy', 0),
(36, '23241039', 'Aisya Nurholivah', 'XII-RPL 6', '083189082327', 0, 0, '$2y$10$AEvmYPD58kWOg0w8ytQ3xehYG0Ogi3vsskiNNSN0uyhpaMLl/sIDe', 0),
(37, '23241295', 'Elsa Ameliah', 'XII-RPL 6', '087796859307', 0, 0, '$2y$10$NJa7l.x9uXb2SDT5lHoZ6.hkNGpyDjae6x4NR6R9Bd5lng5almMCO', 0),
(38, '23241189', 'Fadjar Pikri Ayansyah', 'XII-RPL 6', '082120975751', 0, 0, '$2y$10$Hd.Ax8ui5jgimZ841pMCuO5k4c/9zX3CaawQy8tzJvtOzX8/2SWg6', 0),
(39, '23241268', 'Moch. Irvan Hakim', 'XII-RPL 6', '083151687016', 0, 0, '$2y$10$Wn5Y/z0VPlv.YOG8gY3ru.3GCMbNQ1qY6/gVdRIMUKkPmt910bDz.', 0),
(40, '23241088', 'Jelita Kristianti', 'XII-RPL 3', '087711229097', 0, 0, '$2y$10$r5IrPjSI8N0g8G2GUl53cuE4T64C9iZIOnYSf6WwlzUqln02cV9MW', 0),
(41, '23241077', 'Asti Junianti', 'XII-RPL 6', '089504154665', 0, 0, '$2y$10$x/MOgeiCCYhSZjUEBDeyXul/UADPv5Hv8ZHgBOMb.btckzfVKjTkG', 0),
(42, '23241200', 'Natasya Maharani Hidayat', 'XII-RPL3', '081214897335', 0, 0, '$2y$10$713oPb8PAZxZRrCkku2LAuH3WeP150mkS7elk1HZKj5TcD.SMaD0i', 0),
(43, '23241219', 'Cheysa Halimatuzzakiyah', 'XII-RPL3', '082187761862', 0, 0, '$2y$10$B/cn7sNRuHLi2u0HZ3AHWegoi74aIrHriHT8JJqBmbuY2ICUmYSF.', 0),
(44, '23241212', 'Tjahaya Riskiqa Haerani', 'XII-RPL9', '0881023364563', 0, 0, '$2y$10$QQIvzvhLKzM6sPRQdSqjye5J.Tao8v/iSvp2Ad6TbkKqSzTQYd8m6', 0),
(45, '23241016', 'Irvan Sharipudin', 'XII-RPL9', '081222357078', 0, 0, '$2y$10$UrxQcmt0UiOCfeGkkXNW3.MFHBjTS3rTWmrQoBHCHgxxVT.5ItxIi', 0),
(46, '23241278', 'Rizki Agus Gifari', 'XII-RPL 2', '089502735802', 0, 0, '$2y$10$OCIiRa6u97nG9KS3snzhDuTrL4tRT6phK65VhckkY0jfVaCpRkthO', 0),
(47, '23241061', 'Nurlaela', 'XII-RPL 6', '085179614756', 0, 0, '$2y$10$1qCcVzfi065CZCPcil8/u.hNBs/OMANT/UY9uJN8nQ7MXOZQAYKk2', 0),
(48, '23241256', 'Cintia Afriliani', 'XII-RPL 6', '085624065025', 0, 0, '$2y$10$sh3GevYUJpVe3dyvzIa/buKm8i852N8QGrFiUZj71.85lOlxJbQDO', 0),
(49, '23241428', 'Aditya Ramadhan Supriatna', 'XII-DKV 3', '85603118603', 0, 0, '$2y$10$kWy/HACtym3kTCP0oK11CutZ.yY9Sv/BPAxK5ah3cmG/YpDeCGKV6', 0),
(50, '23241359', 'Abdul Hamid', 'XII-DKV 3', '085871127335', 0, 0, '$2y$10$pNnm33tANizJ2KYBDORw/ezzWToWKiAA0xsFWP1SXNg3/7vyOf4d.', 0),
(51, '23241459', 'Teguh Febrian', 'XII-DKV 3', '089526302455', 0, 0, '$2y$10$FYJku/FWuK41HkQCiTr2s.cd3pkJa868CF/xazSYx5a8ustCJxJpm', 0),
(52, '23241367', 'Devin Ramadhan', 'XII-DKV 2', '081563974942', 0, 0, '$2y$10$N44YdtDPpm4yGd2tkBPwfutSs5VPqUtO.eRCmeYV73UqcmIf25AVi', 0),
(53, '23241441', 'Irfan Zaini', 'XII-DKV 1', '083162167968', 0, 0, '$2y$10$BboDgE7tcMx7FlqM4FPgPuRmxhNGbNBpR8F5J0AXdnZwAK/v66kZm', 0),
(54, '23241364', 'Aura Rahmawati Nurfarizki', 'XII-DKV 1', '082119822720', 0, 0, '$2y$10$MDdlIQiz0FfiR9iAAdTxcezaohofs4FNlzveKc2LpP2TmTtcmhcJ.', 0),
(55, '23241408', 'Keysha Azkia Nanda', 'XII-DKV 1', '085759251420', 0, 0, '$2y$10$D.XZ0y9bqqsFZwhHkiafZ.m188mETHoP7QTGBUEtXWpFmXgxLfBW.', 0),
(56, '23241360', 'Adithia Wini Safitry', 'XII-DKV 2', '087726812512', 0, 0, '$2y$10$CLhX3JZWUeURp9M0yO5HoOamrmVji.46hdf6z.fKAJsuF8DnZtjZS', 0),
(57, '23241382', 'Pramitha Fitriani', 'XII-DKV 2', '085695517081', 0, 0, '$2y$10$Tn62xyf2FdAmGsTQMWYqOOj1awy4XcvhL.h130uMW1OfdRA61Uz0K', 0),
(58, '23241319', 'Theo Pranoto', 'XII-RPL 5', '082130108303', 0, 0, '$2y$10$cGcW8MiFvggL9x9aHk97tesRST9CHJsuqjCIsbng/r5kEFbgXbMmK', 0),
(59, '23241163', 'Muhamad Syarizal', 'XII-RPL 9', '085722123018', 0, 0, '$2y$10$YXxmJ.XiTtmgIfsDLbkgxeBlyu6CgaodkeeLOPxZEKr3H3GdfGt.q', 0),
(60, '23241262', 'Handika', 'XII-RPL 1', '085860006059', 0, 0, '$2y$10$i7JhzusK4/8CQ3rcowoJe.2BqU1QV.vSslKUAvjt4yN8kpFlSSKLy', 0),
(61, '23241063', 'Rangga Ubaidillah', 'XII-RPL 5', '08882116311', 0, 0, '$2y$10$Cl5c5h1DpV0eyOkrwNuv1.z0fxZ.Ar3fI5HHmsxMVVqgSx4hxmhR6', 0),
(62, '23241423', 'Shilla Aprily Gustina', 'XII-DKV 1', '085723634543', 0, 0, '$2y$10$rTU4qB/ECslVlmvAh2MGe.bbw1X0rBiXdN.8XFLXf6ai6f5emZMl6', 0),
(63, '23241397', 'Aziiz Ramadhan', 'XII-DKV 1', '083866966723', 0, 0, '$2y$10$2AjB1h5d6kgIgLK6Y9Q0COq78Xydv3TWNBNhDxpv/u7VLouZSg1O.', 0),
(64, '23241432', 'Calista Aurelia', 'XII-DKV 3', '085867266438', 0, 0, '$2y$10$D2aRbfktmCIqXFK7a872ourS2UQ1lby8OHRMUep8atq4K664kxoYy', 0),
(65, '23241126', 'Muhamad Alpian', 'XII-RPL 1', '081223734996', 0, 0, '$2y$10$BXf9cCAwxiShWGtwaxa5POCKyGT7G.LwLqsOWeiTlrRa02q50Fbou', 0),
(66, '23241266', 'Karmila', 'XII-RPL 3', '088802114330', 0, 0, '$2y$10$Mo/R5HCdGT/opLDcvTM5p.D1OD6qmGMrlGSP.X6Wn4WNY86HBrT32', 0),
(67, '23241308', 'Novita Anggraeni', 'XII-RPL 3', '085320352764', 0, 0, '$2y$10$JoiAGuQvpWC99L6eyx3xeuN8OL5DV4X2fels0zXK1WgNvX.ToThZq', 0),
(68, '23241329', 'Dede Rian', 'XII-RPL 6', '081918766523', 0, 0, '$2y$10$rme3MDgxBckaifb4N3Ah6O29Q9QNOLN5fZMuaCt34lmoxeFyNVFzW', 0),
(69, '23241288', 'Alif Sophian', 'XII-RPL 6', '083829905916', 0, 0, '$2y$10$iJbFXK69C1SX1vX2rG2LPOETTYPemCPoR7eycICxMo0KBxCcE67Pe', 0),
(70, '23241235', 'Muhammad Vito Valleriyan', 'XII-RPL 6', '0895303599107', 0, 0, '$2y$10$2N.Ri2On19vwxXuo0OY5E.5fX.x..vIrM7LpzYw.ISXRAvO62oxIS', 0),
(71, '23241135', 'Reffal Septiana Alfarizqi', 'XII-RPL 6', '085722814742', 0, 0, '$2y$10$30XQQivFCqzrcyDMTg.nX.7Ot6QLC96P1Nweg5E6XSieFWOuWzk.S', 0),
(72, '23241220', 'Dede Eka Saepudin', 'XII-RPL 4', '081460906053', 0, 0, '$2y$10$9X8D3SKEoy2YsrKuyff0sO4.U4se3qds8JNmbKasE9weoVbCPAjl2', 0),
(73, '23241004', 'Andre Sutisna Agustin', 'XII-RPL 7', '089655288020', 0, 0, '$2y$10$bhWTSM/Sc/peuDFYAccxKeKgVZrkf4mhlpSH69BOOxIn4TMWl7dWO', 0),
(74, '23241325', 'Andhika Rasya Setiawan', 'XII-RPL 5', '085523792477', 0, 0, '$2y$10$Z3ATTowfQ8v9Zbivlgzsc.h5j5eHgzm79dtMgJ.4iOR0YtL9qsfF2', 0),
(75, '23241046', 'Diwan', 'XII-RPL 7', '089512514739', 0, 0, '$2y$10$TcSTC3Y9ZA0jJ4PLhmxYge7qyngEV7eSFTgMpAv5vT.CAo8wBrf4W', 0),
(76, '23241101', 'Rizky Muhammad Sidiq', 'XII-RPL 7', '089519488413', 0, 0, '$2y$10$XDIE6RiRQlCUY2R6nZWifOMYNlmW0OloVLq0m9T74Kb2YVQitaRu2', 0),
(77, '23241145', 'Afdika Apriansyah', 'XII-RPL 5', '087817555796', 0, 0, '$2y$10$rGIQbPS6LV0VKUOx9gVL1upef7v1TFUqHXD3I9C8oYT72e76AbvFq', 0),
(78, '23241273', 'Novi Nurhuda', 'XII-RPL 4', '085942550848', 0, 0, '$2y$10$w6VflXB5.7ug2KViHJrnvOb/ZAZtt9TZ/6HHIfWF3DDK0Bi9yC7gu', 0),
(79, '23241147', 'Alya Nugrahayu', 'XII-RPL 4', '08577329890', 0, 0, '$2y$10$snGUO1U6Xp50bUUXk.KjQOAr2a3wIDew3S.hL0UdfAWxFXsMT4kXC', 0),
(80, '23241221', 'Denia Lisnawati', 'XII-RPL 4', '085860267110', 0, 0, '$2y$10$N5ZelaHa/V2t1NmGs6gQY.AduF5F6P8yfkarQ6qeEuzt8dXCxIh1O', 0),
(81, '23241158', 'Juliyanti Oktaviani', 'XII-RPL 4', '082002405636', 0, 0, '$2y$10$qW7IZedStju6Ajku8UWRgevbXGDLtSGuSP8CwcU5c8ZxAIOWdAasq', 0),
(82, '23241317', 'Siti Rofikoh Nur Fadilah', 'XII-RPL 4', '08813082428', 0, 0, '$2y$10$IJVG1h4bKjqlKw4P2EMJAuSl5ohTjhivtVNdujfSnXrke/3G2e10y', 0),
(83, '23241322', 'Zulfikar Qaribullah Santosa', 'XII-RPL 8', '085951548617', 0, 0, '$2y$10$iKFJOFxW1XdPgJN7WQM4.O2Tm9Ohj89n.WGXJHcXe/Pk6tB1KmZBK', 0),
(84, '23241301', 'Kurniawan', 'XII-RPL 2', '', 0, 0, '$2y$10$Y5MonQpXiykjaPGRDxhR.u.eEpqVLBBl5v7z3TFgrbGrGm/Ma/KUq', 0),
(85, '23241129', 'Muhamad Rijal Iskandar', 'XII-RPL 2', '', 0, 0, '$2y$10$0c3/1WvekTiqFl8C.93qquKBsUIGI3YeYRwdyAGxLGNUfnQDmWF4y', 0),
(86, '23241037', 'Adis Pradista ', 'XII-RPL 5', '', 0, 0, '$2y$10$iYru1Yg2bV4Talla5Y0On.wRWWcJJ6PTG3jlUeFg9ND2Lrmbr.UcW', 0),
(87, '23241153', 'Exsell Anggara Putra', 'XII-RPL 7', '083166028336', 0, 0, '$2y$10$HfMaEFhs7j304Ry.ZyQl4.veG5Jy8KW42bFPta3LZfzKjDNY8aQMK', 0),
(88, '23241238', 'Putri Inaya Salma Nugraha', 'XII-RPL 7', '085703473745', 0, 0, '$2y$10$bnteUGz06Z1b3kTsU5BUp.ce0k/GYRcyK.4dyc3GCoNGA1K/.uae2', 0),
(89, '23241190', 'Fida Nur Fadillah', 'XII-RPL 5', '085795772250', 0, 0, '$2y$10$fOUTe3KZ1UXr9rc.dU5hKuUAdt6JY06hEteLq0wt22m0h33RbTIz.', 0),
(90, '23241166', 'Najwa Khairunnisa', 'XII-RPL 2', '081915211987', 0, 0, '$2y$10$rCuS1iu/Cie2uqsBand6nuCMMNd2caDkTbFlqzmIlKbpxXqHT8neS', 0),
(91, '23241185', 'Daviansyah', 'XII-RPL 2', '081313670571', 0, 0, '$2y$10$z5LNY1Ob9SfbfeSS2CaEauRL9yq.M1Cr5Tf4Qrpup7kefPvjWrTAS', 0),
(92, '23241041', 'Arisya Nurzahra', 'XII-RPL 4', '085721507668', 0, 0, '$2y$10$7sC8r3E/IM0QLLG4bAv.d.Ap21iUxw6VmBu2Udd4hI4zVDm77TRzm', 0),
(93, '23241152', 'Dwi Intan', 'XII-RPL 7', '081282406081', 0, 0, '$2y$10$mHDzLm8TzfqLj27MVcMA..1vCcD9sowAoa9SfFIvGeXHZ/mvGDnGG', 0),
(94, '23241328', 'Dea fitriyani', 'XII-RPL 4', '085864567252', 0, 0, '$2y$10$riSwhZWVjpIlQXsuYJU7U.MNzR7TbHYQSVTbeSf1lXCcUa5wW42VW', 0),
(95, '23241346', 'Novita Lestari', 'XII-RPL 4', '082115490196', 0, 0, '$2y$10$4wT.JqpvjhScuwN4LEiXQOBKp/rExq49gBk7r0B3LLXlzYRks4hum', 0),
(96, '23241095', 'Nadya Kaporina', 'XII-RPL 4', '081297671601', 0, 0, '$2y$10$sgIW1muNqg6ZcbKhZ2w3feQKsshkjo0nBFnC88b/Xy4UWscFieYC6', 0),
(97, '23241075', 'Akhira Azaning Sukma', 'XII-RPL 6', '087763256014', 0, 0, '$2y$10$B4QPGb9HNzk9IOGsVV96m.wDPVVgLvN1jcYcPchbNX9nlQWh1c8Dm', 0),
(98, '23241402', 'Deti Kurniasih', 'XII-DKV 1', '085759409218', 0, 0, '$2y$10$KJ9byFOiDRb694qlykkb6uzaTEcIqPW2yMdcdSt4rjJ.gkjsiX6pW', 0),
(99, '23241390', 'Siti Maulida Solihat', 'XII-DKV 1', '083105728938', 0, 0, '$2y$10$BjV719olrRfompMmM9zaeOSR3gtzMgkRTgXfwlrgzRaYDZy4q7NgW', 0),
(100, '23241080', 'Deri Zaelani', 'XII-RPL 9', '082218962836', 0, 0, '$2y$10$12GV1rDX.Y.PhcyzEOS1mOEspZwGu2n44OJeDDvwsdH.4k6e4J3uC', 0),
(101, '23241294', 'Dimas Andriyano Putra', 'XII-RPL 9', '085797228301', 0, 0, '$2y$10$mxBYr6QnkQ75j1F4P8jtQuxgmJiEeVvhkELGEYxH/ZgiGN0t1QzNu', 0),
(102, '23241270', 'Muhammad Dani Rohman', 'XII-RPL 9', '0881023202505', 0, 0, '$2y$10$Xa3TPRY/rXnrNC3YMFZUdOXhUpkmjbBIuOQkWZNeCm0bibmh22c.e', 0),
(103, '23241043', 'Dea Rahmawati', 'XII-RPL 9', '081223674266', 0, 0, '$2y$10$v7D4PniOI537SHe/jJdmKO19OCwzmIHanlql3f1AnBRudmRot8XQy', 0),
(104, '23241331', 'Dimas Maulana Syiddiq', 'XII-RPL 4', '08126091228', 0, 0, '$2y$10$nWgYYoSCQi4HW5kynAbOducVD9U2R4Pbsk2i2LJ2SdL.stERuEhSK', 0),
(105, '23241149', 'Bunga Nur Septia Ramadhani', 'XII-RPL 2', '081991201268', 0, 0, '$2y$10$GGTsOWNS89Tefjuarx3YkeOnHPlI0UhwZfzUMaMdIpAYUvD2XCh4m', 0),
(106, '23241177', 'Sulis Tri Yulia Rahma', 'XII-RPL 6', '081770561739', 0, 0, '$2y$10$Bdp46sdlJePZRj63fJuQ2.yiiUuKehDHjLygcPCeTCQ7c4IE75Gx6', 0),
(107, '23241348', 'Raka Putra Pratama', 'XII-RPL 5', '', 0, 0, '$2y$10$G2KBsNUuJT5d1o8E23ds2.JxF2k1BGT.H39VOA.dbSgIb1Y.6HPwe', 0),
(108, '23241318', 'Syafitri Wulandari Juliani H', 'XII-RPL 5', '', 0, 0, '$2y$10$3mi/MvqnbbgfbL76eejL/.Ssv2IQ3nwWoNMkCTdg.HSVi.Qv8jKyq', 0),
(109, '23241215', 'Ahmad Dan\'yal', 'XII-RPL 9', '', 0, 0, '$2y$10$8qmYkwW3O62W9VJ7aQATW.LpyoNEkIzBVyRRKZEGUJAuQSGjifXei', 0),
(110, '23241453', 'Regina Zaliani', 'XII-DKV 1', '087738743817', 0, 0, '$2y$10$loHtm/hxl33dNazGXXrhPOYiWGyabNAga1tVnXKOqQBeq53Oy2RPa', 0),
(111, '23241448', 'Naila Mutiara', 'XII-DKV 1', '083821876833', 0, 0, '$2y$10$oMYOHGHLxWL/sULeyy3W1OvG31gb5CpEYzyiUK3FDUyGIc4m.JR62', 0),
(112, '23241001', 'Achmad Fadilah Maulana', 'XII-RPL 8', '081914589556', 0, 0, '$2y$10$qHFukwnAW1CKtHthb9Kx4uznkpNxB8QxYUH8KtONYw05ne/oiM2Y2', 0),
(113, '23241260', 'Fahmi Fadilatul Ula', 'XII-RPL 8', '089637506628', 0, 0, '$2y$10$CgfHqkGzSeY0tow8hO/KnOkBfF4OO.QGP9NEUWbBr/K52AD51ASS.', 0),
(114, '23241356', 'Syifa Nuraini', 'XII-RPL 7', '082315020710', 0, 0, '$2y$10$Pt1sZVF43akdVA04XxWQLOcDpXXncXHqPi3kv.ryEBGYWav0CpAqu', 0),
(115, '23241195', 'Mimi Nurjanah', 'XII-RPL 7', '081222681866', 0, 0, '$2y$10$5wH0TtT5YGrsIjNMCVRCyuDdABfqjYHXX5GU5T6F2JMstetF.mKpG', 0),
(116, '23241183', 'Ana Fitriyani', 'XII-RPL 2', '081287518917', 0, 0, '$2y$10$Cf2exnMEcuY1Uyb0AQaOW.294z3SydPT/ShkRGv6ObFvnoS9cvgEi', 0),
(117, '23241244', 'Shafira dwi cahyani', 'XII-RPL 9', '085703473730', 0, 0, '$2y$10$Q7eO./se5jDxxqFy04gw9uqdw0zN0YgkOfkMIibxN/xiW8ABRZCSu', 0),
(118, '23241066', 'Rizki Nur Fauzi', 'XII-RPL 9', '081563860725', 0, 0, '$2y$10$ZR60p7pQCGUh8PIGZTE74.Hg632THGaoLCgxHccyauRlK9VdkZoZ6', 0),
(119, '23241042', 'Cenzha Muhamad Sajjad', 'XII-RPL 8', '087732524659', 0, 0, '$2y$10$AhfZKY5D7Wal5ih.HAXfieAet34X/6U5sneZpYT6dJci8oOBLHUGa', 0),
(120, '23241109', 'Aditya Ramdani', 'XII-RPL 9', '083823061421', 0, 0, '$2y$10$QkjUSnp2YGySQjTb5iGk4u2/U8AV2Atz1SBbjsbIQzo8dgdM.66N.', 0),
(121, '23241332', 'Enden Komalasari', 'XII-RPL 6 ', '082295620179', 0, 0, '$2y$10$syO1tyqBy2KQ91z3955I4O8FNJol9AEqEYVeLsXgJ4OMnC45z4Fbi', 0),
(122, '23241071', 'Wilda Fitriani', 'XII-RPL 9', '082112529452', 0, 0, '$2y$10$zO45BO/T5ZjbHDOlQ4fyl.7.wH5uN0AIUc0LXF/L4zzWLC3NSK4py', 0),
(123, '23241120', 'Fikri Padilah', 'XII-RPL 2', '08212083557', 0, 0, '$2y$10$jv92GvGgtN1ldrtsQCjfgOfuvFA5iTATpzT.ppVUUCBKmYRSZbKzi', 0),
(124, '23241029', 'Rhisma Fauziah', 'XII-RPL 9', '083894064976', 0, 0, '$2y$10$sHd2p6ccXtBYzU.xGAwgheLp2k/EOz0EttTURngYwx0TuPOJQfXbq', 0),
(125, '23241036', 'Vivih Tofia', 'XII-RPL 2', '087790274416', 0, 0, '$2y$10$ls67WJTY59WWQURcQmOjJODYSTda4aNuLgQ2R6/HCZJkICe9BArOS', 0),
(126, '23241174', 'Sarah Sabrina', 'XII-RPL 9', '083816593927', 0, 0, '$2y$10$weHrzw/iBPjk5mUQuOcMOucvSDENEfnRznqMVk0eVrRk2zStYtz8e', 0),
(127, '23241414', 'Nayisa Sri Yuniar', 'XII-DKV 1', '081460484128', 0, 0, '$2y$10$0mhLbWtLkujspd/ngze4FebKuvEvW0z9ZB.hjJjzQ6.tOhOHoamVu', 0),
(128, '23241410', 'Mita Rosita', 'XII-DKV 2', '08970378485', 0, 0, '$2y$10$eV3wf4q1uCJzhJk3DtaNnupeVOMAXDCc1nb53WfTGfaJRzJrh9702', 0),
(129, '23241376', 'Meisya Ashifani Maryam', 'XII-DKV 2', '081949941735', 0, 0, '$2y$10$xC5nLjO/H.t8kAPRdW9WIeV9ex606xRpur7VNqZ1cR7IrVXBqrsWy', 0),
(130, '23241388', 'Shayna Nurmarcella', 'XII-DKV 2', '081572836160', 0, 0, '$2y$10$EvCrYaPZZVDPt6cPdnehN.yo2TFkLT88A.aJxDnyzYbK7zFHR0cEG', 0),
(131, '23241311', 'Revana Putri Ramadani', 'XII-RPL 9', '087785650818', 0, 0, '$2y$10$Lp42dG5LpdysUoSh.VnkueBTnnt9Cmus93jXIWYDaZ/OQ2WKThdr2', 0),
(132, '23241289', 'Anisa Nurasyifa Ramadhani', 'XII-RPL 9', '08999284819', 0, 0, '$2y$10$9H7AkwyzEcUmX4EYjg1ap.01WcysiK2xUG83IKDz37VjbKAR3Qrsq', 0),
(133, '23241313', 'Rini Agustini', 'XII-RPL 7', '082315554073', 0, 0, '$2y$10$4K9SEHapk5JHv3VVg4qvX.ZoemA3ZWelN6LtCXBTiixHeSsw2bQ9q', 0),
(134, '23241065', 'Rima Zesika', 'XII-RPL 7', '085864686312', 0, 0, '$2y$10$70A63z8DAsnNjcQ1qU2A8.JuICfRSDyxwRyphVLz7NHyBaBB5zIpm', 0),
(135, '23241141', 'Sri Yayu Siti Wahyuni', 'XII-RPL 7', '081918631475', 0, 0, '$2y$10$ZuJPWfEhiScmzBDsoQcDseT0OB7ocxsQ2cGwxSvXha4olwcwj2tUi', 0),
(136, '23241074', 'Ahmad Rizky Alghoniyu', 'XII-RPL 6', '083808778607', 0, 0, '$2y$10$LOCOfRTnGYgGi2aW0j5qQOUctEZItpAb/KX16bNk2gxUUrD48cAqK', 0),
(137, '23241252', 'Ali Setiana', 'XII-RPL 6', '082120466350', 0, 0, '$2y$10$Ho3LZAYKkzG/pLlHFZhQ0.HuvszzUenJWaIrRxlfY.TM7LOb2HXiK', 0),
(138, '23241097', 'Pasha Fadilah', 'XII-RPL 2', '08996238541', 0, 0, '$2y$10$jsFxkCTtmB2P9Cy13y/tBuvEU4n932e6N.lT/hjsGZyUql2va8doS', 0),
(139, '23241275', 'Raihan Ahmad Maulana', 'XII-RPL 2', '089675319837', 0, 0, '$2y$10$HxBOJeEgLtlkNcHJqzWSqOI4V5iqeg/qgCaSqENrRp71rmvWY7yze', 0),
(140, '23241421', 'Riski Arif Mustofa', 'XII-DKV 2', '083163444157', 0, 0, '$2y$10$ivYtQ215OjLTAtkAdqhTwul4bR6DhiZKHxFtu94Rqsjpg8Tto1V86', 0),
(141, '23241405', 'Faisal Nurdiansyah', 'XII-DKV 2', '081280170276', 0, 0, '$2y$10$sEnAri395HPgy9VT2/ARguCqRh9sX.20z032BZ.uoMdeLWXEd3A0m', 0),
(142, '23241437', 'Fachri Husaeni', 'XII-DKV 1', '085295407626', 0, 0, '$2y$10$/efO1r6mI1fuicfmd0lkRedgeFtbnQyI6J7Z0KaMLlW6oIItlRkbC', 0),
(143, '23241444', 'Muhammad Abdul Malik El Fata', 'XII-DKV 2', '081214932197', 0, 0, '$2y$10$VpdIa1ixdG9spRsGLkIUYe1vJX9iQh7ZBurC5J20lwzgfIokOgSHe', 0),
(144, '23241180', 'Yusup Kemaldi', 'XII-RPL6', '08211823499', 0, 0, '$2y$10$rQCdfk9sqhnwvIT3VR9Z4OCScIsBzXiMeIhwmHud93zOvIdWV4lya', 0),
(145, '23241114', 'Daffa Ahmad Fadhil', 'XII-RPL6', '081935594790', 0, 0, '$2y$10$QtWnaRRBmKg8i3Ag4JPyEeuAtBRFvJFa/Haz.MxaqgVp13lbpkcrC', 0),
(146, '23241209', 'Sulaeman A', 'XII-RPL6', '08310268729', 0, 0, '$2y$10$HbQMI9V77RAIx6Gdr6Wr9uy9cB5tZlSy4vEJdMltjMrkHQysUm3pG', 0),
(147, '23241179', 'Yayah Sulistiawati', 'XII-RPL6', '085559080233', 0, 0, '$2y$10$CYkcOtkrq7I6r2Y8ZbXwNOG8VytcRodUmrOh0w7SGIaeO7WspRzlC', 0),
(148, '23241186', 'Della Nursifa', 'XII-RPL6', '087822439461', 0, 0, '$2y$10$7h7ukrElGB5yroMVtSv6JuG3DWf4VL8GZwRQvSIZBkpMbzyxCqepu', 0),
(149, '23241225', 'Fina Puspita', 'XII-RPL 5', '085794927330', 0, 0, '$2y$10$dExhIXPTFOBqAKUI9JqoJu7vF.UtX8SUvBFWZOohKteF2bboAssGm', 0),
(150, '23241279', 'Rizqa Luthfiyah', 'XII-RPL 5', '082318373438', 0, 0, '$2y$10$EZ2UjSQjNYih.pht6r9jO.Zh5/PNCs3da.0lQKulYAw4bz0rr4hWO', 0),
(151, '23241269', 'Muhamad Fikri Maulana', 'XII-RPL 5', '081952005685', 0, 0, '$2y$10$EvXwSpOD35K/yRqJelpnCO35/gZYGHg5fvl4f9VBk6PM5DdzpjQxC', 0),
(152, '23241271', 'Muhammad Naufal Ramadhan', 'XII-RPL 2', '082128648804', 0, 0, '$2y$10$qxEvPtau/GyW/OZ5PHiv/uMgtkvRCOEhxJIImJbnGzfOPTfagRbOi', 0),
(153, '23241058', 'Nadia Agustina', 'XII-RPL 3', '081222032184', 0, 0, '$2y$10$ec3Yk8ku9V62T9auBLK9xezV1awqyygCTCQxrwdDsGAI568gd10r6', 0),
(154, '23241047', 'Erni Destiani', 'XII-RPL 5', '082219270828', 0, 0, '$2y$10$r6qeAuT8/OxERsBRoCJ6p.XZF3zkPSYFmfi2ePkRLdqJuGWq4jss6', 0),
(155, '23241123', 'Jesika Yuliana Putri', 'XII-RPL 4', '081214776932', 0, 0, '$2y$10$4bAaXRQ.SgH4Vo6DI7J31uLvnqqtHx40ezGZeHnX9iYgHvzmRbp0m', 0),
(156, '23241105', 'Sri Wulan Wahyuni', 'XII-RPL 5', '0882000459116', 0, 0, '$2y$10$VTlm8LIPJekAe0KBfOWMo.kVVbtKfA3sP6PjY.BVb0SptW07OSoXS', 0),
(157, '23241002', 'Ahmad Kamaludin Husni Mubarak', 'XII-RPL 8', '082120469529', 0, 0, '$2y$10$F7kwLktRAPw7Q.GEe7dKpuesKyR3F9pVAV.F/L6H7BWDaX2evsBji', 0),
(158, '23241298', 'Hexsa Muhamad Romdon Al Ahpa', 'XII-RPL 4', '082116774535', 0, 0, '$2y$10$qkJqSaejr4X9N7bN.sb3W.fOs5WwIWe6pWaoVj2fmewpedfS0U6Py', 0),
(159, '23241027', 'Rangga Hadi K', 'XII-RPL 3', '081952505847', 0, 0, '$2y$10$IF9KnOL5lMOn64NX9dKhXum6vUfpI2WyyV0vVfut1mWGWTKx4Us8S', 0),
(160, '23241056', 'MuhamadTazqi Hamdani', 'XII-RPL 3', '083830861642', 0, 0, '$2y$10$9BjAVU8muFyuEzJY4dDNbuHwBi3QG4oVMkLZrH45S6fmkE4CBB9Hi', 0),
(161, '23241399', 'Cahya Kamila', 'XII-DKV 2', '085951553288', 0, 0, '$2y$10$BGazB89Wn2G3Jr96lYVTxOAr57ackT8SshIR5Xb6wbwMIEPbXK4eu', 0),
(162, '23241407', 'Indah Indriani', 'XII-DKV 2', '087810334726', 0, 0, '$2y$10$xoN8gcoyaGHz19dqnwJrbO0ZU/9RBa/q7fbHmmKHfdi2lO0ZGUnPu', 0),
(163, '23241412', 'Muhamad Roihan Mulki', 'XII-DKV 2', '082318488126', 0, 0, '$2y$10$2SCwijst.uwh4cxVqARWJen27cT1e1chksOm.7ihux3K8LC5eVj.K', 0),
(164, '23241377', 'Mohamad Fadhil Dwi Saputra', 'XII-DKV 2', '081221077610', 0, 0, '$2y$10$1FeIlurJrxwAqUlme56K6OIwqi1RryIjB6MWWP323nZqh6Y/aSxzq', 0),
(165, '23241368', 'Dwi Riskita Pauziah', 'XII-DKV 3', '082324485135', 0, 0, '$2y$10$S0gS.O7d8Rsyv9DoIgpyGep7BrJT16lENxXqiasSsutMjGVEFTVn2', 0),
(166, '23241226', 'Haikal Aldi Rizaldi', 'XII-RPL 3', '', 0, 0, '$2y$10$qVy5vslTnP9Gg3uy9TDqQeRuy1tpCWixVFHbrwJf2A4QK32vOwJhO', 0),
(167, '23241409', 'Lyra Rachmatya', 'XII-DKV 2', '085156972253', 0, 0, '$2y$10$3m2sw93qPZ/FZIj8WOVRK.kap5uD3d6J/4T.BVfOmgtwIRU32qJFS', 0),
(168, '23241396', 'Arlyn Aulia Sheftiany Dewi', 'XII-DKV2', '081573123618', 0, 0, '$2y$10$qAgjN8q8OmAuOg.Kdcf9veSihCDSBhbTOtQuhiBbtgSMbukv5GQ2C', 0),
(169, '23241419', 'Ratnasari Dewi', 'XII-DKV3', '081772847691', 0, 0, '$2y$10$TrwQKOro1WzKYLnFCiezh.4UKDlWX0dZb4B1yj9jsPwfHoqH0rESG', 0),
(170, '23241443', 'Maulida Khairunnisa', 'XII-DKV3', '082120746652', 0, 0, '$2y$10$Hsq46rH45YLEc.x6PM12Ze.GYMcRPRbnhM3VlEzZZpYXlo2wXJ.Ua', 0),
(171, '23241446', 'Muhammad Shoby Kamal', 'XII-DKV3', '081223653883', 0, 0, '$2y$10$Nnb6D3.0DKHffCQv2s/5x.zXNREgjUZ87FhlaoLahczk6NLJDIg7.', 0),
(172, '23241451', 'Rahmat Fajari', 'XII-DKV1', '082324149683', 0, 0, '$2y$10$NZh5AX76EMiH87g7MUntduxnZAhUFwtfW/IaT3GtkKJC/tNbW3XrO', 0),
(173, '23241374', 'Liviandra C Anin', 'XII DKV 1', '08122780694', 0, 0, '$2y$10$3Vheywfxkt2n2pdtoEjHH.3V8B4p6FVx0APPuGL/CFte.6cj2gB2a', 0),
(174, '23241458', 'Shilva Nur Hikmah', 'XII DKV 3', '083857202610', 0, 0, '$2y$10$6hWbhJgOd2BwHt4OSxeRq.SsehdHMfXVurM4pjUwNZsW3hezpXWx6', 0),
(175, '23241460', 'Tiara Sri Handayani', 'XII-DKV 2', '083120681913', 0, 0, '$2y$10$KcSUFaC.G800JU.A.Jvg3ewR4yIWIgTq4vq5pwyBd2ekrE/N0gsxi', 0),
(176, '23241404', 'Faina Desfiana', 'XII-DKV 2', '082116018120', 0, 0, '$2y$10$lGBGXoSsZ85MchZglFqUau5bxOfIZSp6a6bnIGzMqRw7Wp4fUifCW', 0),
(177, '23241442', 'Keyzia Fatia Rany', 'XII-DKV 2', '085793401548', 0, 0, '$2y$10$pMNY3AUc93BLFM1P134EPOAMbsDSZASgPRRJO40RKpKgUmdVr2ttK', 0),
(178, '23241420', 'Rida A\'Dawiyah', 'XII-DKV 1', '081564679830', 0, 0, '$2y$10$nRWz.WYbRNekVZXJG8LPmOloDF9x5tQr4cHCqFAYv3u2aCE25YU2O', 0),
(179, '23241392', 'Yeti Safitri', 'XII-DKV 1', '0895610432360', 0, 0, '$2y$10$8q.bI.evMJWAhR6gKdyCDeVnKBeTn9woaVPr/vy9nM/P1TQT4SSJS', 0),
(180, '23241187', 'Della Puspita Sari', 'XII-RPL 4', '087861980682', 0, 0, '$2y$10$iEphkjk7EukIFHCOWGhZxuLVpHkrgcvPF6T4aZalkXrWoSo4I2MR2', 0),
(181, '23241254', 'Ani Febriani', 'XII-RPL 2', '081252328795', 0, 0, '$2y$10$Jea3DBVqKhdOy2MLwv5.x.jn5sqSV4Qij/hwLxoqE.aJPQXclJ9Yu', 0),
(182, '23241106', 'Thea Putri Ananda', 'XII-RPL 8', '08882219020', 0, 0, '$2y$10$jgmd8s9RayBYZl3V3pAiGuXMVA7xlBuLBjLYZ6gCVQJNK7IXptX0q', 0),
(183, '23241315', 'Rosy Putriyansyah', 'XII-RPL 3', '081930627527', 0, 0, '$2y$10$aMSUmR.PVByJiTcXzsCZ3eNVQHmqzk1J/yTSUbHI3Ls2FnVxPE7Um', 0),
(184, '23241245', 'Siti Rahayu', 'XII-RPL 8', '082115287393', 0, 0, '$2y$10$xmM7sbC2PwJLDINgBaU.BuoSBHqcwlJfRlZ1g/j6FxZy2/UruLyZq', 0),
(185, '23241240', 'Rendi Rizkiyansyah', 'XII-RPL 5', '085860293128', 0, 0, '$2y$10$E1R93gEf2xkWEqRS4ytF2.pRZMYmHFXep.6aFN3MGGFIedanPCIca', 0),
(186, '23241157', 'Indra Arifin', 'XII-RPL 3', '085759900384', 0, 0, '$2y$10$6EsSfdH/EOR/Hxk5fm54HeXUdaYHxKpMWgkX.R2JGj9LGBswe4MUq', 0),
(187, '23241138', 'Rizki Putra Ramadhan', 'XII-RPL 5', '087720955834', 0, 0, '$2y$10$.BlZzRJjSWMNg1wdEP8qJ.A/16T77djAa85Zmz07fcdcvIKcQ3Cya', 0),
(188, '23241092', 'Muhamad Agus Prasetyo', 'XII-RPL 5', '089529912798', 0, 0, '$2y$10$gKDe.I4n0Awe13bxEsJDvekBYfzjHFoE/CjYb4koN29.r7.F7FNAq', 0),
(189, '23241076', 'Apin Aditya Suhendi', 'XII_RPL 5', '081214657265', 0, 0, '$2y$10$lQvcCVRWAG6RAWM/Fx7euuBH2RpW206zGlFD0K6VNwB7KZg8Ej.7W', 0),
(190, '23241233', 'Muhamad Azril Renanda Putra', 'XII-RPL 3', '085860609837', 0, 0, '$2y$10$ahwewx5Ph4xF2BSWeuwSZeSGA/356BG5AJrg/xfeF49eiv9Wt8oDu', 0),
(191, '23241231', 'M Riyadh Daffa Jalasena', 'XII-RPL 3', '085930426721', 0, 0, '$2y$10$wEK79y/q2O.r1hohJRnhWuhrMbAkHeR9ILHLLhyKFxQHPnauypzNK', 0),
(192, '23241124', 'Kevin Harli Pratama', 'XII-RPL 8', '085931480047', 0, 0, '$2y$10$nd5.GyOC7EyG/hacBR48c.TyCKjHtgM8T.0PeicCvna8GqTlSLKEa', 0),
(193, '23241211', 'Syifa Nur Fadillah', 'XII-RPL 2', '085942974010', 0, 0, '$2y$10$8BXlwqyAs2bsLRx.MIfWcuqJYkdm0RGffXJ/qZcu3jHJ4ovF.jhXm', 0),
(194, '23241353', 'Rubi Prihatini', 'XII-RPL 2', '081542612732', 0, 0, '$2y$10$a6F8Qo98xyOCgeQWIBbXGOia1wcrxZwgpxnpp4wNYDxPkU1KZjokS', 0),
(195, '23241342', 'Muhammad Fahrizal', 'XII-RPL 2', '081808379302', 0, 0, '$2y$10$vpDajNsEnPP/ENIgp1Er3.Kn004RGXc80YLJc39kHzyFshqXcjbSS', 0),
(196, '23241057', 'Muhammad Irfan L', 'XII-RPL 2', '085293139224', 0, 0, '$2y$10$KWIkfEHQemzehk4.v.fsvebe53t.sPVsvYU.8MKGB4Qh2XWoDOakG', 0),
(197, '23241228', 'Insan kamil R', 'XII-RPL 2', '085187290174', 0, 0, '$2y$10$pP.BADFrZwS1ZvwzeUxrXeBD0aV18boWYtoEfAq.ViYKwZBVGAmKC', 0),
(198, '23242254', 'Febby Nur Padila', 'XII-RPL 9', '082126024977', 0, 0, '$2y$10$AaFTeeohGUFW5HnSdUCOU.WHriKnGg90nrFKFs/qjWmJj3/iO4d5e', 0),
(199, '23241274', 'Putri Maharani', 'XII-RPL 5', '081573798237', 0, 0, '$2y$10$ZKzig/0VO6lE2K9J8EZ3Z.ZboyNQm4ZrWCzsSvxVkwacQ1BAB0ESy', 0),
(200, '23241310', 'Raka Aldiansyah', 'XII-RPL 3', '081914589519', 0, 0, '$2y$10$s1Bpk.kdYEapDdDecca1pOOMY/g711uQR14kOGY.MUfoYRjPnsf8i', 0),
(201, '23241018', 'Moh. Ihsan Julian Al-Sidik', 'XII-RPL 1', '085777429733', 0, 0, '$2y$10$RIpNIYpmKKSuF4ZyklPokeqfIOReF5MpXiTzSAVi/iMFNqEiX/8u.', 0),
(202, '23241341', 'Muhammad Alfiil', 'XII-RPL 3', '', 0, 0, '$2y$10$mQFUzRNdWbSbCAfN68gWFOV9Ogvlf5PSXl9e2pRYtrfFUYoZaOSPO', 0),
(203, '23241198', 'Muhammad Rehan Fahruzi', 'XII-RPL 9', '081572010095', 0, 0, '$2y$10$LwxpWU72ksgRdRiT8x1FaeQ5tZMPPUkTaWRDr3jWzH3nwt5IqCzy2', 0),
(204, '23241230', 'Kristian', 'XII-RPL 5', '085965941583', 0, 0, '$2y$10$KxRSX5heCu9e0maPoRTvO.JMuw8KRHrtSL08ZDhAiwugTeAZpIdu2', 0),
(205, '23241102', 'Sanya Dwi Novia', 'XII-RPL 7', '082128224489', 0, 0, '$2y$10$Qm6leBxOLVLgzmACvWxsWe4.iq2RVmQCUBNnk7JZUOnWkIrxPZfSq', 0),
(206, '23241084', 'Fajar Fadilah', 'XII-RPL 1', '081952687279', 0, 0, '$2y$10$oYU2PqFKZyONeqp311WOcun61Y1djcFT5cswMR1S185Lan/BCaoua', 0),
(207, '23241239', 'Rafli Nurvizal', 'XII-RPL 1', '085720752505', 0, 0, '$2y$10$mGhUo.DBQjFYzS.sRgwEpeQPTMm1qvp5m5fl5hrtpZjDcM.6o8Hbm', 0),
(208, '23241165', 'Muhammad Reyno Permana', 'XII-RPL 1', '082119807458', 0, 0, '$2y$10$B1CQ4krCIq4sNFptcGgmAu5mtC54lonXkHboulpjfZDSPB.A9smZe', 0),
(209, '23241280', 'Sherina Cinta Derisa', 'XII-RPL9', '081901268756', 0, 3, '$2y$10$DBGNR/x1lcmx90.pgTPDzuQ4/O98KN0/o9Dx.IxzRGIJMBToyvNa6', 0),
(210, '23241104', 'Siti Zaqiah', 'XII-RPL9', '083138776696', 0, 0, '$2y$10$AcWsS50DdjEe5pOSaQEMmejRxLIFNHTsO2WFogJ0t8P/2Mh2MsTtC', 0),
(211, '23241416', 'Rahadian Arsy', 'XII-DKV 2', '081214659208', 0, 0, '$2y$10$gWkFTNnWWhWTSgshIhxnHOUkyyKQs69nWjIXAz.45FS9exvLGn56G', 0),
(212, '24252001', 'Eka Prasetya', 'XII-DKV 1', '081297612535', 0, 3, '$2y$10$TRLroP4Db63ATqaOhutX8e4epTfqWtb999Zfs8IfdpjvKd5ZrjOHa', 0),
(213, '23241363', 'Arina Alfatihah', 'XII-DKV 3', '082130715009', 0, 0, '$2y$10$/yRBHVlZ5pv0s.ppAuzMG.O7XKktHY2TugYE1kcb.ieQFfHQnXT6C', 0),
(214, '23241372', 'Iklima Dwi Rohmah', 'XII-DKV 3', '082119949002', 0, 0, '$2y$10$8bga78UCB7kNhUAjR6FRnegHm27PRTkJJu3jTuxC4R.ARYk1JrWSu', 0),
(215, '23241366', 'Dealova Dwi Agustin', 'XII-DKV 3', '085156380088', 0, 0, '$2y$10$iK20pWsTMrzuCH/0a3Iose3yFLdPixmf8jGqPTESuzhB4z3vJpMX6', 0),
(216, '23241389', 'Sigit Riyana', 'XII-DKV 1', '088218244607', 0, 0, '$2y$10$HiypeNqFGYaRnOHKi8u/C.wVsp80BI6O7lFOEArXFlF7qqGf9FTSu', 0),
(217, '23241406', 'Habib Aziz Faturrahman', 'XII-DKV 1', '087824468292', 0, 0, '$2y$10$OjRzvR25O9hZs0tJhiycUO8XBjvKTJXa1rohIHW79E7ZBKCeMJiVy', 0),
(218, '23241411', 'Mohamad Ridwan Hanapi', 'XII-DKV 1', '085219215861', 0, 0, '$2y$10$MufJ/57C7TR0aRYcKOdtBejp/xYV2XdyMFc2ckv5RO.G4eB0jOeqm', 0),
(219, '23241445', 'Muhammad Arya Rofik Nurzaman', 'XII-DKV 2', '0895327582678', 0, 0, '$2y$10$KigzIvOfMr6j8GQIB/rd1ObQVFUc1N/6Popvt3jsjpImZmxs/o/Bm', 0),
(220, '23241387', 'Riza Triani Mufidah', 'XII-DKV 3', '085713899059', 0, 0, '$2y$10$yjMyy5B.Ot6Y1Hg65nCTyOVNWY6O.xPHxTkc/AJM6Dv9405Ba3tiC', 0),
(221, '23241401', 'Dede Lidya', 'XII-DKV 3', '081288511348', 0, 0, '$2y$10$jkAUp3D/8vsWdDHBzV7wg.eQbY3/XLkJGAiptN.GXhyRVo5bq4glW', 0),
(222, '23241243', 'Rizky Swaibatul Aslamiyah', 'XII-RPL 6', '08128085112', 0, 0, '$2y$10$NAzc7lEUqUTMqSTFWa7wiOdu3bczAnnNX6NDDl/0FawAw9MPx9Zom', 0),
(223, '23241236', 'Natasya Ramadhani', 'XII-RPL 6', '085813352905', 0, 0, '$2y$10$tGHGItnvFGZZQNZmIOggHeGNWQbnK4LybGWwk5QoRAaBXSt2b2jeK', 0),
(224, '23241321', 'Zihan Ganesah Nur Ramadani', 'XII-RPL 6', '0895388825070', 0, 0, '$2y$10$YCNXappO78b8/Q1izrTLmOz4ZAUeKg/ZXpUBYz7foT6AjZyIAc9dq', 0),
(225, '23241206', 'Risma Rahayu', 'XII-RPL 6', '082113534294', 0, 0, '$2y$10$J093vs8Iuez/sgn7SmB4WufsvGa8uS1WEKwMZiBsTNYmcrORv89Eu', 0),
(226, '23241035', 'Ufas Amzani', 'XII-RPL 2', '085943535162', 0, 0, '$2y$10$Io.VO8VRo0Tf5v4v9gRj9u6mOOe0QW32mVyNRQXi87BVN4rbB1Asq', 0),
(227, '23241287', 'Ahmad Faqo Kulla Nugraha', 'XII-RPL 8', '083107896014', 0, 0, '$2y$10$mkzl9122ObrxFEBPwXkj4eL.PwZgo9NCe4jscXrxxiWISNOPY9zCi', 0),
(228, '23241345', 'Noval Muhamad Fasya', 'XII-RPL 7', '085694928873', 0, 0, '$2y$10$F5WH6RI3ATZc7ceILYKjzugn8i.hnJXVpXDFs.VyNZCZNhRpmcFIy', 0),
(229, '23241118', 'Egar Wahyu Saefulloh', 'XII-RPL 7', '089609935847', 0, 0, '$2y$10$QArnAuPgp9wTmi4CQV4Uau.rTPIB6qkjMCmexaNdyXptJSduwZKEq', 0),
(230, '23241078', 'Dadan Juanda', 'XII-RPL  5', '081292730165', 0, 0, '$2y$10$Krkwgekin2MGmUxTjoJU5.XT9BEEk9JB0BptwOd28IxoN9y2Zxg56', 0),
(231, '23241175', 'Sigiet Pratama', 'XII-RPL 2', '082126779563', 0, 0, '$2y$10$hsdnDxPfzoNzFiI70EUB2eF23X8tmdIJysWmw7fo8XTqQj2P/rtmi', 0),
(232, '23241371', 'Farhan Ahmad Al Haadi', 'XII-DKV 2', '081573138154', 0, 0, '$2y$10$je0M0vp4cVILRB4G6dr07uO9HhmhyOU3XjMzfywJVdSfLFT2IZGd6', 0),
(233, '23241433', 'Chemal Cahyana Nugraha', 'XII-DKV 1', '083142177645', 0, 0, '$2y$10$Oo2QJom82s/rAukCDAW3l.QTCx301GS694GcQ09DKkg7Nbe70MPAS', 0),
(234, '23241384', 'Rangga Hardiansyah', 'XII-DKV 1', '085720441939', 0, 0, '$2y$10$QikXdjuW2nM/8wq8DaYf2OsuG5ZyFNtUAN/cOz69BybBZ33Sqtupu', 0),
(235, '23241435', 'Dicka Saefulloh', 'XII-DKV 3', '085703233362', 0, 0, '$2y$10$FJGQ1LLrlGPdaiv6nCAhDe95ebtja8f0iwqUhr7pywlTcPCJwnsDu', 0),
(236, '23241438', 'Faiz Rizki Nurfaizi', 'XII-DKV 3', '0895428578934', 0, 0, '$2y$10$Det5NqNYpcWsJXAa25cMzOzNHMFZAsZ9xBF107lqfdU3LJCIitA5K', 0),
(237, '23241085', 'Fikri Okta Pratama', 'XII-RPL 4', '088218246203', 0, 0, '$2y$10$hIRAmmo5LqnQtb2cHWhfxOodvd8eiD9gnp0yIULV9QfRLTHSOvrVS', 0),
(238, '23241333', 'Farel Surya Zaelani', 'XII-RPL 8', '082115564820', 0, 0, '$2y$10$SI.yh4uLLYfUTV4CoTqJhOL8D7TD8g9yL7vnZZXH3V1BMy5P/52jy', 0),
(239, '23241072', 'Wisnu Darmawan', 'XII-RPL 8', '081901191343', 0, 0, '$2y$10$pDkMWpjw2NdR2MSo4ymWO.8jW/cZQDaR0FEQDwWnA6lukWfkq6Xya', 0),
(240, '23241217', 'Anggelin Natalia Kusmayadi', 'XII-RPL 9', '083116633918', 0, 0, '$2y$10$.Tbu/fRNpqOsg9k1.3CQde7cXPbk4o3VWEUGsCR1w.IPVE8uEjIai', 0),
(241, '23241320', 'Vina Destiana Rahayu', 'XII-RPL 5', '088270973626', 0, 0, '$2y$10$50kYUlpLBfmDRj9Uw/jG0ejb2oDyEPEXKtAZIKJyktsFAqCX4Kylq', 0),
(242, '23241169', 'Puji Nurpauziah', 'XII-Rpl 5', '087863024176', 0, 0, '$2y$10$eLbaEKc825cbE3Oa7hQ1qOrvvFT9ds.u3fN6n8hMNNDoAFtnAsjaO', 0),
(243, '23241196', 'Muhamad Erlangga Pratama', 'XII-RPL 8', '081315173175', 0, 0, '$2y$10$5V2iNdcqMMYavlUwUhNf0uZ4p44PJCnke9pMlKcN4ZUYLmf38vROS', 0),
(244, '23241052', 'Juan Fadhila Sudia', 'XII-RPL 6', '085860300718', 0, 0, '$2y$10$Xc/xtTmfPR7OIPp/OLqdEuHna/Z/6f.4KMZhHmFIc6wMh2bUNVGuu', 0),
(245, '23241160', 'Lorisa Mistiyaningsih', 'XII-RPL 5', '085945922948', 0, 0, '$2y$10$NaAPOZZZkRhsOstRca9T6eqLDAddvLrfkXKWki3HHlQTh89BaQAwq', 0),
(246, '23241007', 'Dea Noviyanti', 'XII-RPL 6', '087896122980', 0, 0, '$2y$10$824BGkLhTtxGky3lXJUB7eUmeUS3nLkBGT7A4PtvvKqTzInl7jMTS', 0),
(247, '23241272', 'Natisa Nurfariztiawan', 'XII-RPL 5', '085797357582', 0, 0, '$2y$10$4vFAmiRt0MUk6BXkiHzSiehnoMK8x6XIsF6zVzbG9wcxtC8wEHY16', 0),
(248, '23241354', 'Shinta Wida Andini', 'XII-RPL 5', '082241461779', 0, 0, '$2y$10$0kcaoiE/M03pWVdLpDo7Ie/jom55kmYKVEcnFkKc8ENy.WjLGl5Mm', 0),
(249, '23241241', 'Renia Yuliawati', 'XII-RPL 5', '087892814556', 0, 0, '$2y$10$qyL6hScbMK3sVa139U7DneGlZvFn5uAl/4axeBUy6tGaYAyVf8nY.', 0),
(250, '23241246', 'Susanti', 'XII-RPL 4', '083157159446', 0, 0, '$2y$10$6hZM7uZxSpoX87r.4DoCwOTFZf2UXRPNX5DnMbX7L95/zazwTpKJa', 0),
(251, '23241232', 'Mirna Siti Aminah', 'XII-RPL 3', '0881023661422', 0, 0, '$2y$10$YkKkKVpBX6DqS7vWgHWaTuxkdhnWuVP55m44iOAHE35yTR9PWo9p.', 0),
(252, '23241033', 'Sri Suci Gilang Cahaya', 'XII-RPL 3', '081998314237', 0, 0, '$2y$10$2vM3M/jawTfOqq1i98LnX.ydIHLvYUrFv3LlHxv/5km7Xbyit3m.a', 0),
(253, '23241168', 'Perdi Setiadi', 'XII-RPL 6', '081563909264', 0, 0, '$2y$10$BXoyMa8nMCe47wOdqlPyHuBdbR8H0lt4Dv5KWpWPKGfM0lfB9xbwC', 0),
(254, '23241087', 'Ilham Ahmad Zakaria', 'XII-RPL 1', '083169995916', 0, 0, '$2y$10$m1LEec7NhWz/uJ0VC7jfLuJwMnrGCVana.r0su66sQo/AsbxNYlk2', 0),
(255, '23241012', 'Fathir Fadzilah Adz Dzikri', 'XII-R3', '085703978251', 0, 0, '$2y$10$JerjhiN6yXttcNVLuVcIyOciSBfKDzXOOsxCa1xkuCid.qh88RfqG', 0),
(256, '23241201', 'Nova Kharisma Widiyanto', 'XII-R3', '085129282918', 0, 0, '$2y$10$7ULn1bIzy/pFfjowK1CzLuB6wjxH4xE9KeXt.4TbyByIucJbr7m6a', 0),
(257, '23241119', 'Fani Fauziah', 'XII-R3', '082112862746', 0, 0, '$2y$10$dxQFDPj4IK2dvS9TVwDCrugevXRNBeZjeQQMQ52bqBTqLqjJBZxyy', 0),
(258, '23241398', 'Bunga Srilestari', 'XII-DKV 1', '081936057494', 0, 0, '$2y$10$8hQU1Fm.wJRtjyhVB9Ao4.JI9TxvzXTSyTp8xxSS2edzsCghPflGy', 0),
(259, '23241395', 'Annisa Nurul Syifa', 'XII-DKV 2', '083804750033', 0, 0, '$2y$10$IMmvN9siiuiyW0S/EVIXlubGxAxeFKe5arVM7Wev8W4mJ7dmJJwDW', 0),
(260, '23241456', 'Sasa Bela Ananta Putri', 'XII-DKV 1', '081316625011', 0, 0, '$2y$10$xX.KlMAz/idC2BHp3UcTYeOORlQNSqsTiGzWJPsPL8O52Ng2gWq5.', 0),
(261, '23241447', 'Mutiara Andini', 'XII-DKV 1', '082126091317', 0, 0, '$2y$10$SFI7L.DbQLNVIsRM7TaNB.S03jh4VLF9MuIb2uZ1oKzWPaoqR3sNS', 0),
(262, '23241429', 'Alya Aulia Zahra', 'XII-DKV 1', '081586764895', 0, 0, '$2y$10$UYZLMyBeLiu6esoCnE/jk.o8Cw290YhjvNXugTUU7ZK05StCgSt4u', 0),
(263, '23241059', 'Nindya Titis Purwaty', 'XII-RPL 4', '085695116064', 0, 0, '$2y$10$A.vtxqxNCZ8PzlKNOig5QuUMvwfxS2Oxh/gi9eBgbSUzeRUr4iOqW', 0),
(264, '23241045', 'Diandra Cindy Ramadhani', 'XII-RPL 8', '082246379833', 0, 0, '$2y$10$jsACxyYP4WVAgVF/eW1p.OP05qH0rI/q2RsJMvSKVoC0Z54cqNf4K', 0),
(265, '23241062', 'Raisya Nur Rismayanti', 'XII-RPL 2', '081224114599', 0, 0, '$2y$10$CHTVAwHtp2j8PhaLnQ7houT8pIrkjchu7F/tyD5cAz.BHuPXoNfi.', 0),
(266, '23241237', 'Novalia Agustin', 'XII-RPL 2', '081546496921', 0, 0, '$2y$10$76AA.rOr79OhRMhXEKaFNeKuIxP5.pJZE1KOig8tETzRbglCuwYky', 0),
(267, '23241020', 'Muhammad Farel Hermansyah', 'XII-RPL 3', '085798169365', 0, 0, '$2y$10$vA2WLR3CbUuVAPKBEpwu8.bKZZuq9Q8sdjCOj9.NvA8CLOxSdM3dm', 0),
(268, '23241234', 'Muhamad Fachri Darwis', 'XII-RPL 7', '081395877209', 0, 0, '$2y$10$V7pGiHoUuNziHB2MxcGniuNeBwBBghVULdCzbmzNU9t2F7CTHZpIG', 0),
(269, '23241336', 'Irpan Ardiansyah', 'XII-RPL 3', '087844585888', 0, 0, '$2y$10$JqnzAIFPl8H2BFh/TvX52eO.grk4ZFMc3T9jWUEQ4mgr6wnph2W0K', 0),
(270, '23241303', 'Mochamad Suranggana', 'XII-RPL 7', '083896542413', 0, 0, '$2y$10$tJRKQoiyLb7J5hPujb87G.n4a6.L2RIfxRs3nBJCKo1dxpSgGETVS', 0),
(271, '23241005', 'Ariska Aprilyani Supriatna', 'XII-RPL 2', '0882001454440', 0, 0, '$2y$10$3gx...5XGXnxr5oJ9dHsA.IHu0Ko2Y4HbjLRzjuwZMHa8is9nQeyy', 0),
(272, '23241098', 'Pebrianti Anjani Putroi', 'XII-RPL 2', '083116654735', 0, 0, '$2y$10$LZtLI3oZ6FXDjNnQBxhetuXb4mCDjHW8zRb5eMMOYJiDySF9zkMVi', 0),
(273, '23241291', 'Daniati Nur', 'XII-RPL 4', '085723527305', 0, 0, '$2y$10$S7AKn3a5ptzqoDcBic.H/.V3fvcoU9WTLCObyEdRQtSFFo0bp4Z5O', 0),
(274, '23241025', 'Nurhayati', 'XII-RPL 4', '082115523629', 0, 0, '$2y$10$Vr6FzSvvgbiXSn8fA5xhIup1EeiGseCNTlLlHfPp3gmmys/Mo./Iu', 0),
(275, '23241038', 'Ahmad Reza', 'XII-RPL 6', '082143728931', 0, 0, '$2y$10$65gan8XSb5CSRtdafANRDutIN8aLHJki7mgd2nUF8HKAOVM.nC8rW', 0),
(276, '23241067', 'Salwa Nur Firdaus', 'XII-RPL 1', '087821121667', 0, 0, '$2y$10$RBJTtQy9gnf.7lcc5.TuuO1aWpbQLVZPcJx8j7mhn2QcjPX3RejTO', 0),
(277, '23241300', 'Kirani Haerunisa', 'XII-RPL 1', '081223738277', 0, 0, '$2y$10$d.dfoy9yoGS8zEDAoe0Keu4RtKlRjMf.Jqem/ch8OOFpZTLnlh5Mq', 0),
(278, '23241309', 'Raisa Juliyanti', 'XII-RPL 1', '089698921578', 0, 0, '$2y$10$RcdKO2x3z15E99hrN8fw8ebp.sNnA3go8ZggVGXIaZWun2K.If5XC', 0),
(279, '23241128', 'Muhammad Redyansyah', 'XII-RPL 4', '081222172242', 0, 0, '$2y$10$duy/5LYE5utolBAsH1407uNizm0uJXKIhrm4sd73WZhT5ClRkVknS', 0),
(280, '23241156', 'Indah Jelita Wulandari', 'XII-RPL 3', '081214311792', 0, 0, '$2y$10$Bv4SpdzCHJsDYBAIRD2CcOQQrI6yeosWc2TNmMG3vnrfdtzcwBwPi', 0),
(281, '23241347', 'Raisha Dzikrina Shafika', 'XII-RPL 3', '087711229096', 0, 0, '$2y$10$JPjjNMlQAe1JVDuxMziE8OkpmvelblYVqBTyTvxxbhhuWOuHCsANa', 0),
(282, '23241281', 'Siti Nurpadila', 'XII-RPL 3', '081320982091', 0, 0, '$2y$10$g.AVYq24dFDEPrJykCfyguB5SYfQT/AKDijBYObqSjd54cB64ppO2', 0),
(283, '23241293', 'Deva Destafania', 'XII-RPL 2', '081770057391', 0, 0, '$2y$10$q.h/c8SZICJp/fAdRUEg0eQJMZ/3LMGXFXlTRZBrzwvFZgPcQZDB2', 0),
(284, '23241132', 'Paundra Pahala Kusnadi', 'XII-RPL1', '0882001411637', 0, 0, '$2y$10$D9Iq57T5lrCL/tniAIwfx.gu2NVCNQZYC6lmG/9Bb/30qF3qqCuV.', 0),
(285, '23241214', 'Zaki Ahmad R.I', 'XII-RPL1', '085795817343', 0, 0, '$2y$10$n/uvk7CT3LRmqgvJqc.3VOIwIyAGjXi5tscgR8aBGORqQ8QUvE6Ky', 0),
(286, '23241205', 'Rendi Gio P', 'XII-RPL1', '085724523968', 0, 0, '$2y$10$rjycMEGBJDYCA/MBDpBMH.Pa59oSiVQOOKPrVNKupeySUe8pd1Mw.', 0),
(287, '23241048', 'Fauzan Nur Rizki', 'XII-RPL1', '083822995235', 0, 0, '$2y$10$cdBcHFTSOSoFwWt.v2tRq.3u5p1f5bZUp.Wu0u1yeGKozPeW.HhCO', 0),
(288, '23241054', 'Muhammad Haichall', 'XII-RPL1', '081299659711', 0, 0, '$2y$10$yJ2mcFBB23iMnxWMCGXD4ON77hzkbtq9vbqciLxDQa6FsOuTsGrYe', 0),
(289, '23241134', 'Rani Fitriani', 'XII-RPL 8', '085603561087', 0, 0, '$2y$10$a.fjCEOPAX.nyUcQ/7q9C./sTQ8WuPZnS63A9y7xTRTaqsPG8s4wC', 0),
(290, '23241297', 'Firda Meilani', 'XII-RPL 8', '082264995263', 0, 0, '$2y$10$l917Ndh4UbnJ0p1Md7ix9eqFYqHaAHgdNsOvqwMci7ETjsz8N7jU.', 0),
(291, '23241267', 'Mita Andini', 'XII-RPL 7', '085759907226', 0, 0, '$2y$10$P6zPrGX1tIsQQ7yWoK7Gm.Cwmw3NcTp9npfJ4oKYIx9nc8wdI4rrO', 0),
(292, '23241380', 'Nisa Nur Maulida', 'XII-DKV2', '082240458920', 0, 0, '$2y$10$27rFs2Sot2003zTeghpwru6NbkAy3z4q/m16H8sIqcDhOWO6EGDTm', 0),
(293, '23241017', 'Jeal Anggraeni', 'XII-RPL 5', '082126082495', 0, 0, '$2y$10$3r0GIH8xoPF/kWKiJqlqteErnigKn2jl7B/mBLMMw5CfIMnUnMaiq', 0),
(294, '23241139', 'Sarah Kautsar Azzahra', 'XII-RPL 5', '087824987112', 0, 0, '$2y$10$rt.ts9yHj7YIOrCojzmUfOqaRUxt00RdiUKludHXAuEAaGYM.7o0W', 0),
(295, '23241011', 'Enok Nurhasanah', 'XII-RPL 7', '085878691821', 0, 0, '$2y$10$07wxWRZdgulsgbO7Jujx9./qP6WDr04UVCNuM9mdGyZLC1sFGOD3u', 0),
(296, '23241223', 'Elfira Rosa', 'XII-RPL 2', '085157748625', 0, 0, '$2y$10$J4jgoTbW9DRzWMvkO5zAn.6hIwILlLw2kQO1/bhwWNb8Kxnz6Afe.', 0),
(297, '23241340', 'Muhamad Hendro Purnama', 'XII-RPL 8', '081324062822', 0, 0, '$2y$10$Dps33pMwipsfM0Xlg815.uKnKYxCui.gmotYM97n70F7OK9lpScfu', 0),
(298, '23241162', 'Muhamad Bilal Sedawijaya', 'XII-RPL 1', '081321605510', 0, 0, '$2y$10$VFRQRjpxKQd59hN6Ve9ieewoze7mxbubuXy2Kc6XEXzMn6CViY07C', 0),
(299, '23241193', 'Indra Saputra', 'XII-RPL 8', '083114523570', 0, 0, '$2y$10$60P6Pn1IWpYNlL5xPTXGoOzszM5YYjYjEZlR5gyrc0uKyghRT.9Ru', 0),
(300, '23241113', 'Azrin Khoerun Nisa', 'XII-RPL 8', '082118285193', 0, 0, '$2y$10$KnvV6z/lxeH8H6KDERtM1.lDFkSRkMHcTh87a.GNPJQmqSMIUBSqC', 0),
(301, '23241316', 'Shifa Aprillia Nurjanah', 'XII-RPL 8', '089601245451', 0, 0, '$2y$10$wmr3Ts5LKH.9gaiAVAiFl.mvUMA.fAV02J7Y3IrnkKPf7RxK/Rsqa', 0),
(302, '23241140', 'Siti Nuraeni', 'XII-RPL 2', '082116051247', 0, 0, '$2y$10$pN4DgyiD66i93J.5fQYk0eki30WYdMp4RrF1WMPtf1MMo8sTckW4m', 0),
(303, '23241107', 'Winda Verlita Febriyanti', 'XII-RPL 8', '087887374186', 0, 0, '$2y$10$JWVMg/3Wu0LxI8HpMEXNS.lmB9ITAmw4NbkvgGT6rYZnk2XOUvZ/G', 0),
(304, '23241049', 'Hana Shofiana Sholihah', 'XII-RPL1', '08388085831', 0, 0, '$2y$10$Ja90nF7odZoH1OEsshSPye39sF25ZNqzvIJ.VggQYi9QesJ3LPvuC', 0),
(305, '23241086', 'Helma Liaputri', 'XII-RPL 8', '085721184470', 0, 0, '$2y$10$4GR77TxR1zYRAWzNbrqPvO1on1C2H3ADdKEhmuFTVsv8Qsj7gwD4a', 0),
(306, '23241082', 'Dwiki Juliansyah Sudrajat', 'XII-RPL 2', '085659620840', 0, 0, '$2y$10$zz3UMSHoCFCdOezYnv/EWuMQ2Jqfkha7ymZXDdNrytvdm4WiFVZEy', 0),
(307, '23241342', 'Muhamad Hapid Azis', 'XII-RPL 2', '083846356593', 0, 0, '$2y$10$BQbB9E0iWKk8UDSvFjKqcOAHq/ump20z.145Ugliqs/62eUaUIwFu', 0),
(308, '23241053', 'Maya Julianti', 'XII-RPL 4', '0895347902211', 0, 0, '$2y$10$Q8hako1qZWKvr3YKjriOqOZvGGXNluiL.EEQU/25B1Gcrv0S8w8EW', 0),
(309, '23241188', 'Dian Oktapiani', 'XII-RPL 4', '08131572557', 0, 0, '$2y$10$t9fT8jSLUtFyLxa1b6DBJOCfjViP3Hx8ZhCyFAu1Ual1yJYfWrLXa', 0),
(310, '23241010', 'Dito Mahardika Ramadhan', 'XII-RPL 6', '081214236404', 0, 0, '$2y$10$TCeom7oU/GFUTtx1qS474eMyURoTtew1H4/i8jcOgdM54aU6KS8TS', 0),
(311, '23241121', 'Ilham Nur Fatahilah', 'XII-RPL 6', '085343834236', 0, 0, '$2y$10$6I0MIE3BgRZwoEWXnrQ.OO99/YI7uFvLgqAoHuyN1YgEz8SkSUSTS', 0),
(312, '23241210', 'Sunnyta Putri', 'XII-RPL 5', '081930560597', 0, 0, '$2y$10$pk8XkGClEG8FblPYZkqGkuJBb5AVQC8KGrAziWnknqhaYFryHOC7q', 0),
(313, '23241248', 'Vanni Mayoshi', 'XII-RPL 1', '082113084143', 0, 0, '$2y$10$mAGBz.fPkzAWttGZM6YsheESyTlsmjchQ.IRf8znkif4Q59vwtiRK', 0),
(314, '23241292', 'Dede Pikri Hidayattuloh', 'XII-RPL 4', '085965940039', 0, 0, '$2y$10$t3Mo/OM3YgROADBIbmRzF.P8PY02e5LLC1yKmQ0JMTmLVPUfB33Gy', 0),
(315, '23241290', 'Asep Gustiawan', 'XII-RPL 3', '085722233126', 0, 0, '$2y$10$DIxlEMGXEcOvA7HyVMN70OQVPQicj9JIrDDcnddq5zWicdk6St44.', 0),
(316, '23241073', 'Adit Darma Wiguna', 'XII-RPL 4', '083153522964', 0, 0, '$2y$10$R3DcXcu6vDYFNz1RlUpm9uhFF.E7xnPlDRH1GRAh/GqP0dDnas3ze', 0),
(317, '23241302', 'Lutfhiana Nurfadilah', 'XII-RPL 4', '082318384515', 0, 0, '$2y$10$T2zev9fFxGon/y.cEBanB.1eYRa2CJf4cgstmWsCCgQI9N6QjERWS', 0),
(318, '23241351', 'Riko Fuasra Pramudika', 'XII-RPL 4', '081394304412', 0, 0, '$2y$10$oAHN0MGISIwTLtbCtnk9iuK.r3lpboO.rFJD4/NwhtSHJJk2O.bSO', 0),
(319, '23241019', 'Muhamad Iqbal Firmansyah', 'XII-RPL 5', '085793620744', 0, 0, '$2y$10$JyLHEBL5FrmcG4WHqCoccelYWXx277AVHsl7NqeP625Gwzmfg4hG2', 0),
(320, '23241323', 'Ahmad Fauzan', 'XII-RPL 4', '085795515976', 0, 0, '$2y$10$aEWCO6TW/5o6nC2.cEcBZur/uCFlzhmsQw8nxkLkE3MhRrkbiZXX.', 0),
(321, '23241015', 'Ilham Fajar Permana', 'XII-RPL 4', '082119017016', 0, 0, '$2y$10$IT5PfGyNr3LP/WO.YkUpJOem/8fcAfMoWoE2Il.gmB.gQQNurU.QS', 0),
(322, '23241155', 'Firman Galih Kurnia', 'XII-RPL 1', '085862706551', 0, 0, '$2y$10$k/47WeiOhKdkVGBA.JQDPOOvISNxjVZRVhTYONBSmYx4XemduG5P6', 0),
(323, '23241229', 'Kania Novianti', 'XII-RPL 1', '082318060690', 0, 0, '$2y$10$8Kz.5ltnqcGlLu5ozq.zgOZF5/TPqjniV0tk1KfxzMhBEQ3Zoc2Bi', 0),
(324, '23241091', 'Muchamat Raziqin Budi Saputra', 'XII_RPL 1', '085792244155', 0, 0, '$2y$10$R97C0/tXSgqHcexy6rKhau8gOKA07AAkQBrCG5uYQCHW0tHngLv/.', 0),
(325, '23241014', 'Ikhsan Gustani Arifin', 'XII-RPL 9', '085722249389', 0, 0, '$2y$10$/lXzWCWQi9BKWbhShYj9U.03Wqk9Ebcx8LvTefZNBkgDnM5dSL7RW', 0),
(326, '23241204', 'Regita Chika Putri', 'XII-RPL 2', '085694121601', 0, 0, '$2y$10$M5gsbun8vp5/YPp2KvKGBuGS.kCKK1TGd28nB9UzN/Ve7cMj9dXm2', 0),
(327, '23241146', 'Akmal Alfarizi', 'XII-RPL 2', '087709011947', 0, 0, '$2y$10$15B1k3rB0F4BJ8MPSzQ9B..J/GzxRkNFBThbhJeKjyKH76xX84tA.', 0),
(328, '23241090', 'Maya Nur Fanta', 'XII-RPL 7', '085860843375', 0, 0, '$2y$10$6CCy0Q2paDVW1BfUjKnEC.3j3rq4W85HEhFX38irsDfvsaEVhl0t.', 0),
(329, '23241171', 'Renaldi Putra', 'XII-RPL 7', '0895351586430', 0, 0, '$2y$10$ipoJfy7FT3ClcPU0rmPBqu5Sh0yuQsig69wuzoTfJSsnODk.FnnHe', 0),
(330, '23241150', 'Dandi Muhamad Firmansyah', 'XII-RPL 8', '0814861101207', 0, 0, '$2y$10$hEQjK4BaQTcxHmmoWgzuC.RfKl9knoswvKvsYekqM/Vkd/61zhbom', 0),
(331, '23241253', 'Andrian Dwi Andika', 'XII-RPL 4', '081909557097', 0, 0, '$2y$10$.IdRD9C58jKa4tVKEivDhOA9mZlxfoy8gta1UKK0Ke85DY9RpWS7S', 0),
(332, '23241055', 'Muhamad Irpan Pirdaus', 'XII-RPL 4', '081232804181', 0, 0, '$2y$10$jHChCgTjtlyrMuz1MFSCee1GMCHWZ58eLa5h1t/0ICTR7dNnLqq/q', 0),
(333, '23241461', 'Vika Tri Juliani', 'XII-DKV 1', '08157226627', 0, 0, '$2y$10$a6NlTXb15F0LO4TBj7xzOuwK81ajsThHcfOLpTZhrds6N8JUcRXLi', 0),
(334, '23241450', 'Nurul Azkya', 'XII-DKV 2', '085775542991', 0, 0, '$2y$10$T1WSSKqwRoxC..u4YTJdZO7SSUoiAAHgDXFWu1CyC9k49Fvbw5/ru', 0),
(335, '23241440', 'Indyra Agustina Putri I', 'XII-DKV 3', '083892511443', 0, 0, '$2y$10$91u8tw8vDGqYG7.y00AM8eRt4w9WjEc2ut4zXpbDMEOy2evP3om0m', 0),
(336, '23241422', 'Rosa Amalia Nurhidayaty', 'XII-DKV 3', '087830006360', 0, 0, '$2y$10$O35JB9fv1hiYQgx0aSTimuU8h8ulqTJyqeYxnuA.KxFDxESh4q7vq', 0),
(337, '23241424', 'Sucika Nur Syfa Rofii', 'XII-DKV 3', '087875442327', 0, 0, '$2y$10$Z46o0nhYPh5vZDcptk6J5OnvydvVAeBZvp0PtcrlzsFQDWBNTsBGG', 0),
(338, '23241434', 'D.D Aulia Sari', 'XII-DKV 3', '085931156859', 0, 0, '$2y$10$wIEouKoHRjwLH71FJfmmfuTYVUPJOmpfQ5ninEJYY7bi20YG0A4Qi', 0),
(339, '23241369', 'Epa Sulastri', 'XII-DKV 3', '085693928713', 0, 0, '$2y$10$EZz3NmOvwyx2UuKnHqe8MeEqbVjBjDPRrW62xS9a2vVL8njmCANnC', 0),
(340, '23241361', 'Ahmad Komarudin', 'XII-DKV 2', '082126007681', 0, 0, '$2y$10$9fsOhD4RRmP3kp7YgqVCjun6BkorTxD95QlH..ezu2puHcfrBj58u', 0),
(341, '23241373', 'Intan Widyawan', 'XII-DKV 2', '085659700468', 0, 0, '$2y$10$fNU8PVhr/QskiiHlxECffueOLrwD8tnpaVm2f5MrIBq8fWjWb4e86', 0),
(342, '23241400', 'Clarissa Putri Kusnadi', 'XII-DKV 3', '0895808100880', 0, 0, '$2y$10$epQCVVVDHohwuW8Bk5eGSOUhvNh/rdRpDlQLHvVtjoCl4i5.nizYa', 0),
(343, '23241151', 'Dhenda Tri A.Y', 'XII-RPL 9', '082119650009', 0, 0, '$2y$10$6BxouwJUriomPfJH2zt4z.pLi6gVT54EKpOVUf5.C3vhPh4WWpPnK', 0),
(344, '23241148', 'Ariek Huznul Haqim', 'XII-RPL 8', '088218706037', 0, 0, '$2y$10$xG27SvlSkRNk04.iiJv9Pe4LeAI4EO9E98DKt/ARZNTIbVHHPFeDC', 0),
(345, '23241296', 'Fajar Anugerah Suherlan', 'XII-RPL 8', '08882351959', 0, 0, '$2y$10$Q4CUqGXL35m9VYoeFktTvezLHY14QY3l..P/1tTefr6/e1vW5mR4W', 0),
(346, '23241305', 'Muhammad Fahmi Syukri', 'XII-RPL 9', '085800173959', 0, 0, '$2y$10$jOIcXYzrGZ77vu01TXlib.uifZ5ryhwM3P4nrP.cw.fYM8X7vobzi', 0),
(347, '23241299', 'Intan Parhataeni Sari', 'XII-RPL 7', '087813829447', 0, 0, '$2y$10$RVxKNClsQhuEVBi5ir6rLu5a2gv0Luz/ewi94GD8BCxBNIMmSFCPu', 0),
(348, '23241355', 'Soleha Wulandari', 'XII-RPL 7', '083893134848', 0, 0, '$2y$10$g7wCfIRK86ZJwBt7XKMGjutvZWD1xdbSwfFrmvHAC.kSv3Man4PfO', 0),
(349, '23241415', 'Nur Dewi Afni Alvionita', 'XII-DKV 1', '085777005387', 0, 0, '$2y$10$9RkPFsZyrcZ4zZsrsppUD.im5CRe2Lq.WsAwoQ97KlEIShtLbcJG2', 0),
(350, '23241339', 'Moh. Fahri Mubarok', 'XII-RPL 5', '0881022312768', 0, 0, '$2y$10$23lpRYeXNPoJwIBahh74LeP7TabC87gruughUWR7e1jHkJnGJ26yK', 0),
(351, '23241131', 'Nisa Yunita', 'XII-RPL 7', '085794100763', 0, 0, '$2y$10$twvgpzW/K4afDxLwlTM50eXk5ppHH2ROP7HahU95pIgJjpTpqqUAe', 0),
(352, '23241167', 'Nitta Salsa Kirana', 'XII-RPL 7', '083869587852', 0, 0, '$2y$10$x6vIEJ9GN21WjnT51L13gegW2/SOqdb6UNrDNKxk62d0yNYgrtMc.', 0),
(353, '23241112', 'Arief Anggia Purwansyah', 'XII-RPL 8', '085795832406', 0, 0, '$2y$10$0akmQmFkotXaxMNuRjJj1OM56zwyy/ueTXILk1XroESFZPzVyLuge', 0),
(354, '23241089', 'Kaka Maulana', 'XII-RPL 7', '082195412220', 0, 0, '$2y$10$gY4H9mTM/8YV5zOZxzMQs.f8J3zUg0t5h3KweYUdJLfD2HdA2JpdC', 0),
(355, '23241216', 'Algian Akbar Pratama', 'XII-RPL 7', '088222144246', 0, 0, '$2y$10$pri5UZ1PHvRrYlq83HHahuoA23aUDCtNzNipeH1QZFvBVOxmaaFSK', 0),
(356, '23241312', 'Reyhan Firmansyah', 'XII-RPL 7', '081952804274', 0, 0, '$2y$10$ppJuSkZ90mN33HbT/lK0OO8gRc28JChKllZ4v2340uJ6lkpp04qxa', 0),
(357, '23241203', 'Rafi Nurikhsan', 'XII-RPL 7', '083893470681', 0, 0, '$2y$10$6i/zkzwVAwgOHgeg2lw81ehUu7SsAxxIRZszFoxsiuuezWStjap6y', 0);
INSERT INTO `siswa` (`id_siswa`, `nis`, `nama_siswa`, `kelas`, `kontak_siswa`, `id_pembimbing`, `id_tempat`, `password`, `password_status`) VALUES
(358, '23241247', 'Suwandi Rifki E', 'XII-RPL 8', '082118589563', 0, 0, '$2y$10$veoZZp3E6kY09RUMGViFDOqqKDBE/I4Sc.MiXWNjydIuvuwI5cBFi', 0),
(359, '23241034', 'Syifa Nurisman', 'XII-RPL 3', '082116324845', 0, 0, '$2y$10$zoe2oEKypZDyzZdx9.rJbe39gBfhSFFyyNL9cauf5MYRyqcJV6Ley', 0),
(360, '23241242', 'Rizfy Muhammad Adilfy', 'XII-RPL 8', '089653885699', 0, 0, '$2y$10$HRqIFYEE5FgbwejwSzgUs.jJHFxY/u.jBBNRXG/Iyte08diyZdaEq', 0),
(361, '23241093', 'Muhammad Rangga Maulana', 'XII-RPL 8', '082114067084', 0, 0, '$2y$10$oJHjQcD2qSGiqpL96I/m6e8mhzsZlQlNbbSBwz921UC3/sj0a.tEu', 0),
(362, '23241370', 'Fadlan Sabil Sopyan', 'XII-DKV 1', '081411165297', 0, 0, '$2y$10$UDw6KSvDTNGC9ZeLeyV4R.vUQB4RLZP3xgyZ.iOQMkPc06zUpvp6O', 0),
(363, '23241391', 'Tommy Aldy', 'XII-DKV 1', '087736592825', 0, 0, '$2y$10$DCCgC7Hb.ULlOib2yJzEB.JCFovBsmNG/MBoPp/TQzD0rp/groAVa', 0),
(364, '23241379', 'Naura Salsabila', 'XII-DKV 2', '085603520059', 0, 0, '$2y$10$rUfPIitBf3DCYzaGCH70ZOXcjDacauS6Ih9wiT0E69LhimkggCkGe', 0),
(365, '23241425', 'Syahrul Fadli Rukmana', 'XII-DKV 2', '085603511061', 0, 0, '$2y$10$K6zpoeBtNo014fDtjaARBOBeJ2UXY8WK5kf90AOOaM72sczJ/OpZS', 0),
(366, '23241393', 'Adit Tia Irawan', 'XII-DKV 2', '085659571320', 0, 0, '$2y$10$F3kso6lnuAFGxml5MzhrRektMf2oHppNgKw1.t5rdjYDyFhr10FRi', 0),
(367, '23241375', 'Luqi Lukmanul Hakim', 'XII-DKV 3', '085793534892', 0, 0, '$2y$10$W/3q0hOvoYqX1e/.rL1xb.jlG1WOzQsAP6hqISJVD4KgGJerj9za2', 0),
(368, '23241452', 'Ramdan Ilham Nugraha', 'XII-DKV 3', '082128498480', 0, 0, '$2y$10$0FbX6VHuaHgAMqZHMDEXXecgXhJbuM5JWa.r/8B52hy0kK4zQGuDu', 0),
(369, '23241417', 'Rainanda Setiawan Nurdin', 'XII-DKV 3', '085703679967', 0, 0, '$2y$10$Wi4vg8EYGcqpt8BqFLvX5Oeloq7eHo.mpPs6BR0ouY/R2R7ujXDBm', 0),
(370, '23241418', 'Rangga Hermawan', 'XII-DKV 3', '085150798743', 0, 0, '$2y$10$gUbRQ1VEKMWCWue059jnV.9ZpQ.s9Cy3VAus3gmfsRC99XLvvT56W', 0),
(371, '23241403', 'Dhika Aditya Widjaya', 'XII-DKV 3', '082119683593', 0, 0, '$2y$10$aZcL4WogcbrRfOTZCti0veFa0VLKON8YaGHQx1Chul79ZHS89PYKu', 0),
(372, '23241426', 'Wajihuddin Wafa', 'XII-DKV 1', '085942157475', 0, 0, '$2y$10$0SHB7QyxJQ816aPL.bD6PO5HvyF7Gvj33uhOAF.Q.EAYdknvVXz22', 0),
(373, '23241197', 'Muhammad Aprizal Sanjaya', 'XII-RPL 1', '081313011934', 0, 0, '$2y$10$C8uXXVzJ5//MQVLdcfZKkeKYAIsTKcds99C1eXLmJboDQL310xbwG', 0),
(374, '23241136', 'Ripan Nugraha', 'XII-RPL 1', '085861556201', 0, 0, '$2y$10$OW5fndSBUjXWHG0.FYnZwOoNkK6M.oZ8CEm0uyeEvtdhxHBW8wTQ.', 0),
(375, '23241032', 'Silvy Nur Oktaviany', 'XII-RPL 7', '081222175646', 0, 0, '$2y$10$OQ37TYv7Gusfqu17VD1mXeITAuRF/hjSqVI3pw.PsbJbFv2n/hzu6', 0),
(376, '23241083', 'Eva Oktaria', 'XII-RPL 7', '081224184916', 0, 0, '$2y$10$ZKv/ud5MS08Dj91ou7duuucilfNk1NuqW8dghd3Yb4S1z3iF/fOv.', 0),
(377, '23241194', 'Jumiyati Yuningsih', 'XII-RPL 7', '082113533247', 0, 0, '$2y$10$DaSBO2PPbRXifUDCfeORX.E3vnCJGUoZQKASWXfAnYl2hOUA1eUJa', 0),
(378, '23241249', 'Zachra Nur Ramadhina', 'XII-RPL 7', '082118591584', 0, 0, '$2y$10$VmbO0YubkRoV3gkR86MjBOzps2cAAm6oj3N1WwVfKThyTnXX6fuRS', 0),
(379, '23241327', 'Azikry Pratama', 'XII-RPL 9', '089525321170', 0, 0, '$2y$10$zzIm/tUgwJI8.8c9hjhFROFY8A/zHcNp6gUd0zmwDBAtJLyR0c0hi', 0),
(380, '23241182', 'Akmal Astcolani', 'XII-RPL 9', '083180194930', 0, 0, '$2y$10$AG58QQ/1Jf4n5iIi.wFL7.miDD.BDaMhdh0BNSRiwKLN3feJDcpGK', 0),
(381, '23241173', 'Rizal Ahmad Albraikhan', 'XII-RPL 9', '089697543002', 0, 0, '$2y$10$mJ66kH2jRexdb/yx2YxIu.4YRoMRLZNXwCGpiqOiveXyFUp.LBBKi', 0),
(382, '23241335', 'Ijlal Muzhaffar R.', 'XII-RPL 9', '085951620194', 0, 0, '$2y$10$oXg4Fwj3.7RLjLYREfWw4.VgfSkxmZP8E3ZrnGvzJDApeJr8TW0Me', 0),
(383, '23241250', 'Zaki Muhamad Husain', 'XII-RPL 9', '085931515801', 0, 0, '$2y$10$tnqm6pcoxaTbnLVf80zAT.5H.RwIHGB5SBK1Qb68yWxmTrf4X4lZi', 0),
(384, '23241283', 'Tedyana Ramdhani', 'XII-RPL 1', '082126510327', 0, 0, '$2y$10$iseOyVmBxMY6mBDwk2sjdu.qe4n0iZMVlR2IkJmxrKzaqw9lOhJVO', 0),
(385, '23241334', 'Fitriani', 'XII-RPL 8', '085187290028', 0, 0, '$2y$10$zjB9UeGhv7b7YU12.ATDIuRZIAgtoX.tB.kthhKmFp1FxJxT.ETIW', 0),
(386, '23241343', 'Mutiara Gemilang', 'XII-RPL 9', '087877429824', 0, 0, '$2y$10$.GB/pcn19In8IO5FfN2YfuSm.HU/K1O0cpbTMcJ1kdkW.qf7Vad5W', 0),
(387, '23241337', 'Istikomah', 'XII-RPL 9', '085871208024', 0, 0, '$2y$10$tjPayAgjH0XMt646RBV1y.pIKREwseSR4hOwarufXQtjqE7c2DAU2', 0),
(388, '23241192', 'Indah Siti Nur Fadilah', 'XII-RPL 9', '081553028647', 0, 0, '$2y$10$gekkltzVMicVAYhciQm69uvzymrq08IIw/tZyKI/hr5Oy7Y6F.Kum', 0),
(389, '23241122', 'Ilma Puspita', 'XII-RPL 9', '087898112293', 0, 0, '$2y$10$jzprwv6lIla6QuIObEtB4e2nQHVogrJijRtejHaP/CjfYY0BAlhA6', 0),
(390, '23241213', 'Yunita Lestari', 'XII-RPL 9', '087730763945', 0, 0, '$2y$10$tzdLQP3ucy.SGf/x14rX5OyVpr7M/rPMCZrTl74sp1hrZp8PSgQo2', 0),
(391, '23241115', 'Deli Mira Wati', 'XII-RPL 6', '085956296473', 0, 0, '$2y$10$kjLqgqlcQHICv7b7fbWmrewNuCtqxMCUx.se8LYvYfCUE48iI91F6', 0),
(392, '23241127', 'Muhamad Bagus Ari Wibowo', 'XII-RPL 3', '085931515396', 0, 0, '$2y$10$YlO4Q0XKsRDzbkgJdWP0FOzmOl3o9qcWQECzNzXxglNfd49exhN9.', 0),
(393, '23241094', 'Muhammad Rakan Al Imtiyaz', 'XII-RPL 6', '081909822419', 0, 0, '$2y$10$SUauCPfraWU0fu84ku6qX.KT/BdD1w76JLlyjbieOWGRNeaTbqnbi', 0),
(394, '23241191', 'Hadi Budiman', 'XII-RPL 4', '082319511106', 0, 0, '$2y$10$1eyQ0/s7e3HThAKLNiL2AOGQcJHTipwdKDtZ.U3FTRSRNu5srEf8y', 0),
(395, '23241352', 'Rizki Maulana', 'XII-RPL 5', '081223201713', 0, 0, '$2y$10$CSYdi4xoR7t/iH/tO70SEelulJvbJMNAwcRqrTj5Y/M/IUAQuhJyS', 0),
(396, '23241024', 'Nur Arby putra', 'XII-RPL 7', '081312759860', 0, 0, '$2y$10$XqdhF0wLdhWkF6weamP/buhseYbJoheQuG9Y7NPJkktHyc7aG82RS', 0),
(397, '23241357', 'Topan Lesmana', 'XII-RPL 7', '082119665054', 0, 0, '$2y$10$kST/0D4PFfnf6UlGW7sbbOEbz2fsUdGTwlG3pl5jtRpY.mOaxQgAa', 0),
(398, '23241009', 'Dian Mutiani', 'XII-RPL 8', '082249324477', 0, 0, '$2y$10$/VBGmMj2Y9egdq80f5OjE.G8guZaxWWuB72JFwW1cJPgUiPWLX1d6', 0),
(399, '23241350', 'Rheva Zulfa Fadillah', 'XII-RPL 8', '081563338458', 0, 0, '$2y$10$HdaHUrwtR8hFoiPqxJsnaeVirBqiAQ/fmJ3EZ/2MpHcBIExlYrkRe', 0),
(400, '23241208', 'Sintia Padilah', 'XII-RPL 8', '085194231745', 0, 0, '$2y$10$YmLh25iQueEF9NFfNEsBAuXHFEMRtTBdrCscYPNr0pdGprKFa9MIe', 0),
(401, '25', 'Apep Ganteng', 'XII-RPL 25', '0000', 0, 0, '$2y$10$p/ePElW.9BVu9xlBHwY9fundyS5CGVJdhupy9EbM/lHXvdbMVmdbq', 1);

-- --------------------------------------------------------

--
-- Table structure for table `siswa_surat`
--

CREATE TABLE `siswa_surat` (
  `id_surat_siswa` int(11) NOT NULL,
  `id_siswa` int(11) NOT NULL,
  `id_surat` int(11) NOT NULL,
  `status` enum('pending','diterima','ditolak','') NOT NULL DEFAULT 'pending',
  `catatan` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `siswa_surat`
--

INSERT INTO `siswa_surat` (`id_surat_siswa`, `id_siswa`, `id_surat`, `status`, `catatan`) VALUES
(22, 212, 11, 'diterima', ''),
(23, 209, 11, 'diterima', ''),
(24, 144, 12, 'pending', ''),
(25, 10, 12, 'pending', '');

-- --------------------------------------------------------

--
-- Table structure for table `surat`
--

CREATE TABLE `surat` (
  `id_surat` int(11) NOT NULL,
  `no_surat` varchar(50) NOT NULL,
  `id_tempat_pkl` int(11) NOT NULL,
  `tanggal` varchar(20) NOT NULL,
  `status_balasan` enum('Sudah Dibalas','Belum Dibalas','','') NOT NULL DEFAULT 'Belum Dibalas'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `surat`
--

INSERT INTO `surat` (`id_surat`, `no_surat`, `id_tempat_pkl`, `tanggal`, `status_balasan`) VALUES
(11, '001/PAN-PKL/SMK-IF/YPS/XI/2025', 3, '2025-11-17', 'Sudah Dibalas'),
(12, '002/PAN-PKL/SMK-IF/YPS/XI/2025', 3, '2025-11-17', 'Belum Dibalas');

-- --------------------------------------------------------

--
-- Table structure for table `tempat_pkl`
--

CREATE TABLE `tempat_pkl` (
  `id_tempat` int(11) NOT NULL,
  `nama_tempat` varchar(150) NOT NULL,
  `id_pembimbing` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tempat_pkl`
--

INSERT INTO `tempat_pkl` (`id_tempat`, `nama_tempat`, `id_pembimbing`) VALUES
(1, 'Sekretariat Daerah Sumedang', 1),
(2, 'Jonas Photo  (Bandung)', 2),
(3, 'B One Corporation', 2),
(4, 'Percetakan Sofasco', 2),
(5, 'Polres Sumedang', 2),
(6, 'Dinas Pekerjaan Umum', 3),
(7, 'BAPPPEDA', 3),
(8, 'PT. Milari Risalah Wisata', 3),
(9, 'Busy Production', 4),
(10, 'Freya Clinic', 4),
(11, 'Sumedang Laptop', 4),
(12, 'PT. Tritunggal Swarna Factory', 4),
(13, 'PT. Chlorine Digital Media', 5),
(14, 'Ismail Copy Center', 5),
(15, 'PT MULTI BOX INDAH', 5),
(16, 'PT Newtype Tekno Pioneer', 5),
(17, 'Dinas Sosial Sumedang', 5),
(18, 'Dinas Arsip dan Perpustakaan Umum Daerah Sumedang', 6),
(19, 'PT Elion Hanaya Abyudaya', 6),
(20, 'FTI UNSAP', 6),
(21, 'Badan Kesatuan Bangsa dan Politik', 7),
(22, 'MPP Sumedang', 7),
(23, 'Cemara Agung', 7),
(24, 'Dinas Perumahan Kawasan Permukiman', 8),
(25, 'Percetakan Nadhif', 8),
(26, 'JNT Cargo', 8),
(27, 'PLN (Persero)', 9),
(28, 'PT. TIKI Jalur Nugraha Ekakurir (JNE)', 9),
(29, 'Pengadilan Agama Sumedang', 9),
(30, 'Studio Genic', 10),
(31, 'Dinas Koperasi Usaha Kecil Menengah Perdagangan dan Perindustrian ', 10),
(32, 'Pegadaian Sumedang', 10),
(33, 'Kantor Kecamatan Cimalaka', 10),
(34, 'Dikhan Print', 11),
(35, 'RSUD UMAR WIRAHADIKUSUMAH', 11),
(36, 'STO Telkom Sumedang', 11),
(37, 'Dinas Pemberdayaan Masyarakat Desa', 12),
(38, 'Bawaslu', 12),
(39, 'Pubdok Sumedang', 12),
(40, 'Kecamatan Rancakalong', 12),
(41, 'Ciho Print Shop', 13),
(42, 'Kilau Indonesia Cabang Sumedang', 13),
(43, 'Akbar Photo', 13),
(44, 'Kantor Pertanahan (ATRI BPN)', 14),
(45, 'Polsek Sumedang Selatan', 14),
(46, 'Dinas Lingkungan Hidup Kehutanan (DLHK)', 15),
(47, 'Unsap FIB', 15),
(48, 'Kominfo', 16),
(49, 'Madani Printing', 16),
(50, 'Mediana Digital Foto & Digital Printing', 16),
(51, 'PAR Tv', 17),
(52, 'Pengadilan Negeri Sumedang', 17),
(53, 'Kantor Six Diary', 17),
(54, 'Jonas Photo  Jatinangor', 18),
(55, 'Dinas Tenaga Kerja dan Transmigrasi', 18),
(56, 'Erks Radio Sumedang', 18),
(57, 'Disduk Capil Sumedang', 19),
(58, 'Baznas Sumedang', 19),
(59, 'Samsat Sumedang', 19),
(60, 'SMTV', 20),
(61, 'Q photography', 20),
(62, 'Dinas Pertanian dan Ketahanan Pangan', 20),
(63, 'Badan Keuangan dan Aset Daerah', 21),
(64, 'Lapas Sumedang', 21),
(65, 'PT Anidar Wisata Persada', 21),
(66, 'Dinas Pendidikan Sumedang', 22),
(67, 'Timeless Self Photo', 22),
(68, 'BAPENDA', 22),
(69, 'Perumda Air Minum Tirta Medal Sumedang', 23),
(70, 'DPPKBP3A', 23),
(71, 'J&T Express', 23),
(72, 'PT. Kordon Putra Sinergi', 24),
(73, 'Dinas Pariwisata, Kebudayaan, Kepemudaan dan Olahraga (DISPARBUDPORA)', 24),
(74, 'Satpol PP Kabupaten Sumedang', 24),
(75, 'Dinas Perhubungan', 25),
(76, 'Konveksi Harokah', 25),
(77, 'Sumedang Ekspress', 26),
(78, 'STAI Sebelas April Sumedang', 26),
(79, 'Kelurahan Kota Kulon', 26),
(80, 'Panti Baca Cerita/Ceria Space', 27),
(81, 'Dinas Perikanan dan Peternakan', 27),
(82, 'Dinas Kehutanan', 27),
(83, 'Konveksi Bestro', 28),
(84, 'Amorpic Fotography', 28),
(85, 'PT. Sawala Inovasi Indonesia', 28),
(86, 'BPJS Kesehatan', 29),
(87, 'BPJS Ketenagakerjaan Sumedang', 29),
(88, 'Kementrian Agama', 30),
(89, 'Badan Pusat Statistik ', 30),
(90, 'PT Mencari Cinta Sejati', 0),
(91, 'PT hahahaa', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pembimbing`
--
ALTER TABLE `pembimbing`
  ADD PRIMARY KEY (`id_pembimbing`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id_settings`);

--
-- Indexes for table `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`id_siswa`);

--
-- Indexes for table `siswa_surat`
--
ALTER TABLE `siswa_surat`
  ADD PRIMARY KEY (`id_surat_siswa`);

--
-- Indexes for table `surat`
--
ALTER TABLE `surat`
  ADD PRIMARY KEY (`id_surat`);

--
-- Indexes for table `tempat_pkl`
--
ALTER TABLE `tempat_pkl`
  ADD PRIMARY KEY (`id_tempat`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pembimbing`
--
ALTER TABLE `pembimbing`
  MODIFY `id_pembimbing` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id_settings` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `siswa`
--
ALTER TABLE `siswa`
  MODIFY `id_siswa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=402;

--
-- AUTO_INCREMENT for table `siswa_surat`
--
ALTER TABLE `siswa_surat`
  MODIFY `id_surat_siswa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `surat`
--
ALTER TABLE `surat`
  MODIFY `id_surat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tempat_pkl`
--
ALTER TABLE `tempat_pkl`
  MODIFY `id_tempat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
