-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: hoggar.elkdata.ee
-- Generation Time: Jun 10, 2025 at 06:04 PM
-- Server version: 11.4.5-MariaDB-log
-- PHP Version: 8.2.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `vhost137852s1`
--

-- --------------------------------------------------------

--
-- Table structure for table `broneeringud`
--

CREATE TABLE `broneeringud` (
  `id` int(11) NOT NULL,
  `klient_id` int(11) DEFAULT NULL,
  `kylaline_id` int(11) DEFAULT NULL,
  `toa_id` int(11) NOT NULL,
  `saabumine` date NOT NULL,
  `lahkumine` date NOT NULL,
  `loomis_aeg` timestamp NULL DEFAULT current_timestamp(),
  `staatus` enum('ootel','kinnitatud','tühistatud','lõpetatud') DEFAULT 'ootel'
) ;

--
-- Dumping data for table `broneeringud`
--

INSERT INTO `broneeringud` (`id`, `klient_id`, `kylaline_id`, `toa_id`, `saabumine`, `lahkumine`, `loomis_aeg`, `staatus`) VALUES
(17, NULL, NULL, 6, '2025-07-03', '2025-07-05', '2025-06-09 11:12:38', 'ootel'),
(18, 2, NULL, 6, '2025-06-26', '2025-06-28', '2025-06-09 11:21:40', 'tühistatud'),
(19, NULL, NULL, 21, '2025-06-09', '2025-06-10', '2025-06-09 17:31:48', 'ootel'),
(20, NULL, NULL, 22, '2025-06-09', '2025-06-10', '2025-06-09 17:31:50', 'ootel'),
(21, NULL, NULL, 23, '2025-06-09', '2025-06-10', '2025-06-09 17:31:52', 'ootel'),
(22, NULL, 35, 24, '2025-06-09', '2025-06-10', '2025-06-09 19:22:46', 'ootel'),
(23, NULL, NULL, 25, '2025-06-09', '2025-06-10', '2025-06-09 19:33:41', 'kinnitatud'),
(24, NULL, 39, 16, '2025-06-05', '2025-06-11', '2025-06-10 05:33:12', 'kinnitatud'),
(25, NULL, NULL, 17, '2025-06-05', '2025-06-11', '2025-06-10 05:41:52', 'kinnitatud'),
(26, NULL, 44, 1, '2025-06-18', '2025-06-29', '2025-06-10 06:57:35', 'kinnitatud'),
(27, 9, NULL, 2, '2025-06-18', '2025-06-29', '2025-06-10 06:58:57', 'kinnitatud'),
(28, NULL, 47, 6, '2025-06-17', '2025-06-26', '2025-06-10 07:29:32', 'ootel'),
(29, NULL, 49, 16, '2025-06-11', '2026-06-11', '2025-06-10 08:03:58', 'kinnitatud'),
(30, NULL, 51, 21, '2025-06-10', '2027-12-11', '2025-06-10 11:07:42', 'kinnitatud');

-- --------------------------------------------------------

--
-- Table structure for table `broneeringu_teenused`
--

