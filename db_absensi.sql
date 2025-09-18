-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 15, 2025 at 05:15 AM
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
-- Database: `db_absensi`
--

-- --------------------------------------------------------

--
-- Table structure for table `absensi`
--

CREATE TABLE `absensi` (
  `id` int(11) NOT NULL,
  `karyawan_id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `status` enum('Alpa','Izin Tanpa Bayar','Sakit','Izin Dispensasi','Cuti','Surat Dokter','Terlambat','Izin Meninggalkan Tempat Kerja') NOT NULL,
  `keterangan` text DEFAULT NULL,
  `menit_telat` int(11) DEFAULT NULL,
  `file_surat` varchar(255) DEFAULT NULL,
  `menit_terlambat` int(11) DEFAULT 0,
  `file_bukti` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `absensi`
--

INSERT INTO `absensi` (`id`, `karyawan_id`, `tanggal`, `status`, `keterangan`, `menit_telat`, `file_surat`, `menit_terlambat`, `file_bukti`) VALUES
(50, 376, '2025-09-01', 'Cuti', 'Cuti', NULL, NULL, 0, NULL),
(51, 376, '2025-09-02', 'Cuti', 'Cuti', NULL, NULL, 0, NULL),
(52, 376, '2025-09-03', 'Cuti', 'Cuti', NULL, NULL, 0, NULL),
(53, 376, '2025-09-04', 'Cuti', 'Cuti', NULL, NULL, 0, NULL),
(54, 376, '2025-09-05', 'Cuti', 'cuti', NULL, NULL, 0, NULL),
(55, 376, '2025-09-08', 'Cuti', 'cuti\r\n', NULL, NULL, 0, NULL),
(56, 376, '2025-09-09', 'Surat Dokter', 'cuti', NULL, NULL, 0, NULL),
(57, 371, '2025-09-04', 'Cuti', 'Cuti', NULL, NULL, 0, NULL),
(58, 371, '2025-09-08', 'Cuti', 'cuti', NULL, NULL, 0, NULL),
(59, 372, '2025-09-04', 'Izin Meninggalkan Tempat Kerja', 'izin', NULL, NULL, 0, NULL),
(60, 373, '2025-09-08', 'Izin Meninggalkan Tempat Kerja', 'izin', NULL, NULL, 0, NULL),
(61, 385, '2025-09-08', 'Cuti', 'cuti', NULL, NULL, 0, NULL),
(62, 354, '2025-09-08', 'Cuti', 'cuti', NULL, NULL, 0, NULL),
(63, 330, '2025-09-06', 'Cuti', 'cuti', NULL, NULL, 0, NULL),
(64, 330, '2025-09-07', 'Cuti', 'cuti', NULL, NULL, 0, NULL),
(65, 330, '2025-09-08', 'Cuti', 'cuti', NULL, NULL, 0, NULL),
(66, 349, '2025-09-08', 'Cuti', 'cuti', NULL, NULL, 0, NULL),
(67, 349, '2025-09-09', 'Cuti', 'cuti', NULL, NULL, 0, NULL),
(68, 296, '2025-09-03', 'Surat Dokter', 'sakit', NULL, NULL, 0, NULL),
(69, 296, '2025-09-04', 'Surat Dokter', 'sakit', NULL, NULL, 0, NULL),
(70, 296, '2025-09-09', 'Izin Meninggalkan Tempat Kerja', 'izin', NULL, NULL, 0, NULL),
(71, 300, '2025-09-04', 'Terlambat', 'telat', 5, NULL, 0, NULL),
(72, 302, '2025-09-10', 'Cuti', 'cuti', NULL, NULL, 0, NULL),
(73, 302, '2025-09-11', 'Cuti', 'cuti', NULL, NULL, 0, NULL),
(74, 302, '2025-09-12', 'Cuti', 'cuti', NULL, NULL, 0, NULL),
(75, 306, '2025-09-02', 'Terlambat', 'telat', 3, NULL, 0, NULL),
(76, 307, '2025-09-02', 'Cuti', 'cuti', NULL, NULL, 0, NULL),
(77, 307, '2025-09-03', 'Cuti', 'cuti', NULL, NULL, 0, NULL),
(78, 307, '2025-09-04', 'Cuti', 'cuti', NULL, NULL, 0, NULL),
(79, 307, '2025-09-08', 'Cuti', 'cuti', NULL, NULL, 0, NULL),
(80, 307, '2025-09-09', 'Cuti', 'cuti', NULL, NULL, 0, NULL),
(81, 307, '2025-09-29', 'Cuti', 'cuti', NULL, NULL, 0, NULL),
(82, 307, '2025-09-30', 'Cuti', 'cuti', NULL, NULL, 0, NULL),
(83, 309, '2025-09-10', 'Cuti', 'cuti', NULL, NULL, 0, NULL),
(84, 314, '2025-09-01', 'Cuti', 'cuti', NULL, NULL, 0, NULL),
(85, 314, '2025-09-04', 'Cuti', 'cuti', NULL, NULL, 0, NULL),
(86, 314, '2025-09-06', 'Cuti', 'cuti', NULL, NULL, 0, NULL),
(87, 319, '2025-09-04', 'Cuti', 'cuti', NULL, NULL, 0, NULL),
(88, 320, '2025-09-24', 'Cuti', 'cuti', NULL, NULL, 0, NULL),
(89, 320, '2025-09-25', 'Cuti', 'cuti', NULL, NULL, 0, NULL),
(90, 320, '2025-09-26', 'Cuti', 'cuti', NULL, NULL, 0, NULL),
(91, 320, '2025-09-27', 'Cuti', 'cuti', NULL, NULL, 0, NULL),
(92, 320, '2025-09-28', 'Cuti', 'cuti', NULL, NULL, 0, NULL),
(93, 320, '2025-09-29', 'Cuti', 'cuti', NULL, NULL, 0, NULL),
(94, 320, '2025-09-30', 'Cuti', 'cuti', NULL, NULL, 0, NULL),
(95, 323, '2025-09-03', 'Surat Dokter', 'sakit', NULL, NULL, 0, NULL),
(96, 323, '2025-09-08', 'Cuti', 'cuti', NULL, NULL, 0, NULL),
(97, 324, '2025-09-04', 'Cuti', 'cuti', NULL, NULL, 0, NULL),
(98, 325, '2025-09-04', 'Cuti', 'cuti', NULL, NULL, 0, NULL),
(99, 251, '2025-09-06', 'Cuti', 'cuti', NULL, NULL, 0, NULL),
(100, 250, '2025-09-06', 'Cuti', 'cuti', NULL, NULL, 0, NULL),
(101, 253, '2025-09-08', 'Cuti', 'cuti', NULL, NULL, 0, NULL),
(102, 268, '2025-09-11', 'Cuti', 'cuti', NULL, NULL, 0, NULL),
(103, 268, '2025-09-12', 'Cuti', 'cuti', NULL, NULL, 0, NULL),
(104, 268, '2025-09-13', 'Cuti', 'cuti', NULL, NULL, 0, NULL),
(105, 268, '2025-09-14', 'Cuti', 'cuti', NULL, NULL, 0, NULL),
(106, 268, '2025-09-15', 'Cuti', 'cuti', NULL, NULL, 0, NULL),
(107, 268, '2025-09-16', 'Cuti', 'cuti', NULL, NULL, 0, NULL),
(108, 269, '2025-09-15', 'Cuti', 'cuti', NULL, NULL, 0, NULL),
(109, 272, '2025-09-01', 'Cuti', 'cuti', NULL, NULL, 0, NULL),
(110, 272, '2025-09-02', 'Cuti', 'cuti', NULL, NULL, 0, NULL),
(111, 275, '2025-09-01', 'Cuti', 'cuti', NULL, NULL, 0, NULL),
(112, 275, '2025-09-02', 'Cuti', 'cuti', NULL, NULL, 0, NULL),
(113, 279, '2025-09-01', 'Cuti', 'cuti', NULL, NULL, 0, NULL),
(114, 279, '2025-09-02', 'Cuti', 'cuti', NULL, NULL, 0, NULL),
(115, 282, '2025-09-01', 'Cuti', 'cuti', NULL, NULL, 0, NULL),
(116, 286, '2025-09-05', 'Cuti', 'cuti', NULL, NULL, 0, NULL),
(117, 286, '2025-09-06', 'Cuti', 'cuti', NULL, NULL, 0, NULL),
(118, 290, '2025-09-15', 'Cuti', 'cuti', NULL, NULL, 0, NULL),
(119, 292, '2025-09-04', 'Cuti', 'cuti', NULL, NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `nomor_hp` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `nama`, `nomor_hp`, `password`) VALUES
(3, 'javi', '021937', 'e10adc3949ba59abbe56e057f20f883e'),
(4, 'admin', 'admin123', 'd65d7ebfac92fabb1886e2b21a8c6ac8'),
(5, 'admin', 'admin', '0192023a7bbd73250516f069df18b500');

-- --------------------------------------------------------

--
-- Table structure for table `departemen`
--

CREATE TABLE `departemen` (
  `id` int(11) NOT NULL,
  `nama_departemen` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departemen`
--

INSERT INTO `departemen` (`id`, `nama_departemen`) VALUES
(1, 'Security'),
(2, 'Teknik'),
(3, 'BR&HRG'),
(4, 'Accounting & Finance'),
(5, 'BRD'),
(6, 'BMO'),
(7, 'HR/GA'),
(8, 'Purchasing'),
(9, 'Food & Beverage'),
(10, 'Building Relation'),
(11, 'Housekeeping & Gardening'),
(12, 'Team Khusus'),
(13, 'Pondok Indah'),
(14, 'LT 19');

-- --------------------------------------------------------

--
-- Table structure for table `karyawan`
--

CREATE TABLE `karyawan` (
  `id` int(11) NOT NULL,
  `nik` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `departemen_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `karyawan`
--

INSERT INTO `karyawan` (`id`, `nik`, `nama`, `departemen_id`) VALUES
(212, '9440038', 'Dede Yuyun', 14),
(213, '8940071', 'Oman Rochman', 14),
(214, '9640151', 'Sugiarto', 14),
(215, '0040061', 'Ari Wibowo', 13),
(216, '9640061', 'Haryanto Ba Warsida', 13),
(217, '1440749', 'M. Fikih', 13),
(218, '9940073', 'M. Ilham', 13),
(219, '1440726', 'Nurohman', 13),
(220, '0040053', 'Rusli Z.', 13),
(221, '9640101', 'Sugiyarto', 13),
(222, '8540071', 'Sulistyo', 13),
(223, '8640173', 'Edi Ridwan', 1),
(224, '8540331', 'Kusharyanto', 1),
(225, '9640045', 'M. Nuryadi', 1),
(226, '8540391', 'Slamet Suradi', 1),
(227, '8540081', 'Weni Darwis', 1),
(228, '9240039', 'Karso', 1),
(229, '9640029', 'Rustomi', 1),
(230, '9240055', 'Kusnandiono', 1),
(231, '8540373', 'Subekti Ranu H.', 1),
(232, '0440058', 'Martinus N.', 12),
(233, '0540236', 'Alex Sardioko', 12),
(234, '1640795', 'Agung Sulaksono', 12),
(235, '1440741', 'Choerurozi', 12),
(236, '1440742', 'Didik Winarto', 12),
(237, '1440736', 'Dinno Suryo', 12),
(238, '9140069', 'Jaryani', 12),
(239, '1440735', 'M. Ikhsan', 12),
(241, '1340712', 'Slamet Haryadi', 12),
(242, '1640786', 'Wahyu Hidayat', 12),
(243, '1540767', 'Mukhtarudin', 12),
(244, '1840809', 'Nurul Fajar', 12),
(245, '0640279', 'Sriyono', 12),
(246, '1240686', 'Surya Winata', 1),
(247, '1440750', 'Abdul Malik', 1),
(248, '2040842', 'Ade Sandi Maulana', 1),
(249, '2040843', 'Andi Sandra', 1),
(250, '2040844', 'Andris Tri Yahya', 1),
(251, '0040045', 'Agus Djuli D.', 1),
(252, '9940022', 'Agung Nugroho', 1),
(253, '9640088', 'Agus Edi R.', 1),
(254, '0040096', 'Alexander A.', 1),
(255, '9140051', 'Ahmad Husaini', 1),
(256, '1840811', 'Ahmad Zaini', 1),
(257, '1640791', 'Asep Heryadi', 1),
(258, '1840805', 'Chaezar Pracipto', 1),
(259, '9640011', 'Dadang Supria', 1),
(260, '1440732', 'Dwi Hartono', 1),
(261, '1440731', 'Eddy Sutamto', 1),
(262, '1240651', 'Eka Aris YL', 1),
(263, '1640789', 'Eko Julyanto', 1),
(264, '1640792', 'Enda Juanda', 1),
(265, '2340859', 'Guntur Prabowo', 1),
(266, '1240635', 'Harun Suhada', 1),
(267, '2440864', 'Handoko', 1),
(268, '1940828', 'Imam Tohari', 1),
(269, '1940835', 'Isep Wahyudi', 1),
(270, '1440725', 'Iwan Indirwan', 1),
(271, '1140562', 'Janim Jayadi', 1),
(272, '9540016', 'Joko Waluyo', 1),
(273, '1440746', 'Lukman Hakim', 1),
(274, '1140589', 'M. Ismail', 1),
(275, '1940827', 'M. Nur Huda', 1),
(276, '1940831', 'M. Ikbal Fauzi', 1),
(277, '9240071', 'Mamat Rahmat', 1),
(278, '9240063', 'Nana Suyatna', 1),
(279, '1940829', 'Nurokhman Jaya', 1),
(280, '9640071', 'Nurul Abdi', 1),
(281, '1840812', 'Nuryadi', 1),
(282, '2040845', 'Rahmat Dianto', 1),
(283, '0940497', 'Reza Fadriyan', 1),
(284, '2240849', 'Robby Setiawan', 1),
(285, '2340855', 'Toni Gunawan', 1),
(286, '1340711', 'Sukmara', 1),
(287, '0940501', 'Supriyadi', 1),
(288, '1940834', 'Sutanto', 1),
(289, '9940031', 'Sutedi', 1),
(290, '1640775', 'Syaifuloh', 1),
(291, '1640775', 'Teguh Firmansah', 1),
(292, '1440748', 'Teguh Yulianto', 1),
(293, '1440747', 'Yeremia Y Tatipata', 1),
(294, '1640772', 'Ade Thovani', 11),
(295, '1640793', 'Agus Chomsah Nur Rokhman', 11),
(296, '9540059', 'Agus Supriyadi', 11),
(297, '0440211', 'Agus Setiawan', 11),
(298, '2340854', 'Bayu Hermawan', 11),
(299, '1240619', 'Chamid Masruri', 11),
(300, '1940832', 'Candra Irawan', 11),
(301, '1340713', 'Christovorus', 11),
(302, '1940833', 'Dani Dwi Laksono', 11),
(303, '1940836', 'Dani Harsanto', 11),
(304, '1740796', 'Dedi Sudrajat', 11),
(305, '1740802', 'Dedy Wahyudin', 11),
(306, '1940820', 'Elma Amelia L', 11),
(307, '1340716', 'Euis Atika', 11),
(308, '1840813', 'Erik Andrian Saputra', 11),
(309, '1740801', 'Fahrudi', 11),
(310, '1740797', 'Fahrullah', 11),
(311, '1140554', 'Ganesa', 11),
(312, '1640788', 'Indra Saputra', 11),
(313, '1340718', 'Jaenudin', 11),
(314, '1940821', 'Jihan Natasha M', 11),
(315, '1840803', 'Lissiu Hutasoit', 11),
(316, '1340710', 'Liya S', 11),
(317, '1340720', 'Muhamad Arpian', 11),
(318, '1040481', 'Nanang P', 11),
(319, '1340708', 'Nurul Hasanah', 11),
(320, '1340715', 'Petrus Yopie', 11),
(321, '1440730', 'Rosmalia', 11),
(322, '1440740', 'Saepul Basri', 11),
(323, '1440729', 'Sintia Dania Putri', 11),
(324, '1740798', 'Teguh Fadli Alimi', 11),
(325, '1340721', 'Tihamah', 11),
(326, '1340721', 'Ucep', 11),
(327, '1540760', 'Wahyuni', 11),
(328, '2540867', 'Rifal', 11),
(329, '1440733', 'Anthony Gunawan', 2),
(330, '2440863', 'Amel Ryan Miftah', 2),
(331, '1640784', 'Bayu Prawijaya', 2),
(332, '2340852', 'Dimas', 2),
(333, '0740075', 'Erlan Zaelani', 2),
(334, '1540770', 'Edi Sujarwo', 2),
(335, '2040839', 'Fajar Rusandi', 2),
(336, '1340717', 'Hastho Broto', 2),
(337, '', 'Iskandar Marjuni', 2),
(338, '1840810', 'M. Nurhadi', 2),
(339, '2040840', 'M. Ali Aulia', 2),
(340, '2340853', 'Nanang Jamaludin', 2),
(341, '2040847', 'Nurrohman', 2),
(342, '9640134', 'Oman Suganda', 2),
(343, '1940815', 'Puspo Wardoyo', 2),
(344, '0740083', 'Rachmad Iwan P', 2),
(345, '2340851', 'Ricky Hermawan', 2),
(346, '2040841', 'Roki Setiawan', 2),
(347, '1140571', 'Septo Trihandoko', 2),
(348, '1240627', 'Sudaryanto', 2),
(349, '1540771', 'Subaktio Purwanto', 2),
(350, '1940822', 'Sugito', 2),
(351, '', 'Usep Saepul', 2),
(352, '2340860', 'Wahyudi', 2),
(353, '0440031', ' Iwan Gunawan ', 6),
(354, '9040102', ' Ade Aryani ', 6),
(355, '2340858', ' Herlina ', 10),
(356, '9540032', ' Dadang Sanusi ', 10),
(357, '1040503', ' Musarofah ', 10),
(358, '9140070', ' Nurlaela ', 10),
(359, '0840368', ' Faijar Dwi N. ', 9),
(360, '9040153', ' Mardiyono ', 9),
(361, '0140031', ' Wiyono', 9),
(362, '1340714', ' Yayan IR ', 9),
(363, '1640781', ' Arrohman S ', 9),
(364, '9140018', ' Andi Sumarta ', 8),
(365, '9240128', ' Qomari ', 8),
(366, '1540763', ' Felya Gabi Megan ', 8),
(367, '1440744', ' Fransisca Irawaty ', 8),
(368, '1640778', 'Anthony Wijaya', 7),
(369, '1140546', 'Lydia Fitri', 7),
(370, '1140601', 'Deden Saputra', 7),
(371, '1840814', 'Pyas Anggit Ditira', 7),
(372, '1640787', 'Reza Dewantoro', 7),
(373, '1940816', 'Yoselin Pardosi', 7),
(374, '1940826', 'Agus Setiawan', 7),
(375, '9640118', 'Budi Santoso K.', 7),
(376, '2240848', 'Sumantri Arifin', 7),
(377, '2040837', 'Deni', 7),
(378, '0440023', 'Johan Budihartanto', 4),
(379, '0640295', 'Brian Jonathan', 4),
(380, '9340033', 'Ova S.', 4),
(381, '1240643', 'M. Pamungkas', 4),
(382, '2040846', 'Alvin Suganda', 4),
(383, '1640785', 'Danish Artha', 4),
(384, '1640779', 'Chatarina Wigati', 4),
(385, '2340860', 'Errin Taruna', 4),
(386, '1440738', 'Dhyan Sulistiyono', 4);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `absensi`
--
ALTER TABLE `absensi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `karyawan_id` (`karyawan_id`);

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `departemen`
--
ALTER TABLE `departemen`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `karyawan`
--
ALTER TABLE `karyawan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `departemen_id` (`departemen_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `absensi`
--
ALTER TABLE `absensi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=120;

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `departemen`
--
ALTER TABLE `departemen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `karyawan`
--
ALTER TABLE `karyawan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=387;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `absensi`
--
ALTER TABLE `absensi`
  ADD CONSTRAINT `absensi_ibfk_1` FOREIGN KEY (`karyawan_id`) REFERENCES `karyawan` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `karyawan`
--
ALTER TABLE `karyawan`
  ADD CONSTRAINT `karyawan_ibfk_1` FOREIGN KEY (`departemen_id`) REFERENCES `departemen` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
