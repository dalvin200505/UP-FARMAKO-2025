-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 22, 2025 at 04:47 AM
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
-- Database: `tiket`
--

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id_pembayaran` int NOT NULL,
  `id_pemesanan` int DEFAULT NULL,
  `metode_pembayaran` varchar(50) DEFAULT NULL,
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `tanggal_pembayaran` datetime DEFAULT NULL,
  `status` varchar(20) DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pemesanan`
--

CREATE TABLE `pemesanan` (
  `id_pemesanan` int NOT NULL,
  `id_user` int NOT NULL,
  `id_rute` int NOT NULL,
  `tanggal_pemesanan` datetime NOT NULL,
  `status` enum('pending','berhasil','batal') NOT NULL,
  `username` varchar(255) NOT NULL,
  `status_pembayaran` varchar(50) DEFAULT 'Menunggu Konfirmasi',
  `status_pembayaran_lama` varchar(50) DEFAULT NULL,
  `jumlah_tiket` int NOT NULL,
  `bukti_pembayaran` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pemesanan`
--

INSERT INTO `pemesanan` (`id_pemesanan`, `id_user`, `id_rute`, `tanggal_pemesanan`, `status`, `username`, `status_pembayaran`, `status_pembayaran_lama`, `jumlah_tiket`, `bukti_pembayaran`) VALUES
(43, 9, 6, '2025-04-20 08:51:44', 'berhasil', 'jahra', 'Terkonfirmasi', 'Menunggu Konfirmasi', 1, 'bukti_1745139139.jpg'),
(44, 8, 2, '2025-04-20 09:36:38', 'berhasil', 'epul', 'Terkonfirmasi', 'Menunggu Konfirmasi', 1, 'bukti_1745141821.jpg'),
(45, 10, 4, '2025-04-20 09:44:24', 'berhasil', 'fajar', 'Terkonfirmasi', 'Menunggu Konfirmasi', 1, 'bukti_1745142325.png'),
(46, 10, 5, '2025-04-20 14:41:45', 'berhasil', 'fajar', 'pending', 'Menunggu Konfirmasi', 1, 'bukti_1745160134.jpeg'),
(47, 15, 7, '2025-04-21 04:06:48', 'berhasil', 'penumpang', 'Terkonfirmasi', 'Terkonfirmasi', 1, 'bukti_1745208470.pdf'),
(48, 9, 1, '2025-04-21 04:53:06', 'batal', 'jahra', 'Menunggu Konfirmasi', NULL, 1, NULL),
(49, 9, 3, '2025-04-21 05:01:59', 'pending', 'jahra', 'Terkonfirmasi', 'Menunggu Konfirmasi', 1, 'bukti_1745213723.pdf'),
(50, 16, 7, '2025-04-21 06:20:38', 'pending', 'rafa', 'Lunas', NULL, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `penumpang`
--

CREATE TABLE `penumpang` (
  `id_penumpang` int NOT NULL,
  `id_pemesanan` int NOT NULL,
  `nama_penumpang` varchar(255) NOT NULL,
  `no_identitas` varchar(50) NOT NULL,
  `kursi` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `penumpang`
--

INSERT INTO `penumpang` (`id_penumpang`, `id_pemesanan`, `nama_penumpang`, `no_identitas`, `kursi`) VALUES
(43, 43, 'jahra', '223311', '1'),
(44, 44, 'epul', '3333333333', '1'),
(45, 45, 'fajar', '7654321', '1'),
(46, 46, 'fajar', '7654321', '2'),
(47, 47, 'andrean', '1122332', '12'),
(48, 48, 'jahra', '1122332', '14'),
(49, 49, 'jahra', '1122332', '14'),
(50, 50, 'rafa', '11112233', '16');

-- --------------------------------------------------------

--
-- Table structure for table `riwayat_pembayaran`
--

CREATE TABLE `riwayat_pembayaran` (
  `id_riwayat` int NOT NULL,
  `id_pemesanan` int NOT NULL,
  `status_pembayaran` varchar(50) NOT NULL,
  `tanggal_update` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `riwayat_pembayaran`
--

INSERT INTO `riwayat_pembayaran` (`id_riwayat`, `id_pemesanan`, `status_pembayaran`, `tanggal_update`) VALUES
(14, 43, 'Terkonfirmasi', '2025-04-20 09:24:58'),
(15, 44, 'Terkonfirmasi', '2025-04-20 09:38:00'),
(16, 45, 'Terkonfirmasi', '2025-04-20 09:56:56'),
(18, 47, 'Terkonfirmasi', '2025-04-21 04:12:44'),
(19, 47, 'Terkonfirmasi', '2025-04-21 04:12:48'),
(20, 49, 'Terkonfirmasi', '2025-04-21 06:28:02');

-- --------------------------------------------------------

--
-- Table structure for table `rute_transportasi`
--

CREATE TABLE `rute_transportasi` (
  `id_rute` int NOT NULL,
  `id_transportasi` int NOT NULL,
  `asal` varchar(255) NOT NULL,
  `tujuan` varchar(255) NOT NULL,
  `tanggal_keberangkatan` date NOT NULL,
  `waktu_keberangkatan` time NOT NULL,
  `harga` varchar(255) NOT NULL,
  `kelas` enum('Ekonomi','Bisnis','Vip') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `rute_transportasi`
--

INSERT INTO `rute_transportasi` (`id_rute`, `id_transportasi`, `asal`, `tujuan`, `tanggal_keberangkatan`, `waktu_keberangkatan`, `harga`, `kelas`) VALUES
(1, 4, 'Jakarta', 'Bali/Denpasar', '2025-04-17', '12:10:00', '145000', 'Ekonomi'),
(2, 2, 'Bandung (BDG-A)', 'Semarang Poncol (SMC)', '2025-04-17', '16:11:00', '2500000', 'Bisnis'),
(3, 3, 'Bogor', 'Surabaya', '2025-04-21', '11:11:00', '500000', 'Ekonomi'),
(4, 5, 'Gambir', 'Yogyakarta', '2025-04-20', '08:52:00', '750000', 'Bisnis'),
(5, 6, 'solo', 'Yogyakarta', '2025-04-20', '08:55:00', '750000', 'Ekonomi'),
(6, 7, 'Bogor', 'Semarang ', '2025-04-21', '12:47:00', '600000', 'Bisnis'),
(7, 3, 'Bogor', 'Surabaya', '2025-04-21', '11:11:00', '700000', 'Bisnis'),
(8, 3, 'Bogor', 'Surabaya', '2025-04-21', '11:11:00', '800000', 'Vip'),
(9, 7, 'Bogor', 'Semarang ', '2025-04-22', '12:47:00', '400000', 'Ekonomi'),
(10, 7, 'Bogor', 'Semarang ', '2025-04-21', '12:47:00', '700000', 'Vip'),
(12, 10, 'Bogor', 'Semarang ', '2025-04-21', '14:18:00', '500', 'Ekonomi');

-- --------------------------------------------------------

--
-- Table structure for table `transportasi`
--

CREATE TABLE `transportasi` (
  `id_transportasi` int NOT NULL,
  `nama` varchar(255) NOT NULL,
  `jenis_transportasi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `kapasitas` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `transportasi`
--

INSERT INTO `transportasi` (`id_transportasi`, `nama`, `jenis_transportasi`, `kapasitas`) VALUES
(2, 'kereta KAI', 'kereta', 100),
(3, 'Parahyangan', 'kereta', 150),
(4, 'Jayakarta', 'kereta', 1500),
(5, 'Taksaka', 'kereta', 600),
(6, 'Argo Lawu', 'Kereta', 600),
(7, 'Argo Kencana', 'kereta', 600),
(10, 'Kereta  UKK', 'pesawat', 125);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int NOT NULL,
  `nama` varchar(255) NOT NULL,
  `email` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `alamat` text,
  `role` enum('admin','petugas','pemesan') NOT NULL,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `nama`, `email`, `username`, `password`, `alamat`, `role`, `foto`) VALUES
