-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 18, 2025 at 05:25 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `perpustakaan`
--

-- --------------------------------------------------------

--
-- Table structure for table `data_anggota`
--

CREATE TABLE `data_anggota` (
  `id_siswa` varchar(20) NOT NULL,
  `nama` varchar(30) NOT NULL,
  `jurusan` varchar(30) NOT NULL,
  `kelas` varchar(10) NOT NULL,
  `semester` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `data_anggota`
--

INSERT INTO `data_anggota` (`id_siswa`, `nama`, `jurusan`, `kelas`, `semester`) VALUES
('11', '11', '11', '11', '11'),
('22', '22', '22', '22', '22'),
('AKL111', 'Intan', 'Akuntansi', '11', '4'),
('EK11', 'eki', 'MI', 'B1', '3'),
('MI20', 'Anugrah', 'Manajemen informatika', 'B1', '5'),
('MI21', 'Amel', 'Manajemen informatika', 'B1', '4'),
('MI22', 'jabal', 'Manajemen informatika', 'B1', '6'),
('PM111', 'ilham sukiman', 'pemasaran', '10', '1'),
('RPL222', 'Muhammad Jammaludin', 'Rekayasa perangkat lunak', '11', '3');

-- --------------------------------------------------------

--
-- Table structure for table `data_buku`
--

CREATE TABLE `data_buku` (
  `id_buku` varchar(11) NOT NULL,
  `judul_buku` varchar(255) NOT NULL,
  `isbn` varchar(255) NOT NULL,
  `nama_penulis` varchar(255) NOT NULL,
  `nama_penerbit` varchar(255) NOT NULL,
  `jumlah_halaman` int(11) NOT NULL,
  `foto` longblob DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `data_buku`
--

INSERT INTO `data_buku` (`id_buku`, `judul_buku`, `isbn`, `nama_penulis`, `nama_penerbit`, `jumlah_halaman`, `foto`) VALUES
('BK001', 'Buku PHP', '991010-222002020', 'Anton', 'gaijin', 110, 0x75706c6f61642f36383531373737303331343838342e38343731353933362e6a7067),
('BK002', 'Buku langit', '8111-20201-112', 'ilham', 'Bootsrap', 98, 0x75706c6f61642f36383532326263316537323565342e30383332333138372e6a706567),
('BK003', 'Buku quotes simple', '7188293-1219219-33991', 'ilham', 'Bootsrap', 40, 0x75706c6f61642f36383532326330343834613165332e34393333333437322e6a7067);

-- --------------------------------------------------------

--
-- Table structure for table `lihat_anggota`
--

CREATE TABLE `lihat_anggota` (
  `id_siswa` int(11) NOT NULL,
  `nama_siswa` int(11) NOT NULL,
  `jurusan` int(11) NOT NULL,
  `kelas` int(11) NOT NULL,
  `semester` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(11, 'kiki', '11', 'admin'),
(22, '22', '22', 'user'),
(444, 'iman', '444', 'admin'),
(446, 'Muhammad Jammaludin', 'RPL222', 'user'),
(447, 'anton', 'PM111', 'user'),
(448, 'jabal', 'MI22', 'user'),
(449, 'Intan', 'AKL111', 'user'),
(450, 'ilham sukiman', 'PM111', 'user'),
(451, 'ilham nurkarim', 'ABI22', 'user'),
(452, 'Amel', 'MI21', 'user'),
(453, '11', '11', 'user'),
(456, 'Anugrah', 'MI20', 'user'),
(457, 'eki', 'EK11', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `data_anggota`
--
ALTER TABLE `data_anggota`
  ADD PRIMARY KEY (`id_siswa`);

--
-- Indexes for table `data_buku`
--
ALTER TABLE `data_buku`
  ADD PRIMARY KEY (`id_buku`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=458;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
