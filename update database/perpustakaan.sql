-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 30, 2025 at 03:49 AM
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
('MI22', 'eki', 'Manajemen informatika', 'B1', '3');

-- --------------------------------------------------------

--
-- Table structure for table `data_buku`
--

CREATE TABLE `data_buku` (
  `id_buku` varchar(11) NOT NULL,
  `judul_buku` varchar(255) NOT NULL,
  `kategori_buku` varchar(255) NOT NULL,
  `isbn` varchar(255) NOT NULL,
  `nama_penulis` varchar(255) NOT NULL,
  `nama_penerbit` varchar(255) NOT NULL,
  `jumlah_halaman` int(11) NOT NULL,
  `foto` longblob DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `data_buku`
--

INSERT INTO `data_buku` (`id_buku`, `judul_buku`, `kategori_buku`, `isbn`, `nama_penulis`, `nama_penerbit`, `jumlah_halaman`, `foto`) VALUES
('BK001', 'akuntansi dasar', 'Buku penjurusan', '991010-222002020', 'diana', 'anatiansa diana', 56, 0x75706c6f61642f62756b755f36383837343863613037376336362e34343132393432302e6a7067),
('BK002', 'Buku PHP', 'Buku penjurusan', '788912-199223', 'Intan purnama', 'purnama', 56, 0x75706c6f61642f62756b755f36383837343732323335323139392e30363132363439392e6a7067),
('BK003', 'Matematika dasar', 'Matematika', '718912-199223', 'Ai Tusi Fatimah', 'Toto nusantara', 77, 0x75706c6f61642f62756b755f36383837343738623832316464362e32363433343230352e6a7067),
('BK004', 'Matematika exce', 'Matematika', '223333-1212121', 'Sri Suryanti', 'Universitas Muhammadiyah Gresik', 55, 0x75706c6f61642f62756b755f36383837343764653632666433392e35363433343734382e6a7067);

-- --------------------------------------------------------

--
-- Table structure for table `data_list_buku`
--

CREATE TABLE `data_list_buku` (
  `id_buku` varchar(50) DEFAULT NULL,
  `isbn` varchar(13) NOT NULL,
  `judul_buku` varchar(255) NOT NULL,
  `kategori_buku` varchar(255) DEFAULT NULL,
  `nama_penulis` varchar(255) NOT NULL,
  `nama_penerbit` varchar(255) NOT NULL,
  `tanggal_ditambahkan` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `data_list_buku`
--

INSERT INTO `data_list_buku` (`id_buku`, `isbn`, `judul_buku`, `kategori_buku`, `nama_penulis`, `nama_penerbit`, `tanggal_ditambahkan`) VALUES
('BK003', '718912-199223', 'Matematika dasar', NULL, 'Ai Tusi Fatimah', 'Toto nusantara', '2025-07-28'),
('BK004', '223333-121212', 'Matematika exce', NULL, 'Sri Suryanti', 'Universitas Muhammadiyah Gresik', '2025-07-28'),
('BK001', '991010-222002', 'akuntansi dasar', NULL, 'diana', 'anatiansa diana', '2025-07-28');

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
('akuntansi dasar', '2025-01-10', '2025-01-13', 'belum dikembalikan', 'BK001'),
('akuntansi dasar', '2025-01-15', '2025-01-17', 'belum dikembalikan', 'BK001'),
('akuntansi dasar', '2025-01-18', '2025-01-29', 'belum dikembalikan', 'BK001'),
('Buku PHP', '2025-02-05', '2025-02-14', 'belum dikembalikan', 'BK002');

-- --------------------------------------------------------

--
-- Table structure for table `data_pinjam`
--

CREATE TABLE `data_pinjam` (
  `id_buku` varchar(11) NOT NULL,
  `judul_buku` varchar(255) NOT NULL,
  `kategori_buku` varchar(255) DEFAULT NULL,
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

INSERT INTO `data_pinjam` (`id_buku`, `judul_buku`, `kategori_buku`, `isbn`, `nama_penulis`, `nama_penerbit`, `jumlah_halaman`, `foto`, `tanggal_pinjam`, `tanggal_pengembalian`) VALUES
('BK001', 'akuntansi dasar', 'Buku penjurusan', '991010-222002020', 'diana', 'anatiansa diana', 56, 0x75706c6f61642f62756b755f36383837343863613037376336362e34343132393432302e6a7067, '2025-01-10', '2025-01-13'),
('BK001', 'akuntansi dasar', 'Buku penjurusan', '991010-222002020', 'diana', 'anatiansa diana', 56, 0x75706c6f61642f62756b755f36383837343863613037376336362e34343132393432302e6a7067, '2025-01-15', '2025-01-17'),
('BK001', 'akuntansi dasar', 'Buku penjurusan', '991010-222002020', 'diana', 'anatiansa diana', 56, 0x75706c6f61642f62756b755f36383837343863613037376336362e34343132393432302e6a7067, '2025-01-18', '2025-01-29'),
('BK002', 'Buku PHP', 'Buku penjurusan', '788912-199223', 'Intan purnama', 'purnama', 56, 0x75706c6f61642f62756b755f36383837343732323335323139392e30363132363439392e6a7067, '2025-02-05', '2025-02-14');

-- --------------------------------------------------------

--
-- Table structure for table `data_pustakawan`
--

CREATE TABLE `data_pustakawan` (
  `id_pustakawan` varchar(11) NOT NULL,
  `nama_pustakawan` varchar(255) DEFAULT NULL,
  `jabatan` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `data_pustakawan`
--

INSERT INTO `data_pustakawan` (`id_pustakawan`, `nama_pustakawan`, `jabatan`) VALUES
('PK001', 'Rezky', 'petugas');

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
(111, '111', '111', 'admin'),
(548, 'Rezky', 'PK001', 'admin'),
(549, 'eki', 'MI22', 'user');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=550;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
