-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 20, 2025 at 07:02 PM
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
('22', '33', '22', '22', '22'),
('AKL111', 'Intan', 'Akuntansi', '11', '4'),
('EK11', 'eki', 'MI', 'B1', '3'),
('MI20', 'Anugrah', 'Manajemen informatika', 'B1', '5'),
('MI21', 'Amel', 'Manajemen informatika', 'B2', '4'),
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
('BK001', 'Buku PHP', '991010-222002020', 'ilham', 'Gaijin', 100, 0x75706c6f61642f36383535393265666336663836362e34393537323635322e6a7067),
('BK002', 'Buku langit', '8111-20201-112', 'ilham', 'Garena', 98, 0x75706c6f61642f36383532326263316537323565342e30383332333138372e6a706567),
('BK003', '100 Quotes Simple Thinking about Blood Type', '788912-199223', 'Jabal', 'Moonton', 100, 0x75706c6f61642f36383535383033373033386233382e35393336313036302e6a7067);

-- --------------------------------------------------------

--
-- Table structure for table `data_pengembalian`
--

CREATE TABLE `data_pengembalian` (
  `id_buku` int(11) NOT NULL,
  `judul_buku` varchar(50) NOT NULL,
  `tanggal_pinjam` date NOT NULL,
  `tanggal_pengembalian` date NOT NULL,
  `status` enum('sudah dikembalikan','belum dikembalikan') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `data_pengembalian`
--

INSERT INTO `data_pengembalian` (`id_buku`, `judul_buku`, `tanggal_pinjam`, `tanggal_pengembalian`, `status`) VALUES
(0, 'EA', '2025-06-05', '2025-06-19', 'belum dikembalikan');

-- --------------------------------------------------------

--
-- Table structure for table `data_pinjam`
--

CREATE TABLE `data_pinjam` (
  `id_buku` varchar(11) NOT NULL,
  `judul_buku` varchar(255) NOT NULL,
  `isbn` varchar(255) NOT NULL,
  `nama_penulis` varchar(255) NOT NULL,
  `nama_penerbit` varchar(255) NOT NULL,
  `jumlah_halaman` int(11) NOT NULL,
  `foto` longblob DEFAULT NULL,
  `tanggal_pinjam` date DEFAULT NULL,
  `tanggal_pengembalian` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `data_pinjam`
--

INSERT INTO `data_pinjam` (`id_buku`, `judul_buku`, `isbn`, `nama_penulis`, `nama_penerbit`, `jumlah_halaman`, `foto`, `tanggal_pinjam`, `tanggal_pengembalian`) VALUES
('EA', 'EA', '991010-222002020', 'EA', 'EA', 22, 0x75706c6f61642f36383535393339393131306236332e36303636343039322e6a7067, '2025-06-05', '2025-06-19');

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
