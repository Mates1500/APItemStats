-- phpMyAdmin SQL Dump
-- version 4.1.4
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Aug 28, 2015 at 08:50 PM
-- Server version: 5.6.15-log
-- PHP Version: 5.5.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `apitems`
--

-- --------------------------------------------------------

--
-- Table structure for table `cacheddata`
--

CREATE TABLE IF NOT EXISTS `cacheddata` (
  `ikey` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `region` text NOT NULL,
  `winrate` double NOT NULL,
  `pickrate` double NOT NULL,
  `avgpurchase` int(11) NOT NULL,
  `medpurchase` int(11) NOT NULL,
  `patch` text NOT NULL,
  PRIMARY KEY (`ikey`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1082 ;

-- --------------------------------------------------------

--
-- Table structure for table `itemstats`
--

CREATE TABLE IF NOT EXISTS `itemstats` (
  `key` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `item_name` text NOT NULL,
  `item_description` text NOT NULL,
  `region` text NOT NULL,
  `winrate` double NOT NULL,
  `popularity` bigint(20) NOT NULL,
  `purchase_timestamps` longblob NOT NULL,
  `patch` text NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1085 ;

-- --------------------------------------------------------

--
-- Table structure for table `scannedmatches`
--

CREATE TABLE IF NOT EXISTS `scannedmatches` (
  `key` int(11) NOT NULL AUTO_INCREMENT,
  `match_id` bigint(20) NOT NULL,
  `region` text NOT NULL,
  `useful` tinyint(1) NOT NULL,
  `patch` text NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=8283 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
