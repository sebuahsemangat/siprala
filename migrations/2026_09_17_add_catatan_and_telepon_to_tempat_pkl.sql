-- Migration: Menambahkan field catatan (text) dan no_telepon (varchar) pada tabel tempat_pkl
-- Dijalankan pada: 2026-09-17

ALTER TABLE `tempat_pkl` 
ADD COLUMN `no_telepon` VARCHAR(25) NULL DEFAULT NULL AFTER `kota`,
ADD COLUMN `catatan` TEXT NULL DEFAULT NULL AFTER `no_telepon`;
