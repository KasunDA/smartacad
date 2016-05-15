-- phpMyAdmin SQL Dump
-- version 4.4.14
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 15, 2016 at 11:34 PM
-- Server version: 5.6.26
-- PHP Version: 5.6.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `solid_steps`
--

DELIMITER $$
--
-- Procedures
--
DROP PROCEDURE IF EXISTS `sp_assignSubject2Classlevels`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_assignSubject2Classlevels`(IN `LevelID` INT, `TermID` INT, `SubjectIDs` VARCHAR(225))
BEGIN
		DECLARE done1 BOOLEAN DEFAULT FALSE;
		DECLARE ClassID INT;
		DECLARE cur1 CURSOR FOR SELECT classroom_id FROM classrooms WHERE classlevel_id=LevelID;
		DECLARE CONTINUE HANDLER FOR NOT FOUND SET done1 = TRUE;

#Open The Cursor For Iterating Through The Recordset cur1
		OPEN cur1;
		REPEAT
			FETCH cur1 INTO ClassID;
			IF NOT done1 THEN
				BEGIN
-- Procedure Call -- To register the subjects to the students in that classroom
					CALL `sp_assignSubject2Classrooms`(ClassID, TermID, SubjectIDs);
				END;
			END IF;
		UNTIL done1 END REPEAT;
		CLOSE cur1;
	END$$

DROP PROCEDURE IF EXISTS `sp_assignSubject2Classrooms`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_assignSubject2Classrooms`(IN `ClassID` INT, `TermID` INT, `SubjectIDs` VARCHAR(225))
BEGIN
#Create a Temporary Table to Hold The Values
		DROP TEMPORARY TABLE IF EXISTS SubjectTemp;
		CREATE TEMPORARY TABLE IF NOT EXISTS SubjectTemp
		(
-- Add the column definitions for the TABLE variable here
			row_id int AUTO_INCREMENT,
			subject_id INT, PRIMARY KEY (row_id)
		);

		IF SubjectIDs IS NOT NULL THEN
			BEGIN
				DECLARE count INT Default 0 ;
				DECLARE subject_id VARCHAR(255);
				simple_loop: LOOP
					SET count = count + 1;
					SET subject_id = SPLIT_STR(SubjectIDs, ',', count);
					IF subject_id = '' THEN
						LEAVE simple_loop;
					END IF;
# Insert into the attend details table those present
					INSERT INTO SubjectTemp(subject_id)
						SELECT subject_id;
				END LOOP simple_loop;
			END;
		END IF;

			Block1: BEGIN
			#DELETE FROM subject_students_registers WHERE subject_classlevel_id IN
			 #(
			#	 SELECT subject_classlevel_id FROM subject_classlevels WHERE class_id=ClassID
             #    AND academic_term_id=TermID AND subject_id
			#	NOT IN (SELECT subject_id FROM SubjectTemp)
			 #);

				DELETE FROM subject_classrooms WHERE classroom_id=ClassID
				AND academic_term_id=TermID AND exam_status_id=2 AND subject_id
				NOT IN (SELECT subject_id FROM SubjectTemp);

				Block2: BEGIN
				DECLARE done1 BOOLEAN DEFAULT FALSE;
				DECLARE SubjectID INT;
				DECLARE cur1 CURSOR FOR SELECT subject_id FROM SubjectTemp;
				DECLARE CONTINUE HANDLER FOR NOT FOUND SET done1 = TRUE;

#Open The Cursor For Iterating Through The Recordset cur1
				OPEN cur1;
				REPEAT
					FETCH cur1 INTO SubjectID;
					IF NOT done1 THEN
						BEGIN
							SET @Exist = (SELECT COUNT(*) FROM subject_classrooms WHERE subject_id=SubjectID
                            AND classroom_id=ClassID AND academic_term_id=TermID);
							IF @Exist = 0 THEN
								BEGIN
# Insert into subject classlevel those newly assigned subjects
									INSERT INTO subject_classrooms(subject_id, classroom_id, academic_term_id)
									VALUES(SubjectID, ClassID, TermID);

-- Procedure Call -- To register the subjects to the students in that classroom
									-- CALL proc_assignSubject2Students(LAST_INSERT_ID());
								END;
							END IF;
						END;
					END IF;
				UNTIL done1 END REPEAT;
				CLOSE cur1;
			END Block2;

-- Delete the teachers_subjects record that has no id in subjects classlevel table
			#DELETE FROM teachers_subjects WHERE subject_classlevel_id
			#NOT IN (SELECT subject_classlevel_id FROM subject_classlevels);
		END Block1;
	END$$

--
-- Functions
--
DROP FUNCTION IF EXISTS `SPLIT_STR`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `SPLIT_STR`(
	x VARCHAR(255),
	delim VARCHAR(12),
	pos INT
) RETURNS varchar(255) CHARSET latin1
RETURN REPLACE(SUBSTRING(SUBSTRING_INDEX(x, delim, pos),
													 LENGTH(SUBSTRING_INDEX(x, delim, pos -1)) + 1),
								 delim, '')$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `academic_terms`
--

