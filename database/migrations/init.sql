-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 20, 2018 at 10:09 AM
-- Server version: 5.5.38-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "-07:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `forge`
--

-- --------------------------------------------------------

--
-- Table structure for table `bags`
--

CREATE TABLE IF NOT EXISTS `bags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `part_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `marked` tinyint(1) NOT NULL,
  `delivered` tinyint(1) NOT NULL,
  `delivery_id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL DEFAULT '0',
  `delivered_by` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=59 ;

-- --------------------------------------------------------

--
-- Table structure for table `config`
--

CREATE TABLE IF NOT EXISTS `config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ckey` longtext NOT NULL,
  `value` longtext NOT NULL,
  `setting_name` varchar(64) NOT NULL,
  `setting_description` longtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `id_2` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `config`
--

INSERT INTO `config` (`id`, `ckey`, `value`, `setting_name`, `setting_description`) VALUES
(1, 'dev_mode', '1', 'Developer Mode', 'Developer mode lets you see everything, including some outputs into console.'),
(2, 'show_locations', '0', 'Show Locations', 'Enables the "Locations" route and provides a link under Data > Locations in the Navbar.'),
(3, 'auto_generate_url', '0', 'Auto-Generation URL', 'This link is used to auto-generate parts, part profiles, filaments, and printers.'),
(4, 'generate_from_url_tree', '0', 'Auto-Generate', 'If on, use "/regenerate" to regenerate all of the parts, profiles, filaments, and printers from the URL tree. If no URL is set, this won''t work.'),
(5, 'use_aleph_delivery_method', '1', 'Aleph Delivery Method', 'This adds an input field next to bags during delivery prep to mark which container that bag is in. Those numbers will get entered into a table where they can be viewed again with a button next to the delivery on /deliveries/all.');

-- --------------------------------------------------------

--
-- Table structure for table `deliveries`
--

CREATE TABLE IF NOT EXISTS `deliveries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;

-- --------------------------------------------------------

--
-- Table structure for table `filaments`
--

CREATE TABLE IF NOT EXISTS `filaments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filament_name` varchar(64) NOT NULL,
  `background_color` varchar(7) NOT NULL,
  `text_color` varchar(7) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

--
-- Dumping data for table `filaments`
--

INSERT INTO `filaments` (`id`, `filament_name`, `background_color`, `text_color`, `active`, `updated_at`, `created_at`) VALUES
(1, 'Black', '#FFFFFF', '#000000', 1, '2018-09-10 04:00:00', '0000-00-00 00:00:00'),
(2, 'Lulzbot Green', '#66FF66', '#000000', 1, '2018-09-10 13:22:40', '0000-00-00 00:00:00'),
(3, 'Hammer Gray', '#4d4d4d', '#FFFFFF', 1, '2018-09-10 04:00:00', '0000-00-00 00:00:00'),
(4, 'Ghost Gray', '#d9d9d9', '#000000', 0, '2018-09-10 17:57:53', '0000-00-00 00:00:00'),
(5, 'Ninjaflex Black', '#a71fa7', '#ffffff', 1, '2018-09-18 15:43:39', '2018-09-18 15:43:39'),
(6, 'Ninjaflex Green', '#00ffa7', '#000000', 1, '2018-09-18 15:47:17', '2018-09-18 15:47:17');

-- --------------------------------------------------------

--
-- Table structure for table `inventories`
--

CREATE TABLE IF NOT EXISTS `inventories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `part_id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `to_total` int(11) NOT NULL,
  `from_total` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4318 ;

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE IF NOT EXISTS `locations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `location_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `location_description` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `admin_only` tinyint(1) NOT NULL COMMENT 'Is this a default location for data entry?',
  `required` tinyint(4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=20 ;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`id`, `location_name`, `location_description`, `admin_only`, `required`, `created_at`, `updated_at`) VALUES
