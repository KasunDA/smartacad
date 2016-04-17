-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Apr 17, 2016 at 12:24 PM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `school`
--

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Dumping data for table `menus`
--

INSERT INTO `menus` (`menu_id`, `menu`, `menu_url`, `active`, `sequence`, `type`, `icon`, `menu_header_id`, `created_at`, `updated_at`) VALUES
(1, 'SYSTEM', '#', 1, 1, 1, 'fa fa-television', 1, '2016-03-29 23:33:49', '2016-03-29 23:33:49'),
(2, 'PROFILE', '#', 1, 3, 1, 'fa fa-book', 2, '2016-03-30 20:33:36', '2016-04-17 08:01:21'),
(3, 'HOME', '/home', 1, 1, 2, 'fa fa-home', 4, '2016-04-15 11:02:49', '2016-04-15 11:02:49'),
(4, 'GUARDIANS', '#', 1, 1, 1, 'fa fa-users', 2, '2016-04-17 08:01:21', '2016-04-17 08:04:19');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=8 ;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`menu_item_id`, `menu_item`, `menu_item_url`, `menu_item_icon`, `active`, `sequence`, `type`, `menu_id`, `created_at`, `updated_at`) VALUES
(1, 'SETTINGS', '#', 'fa fa-cogs', 1, '1', 1, 1, '2016-03-30 10:04:10', '2016-03-30 10:04:10'),
(2, 'USERS', '#', 'fa fa-users', 1, '2', 1, 1, '2016-03-30 10:47:28', '2016-03-30 10:47:28'),
(3, 'VIEW ', '/profiles', 'fa fa-user', 1, '1', 1, 2, '2016-03-30 20:35:07', '2016-03-31 12:24:54'),
(4, 'EDIT', '/profiles/edit', 'fa fa-edit', 1, '2', 1, 2, '2016-03-30 20:35:07', '2016-03-31 07:40:40'),
(5, 'MANAGE', '/guardians', 'fa fa-list', 1, '1', 1, 4, '2016-04-17 08:05:55', '2016-04-17 08:05:55'),
(6, 'ADD NEW', '/guardians/create', 'fa fa-user', 1, '2', 1, 4, '2016-04-17 08:05:55', '2016-04-17 08:05:55'),
(7, 'RECORDS', '#', 'fa fa-book', 1, '3', 1, 1, '2016-04-17 09:18:47', '2016-04-17 09:19:02');

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
('2016_04_17_094945_create_titles_table', 2);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=55 ;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`permission_id`, `name`, `display_name`, `description`, `uri`, `created_at`, `updated_at`) VALUES
(1, 'AuthController@getLogin', 'User Login', '', 'auth/login/', '2016-03-30 10:28:56', '2016-03-30 10:28:56'),
(2, 'AuthController@getLogout', 'User Logout', '', 'auth/logout/', '2016-03-30 10:28:56', '2016-03-30 10:28:56'),
(3, 'AuthController@getRegister', 'Register a user', '', 'auth/register/', '2016-03-30 10:28:56', '2016-03-30 10:28:56'),
(4, 'AuthController@logout', '', '', 'logout', '2016-03-30 10:28:56', '2016-04-16 18:12:07'),
(5, 'AuthController@showLoginForm', '', '', 'login', '2016-03-30 10:28:56', '2016-04-16 18:12:07'),
(6, 'AuthController@showRegistrationForm', '', '', 'register', '2016-03-30 10:28:56', '2016-04-16 18:12:07'),
(7, 'DashboardController@getIndex', '', '', 'dashboard/index/', '2016-03-30 10:28:56', '2016-04-16 18:12:07'),
(8, 'DashboardController@getIndexDashboard', '', '', 'dashboard', '2016-03-30 10:28:56', '2016-04-16 18:12:07'),
(9, 'GuardianController@getCreate', '', '', 'guardians/create/', '2016-03-30 10:28:56', '2016-04-17 08:13:58'),
(10, 'GuardianController@getIndex', '', '', 'guardians/index/', '2016-03-30 10:28:57', '2016-04-17 08:13:58'),
(11, 'GuardianController@getIndexGuardians', '', '', 'guardians', '2016-03-30 10:28:57', '2016-04-17 08:13:58'),
(12, 'HomeController@getIndex', '', '', 'home', '2016-03-30 10:28:57', '2016-04-17 08:13:58'),
(13, 'HomeController@getIndexHome/index/', '', '', 'home/index/', '2016-03-30 10:28:57', '2016-04-17 08:13:58'),
(14, 'MenuController@getDelete', '', '', 'menus/delete/', '2016-03-30 10:28:57', '2016-04-17 08:13:58'),
(15, 'MenuController@getIndex', '', '', 'menus/index/', '2016-03-30 10:28:57', '2016-04-17 08:13:58'),
(16, 'MenuController@getIndexMenus', '', '', 'menus', '2016-03-30 10:28:57', '2016-04-17 08:13:58'),
(17, 'MenuHeaderController@getDelete', '', '', 'menu-headers/delete/', '2016-03-30 10:28:57', '2016-04-17 08:13:58'),
(18, 'MenuHeaderController@getIndex', '', '', 'menu-headers/index/', '2016-03-30 10:28:57', '2016-04-17 08:13:58'),
(19, 'MenuHeaderController@getIndexMenu-headers', '', '', 'menu-headers', '2016-03-30 10:28:57', '2016-04-17 08:13:58'),
(20, 'MenuItemController@getDelete', '', '', 'menu-items/delete/', '2016-03-30 10:28:57', '2016-04-17 08:13:58'),
(21, 'MenuItemController@getIndex', '', '', 'menu-items/index/', '2016-03-30 10:28:57', '2016-04-17 08:13:58'),
(22, 'MenuItemController@getIndexMenu-items', '', '', 'menu-items', '2016-03-30 10:28:57', '2016-04-17 08:13:58'),
(23, 'PasswordController@reset', '', '', 'password/reset', '2016-03-30 10:28:57', '2016-04-17 08:13:58'),
(24, 'PasswordController@sendResetLinkEmail', '', '', 'password/email', '2016-03-30 10:28:57', '2016-04-17 08:13:58'),
(25, 'PasswordController@showResetForm', '', '', 'password/reset/', '2016-03-30 10:28:57', '2016-04-17 08:13:58'),
(26, 'PermissionsController@getIndex', '', '', 'permissions/index/', '2016-03-30 10:28:57', '2016-04-17 08:13:59'),
(27, 'PermissionsController@getIndexPermissions', '', '', 'permissions', '2016-03-30 10:28:57', '2016-04-17 08:13:59'),
(28, 'PermissionsController@getRolesPermissions', '', '', 'permissions/roles-permissions/', '2016-03-30 10:28:57', '2016-04-17 08:13:59'),
(29, 'ProfileController@getEdit', '', '', 'profiles/edit/', '2016-03-30 10:28:57', '2016-04-17 08:13:59'),
(30, 'ProfileController@getIndex', '', '', 'profiles/index/', '2016-03-30 10:28:57', '2016-04-17 08:13:59'),
(31, 'ProfileController@getIndexProfiles', '', '', 'profiles', '2016-03-30 10:28:58', '2016-04-17 08:13:59'),
(32, 'RolesController@getDelete', '', '', 'roles/delete/', '2016-03-30 10:28:58', '2016-04-17 08:13:59'),
(33, 'RolesController@getIndex', '', '', 'roles/index/', '2016-03-30 10:28:58', '2016-04-17 08:13:59'),
(34, 'RolesController@getIndexRoles', '', '', 'roles', '2016-03-30 10:28:58', '2016-04-17 08:13:59'),
(35, 'RolesController@getUsersRoles', '', '', 'roles/users-roles/', '2016-03-30 10:28:58', '2016-04-17 08:13:59'),
(36, 'SubMenuItemController@getDelete', '', '', 'sub-menu-items/delete/', '2016-03-30 10:28:58', '2016-04-17 08:13:59'),
(37, 'SubMenuItemController@getIndex', '', '', 'sub-menu-items/index/', '2016-03-30 10:28:58', '2016-04-17 08:13:59'),
(38, 'SubMenuItemController@getIndexSub-menu-items', '', '', 'sub-menu-items', '2016-04-16 18:12:07', '2016-04-17 08:13:59'),
(39, 'SubMostMenuItemController@getDelete', '', '', 'sub-most-menu-items/delete/', '2016-04-16 18:12:07', '2016-04-17 08:13:59'),
(40, 'SubMostMenuItemController@getIndex', '', '', 'sub-most-menu-items/index/', '2016-04-16 18:12:07', '2016-04-17 08:13:59'),
(41, 'SubMostMenuItemController@getIndexSub-most-menu-items', '', '', 'sub-most-menu-items', '2016-04-16 18:12:07', '2016-04-17 08:13:59'),
(42, 'TitleController@getDelete', '', '', 'titles/delete/', '2016-04-16 18:12:07', '2016-04-17 09:09:58'),
(43, 'TitleController@getIndex', '', '', 'titles/index/', '2016-04-16 18:12:07', '2016-04-17 09:09:58'),
(44, 'TitleController@getIndexTitles', '', '', 'titles', '2016-04-16 18:12:07', '2016-04-17 09:09:58'),
(45, 'UserController@getChange', '', '', 'users/change/', '2016-04-16 18:12:07', '2016-04-17 09:09:58'),
(46, 'UserController@getCreate', '', '', 'users/create/', '2016-04-16 18:12:07', '2016-04-17 09:09:58'),
(47, 'UserController@getEdit', '', '', 'users/edit/', '2016-04-16 18:12:07', '2016-04-17 09:09:58'),
(48, 'UserController@getIndex', '', '', 'users/index/', '2016-04-16 18:12:07', '2016-04-17 09:09:58'),
(49, 'UserController@getIndexUsers', '', '', 'users', '2016-04-17 08:14:00', '2016-04-17 09:09:58'),
(50, 'UserController@getStatus', '', '', 'users/status/', '2016-04-17 08:14:00', '2016-04-17 09:09:58'),
(51, 'UserController@getView', '', '', 'users/view/', '2016-04-17 08:14:00', '2016-04-17 09:09:58'),
(52, 'UserTypeController@getDelete', '', '', 'user-types/delete/', '2016-04-17 09:09:58', '2016-04-17 09:09:58'),
(53, 'UserTypeController@getIndex', '', '', 'user-types/index/', '2016-04-17 09:09:58', '2016-04-17 09:09:58'),
(54, 'UserTypeController@getIndexUser-types', '', '', 'user-types', '2016-04-17 09:09:58', '2016-04-17 09:09:58');

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
(1, 3),
(4, 3),
(9, 3),
(10, 3);

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
(1, 3),
(2, 3),
(1, 4),
(2, 4);

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
(1, 6),
(2, 6),
(1, 7),
(2, 7);

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
(2, 6);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=7 ;

