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


/*!40101 SET @OLD_CHARACTER_SET_CLIENT = @@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS = @@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION = @@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `delphiscreenshottestsuite`
--
CREATE DATABASE IF NOT EXISTS `delphiscreenshottestsuite`
  DEFAULT CHARACTER SET latin1
  COLLATE latin1_swedish_ci;
USE `delphiscreenshottestsuite`;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
CREATE TABLE `comments` (
  `project` VARCHAR(255)
            CHARACTER SET utf8          DEFAULT NULL,
  `test`    VARCHAR(255)
            CHARACTER SET utf8 NOT NULL DEFAULT '',
  `comment` VARCHAR(255)                DEFAULT NULL,
  `time`    DATETIME                    DEFAULT NULL
)
  ENGINE = InnoDB
  DEFAULT CHARSET = latin1;

-- --------------------------------------------------------

--
-- Table structure for table `job_warteschlange`
--

DROP TABLE IF EXISTS `job_warteschlange`;
CREATE TABLE `job_warteschlange` (
  `ID`         INT(11) NOT NULL,
  `project`    VARCHAR(255) DEFAULT NULL,
  `user_email` VARCHAR(255) DEFAULT NULL,
  `Datum`      DATETIME     DEFAULT CURRENT_TIMESTAMP
)
  ENGINE = InnoDB
  DEFAULT CHARSET = latin1;

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
CREATE TABLE `projects` (
  `ID`       INT(11) NOT NULL,
  `title`    VARCHAR(255) DEFAULT NULL,
  `status`   TINYINT(1)   DEFAULT NULL,
  `ratio`    VARCHAR(255) DEFAULT NULL,
  `duration` VARCHAR(32)  DEFAULT ''
)
  ENGINE = InnoDB
  DEFAULT CHARSET = latin1;

-- --------------------------------------------------------

--
-- Table structure for table `subscribers`
--

DROP TABLE IF EXISTS `subscribers`;
CREATE TABLE `subscribers` (
  `project` VARCHAR(255) DEFAULT NULL,
  `email`   VARCHAR(255) DEFAULT NULL,
  `ID`      INT(11) NOT NULL
)
  ENGINE = InnoDB
  DEFAULT CHARSET = latin1;

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
  ADD UNIQUE KEY `title` (`title`);

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
  MODIFY `ID` INT(11) NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 5;
--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `ID` INT(11) NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 10;
--
-- AUTO_INCREMENT for table `subscribers`
--
ALTER TABLE `subscribers`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
