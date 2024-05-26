-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 26, 2024 at 08:14 PM
-- Server version: 8.0.32
-- PHP Version: 8.1.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `flashcards`
--

-- --------------------------------------------------------

--
-- Table structure for table `woerter_txt`
--

CREATE TABLE `woerter_txt` (
  `COL 1` varchar(3) NOT NULL,
  `COL 2` varchar(7) DEFAULT NULL,
  `COL 3` varchar(55) DEFAULT NULL,
  `COL 4` varchar(25) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `COL 5` varchar(55) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `COL 6` varchar(55) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `COL 7` varchar(25) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `COL 8` varchar(7) DEFAULT NULL,
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `woerter_txt`
--

INSERT INTO `woerter_txt` (`COL 1`, `COL 2`, `COL 3`, `COL 4`, `COL 5`, `COL 6`, `COL 7`, `COL 8`, `Col 9`) VALUES
('10', '(Verb)', 'behaupten', 'hat behauptet', '', 'claim', 'studio d B1', '6', ''),
('100', '(Verb)', 'sinken', 'ist gesunken', '', 'sink', 'studio d B1', '6', ''),
('101', 'das', 'Skigebiet', '-e', '', 'Ski Area', 'studio d B1', '6', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `woerter_txt`
--
ALTER TABLE `woerter_txt`
  ADD PRIMARY KEY (`COL 1`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
