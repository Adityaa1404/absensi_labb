-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 27, 2026 at 02:17 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `absensi_lab`
--

-- --------------------------------------------------------

--
-- Table structure for table `absensi`
--

CREATE TABLE `absensi` (
  `id_absensi` int NOT NULL,
  `plotting_id` int NOT NULL,
  `tanggal` date NOT NULL,
  `pertemuan_ke` int DEFAULT NULL,
  `jam_mulai` time DEFAULT NULL,
  `jam_selesai` time DEFAULT NULL,
  `deskripsi_tugas` text NOT NULL,
  `foto_kegiatan` varchar(255) NOT NULL,
  `foto_selfie` varchar(255) NOT NULL,
  `status_verifikasi` enum('pending','disetujui','ditolak') DEFAULT 'pending',
  `pesan_dosen` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mata_kuliah`
--

CREATE TABLE `mata_kuliah` (
  `id_matkul` int NOT NULL,
  `nama_matkul` varchar(100) NOT NULL,
  `deskripsi` text,
  `dosen_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mata_kuliah`
--

INSERT INTO `mata_kuliah` (`id_matkul`, `nama_matkul`, `deskripsi`, `dosen_id`, `created_at`) VALUES
(3, 'BP2', NULL, 14, '2026-08-27 13:19:28');

-- --------------------------------------------------------

--
-- Table structure for table `plotting`
--

CREATE TABLE `plotting` (
  `id_plotting` int NOT NULL,
  `matkul_id` int NOT NULL,
  `asdos_id` int NOT NULL,
  `periode_mulai` date NOT NULL,
  `periode_selesai` date NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `plotting`
--

INSERT INTO `plotting` (`id_plotting`, `matkul_id`, `asdos_id`, `periode_mulai`, `periode_selesai`, `is_active`, `created_at`) VALUES
(3, 3, 2, '2026-08-27', '2027-02-28', 1, '2026-08-27 13:19:46'),
(4, 3, 11, '2026-08-27', '2027-02-27', 1, '2026-08-27 13:20:09');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int NOT NULL,
  `nama` varchar(50) DEFAULT NULL,
  `identity_number` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('dosen','asdos','super_admin') NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `email` varchar(80) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `nama`, `identity_number`, `password`, `role`, `is_active`, `created_at`, `email`, `no_hp`) VALUES
(2, 'AAN', '25082010046', '$2y$10$H.RRD/h4c1BVnLSUA5AXROIfNapwRydWPQ5MC44e9RMWera.lgbnG', 'asdos', 1, '2026-08-09 17:06:27', NULL, NULL),
(3, 'ola', '25082010001', '$2y$10$C7Cbi2D82w9CGeQGYBxz9eeaBPC5tLohkvdJe0SPA3kbn4l9EGB8e', 'asdos', 1, '2026-08-11 14:41:38', 'ola@gmail.com', '08123456789'),
(11, 'halo', '25082010100', '$2y$10$wLqnthPvvwwplRte9Gc.B.e5H77ZhfOTWTyrOjck/wAOM5MU33.Be', 'asdos', 1, '2026-08-11 14:55:57', 'WinNoLimitz@gmail.com', '08123456789'),
(13, 'a', '1', '$2y$10$dxfR37P1ztxWwS9tkEeuNezTDgAL3THC6Etxz.y6D2KlqeWBDRtW2', 'dosen', 1, '2026-08-11 15:04:51', 'cozuu101@edumail.edu.rs', '123'),
(14, 'aaaa', '25082010111', '$2y$10$ONcGtrQY5xIqAZI1SSrf9exFWJSeY12fyQR8ueOW8HoxGPn0AfZmm', 'dosen', 1, '2026-08-11 15:19:07', '25082010046@student.upnjatim.ac.id', '5555'),
(16, 'Super Admin Lab', 'admin', '$2y$10$hPMwo9tp4OmobwNsvl6sd.VlkKgHwyuKxNMplrQ7ktlAK.a8dep0C', 'super_admin', 1, '2026-08-24 08:32:47', 'admin@labsi.ac.id', '08123456789');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `absensi`
--
ALTER TABLE `absensi`
  ADD PRIMARY KEY (`id_absensi`),
  ADD KEY `fk_absensi_plotting` (`plotting_id`);

--
-- Indexes for table `mata_kuliah`
--
ALTER TABLE `mata_kuliah`
  ADD PRIMARY KEY (`id_matkul`),
  ADD KEY `dosen_id` (`dosen_id`);

--
-- Indexes for table `plotting`
--
ALTER TABLE `plotting`
  ADD PRIMARY KEY (`id_plotting`),
  ADD UNIQUE KEY `uniq_plot` (`matkul_id`,`asdos_id`),
  ADD KEY `asdos_id` (`asdos_id`),
  ADD KEY `idx_matkul` (`matkul_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `FK` (`identity_number`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `absensi`
--
ALTER TABLE `absensi`
  MODIFY `id_absensi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `mata_kuliah`
--
ALTER TABLE `mata_kuliah`
  MODIFY `id_matkul` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `plotting`
--
ALTER TABLE `plotting`
  MODIFY `id_plotting` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `absensi`
--
ALTER TABLE `absensi`
  ADD CONSTRAINT `fk_absensi_plotting` FOREIGN KEY (`plotting_id`) REFERENCES `plotting` (`id_plotting`) ON DELETE CASCADE;

--
-- Constraints for table `mata_kuliah`
--
ALTER TABLE `mata_kuliah`
  ADD CONSTRAINT `mata_kuliah_ibfk_1` FOREIGN KEY (`dosen_id`) REFERENCES `users` (`id_user`);

--
-- Constraints for table `plotting`
--
ALTER TABLE `plotting`
  ADD CONSTRAINT `plotting_ibfk_1` FOREIGN KEY (`matkul_id`) REFERENCES `mata_kuliah` (`id_matkul`),
  ADD CONSTRAINT `plotting_ibfk_2` FOREIGN KEY (`asdos_id`) REFERENCES `users` (`id_user`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
