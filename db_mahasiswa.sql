-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 23, 2025 at 06:14 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_mahasiswa`
--

-- --------------------------------------------------------

--
-- Table structure for table `jurusan`
--

CREATE TABLE `jurusan` (
  `id_jurusan` int(2) NOT NULL,
  `nama_jurusan` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jurusan`
--

INSERT INTO `jurusan` (`id_jurusan`, `nama_jurusan`) VALUES
(1, 'Fakultas Teknik'),
(2, 'Fakultas Ekonomi Bisnis'),
(3, 'Fakultas Ilmu Komputer'),
(4, 'Fakultas Ilmu Sosial dan Ilmu Politik');

-- --------------------------------------------------------

--
-- Table structure for table `mahasiswa`
--

CREATE TABLE `mahasiswa` (
  `nim` varchar(9) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `tgl_lahir` date DEFAULT NULL,
  `alamat` varchar(150) DEFAULT NULL,
  `agama` varchar(1) DEFAULT NULL COMMENT 'A=Islam, B=Kristen, C=Katolik, D=Hindu, E=Buddha, F=Konghucu',
  `no_hp` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `id_prodi` int(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mahasiswa`
--

INSERT INTO `mahasiswa` (`nim`, `nama`, `tgl_lahir`, `alamat`, `agama`, `no_hp`, `email`, `id_prodi`) VALUES
('202300001', 'Budi Santoso', '2003-01-15', 'Jl. Merdeka No.10', 'A', '081234567890', 'budi.s@example.com', 1),
('202300002', 'Siti Aminah', '2002-05-20', 'Jl. Kebon Jeruk No.5', 'A', '085612345678', 'siti.a@example.com', 2),
('202300003', 'Joko Susilo', '2004-11-01', 'Jl. Sudirman No.20', 'C', '087890123456', 'joko.s@example.com', 1),
('202300004', 'Maria Simanjuntak', '2003-07-22', 'Jl. Thamrin No.30', 'B', '081122334455', 'maria.s@example.com', 3),
('202300005', 'Kevin Adiwijaya', '2001-09-10', 'Jl. Gatot Subroto No.15', 'E', '089988776655', 'kevin.a@example.com', 4),
('202300006', 'Putri Ayu', '2004-03-08', 'Perumahan Indah Blok C', 'A', '081345678901', 'putri.a@example.com', 5),
('202300007', 'Bayu Pranoto', '2002-12-03', 'Komplek Harapan Jaya', 'A', '085798765432', 'bayu.p@example.com', 6),
('202300008', 'Dewi Lestari', '2003-06-25', 'Jl. Pahlawan No.8', 'B', '081901234567', 'dewi.l@example.com', 7),
('202300009', 'Rizky Fadillah', '2005-01-01', 'Gg. Melati No.2', 'A', '081212345678', 'rizky.f@example.com', 8),
('202300010', 'Cindy Aprilia', '2002-04-18', 'Kavling Baru Blok A-5', 'C', '087765432109', 'cindy.a@example.com', 9);

-- --------------------------------------------------------

--
-- Table structure for table `prodi`
--

CREATE TABLE `prodi` (
  `id_prodi` int(2) NOT NULL,
  `nama_prodi` varchar(40) NOT NULL,
  `id_jurusan` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prodi`
--

INSERT INTO `prodi` (`id_prodi`, `nama_prodi`, `id_jurusan`) VALUES
(1, 'Teknik Informatika', 1),
(2, 'Sistem Informasi', 1),
(3, 'Teknik Elektro', 1),
(4, 'Manajemen', 2),
(5, 'Akuntansi', 2),
(6, 'Ekonomi Pembangunan', 2),
(7, 'Ilmu Komunikasi', 4),
(8, 'Hubungan Internasional', 4),
(9, 'Ilmu Pemerintahan', 4),
(10, 'Agribisnis', 2);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userId` varchar(30) NOT NULL,
  `passw` varchar(100) NOT NULL,
  `nama` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userId`, `passw`, `nama`) VALUES
('admin', 'admin', 'Administrator Sistem'),
('user', '$2y$10$Q7wXwY.y.5hM9T0V0O1E7u7r6s.t8.u9x.vA2C3D4E5F6G7H8I9', 'User Biasa');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `jurusan`
--
ALTER TABLE `jurusan`
  ADD PRIMARY KEY (`id_jurusan`);

--
-- Indexes for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD PRIMARY KEY (`nim`),
  ADD KEY `id_prodi` (`id_prodi`);

--
-- Indexes for table `prodi`
--
ALTER TABLE `prodi`
  ADD PRIMARY KEY (`id_prodi`),
  ADD KEY `id_jurusan` (`id_jurusan`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `jurusan`
--
ALTER TABLE `jurusan`
  MODIFY `id_jurusan` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `prodi`
--
ALTER TABLE `prodi`
  MODIFY `id_prodi` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD CONSTRAINT `mahasiswa_ibfk_1` FOREIGN KEY (`id_prodi`) REFERENCES `prodi` (`id_prodi`) ON UPDATE CASCADE;

--
-- Constraints for table `prodi`
--
ALTER TABLE `prodi`
  ADD CONSTRAINT `prodi_ibfk_1` FOREIGN KEY (`id_jurusan`) REFERENCES `jurusan` (`id_jurusan`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
