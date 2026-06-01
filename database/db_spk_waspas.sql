-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 08, 2025 at 04:52 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 7.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_spk_waspas`
--

-- --------------------------------------------------------

--
-- Table structure for table `karyawan`
--

CREATE TABLE `karyawan` (
  `id_karyawan` int(11) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `no_telp` varchar(15) NOT NULL,
  `alamat` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `karyawan`
--

INSERT INTO `karyawan` (`id_karyawan`, `nama`, `no_telp`, `alamat`) VALUES
(1, 'Fajar widanto', '08231', 'Kebumen'),
(2, 'Mifta ismanto', '089', 'asdas'),
(3, 'Ahmad zaki', '089', 'sdas'),
(4, 'Muhamad azriel', '089', 'sdad'),
(5, 'Abi humayroh', '089', 'asdas'),
(6, 'Muhamad vijay', '089', 'das'),
(7, 'Virgiawan ilyasa', '089', 'adsfsdf'),
(8, 'Ary valentino ', '089', 'asdas'),
(9, 'Achmad jalalulail', '089', 'asdfsdf'),
(10, 'Muhamad fazri', '089', 'sdfsd');

-- --------------------------------------------------------

--
-- Table structure for table `kriteria`
--

CREATE TABLE `kriteria` (
  `id_kriteria` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `nm_kriteria` varchar(50) NOT NULL,
  `bobot` int(11) NOT NULL,
  `status` enum('Benefit','Cost') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `kriteria`
--

INSERT INTO `kriteria` (`id_kriteria`, `id_user`, `nm_kriteria`, `bobot`, `status`) VALUES
(1, 1, 'absensi', 10, 'Cost'),
(2, 1, 'Lama bekerja ', 15, 'Benefit'),
(3, 1, 'Skill', 20, 'Benefit'),
(4, 1, 'Kerja sama team', 25, 'Benefit'),
(5, 1, 'Hasil kerja', 30, 'Benefit');

-- --------------------------------------------------------

--
-- Table structure for table `penilaian`
--

CREATE TABLE `penilaian` (
  `id_penilaian` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_karyawan` int(11) NOT NULL,
  `id_kriteria` int(11) NOT NULL,
  `id_sub_kriteria` int(11) NOT NULL,
  `periode` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `penilaian`
--

INSERT INTO `penilaian` (`id_penilaian`, `id_user`, `id_karyawan`, `id_kriteria`, `id_sub_kriteria`, `periode`) VALUES
(71, 1, 1, 1, 52, '2025-08-08'),
(72, 1, 1, 2, 55, '2025-08-08'),
(73, 1, 1, 3, 58, '2025-08-08'),
(74, 1, 1, 4, 64, '2025-08-08'),
(75, 1, 1, 5, 68, '2025-08-08'),
(76, 1, 2, 1, 53, '2025-08-08'),
(77, 1, 2, 2, 55, '2025-08-08'),
(78, 1, 2, 3, 58, '2025-08-08'),
(79, 1, 2, 4, 64, '2025-08-08'),
(80, 1, 2, 5, 69, '2025-08-08'),
(81, 1, 3, 1, 53, '2025-08-08'),
(82, 1, 3, 2, 56, '2025-08-08'),
(83, 1, 3, 3, 58, '2025-08-08'),
(84, 1, 3, 4, 63, '2025-08-08'),
(85, 1, 3, 5, 69, '2025-08-08'),
(86, 1, 4, 1, 52, '2025-08-08'),
(87, 1, 4, 2, 56, '2025-08-08'),
(88, 1, 4, 3, 58, '2025-08-08'),
(89, 1, 4, 4, 64, '2025-08-08'),
(90, 1, 4, 5, 69, '2025-08-08'),
(91, 1, 5, 1, 53, '2025-08-08'),
(92, 1, 5, 2, 56, '2025-08-08'),
(93, 1, 5, 3, 58, '2025-08-08'),
(94, 1, 5, 4, 62, '2025-08-08'),
(95, 1, 5, 5, 68, '2025-08-08'),
(96, 1, 6, 1, 53, '2025-08-08'),
(97, 1, 6, 2, 56, '2025-08-08'),
(98, 1, 6, 3, 58, '2025-08-08'),
(99, 1, 6, 4, 63, '2025-08-08'),
(100, 1, 6, 5, 68, '2025-08-08'),
(101, 1, 7, 1, 52, '2025-08-08'),
(102, 1, 7, 2, 56, '2025-08-08'),
(103, 1, 7, 3, 59, '2025-08-08'),
(104, 1, 7, 4, 65, '2025-08-08'),
(105, 1, 7, 5, 70, '2025-08-08'),
(106, 1, 8, 1, 53, '2025-08-08'),
(107, 1, 8, 2, 55, '2025-08-08'),
(108, 1, 8, 3, 58, '2025-08-08'),
(109, 1, 8, 4, 63, '2025-08-08'),
(110, 1, 8, 5, 68, '2025-08-08'),
(111, 1, 9, 1, 52, '2025-08-08'),
(112, 1, 9, 2, 55, '2025-08-08'),
(113, 1, 9, 3, 58, '2025-08-08'),
(114, 1, 9, 4, 64, '2025-08-08'),
(115, 1, 9, 5, 70, '2025-08-08'),
(116, 1, 10, 1, 53, '2025-08-08'),
(117, 1, 10, 2, 55, '2025-08-08'),
(118, 1, 10, 3, 58, '2025-08-08'),
(119, 1, 10, 4, 63, '2025-08-08'),
(120, 1, 10, 5, 67, '2025-08-08');

