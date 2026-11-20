CREATE DATABASE IF NOT EXISTS db_mahasiswa
    DEFAULT CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE db_mahasiswa;

DROP TABLE IF EXISTS mahasiswa;

CREATE TABLE mahasiswa (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    nim VARCHAR(20) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    alamat TEXT NOT NULL,
    prodi VARCHAR(100) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_mahasiswa_nim (nim)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

INSERT INTO mahasiswa (nim, nama, alamat, prodi) VALUES
('20.51.001', 'Muhammad Baba', 'Tarakan, Kalimantan Utara', 'S1 - Teknik Informatika'),
('10.51.002', 'Syahida Arsyma', 'Juata Laut', 'S1 - Teknik Informatika'),
('10.50.001', 'Muhammad Uwais Al Qarni', 'Tarakan, Kalimantan Utara', 'S1 - Sistem Informasi'),
('10.50.013', 'Muhammad Hafid', 'Tarakan', 'S1 - Sistem Informasi');