--
-- Dumping data for table `sub_menu_items`
--

INSERT INTO `sub_menu_items` (`sub_menu_item_id`, `sub_menu_item`, `sub_menu_item_url`, `sub_menu_item_icon`, `active`, `sequence`, `type`, `menu_item_id`, `created_at`, `updated_at`) VALUES
(1, 'MANAGE MENUS', '#', 'fa fa-list', 1, '1', 1, 1, '2016-03-30 10:05:26', '2016-03-30 10:05:26'),
(2, 'PERMISSIONS', '#', 'fa fa-lock', 1, '2', 1, 1, '2016-03-30 10:21:39', '2016-03-30 10:41:46'),
(3, 'ROLES', '#', 'fa fa-users', 1, '3', 1, 1, '2016-03-30 10:41:35', '2016-03-30 10:41:46'),
(4, 'CREATE', '/users/create', 'fa fa-user', 1, '1', 1, 2, '2016-03-30 10:49:22', '2016-03-30 10:49:22'),
(5, 'MANAGE', '/users', 'fa fa-users', 1, '2', 1, 2, '2016-03-30 10:49:22', '2016-03-30 10:49:22'),
(6, 'TITLES', '/titles', 'fa fa-plus', 1, '1', 1, 7, '2016-04-17 09:22:55', '2016-04-17 09:22:55');

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
-- Table structure for table `titles`
--

