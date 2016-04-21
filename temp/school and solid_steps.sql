-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Apr 21, 2016 at 06:39 PM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `schools`
--
CREATE DATABASE IF NOT EXISTS `schools` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `schools`;

-- --------------------------------------------------------

--
-- Table structure for table `marital_statuses`
--

DROP TABLE IF EXISTS `marital_statuses`;
CREATE TABLE IF NOT EXISTS `marital_statuses` (
  `marital_status_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `marital_status` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `marital_status_abbr` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`marital_status_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `marital_statuses`
--

INSERT INTO `marital_statuses` (`marital_status_id`, `marital_status`, `marital_status_abbr`, `created_at`, `updated_at`) VALUES
(1, 'Married', 'M', NULL, NULL),
(3, 'Single', 'S', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `salutations`
--

DROP TABLE IF EXISTS `salutations`;
CREATE TABLE IF NOT EXISTS `salutations` (
  `salutation_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `salutation` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `salutation_abbr` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`salutation_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Dumping data for table `salutations`
--

INSERT INTO `salutations` (`salutation_id`, `salutation`, `salutation_abbr`) VALUES
(1, 'Mister', 'Mr.'),
(2, 'Mistress', 'Mrs.'),
(3, 'Doctor', 'Dr.'),
(4, 'Miss', 'Miss.');

-- --------------------------------------------------------

--
-- Table structure for table `school_databases`
--

DROP TABLE IF EXISTS `school_databases`;
CREATE TABLE IF NOT EXISTS `school_databases` (
  `school_database_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `host` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `database` varchar(225) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `schools_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`school_database_id`),
  KEY `school_databases_schools_id_index` (`schools_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `schools`
--

DROP TABLE IF EXISTS `schools`;
CREATE TABLE IF NOT EXISTS `schools` (
  `schools_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `full_name` varchar(225) COLLATE utf8_unicode_ci NOT NULL,
  `phone_no` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `motto` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `website` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8_unicode_ci,
  `logo` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `admin_id` int(11) unsigned DEFAULT NULL,
  `status_id` int(10) unsigned NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`schools_id`),
  KEY `status_id` (`status_id`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `schools`
--

INSERT INTO `schools` (`schools_id`, `name`, `full_name`, `phone_no`, `email`, `motto`, `website`, `address`, `logo`, `admin_id`, `status_id`, `created_at`, `updated_at`) VALUES
(1, 'Solid Steps', 'Solid Steps Memorial High', '02830374944', 'solid@steps.high', '', 'www.solidsteps.international', 'Ekotun Egbe, Lagos', '1_logo.png', NULL, 1, '2016-04-17 15:18:14', '2016-04-17 15:18:14'),
(2, 'Jokers', 'Douche Bag', '01893044554', 'joker@douche.bag', 'Light is Power', 'www.joker.douche', 'Malawi.com', '2_logo.jpg', NULL, 2, '2016-04-17 14:57:27', '2016-04-17 15:48:05'),
(3, 'SolidSteps', 'Solid Steps International School', '+2348061539278', 'nondefyde@gmail.com', 'taking solid steps to our vision', 'www.solidsteps.com', '4 ikuna Street Liasu Rd.', '3_logo.png', NULL, 1, '2016-04-19 11:42:16', '2016-04-19 11:43:29');
--
-- Database: `solid_steps`
--
CREATE DATABASE IF NOT EXISTS `solid_steps` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `solid_steps`;

-- --------------------------------------------------------

--
-- Table structure for table `menu_headers`
--

DROP TABLE IF EXISTS `menu_headers`;
CREATE TABLE IF NOT EXISTS `menu_headers` (
  `menu_header_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `menu_header` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `active` int(10) unsigned NOT NULL DEFAULT '1',
  `sequence` int(10) unsigned NOT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`menu_header_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Dumping data for table `menu_headers`
--

INSERT INTO `menu_headers` (`menu_header_id`, `menu_header`, `active`, `sequence`, `type`, `created_at`, `updated_at`) VALUES
(1, 'SETUPS', 1, 10, 1, '2016-03-29 23:30:39', '2016-03-30 20:33:06'),
(2, 'ACCOUNTS', 1, 9, 1, '2016-03-30 20:33:06', '2016-04-17 08:01:38'),
(3, 'RECORDS', 1, 8, 1, '2016-03-31 07:45:49', '2016-03-31 07:45:49'),
(4, 'PORTAL', 1, 1, 2, '2016-04-15 10:41:26', '2016-04-15 10:55:41');

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

DROP TABLE IF EXISTS `menu_items`;
CREATE TABLE IF NOT EXISTS `menu_items` (
  `menu_item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `menu_item` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `menu_item_url` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `menu_item_icon` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `active` int(10) unsigned NOT NULL DEFAULT '1',
  `sequence` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '1',
  `menu_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`menu_item_id`),
  KEY `menu_items_menu_id_index` (`menu_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=10 ;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`menu_item_id`, `menu_item`, `menu_item_url`, `menu_item_icon`, `active`, `sequence`, `type`, `menu_id`, `created_at`, `updated_at`) VALUES
(1, 'SETTINGS', '#', 'fa fa-cogs', 1, '1', 1, 1, '2016-03-30 10:04:10', '2016-03-30 10:04:10'),
(2, 'USERS', '#', 'fa fa-users', 1, '2', 1, 1, '2016-03-30 10:47:28', '2016-03-30 10:47:28'),
(3, 'MY SCHOOL', '#', 'fa fa-home', 1, '1', 1, 2, '2016-03-30 20:35:07', '2016-04-17 11:09:15'),
(4, 'PERSONAL', '#', 'fa fa-user', 1, '2', 1, 2, '2016-03-30 20:35:07', '2016-04-17 11:07:12'),
(5, 'MANAGE', '/sponsors', 'fa fa-list', 1, '1', 1, 4, '2016-04-17 08:05:55', '2016-04-17 10:04:19'),
(7, 'RECORDS', '#', 'fa fa-book', 1, '3', 1, 1, '2016-04-17 09:18:47', '2016-04-17 09:19:02'),
(8, 'SCHOOLS', '#', 'fa fa-home', 1, '1', 1, 1, '2016-04-17 10:46:04', '2016-04-17 11:09:15'),
(9, 'MANAGE', '/staffs', 'fa fa-list', 1, '1', 1, 6, '2016-04-18 20:51:45', '2016-04-18 21:41:57');

-- --------------------------------------------------------

--
-- Table structure for table `menus`
--

DROP TABLE IF EXISTS `menus`;
CREATE TABLE IF NOT EXISTS `menus` (
  `menu_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `menu` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `menu_url` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `active` int(10) unsigned NOT NULL DEFAULT '1',
  `sequence` int(10) unsigned NOT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '1',
  `icon` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `menu_header_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`menu_id`),
  KEY `menus_menu_header_id_index` (`menu_header_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=7 ;

--
-- Dumping data for table `menus`
--

INSERT INTO `menus` (`menu_id`, `menu`, `menu_url`, `active`, `sequence`, `type`, `icon`, `menu_header_id`, `created_at`, `updated_at`) VALUES
(1, 'SYSTEM', '#', 1, 1, 1, 'fa fa-television', 1, '2016-03-29 23:33:49', '2016-03-29 23:33:49'),
(2, 'PROFILE', '#', 1, 3, 1, 'fa fa-book', 2, '2016-03-30 20:33:36', '2016-04-18 21:37:17'),
(4, 'SPONSORS', '#', 1, 1, 1, 'fa fa-users', 2, '2016-04-17 08:01:21', '2016-04-18 21:37:17'),
(5, 'ADD ACCOUNT', '/accounts/create', 1, 5, 1, 'fa fa-user-plus', 2, '2016-04-18 20:48:45', '2016-04-18 21:37:01'),
(6, 'STAFFS', '#', 1, 2, 1, 'fa fa-users', 2, '2016-04-18 20:51:00', '2016-04-18 21:37:17');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `migration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`migration`, `batch`) VALUES
('2016_03_03_195545_create_user_type_table', 1),
('2016_03_03_195633_create_users_table', 1),
('2016_03_03_195659_create_all_menus_table', 1),
('2016_03_05_060819_entrust_setup_tables', 1),
('2016_03_15_050508_create_roles_menus_assoc_tables', 1),
('2016_03_03_195545_create_user_type_table', 1),
('2016_03_03_195633_create_users_table', 1),
('2016_03_03_195659_create_all_menus_table', 1),
('2016_03_05_060819_entrust_setup_tables', 1),
('2016_03_15_050508_create_roles_menus_assoc_tables', 1),
('2016_03_03_195545_create_user_type_table', 1),
('2016_03_03_195633_create_users_table', 1),
('2016_03_03_195659_create_all_menus_table', 1),
('2016_03_05_060819_entrust_setup_tables', 1),
('2016_03_15_050508_create_roles_menus_assoc_tables', 1),
('2016_04_17_104628_create_sponsor_table', 3),
('2016_04_17_121149_create_schools_table', 4),
('2016_04_17_195404_create_school_databases_table', 5),
('2016_04_19_093540_create_salutaions_table', 5),
('2016_04_19_115455_create_marital_statuses_table', 6);

-- --------------------------------------------------------

--
-- Table structure for table `permission_role`
--

DROP TABLE IF EXISTS `permission_role`;
CREATE TABLE IF NOT EXISTS `permission_role` (
  `permission_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `permission_role_role_id_foreign` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `permission_role`
--

INSERT INTO `permission_role` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1),
(16, 1),
(17, 1),
(18, 1),
(19, 1),
(20, 1),
(21, 1),
(22, 1),
(23, 1),
(24, 1),
(25, 1),
(26, 1),
(27, 1),
(28, 1),
(29, 1),
(30, 1),
(31, 1),
(32, 1),
(33, 1),
(34, 1),
(35, 1),
(36, 1),
(37, 1),
(38, 1),
(39, 1),
(40, 1),
(41, 1),
(42, 1),
(43, 1),
(44, 1),
(45, 1),
(46, 1),
(47, 1),
(48, 1),
(49, 1),
(50, 1),
(51, 1),
(52, 1),
(53, 1),
(54, 1),
(55, 1),
(56, 1),
(57, 1),
(58, 1),
(59, 1),
(60, 1),
(61, 1),
(62, 1),
(63, 1),
(64, 1),
(65, 1),
(66, 1),
(1, 2),
(2, 2),
(3, 2),
(4, 2),
(5, 2),
(6, 2),
(7, 2),
(8, 2),
(9, 2),
(10, 2),
(11, 2),
(12, 2),
(13, 2),
(14, 2),
(15, 2),
(16, 2),
(17, 2),
(18, 2),
(19, 2),
(20, 2),
(21, 2),
(22, 2),
(23, 2),
(24, 2),
(25, 2),
(26, 2),
(27, 2),
(28, 2),
(29, 2),
(30, 2),
(31, 2),
(32, 2),
(33, 2),
(34, 2),
(35, 2),
(36, 2),
(37, 2),
(38, 2),
(39, 2),
(40, 2),
(41, 2),
(42, 2),
(43, 2),
(44, 2),
(45, 2),
(46, 2),
(47, 2),
(48, 2),
(49, 2),
(50, 2),
(51, 2),
(52, 2),
(53, 2),
(54, 2),
(55, 2),
(56, 2),
(57, 2),
(58, 2),
(59, 2),
(60, 2),
(61, 2),
(62, 2),
(63, 2),
(64, 2),
(65, 2),
(66, 2),
(1, 3),
(2, 3),
(4, 3),
(9, 3),
(10, 3);

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `permission_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `display_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `uri` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`permission_id`),
  UNIQUE KEY `permissions_name_unique` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=67 ;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`permission_id`, `name`, `display_name`, `description`, `uri`, `created_at`, `updated_at`) VALUES
(1, 'AccountsController@getCreate', '', '', 'accounts/create/', '2016-04-17 13:17:42', '2016-04-19 15:59:32'),
(2, 'AuthController@getLogin', '', '', 'auth/login/', '2016-04-17 13:17:42', '2016-04-19 15:59:32'),
(3, 'AuthController@getLogout', '', '', 'auth/logout/', '2016-04-17 13:17:42', '2016-04-19 15:59:32'),
(4, 'AuthController@getRegister', '', '', 'auth/register/', '2016-04-17 13:17:42', '2016-04-19 15:59:32'),
(5, 'AuthController@logout', '', '', 'logout', '2016-04-17 13:17:42', '2016-04-19 15:59:32'),
(6, 'AuthController@showLoginForm', '', '', 'login', '2016-04-17 13:17:42', '2016-04-19 15:59:33'),
(7, 'AuthController@showRegistrationForm', '', '', 'register', '2016-04-17 13:17:42', '2016-04-19 15:59:33'),
(8, 'DashboardController@getIndex', '', '', 'dashboard/index/', '2016-04-17 13:17:42', '2016-04-19 15:59:33'),
(9, 'DashboardController@getIndexDashboard', '', '', 'dashboard', '2016-04-17 13:17:42', '2016-04-19 15:59:33'),
(10, 'HomeController@getIndex', '', '', 'home', '2016-04-17 13:17:42', '2016-04-19 15:59:33'),
(11, 'HomeController@getIndexHome/index/', '', '', 'home/index/', '2016-04-17 13:17:42', '2016-04-19 15:59:33'),
(12, 'MaritalStatusController@getDelete', '', '', 'marital-statuses/delete/', '2016-04-17 13:17:42', '2016-04-19 15:59:33'),
(13, 'MaritalStatusController@getIndex', '', '', 'marital-statuses/index/', '2016-04-17 13:17:42', '2016-04-19 15:59:33'),
(14, 'MaritalStatusController@getIndexMarital-statuses', '', '', 'marital-statuses', '2016-04-17 13:17:42', '2016-04-19 15:59:33'),
(15, 'MenuController@getDelete', '', '', 'menus/delete/', '2016-04-17 13:17:42', '2016-04-19 15:59:33'),
(16, 'MenuController@getIndex', '', '', 'menus/index/', '2016-04-17 13:17:42', '2016-04-19 15:59:33'),
(17, 'MenuController@getIndexMenus', '', '', 'menus', '2016-04-17 13:17:42', '2016-04-19 15:59:33'),
(18, 'MenuHeaderController@getDelete', '', '', 'menu-headers/delete/', '2016-04-17 13:17:42', '2016-04-19 15:59:33'),
(19, 'MenuHeaderController@getIndex', '', '', 'menu-headers/index/', '2016-04-17 13:17:42', '2016-04-19 15:59:33'),
(20, 'MenuHeaderController@getIndexMenu-headers', '', '', 'menu-headers', '2016-04-17 13:17:42', '2016-04-19 15:59:33'),
(21, 'MenuItemController@getDelete', '', '', 'menu-items/delete/', '2016-04-17 13:17:42', '2016-04-19 15:59:33'),
(22, 'MenuItemController@getIndex', '', '', 'menu-items/index/', '2016-04-17 13:17:42', '2016-04-19 15:59:33'),
(23, 'MenuItemController@getIndexMenu-items', '', '', 'menu-items', '2016-04-17 13:17:42', '2016-04-19 15:59:33'),
(24, 'PasswordController@reset', '', '', 'password/reset', '2016-04-17 13:17:42', '2016-04-19 15:59:33'),
(25, 'PasswordController@sendResetLinkEmail', '', '', 'password/email', '2016-04-17 13:17:42', '2016-04-19 15:59:34'),
(26, 'PasswordController@showResetForm', '', '', 'password/reset/', '2016-04-17 13:17:42', '2016-04-19 15:59:34'),
(27, 'PermissionsController@getIndex', '', '', 'permissions/index/', '2016-04-17 13:17:42', '2016-04-19 15:59:34'),
(28, 'PermissionsController@getIndexPermissions', '', '', 'permissions', '2016-04-17 13:17:42', '2016-04-19 15:59:34'),
(29, 'PermissionsController@getRolesPermissions', '', '', 'permissions/roles-permissions/', '2016-04-17 13:17:42', '2016-04-19 15:59:34'),
(30, 'ProfileController@getEdit', '', '', 'profiles/edit/', '2016-04-17 13:17:42', '2016-04-19 15:59:34'),
(31, 'ProfileController@getIndex', '', '', 'profiles/index/', '2016-04-17 13:17:42', '2016-04-19 15:59:34'),
(32, 'ProfileController@getIndexProfiles', '', '', 'profiles', '2016-04-17 13:17:42', '2016-04-19 15:59:34'),
(33, 'RolesController@getDelete', '', '', 'roles/delete/', '2016-04-17 13:17:42', '2016-04-19 15:59:34'),
(34, 'RolesController@getIndex', '', '', 'roles/index/', '2016-04-17 13:17:42', '2016-04-19 15:59:34'),
(35, 'RolesController@getIndexRoles', '', '', 'roles', '2016-04-17 13:17:42', '2016-04-19 15:59:34'),
(36, 'RolesController@getUsersRoles', '', '', 'roles/users-roles/', '2016-04-17 13:17:42', '2016-04-19 15:59:34'),
(37, 'SalutationController@getDelete', '', '', 'salutations/delete/', '2016-04-17 13:17:42', '2016-04-19 15:59:34'),
(38, 'SalutationController@getIndex', '', '', 'salutations/index/', '2016-04-17 13:17:42', '2016-04-19 15:59:34'),
(39, 'SalutationController@getIndexSalutations', '', '', 'salutations', '2016-04-17 13:17:42', '2016-04-19 15:59:34'),
(40, 'SchoolController@getCreate', '', '', 'schools/create/', '2016-04-17 13:17:42', '2016-04-19 15:59:34'),
(41, 'SchoolController@getDbConfig', '', '', 'schools/db-config/', '2016-04-17 13:17:42', '2016-04-19 15:59:34'),
(42, 'SchoolController@getEdit', '', '', 'schools/edit/', '2016-04-17 13:17:42', '2016-04-19 15:59:34'),
(43, 'SchoolController@getIndex', '', '', 'schools/index/', '2016-04-17 13:17:42', '2016-04-19 15:59:34'),
(44, 'SchoolController@getIndexSchools', '', '', 'schools', '2016-04-17 13:17:42', '2016-04-19 15:59:34'),
(45, 'SchoolController@getSearch', '', '', 'schools/search/', '2016-04-17 13:17:42', '2016-04-19 15:59:34'),
(46, 'SchoolController@getStatus', '', '', 'schools/status/', '2016-04-17 13:17:42', '2016-04-19 15:59:35'),
(47, 'SponsorController@getIndex', '', '', 'sponsors/index/', '2016-04-17 13:17:42', '2016-04-19 15:59:35'),
(48, 'SponsorController@getIndexSponsors', '', '', 'sponsors', '2016-04-17 13:17:42', '2016-04-19 15:59:35'),
(49, 'StaffController@getIndex', '', '', 'staffs/index/', '2016-04-17 13:17:42', '2016-04-19 15:59:35'),
(50, 'StaffController@getIndexStaffs', '', '', 'staffs', '2016-04-17 13:17:42', '2016-04-19 15:59:35'),
(51, 'SubMenuItemController@getDelete', '', '', 'sub-menu-items/delete/', '2016-04-17 13:17:42', '2016-04-19 15:59:35'),
(52, 'SubMenuItemController@getIndex', '', '', 'sub-menu-items/index/', '2016-04-17 13:17:42', '2016-04-19 15:59:35'),
(53, 'SubMenuItemController@getIndexSub-menu-items', '', '', 'sub-menu-items', '2016-04-17 13:17:42', '2016-04-19 15:59:35'),
(54, 'SubMostMenuItemController@getDelete', '', '', 'sub-most-menu-items/delete/', '2016-04-17 13:17:42', '2016-04-19 15:59:35'),
(55, 'SubMostMenuItemController@getIndex', '', '', 'sub-most-menu-items/index/', '2016-04-17 13:17:42', '2016-04-19 15:59:35'),
(56, 'SubMostMenuItemController@getIndexSub-most-menu-items', '', '', 'sub-most-menu-items', '2016-04-17 13:17:42', '2016-04-19 15:59:35'),
(57, 'UserController@getChange', '', '', 'users/change/', '2016-04-17 13:17:42', '2016-04-19 15:59:35'),
(58, 'UserController@getCreate', '', '', 'users/create/', '2016-04-17 13:17:42', '2016-04-19 15:59:35'),
(59, 'UserController@getEdit', '', '', 'users/edit/', '2016-04-19 15:59:35', '2016-04-19 15:59:35'),
(60, 'UserController@getIndex', '', '', 'users/index/', '2016-04-19 15:59:35', '2016-04-19 15:59:35'),
(61, 'UserController@getIndexUsers', '', '', 'users', '2016-04-19 15:59:35', '2016-04-19 15:59:35'),
(62, 'UserController@getStatus', '', '', 'users/status/', '2016-04-19 15:59:35', '2016-04-19 15:59:35'),
(63, 'UserController@getView', '', '', 'users/view/', '2016-04-19 15:59:35', '2016-04-19 15:59:35'),
(64, 'UserTypeController@getDelete', '', '', 'user-types/delete/', '2016-04-19 15:59:35', '2016-04-19 15:59:35'),
(65, 'UserTypeController@getIndex', '', '', 'user-types/index/', '2016-04-19 15:59:35', '2016-04-19 15:59:35'),
(66, 'UserTypeController@getIndexUser-types', '', '', 'user-types', '2016-04-19 15:59:35', '2016-04-19 15:59:35');

-- --------------------------------------------------------

--
-- Table structure for table `role_user`
--

DROP TABLE IF EXISTS `role_user`;
CREATE TABLE IF NOT EXISTS `role_user` (
  `user_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `role_user_role_id_foreign` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `role_user`
--

INSERT INTO `role_user` (`user_id`, `role_id`) VALUES
(1, 1),
(2, 3);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `role_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `display_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_type_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`role_id`),
  UNIQUE KEY `roles_name_unique` (`name`),
  KEY `roles_user_type_id_index` (`user_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `name`, `display_name`, `description`, `user_type_id`, `created_at`, `updated_at`) VALUES
(1, 'developer', 'Developer', 'The software developer', 2, '2016-03-29 23:30:11', '2016-03-31 13:09:43'),
(2, 'super_admin', 'Super Admin', 'System Administrator', 1, '2016-03-30 10:51:57', '2016-03-31 13:08:59'),
(3, 'parent', 'Parent', 'Parent', 3, '2016-04-16 18:25:54', '2016-04-16 18:25:54');

-- --------------------------------------------------------

--
-- Table structure for table `roles_menu_headers`
--

DROP TABLE IF EXISTS `roles_menu_headers`;
CREATE TABLE IF NOT EXISTS `roles_menu_headers` (
  `role_id` int(10) unsigned NOT NULL,
  `menu_header_id` int(10) unsigned DEFAULT NULL,
  KEY `roles_menu_headers_role_id_index` (`role_id`),
  KEY `roles_menu_headers_menu_header_id_index` (`menu_header_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `roles_menu_headers`
--

INSERT INTO `roles_menu_headers` (`role_id`, `menu_header_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(2, 4);

-- --------------------------------------------------------

--
-- Table structure for table `roles_menu_items`
--

DROP TABLE IF EXISTS `roles_menu_items`;
CREATE TABLE IF NOT EXISTS `roles_menu_items` (
  `role_id` int(10) unsigned NOT NULL,
  `menu_item_id` int(10) unsigned DEFAULT NULL,
  KEY `roles_menu_items_role_id_index` (`role_id`),
  KEY `roles_menu_items_menu_item_id_index` (`menu_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `roles_menu_items`
--

INSERT INTO `roles_menu_items` (`role_id`, `menu_item_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(2, 5),
(1, 7),
(2, 7),
(1, 8),
(2, 8),
(2, 3),
(2, 4),
(1, 9),
(2, 9);

-- --------------------------------------------------------

--
-- Table structure for table `roles_menus`
--

DROP TABLE IF EXISTS `roles_menus`;
CREATE TABLE IF NOT EXISTS `roles_menus` (
  `role_id` int(10) unsigned NOT NULL,
  `menu_id` int(10) unsigned DEFAULT NULL,
  KEY `roles_menus_role_id_index` (`role_id`),
  KEY `roles_menus_menu_id_index` (`menu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `roles_menus`
--

INSERT INTO `roles_menus` (`role_id`, `menu_id`) VALUES
(1, 1),
(1, 2),
(1, 4),
(2, 4),
(1, 5),
(2, 5),
(1, 6),
(2, 6);

-- --------------------------------------------------------

--
-- Table structure for table `roles_sub_menu_items`
--

DROP TABLE IF EXISTS `roles_sub_menu_items`;
CREATE TABLE IF NOT EXISTS `roles_sub_menu_items` (
  `role_id` int(10) unsigned NOT NULL,
  `sub_menu_item_id` int(10) unsigned DEFAULT NULL,
  KEY `roles_sub_menu_items_role_id_index` (`role_id`),
  KEY `roles_sub_menu_items_sub_menu_item_id_index` (`sub_menu_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `roles_sub_menu_items`
--

INSERT INTO `roles_sub_menu_items` (`role_id`, `sub_menu_item_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(2, 6),
(1, 7),
(2, 7),
(1, 8),
(2, 8),
(1, 9),
(2, 9),
(1, 10),
(2, 10),
(1, 11),
(2, 11),
(1, 12),
(2, 12),
(1, 13),
(2, 13);

-- --------------------------------------------------------

--
-- Table structure for table `roles_sub_most_menu_items`
--

DROP TABLE IF EXISTS `roles_sub_most_menu_items`;
CREATE TABLE IF NOT EXISTS `roles_sub_most_menu_items` (
  `role_id` int(10) unsigned NOT NULL,
  `sub_most_menu_item_id` int(10) unsigned DEFAULT NULL,
  KEY `roles_sub_most_menu_items_role_id_index` (`role_id`),
  KEY `roles_sub_most_menu_items_sub_most_menu_item_id_index` (`sub_most_menu_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `roles_sub_most_menu_items`
--

INSERT INTO `roles_sub_most_menu_items` (`role_id`, `sub_most_menu_item_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9);

-- --------------------------------------------------------

--
-- Table structure for table `sponsors`
--

DROP TABLE IF EXISTS `sponsors`;
CREATE TABLE IF NOT EXISTS `sponsors` (
  `sponsor_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `titles` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `address` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `city` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `state_id` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `country_id` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `school_id` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`sponsor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `sub_menu_items`
--

DROP TABLE IF EXISTS `sub_menu_items`;
CREATE TABLE IF NOT EXISTS `sub_menu_items` (
  `sub_menu_item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sub_menu_item` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `sub_menu_item_url` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `sub_menu_item_icon` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `active` int(10) unsigned NOT NULL DEFAULT '1',
  `sequence` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '1',
  `menu_item_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`sub_menu_item_id`),
  KEY `sub_menu_items_menu_item_id_index` (`menu_item_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=14 ;

--
-- Dumping data for table `sub_menu_items`
--

INSERT INTO `sub_menu_items` (`sub_menu_item_id`, `sub_menu_item`, `sub_menu_item_url`, `sub_menu_item_icon`, `active`, `sequence`, `type`, `menu_item_id`, `created_at`, `updated_at`) VALUES
(1, 'MANAGE MENUS', '#', 'fa fa-list', 1, '1', 1, 1, '2016-03-30 10:05:26', '2016-03-30 10:05:26'),
(2, 'PERMISSIONS', '#', 'fa fa-lock', 1, '2', 1, 1, '2016-03-30 10:21:39', '2016-03-30 10:41:46'),
(3, 'ROLES', '#', 'fa fa-users', 1, '3', 1, 1, '2016-03-30 10:41:35', '2016-03-30 10:41:46'),
(4, 'CREATE', '/users/create', 'fa fa-user', 1, '1', 1, 2, '2016-03-30 10:49:22', '2016-03-30 10:49:22'),
(5, 'MANAGE', '/users', 'fa fa-users', 1, '2', 1, 2, '2016-03-30 10:49:22', '2016-03-30 10:49:22'),
(6, 'SALUTATIONS', '/salutations', 'fa fa-plus', 1, '1', 1, 7, '2016-04-17 09:22:55', '2016-04-19 08:55:37'),
(7, 'MANAGE', '/schools', 'fa fa-list', 1, '1', 1, 8, '2016-04-17 10:47:21', '2016-04-17 10:47:21'),
(8, 'CREATE', '/schools/create', 'fa fa-plus', 1, '2', 1, 8, '2016-04-17 10:47:58', '2016-04-17 10:47:58'),
(9, 'VIEW', '/profiles', 'fa fa-eye', 1, '1', 1, 4, '2016-04-17 11:06:59', '2016-04-17 11:06:59'),
(10, 'EDIT', '/profiles/edit', 'fa fa-edit', 1, '2', 1, 4, '2016-04-17 11:07:00', '2016-04-17 11:07:00'),
(11, 'UPDATE', '/schools/profile', 'fa fa-edit', 1, '1', 1, 3, '2016-04-17 11:08:39', '2016-04-19 16:08:28'),
(12, 'USER TYPES', '/user-types', 'fa fa-user-plus', 1, '3', 1, 2, '2016-04-18 20:38:12', '2016-04-18 20:38:39'),
(13, 'M. STAUESES', '/marital-statuses', 'fa fa-table', 1, '2', 1, 7, '2016-04-19 10:58:11', '2016-04-19 10:58:28');

-- --------------------------------------------------------

--
-- Table structure for table `sub_most_menu_items`
--

DROP TABLE IF EXISTS `sub_most_menu_items`;
CREATE TABLE IF NOT EXISTS `sub_most_menu_items` (
  `sub_most_menu_item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sub_most_menu_item` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `sub_most_menu_item_url` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `sub_most_menu_item_icon` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `active` int(10) unsigned NOT NULL DEFAULT '1',
  `sequence` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '1',
  `sub_menu_item_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`sub_most_menu_item_id`),
  KEY `sub_most_menu_items_sub_menu_item_id_index` (`sub_menu_item_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=10 ;

--
-- Dumping data for table `sub_most_menu_items`
--

INSERT INTO `sub_most_menu_items` (`sub_most_menu_item_id`, `sub_most_menu_item`, `sub_most_menu_item_url`, `sub_most_menu_item_icon`, `active`, `sequence`, `type`, `sub_menu_item_id`, `created_at`, `updated_at`) VALUES
(1, 'Header', '/menu-headers', 'fa fa-list', 1, '1', 1, 1, '2016-03-30 10:15:33', '2016-03-30 10:15:33'),
(2, 'Menu', '/menus', 'fa fa-list', 1, '2', 1, 1, '2016-03-30 10:16:39', '2016-03-30 10:16:39'),
(3, 'Menu Items', '/menu-items', 'fa fa-list', 1, '3', 1, 1, '2016-03-30 10:17:42', '2016-03-30 10:18:54'),
(4, 'Sub Menu', '/sub-menu-items', 'fa fa-list', 1, '4', 1, 1, '2016-03-30 10:18:54', '2016-03-30 10:18:54'),
(5, 'Sub-most Menu', '/sub-most-menu-items', 'fa fa-list', 1, '5', 1, 1, '2016-03-30 10:19:42', '2016-03-30 10:25:09'),
(6, 'Manage', '/permissions', 'fa fa-list', 1, '1', 1, 2, '2016-03-30 10:24:08', '2016-03-30 10:25:56'),
(7, 'Assign', '/permissions/roles-permissions/', 'fa fa-users', 1, '2', 1, 2, '2016-03-30 10:34:38', '2016-03-30 10:35:07'),
(8, 'Manage', '/roles', 'fa fa-table', 1, '1', 1, 3, '2016-03-30 10:43:20', '2016-03-30 10:43:20'),
(9, 'Assign', '/roles/users-roles', 'fa fa-users', 1, '2', 1, 3, '2016-03-30 10:43:20', '2016-03-30 10:43:20');

-- --------------------------------------------------------

--
-- Table structure for table `user_types`
--

DROP TABLE IF EXISTS `user_types`;
CREATE TABLE IF NOT EXISTS `user_types` (
  `user_type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_type` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `type` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`user_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Dumping data for table `user_types`
--

INSERT INTO `user_types` (`user_type_id`, `user_type`, `type`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 1, '2016-04-06 23:00:00', NULL),
(2, 'Admin', 1, '2016-04-20 23:00:00', NULL),
(3, 'Sponsor', 2, '2016-04-28 08:29:31', '2016-04-18 21:38:01'),
(4, 'Staff', 2, '2016-04-18 21:38:01', '2016-04-18 21:38:01');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` tinyint(4) NOT NULL,
  `password` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `first_name` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `user_type_id` int(10) unsigned NOT NULL,
  `verified` int(10) unsigned NOT NULL DEFAULT '0',
  `status` int(10) unsigned NOT NULL DEFAULT '1',
  `gender` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone_no` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `verification_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_user_type_id_index` (`user_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `title`, `password`, `email`, `first_name`, `last_name`, `user_type_id`, `verified`, `status`, `gender`, `phone_no`, `dob`, `avatar`, `verification_code`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 1, '$2y$10$J6VM0.ySq0icTRaDtXjjI.i7MWJy6UUlPDgmJ3ygFUxDxJ/MeAk5G', 'admin@gmail.com', 'Emmanuel', 'Okafor', 1, 1, 1, 'Male', '08061539278', '2016-04-14', '1_avatar.jpg', NULL, 'uPMv7hYCF6NeQ9xn6dFrzzaOOi69VWYqLtoGayAYcdUF588IVceA6zUzhdcC', NULL, '2016-04-18 20:51:50'),
(2, 0, '$2y$10$J6VM0.ySq0icTRaDtXjjI.i7MWJy6UUlPDgmJ3ygFUxDxJ/MeAk5G', 'parent@gmail.com', 'John', 'Mario', 3, 1, 1, 'Male', '08022443355', '2010-12-10', '2_avatar.jpg', NULL, 'K6UqxlqmItGWW7AjT8oQy5ioRwxhiHiGuR336AA8I8Zeo5zAIF13qdTmqYDI', NULL, '2016-04-16 19:53:31'),
(3, 0, '$2y$10$azCiEhPih3MoK4MZHZtG3OH47NAQ7tNO270.TXTZbWLNsFU.NkTA.', 'nondefyde@gmail.com', 'Okafor', 'Emmanuel', 3, 1, 1, NULL, '+2348061539278', NULL, NULL, '4PN6o76Oebp7HOjuLEWSraJi4aVhi6uV1wiyrUm5', NULL, '2016-04-18 21:36:00', '2016-04-18 21:36:00'),
(4, 2, '$2y$10$J6VM0.ySq0icTRaDtXjjI.i7MWJy6UUlPDgmJ3ygFUxDxJ/MeAk5G', 'staff@gmail.com', 'Martins', 'Copuer', 4, 1, 1, 'Male', '08022443355', '2010-12-10', '4_avatar.png', NULL, 'K6UqxlqmItGWW7AjT8oQy5ioRwxhiHiGuR336AA8I8Zeo5zAIF13qdTmqYDI', NULL, '2016-04-19 08:26:20');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `permission_role`
--
ALTER TABLE `permission_role`
  ADD CONSTRAINT `permission_role_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`permission_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `permission_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `role_user`
--
ALTER TABLE `role_user`
  ADD CONSTRAINT `role_user_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `role_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `roles_menu_headers`
--
ALTER TABLE `roles_menu_headers`
  ADD CONSTRAINT `roles_menu_headers_menu_header_id_foreign` FOREIGN KEY (`menu_header_id`) REFERENCES `menu_headers` (`menu_header_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `roles_menu_headers_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE CASCADE;

--
-- Constraints for table `roles_menu_items`
--
ALTER TABLE `roles_menu_items`
  ADD CONSTRAINT `roles_menu_items_menu_item_id_foreign` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`menu_item_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `roles_menu_items_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE CASCADE;

--
-- Constraints for table `roles_menus`
--
ALTER TABLE `roles_menus`
  ADD CONSTRAINT `roles_menus_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`menu_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `roles_menus_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE CASCADE;

--
-- Constraints for table `roles_sub_menu_items`
--
ALTER TABLE `roles_sub_menu_items`
  ADD CONSTRAINT `roles_sub_menu_items_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `roles_sub_menu_items_sub_menu_item_id_foreign` FOREIGN KEY (`sub_menu_item_id`) REFERENCES `sub_menu_items` (`sub_menu_item_id`) ON DELETE CASCADE;

--
-- Constraints for table `roles_sub_most_menu_items`
--
ALTER TABLE `roles_sub_most_menu_items`
  ADD CONSTRAINT `roles_sub_most_menu_items_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `roles_sub_most_menu_items_sub_most_menu_item_id_foreign` FOREIGN KEY (`sub_most_menu_item_id`) REFERENCES `sub_most_menu_items` (`sub_most_menu_item_id`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
