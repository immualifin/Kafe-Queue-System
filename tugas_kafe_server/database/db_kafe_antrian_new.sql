-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 10, 2025 at 08:38 AM
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
-- Database: `db_kafe_antrian`
--

-- --------------------------------------------------------

--
-- Table structure for table `detail_pesanan`
--

CREATE TABLE `detail_pesanan` (
  `id_detail` int(11) NOT NULL,
  `id_pesanan` int(11) NOT NULL,
  `id_menu` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `harga_satuan` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_pesanan`
--

INSERT INTO `detail_pesanan` (`id_detail`, `id_pesanan`, `id_menu`, `jumlah`, `harga_satuan`) VALUES
(1, 1, 11, 1, 20000.00),
(2, 1, 14, 1, 22000.00),
(3, 2, 9, 1, 18000.00),
(4, 3, 9, 1, 18000.00);

-- --------------------------------------------------------

--
-- Table structure for table `kategori_menu`
--

CREATE TABLE `kategori_menu` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `slug` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori_menu`
--

INSERT INTO `kategori_menu` (`id_kategori`, `nama_kategori`, `deskripsi`, `icon`, `slug`) VALUES
(1, 'Makanan Berat', 'Makanan utama dan nasi', '🍛', 'makanan-berat'),
(2, 'Minuman', 'Berbagai jenis minuman dingin dan panas', '🥤', 'minuman'),
(3, 'Cemilan', 'Makanan ringan untuk teman ngopi', '🍿', 'cemilan'),
(4, 'Dessert', 'Pencuci mulut manis', '🍰', 'dessert');

-- --------------------------------------------------------

--
-- Table structure for table `menus`
--

CREATE TABLE `menus` (
  `id_menu` int(11) NOT NULL,
  `id_kategori` int(11) NOT NULL,
  `nama_menu` varchar(150) NOT NULL,
  `harga` decimal(10,2) NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `status_tersedia` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menus`
--

INSERT INTO `menus` (`id_menu`, `id_kategori`, `nama_menu`, `harga`, `gambar`, `status_tersedia`, `created_at`) VALUES
(1, 1, 'Nasi Goreng Special', 25000.00, 'https://media.istockphoto.com/id/1398317278/id/foto/nasi-goreng-thailand-dengan-udang-daun-bawang-jeruk-nipis-dengan-latar-belakang-kayu-tampak.jpg?s=612x612&w=0&k=20&c=H8hjF2FSGAcwdAFjeA73KZowjmUmgCbAWjHsn_68_zQ=', 1, '2025-12-05 15:35:58'),
(2, 1, 'Mie Ayam Bakso', 22000.00, 'https://media.istockphoto.com/id/2248805517/id/foto/mie-yamin-pangsit.jpg?s=612x612&w=0&k=20&c=nefu9spxFKlxePgwo8e1j20LdLQMAOxaUCFLxJQu1Xk=', 1, '2025-12-05 15:35:58'),
(3, 1, 'Ayam Bakar Madu', 35000.00, 'https://images.unsplash.com/photo-1645066803665-d16a79a21566?w=400&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8M3x8YXlhbSUyMGJha2FyfGVufDB8fDB8fHww', 1, '2025-12-05 15:35:58'),
(4, 1, 'Nasi Rames Komplit', 28000.00, 'https://media.istockphoto.com/id/1301011215/id/foto/indonesian-food.jpg?s=612x612&w=0&k=20&c=umAs6tUgTKRRU3fUAyCbGZbXvtXkoEDtsB3q_cz9s4I=', 1, '2025-12-05 15:35:58'),
(5, 2, 'Es Teh Manis', 8000.00, 'https://media.istockphoto.com/id/1356167319/id/foto/segelas-es-teh-jus-coklat-diisolasi-di-latar-belakang-putih.jpg?s=612x612&w=0&k=20&c=2UuWYkHY014VJvSGTBPgtTbJSNkqIGMVIis1l4ptpxQ=', 1, '2025-12-05 15:35:58'),
(6, 2, 'Kopi Hitam', 10000.00, 'https://media.istockphoto.com/id/1143290013/id/foto/cangkir-kopi-terisolasi.jpg?s=612x612&w=0&k=20&c=jNa_yHu_vRmggqgcZUCELfALIAO0pso8TnAA78GxkbM=', 1, '2025-12-05 15:35:58'),
(7, 2, 'Jus Alpukat', 15000.00, 'https://media.istockphoto.com/id/984772002/id/foto/smoothie-hijau-sehat-vegetarian-dari-alpukat-daun-bayam-biji-apel-dan-chia-pada-latar-belakang.jpg?s=612x612&w=0&k=20&c=1dAdKtu_efeiJvfgchHQ7DdU2SvAuHFpaLlg2zjRHZg=', 1, '2025-12-05 15:35:58'),
(8, 2, 'Es Cendol', 12000.00, 'https://cdn.pixabay.com/photo/2015/07/09/07/39/cendol-837368_960_720.jpg', 1, '2025-12-05 15:35:58'),
(9, 3, 'Kentang Goreng', 18000.00, 'https://media.istockphoto.com/id/618214356/id/foto/sekeranjang-kentang-goreng-makanan-cepat-saji-terkenal.jpg?s=612x612&w=0&k=20&c=9-3TS9GXgVt_suJLiXUdn48RdSdPFVR9Cwc3uCoRjnk=', 1, '2025-12-05 15:35:58'),
(10, 3, 'Pisang Goreng', 15000.00, 'https://images.unsplash.com/photo-1762941904142-9d91ca413e66?q=80&w=735&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', 1, '2025-12-05 15:35:58'),
(11, 3, 'Roti Bakar', 20000.00, 'https://media.istockphoto.com/id/155388694/id/foto/sandwich-panini.jpg?s=612x612&w=0&k=20&c=rV7MaaLkcH0oMt1y3coX2c7AmLNhdlIjQ5DBdJr7Du4=', 1, '2025-12-05 15:35:58'),
(12, 3, 'Sosis Bakar', 25000.00, 'https://images.unsplash.com/photo-1685798830559-fcdda5b0915d?q=80&w=871&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', 1, '2025-12-05 15:35:58'),
(13, 4, 'Es Krim Vanila', 12000.00, 'https://media.istockphoto.com/id/93168495/id/foto/empat-sendok-es-krim-vanila-dalam-mangkuk-kaca.jpg?s=612x612&w=0&k=20&c=u_GqVUglJplyQs548QhoW5fzcGp3v0ZyevBZIKw6hMc=', 1, '2025-12-05 15:35:58'),
(14, 4, 'Pancake Madu', 22000.00, 'https://images.pexels.com/photos/376464/pexels-photo-376464.jpeg', 1, '2025-12-05 15:35:58'),
(15, 4, 'Brownies Coklat', 18000.00, 'https://images.unsplash.com/photo-1636743715220-d8f8dd900b87?q=80&w=685&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', 1, '2025-12-05 15:35:58'),
(16, 4, 'Wafel', 25000.00, 'https://media.istockphoto.com/id/1486162469/id/foto/wafel-dengan-stroberi-dan-cokelat.jpg?s=612x612&w=0&k=20&c=lH8QXziBFpeU7lRGsnxT_xhW3oBMEVxadkvvHFtatgQ=', 1, '2025-12-05 15:35:58');

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `id_pesanan` int(11) NOT NULL,
  `nama_pelanggan` varchar(100) NOT NULL,
  `no_meja` varchar(10) NOT NULL,
  `total_harga` decimal(10,2) NOT NULL,
  `status_pesanan` enum('pending','proses','siap','selesai') NOT NULL DEFAULT 'pending',
  `waktu_pesan` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `id_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pesanan`
--

INSERT INTO `pesanan` (`id_pesanan`, `nama_pelanggan`, `no_meja`, `total_harga`, `status_pesanan`, `waktu_pesan`, `updated_at`, `id_user`) VALUES
(1, 'test', 'Meja 1', 42000.00, 'selesai', '2025-12-05 15:37:50', '2025-12-05 15:51:45', 1),
(2, 'test2', 'Meja 2', 18000.00, 'selesai', '2025-12-05 15:47:00', '2025-12-05 15:52:40', 2),
(3, 'test', 'Meja 1', 18000.00, 'selesai', '2025-12-10 05:36:53', '2025-12-10 06:00:33', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `role` enum('admin','kasir') NOT NULL DEFAULT 'kasir',
  `session_token` varchar(255) DEFAULT NULL,
  `session_expiry` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `username`, `password`, `nama_lengkap`, `role`, `session_token`, `session_expiry`, `created_at`) VALUES
(1, 'admin', '$2a$12$tT4.oa.kU4P5WRJCKsKLluJhMz/PnftbIzHYPvJGDhW6R9MCLrTle', 'Administrator', 'admin', '42f32fdbdb6201e6c73e9cdcb8501e36565e702dfda8690840fd3a7a6c53f920', '2025-12-10 23:48:04', '2025-12-05 15:35:58'),
(2, 'kasir1', '$2a$12$M9K0G1ah496UHf/2iDNELeAki4IVvz6ExKzclXOAMEMkFGkLy9IH2', 'Kasir 1', 'kasir', '9e30b85bf9a9a47c38a0c2f5e108be7c976aef06787dfc014bc278dbd445fe88', '2025-12-06 09:52:16', '2025-12-05 15:35:58');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_menu` (`id_menu`),
  ADD KEY `idx_detail_pesanan` (`id_pesanan`);

--
-- Indexes for table `kategori_menu`
--
ALTER TABLE `kategori_menu`
  ADD PRIMARY KEY (`id_kategori`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`id_menu`),
  ADD KEY `idx_menu_kategori` (`id_kategori`),
  ADD KEY `idx_menu_tersedia` (`status_tersedia`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id_pesanan`),
  ADD KEY `idx_pesanan_status` (`status_pesanan`),
  ADD KEY `idx_pesanan_waktu` (`waktu_pesan`),
  ADD KEY `idx_pesanan_user` (`id_user`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `kategori_menu`
--
ALTER TABLE `kategori_menu`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `menus`
--
ALTER TABLE `menus`
  MODIFY `id_menu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id_pesanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD CONSTRAINT `detail_pesanan_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE,
  ADD CONSTRAINT `detail_pesanan_ibfk_2` FOREIGN KEY (`id_menu`) REFERENCES `menus` (`id_menu`) ON DELETE CASCADE;

--
-- Constraints for table `menus`
--
ALTER TABLE `menus`
  ADD CONSTRAINT `menus_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategori_menu` (`id_kategori`) ON DELETE CASCADE;

--
-- Constraints for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD CONSTRAINT `pesanan_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
