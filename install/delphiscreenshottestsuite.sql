-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 20, 2017 at 04:10 PM
-- Server version: 5.7.15-log
-- PHP Version: 7.0.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `delphiscreenshottestsuite`
--
CREATE DATABASE IF NOT EXISTS `delphiscreenshottestsuite` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `delphiscreenshottestsuite`;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
CREATE TABLE `comments` (
  `project` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `test` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `comment` varchar(255) DEFAULT NULL,
  `time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `job_warteschlange`
--

DROP TABLE IF EXISTS `job_warteschlange`;
CREATE TABLE `job_warteschlange` (
  `ID` int(11) NOT NULL,
  `project` varchar(255) DEFAULT NULL,
  `user_email` varchar(255) DEFAULT NULL,
  `Datum` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
CREATE TABLE `projects` (
  `ID` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `ratio` varchar(255) DEFAULT NULL,
  `duration` varchar(32) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `subscribers`
--

DROP TABLE IF EXISTS `subscribers`;
CREATE TABLE `subscribers` (
  `project` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`test`);

--
-- Indexes for table `job_warteschlange`
--
ALTER TABLE `job_warteschlange`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `title` (`title`),
  ADD UNIQUE KEY `title_2` (`title`),
  ADD UNIQUE KEY `title_3` (`title`),
  ADD UNIQUE KEY `title_4` (`title`),
  ADD UNIQUE KEY `title_5` (`title`),
  ADD UNIQUE KEY `title_6` (`title`),
  ADD UNIQUE KEY `title_7` (`title`),
  ADD UNIQUE KEY `title_8` (`title`),
  ADD UNIQUE KEY `title_9` (`title`),
  ADD UNIQUE KEY `title_10` (`title`),
  ADD UNIQUE KEY `title_11` (`title`),
  ADD UNIQUE KEY `title_12` (`title`),
  ADD UNIQUE KEY `title_13` (`title`),
  ADD UNIQUE KEY `title_14` (`title`),
  ADD UNIQUE KEY `title_15` (`title`),
  ADD UNIQUE KEY `title_16` (`title`),
  ADD UNIQUE KEY `title_17` (`title`),
  ADD UNIQUE KEY `title_18` (`title`),
  ADD UNIQUE KEY `title_19` (`title`),
  ADD UNIQUE KEY `title_20` (`title`),
  ADD UNIQUE KEY `title_21` (`title`),
  ADD UNIQUE KEY `title_22` (`title`),
  ADD UNIQUE KEY `title_23` (`title`),
  ADD UNIQUE KEY `title_24` (`title`),
  ADD UNIQUE KEY `title_25` (`title`),
  ADD UNIQUE KEY `title_26` (`title`),
  ADD UNIQUE KEY `title_27` (`title`),
  ADD UNIQUE KEY `title_28` (`title`),
  ADD UNIQUE KEY `title_29` (`title`),
  ADD UNIQUE KEY `title_30` (`title`),
  ADD UNIQUE KEY `title_31` (`title`),
  ADD UNIQUE KEY `title_32` (`title`),
  ADD UNIQUE KEY `title_33` (`title`),
  ADD UNIQUE KEY `title_34` (`title`),
  ADD UNIQUE KEY `title_35` (`title`),
  ADD UNIQUE KEY `title_36` (`title`),
  ADD UNIQUE KEY `title_37` (`title`),
  ADD UNIQUE KEY `title_38` (`title`),
  ADD UNIQUE KEY `title_39` (`title`),
  ADD UNIQUE KEY `title_40` (`title`),
  ADD UNIQUE KEY `title_41` (`title`),
  ADD UNIQUE KEY `title_42` (`title`),
  ADD UNIQUE KEY `title_43` (`title`),
  ADD UNIQUE KEY `title_44` (`title`),
  ADD UNIQUE KEY `title_45` (`title`),
  ADD UNIQUE KEY `title_46` (`title`),
  ADD UNIQUE KEY `title_47` (`title`),
  ADD UNIQUE KEY `title_48` (`title`),
  ADD UNIQUE KEY `title_49` (`title`),
  ADD UNIQUE KEY `title_50` (`title`),
  ADD UNIQUE KEY `title_51` (`title`),
  ADD UNIQUE KEY `title_52` (`title`),
  ADD UNIQUE KEY `title_53` (`title`),
  ADD UNIQUE KEY `title_54` (`title`),
  ADD UNIQUE KEY `title_55` (`title`),
  ADD UNIQUE KEY `title_56` (`title`),
  ADD UNIQUE KEY `title_57` (`title`),
  ADD UNIQUE KEY `title_58` (`title`),
  ADD UNIQUE KEY `title_59` (`title`),
  ADD UNIQUE KEY `title_60` (`title`),
  ADD UNIQUE KEY `title_61` (`title`),
  ADD UNIQUE KEY `title_62` (`title`),
  ADD UNIQUE KEY `title_63` (`title`);

--
-- Indexes for table `subscribers`
--
ALTER TABLE `subscribers`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `job_warteschlange`
--
ALTER TABLE `job_warteschlange`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `subscribers`
--
ALTER TABLE `subscribers`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