DROP TABLE IF EXISTS `titles`;
CREATE TABLE IF NOT EXISTS `titles` (
  `title_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`title_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `titles`
--

INSERT INTO `titles` (`title_id`, `title`, `created_at`, `updated_at`) VALUES
(1, 'Mr', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `password`, `email`, `first_name`, `last_name`, `user_type_id`, `verified`, `status`, `gender`, `phone_no`, `dob`, `avatar`, `verification_code`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, '$2y$10$J6VM0.ySq0icTRaDtXjjI.i7MWJy6UUlPDgmJ3ygFUxDxJ/MeAk5G', 'admin@gmail.com', 'Emmanuel', 'Okafor', 1, 1, 1, 'Male', '08061539278', '2016-04-14', '1_avatar.jpg', NULL, 'ot3NzxCn3YItlV19d30SA4bWS93IFKMC3Hu7pS5KQzM1cBE51yPR8mFlDcYn', NULL, '2016-04-16 19:53:50'),
(2, '$2y$10$J6VM0.ySq0icTRaDtXjjI.i7MWJy6UUlPDgmJ3ygFUxDxJ/MeAk5G', 'parent@gmail.com', 'John', 'Mario', 3, 1, 1, 'Male', '08022443355', '2010-12-10', '2_avatar.jpg', NULL, 'K6UqxlqmItGWW7AjT8oQy5ioRwxhiHiGuR336AA8I8Zeo5zAIF13qdTmqYDI', NULL, '2016-04-16 19:53:31');

-- --------------------------------------------------------

--
-- Table structure for table `user_types`
--

DROP TABLE IF EXISTS `user_types`;
CREATE TABLE IF NOT EXISTS `user_types` (
  `user_type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_type` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`user_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `user_types`
--

INSERT INTO `user_types` (`user_type_id`, `user_type`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', '2016-04-06 23:00:00', NULL),
(2, 'Admin', '2016-04-20 23:00:00', NULL),
(3, 'Parent', '2016-04-28 08:29:31', NULL);

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
-- Constraints for table `roles_menus`
--
ALTER TABLE `roles_menus`
  ADD CONSTRAINT `roles_menus_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`menu_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `roles_menus_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE CASCADE;

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

--
-- Constraints for table `role_user`
--
ALTER TABLE `role_user`
  ADD CONSTRAINT `role_user_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `role_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
