-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 25, 2026 at 12:54 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `nete_new`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` char(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'unique id',
  `key_value` char(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'That unique key'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `key_value`) VALUES
('netedemo070', '7f83b1657ff1fc53b92dc18148a1d65dfc2d4b1fa3d677284addd200126d9069');

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE `files` (
  `id` char(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'unique id',
  `Uploader` char(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Foreign Key',
  `file_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'file name',
  `file_link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'link to path',
  `size` bigint NOT NULL COMMENT 'size',
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Files Type',
  `sha256_hash` char(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'The unique hash of the file',
  `parent_folder_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Special ID for folder',
  `mime_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Mime extension of the files',
  `share` tinyint(1) NOT NULL COMMENT 'Available for other user to read?',
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When this appear?',
  `file_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Is file tamper?\r\n0=ok, 1=not ok.',
  `last_check` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'The last time of tamper check'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `note`
--

CREATE TABLE `note` (
  `id` char(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Unique',
  `Whois` char(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Foreign key',
  `text` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Text_Content',
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When?',
  `public_value` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Is public or not?',
  `tag` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'for CSS. yea...'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `preference`
--

CREATE TABLE `preference` (
  `userid` char(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'id',
  `background` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'B1' COMMENT 'a background :D'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `report`
--

CREATE TABLE `report` (
  `id` char(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Unique ID',
  `cause` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'What is cause',
  `user` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Who cause this',
  `IP` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'User IP address',
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When it cause'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `iduser` char(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Unique id',
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'user name',
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'user password',
  `salt` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'A secured salt',
  `comment` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'comment on user',
  `storage_allocated` bigint NOT NULL COMMENT 'How many storage size allow for this user?'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='This for user database';

-- --------------------------------------------------------

--
-- Table structure for table `user_report`
--

CREATE TABLE `user_report` (
  `id` char(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `USER_ID` char(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ITEM_TYPE` enum('file','note') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ITEM_ID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `REASON` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `DATE` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `verification_locks`
--

CREATE TABLE `verification_locks` (
  `verify_id` char(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`);

--
-- Indexes for table `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_UPLOADER` (`Uploader`) USING BTREE;

--
-- Indexes for table `note`
--
ALTER TABLE `note`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_WHOIS` (`Whois`) USING BTREE;

--
-- Indexes for table `preference`
--
ALTER TABLE `preference`
  ADD PRIMARY KEY (`userid`);

--
-- Indexes for table `report`
--
ALTER TABLE `report`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`iduser`);

--
-- Indexes for table `user_report`
--
ALTER TABLE `user_report`
  ADD PRIMARY KEY (`id`),
  ADD KEY `USER_ID` (`USER_ID`);

--
-- Indexes for table `verification_locks`
--
ALTER TABLE `verification_locks`
  ADD PRIMARY KEY (`verify_id`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `files`
--
ALTER TABLE `files`
  ADD CONSTRAINT `FK_USER_OWNER` FOREIGN KEY (`Uploader`) REFERENCES `user` (`iduser`);

--
-- Constraints for table `note`
--
ALTER TABLE `note`
  ADD CONSTRAINT `FK_USER` FOREIGN KEY (`Whois`) REFERENCES `user` (`iduser`);

--
-- Constraints for table `preference`
--
ALTER TABLE `preference`
  ADD CONSTRAINT `FK_USERID` FOREIGN KEY (`userid`) REFERENCES `user` (`iduser`);

--
-- Constraints for table `user_report`
--
ALTER TABLE `user_report`
  ADD CONSTRAINT `user_report_ibfk_1` FOREIGN KEY (`USER_ID`) REFERENCES `user` (`iduser`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
