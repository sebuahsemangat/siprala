-- Migration: Menambahkan kolom kapasitas (int) pada tabel tempat_pkl
-- Dijalankan pada: 2026-09-19

ALTER TABLE `tempat_pkl` 
ADD COLUMN `kapasitas` INT NOT NULL DEFAULT 0 AFTER `catatan`;
