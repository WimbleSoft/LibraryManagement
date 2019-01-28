-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jan 28, 2019 at 09:19 AM
-- Server version: 5.7.19
-- PHP Version: 5.6.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `libraryrecords`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

DROP TABLE IF EXISTS `books`;
CREATE TABLE IF NOT EXISTS `books` (
  `bookId` int(11) NOT NULL AUTO_INCREMENT,
  `bookName` varchar(50) NOT NULL,
  `bookWriter` varchar(30) NOT NULL,
  `bookStatus` int(11) NOT NULL,
  PRIMARY KEY (`bookId`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`bookId`, `bookName`, `bookWriter`, `bookStatus`) VALUES
(4, 'Mass Effect - Keşif1', 'Drew Karpyshyn2', 0);

-- --------------------------------------------------------

--
-- Table structure for table `computers`
--

DROP TABLE IF EXISTS `computers`;
CREATE TABLE IF NOT EXISTS `computers` (
  `computerId` int(11) NOT NULL AUTO_INCREMENT,
  `manufacturer` varchar(25) NOT NULL,
  `model` varchar(25) NOT NULL,
  `serialNo` varchar(25) NOT NULL,
  `computerStatus` int(11) NOT NULL,
  PRIMARY KEY (`computerId`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `computers`
--

INSERT INTO `computers` (`computerId`, `manufacturer`, `model`, `serialNo`, `computerStatus`) VALUES
(7, 'Dell-s', 'ASD1234', '122345', 1);

-- --------------------------------------------------------

--
-- Table structure for table `keys`
--

DROP TABLE IF EXISTS `keys`;
CREATE TABLE IF NOT EXISTS `keys` (
  `keyId` int(11) NOT NULL AUTO_INCREMENT,
  `keyNumber` varchar(20) NOT NULL,
  `keyContent` text NOT NULL,
  `keyStatus` int(11) NOT NULL,
  PRIMARY KEY (`keyId`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `keys`
--

INSERT INTO `keys` (`keyId`, `keyNumber`, `keyContent`, `keyStatus`) VALUES
(4, '12456213', 'Salon Anahtarıs', 0);

-- --------------------------------------------------------

--
-- Table structure for table `loans`
--

DROP TABLE IF EXISTS `loans`;
CREATE TABLE IF NOT EXISTS `loans` (
  `loanId` int(11) NOT NULL AUTO_INCREMENT,
  `productId` int(11) NOT NULL,
  `type` varchar(10) NOT NULL,
  `loanDate` date NOT NULL,
  `lenderId` int(11) NOT NULL,
  `loanerId` varchar(30) NOT NULL,
  `returnDate` date NOT NULL,
  `returnAccepterId` int(30) NOT NULL,
  PRIMARY KEY (`loanId`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `loans`
--

INSERT INTO `loans` (`loanId`, `productId`, `type`, `loanDate`, `lenderId`, `loanerId`, `returnDate`, `returnAccepterId`) VALUES
(34, 7, 'Bilgisayar', '2016-09-06', 1, '1', '2019-01-09', 1);

-- --------------------------------------------------------

--
-- Table structure for table `personnel`
--

DROP TABLE IF EXISTS `personnel`;
CREATE TABLE IF NOT EXISTS `personnel` (
  `personnelId` int(11) NOT NULL AUTO_INCREMENT,
  `userName` varchar(4020) NOT NULL,
  `nameSurname` varchar(50) NOT NULL,
  `passWord` text NOT NULL,
  `eMail` varchar(50) NOT NULL,
  `phone` text NOT NULL,
  `auth` varchar(2) NOT NULL,
  PRIMARY KEY (`personnelId`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `personnel`
--

INSERT INTO `personnel` (`personnelId`, `userName`, `nameSurname`, `passWord`, `eMail`, `phone`, `auth`) VALUES
(1, 'fatik', 'Fatih Sağlam', '81dc9bdb52d04dc20036dbd8313ed055', 'silentwimble@hotmail.com', '5422121727', '1'),
(2, 'asdasd', 'Ali Çatalbaş', '81dc9bdb52d04dc20036dbd8313ed055', 'asdasd@gmail.com', '456512215', '1');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