CREATE TABLE `broneeringu_teenused` (
  `id` int(11) NOT NULL,
  `broneering_id` int(11) NOT NULL,
  `teenus_id` int(11) NOT NULL,
  `kogus` int(11) DEFAULT 1,
  `hind` decimal(10,2) NOT NULL,
  `lisatud_aeg` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `broneeringu_teenused`
--

INSERT INTO `broneeringu_teenused` (`id`, `broneering_id`, `teenus_id`, `kogus`, `hind`, `lisatud_aeg`) VALUES
(68, 17, 7, 1, 10.00, '2025-06-09 11:12:38'),
(69, 17, 2, 1, 5.00, '2025-06-09 11:12:38'),
(70, 17, 4, 1, 25.00, '2025-06-09 11:12:38'),
(71, 18, 5, 1, 0.00, '2025-06-09 11:21:40'),
(72, 18, 7, 1, 10.00, '2025-06-09 11:21:41'),
(73, 19, 7, 1, 10.00, '2025-06-09 17:31:48'),
(74, 19, 6, 1, 3.00, '2025-06-09 17:31:49'),
(75, 20, 7, 1, 10.00, '2025-06-09 17:31:50'),
(76, 20, 6, 1, 3.00, '2025-06-09 17:31:50'),
(77, 21, 7, 1, 10.00, '2025-06-09 17:31:52'),
(78, 21, 6, 1, 3.00, '2025-06-09 17:31:52'),
(79, 22, 7, 1, 10.00, '2025-06-09 19:22:46'),
(80, 22, 4, 1, 25.00, '2025-06-09 19:22:46'),
(81, 22, 6, 1, 3.00, '2025-06-09 19:22:46'),
(82, 23, 5, 1, 0.00, '2025-06-09 19:33:41'),
(83, 23, 7, 1, 10.00, '2025-06-09 19:33:41'),
(84, 23, 2, 1, 5.00, '2025-06-09 19:33:41'),
(85, 24, 1, 1, 12.50, '2025-06-10 05:33:12'),
(86, 24, 2, 1, 5.00, '2025-06-10 05:33:12'),
(87, 25, 1, 1, 12.50, '2025-06-10 05:41:52'),
(88, 25, 7, 1, 10.00, '2025-06-10 05:41:52'),
(89, 25, 4, 1, 25.00, '2025-06-10 05:41:52'),
(90, 26, 1, 1, 12.50, '2025-06-10 06:57:35'),
(91, 26, 3, 1, 0.00, '2025-06-10 06:57:35'),
(92, 26, 5, 1, 0.00, '2025-06-10 06:57:35'),
(93, 26, 7, 1, 10.00, '2025-06-10 06:57:35'),
(94, 26, 2, 1, 5.00, '2025-06-10 06:57:35'),
(95, 26, 4, 1, 25.00, '2025-06-10 06:57:35'),
(96, 26, 8, 1, 20.00, '2025-06-10 06:57:35'),
(97, 26, 6, 1, 3.00, '2025-06-10 06:57:35'),
(98, 27, 1, 1, 12.50, '2025-06-10 06:58:57'),
(99, 27, 5, 1, 0.00, '2025-06-10 06:58:57'),
(100, 27, 7, 1, 10.00, '2025-06-10 06:58:57'),
(101, 27, 4, 1, 25.00, '2025-06-10 06:58:57'),
(102, 27, 6, 1, 3.00, '2025-06-10 06:58:57'),
(103, 28, 7, 1, 10.00, '2025-06-10 07:29:32'),
(104, 28, 4, 1, 25.00, '2025-06-10 07:29:32'),
(105, 28, 6, 1, 3.00, '2025-06-10 07:29:32'),
(106, 29, 1, 1, 12.50, '2025-06-10 08:03:58'),
(107, 29, 5, 1, 0.00, '2025-06-10 08:03:58'),
(108, 29, 4, 1, 25.00, '2025-06-10 08:03:58'),
(109, 30, 3, 1, 0.00, '2025-06-10 11:07:42'),
(110, 30, 7, 1, 10.00, '2025-06-10 11:07:42'),
(111, 30, 4, 1, 25.00, '2025-06-10 11:07:42'),
(112, 30, 6, 1, 3.00, '2025-06-10 11:07:42');

-- --------------------------------------------------------

--
-- Table structure for table `kasutajad`
--

CREATE TABLE `kasutajad` (
  `id` int(11) NOT NULL,
  `kasutajanimi` varchar(50) DEFAULT NULL,
  `parool` varchar(255) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `email_kinnituskood` varchar(32) DEFAULT NULL,
  `email_kinnitatud` tinyint(1) DEFAULT 0,
  `loomis_aeg` timestamp NULL DEFAULT current_timestamp(),
  `roll` enum('admin','töötaja','klient') DEFAULT 'klient',
  `email_koodi_aeg` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kasutajad`
--

INSERT INTO `kasutajad` (`id`, `kasutajanimi`, `parool`, `email`, `email_kinnituskood`, `email_kinnitatud`, `loomis_aeg`, `roll`, `email_koodi_aeg`) VALUES
(2, 'kaspar', '$2y$10$9BFVKUQmR0.E1VCdTD4tIOImwjEStVlO6TQlDauIid3IIC5n80XNq', 'plaaskaspar@gmail.com', NULL, 1, '2025-06-09 07:48:13', 'klient', NULL),
(4, 'admin', '$2y$10$NGd4I3r8yWkDgxRkuQzE2.glMkssDgVSoSdFCl1Z5aJptgdft8FZK', 'admin@gmail.com', 'ec9c853b90cf60748eaa4a30417022aa', 1, '2025-06-09 19:35:15', 'admin', NULL),
(6, 'otammik', '$2y$10$u4MgrksHcWBvgDs/OdyN8.uyVpczVKSzBSNeSAVlW9dzwKfxdS99a', 'ott.tammik@hkhk.edu.ee', NULL, 1, '2025-06-10 06:58:27', 'klient', NULL),
(7, 'jussu', '$2y$10$qOQugw3Uueu0ItJPITk2VO3RC1QoYUnjbMgXaDjFkrM0ggZ2HkCPq', 'roomet.altmae@gmail.com', NULL, 1, '2025-06-10 07:23:48', 'klient', NULL),
(8, 'matu', '$2y$10$x.OqVMQZP80ed0UMAdKGU.0CPO76YkvZqrQynWzedhKvKrtIewEGi', 'mattias.elmers@gmail.com', NULL, 1, '2025-06-10 08:06:33', 'klient', NULL),
(9, 'hendri', '$2y$10$P2Cwgd967iqjY0Zc0yE87.FsSUdXnp2EaEIcsPs2oicmGgZk3bWem', 'hendriolev@gmail.com', NULL, 1, '2025-06-10 11:08:34', 'klient', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `kliendid`
--

CREATE TABLE `kliendid` (
  `id` int(11) NOT NULL,
  `kasutaja_id` int(11) DEFAULT NULL,
  `eesnimi` varchar(50) NOT NULL,
  `perenimi` varchar(50) NOT NULL,
  `telefon` varchar(20) NOT NULL,
  `isikukood` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kliendid`
--

INSERT INTO `kliendid` (`id`, `kasutaja_id`, `eesnimi`, `perenimi`, `telefon`, `isikukood`) VALUES
(2, 2, 'kaspar', 'plaas', '+37254664204', '50209154215'),
(5, 4, 'admin', 'admin', '+37254664204', '50209154215'),
(8, 6, 'ott', 'tammik', '+37254664204', '12345432634'),
(9, 6, '', '', '', ''),
(10, 7, 'Juhan', 'Maasikas', '+37258231942', '12345678911'),
(11, 8, 'Mattias', 'Elmers', '+37212343535', '12345678901'),
(12, 9, 'hendri', 'serman', '+37254664204', '12345432634');

-- --------------------------------------------------------

--
-- Table structure for table `kylalised`
--

CREATE TABLE `kylalised` (
  `id` int(11) NOT NULL,
  `eesnimi` varchar(50) NOT NULL,
  `perenimi` varchar(50) NOT NULL,
  `isikukood` varchar(20) DEFAULT NULL,
  `telefon` varchar(20) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `loodud` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kylalised`
--

INSERT INTO `kylalised` (`id`, `eesnimi`, `perenimi`, `isikukood`, `telefon`, `email`, `loodud`) VALUES
(27, 'Kaspar', 'admin', '50209154215', '54664204', 'plaaskaspar@gmail.com', '2025-06-09 11:12:31'),
(29, 'Kaspar', 'Plaas', '50209154215', '54664204', 'plaaskaspar@gmail.com', '2025-06-09 17:31:16'),
(34, 'Kaspar', 'Plaas', '50209154215', '54664204', 'plaaskaspar@gmail.com', '2025-06-09 19:22:37'),
(35, '', '', '', '', '', '2025-06-09 19:22:46'),
(36, 'admin', 'admin', '50209154215', '54664204', 'admin@admin.com', '2025-06-09 19:33:14'),
(37, 'admin', 'admin', '50209154215', '54664204', 'admin@admin.com', '2025-06-09 19:33:32'),
(38, 'joosep', 'alasoo', '50000000000', '56821234', 'joosepalasoo528@gmail.com', '2025-06-10 05:31:28'),
(39, '', '', '', '', '', '2025-06-10 05:33:12'),
(40, 'joosep', 'alasoo', '50000000000', '+37256821243', 'joosepalasoo528@gmail.com', '2025-06-10 05:41:22'),
(41, 'admin', 'admin', '50209154215', '+37254664204', 'admin@gmail.com', '2025-06-10 06:56:15'),
(42, 'admin', 'admin', '50209154215', '+37254664204', 'admin@gmail.com', '2025-06-10 06:56:29'),
(43, 'Ott', 'Tammik', '1234546547457', '12345678', 'ott.tammik@hkhk.edu.ee', '2025-06-10 06:57:22'),
(44, '', '', '', '', '', '2025-06-10 06:57:35'),
(45, 'ott', 'tammik', '12345432634', '+37254664204', 'ott.tammik@hkhk.edu.ee', '2025-06-10 06:58:52'),
(46, 'Ott', 'Tammik', '1234546547457', '12345678', 'ott.tammik@hkhk.edu.ee', '2025-06-10 07:29:26'),
(47, '', '', '', '', '', '2025-06-10 07:29:32'),
(48, 'Mattias', 'Elmer', '12345678901', '12345677', 'mattias.elmers@gmail.com', '2025-06-10 08:03:27'),
(49, '', '', '', '', '', '2025-06-10 08:03:58'),
(50, 'Hendri', 'Serman', '12345678911', '12345678', 'hendriolev@gmail.com', '2025-06-10 11:07:09'),
(51, '', '', '', '', '', '2025-06-10 11:07:42');

-- --------------------------------------------------------

--
-- Table structure for table `maksed`
--

CREATE TABLE `maksed` (
  `id` int(11) NOT NULL,
  `broneering_id` int(11) NOT NULL,
  `summa` decimal(10,2) NOT NULL,
  `staatus` enum('ootel','tasutud','tühistatud') DEFAULT 'ootel',
  `makseviis` enum('pangaülekanne','sularaha','krediitkaart') DEFAULT NULL,
  `stripe_id` varchar(255) DEFAULT NULL,
  `tahtaeg` date NOT NULL,
  `loomis_aeg` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `maksed`
--

INSERT INTO `maksed` (`id`, `broneering_id`, `summa`, `staatus`, `makseviis`, `stripe_id`, `tahtaeg`, `loomis_aeg`) VALUES
(2, 17, 528.00, 'tasutud', 'krediitkaart', 'cs_test_a16wMKQDlk8QCbXMXoKqLHNghgizoxRqn30HWNPzK9uYqBwmS9B92OrX6r', '2025-06-10', '2025-06-09 11:12:38'),
(3, 18, 492.00, 'tasutud', 'krediitkaart', 'cs_test_a1SEkd1QKRfX3fDC6ClsLPL1wdZycdw7nHji1PrMEZRi4YEThZoohjNLvM', '2025-06-10', '2025-06-09 11:21:41'),
(4, 19, 135.60, 'ootel', 'krediitkaart', 'cs_test_a1jujTxHL9mWom7COTanpDH3BtMOzD25FmyxIqNwFJjnIArenObR1EAlIM', '2025-06-10', '2025-06-09 17:31:49'),
(5, 20, 135.60, 'ootel', 'krediitkaart', 'cs_test_a1qyjmF2BkRqJ5Zlc7mBtiw69ScQwrZKnB9RVvFz6DhDuEcqGdV5WBCGR7', '2025-06-10', '2025-06-09 17:31:50'),
(6, 21, 135.60, 'ootel', 'krediitkaart', 'cs_test_a1xB6qudc2VmW2SjVyutHD7uSjHAEexvSCPQSWmvG7NV5ZTzp7JtavhEdJ', '2025-06-10', '2025-06-09 17:31:52'),
(7, 22, 165.60, 'tasutud', 'krediitkaart', 'cs_test_a1GrRSO8rciVsJ52z5ZAFygmHdKalEcbyO1zV8kZpD7OjP75BWHTcAibLt', '2025-06-10', '2025-06-09 19:22:46'),
(8, 23, 138.00, 'tasutud', 'krediitkaart', 'cs_test_a1swdSLm03kiPR9sOQ5LHvxVMviNk2NexmVnXK5sOJnn0YAjCZttjTLmn1', '2025-06-10', '2025-06-09 19:33:41'),
(9, 24, 1101.00, 'tasutud', 'krediitkaart', 'cs_test_a1BZ4yz65o2g6McOIblDWBrsRJxF2g1P7jb1OLawmZmOp6mwlBjt8JgJdH', '2025-06-11', '2025-06-10 05:33:12'),
(10, 25, 1137.00, 'tasutud', 'krediitkaart', 'cs_test_a1avUy1iuRSc79DoxU7hZfR4NO00EIJwti6ftBGuaZV7tL30HfxXqtYsnb', '2025-06-11', '2025-06-10 05:41:52'),
(11, 26, 3390.60, 'tasutud', 'krediitkaart', 'cs_test_a1jlchKRHSGQ7di1hpwNHP8BnI8NAiye49ZoXv05WAhMviyHpfwOsu4S7e', '2025-06-11', '2025-06-10 06:57:35'),
(12, 27, 3360.60, 'tasutud', 'krediitkaart', 'cs_test_a1UxlHK02xqsIFmiaYWkVsgSGxNXrh8B0W2eHD1RuYtKhbgdWKHWiDLLOo', '2025-06-11', '2025-06-10 06:58:57'),
(13, 28, 2205.60, 'ootel', 'krediitkaart', 'cs_test_a1q6ntu5vaHDOu6UHGT8vlkoIJULPXw3IZueH2QvhrTYQP1aqdKYWuMO7p', '2025-06-11', '2025-06-10 07:29:32'),
(14, 29, 65745.00, 'tasutud', 'krediitkaart', 'cs_test_a15fdP8fC7ktquwVbWR0RkBRKhrUi3H3wwXrlo5muI4YKXC9QVm9Zvtxec', '2025-06-11', '2025-06-10 08:03:58'),
(15, 30, 109730.60, 'tasutud', 'krediitkaart', 'cs_test_a1S8b7qzYkNoBJSXDAW1VATMsSvmSE757FDeZ3MmRoJ4JlV5k2XxOu5Uim', '2025-06-11', '2025-06-10 11:07:42');

-- --------------------------------------------------------

--
-- Table structure for table `teenused`
--

CREATE TABLE `teenused` (
  `id` int(11) NOT NULL,
  `teenus` varchar(50) NOT NULL,
  `hind` decimal(10,2) NOT NULL,
  `kirjeldus` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teenused`
--

INSERT INTO `teenused` (`id`, `teenus`, `hind`, `kirjeldus`) VALUES
(1, 'Hommikusöök', 12.50, 'Kohvikus serveeritav rikkalik hommikusöök valikuga puuviljad, saiakesed, munaroog, jogurtid ja kuumad joogid.'),
(2, 'Parkimine', 5.00, 'Kindlustatud parkimine hotelli parklas kogu ööpäeva jooksul.'),
(3, 'Lastevoodi', 0.00, 'Lisavoodi väikelapsele toas (vajalik ette teatada).'),
(4, 'SPA-kasutus', 25.00, 'Piiramatu ligipääs SPA-alale, sealhulgas saun, bassein ja jakuzzi.'),
(5, 'Laua broneerimine restoranis', 0.00, 'Eelnevalt broneeritud laua tagamine hotelli restoranis soovitud ajal.'),
(6, 'WiFi Premium', 3.00, 'Kiirem ja piiramatu internetiühendus kogu hotellialal.'),
(7, 'Loomadega reisimine', 10.00, 'Lemmiklooma majutamine toas (koerte/kasside jaoks, maksimaalne kaal 10 kg).'),
(8, 'Tasuline hilja välja checkimine', 20.00, 'Võimalus hilinenud väljaregistreerumiseks kuni kella 16ni.');

-- --------------------------------------------------------

--
-- Table structure for table `toad`
--

CREATE TABLE `toad` (
  `id` int(11) NOT NULL,
  `toa_id` int(11) NOT NULL,
  `toa_nr` varchar(10) NOT NULL,
  `toa_korrus` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `toad`
--

INSERT INTO `toad` (`id`, `toa_id`, `toa_nr`, `toa_korrus`) VALUES
(1, 1, '101', 1),
(2, 1, '102', 1),
(3, 1, '201', 2),
(4, 1, '202', 2),
(5, 1, '301', 3),
(6, 2, '103', 1),
(7, 2, '104', 1),
(8, 2, '203', 2),
(9, 2, '204', 2),
(10, 2, '302', 3),
(11, 3, '105', 1),
(12, 3, '205', 2),
(13, 3, '206', 2),
(14, 3, '303', 3),
(15, 3, '304', 3),
(16, 4, '106', 1),
(17, 4, '207', 2),
(18, 4, '208', 2),
(19, 4, '305', 3),
(20, 4, '306', 3),
(21, 5, '107', 1),
(22, 5, '209', 2),
(23, 5, '210', 2),
(24, 5, '307', 3),
(25, 5, '308', 3),
(26, 6, '108', 1),
(27, 6, '211', 2),
(28, 6, '212', 2),
(29, 6, '309', 3),
(30, 6, '310', 3);

-- --------------------------------------------------------

--
-- Table structure for table `toa_tyyp`
--

CREATE TABLE `toa_tyyp` (
  `id` int(11) NOT NULL,
  `toa_tyyp` varchar(50) NOT NULL,
  `toa_hind` decimal(10,2) NOT NULL,
  `toa_kirjeldus` text DEFAULT NULL,
  `toa_maht` int(11) NOT NULL,
  `toa_pilt` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `toa_tyyp`
--

INSERT INTO `toa_tyyp` (`id`, `toa_tyyp`, `toa_hind`, `toa_kirjeldus`, `toa_maht`, `toa_pilt`) VALUES
(1, 'Sviit', 250.00, 'Luksuslik sviit', 4, '../pildid/toad/sviit.jpg'),
(2, 'Lux', 200.00, 'Stiilne ja avar', 3, '../pildid/toad/lux.jpg'),
(3, 'Peretuba', 180.00, 'Suur tuba perele', 4, '../pildid/toad/peretuba.jpg'),
(4, 'Deluxe', 150.00, 'Mugav deluxe-tuba', 2, '../pildid/toad/deluxe.jpg'),
(5, 'Standard', 100.00, 'Tavaline standardtuba', 2, '../pildid/toad/standard.jpg'),
(6, 'Premium', 220.00, 'Premium mugavustega tuba', 3, '../pildid/toad/premium.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `broneeringud`
--
ALTER TABLE `broneeringud`
  ADD PRIMARY KEY (`id`),
  ADD KEY `klient_id` (`klient_id`),
  ADD KEY `kylaline_id` (`kylaline_id`),
  ADD KEY `toa_id` (`toa_id`);

--
-- Indexes for table `broneeringu_teenused`
--
ALTER TABLE `broneeringu_teenused`
  ADD PRIMARY KEY (`id`),
  ADD KEY `broneering_id` (`broneering_id`),
  ADD KEY `teenus_id` (`teenus_id`);

--
-- Indexes for table `kasutajad`
--
ALTER TABLE `kasutajad`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kasutajanimi` (`kasutajanimi`);

--
-- Indexes for table `kliendid`
--
ALTER TABLE `kliendid`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kasutaja_id` (`kasutaja_id`);

--
-- Indexes for table `kylalised`
--
ALTER TABLE `kylalised`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `maksed`
--
ALTER TABLE `maksed`
  ADD PRIMARY KEY (`id`),
  ADD KEY `broneering_id` (`broneering_id`);

--
-- Indexes for table `teenused`
--
ALTER TABLE `teenused`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `toad`
--
ALTER TABLE `toad`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `toa_nr` (`toa_nr`),
  ADD KEY `toa_id` (`toa_id`);

--
-- Indexes for table `toa_tyyp`
--
ALTER TABLE `toa_tyyp`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `broneeringud`
--
ALTER TABLE `broneeringud`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `broneeringu_teenused`
--
ALTER TABLE `broneeringu_teenused`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113;

--
-- AUTO_INCREMENT for table `kasutajad`
--
ALTER TABLE `kasutajad`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `kliendid`
--
ALTER TABLE `kliendid`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `kylalised`
--
ALTER TABLE `kylalised`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `maksed`
--
ALTER TABLE `maksed`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `teenused`
--
ALTER TABLE `teenused`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `toad`
--
ALTER TABLE `toad`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `toa_tyyp`
--
ALTER TABLE `toa_tyyp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `broneeringud`
--
ALTER TABLE `broneeringud`
  ADD CONSTRAINT `broneeringud_ibfk_1` FOREIGN KEY (`klient_id`) REFERENCES `kliendid` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `broneeringud_ibfk_2` FOREIGN KEY (`kylaline_id`) REFERENCES `kylalised` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `broneeringud_ibfk_3` FOREIGN KEY (`toa_id`) REFERENCES `toad` (`id`);

--
-- Constraints for table `broneeringu_teenused`
--
ALTER TABLE `broneeringu_teenused`
  ADD CONSTRAINT `broneeringu_teenused_ibfk_1` FOREIGN KEY (`broneering_id`) REFERENCES `broneeringud` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `broneeringu_teenused_ibfk_2` FOREIGN KEY (`teenus_id`) REFERENCES `teenused` (`id`);

--
-- Constraints for table `kliendid`
--
ALTER TABLE `kliendid`
  ADD CONSTRAINT `kliendid_ibfk_1` FOREIGN KEY (`kasutaja_id`) REFERENCES `kasutajad` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `maksed`
--
ALTER TABLE `maksed`
  ADD CONSTRAINT `maksed_ibfk_1` FOREIGN KEY (`broneering_id`) REFERENCES `broneeringud` (`id`);

--
-- Constraints for table `toad`
--
ALTER TABLE `toad`
  ADD CONSTRAINT `toad_ibfk_1` FOREIGN KEY (`toa_id`) REFERENCES `toa_tyyp` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
