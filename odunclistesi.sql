-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 06 Eyl 2016, 01:22:30
-- Sunucu sürümü: 5.6.17
-- PHP Sürümü: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Veritabanı: `odunclistesi`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `anahtarlar`
--

CREATE TABLE IF NOT EXISTS `anahtarlar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numara` varchar(20) NOT NULL,
  `icerigi` text NOT NULL,
  `durum` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Tablo döküm verisi `anahtarlar`
--

INSERT INTO `anahtarlar` (`id`, `numara`, `icerigi`, `durum`) VALUES
(4, '12456213', 'Salon Anahtarıs', '0');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `bilgisayarlar`
--

CREATE TABLE IF NOT EXISTS `bilgisayarlar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `marka` varchar(25) NOT NULL,
  `model` varchar(25) NOT NULL,
  `serino` varchar(25) NOT NULL,
  `durum` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Tablo döküm verisi `bilgisayarlar`
--

INSERT INTO `bilgisayarlar` (`id`, `marka`, `model`, `serino`, `durum`) VALUES
(7, 'Dell-s', 'ASD1234', '122345', 1);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `kitaplar`
--

CREATE TABLE IF NOT EXISTS `kitaplar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kitapadi` varchar(50) NOT NULL,
  `yazari` varchar(30) NOT NULL,
  `durum` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Tablo döküm verisi `kitaplar`
--

INSERT INTO `kitaplar` (`id`, `kitapadi`, `yazari`, `durum`) VALUES
(4, 'Mass Effect - Keşif1', 'Drew Karpyshyn2', '0');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `oduncler`
--

CREATE TABLE IF NOT EXISTS `oduncler` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `urunid` int(11) NOT NULL,
  `tur` varchar(10) NOT NULL,
  `odunctarihi` date NOT NULL,
  `oduncveren` int(11) NOT NULL,
  `oduncalan` varchar(30) NOT NULL,
  `iadetarihi` date NOT NULL,
  `iadealan` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=35 ;

--
-- Tablo döküm verisi `oduncler`
--

INSERT INTO `oduncler` (`id`, `urunid`, `tur`, `odunctarihi`, `oduncveren`, `oduncalan`, `iadetarihi`, `iadealan`) VALUES
(34, 7, 'Bilgisayar', '2016-09-06', 1, '1', '0000-00-00', '');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `personel`
--

CREATE TABLE IF NOT EXISTS `personel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kadi` varchar(20) NOT NULL,
  `adsoyad` varchar(50) NOT NULL,
  `sifre` text NOT NULL,
  `eposta` varchar(50) NOT NULL,
  `telefon` text NOT NULL,
  `yetki` varchar(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Tablo döküm verisi `personel`
--

INSERT INTO `personel` (`id`, `kadi`, `adsoyad`, `sifre`, `eposta`, `telefon`, `yetki`) VALUES
(1, 'fatik', 'Fatih Sağlam', '81dc9bdb52d04dc20036dbd8313ed055', 'silentwimble@hotmail.com', '5422121727', '1'),
(2, 'asdasd', 'Ali Çatalbaş', '81dc9bdb52d04dc20036dbd8313ed055', 'asdasd@gmail.com', '456512215', '1');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