DROP TABLE IF EXISTS `academic_terms`;
CREATE TABLE IF NOT EXISTS `academic_terms` (
  `academic_term_id` int(10) unsigned NOT NULL,
  `academic_term` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `status` int(10) unsigned NOT NULL DEFAULT '2',
  `academic_year_id` int(10) unsigned NOT NULL,
  `term_type_id` int(10) unsigned NOT NULL,
  `term_begins` date DEFAULT NULL,
  `term_ends` date DEFAULT NULL,
  `exam_status_id` int(10) unsigned NOT NULL DEFAULT '2',
  `exam_setup_by` int(10) unsigned DEFAULT NULL,
  `exam_setup_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `academic_terms`
--

INSERT INTO `academic_terms` (`academic_term_id`, `academic_term`, `status`, `academic_year_id`, `term_type_id`, `term_begins`, `term_ends`, `exam_status_id`, `exam_setup_by`, `exam_setup_date`, `created_at`, `updated_at`) VALUES
(1, '2015-2016 Second Term', 2, 1, 2, '2016-05-03', '2016-05-27', 2, NULL, NULL, '2016-05-14 18:13:08', '2016-05-15 18:25:36'),
(2, '2015-2016 Third Term', 1, 1, 3, '2016-06-07', '2016-08-26', 2, NULL, NULL, '2016-05-14 18:13:08', '2016-05-15 18:25:36'),
(3, '2016-2017 First Term', 2, 2, 1, '2016-05-17', '2016-08-10', 2, NULL, NULL, '2016-05-15 18:24:52', '2016-05-15 18:25:36');

-- --------------------------------------------------------

--
-- Table structure for table `academic_years`
--

DROP TABLE IF EXISTS `academic_years`;
CREATE TABLE IF NOT EXISTS `academic_years` (
  `academic_year_id` int(10) unsigned NOT NULL,
  `academic_year` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `status` int(10) unsigned NOT NULL DEFAULT '2',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `academic_years`
--

INSERT INTO `academic_years` (`academic_year_id`, `academic_year`, `status`, `created_at`, `updated_at`) VALUES
(1, '2015-2016', 1, '2016-05-11 19:49:23', '2016-05-11 19:49:23'),
(2, '2016-2017', 2, '2016-05-15 18:24:15', '2016-05-15 18:24:15');

-- --------------------------------------------------------

--
-- Table structure for table `classgroups`
--

DROP TABLE IF EXISTS `classgroups`;
CREATE TABLE IF NOT EXISTS `classgroups` (
  `classgroup_id` int(10) unsigned NOT NULL,
  `classgroup` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ca_weight_point` int(10) unsigned DEFAULT '0',
  `exam_weight_point` int(10) unsigned DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `classgroups`
--

INSERT INTO `classgroups` (`classgroup_id`, `classgroup`, `ca_weight_point`, `exam_weight_point`, `created_at`, `updated_at`) VALUES
(1, 'Junior Secondary School', 30, 70, '2016-05-11 19:46:13', '2016-05-11 19:46:13');

-- --------------------------------------------------------

--
-- Table structure for table `classlevels`
--

DROP TABLE IF EXISTS `classlevels`;
CREATE TABLE IF NOT EXISTS `classlevels` (
  `classlevel_id` int(10) unsigned NOT NULL,
  `classlevel` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `classgroup_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `classlevels`
--

INSERT INTO `classlevels` (`classlevel_id`, `classlevel`, `classgroup_id`, `created_at`, `updated_at`) VALUES
(1, 'JS 1', 1, '2016-05-11 19:46:50', '2016-05-11 19:46:50'),
(2, 'JS 2', 1, '2016-05-14 18:16:33', '2016-05-14 18:16:33');

-- --------------------------------------------------------

--
-- Table structure for table `classrooms`
--

DROP TABLE IF EXISTS `classrooms`;
CREATE TABLE IF NOT EXISTS `classrooms` (
  `classroom_id` int(10) unsigned NOT NULL,
  `classroom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `class_size` int(11) DEFAULT NULL,
  `class_status` int(10) unsigned NOT NULL DEFAULT '1',
  `classlevel_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `classrooms`
--

INSERT INTO `classrooms` (`classroom_id`, `classroom`, `class_size`, `class_status`, `classlevel_id`, `created_at`, `updated_at`) VALUES
(1, 'JSS 1A', NULL, 1, 1, '2016-05-14 18:17:02', '2016-05-14 18:17:02'),
(2, 'JSS 1B', NULL, 1, 1, '2016-05-14 18:17:02', '2016-05-14 18:17:02'),
(3, 'JSS 2A', NULL, 1, 2, '2016-05-14 18:17:02', '2016-05-14 18:17:02'),
(4, 'JSS 2B', NULL, 1, 2, '2016-05-14 18:17:02', '2016-05-14 18:17:02');

-- --------------------------------------------------------

--
-- Table structure for table `menus`
--

DROP TABLE IF EXISTS `menus`;
CREATE TABLE IF NOT EXISTS `menus` (
  `menu_id` int(10) unsigned NOT NULL,
  `menu` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `menu_url` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `active` int(10) unsigned NOT NULL DEFAULT '1',
  `sequence` int(10) unsigned NOT NULL,
  `type` int(10) unsigned NOT NULL DEFAULT '1',
  `icon` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `menu_header_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `menus`
--

INSERT INTO `menus` (`menu_id`, `menu`, `menu_url`, `active`, `sequence`, `type`, `icon`, `menu_header_id`, `created_at`, `updated_at`) VALUES
(1, 'SYSTEM', '#', 1, 1, 1, 'fa fa-television', 1, '2016-03-29 22:33:49', '2016-03-29 22:33:49'),
(2, 'PROFILE', '#', 1, 3, 1, 'fa fa-book', 2, '2016-03-30 19:33:36', '2016-04-18 20:37:17'),
(4, 'SPONSORS', '#', 1, 1, 1, 'fa fa-users', 2, '2016-04-17 07:01:21', '2016-04-18 20:37:17'),
(5, 'ADD ACCOUNT', '/accounts/create', 0, 5, 1, 'fa fa-user-plus', 2, '2016-04-18 19:48:45', '2016-04-29 07:38:28'),
(6, 'STAFFS', '#', 1, 2, 1, 'fa fa-users', 2, '2016-04-18 19:51:00', '2016-04-18 20:37:17'),
(7, 'MASTER RECORDS', '#', 1, 2, 1, 'fa fa-book', 1, '2016-05-10 03:53:29', '2016-05-10 03:53:29');

-- --------------------------------------------------------

--
-- Table structure for table `menu_headers`
--

DROP TABLE IF EXISTS `menu_headers`;
CREATE TABLE IF NOT EXISTS `menu_headers` (
  `menu_header_id` int(10) unsigned NOT NULL,
  `menu_header` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `active` int(10) unsigned NOT NULL DEFAULT '1',
  `sequence` int(10) unsigned NOT NULL,
  `type` int(10) unsigned NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `menu_headers`
--

INSERT INTO `menu_headers` (`menu_header_id`, `menu_header`, `active`, `sequence`, `type`, `created_at`, `updated_at`) VALUES
(1, 'SETUPS', 1, 10, 1, '2016-03-29 22:30:39', '2016-03-30 19:33:06'),
(2, 'ACCOUNTS', 1, 8, 1, '2016-03-30 19:33:06', '2016-05-15 20:15:32'),
(3, 'RECORDS', 1, 7, 1, '2016-03-31 06:45:49', '2016-05-15 20:15:32'),
(4, 'PORTAL', 1, 1, 2, '2016-04-15 09:41:26', '2016-04-15 09:55:41');

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

DROP TABLE IF EXISTS `menu_items`;
CREATE TABLE IF NOT EXISTS `menu_items` (
  `menu_item_id` int(10) unsigned NOT NULL,
  `menu_item` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `menu_item_url` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `menu_item_icon` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `active` int(10) unsigned NOT NULL DEFAULT '1',
  `sequence` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` int(10) unsigned NOT NULL DEFAULT '1',
  `menu_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`menu_item_id`, `menu_item`, `menu_item_url`, `menu_item_icon`, `active`, `sequence`, `type`, `menu_id`, `created_at`, `updated_at`) VALUES
(1, 'SETTINGS', '#', 'fa fa-cogs', 1, '1', 1, 1, '2016-03-30 09:04:10', '2016-03-30 09:04:10'),
(2, 'USERS', '#', 'fa fa-users', 1, '5', 1, 1, '2016-03-30 09:47:28', '2016-05-10 03:51:30'),
(3, 'MY SCHOOL', '#', 'fa fa-home', 1, '1', 1, 2, '2016-03-30 19:35:07', '2016-04-17 10:09:15'),
(4, 'PERSONAL', '#', 'fa fa-user', 1, '2', 1, 2, '2016-03-30 19:35:07', '2016-04-17 10:07:12'),
(5, 'MANAGE', '/sponsors', 'fa fa-list', 1, '1', 1, 4, '2016-04-17 07:05:55', '2016-04-17 09:04:19'),
(7, 'RECORDS', '#', 'fa fa-book', 1, '3', 1, 1, '2016-04-17 08:18:47', '2016-04-17 08:19:02'),
(8, 'SCHOOLS', '#', 'fa fa-home', 1, '4', 1, 1, '2016-04-17 09:46:04', '2016-05-10 03:51:30'),
(9, 'MANAGE', '/staffs', 'fa fa-list', 1, '1', 1, 6, '2016-04-18 19:51:45', '2016-04-18 20:41:57'),
(10, 'ACADEMIC YEAR', '/academic-years', 'fa fa-plus', 1, '1', 1, 7, '2016-05-10 03:50:13', '2016-05-10 03:54:32'),
(11, 'ACADEMIC TERM', '/academic-terms', 'fa fa-plus', 1, '2', 1, 7, '2016-05-10 03:55:57', '2016-05-10 03:55:57'),
(12, 'CLASS GROUP', '/class-groups', 'fa fa-plus', 1, '3', 1, 7, '2016-05-10 03:55:57', '2016-05-10 03:55:57'),
(13, 'CLASS LEVEL', '/class-levels', 'fa fa-plus', 1, '4', 1, 7, '2016-05-10 03:56:43', '2016-05-10 03:56:43'),
(14, 'CLASS ROOMS', '/class-rooms', 'fa fa-plus', 1, '5', 1, 7, '2016-05-10 03:57:10', '2016-05-10 03:57:24'),
(15, 'SUBJECTS', '#', 'fa fa-book', 1, '6', 1, 7, '2016-05-11 15:12:46', '2016-05-11 15:12:46');

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
('2016_04_17_104628_create_sponsor_table', 1),
('2016_04_17_121149_create_schools_table', 1),
('2016_04_17_195404_create_school_databases_table', 1),
('2016_04_19_093540_create_salutaions_table', 1),
('2016_04_19_115455_create_marital_statuses_table', 1),
('2016_04_21_202331_create_staffs_table', 1),
('2016_04_28_175829_create_state_and_lga_table', 1),
('2016_05_01_162613_create_academic_years_and_terms_table', 2),
('2016_05_08_144434_create_class_groups_levels_rooms_table', 2),
('2016_05_01_162805_create_subject_groups_and_subjects_table', 3),
('2016_05_14_173333_create_subject_classes_table', 4);

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `permission_id` int(10) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `display_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `uri` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`permission_id`, `name`, `display_name`, `description`, `uri`, `created_at`, `updated_at`) VALUES
(1, 'AccountsController@getCreate', '', '', 'accounts/create/', '2016-04-17 12:17:42', '2016-04-19 14:59:32'),
(2, 'AuthController@getLogin', '', '', 'auth/login/', '2016-04-17 12:17:42', '2016-04-19 14:59:32'),
(3, 'AuthController@getLogout', '', '', 'auth/logout/', '2016-04-17 12:17:42', '2016-04-19 14:59:32'),
(4, 'AuthController@getRegister', '', '', 'auth/register/', '2016-04-17 12:17:42', '2016-04-19 14:59:32'),
(5, 'AuthController@logout', '', '', 'logout', '2016-04-17 12:17:42', '2016-04-19 14:59:32'),
(6, 'AuthController@showLoginForm', '', '', 'login', '2016-04-17 12:17:42', '2016-04-19 14:59:33'),
(7, 'AuthController@showRegistrationForm', '', '', 'register', '2016-04-17 12:17:42', '2016-04-19 14:59:33'),
(8, 'DashboardController@getIndex', '', '', 'dashboard/index/', '2016-04-17 12:17:42', '2016-04-19 14:59:33'),
(9, 'DashboardController@getIndexDashboard', '', '', 'dashboard', '2016-04-17 12:17:42', '2016-04-19 14:59:33'),
(10, 'HomeController@getIndex', '', '', 'home', '2016-04-17 12:17:42', '2016-04-19 14:59:33'),
(11, 'HomeController@getIndexHome/index/', '', '', 'home/index/', '2016-04-17 12:17:42', '2016-04-19 14:59:33'),
(12, 'MaritalStatusController@getDelete', '', '', 'marital-statuses/delete/', '2016-04-17 12:17:42', '2016-04-19 14:59:33'),
(13, 'MaritalStatusController@getIndex', '', '', 'marital-statuses/index/', '2016-04-17 12:17:42', '2016-04-19 14:59:33'),
(14, 'MaritalStatusController@getIndexMarital-statuses', '', '', 'marital-statuses', '2016-04-17 12:17:42', '2016-04-19 14:59:33'),
(15, 'MenuController@getDelete', '', '', 'menus/delete/', '2016-04-17 12:17:42', '2016-04-19 14:59:33'),
(16, 'MenuController@getIndex', '', '', 'menus/index/', '2016-04-17 12:17:42', '2016-04-19 14:59:33'),
(17, 'MenuController@getIndexMenus', '', '', 'menus', '2016-04-17 12:17:42', '2016-04-19 14:59:33'),
(18, 'MenuHeaderController@getDelete', '', '', 'menu-headers/delete/', '2016-04-17 12:17:42', '2016-04-19 14:59:33'),
(19, 'MenuHeaderController@getIndex', '', '', 'menu-headers/index/', '2016-04-17 12:17:42', '2016-04-19 14:59:33'),
(20, 'MenuHeaderController@getIndexMenu-headers', '', '', 'menu-headers', '2016-04-17 12:17:42', '2016-04-19 14:59:33'),
(21, 'MenuItemController@getDelete', '', '', 'menu-items/delete/', '2016-04-17 12:17:42', '2016-04-19 14:59:33'),
(22, 'MenuItemController@getIndex', '', '', 'menu-items/index/', '2016-04-17 12:17:42', '2016-04-19 14:59:33'),
(23, 'MenuItemController@getIndexMenu-items', '', '', 'menu-items', '2016-04-17 12:17:42', '2016-04-19 14:59:33'),
(24, 'PasswordController@reset', '', '', 'password/reset', '2016-04-17 12:17:42', '2016-04-19 14:59:33'),
(25, 'PasswordController@sendResetLinkEmail', '', '', 'password/email', '2016-04-17 12:17:42', '2016-04-19 14:59:34'),
(26, 'PasswordController@showResetForm', '', '', 'password/reset/', '2016-04-17 12:17:42', '2016-04-19 14:59:34'),
(27, 'PermissionsController@getIndex', '', '', 'permissions/index/', '2016-04-17 12:17:42', '2016-04-19 14:59:34'),
(28, 'PermissionsController@getIndexPermissions', '', '', 'permissions', '2016-04-17 12:17:42', '2016-04-19 14:59:34'),
(29, 'PermissionsController@getRolesPermissions', '', '', 'permissions/roles-permissions/', '2016-04-17 12:17:42', '2016-04-19 14:59:34'),
(30, 'ProfileController@getEdit', '', '', 'profiles/edit/', '2016-04-17 12:17:42', '2016-04-19 14:59:34'),
(31, 'ProfileController@getIndex', '', '', 'profiles/index/', '2016-04-17 12:17:42', '2016-04-19 14:59:34'),
(32, 'ProfileController@getIndexProfiles', '', '', 'profiles', '2016-04-17 12:17:42', '2016-04-19 14:59:34'),
(33, 'RolesController@getDelete', '', '', 'roles/delete/', '2016-04-17 12:17:42', '2016-04-19 14:59:34'),
(34, 'RolesController@getIndex', '', '', 'roles/index/', '2016-04-17 12:17:42', '2016-04-19 14:59:34'),
(35, 'RolesController@getIndexRoles', '', '', 'roles', '2016-04-17 12:17:42', '2016-04-19 14:59:34'),
(36, 'RolesController@getUsersRoles', '', '', 'roles/users-roles/', '2016-04-17 12:17:42', '2016-04-19 14:59:34'),
(37, 'SalutationController@getDelete', '', '', 'salutations/delete/', '2016-04-17 12:17:42', '2016-04-19 14:59:34'),
(38, 'SalutationController@getIndex', '', '', 'salutations/index/', '2016-04-17 12:17:42', '2016-04-19 14:59:34'),
(39, 'SalutationController@getIndexSalutations', '', '', 'salutations', '2016-04-17 12:17:42', '2016-04-19 14:59:34'),
(40, 'SchoolController@getCreate', '', '', 'schools/create/', '2016-04-17 12:17:42', '2016-04-19 14:59:34'),
(41, 'SchoolController@getDbConfig', '', '', 'schools/db-config/', '2016-04-17 12:17:42', '2016-04-19 14:59:34'),
(42, 'SchoolController@getEdit', '', '', 'schools/edit/', '2016-04-17 12:17:42', '2016-04-19 14:59:34'),
(43, 'SchoolController@getIndex', '', '', 'schools/index/', '2016-04-17 12:17:42', '2016-04-19 14:59:34'),
(44, 'SchoolController@getIndexSchools', '', '', 'schools', '2016-04-17 12:17:42', '2016-04-19 14:59:34'),
(45, 'SchoolController@getSearch', '', '', 'schools/search/', '2016-04-17 12:17:42', '2016-04-19 14:59:34'),
(46, 'SchoolController@getStatus', '', '', 'schools/status/', '2016-04-17 12:17:42', '2016-04-19 14:59:35'),
(47, 'SponsorController@getIndex', '', '', 'sponsors/index/', '2016-04-17 12:17:42', '2016-04-19 14:59:35'),
(48, 'SponsorController@getIndexSponsors', '', '', 'sponsors', '2016-04-17 12:17:42', '2016-04-19 14:59:35'),
(49, 'StaffController@getIndex', '', '', 'staffs/index/', '2016-04-17 12:17:42', '2016-04-19 14:59:35'),
(50, 'StaffController@getIndexStaffs', '', '', 'staffs', '2016-04-17 12:17:42', '2016-04-19 14:59:35'),
(51, 'SubMenuItemController@getDelete', '', '', 'sub-menu-items/delete/', '2016-04-17 12:17:42', '2016-04-19 14:59:35'),
(52, 'SubMenuItemController@getIndex', '', '', 'sub-menu-items/index/', '2016-04-17 12:17:42', '2016-04-19 14:59:35'),
(53, 'SubMenuItemController@getIndexSub-menu-items', '', '', 'sub-menu-items', '2016-04-17 12:17:42', '2016-04-19 14:59:35'),
(54, 'SubMostMenuItemController@getDelete', '', '', 'sub-most-menu-items/delete/', '2016-04-17 12:17:42', '2016-04-19 14:59:35'),
(55, 'SubMostMenuItemController@getIndex', '', '', 'sub-most-menu-items/index/', '2016-04-17 12:17:42', '2016-04-19 14:59:35'),
(56, 'SubMostMenuItemController@getIndexSub-most-menu-items', '', '', 'sub-most-menu-items', '2016-04-17 12:17:42', '2016-04-19 14:59:35'),
(57, 'UserController@getChange', '', '', 'users/change/', '2016-04-17 12:17:42', '2016-04-19 14:59:35'),
(58, 'UserController@getCreate', '', '', 'users/create/', '2016-04-17 12:17:42', '2016-04-19 14:59:35'),
(59, 'UserController@getEdit', '', '', 'users/edit/', '2016-04-19 14:59:35', '2016-04-19 14:59:35'),
(60, 'UserController@getIndex', '', '', 'users/index/', '2016-04-19 14:59:35', '2016-04-19 14:59:35'),
(61, 'UserController@getIndexUsers', '', '', 'users', '2016-04-19 14:59:35', '2016-04-19 14:59:35'),
(62, 'UserController@getStatus', '', '', 'users/status/', '2016-04-19 14:59:35', '2016-04-19 14:59:35'),
(63, 'UserController@getView', '', '', 'users/view/', '2016-04-19 14:59:35', '2016-04-19 14:59:35'),
(64, 'UserTypeController@getDelete', '', '', 'user-types/delete/', '2016-04-19 14:59:35', '2016-04-19 14:59:35'),
(65, 'UserTypeController@getIndex', '', '', 'user-types/index/', '2016-04-19 14:59:35', '2016-04-19 14:59:35'),
(66, 'UserTypeController@getIndexUser-types', '', '', 'user-types', '2016-04-19 14:59:35', '2016-04-19 14:59:35');

-- --------------------------------------------------------

--
-- Table structure for table `permission_role`
--

DROP TABLE IF EXISTS `permission_role`;
CREATE TABLE IF NOT EXISTS `permission_role` (
  `permission_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL
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
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `role_id` int(10) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `display_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_type_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `name`, `display_name`, `description`, `user_type_id`, `created_at`, `updated_at`) VALUES
(1, 'developer', 'Developer', 'The software developer', 1, '2016-03-29 22:30:11', '2016-04-28 21:36:59'),
(2, 'super_admin', 'Super Admin', 'System Administrator', 2, '2016-03-30 09:51:57', '2016-04-28 22:33:03'),
(3, 'sponsor', 'Sponsor', 'Sponsor', 3, '2016-04-16 17:25:54', '2016-04-28 21:36:59'),
(4, 'staff', 'Staff', 'Staff', 4, '2016-04-16 17:25:54', '2016-04-28 21:36:59');

-- --------------------------------------------------------

--
-- Table structure for table `roles_menus`
--

DROP TABLE IF EXISTS `roles_menus`;
CREATE TABLE IF NOT EXISTS `roles_menus` (
  `role_id` int(10) unsigned NOT NULL,
  `menu_id` int(10) unsigned DEFAULT NULL
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
(2, 6),
(2, 1),
(4, 2),
(2, 2),
(3, 4),
(1, 7),
(2, 7);

-- --------------------------------------------------------

--
-- Table structure for table `roles_menu_headers`
--

DROP TABLE IF EXISTS `roles_menu_headers`;
CREATE TABLE IF NOT EXISTS `roles_menu_headers` (
  `role_id` int(10) unsigned NOT NULL,
  `menu_header_id` int(10) unsigned DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `roles_menu_headers`
--

INSERT INTO `roles_menu_headers` (`role_id`, `menu_header_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(4, 2),
(2, 2),
(4, 1),
(2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `roles_menu_items`
--

DROP TABLE IF EXISTS `roles_menu_items`;
CREATE TABLE IF NOT EXISTS `roles_menu_items` (
  `role_id` int(10) unsigned NOT NULL,
  `menu_item_id` int(10) unsigned DEFAULT NULL
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
(1, 8),
(2, 3),
(2, 4),
(1, 9),
(2, 9),
(2, 2),
(2, 1),
(4, 4),
(1, 10),
(2, 10),
(1, 11),
(2, 11),
(1, 12),
(2, 12),
(1, 13),
(2, 13),
(1, 14),
(2, 14),
(1, 15),
(2, 15);

-- --------------------------------------------------------

--
-- Table structure for table `roles_sub_menu_items`
--

DROP TABLE IF EXISTS `roles_sub_menu_items`;
CREATE TABLE IF NOT EXISTS `roles_sub_menu_items` (
  `role_id` int(10) unsigned NOT NULL,
  `sub_menu_item_id` int(10) unsigned DEFAULT NULL
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
(1, 13),
(2, 13),
(2, 4),
(2, 5),
(2, 3),
(4, 9),
(4, 10),
(1, 14),
(1, 15),
(1, 16),
(2, 16),
(1, 17),
(2, 17);

-- --------------------------------------------------------

--
-- Table structure for table `roles_sub_most_menu_items`
--

DROP TABLE IF EXISTS `roles_sub_most_menu_items`;
CREATE TABLE IF NOT EXISTS `roles_sub_most_menu_items` (
  `role_id` int(10) unsigned NOT NULL,
  `sub_most_menu_item_id` int(10) unsigned DEFAULT NULL
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
(1, 9),
(2, 9);

-- --------------------------------------------------------

--
-- Table structure for table `role_user`
--

DROP TABLE IF EXISTS `role_user`;
CREATE TABLE IF NOT EXISTS `role_user` (
  `user_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `role_user`
--

INSERT INTO `role_user` (`user_id`, `role_id`) VALUES
(1, 1),
(2, 2),
(3, 4),
(4, 4),
(5, 4),
(6, 4),
(7, 4),
(8, 4),
(9, 4),
(10, 4),
(11, 4),
(12, 4),
(13, 4),
(14, 4),
(15, 4),
(16, 4),
(17, 4);

-- --------------------------------------------------------

--
-- Table structure for table `sponsors`
--

DROP TABLE IF EXISTS `sponsors`;
CREATE TABLE IF NOT EXISTS `sponsors` (
  `sponsor_id` int(10) unsigned NOT NULL,
  `sponsor_no` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `first_name` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `other_name` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `phone_no` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone_no2` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8_unicode_ci,
  `lga_id` int(10) unsigned DEFAULT NULL,
  `salutation_id` int(10) unsigned DEFAULT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staffs`
--

DROP TABLE IF EXISTS `staffs`;
CREATE TABLE IF NOT EXISTS `staffs` (
  `staff_id` int(10) unsigned NOT NULL,
  `staff_no` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `first_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `other_name` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `gender` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone_no` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone_no2` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8_unicode_ci,
  `lga_id` int(10) unsigned DEFAULT NULL,
  `salutation_id` int(10) unsigned DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subject_classrooms`
--

DROP TABLE IF EXISTS `subject_classrooms`;
CREATE TABLE IF NOT EXISTS `subject_classrooms` (
  `subject_classroom_id` int(10) unsigned NOT NULL,
  `subject_id` int(10) unsigned NOT NULL,
  `classroom_id` int(10) unsigned NOT NULL,
  `academic_term_id` int(10) unsigned NOT NULL,
  `exam_status_id` int(10) unsigned NOT NULL DEFAULT '2'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subject_tutors`
--

DROP TABLE IF EXISTS `subject_tutors`;
CREATE TABLE IF NOT EXISTS `subject_tutors` (
  `subject_tutor_id` int(10) unsigned NOT NULL,
  `tutor_id` int(10) unsigned DEFAULT NULL,
  `subject_classroom_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sub_menu_items`
--

DROP TABLE IF EXISTS `sub_menu_items`;
CREATE TABLE IF NOT EXISTS `sub_menu_items` (
  `sub_menu_item_id` int(10) unsigned NOT NULL,
  `sub_menu_item` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `sub_menu_item_url` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `sub_menu_item_icon` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `active` int(10) unsigned NOT NULL DEFAULT '1',
  `sequence` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` int(10) unsigned NOT NULL DEFAULT '1',
  `menu_item_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sub_menu_items`
--

INSERT INTO `sub_menu_items` (`sub_menu_item_id`, `sub_menu_item`, `sub_menu_item_url`, `sub_menu_item_icon`, `active`, `sequence`, `type`, `menu_item_id`, `created_at`, `updated_at`) VALUES
(1, 'MANAGE MENUS', '#', 'fa fa-list', 1, '1', 1, 1, '2016-03-30 09:05:26', '2016-03-30 09:05:26'),
(2, 'PERMISSIONS', '#', 'fa fa-lock', 1, '2', 1, 1, '2016-03-30 09:21:39', '2016-03-30 09:41:46'),
(3, 'ROLES', '#', 'fa fa-users', 1, '3', 1, 1, '2016-03-30 09:41:35', '2016-03-30 09:41:46'),
(4, 'CREATE', '/users/create', 'fa fa-user', 1, '1', 1, 2, '2016-03-30 09:49:22', '2016-03-30 09:49:22'),
(5, 'MANAGE', '/users', 'fa fa-users', 1, '2', 1, 2, '2016-03-30 09:49:22', '2016-03-30 09:49:22'),
(6, 'SALUTATIONS', '/salutations', 'fa fa-plus', 1, '1', 1, 7, '2016-04-17 08:22:55', '2016-04-19 07:55:37'),
(7, 'MANAGE', '/schools', 'fa fa-list', 1, '1', 1, 8, '2016-04-17 09:47:21', '2016-04-17 09:47:21'),
(8, 'CREATE', '/schools/create', 'fa fa-plus', 1, '2', 1, 8, '2016-04-17 09:47:58', '2016-04-17 09:47:58'),
(9, 'VIEW', '/profiles', 'fa fa-eye', 1, '1', 1, 4, '2016-04-17 10:06:59', '2016-04-17 10:06:59'),
(10, 'EDIT', '/profiles/edit', 'fa fa-edit', 1, '2', 1, 4, '2016-04-17 10:07:00', '2016-04-17 10:07:00'),
(11, 'UPDATE', '/schools/edit', 'fa fa-edit', 1, '1', 1, 3, '2016-04-17 10:08:39', '2016-04-28 22:41:11'),
(12, 'USER TYPES', '/user-types', 'fa fa-user-plus', 1, '3', 1, 2, '2016-04-18 19:38:12', '2016-04-18 19:38:39'),
(13, 'M. STAUESES', '/marital-statuses', 'fa fa-table', 1, '2', 1, 7, '2016-04-19 09:58:11', '2016-04-19 09:58:28'),
(14, 'SUBJECT GROUPS', '/subject-groups', 'fa fa-book', 1, '3', 1, 7, '2016-05-11 14:13:29', '2016-05-11 14:13:29'),
(15, 'SUBJECTS', '/subjects', 'fa fa-table', 1, '4', 1, 7, '2016-05-11 14:13:30', '2016-05-11 14:13:30'),
(16, 'MANAGE', '/school-subjects', 'fa fa-plus-square', 1, '1', 1, 15, '2016-05-11 15:14:15', '2016-05-11 15:14:15'),
(17, 'VIEW LIST', '/school-subjects/view', 'fa fa-eye', 1, '2', 1, 15, '2016-05-11 15:14:15', '2016-05-11 15:14:15');

-- --------------------------------------------------------

--
-- Table structure for table `sub_most_menu_items`
--

DROP TABLE IF EXISTS `sub_most_menu_items`;
CREATE TABLE IF NOT EXISTS `sub_most_menu_items` (
  `sub_most_menu_item_id` int(10) unsigned NOT NULL,
  `sub_most_menu_item` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `sub_most_menu_item_url` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `sub_most_menu_item_icon` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `active` int(10) unsigned NOT NULL DEFAULT '1',
  `sequence` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` int(10) unsigned NOT NULL DEFAULT '1',
  `sub_menu_item_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sub_most_menu_items`
--

INSERT INTO `sub_most_menu_items` (`sub_most_menu_item_id`, `sub_most_menu_item`, `sub_most_menu_item_url`, `sub_most_menu_item_icon`, `active`, `sequence`, `type`, `sub_menu_item_id`, `created_at`, `updated_at`) VALUES
(1, 'Header', '/menu-headers', 'fa fa-list', 1, '1', 1, 1, '2016-03-30 09:15:33', '2016-03-30 09:15:33'),
(2, 'Menu', '/menus', 'fa fa-list', 1, '2', 1, 1, '2016-03-30 09:16:39', '2016-03-30 09:16:39'),
(3, 'Menu Items', '/menu-items', 'fa fa-list', 1, '3', 1, 1, '2016-03-30 09:17:42', '2016-03-30 09:18:54'),
(4, 'Sub Menu', '/sub-menu-items', 'fa fa-list', 1, '4', 1, 1, '2016-03-30 09:18:54', '2016-03-30 09:18:54'),
(5, 'Sub-most Menu', '/sub-most-menu-items', 'fa fa-list', 1, '5', 1, 1, '2016-03-30 09:19:42', '2016-03-30 09:25:09'),
(6, 'Manage', '/permissions', 'fa fa-list', 1, '1', 1, 2, '2016-03-30 09:24:08', '2016-03-30 09:25:56'),
(7, 'Assign', '/permissions/roles-permissions/', 'fa fa-users', 1, '2', 1, 2, '2016-03-30 09:34:38', '2016-03-30 09:35:07'),
(8, 'Manage', '/roles', 'fa fa-table', 1, '1', 1, 3, '2016-03-30 09:43:20', '2016-03-30 09:43:20'),
(9, 'Assign', '/roles/users-roles', 'fa fa-users', 1, '2', 1, 3, '2016-03-30 09:43:20', '2016-03-30 09:43:20');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(10) unsigned NOT NULL,
  `password` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `phone_no` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `first_name` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `middle_name` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gender` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `phone_no2` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_type_id` int(10) unsigned NOT NULL,
  `lga_id` int(10) unsigned DEFAULT NULL,
  `salutation_id` int(10) unsigned DEFAULT NULL,
  `verified` int(10) unsigned NOT NULL DEFAULT '0',
  `status` int(10) unsigned NOT NULL DEFAULT '1',
  `avatar` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `verification_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `password`, `phone_no`, `email`, `first_name`, `last_name`, `middle_name`, `gender`, `dob`, `phone_no2`, `user_type_id`, `lga_id`, `salutation_id`, `verified`, `status`, `avatar`, `verification_code`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, '$2y$10$WgHQSaszEOSJpz2HsoUToeJoyCxh7fGuc3ZoLJA.NubXU42L6E3SG', '08011223344', 'admin@gmail.com', 'Emma', 'Okafor', '', 'Male', '2016-04-05', '', 1, 0, 1, 1, 1, '1_avatar.jpg', NULL, 'xaS2QNXIOwxmVmjCQbm9sYQGh8mKDQkW88MBBOYDRLL9foHMleg2vYQV4ql2', NULL, '2016-05-11 19:39:24'),
(2, '$2y$10$WgHQSaszEOSJpz2HsoUToeJoyCxh7fGuc3ZoLJA.NubXU42L6E3SG', '08161730788', 'bamidelemike2003@yahoo.com', 'Bamidele', 'Micheal', '', 'Male', '1976-02-11', '08066303843', 2, NULL, 1, 1, 1, NULL, 'x9pxH08aB60ZKwe12DDKbiD3V5628TyGMd1v8Q5I', 'oPTaCPLgnCAnlOQBeTHDngxGmchCgWYzeKIe0spRhdSpoNMx9Uofb15NqTj2', '2016-04-28 22:21:05', '2016-05-05 20:24:01'),
(3, '$2y$10$Pc9CBBOKkpbTAlnoxc0iveGkS5xRKREBYlwyPRzxSUxsb.9nNJ9cS', '08186644996', 'onegirl2004@yahoo.com', 'Emina', 'Omotolani', NULL, NULL, NULL, NULL, 4, NULL, NULL, 1, 1, '3_avatar.png', 'sJkNJULOX0XDBVHoG929c8zOuHvuQJ8taqOE4MK7', NULL, '2016-05-05 19:29:34', '2016-05-10 03:05:24'),
(4, '$2y$10$eouumWL7oBFrGRwQLcPRe.uv5CRlFKFNSLvni8eLBM4n3Lb9al/Wm', '08032492560', 'agiebabe2003@yahoo.comk', 'Agetu', 'Agnes', NULL, NULL, NULL, NULL, 4, NULL, NULL, 1, 1, NULL, 'mBFoXfYp7feFMhsnzYWkh616IV2wq2e5LdtOOYRl', NULL, '2016-05-05 19:30:48', '2016-05-05 19:30:48'),
(5, '$2y$10$08ymddnGq3lEWheZSMe3Puc/fLtGo7pDZ5dm1Pmh9CupX3AV/KvO6', '08138281504', 'thesuccessor2020@yahoo.com', 'Akinremi', 'omobolaji', NULL, NULL, NULL, NULL, 4, NULL, NULL, 1, 1, NULL, 'kmmCEClYr018UobxCFrOHmDVVOaz1eaD60Nn2ow9', NULL, '2016-05-05 19:32:16', '2016-05-05 19:32:16'),
(6, '$2y$10$r7i.xoOrQP6n0B5JQLtmCuaY.MvCuNoEsinb6ALaNjov4Ck2Nfnx.', '08032984249', 'chukwuonyelilian@gmail.com', 'Chukwuka', 'Lilian', NULL, NULL, NULL, NULL, 4, NULL, NULL, 1, 1, NULL, 'rPp9ofMqUMCat73BPt0Pod2v2Rg362iUtO5QNzU0', NULL, '2016-05-05 19:34:16', '2016-05-05 19:34:16'),
(7, '$2y$10$MDDp2iaWLGqwbDvYBpsB/.YB12d45YNbknT6yEMWWPi5JRBPW9AiG', '08066451585', 'soldemo20042001@yahoo.com', 'Ademola', 'Solomon', NULL, NULL, NULL, NULL, 4, NULL, NULL, 1, 1, NULL, 'MZXJDhTOSnwR0ZXphZLBScldsf880b3vzyoHTrfK', NULL, '2016-05-05 19:36:04', '2016-05-05 19:36:04'),
(8, '$2y$10$.8x6F9cBdIHCbgy1WwT.nOZk7w5LDa75jATtatuORkejMemmr35yG', '07062175334', 'peroski4chuks@yahoo.com', 'Peter', 'Okuagu', NULL, NULL, NULL, NULL, 4, NULL, NULL, 1, 1, NULL, 'b8M3Gs5lGycCX3cYnH44gkUxmRxoVBCWLOQGDNK8', NULL, '2016-05-05 19:37:13', '2016-05-05 19:37:13'),
(9, '$2y$10$FC1BwB23i/GGqkhC6h1TIuN0.OjLUYkcu7MdT3xak4DrqXbZ/U6R2', '08090948734', 'okelolaademola@yahoo.com', 'Okelola', 'Ademola', NULL, NULL, NULL, NULL, 4, NULL, NULL, 1, 1, NULL, 'EbvbXeWe5EKkK4b7zKn6EWGZmbsNvGbAlRQIGI1S', NULL, '2016-05-05 19:38:23', '2016-05-05 19:38:23'),
(10, '$2y$10$q5U4WE68xHhbqVK3gwkYjezapu3g/aB1nYTqhyRPHRFjXy9OpVpaS', '08035255510', 'allanzah5525@yahoo.com', 'Allanzah', 'Oluwabunmi', NULL, NULL, NULL, NULL, 4, NULL, NULL, 1, 1, NULL, 'rjcdhR2WJW27XocmNwoORcfERnQV1qoh3mTT3dK5', NULL, '2016-05-05 19:43:28', '2016-05-05 19:43:28'),
(11, '$2y$10$6xA35RW0HJcsAr9Bs9LfSO3wA8opLWEyZ1ztgypeC1ET1r73tuC7.', '08028167155', 'oluwatosinaroomotosho@yahoo.com', 'Omotosho', 'oluwatosin', NULL, NULL, NULL, NULL, 4, NULL, NULL, 1, 1, NULL, 'BPNbWERr3zwJMKVetEjUDc6akSLVQrnhPhRvwAg2', NULL, '2016-05-05 19:45:11', '2016-05-05 19:45:11'),
(12, '$2y$10$5dGmkChzVeFNuFie2vPDWO9X0k5PtafYoFVydeUtLR8NDwYMjFErW', '08121680773', 'kateokolie@yahoo.com', 'Okolie', 'Kate', NULL, NULL, NULL, NULL, 4, NULL, NULL, 1, 1, NULL, 'Di4CWeDOgqXqB4XbJdiDS16NjzrYVbBo9NJMLT5y', NULL, '2016-05-05 19:46:24', '2016-05-05 19:46:24'),
(13, '$2y$10$.x6aocbaec7cJgHxJlFTL.V7sSoWirznKVc61rgp/D68jon93oDvK', '08060633784', 'divinebazunu@yahoo.com', 'Oshoma', 'Angela', NULL, NULL, NULL, NULL, 4, NULL, NULL, 1, 1, NULL, 'sTFKNF2E2T3Cz5AM10B0G8Pz3s4jt5CX7gcJfzdW', NULL, '2016-05-05 19:47:38', '2016-05-05 19:47:38'),
(14, '$2y$10$yGK1y1ZffAC8mgJ8VDHZfuL6.6Xf.lQh.pTITfRgxu2x2OoNDsuc.', '08025307294', 'relatingus@yahoo.com', 'Adekoya', 'Biola', NULL, NULL, NULL, NULL, 4, NULL, NULL, 1, 1, NULL, 'xTJDhUgrzH5ywWfzHkbwKFs3ZgLrueRA3cXxzK2S', NULL, '2016-05-05 19:48:33', '2016-05-05 19:48:33'),
(15, '$2y$10$bQ9WAh/O8Q/21TUuXsaWbeqvOx3gJiN99QnahcWzfUg4VMURcsB6G', '07066847811', 'demolastar@gmail.com', 'Balogun', 'Ademola', NULL, NULL, NULL, NULL, 4, NULL, NULL, 1, 1, NULL, 'CAwD6crBPhv8WdtOrvrxHe5efULPZiObNcmkwIuM', NULL, '2016-05-05 19:49:33', '2016-05-05 19:49:33'),
(16, '$2y$10$KY9ccp6tm/85Iibc3lpAXeOVUtOiVGjmVqITVDJPHtYBXdzUKYE2q', '08020705912', 'olabisi_motunrayo@yahoo.com', 'Sobukanla', 'Olabisi', NULL, NULL, NULL, NULL, 4, NULL, NULL, 1, 1, '16_avatar.png', 'xq55STVDEya0pNSUnsYAyj5x68iPuC1ieBFmRYSc', NULL, '2016-05-05 19:50:41', '2016-05-10 03:36:31'),
(17, '$2y$10$.z5L2MNbvDG2of0jTpU0KONZXSj6Zrbq525c4p6yPNknjBxODppUq', '07037746048', 'solidstepsch@yahoo.com', 'Okpor', 'Julianah', NULL, NULL, NULL, NULL, 4, NULL, NULL, 1, 1, NULL, '5a9v460AasF26HHSh59ewkK3JZ6M0nmKlYvd0xzO', NULL, '2016-05-05 19:52:07', '2016-05-05 19:52:07');

-- --------------------------------------------------------

--
-- Table structure for table `user_types`
--

DROP TABLE IF EXISTS `user_types`;
CREATE TABLE IF NOT EXISTS `user_types` (
  `user_type_id` int(10) unsigned NOT NULL,
  `user_type` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `type` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `user_types`
--

INSERT INTO `user_types` (`user_type_id`, `user_type`, `type`, `created_at`, `updated_at`) VALUES
(1, 'Developer', 1, NULL, NULL),
(2, 'Super Admin', 2, NULL, NULL),
(3, 'Sponsor', 2, '2016-04-28 21:35:55', '2016-04-28 21:35:55'),
(4, 'Staff', 2, '2016-04-28 21:35:15', '2016-04-28 21:35:15');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academic_terms`
--
ALTER TABLE `academic_terms`
  ADD PRIMARY KEY (`academic_term_id`),
  ADD KEY `academic_terms_status_index` (`status`),
  ADD KEY `academic_terms_academic_year_id_index` (`academic_year_id`),
  ADD KEY `academic_terms_term_type_id_index` (`term_type_id`),
  ADD KEY `academic_terms_exam_status_id_index` (`exam_status_id`),
  ADD KEY `academic_terms_exam_setup_by_index` (`exam_setup_by`);

--
-- Indexes for table `academic_years`
--
ALTER TABLE `academic_years`
  ADD PRIMARY KEY (`academic_year_id`),
  ADD KEY `academic_years_status_index` (`status`);

--
-- Indexes for table `classgroups`
--
ALTER TABLE `classgroups`
  ADD PRIMARY KEY (`classgroup_id`);

--
-- Indexes for table `classlevels`
--
ALTER TABLE `classlevels`
  ADD PRIMARY KEY (`classlevel_id`),
  ADD KEY `classlevels_classgroup_id_index` (`classgroup_id`);

--
-- Indexes for table `classrooms`
--
ALTER TABLE `classrooms`
  ADD PRIMARY KEY (`classroom_id`),
  ADD KEY `classrooms_classlevel_id_index` (`classlevel_id`);

--
-- Indexes for table `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`menu_id`),
  ADD KEY `menus_menu_header_id_index` (`menu_header_id`);

--
-- Indexes for table `menu_headers`
--
ALTER TABLE `menu_headers`
  ADD PRIMARY KEY (`menu_header_id`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`menu_item_id`),
  ADD KEY `menu_items_menu_id_index` (`menu_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`permission_id`),
  ADD UNIQUE KEY `permissions_name_unique` (`name`);

--
-- Indexes for table `permission_role`
--
ALTER TABLE `permission_role`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `permission_role_role_id_foreign` (`role_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `roles_name_unique` (`name`),
  ADD KEY `roles_user_type_id_index` (`user_type_id`);

--
-- Indexes for table `roles_menus`
--
ALTER TABLE `roles_menus`
  ADD KEY `roles_menus_role_id_index` (`role_id`),
  ADD KEY `roles_menus_menu_id_index` (`menu_id`);

--
-- Indexes for table `roles_menu_headers`
--
ALTER TABLE `roles_menu_headers`
  ADD KEY `roles_menu_headers_role_id_index` (`role_id`),
  ADD KEY `roles_menu_headers_menu_header_id_index` (`menu_header_id`);

--
-- Indexes for table `roles_menu_items`
--
ALTER TABLE `roles_menu_items`
  ADD KEY `roles_menu_items_role_id_index` (`role_id`),
  ADD KEY `roles_menu_items_menu_item_id_index` (`menu_item_id`);

--
-- Indexes for table `roles_sub_menu_items`
--
ALTER TABLE `roles_sub_menu_items`
  ADD KEY `roles_sub_menu_items_role_id_index` (`role_id`),
  ADD KEY `roles_sub_menu_items_sub_menu_item_id_index` (`sub_menu_item_id`);

--
-- Indexes for table `roles_sub_most_menu_items`
--
ALTER TABLE `roles_sub_most_menu_items`
  ADD KEY `roles_sub_most_menu_items_role_id_index` (`role_id`),
  ADD KEY `roles_sub_most_menu_items_sub_most_menu_item_id_index` (`sub_most_menu_item_id`);

--
-- Indexes for table `role_user`
--
ALTER TABLE `role_user`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `role_user_role_id_foreign` (`role_id`);

--
-- Indexes for table `sponsors`
--
ALTER TABLE `sponsors`
  ADD PRIMARY KEY (`sponsor_id`),
  ADD KEY `sponsors_lga_id_index` (`lga_id`),
  ADD KEY `sponsors_salutation_id_index` (`salutation_id`),
  ADD KEY `sponsors_created_by_index` (`created_by`);

--
-- Indexes for table `staffs`
--
ALTER TABLE `staffs`
  ADD PRIMARY KEY (`staff_id`),
  ADD KEY `staffs_lga_id_index` (`lga_id`),
  ADD KEY `staffs_salutation_id_index` (`salutation_id`),
  ADD KEY `staffs_created_by_index` (`created_by`);

--
-- Indexes for table `subject_classrooms`
--
ALTER TABLE `subject_classrooms`
  ADD PRIMARY KEY (`subject_classroom_id`),
  ADD KEY `subject_classrooms_subject_id_index` (`subject_id`),
  ADD KEY `subject_classrooms_classroom_id_index` (`classroom_id`),
  ADD KEY `subject_classrooms_academic_term_id_index` (`academic_term_id`),
  ADD KEY `subject_classrooms_exam_status_id_index` (`exam_status_id`);

--
-- Indexes for table `subject_tutors`
--
ALTER TABLE `subject_tutors`
  ADD PRIMARY KEY (`subject_tutor_id`),
  ADD KEY `subject_tutors_tutor_id_index` (`tutor_id`),
  ADD KEY `subject_tutors_subject_classroom_id_index` (`subject_classroom_id`);

--
-- Indexes for table `sub_menu_items`
--
ALTER TABLE `sub_menu_items`
  ADD PRIMARY KEY (`sub_menu_item_id`),
  ADD KEY `sub_menu_items_menu_item_id_index` (`menu_item_id`);

--
-- Indexes for table `sub_most_menu_items`
--
ALTER TABLE `sub_most_menu_items`
  ADD PRIMARY KEY (`sub_most_menu_item_id`),
  ADD KEY `sub_most_menu_items_sub_menu_item_id_index` (`sub_menu_item_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `phone_no` (`phone_no`),
  ADD KEY `users_user_type_id_index` (`user_type_id`),
  ADD KEY `salutation_id` (`salutation_id`),
  ADD KEY `lga_id` (`lga_id`);

--
-- Indexes for table `user_types`
--
ALTER TABLE `user_types`
  ADD PRIMARY KEY (`user_type_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `academic_terms`
--
ALTER TABLE `academic_terms`
  MODIFY `academic_term_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `academic_years`
--
ALTER TABLE `academic_years`
  MODIFY `academic_year_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `classgroups`
--
ALTER TABLE `classgroups`
  MODIFY `classgroup_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `classlevels`
--
ALTER TABLE `classlevels`
  MODIFY `classlevel_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `classrooms`
--
ALTER TABLE `classrooms`
  MODIFY `classroom_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `menus`
--
ALTER TABLE `menus`
  MODIFY `menu_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `menu_headers`
--
ALTER TABLE `menu_headers`
  MODIFY `menu_header_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `menu_item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `permission_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=67;
--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `sponsors`
--
ALTER TABLE `sponsors`
  MODIFY `sponsor_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `staffs`
--
ALTER TABLE `staffs`
  MODIFY `staff_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `subject_classrooms`
--
ALTER TABLE `subject_classrooms`
  MODIFY `subject_classroom_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `subject_tutors`
--
ALTER TABLE `subject_tutors`
  MODIFY `subject_tutor_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `sub_menu_items`
--
ALTER TABLE `sub_menu_items`
  MODIFY `sub_menu_item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT for table `sub_most_menu_items`
--
ALTER TABLE `sub_most_menu_items`
  MODIFY `sub_most_menu_item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT for table `user_types`
--
ALTER TABLE `user_types`
  MODIFY `user_type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `academic_terms`
--
ALTER TABLE `academic_terms`
  ADD CONSTRAINT `academic_terms_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`academic_year_id`) ON DELETE CASCADE ON UPDATE CASCADE;

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

--
-- Constraints for table `subject_classrooms`
--
ALTER TABLE `subject_classrooms`
  ADD CONSTRAINT `subject_classrooms_classroom_id_foreign` FOREIGN KEY (`classroom_id`) REFERENCES `classrooms` (`classroom_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `subject_classrooms_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `schools`.`subjects` (`subject_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `subject_tutors`
--
ALTER TABLE `subject_tutors`
  ADD CONSTRAINT `subject_tutors_tutor_id_foreign` FOREIGN KEY (`tutor_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
