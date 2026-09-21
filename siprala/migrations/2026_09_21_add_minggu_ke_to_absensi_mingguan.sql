-- Migration: Menambahkan kolom minggu_ke pada tabel absensi_mingguan
-- Dijalankan pada: 2026-09-21

ALTER TABLE `absensi_mingguan` 
ADD COLUMN `minggu_ke` INT NOT NULL DEFAULT 1 AFTER `id_tempat`;