-- --------------------------------------------------------

--
-- Table structure for table `sub_kriteria`
--

CREATE TABLE `sub_kriteria` (
  `id_sub_kriteria` int(11) NOT NULL,
  `id_kriteria` int(11) NOT NULL,
  `keterangan` varchar(70) NOT NULL,
  `nilai` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `sub_kriteria`
--

INSERT INTO `sub_kriteria` (`id_sub_kriteria`, `id_kriteria`, `keterangan`, `nilai`) VALUES
(52, 1, 'selalu masuk', 1),
(53, 1, 'tidak masuk 1-2 hari', 2),
(54, 1, 'tidak masuk 3-4 hari ', 3),
(55, 2, '0 - 1 tahun', 1),
(56, 2, '1-3 tahun', 2),
(57, 2, '3 - 5 tahun', 3),
(58, 3, 'mengoperasikan microsoft word', 1),
(59, 3, 'mengoperasikan micosoft excel', 2),
(60, 3, 'mengoperasikan online shop', 3),
(61, 4, 'sangat buruk', 1),
(62, 4, 'buruk', 2),
(63, 4, 'cukup', 3),
(64, 4, 'baik', 4),
(65, 4, 'sangat baik', 5),
(66, 5, 'sangat buruk', 1),
(67, 5, 'buruk', 2),
(68, 5, 'cukup', 3),
(69, 5, 'baik', 4),
(70, 5, 'sangat baik', 5);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `username` varchar(16) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `nama` varchar(70) DEFAULT NULL,
  `email` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `username`, `password`, `nama`, `email`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'admin', 'admin@gmail.com');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `karyawan`
--
ALTER TABLE `karyawan`
  ADD PRIMARY KEY (`id_karyawan`);

--
-- Indexes for table `kriteria`
--
ALTER TABLE `kriteria`
  ADD PRIMARY KEY (`id_kriteria`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `penilaian`
--
ALTER TABLE `penilaian`
  ADD PRIMARY KEY (`id_penilaian`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_kriteria` (`id_kriteria`),
  ADD KEY `id_sub_kriteria` (`id_sub_kriteria`),
  ADD KEY `id_karyawan` (`id_karyawan`);

--
-- Indexes for table `sub_kriteria`
--
ALTER TABLE `sub_kriteria`
  ADD PRIMARY KEY (`id_sub_kriteria`),
  ADD KEY `id_kriteria` (`id_kriteria`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `karyawan`
--
ALTER TABLE `karyawan`
  MODIFY `id_karyawan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `kriteria`
--
ALTER TABLE `kriteria`
  MODIFY `id_kriteria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `penilaian`
--
ALTER TABLE `penilaian`
  MODIFY `id_penilaian` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT for table `sub_kriteria`
--
ALTER TABLE `sub_kriteria`
  MODIFY `id_sub_kriteria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
