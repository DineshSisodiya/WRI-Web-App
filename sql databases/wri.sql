-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 26, 2019 at 02:22 PM
-- Server version: 10.1.21-MariaDB
-- PHP Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wri`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `name` varchar(100) CHARACTER SET utf8 NOT NULL,
  `email` varchar(255) CHARACTER SET utf8 NOT NULL,
  `password` varchar(255) CHARACTER SET utf8 NOT NULL,
  `otp` varchar(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`name`, `email`, `password`, `otp`) VALUES
('Dinesh', 'erdscs@gmail.com', '3d0fdcce830d727f7a18cd4616b4c881', NULL),
('yogesh doria', 'yogeshdoria@gmail.com', '3d0fdcce830d727f7a18cd4616b4c881', '736707');

-- --------------------------------------------------------

--
-- Table structure for table `donation`
--

CREATE TABLE `donation` (
  `don_id` bigint(20) NOT NULL,
  `don_amount` int(11) NOT NULL,
  `don_period` varchar(100) NOT NULL,
  `payment_mode` varchar(50) NOT NULL,
  `transaction_id` varchar(5) NOT NULL DEFAULT 'NA',
  `on_date` date NOT NULL,
  `received_by` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `donation`
--

INSERT INTO `donation` (`don_id`, `don_amount`, `don_period`, `payment_mode`, `transaction_id`, `on_date`, `received_by`) VALUES
(5432167890, 27000, 'half-yearly', 'cash', 'NA', '2019-01-17', 'Dinesh'),
(6745675909, 9999, 'two-year', 'cash', 'NA', '2019-01-21', 'Dinesh'),
(6745675999, 5000, 'quarterly', 'cash', 'NA', '2019-01-22', 'Dinesh'),
(6745998877, 27000, 'without-plan', 'paytm', '12345', '2019-01-21', 'Manoj'),
(6747775476, 1000, 'without-plan', 'bhim upi', '88888', '2019-01-26', 'Dinesh'),
(6747775476, 400, 'half-yearly', 'cash', 'NA', '2019-01-16', 'dinesh'),
(7206957203, 1187, 'half-yearly', 'phonpe', '12345', '2019-01-26', 'Yogesh');

-- --------------------------------------------------------

--
-- Table structure for table `donors`
--

CREATE TABLE `donors` (
  `mobile` bigint(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `dob` date NOT NULL,
  `whatsapp` bigint(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `blood_group` enum('A+','B+','AB\r\n\r\n+','O+','A-','B-','AB-','O-','UK') NOT NULL,
  `can_donate_blood` char(3) NOT NULL DEFAULT 'no',
  `area` text NOT NULL,
  `tehsil` varchar(255) NOT NULL,
  `district` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL DEFAULT 'india',
  `joined_on` date NOT NULL,
  `revoked_on` date DEFAULT NULL,
  `joined_by_name` varchar(100) NOT NULL,
  `joined_by_mail` varchar(255) NOT NULL,
  `active` char(3) NOT NULL DEFAULT 'yes',
  `is_volunteer` char(3) NOT NULL DEFAULT 'no'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `donors`
--

INSERT INTO `donors` (`mobile`, `first_name`, `last_name`, `dob`, `whatsapp`, `email`, `blood_group`, `can_donate_blood`, `area`, `tehsil`, `district`, `state`, `country`, `joined_on`, `revoked_on`, `joined_by_name`, `joined_by_mail`, `active`, `is_volunteer`) VALUES
(1230987654, 'dinesh', 'sisodiya', '1999-01-20', 0, 'erdscs@gmail.com', 'A-', 'no', 'hjvfgjh', 'hyfj', 'jhyfjh', 'gj', 'yiuyiu', '2019-01-12', NULL, 'Dinesh', 'erdscs@gmail.com', 'yes', 'no'),
(1231231234, 'Ashok', 'sisodiya', '1999-01-19', 0, 'erdscs@gmail.com', 'B+', 'yes', 'hjvfgjh', 'hyfj', 'jhyfjh', 'gj', 'yiuyiu', '2019-01-13', NULL, 'Dinesh', 'erdscs@gmail.com', 'yes', 'yes'),
(1234523456, 'Pavan', 'meena', '1990-01-18', 0, 'erdscs@gmail.com', 'A+', 'no', 'h4', 'thaneshar', 'kurukshetra', 'rajasthan', 'india', '2019-01-12', NULL, 'Dinesh', 'erdscs@gmail.com', 'yes', 'no'),
(1823745960, 'fdds', 'sisodiya', '1999-01-01', 0, 'erdscs@gmail.com', 'B+', 'yes', 'hjvfgjh', 'hyfj', 'jhyfjh', 'gj', 'yiuyiu', '2019-01-12', NULL, 'Dinesh', 'erdscs@gmail.com', 'yes', 'no'),
(3214567890, 'dinu', 'fhf', '1999-01-13', 5432167890, 'www.sisodiyasite@gmail.com', 'A+', 'no', 'fjf', 'yf', 'kjjkj', 'jkgjkjg', 'jgjk', '2019-01-21', NULL, 'Dinesh', 'erdscs@gmail.com', 'yes', 'yes'),
(4567123456, 'dinesh', 'bhil', '2000-01-06', 0, 'www.sisodiyasite@gmail.com', 'A-', 'no', 'h4', 'thaneshar', 'kurukshetra', 'haryana', 'india', '2019-01-12', NULL, 'Dinesh', 'erdscs@gmail.com', 'yes', 'no'),
(5432167890, 'naresh', 'jat', '1999-01-07', 5432167890, 'www.extraid@gmail.com', 'A+', 'no', 'vfjh', 'uyyruy', 'uyuy', 'yuytiu', 'qqytyuy', '2019-01-21', NULL, 'Dinesh', 'erdscs@gmail.com', 'yes', 'yes'),
(6645675476, 'fdds', 'tredyt', '1999-01-01', 9999999999, 'erdscs@gmail.com', 'A+', 'yes', 'hgfjhf', 'ytrfyuf', 'fyrfyu', 'yuruyfru', 'tytrytryt', '2019-01-21', NULL, 'Dinesh', 'erdscs@gmail.com', 'yes', 'no'),
(6745005476, 'dddddd', 'dfsjkfhsdfjk', '1999-01-22', 9999999999, 'erdscs@gmail.com', 'A+', 'no', 'jkjkjhjk', 'jh', 'kjh', 'kjh', 'jkhk', '2019-01-21', NULL, 'Dinesh', 'erdscs@gmail.com', 'yes', 'no'),
(6745675076, 'dinesh', 'sisodiya', '1999-01-01', 0, 'erdscs@gmail.com', 'B+', 'yes', 'hjvfgjh', 'hyfj', 'jhyfjh', 'jyf', 'jkhk', '2019-01-12', NULL, 'Dinesh', 'erdscs@gmail.com', 'yes', 'no'),
(6745675400, 'dinesh', 'sisodiya', '1999-01-01', 0, 'erdscs@gmail.com', 'A-', 'yes', 'hg', 'hyfj', 'jaipur', 'jyf', 'jgj', '2019-01-13', NULL, 'Dinesh', 'erdscs@gmail.com', 'yes', 'yes'),
(6745675470, 'Manoj', 'sisodiya', '1999-01-22', 0, 'erdscs@gmail.com', 'B+', 'yes', '112 shiv shankar colony near sanganer railway station', 'sanganer', 'jaipur', 'rajasthan', 'india', '2019-01-12', NULL, 'Dinesh', 'erdscs@gmail.com', 'yes', 'yes'),
(6745675476, 'Raj', 'sisodiya', '1999-01-20', 0, 'erdscs@gmail.com', 'A+', 'no', 'h4', 'hyfj', 'jhyfjh', 'gj', 'yiuyiu', '2019-01-12', NULL, 'Dinesh', 'erdscs@gmail.com', 'yes', 'no'),
(6745675888, 'etre', 'uyruru', '1999-01-12', 9999999999, 'www.extraid@gmail.com', 'A+', 'yes', 'fhghf', 'hgfhjfhj', 'hgfjhgj', 'mmkjk', 'tiuytiu', '2019-01-21', NULL, 'Dinesh', 'erdscs@gmail.com', 'yes', 'yes'),
(6745675909, 'abcd', 'abcd', '1999-01-26', 6745675909, 'erdscs@gmail.com', 'A+', 'no', 'jhg', 'hg', 'jhg', 'hgjjg', 'jhg', '2019-01-21', NULL, 'Dinesh', 'erdscs@gmail.com', 'yes', 'yes'),
(6745675999, 'Pavan', 'meena', '1992-01-16', 9999999999, 'erdscs@gmail.com', 'A+', 'no', 'hjhgj', 'hjgfjhgj', 'jhgjhgjh', 'jhgjhgj', 'jhgjh', '2019-01-22', NULL, 'Dinesh', 'erdscs@gmail.com', 'yes', 'yes'),
(6745998877, 'dinesh', 'sisodiya', '1999-01-28', 6745998877, 'erdscs@gmail.com', 'B-', 'no', 'jkkjg', 'kjgkg', 'kjg', 'kjg', 'kjkjgkj', '2019-01-21', NULL, 'Dinesh', 'erdscs@gmail.com', 'yes', 'no'),
(6747775476, 'Anand', 'Mohan', '1999-01-28', 6747775476, 'anandmohan@gmail.com', 'B+', 'no', 'kaithal', 'kurukshetra', 'kurukshetra', 'haryana', 'india', '2019-01-26', NULL, 'Dinesh', 'erdscs@gmail.com', 'yes', 'no'),
(7206957203, 'dinesh', 'sisodiya', '1999-01-01', 0, 'erdscs@gmail.com', 'B+', 'yes', 'hjvfgjh', 'sanganer', 'kurukshetra', 'rajasthan', 'india', '2019-01-13', NULL, 'Dinesh', 'erdscs@gmail.com', 'yes', 'no'),
(7206957225, 'Rajesh', 'Garg', '1999-01-13', 7206957225, 'erdscs@gmail.com', 'B-', 'no', 'kaithal', 'thaneshar', 'kurukshetra', 'haryana', 'india', '2019-01-21', NULL, 'Dinesh', 'erdscs@gmail.com', 'yes', 'yes'),
(7206957555, 'Nishchay', 'garg', '1999-01-06', 7206957225, 'ptanhi@gmail.com', 'A+', 'yes', 'kaithal', 'thaneshar', 'kurukshetra', 'haryana', 'india', '2019-01-21', NULL, 'Dinesh', 'erdscs@gmail.com', 'yes', 'no'),
(7206957888, 'yuru', 'yiuy', '1999-01-06', 9999999999, 'yogeshdoria@gmail.com', 'A+', 'no', 'yufyru', 'uyruyr', 'uytiu', 'iuyn', 'bnmn', '2019-01-21', NULL, 'Dinesh', 'erdscs@gmail.com', 'yes', 'yes'),
(8765432190, 'Ayush', 'jain', '1999-02-03', 8765432190, 'ptanhi@gmail.com', 'A+', 'no', 'fdfgd', 'gtdgh', 'yuuy', 'yuruy', 'uuy', '2019-01-21', NULL, 'Dinesh', 'erdscs@gmail.com', 'yes', 'no');

-- --------------------------------------------------------

--
-- Table structure for table `family`
--

CREATE TABLE `family` (
  `don_mobile` bigint(20) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) CHARACTER SET ascii NOT NULL,
  `dob` date NOT NULL,
  `mobile` varchar(10) DEFAULT 'NA',
  `blood_group` char(4) NOT NULL,
  `can_donate_blood` char(3) NOT NULL DEFAULT 'no',
  `relation` char(20) NOT NULL,
  `is_volunteer` char(3) NOT NULL DEFAULT 'no'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `family`
--

INSERT INTO `family` (`don_mobile`, `first_name`, `last_name`, `dob`, `mobile`, `blood_group`, `can_donate_blood`, `relation`, `is_volunteer`) VALUES
(1230987654, 'Manoj', 'Sisodiya', '2005-01-21', 'NA', 'A+', 'no', 'brother', 'yes'),
(1234523456, 'Akshay', 'Meena', '2005-01-21', 'NA', 'A+', 'no', 'brother', 'no'),
(7206957203, 'aaxxd', 'hekds', '1999-01-01', '1234567800', 'A+', 'yes', 'mother', 'yes'),
(7206957203, 'afsad', 'dfsk', '2009-10-08', 'NA', 'A+', 'no', 'brother', 'no'),
(7206957203, 'dfssd', 'dfdsfd', '1990-01-17', 'NA', 'A+', 'no', 'brother', 'no'),
(7206957203, 'ekwewkerwk', 'hekds', '1999-01-12', '1234567890', 'A+', 'no', 'brother', 'no'),
(7206957203, 'Ganga', 'Devi', '2011-01-24', 'NA', 'A+', 'no', 'mother', 'no'),
(7206957203, 'manika', 'sisodiya', '1999-01-01', 'NA', 'A+', 'no', 'sister', 'no'),
(7206957203, 'Manoj', 'Sisodiya', '2005-01-21', 'NA', 'A+', 'no', 'brother', 'no'),
(7206957203, 'Raj', 'Bairwa', '1999-01-26', 'NA', 'B+', 'no', 'brother', 'yes'),
(7206957203, 'Raju', 'Bairwa', '1999-01-26', 'NA', 'B+', 'no', 'brother', 'yes'),
(7206957203, 'Ramsahay', 'Sisodiya', '2016-01-21', 'NA', 'A+', 'no', 'father', 'no'),
(7206957203, 'Vartu', 'sing', '1999-01-28', 'NA', 'A+', 'yes', 'spouse', 'yes'),
(7206957203, 'xxxxxxd', 'hekds', '1999-01-01', '1234567800', 'A+', 'no', 'mother', 'yes');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `donation`
--
ALTER TABLE `donation`
  ADD PRIMARY KEY (`don_id`,`transaction_id`,`on_date`);

--
-- Indexes for table `donors`
--
ALTER TABLE `donors`
  ADD PRIMARY KEY (`mobile`);

--
-- Indexes for table `family`
--
ALTER TABLE `family`
  ADD PRIMARY KEY (`don_mobile`,`first_name`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `donation`
--
ALTER TABLE `donation`
  ADD CONSTRAINT `donation_ibfk_1` FOREIGN KEY (`don_id`) REFERENCES `donors` (`mobile`);

--
-- Constraints for table `family`
--
ALTER TABLE `family`
  ADD CONSTRAINT `family_ibfk_1` FOREIGN KEY (`don_mobile`) REFERENCES `donors` (`mobile`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
