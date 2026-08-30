-- MySQL dump 10.13  Distrib 8.0.30, for Win64 (x86_64)
--
-- Host: localhost    Database: absensi_lab
-- ------------------------------------------------------
-- Server version	8.0.30

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `absensi_lab`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `absensi_lab` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `absensi_lab`;

--
-- Table structure for table `absensi`
--

DROP TABLE IF EXISTS `absensi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `absensi` (
  `id_absensi` int NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_absensi`),
  KEY `fk_absensi_plotting` (`plotting_id`),
  CONSTRAINT `fk_absensi_plotting` FOREIGN KEY (`plotting_id`) REFERENCES `plotting` (`id_plotting`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `absensi`
--

LOCK TABLES `absensi` WRITE;
/*!40000 ALTER TABLE `absensi` DISABLE KEYS */;
INSERT INTO `absensi` VALUES (7,5,'2026-08-30',1,'18:32:00','19:32:00','gacor','843e5e0576bc0bd77b9064b6108b75df.jpg','526189f5ddcd5093ecbd3c7f877c3664.jpg','disetujui',NULL,'2026-08-30 11:34:28','2026-08-30 11:36:25'),(8,7,'2026-08-30',1,'19:16:00','20:16:00','tes doang si','07e4c484aa80a6c6ff327608cd9bd41a.jpg','cc57e0129212611d5878437c804765d9.jpg','pending',NULL,'2026-08-30 12:17:14',NULL);
/*!40000 ALTER TABLE `absensi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mata_kuliah`
--

DROP TABLE IF EXISTS `mata_kuliah`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mata_kuliah` (
  `id_matkul` int NOT NULL AUTO_INCREMENT,
  `nama_matkul` varchar(100) NOT NULL,
  `deskripsi` text,
  `dosen_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_matkul`),
  KEY `dosen_id` (`dosen_id`),
  CONSTRAINT `mata_kuliah_ibfk_1` FOREIGN KEY (`dosen_id`) REFERENCES `users` (`id_user`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mata_kuliah`
--

LOCK TABLES `mata_kuliah` WRITE;
/*!40000 ALTER TABLE `mata_kuliah` DISABLE KEYS */;
INSERT INTO `mata_kuliah` VALUES (3,'BP2 - Paralel B',NULL,13,'2026-08-27 13:19:28'),(4,'ADSI - Paralel D','aaaaa',14,'2026-08-30 10:41:55');
/*!40000 ALTER TABLE `mata_kuliah` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plotting`
--

DROP TABLE IF EXISTS `plotting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `plotting` (
  `id_plotting` int NOT NULL AUTO_INCREMENT,
  `matkul_id` int NOT NULL,
  `asdos_id` int NOT NULL,
  `periode_mulai` date NOT NULL,
  `periode_selesai` date NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_plotting`),
  UNIQUE KEY `uniq_plot` (`matkul_id`,`asdos_id`),
  KEY `asdos_id` (`asdos_id`),
  KEY `idx_matkul` (`matkul_id`),
  CONSTRAINT `plotting_ibfk_1` FOREIGN KEY (`matkul_id`) REFERENCES `mata_kuliah` (`id_matkul`),
  CONSTRAINT `plotting_ibfk_2` FOREIGN KEY (`asdos_id`) REFERENCES `users` (`id_user`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plotting`
--

LOCK TABLES `plotting` WRITE;
/*!40000 ALTER TABLE `plotting` DISABLE KEYS */;
INSERT INTO `plotting` VALUES (5,3,3,'2026-08-29','2026-11-24',0,'2026-08-29 05:31:29'),(7,4,3,'2026-08-30','2027-03-02',1,'2026-08-30 11:53:04');
/*!40000 ALTER TABLE `plotting` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id_user` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(50) DEFAULT NULL,
  `identity_number` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('dosen','asdos','super_admin') NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `email` varchar(80) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `FK` (`identity_number`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (3,'ola','25082010001','$2y$10$FnrN3ck8u3ts4c3cecVXSu0wYv06JZoi1Qc7zTWTqISwa4nPKP3pi','asdos',1,'2026-08-11 14:41:38','ola@gmail.com','08123456789'),(11,'halo','25082010100','$2y$10$wLqnthPvvwwplRte9Gc.B.e5H77ZhfOTWTyrOjck/wAOM5MU33.Be','asdos',1,'2026-08-11 14:55:57','WinNoLimitz@gmail.com','08123456789'),(13,'pak cahyo','121','$2y$10$aUrVkL8NOOhH0QAhMskXYefEZiUZMgnhRx9DvhPxAzDDTfTABcqIi','dosen',1,'2026-08-11 15:04:51','cozuu101@edumail.edu.rs','123'),(14,'aaaa','25082010111','$2y$10$ONcGtrQY5xIqAZI1SSrf9exFWJSeY12fyQR8ueOW8HoxGPn0AfZmm','dosen',1,'2026-08-11 15:19:07','25082010046@student.upnjatim.ac.id','5555'),(16,'Super Admin Lab','admin','$2y$10$hPMwo9tp4OmobwNsvl6sd.VlkKgHwyuKxNMplrQ7ktlAK.a8dep0C','super_admin',1,'2026-08-24 08:32:47','admin@labsi.ac.id','08123456789');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'absensi_lab'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-08-30 20:03:07