(2, 'petugas', 'petugas@gmail.com', 'petugas', '$2y$10$s2RrtrnYDdj6IjxLsXkDl.mI3pQHM.EDrgWVbkCEOPKSfxdTmKMBu', 'cimande bogor', 'petugas', NULL),
(3, 'dalvin', 'dalvinkandiyas@gmail.com', 'dalvin', '$2y$10$CDBG5fPNZxMl6Bc1iptGy.RCRyy4WSnoF8lYsDkwCc.ro6Ng5m7Zm', 'cisalopa', 'admin', NULL),
(4, 'pemesan', 'pemesan@gmail.com', 'pemesan', '$2y$10$mpFvO2EQA3DKSpR04LtzXukxzA2nISCG8d8rWn5f0Wwuavy5Rmwby', 'ciherang city', 'pemesan', NULL),
(6, 'reyhan', 'reyhan@gmail.com', 'reyhan', '$2y$10$EYOACxEkduUeIbRQcNdi3Om2X4ouVIS4tlDrfRicgDsr6.ae0K3p.', 'ciherang city', 'pemesan', '6801c35921775.jpg'),
(7, 'admin', 'admin@gmail.com', 'admin1', '$2y$10$t31vZOd7Dy2UQSIcOpRRrurBw3C4U.LKLzhrufhPnVaq9CM480KtO', 'cimande bogor', 'admin', NULL),
(8, 'epul', 'epul@gmail.com', 'epul', '$2y$10$sIk4WbXSCAI5f.fLGvoD6u7x2rp4fYA4djphqVULN6oQxzU/7MCa2', 'kp.cimande', 'pemesan', NULL),
(9, 'jahra', 'jahra@gmail.com', 'jahra', '$2y$10$7Eud7rCGo0FUgAuWSf0YQeijLaRTWQUPfgzZEF0HZ6LjeIHL1hMZ2', 'cimande bogor', 'pemesan', '6805dbd620967.jpg'),
(10, 'fajar', 'fajar@gmail.com', 'fajar', '$2y$10$MCzmVc3LrtlxzJ7IRsX0i.oeigi4JprnIXNlCGTUuwBHpCfmGayt2', 'ciherang city', 'pemesan', NULL),
(15, 'Penumpang', 'penumpang@gmail.com', 'penumpang', '$2y$10$xHJAz/tyFTIR2Ig0gOYNauqXqpW8cLO823WQ7g0X.y7ePY1PXQG6O', 'cianjur', 'pemesan', '6805c2d9c4b43.jpg'),
(16, 'rafa', 'rafa@gmail.com', 'rafa', '$2y$10$j6GT5G0Phm.4G.2f7t7pMuUgqiJ7QFcEHxNseO1U6DhOlpa18Ea6y', 'kp.cimande', 'pemesan', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD KEY `id_pemesanan` (`id_pemesanan`);

--
-- Indexes for table `pemesanan`
--
ALTER TABLE `pemesanan`
  ADD PRIMARY KEY (`id_pemesanan`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_rute` (`id_rute`);

--
-- Indexes for table `penumpang`
--
ALTER TABLE `penumpang`
  ADD PRIMARY KEY (`id_penumpang`),
  ADD KEY `id_pemesanan` (`id_pemesanan`);

--
-- Indexes for table `riwayat_pembayaran`
--
ALTER TABLE `riwayat_pembayaran`
  ADD PRIMARY KEY (`id_riwayat`),
  ADD KEY `id_pemesanan` (`id_pemesanan`);

--
-- Indexes for table `rute_transportasi`
--
ALTER TABLE `rute_transportasi`
  ADD PRIMARY KEY (`id_rute`),
  ADD KEY `id_transportasi` (`id_transportasi`);

--
-- Indexes for table `transportasi`
--
ALTER TABLE `transportasi`
  ADD PRIMARY KEY (`id_transportasi`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_pembayaran` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pemesanan`
--
ALTER TABLE `pemesanan`
  MODIFY `id_pemesanan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `penumpang`
--
ALTER TABLE `penumpang`
  MODIFY `id_penumpang` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `riwayat_pembayaran`
--
ALTER TABLE `riwayat_pembayaran`
  MODIFY `id_riwayat` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `rute_transportasi`
--
ALTER TABLE `rute_transportasi`
  MODIFY `id_rute` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `transportasi`
--
ALTER TABLE `transportasi`
  MODIFY `id_transportasi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`id_pemesanan`) REFERENCES `pemesanan` (`id_pemesanan`);

--
-- Constraints for table `pemesanan`
--
ALTER TABLE `pemesanan`
  ADD CONSTRAINT `pemesanan_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`),
  ADD CONSTRAINT `pemesanan_ibfk_2` FOREIGN KEY (`id_rute`) REFERENCES `rute_transportasi` (`id_rute`);

--
-- Constraints for table `penumpang`
--
ALTER TABLE `penumpang`
  ADD CONSTRAINT `penumpang_ibfk_1` FOREIGN KEY (`id_pemesanan`) REFERENCES `pemesanan` (`id_pemesanan`);

--
-- Constraints for table `riwayat_pembayaran`
--
ALTER TABLE `riwayat_pembayaran`
  ADD CONSTRAINT `riwayat_pembayaran_ibfk_1` FOREIGN KEY (`id_pemesanan`) REFERENCES `pemesanan` (`id_pemesanan`);

--
-- Constraints for table `rute_transportasi`
--
ALTER TABLE `rute_transportasi`
  ADD CONSTRAINT `rute_transportasi_ibfk_1` FOREIGN KEY (`id_transportasi`) REFERENCES `transportasi` (`id_transportasi`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
