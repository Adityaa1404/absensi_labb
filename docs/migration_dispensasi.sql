-- Migration SQL: Fitur Surat Dispensasi & Settings Institusi
-- Dibuat untuk Sistem Informasi Absensi & Penugasan Asisten Laboratorium

CREATE TABLE IF NOT EXISTS `settings` (
    `id_setting` INT AUTO_INCREMENT PRIMARY KEY,
    `setting_key` VARCHAR(100) NOT NULL UNIQUE,
    `setting_value` TEXT NULL,
    `setting_label` VARCHAR(255) NULL COMMENT 'Label ramah pengguna untuk UI',
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed Data Konfigurasi Awal
INSERT IGNORE INTO `settings` (`setting_key`, `setting_value`, `setting_label`) VALUES
('institusi_nama', 'UNIVERSITAS PEMBANGUNAN NASIONAL "VETERAN" JAWA TIMUR', 'Nama Institusi'),
('institusi_himpunan', 'HIMPUNAN MAHASISWA SISTEM INFORMASI', 'Nama Himpunan/Organisasi'),
('institusi_fakultas', 'FAKULTAS ILMU KOMPUTER', 'Nama Fakultas'),
('institusi_alamat', 'Jalan Raya Rungkut Madya, Gunung Anyar, Surabaya, 60294', 'Alamat Institusi'),
('institusi_email', 'himasifo.fasilkom@upnjatim.ac.id', 'Email Institusi'),
('institusi_website', 'http://himasifo.fasilkom.upnjatim.ac.id/', 'Website Institusi'),
('institusi_kota', 'Surabaya', 'Kota Institusi'),
('kaprodi_nama', 'Siti Mukaromah, S.Kom, M.Kom', 'Nama Koordinator Prodi'),
('kaprodi_nip', '19810704 2021212 011', 'NIP Koordinator Prodi'),
('kaprodi_jabatan', 'Koordinator Program Studi Sistem Informasi', 'Jabatan Koordinator Prodi');

CREATE TABLE IF NOT EXISTS `dispensasi` (
    `id_dispensasi` INT AUTO_INCREMENT PRIMARY KEY,
    `plotting_id` INT NOT NULL,
    `nomor_surat` VARCHAR(100) NOT NULL,
    `tanggal_pelaksanaan` DATE NOT NULL,
    `tujuan_surat` VARCHAR(500) NOT NULL COMMENT 'Input manual: kepada siapa surat ditujukan',
    `tempat_pelaksanaan` VARCHAR(500) NOT NULL COMMENT 'Input manual: tempat pelaksanaan mengajar',
    `jenis_kegiatan` ENUM('magang', 'perkuliahan') NOT NULL DEFAULT 'magang' COMMENT 'Jenis izin: kegiatan magang atau perkuliahan',
    `status` ENUM('menunggu', 'disetujui', 'ditolak') NOT NULL DEFAULT 'menunggu',
    `catatan_dosen` TEXT NULL COMMENT 'Wajib diisi jika dosen menolak',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX `idx_dispensasi_plotting` (`plotting_id`),
    INDEX `idx_dispensasi_status` (`status`),

    CONSTRAINT `fk_dispensasi_plotting`
        FOREIGN KEY (`plotting_id`) REFERENCES `plotting`(`id_plotting`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
