-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 08, 2025 at 07:14 PM
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
  `nama_siswa` varchar(30) NOT NULL,
  `jurusan` varchar(30) NOT NULL,
  `kelas` varchar(10) NOT NULL,
  `semester` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `data_anggota`
--

INSERT INTO `data_anggota` (`id_siswa`, `nama_siswa`, `jurusan`, `kelas`, `semester`) VALUES
('Eki', 'Eki', 'Eki', '11', '1'),
('PM111', 'Jamal', 'pemasaran', '10', '1');

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
('BK001', 'Buku PHP', '991010-222002020', 'Anton', 'Garena', 111, 0x75706c6f61642f62756b755f36383637613237353264336439312e32343331363434372e6a7067),
('BK002', '100 Quotes Simple Thinking about Blood Type', '788912-199223', 'Anton', 'Garena', 100, 0x75706c6f61642f62756b755f36383662636362373936643763342e37343737303031312e6a7067),
('BK003', 'Buku langit', '788912-199223', 'Ilham sukiman', 'Bootsrap', 100, 0x75706c6f61642f62756b755f36383662636364313961363466352e32383431353232372e6a7067);

-- --------------------------------------------------------

--
-- Table structure for table `data_list_buku`
--

CREATE TABLE `data_list_buku` (
  `id_buku` varchar(50) DEFAULT NULL,
  `isbn` varchar(13) NOT NULL,
  `judul_buku` varchar(255) NOT NULL,
  `nama_penulis` varchar(255) NOT NULL,
  `nama_penerbit` varchar(255) NOT NULL,
  `tanggal_ditambahkan` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `data_list_buku`
--

INSERT INTO `data_list_buku` (`id_buku`, `isbn`, `judul_buku`, `nama_penulis`, `nama_penerbit`, `tanggal_ditambahkan`) VALUES
('BK001', '991010-222002', 'Buku PHP', 'Anton', 'Garena', '2025-07-04'),
('BK002', '788912-199223', '100 Quotes Simple Thinking about Blood Type', 'Anton', 'Garena', '2025-07-07'),
('BK003', '788912-199223', 'Buku langit', 'Ilham sukiman', 'Bootsrap', '2025-07-07');

-- --------------------------------------------------------

--
-- Table structure for table `data_pengembalian`
--

CREATE TABLE `data_pengembalian` (
  `judul_buku` varchar(50) NOT NULL,
  `tanggal_pinjam` date NOT NULL,
  `tanggal_pengembalian` date NOT NULL,
  `status_pengembalian` enum('sudah dikembalikan','belum dikembalikan') NOT NULL,
  `id_buku` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `data_pengembalian`
--

INSERT INTO `data_pengembalian` (`judul_buku`, `tanggal_pinjam`, `tanggal_pengembalian`, `status_pengembalian`, `id_buku`) VALUES
('Buku PHP', '2025-07-08', '2025-07-15', 'sudah dikembalikan', 'BK001');

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
('BK001', 'Buku PHP', '991010-222002020', 'Anton', 'Garena', 111, 0x75706c6f61642f62756b755f36383637613237353264336439312e32343331363434372e6a7067, '2025-07-08', '2025-07-15');

-- --------------------------------------------------------

--
-- Table structure for table `data_pustakawan`
--

CREATE TABLE `data_pustakawan` (
  `id_pustakawan` int(11) NOT NULL,
  `nama_pustakawan` varchar(255) DEFAULT NULL,
  `jabatan` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `data_pustakawan`
--

INSERT INTO `data_pustakawan` (`id_pustakawan`, `nama_pustakawan`, `jabatan`) VALUES
(222, 'ilham sukiman', 'guru piket'),
(444, 'admin', 'petugas ');

-- --------------------------------------------------------

--
-- Table structure for table `histori_pengembalian`
--

CREATE TABLE `histori_pengembalian` (
  `judul_buku` varchar(50) NOT NULL,
  `tanggal_pinjam` date NOT NULL,
  `tanggal_pengembalian` date NOT NULL,
  `id_buku` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `histori_pengembalian`
--

INSERT INTO `histori_pengembalian` (`judul_buku`, `tanggal_pinjam`, `tanggal_pengembalian`, `id_buku`) VALUES
('Buku PHP', '2025-07-08', '2025-07-08', 'BK001');

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
(111, 'admin', '111', 'admin'),
(468, 'Jamal', 'PM111', 'user'),
(473, 'ilham sukiman', '222', 'admin'),
(477, 'Eki', 'Eki', 'user');

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
-- Indexes for table `data_pustakawan`
--
ALTER TABLE `data_pustakawan`
  ADD PRIMARY KEY (`id_pustakawan`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=479;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