(1, 'Collections', 'Green/Yellow bins by K and L pods.\r\n', 0, 1, '2018-07-18 03:01:52', '2018-09-18 12:49:49'),
(2, 'Processing', 'Blue/Black bins by E and F pod. ', 0, 1, '2018-07-18 03:05:50', '2018-09-18 12:49:53'),
(3, 'Backstock', 'Green Shelves by the door to cluster and above the printer cabinets.', 0, 1, '2018-07-18 03:06:29', '2018-09-18 12:49:56'),
(4, 'Fails', 'No specific locations. Used to calculate fails, filaments, and rates.', 1, 1, '2018-07-18 03:07:08', '2018-09-18 12:49:59'),
(5, 'InHouse', 'For parts used in house.', 0, 1, '2018-08-17 20:39:57', '2018-09-18 12:50:02'),
(6, 'Initial', 'This is used when initializing ACIT.', 1, 1, '2018-09-04 22:58:20', '2018-09-18 12:50:04');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE IF NOT EXISTS `migrations` (
  `migration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`migration`, `batch`) VALUES
('2014_10_12_000000_create_users_table', 1),
('2014_10_12_100000_create_password_resets_table', 1),
('2018_07_15_175712_create_transfers_table', 1),
('2018_07_15_181430_create_locations_table', 1),
('2018_07_15_181446_create_parts_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `part_id` int(11) NOT NULL,
  `priority` tinyint(1) NOT NULL DEFAULT '0',
  `quantity` int(11) DEFAULT '0',
  `filled` int(11) NOT NULL DEFAULT '0',
  `closed` tinyint(4) NOT NULL,
  `mo` varchar(32) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=589 ;

-- --------------------------------------------------------

--
-- Table structure for table `overages`
--

CREATE TABLE IF NOT EXISTS `overages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `part_id` int(11) NOT NULL,
  `delivery_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `resolved` int(11) NOT NULL,
  `ov_mo` varchar(16) NOT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `parts`
--

CREATE TABLE IF NOT EXISTS `parts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `part_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `part_serial` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `part_version` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `part_color` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `part_mass` float NOT NULL,
  `part_waste` float NOT NULL,
  `recommended_bagging` int(11) NOT NULL,
  `part_cleaned` tinyint(1) NOT NULL,
  `in_moratorium` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=171 ;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `password_resets_email_index` (`email`),
  KEY `password_resets_token_index` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `printers`
--

CREATE TABLE IF NOT EXISTS `printers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=33 ;

--
-- Dumping data for table `printers`
--

INSERT INTO `printers` (`id`, `name`, `active`, `created_at`, `updated_at`) VALUES
(1, 'Mini', 1, '2018-08-24 17:52:27', '2018-08-24 17:52:27'),
(2, 'Mini2', 1, '2018-08-24 17:52:27', '2018-09-10 13:22:42'),
(3, 'Taz6', 1, '2018-08-24 17:52:27', '2018-08-22 21:26:38'),
(4, 'Taz7', 1, '2018-08-24 17:52:27', '2018-09-18 13:29:08');

-- --------------------------------------------------------

--
-- Table structure for table `print_profiles`
--

CREATE TABLE IF NOT EXISTS `print_profiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `part_id` int(11) NOT NULL,
  `printer_id` int(11) NOT NULL,
  `lead_time` int(11) NOT NULL,
  `prints` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3088 ;

-- --------------------------------------------------------

--
-- Table structure for table `transfers`
--

CREATE TABLE IF NOT EXISTS `transfers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `part_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `reversal` tinyint(4) NOT NULL,
  `to_location_id` int(11) NOT NULL,
  `from_location_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=89 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `first_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `active` tinyint(1) NOT NULL,
  `admin` tinyint(1) NOT NULL,
  `account_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0: inactive, 1: default, 2:admin',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=15 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `first_name`, `last_name`, `username`, `email`, `password`, `remember_token`, `active`, `admin`, `account_type`, `created_at`, `updated_at`) VALUES
(5, '', 'Admin', 'Overlord', 'admin', 'admin', '', 1, 1, 2, '2018-07-21 03:03:35', '2018-09-13 18:34:42');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
