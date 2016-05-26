-- phpMyAdmin SQL Dump
-- version 4.0.10.14
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: May 25, 2016 at 11:50 AM
-- Server version: 5.5.49-cll
-- PHP Version: 5.4.31

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `solidste_portal`
--

--
-- Truncate table before insert `academic_terms`
--

TRUNCATE TABLE `academic_terms`;
--
-- Dumping data for table `academic_terms`
--

INSERT INTO `academic_terms` (`academic_term_id`, `academic_term`, `status`, `academic_year_id`, `term_type_id`, `term_begins`, `term_ends`, `exam_status_id`, `exam_setup_by`, `exam_setup_date`, `created_at`, `updated_at`) VALUES
(1, 'Third Term 2015 - 2016', 1, 1, 3, '2016-04-15', '2016-07-15', 2, NULL, NULL, '2016-05-11 15:41:04', '2016-05-11 15:42:07');

--
-- Truncate table before insert `academic_years`
--

TRUNCATE TABLE `academic_years`;
--
-- Dumping data for table `academic_years`
--

INSERT INTO `academic_years` (`academic_year_id`, `academic_year`, `status`, `created_at`, `updated_at`) VALUES
(1, '2015 - 2016', 1, '2016-05-11 15:37:05', '2016-05-11 15:37:05');

--
-- Truncate table before insert `classgroups`
--

TRUNCATE TABLE `classgroups`;
--
-- Dumping data for table `classgroups`
--

INSERT INTO `classgroups` (`classgroup_id`, `classgroup`, `ca_weight_point`, `exam_weight_point`, `created_at`, `updated_at`) VALUES
(1, 'Junior Secondary', 40, 60, '2016-05-11 15:50:22', '2016-05-19 17:13:20'),
(2, 'Senior Secondary', 40, 60, '2016-05-11 15:50:22', '2016-05-19 17:13:20');

--
-- Truncate table before insert `classlevels`
--

TRUNCATE TABLE `classlevels`;
--
-- Dumping data for table `classlevels`
--

INSERT INTO `classlevels` (`classlevel_id`, `classlevel`, `classgroup_id`, `created_at`, `updated_at`) VALUES
(1, 'JSS 1', 1, '2016-05-11 15:52:27', '2016-05-11 15:52:27'),
(2, 'JSS 2', 1, '2016-05-11 15:52:27', '2016-05-11 15:52:27'),
(3, 'JSS 3', 1, '2016-05-11 15:52:27', '2016-05-11 15:52:27'),
(4, 'SS 1', 2, '2016-05-11 15:52:27', '2016-05-11 15:52:27');

--
-- Truncate table before insert `classrooms`
--

TRUNCATE TABLE `classrooms`;
--
-- Dumping data for table `classrooms`
--

INSERT INTO `classrooms` (`classroom_id`, `classroom`, `class_size`, `class_status`, `classlevel_id`, `created_at`, `updated_at`) VALUES
(1, 'JSS 1 PEACE (A)', 9, 1, 1, '2016-05-11 15:59:09', '2016-05-13 15:23:53'),
(2, 'JSS 1 FAVOUR (B)', 9, 1, 1, '2016-05-11 15:59:09', '2016-05-13 15:23:54'),
(3, 'JSS 2 FAITH (A)', 12, 1, 2, '2016-05-11 15:59:09', '2016-05-13 15:23:54'),
(4, 'JSS 3 JOY (A)', 8, 1, 3, '2016-05-11 15:59:09', '2016-05-13 15:23:54'),
(5, 'SS 1 GOLD (A)', 6, 1, 4, '2016-05-13 15:23:54', '2016-05-13 15:23:54');

--
-- Truncate table before insert `grades`
--

TRUNCATE TABLE `grades`;
--
-- Dumping data for table `grades`
--

INSERT INTO `grades` (`grade_id`, `grade`, `grade_abbr`, `upper_bound`, `lower_bound`, `classgroup_id`, `created_at`, `updated_at`) VALUES
(1, 'Distinction', 'A1', 100.00, 81.00, 2, '2016-05-19 15:24:33', '2016-05-19 15:24:33'),
(2, 'Very Good', 'B2', 80.00, 76.00, 2, '2016-05-19 15:24:33', '2016-05-19 15:24:33'),
(3, 'Very Good', 'B3', 75.00, 71.00, 2, '2016-05-19 15:24:33', '2016-05-19 15:24:33'),
(4, 'Credit', 'C4', 70.00, 66.00, 2, '2016-05-19 15:24:33', '2016-05-19 15:24:33'),
(5, 'Credit', 'C5', 65.00, 61.00, 2, '2016-05-19 15:24:33', '2016-05-19 15:24:33'),
(6, 'Credit', 'C6', 60.00, 56.00, 2, '2016-05-19 15:24:33', '2016-05-19 15:24:33'),
(7, 'Pass', 'P7', 55.00, 51.00, 2, '2016-05-19 15:24:33', '2016-05-19 15:24:33'),
(8, 'Pass', 'P8', 50.00, 46.00, 2, '2016-05-19 15:24:33', '2016-05-19 15:24:33'),
(9, 'Fail', 'F9', 45.00, 0.00, 2, '2016-05-19 15:24:33', '2016-05-19 15:24:33'),
(10, 'Distinction', 'A', 100.00, 71.00, 1, '2016-05-19 15:24:33', '2016-05-19 15:24:33'),
(11, 'Very good', 'B', 80.00, 71.00, 1, '2016-05-19 15:24:33', '2016-05-19 15:47:36'),
(12, 'credit', 'C', 70.00, 61.00, 1, '2016-05-19 15:24:33', '2016-05-19 15:47:36'),
(13, 'Pass', 'D', 60.00, 51.00, 1, '2016-05-19 15:24:33', '2016-05-19 15:47:36'),
(14, 'Pass', 'E', 50.00, 41.00, 1, '2016-05-19 15:24:33', '2016-05-19 15:47:36'),
(15, 'Fail', 'F', 40.00, 0.00, 1, '2016-05-19 15:47:36', '2016-05-19 15:47:36');

--
-- Truncate table before insert `menus`
--

TRUNCATE TABLE `menus`;
--
-- Dumping data for table `menus`
--

INSERT INTO `menus` (`menu_id`, `menu`, `menu_url`, `active`, `sequence`, `type`, `icon`, `menu_header_id`, `created_at`, `updated_at`) VALUES
(1, 'SYSTEM', '#', 1, 1, 1, 'fa fa-television', 1, '2016-03-29 22:33:49', '2016-03-29 22:33:49'),
(2, 'PROFILE', '#', 1, 4, 1, 'fa fa-book', 2, '2016-03-30 19:33:36', '2016-05-23 14:16:17'),
(4, 'SPONSORS', '#', 1, 2, 1, 'fa fa-users', 2, '2016-04-17 07:01:21', '2016-05-23 14:16:17'),
(5, 'ADD ACCOUNT', '/accounts/create', 0, 5, 1, 'fa fa-user-plus', 2, '2016-04-18 19:48:45', '2016-04-29 07:38:28'),
(6, 'STAFFS', '#', 1, 3, 1, 'fa fa-users', 2, '2016-04-18 19:51:00', '2016-05-23 14:16:17'),
(7, 'MASTER RECORDS', '#', 1, 2, 1, 'fa fa-book', 1, '2016-05-10 03:53:29', '2016-05-10 03:53:29'),
(8, 'STUDENTS', '#', 1, 1, 1, 'fa fa-users', 2, '2016-05-23 14:16:17', '2016-05-23 14:16:17');

--
-- Truncate table before insert `menu_headers`
--

TRUNCATE TABLE `menu_headers`;
--
-- Dumping data for table `menu_headers`
--

INSERT INTO `menu_headers` (`menu_header_id`, `menu_header`, `active`, `sequence`, `type`, `created_at`, `updated_at`) VALUES
(1, 'SETUPS', 1, 10, 1, '2016-03-29 22:30:39', '2016-03-30 19:33:06'),
(2, 'ACCOUNTS', 1, 9, 1, '2016-03-30 19:33:06', '2016-04-17 07:01:38'),
(3, 'RECORDS', 1, 8, 1, '2016-03-31 06:45:49', '2016-03-31 06:45:49'),
(4, 'PORTAL', 1, 1, 2, '2016-04-15 09:41:26', '2016-04-15 09:55:41');

--
-- Truncate table before insert `menu_items`
--

TRUNCATE TABLE `menu_items`;
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
(15, 'SUBJECTS', '#', 'fa fa-book', 1, '3', 1, 7, '2016-05-13 02:46:59', '2016-05-16 13:39:21'),
(16, 'SESSION', '#', 'fa fa-table', 1, '1', 1, 7, '2016-05-13 17:09:07', '2016-05-13 17:09:07'),
(17, 'CLASS', '#', 'fa fa-table', 1, '2', 1, 7, '2016-05-13 17:09:07', '2016-05-13 17:09:07'),
(19, 'SUBJECT TO CLASS', '/subject-classrooms', 'fa fa-list', 1, '4', 1, 7, '2016-05-16 13:38:22', '2016-05-16 13:39:51'),
(20, 'GRADE GROUPING', '/grades', 'fa fa-check', 1, '6', 1, 7, '2016-05-19 03:54:12', '2016-05-19 03:54:12'),
(21, 'CREATE', '/students/create', 'fa fa-plus', 1, '1', 1, 8, '2016-05-23 14:17:34', '2016-05-23 14:40:11'),
(22, 'MANAGE', '/students', 'fa fa-list', 1, '2', 1, 8, '2016-05-23 14:19:07', '2016-05-23 14:19:07');

--
-- Truncate table before insert `migrations`
--

TRUNCATE TABLE `migrations`;
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
('2016_05_14_173333_create_subject_classes_table', 4),
('2016_05_17_184714_create_students_table', 5),
('2016_05_18_182321_create_grades_table', 6);

--
-- Truncate table before insert `permissions`
--

TRUNCATE TABLE `permissions`;
--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`permission_id`, `name`, `display_name`, `description`, `uri`, `created_at`, `updated_at`) VALUES
(1, 'AcademicTermsController@getDelete', '', '', 'academic-terms/delete/', '2016-04-17 12:17:42', '2016-05-23 14:51:39'),
(2, 'AcademicTermsController@getIndex', '', '', 'academic-terms/index/', '2016-04-17 12:17:42', '2016-05-23 14:51:39'),
(3, 'AcademicTermsController@getIndexAcademic-terms', '', '', 'academic-terms', '2016-04-17 12:17:42', '2016-05-23 14:51:39'),
(4, 'AcademicYearsController@getDelete', '', '', 'academic-years/delete/', '2016-04-17 12:17:42', '2016-05-23 14:51:39'),
(5, 'AcademicYearsController@getIndex', '', '', 'academic-years/index/', '2016-04-17 12:17:42', '2016-05-23 14:51:39'),
(6, 'AcademicYearsController@getIndexAcademic-years', '', '', 'academic-years', '2016-04-17 12:17:42', '2016-05-23 14:51:39'),
(7, 'AccountsController@getCreate', '', '', 'accounts/create/', '2016-04-17 12:17:42', '2016-05-23 14:51:39'),
(8, 'AssessmentSetupsController@getDelete', '', '', 'assessment-setups/delete/', '2016-04-17 12:17:42', '2016-05-23 14:51:39'),
(9, 'AssessmentSetupsController@getDetails', '', '', 'assessment-setups/details/', '2016-04-17 12:17:42', '2016-05-23 14:51:39'),
(10, 'AssessmentSetupsController@getDetailsDelete', '', '', 'assessment-setups/details-delete/', '2016-04-17 12:17:42', '2016-05-23 14:51:39'),
(11, 'AssessmentSetupsController@getIndex', '', '', 'assessment-setups/index/', '2016-04-17 12:17:42', '2016-05-23 14:51:39'),
(12, 'AssessmentSetupsController@getIndexAssessment-setups', '', '', 'assessment-setups', '2016-04-17 12:17:42', '2016-05-23 14:51:39'),
(13, 'AuthController@getLogin', '', '', 'auth/login/', '2016-04-17 12:17:42', '2016-05-23 14:51:40'),
(14, 'AuthController@getLogout', '', '', 'auth/logout/', '2016-04-17 12:17:42', '2016-05-23 14:51:40'),
(15, 'AuthController@getRegister', '', '', 'auth/register/', '2016-04-17 12:17:42', '2016-05-23 14:51:40'),
(16, 'AuthController@logout', '', '', 'logout', '2016-04-17 12:17:42', '2016-05-23 14:51:40'),
(17, 'AuthController@showLoginForm', '', '', 'login', '2016-04-17 12:17:42', '2016-05-23 14:51:40'),
(18, 'AuthController@showRegistrationForm', '', '', 'register', '2016-04-17 12:17:42', '2016-05-23 14:51:40'),
(19, 'ClassGroupsController@getDelete', '', '', 'class-groups/delete/', '2016-04-17 12:17:42', '2016-05-23 14:51:40'),
(20, 'ClassGroupsController@getIndex', '', '', 'class-groups/index/', '2016-04-17 12:17:42', '2016-05-23 14:51:40'),
(21, 'ClassGroupsController@getIndexClass-groups', '', '', 'class-groups', '2016-04-17 12:17:42', '2016-05-23 14:51:40'),
(22, 'ClassLevelsController@getDelete', '', '', 'class-levels/delete/', '2016-04-17 12:17:42', '2016-05-23 14:51:40'),
(23, 'ClassLevelsController@getIndex', '', '', 'class-levels/index/', '2016-04-17 12:17:42', '2016-05-23 14:51:40'),
(24, 'ClassLevelsController@getIndexClass-levels', '', '', 'class-levels', '2016-04-17 12:17:42', '2016-05-23 14:51:40'),
(25, 'ClassRoomsController@getDelete', '', '', 'class-rooms/delete/', '2016-04-17 12:17:42', '2016-05-23 14:51:40'),
(26, 'ClassRoomsController@getIndex', '', '', 'class-rooms/index/', '2016-04-17 12:17:42', '2016-05-23 14:51:40'),
(27, 'ClassRoomsController@getIndexClass-rooms', '', '', 'class-rooms', '2016-04-17 12:17:42', '2016-05-23 14:51:40'),
(28, 'DashboardController@getIndex', '', '', 'dashboard/index/', '2016-04-17 12:17:42', '2016-05-23 14:51:40'),
(29, 'DashboardController@getIndexDashboard', '', '', 'dashboard', '2016-04-17 12:17:42', '2016-05-23 14:51:40'),
(30, 'GradesController@getDelete', '', '', 'grades/delete/', '2016-04-17 12:17:42', '2016-05-23 14:51:40'),
(31, 'GradesController@getIndex', '', '', 'grades/index/', '2016-04-17 12:17:42', '2016-05-23 14:51:40'),
(32, 'GradesController@getIndexGrades', '', '', 'grades', '2016-04-17 12:17:42', '2016-05-23 14:51:40'),
(33, 'HomeController@getIndex', '', '', 'home', '2016-04-17 12:17:42', '2016-05-23 14:51:40'),
(34, 'HomeController@getIndexHome/index/', '', '', 'home/index/', '2016-04-17 12:17:42', '2016-05-23 14:51:40'),
(35, 'ListBoxController@getAcademicTerm', '', '', 'list-box/academic-term/', '2016-04-17 12:17:42', '2016-05-23 14:51:40'),
(36, 'ListBoxController@getClassroom', '', '', 'list-box/classroom/', '2016-04-17 12:17:42', '2016-05-23 14:51:40'),
(37, 'ListBoxController@getLga', '', '', 'list-box/lga/', '2016-04-17 12:17:42', '2016-05-23 14:51:40'),
(38, 'MaritalStatusController@getDelete', '', '', 'marital-statuses/delete/', '2016-04-17 12:17:42', '2016-05-23 14:51:40'),
(39, 'MaritalStatusController@getIndex', '', '', 'marital-statuses/index/', '2016-04-17 12:17:42', '2016-05-23 14:51:40'),
(40, 'MaritalStatusController@getIndexMarital-statuses', '', '', 'marital-statuses', '2016-04-17 12:17:42', '2016-05-23 14:51:40'),
(41, 'MenuController@getDelete', '', '', 'menus/delete/', '2016-04-17 12:17:42', '2016-05-23 14:51:40'),
(42, 'MenuController@getIndex', '', '', 'menus/index/', '2016-04-17 12:17:42', '2016-05-23 14:51:40'),
(43, 'MenuController@getIndexMenus', '', '', 'menus', '2016-04-17 12:17:42', '2016-05-23 14:51:40'),
(44, 'MenuHeaderController@getDelete', '', '', 'menu-headers/delete/', '2016-04-17 12:17:42', '2016-05-23 14:51:40'),
(45, 'MenuHeaderController@getIndex', '', '', 'menu-headers/index/', '2016-04-17 12:17:42', '2016-05-23 14:51:40'),
(46, 'MenuHeaderController@getIndexMenu-headers', '', '', 'menu-headers', '2016-04-17 12:17:42', '2016-05-23 14:51:40'),
(47, 'MenuItemController@getDelete', '', '', 'menu-items/delete/', '2016-04-17 12:17:42', '2016-05-23 14:51:40'),
(48, 'MenuItemController@getIndex', '', '', 'menu-items/index/', '2016-04-17 12:17:42', '2016-05-23 14:51:40'),
(49, 'MenuItemController@getIndexMenu-items', '', '', 'menu-items', '2016-04-17 12:17:42', '2016-05-23 14:51:40'),
(50, 'PasswordController@reset', '', '', 'password/reset', '2016-04-17 12:17:42', '2016-05-23 14:51:40'),
(51, 'PasswordController@sendResetLinkEmail', '', '', 'password/email', '2016-04-17 12:17:42', '2016-05-23 14:51:40'),
(52, 'PasswordController@showResetForm', '', '', 'password/reset/', '2016-04-17 12:17:42', '2016-05-23 14:51:40'),
(53, 'PermissionsController@getIndex', '', '', 'permissions/index/', '2016-04-17 12:17:42', '2016-05-23 14:51:40'),
(54, 'PermissionsController@getIndexPermissions', '', '', 'permissions', '2016-04-17 12:17:42', '2016-05-23 14:51:40'),
(55, 'PermissionsController@getRolesPermissions', '', '', 'permissions/roles-permissions/', '2016-04-17 12:17:42', '2016-05-23 14:51:40'),
(56, 'ProfileController@getEdit', '', '', 'profiles/edit/', '2016-04-17 12:17:42', '2016-05-23 14:51:40'),
(57, 'ProfileController@getIndex', '', '', 'profiles/index/', '2016-04-17 12:17:42', '2016-05-23 14:51:40'),
(58, 'ProfileController@getIndexProfiles', '', '', 'profiles', '2016-04-17 12:17:42', '2016-05-23 14:51:40'),
(59, 'RolesController@getDelete', '', '', 'roles/delete/', '2016-04-19 14:59:35', '2016-05-23 14:51:40'),
(60, 'RolesController@getIndex', '', '', 'roles/index/', '2016-04-19 14:59:35', '2016-05-23 14:51:40'),
(61, 'RolesController@getIndexRoles', '', '', 'roles', '2016-04-19 14:59:35', '2016-05-23 14:51:40'),
(62, 'RolesController@getUsersRoles', '', '', 'roles/users-roles/', '2016-04-19 14:59:35', '2016-05-23 14:51:40'),
(63, 'SalutationController@getDelete', '', '', 'salutations/delete/', '2016-04-19 14:59:35', '2016-05-23 14:51:40'),
(64, 'SalutationController@getIndex', '', '', 'salutations/index/', '2016-04-19 14:59:35', '2016-05-23 14:51:40'),
(65, 'SalutationController@getIndexSalutations', '', '', 'salutations', '2016-04-19 14:59:35', '2016-05-23 14:51:40'),
(66, 'SchoolController@getCreate', '', '', 'schools/create/', '2016-04-19 14:59:35', '2016-05-23 14:51:40'),
(67, 'SchoolController@getDbConfig', '', '', 'schools/db-config/', '2016-05-23 14:51:40', '2016-05-23 14:51:40'),
(68, 'SchoolController@getEdit', '', '', 'schools/edit/', '2016-05-23 14:51:40', '2016-05-23 14:51:40'),
(69, 'SchoolController@getIndex', '', '', 'schools/index/', '2016-05-23 14:51:40', '2016-05-23 14:51:40'),
(70, 'SchoolController@getIndexSchools', '', '', 'schools', '2016-05-23 14:51:40', '2016-05-23 14:51:40'),
(71, 'SchoolController@getSearch', '', '', 'schools/search/', '2016-05-23 14:51:40', '2016-05-23 14:51:40'),
(72, 'SchoolController@getStatus', '', '', 'schools/status/', '2016-05-23 14:51:40', '2016-05-23 14:51:40'),
(73, 'SchoolSubjectsController@getIndex', '', '', 'school-subjects/index/', '2016-05-23 14:51:40', '2016-05-23 14:51:40'),
(74, 'SchoolSubjectsController@getIndexSchool-subjects', '', '', 'school-subjects', '2016-05-23 14:51:40', '2016-05-23 14:51:40'),
(75, 'SchoolSubjectsController@getRename', '', '', 'school-subjects/rename/', '2016-05-23 14:51:40', '2016-05-23 14:51:40'),
(76, 'SchoolSubjectsController@getView', '', '', 'school-subjects/view/', '2016-05-23 14:51:40', '2016-05-23 14:51:40'),
(77, 'SponsorController@getEdit', '', '', 'sponsors/edit/', '2016-05-23 14:51:40', '2016-05-23 14:51:40'),
(78, 'SponsorController@getIndex', '', '', 'sponsors/index/', '2016-05-23 14:51:40', '2016-05-23 14:51:40'),
(79, 'SponsorController@getIndexSponsors', '', '', 'sponsors', '2016-05-23 14:51:40', '2016-05-23 14:51:40'),
(80, 'SponsorController@getView', '', '', 'sponsors/view/', '2016-05-23 14:51:40', '2016-05-23 14:51:40'),
(81, 'StaffController@getEdit', '', '', 'staffs/edit/', '2016-05-23 14:51:40', '2016-05-23 14:51:40'),
(82, 'StaffController@getIndex', '', '', 'staffs/index/', '2016-05-23 14:51:40', '2016-05-23 14:51:40'),
(83, 'StaffController@getIndexStaffs', '', '', 'staffs', '2016-05-23 14:51:40', '2016-05-23 14:51:40'),
(84, 'StaffController@getView', '', '', 'staffs/view/', '2016-05-23 14:51:40', '2016-05-23 14:51:40'),
(85, 'StudentController@getCreate', '', '', 'students/create/', '2016-05-23 14:51:40', '2016-05-23 14:51:40'),
(86, 'StudentController@getEdit', '', '', 'students/edit/', '2016-05-23 14:51:40', '2016-05-23 14:51:40'),
(87, 'StudentController@getIndex', '', '', 'students/index/', '2016-05-23 14:51:40', '2016-05-23 14:51:40'),
(88, 'StudentController@getIndexStudents', '', '', 'students', '2016-05-23 14:51:40', '2016-05-23 14:51:40'),
(89, 'StudentController@getSponsors', '', '', 'students/sponsors/', '2016-05-23 14:51:40', '2016-05-23 14:51:40'),
(90, 'StudentController@getView', '', '', 'students/view/', '2016-05-23 14:51:40', '2016-05-23 14:51:40'),
(91, 'SubMenuItemController@getDelete', '', '', 'sub-menu-items/delete/', '2016-05-23 14:51:40', '2016-05-23 14:51:40'),
(92, 'SubMenuItemController@getIndex', '', '', 'sub-menu-items/index/', '2016-05-23 14:51:40', '2016-05-23 14:51:40'),
(93, 'SubMenuItemController@getIndexSub-menu-items', '', '', 'sub-menu-items', '2016-05-23 14:51:40', '2016-05-23 14:51:40'),
(94, 'SubMostMenuItemController@getDelete', '', '', 'sub-most-menu-items/delete/', '2016-05-23 14:51:40', '2016-05-23 14:51:40'),
(95, 'SubMostMenuItemController@getIndex', '', '', 'sub-most-menu-items/index/', '2016-05-23 14:51:40', '2016-05-23 14:51:40'),
(96, 'SubMostMenuItemController@getIndexSub-most-menu-items', '', '', 'sub-most-menu-items', '2016-05-23 14:51:40', '2016-05-23 14:51:40'),
(97, 'SubjectClassRoomsController@getAssignTutor', '', '', 'subject-classrooms/assign-tutor/', '2016-05-23 14:51:40', '2016-05-23 14:51:40'),
(98, 'SubjectClassRoomsController@getIndex', '', '', 'subject-classrooms/index/', '2016-05-23 14:51:40', '2016-05-23 14:51:40'),
(99, 'SubjectClassRoomsController@getIndexSubject-classrooms', '', '', 'subject-classrooms', '2016-05-23 14:51:40', '2016-05-23 14:51:40'),
(100, 'SubjectGroupsController@getDelete', '', '', 'subject-groups/delete/', '2016-05-23 14:51:40', '2016-05-23 14:51:40'),
(101, 'SubjectGroupsController@getIndex', '', '', 'subject-groups/index/', '2016-05-23 14:51:40', '2016-05-23 14:51:40'),
(102, 'SubjectGroupsController@getIndexSubject-groups', '', '', 'subject-groups', '2016-05-23 14:51:40', '2016-05-23 14:51:40'),
(103, 'SubjectsController@getDelete', '', '', 'subjects/delete/', '2016-05-23 14:51:40', '2016-05-23 14:51:40'),
(104, 'SubjectsController@getIndex', '', '', 'subjects/index/', '2016-05-23 14:51:40', '2016-05-23 14:51:40'),
(105, 'SubjectsController@getIndexSubjects', '', '', 'subjects', '2016-05-23 14:51:40', '2016-05-23 14:51:40'),
(106, 'UserController@getChange', '', '', 'users/change/', '2016-05-23 14:51:40', '2016-05-23 14:51:40'),
(107, 'UserController@getCreate', '', '', 'users/create/', '2016-05-23 14:51:41', '2016-05-23 14:51:41'),
(108, 'UserController@getEdit', '', '', 'users/edit/', '2016-05-23 14:51:41', '2016-05-23 14:51:41'),
(109, 'UserController@getIndex', '', '', 'users/index/', '2016-05-23 14:51:41', '2016-05-23 14:51:41'),
(110, 'UserController@getIndexUsers', '', '', 'users', '2016-05-23 14:51:41', '2016-05-23 14:51:41'),
(111, 'UserController@getStatus', '', '', 'users/status/', '2016-05-23 14:51:41', '2016-05-23 14:51:41'),
(112, 'UserController@getView', '', '', 'users/view/', '2016-05-23 14:51:41', '2016-05-23 14:51:41'),
(113, 'UserTypeController@getDelete', '', '', 'user-types/delete/', '2016-05-23 14:51:41', '2016-05-23 14:51:41'),
(114, 'UserTypeController@getIndex', '', '', 'user-types/index/', '2016-05-23 14:51:41', '2016-05-23 14:51:41'),
(115, 'UserTypeController@getIndexUser-types', '', '', 'user-types', '2016-05-23 14:51:41', '2016-05-23 14:51:41');

--
-- Truncate table before insert `permission_role`
--

TRUNCATE TABLE `permission_role`;
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
(67, 1),
(68, 1),
(69, 1),
(70, 1),
(71, 1),
(72, 1),
(73, 1),
(74, 1),
(75, 1),
(76, 1),
(77, 1),
(78, 1),
(79, 1),
(80, 1),
(81, 1),
(82, 1),
(83, 1),
(84, 1),
(85, 1),
(86, 1),
(87, 1),
(88, 1),
(89, 1),
(90, 1),
(91, 1),
(92, 1),
(93, 1),
(94, 1),
(95, 1),
(96, 1),
(97, 1),
(98, 1),
(99, 1),
(100, 1),
(101, 1),
(102, 1),
(103, 1),
(104, 1),
(105, 1),
(106, 1),
(107, 1),
(108, 1),
(109, 1),
(110, 1),
(111, 1),
(112, 1),
(113, 1),
(114, 1),
(115, 1),
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
(75, 2),
(77, 2),
(78, 2),
(80, 2),
(81, 2),
(82, 2),
(83, 2),
(84, 2),
(85, 2),
(86, 2),
(87, 2),
(88, 2),
(89, 2),
(90, 2),
(97, 2),
(98, 2),
(99, 2),
(100, 2),
(101, 2),
(102, 2),
(103, 2),
(104, 2),
(105, 2),
(107, 2),
(110, 2),
(113, 2),
(1, 3),
(2, 3),
(4, 3),
(9, 3),
(10, 3);

--
-- Truncate table before insert `roles`
--

TRUNCATE TABLE `roles`;
--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `name`, `display_name`, `description`, `user_type_id`, `created_at`, `updated_at`) VALUES
(1, 'developer', 'Developer', 'The software developer', 1, '2016-03-29 22:30:11', '2016-04-28 21:36:59'),
(2, 'super_admin', 'Super Admin', 'System Administrator', 2, '2016-03-30 09:51:57', '2016-04-28 22:33:03'),
(3, 'sponsor', 'Sponsor', 'Sponsor', 3, '2016-04-16 17:25:54', '2016-04-28 21:36:59'),
(4, 'staff', 'Staff', 'Staff', 4, '2016-04-16 17:25:54', '2016-04-28 21:36:59');

--
-- Truncate table before insert `roles_menus`
--

TRUNCATE TABLE `roles_menus`;
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
(2, 7),
(1, 8),
(2, 8);

--
-- Truncate table before insert `roles_menu_headers`
--

TRUNCATE TABLE `roles_menu_headers`;
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

--
-- Truncate table before insert `roles_menu_items`
--

TRUNCATE TABLE `roles_menu_items`;
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
(1, 15),
(2, 15),
(1, 16),
(2, 16),
(1, 17),
(2, 17),
(1, 19),
(2, 19),
(1, 20),
(2, 20),
(1, 21),
(2, 21),
(1, 22),
(2, 22);

--
-- Truncate table before insert `roles_sub_menu_items`
--

TRUNCATE TABLE `roles_sub_menu_items`;
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
(2, 17),
(1, 18),
(2, 18),
(1, 19),
(2, 19),
(1, 20),
(2, 20),
(1, 21),
(2, 21),
(1, 22),
(2, 22);

--
-- Truncate table before insert `roles_sub_most_menu_items`
--

TRUNCATE TABLE `roles_sub_most_menu_items`;
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

--
-- Truncate table before insert `role_user`
--

TRUNCATE TABLE `role_user`;
--
-- Dumping data for table `role_user`
--

INSERT INTO `role_user` (`user_id`, `role_id`) VALUES
(1, 1),
(2, 2),
(18, 3),
(19, 3),
(20, 3),
(21, 3),
(22, 3),
(23, 3),
(24, 3),
(25, 3),
(26, 3),
(27, 3),
(28, 3),
(29, 3),
(30, 3),
(31, 3),
(32, 3),
(33, 3),
(34, 3),
(35, 3),
(36, 3),
(37, 3),
(38, 3),
(39, 3),
(40, 3),
(41, 3),
(42, 3),
(43, 3),
(44, 3),
(45, 3),
(46, 3),
(47, 3),
(48, 3),
(49, 3),
(50, 3),
(51, 3),
(52, 3),
(53, 3),
(54, 3),
(55, 3),
(56, 3),
(57, 3),
(58, 3),
(59, 3),
(60, 3),
(61, 3),
(62, 3),
(63, 3),
(64, 3),
(65, 3),
(66, 3),
(67, 3),
(68, 3),
(69, 3),
(70, 3),
(71, 3),
(72, 3),
(73, 3),
(74, 3),
(75, 3),
(76, 3),
(77, 3),
(78, 3),
(79, 3),
(80, 3),
(81, 3),
(82, 3),
(83, 3),
(84, 3),
(85, 3),
(86, 3),
(87, 3),
(88, 3),
(89, 3),
(90, 3),
(91, 3),
(92, 3),
(93, 3),
(94, 3),
(95, 3),
(96, 3),
(97, 3),
(98, 3),
(99, 3),
(100, 3),
(101, 3),
(102, 3),
(103, 3),
(104, 3),
(105, 3),
(106, 3),
(107, 3),
(108, 3),
(109, 3),
(110, 3),
(111, 3),
(112, 3),
(113, 3),
(114, 3),
(115, 3),
(116, 3),
(117, 3),
(118, 3),
(119, 3),
(120, 3),
(121, 3),
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
(17, 4),
(122, 4),
(123, 4),
(124, 4),
(125, 4);

--
-- Truncate table before insert `sponsors`
--

TRUNCATE TABLE `sponsors`;
--
-- Truncate table before insert `staffs`
--

TRUNCATE TABLE `staffs`;
--
-- Truncate table before insert `students`
--

TRUNCATE TABLE `students`;
--
-- Truncate table before insert `student_classes`
--

TRUNCATE TABLE `student_classes`;
--
-- Truncate table before insert `subject_classrooms`
--

TRUNCATE TABLE `subject_classrooms`;
--
-- Dumping data for table `subject_classrooms`
--

INSERT INTO `subject_classrooms` (`subject_classroom_id`, `subject_id`, `classroom_id`, `academic_term_id`, `exam_status_id`, `tutor_id`, `created_at`, `updated_at`) VALUES
(1, 14, 1, 1, 2, 122, '2016-05-18 18:41:54', '2016-05-18 18:41:45'),
(2, 3, 1, 1, 2, 122, '2016-05-18 18:41:54', '2016-05-18 18:41:54'),
(3, 4, 1, 1, 2, 7, '2016-05-18 18:41:54', '2016-05-18 18:16:01'),
(4, 5, 1, 1, 2, 8, '2016-05-18 18:41:54', '2016-05-18 18:16:08'),
(5, 16, 1, 1, 2, 4, NULL, '2016-05-18 18:19:27'),
(6, 38, 1, 1, 2, 6, '2016-05-18 18:41:54', '2016-05-18 18:17:08'),
(7, 9, 1, 1, 2, 3, NULL, '2016-05-18 18:17:07'),
(8, 1, 1, 1, 2, 6, NULL, '2016-05-18 18:17:14'),
(9, 39, 1, 1, 2, 123, NULL, '2016-05-18 18:42:01'),
(10, 15, 1, 1, 2, 3, NULL, '2016-05-18 18:17:32'),
(11, 2, 1, 1, 2, 7, NULL, '2016-05-18 18:19:42'),
(12, 8, 1, 1, 2, 12, NULL, '2016-05-18 18:19:47'),
(13, 6, 1, 1, 2, 4, NULL, '2016-05-18 18:19:50'),
(14, 13, 1, 1, 2, 124, NULL, '2016-05-18 18:42:08'),
(15, 14, 2, 1, 2, 122, NULL, '2016-05-18 18:42:54'),
(16, 3, 2, 1, 2, 122, NULL, '2016-05-18 18:42:58'),
(17, 4, 2, 1, 2, 7, NULL, '2016-05-18 18:43:03'),
(18, 5, 2, 1, 2, 8, NULL, '2016-05-18 18:43:06'),
(19, 16, 2, 1, 2, 4, NULL, '2016-05-18 18:43:12'),
(20, 38, 2, 1, 2, 6, NULL, '2016-05-18 18:43:18'),
(21, 9, 2, 1, 2, 3, NULL, '2016-05-18 18:43:22'),
(22, 1, 2, 1, 2, 6, NULL, '2016-05-18 18:43:26'),
(23, 39, 2, 1, 2, 123, NULL, '2016-05-18 18:43:30'),
(24, 15, 2, 1, 2, 3, NULL, '2016-05-18 18:43:33'),
(25, 2, 2, 1, 2, 7, NULL, '2016-05-18 18:43:40'),
(26, 8, 2, 1, 2, 12, NULL, '2016-05-18 18:43:43'),
(27, 6, 2, 1, 2, 4, NULL, '2016-05-18 18:43:54'),
(28, 13, 2, 1, 2, 124, NULL, '2016-05-18 18:43:58'),
(29, 14, 3, 1, 2, 3, NULL, '2016-05-18 18:49:07'),
(30, 3, 3, 1, 2, 122, NULL, '2016-05-18 18:45:42'),
(31, 4, 3, 1, 2, 7, NULL, '2016-05-18 18:45:49'),
(32, 5, 3, 1, 2, 8, NULL, '2016-05-18 18:46:01'),
(33, 16, 3, 1, 2, 124, NULL, '2016-05-18 18:46:11'),
(34, 38, 3, 1, 2, 4, NULL, '2016-05-18 18:46:20'),
(35, 9, 3, 1, 2, 3, NULL, '2016-05-18 18:46:23'),
(36, 1, 3, 1, 2, 6, NULL, '2016-05-18 18:46:30'),
(37, 39, 3, 1, 2, 123, NULL, '2016-05-18 18:46:36'),
(38, 15, 3, 1, 2, 3, NULL, '2016-05-18 18:46:38'),
(39, 2, 3, 1, 2, 7, NULL, '2016-05-18 18:46:51'),
(43, 14, 4, 1, 2, 122, NULL, '2016-05-18 18:50:43'),
(44, 3, 4, 1, 2, 122, NULL, '2016-05-18 18:50:47'),
(45, 4, 4, 1, 2, 7, NULL, '2016-05-18 18:50:51'),
(46, 5, 4, 1, 2, 8, NULL, '2016-05-18 18:50:59'),
(47, 16, 4, 1, 2, 124, NULL, '2016-05-18 18:51:10'),
(48, 38, 4, 1, 2, 4, NULL, '2016-05-18 18:51:14'),
(49, 9, 4, 1, 2, 3, NULL, '2016-05-18 18:51:17'),
(50, 1, 4, 1, 2, 6, NULL, '2016-05-18 18:51:28'),
(51, 39, 4, 1, 2, 123, NULL, '2016-05-18 18:51:38'),
(52, 15, 4, 1, 2, 3, NULL, '2016-05-18 18:51:41'),
(53, 2, 4, 1, 2, 7, NULL, '2016-05-18 18:51:47'),
(54, 8, 4, 1, 2, 12, NULL, '2016-05-18 18:51:50'),
(55, 6, 4, 1, 2, 4, NULL, '2016-05-18 18:51:56'),
(56, 13, 4, 1, 2, 124, NULL, '2016-05-18 18:52:01'),
(57, 23, 5, 1, 2, 122, NULL, '2016-05-18 18:52:27'),
(58, 22, 5, 1, 2, 122, NULL, '2016-05-18 18:52:31'),
(59, 16, 5, 1, 2, 124, NULL, '2016-05-18 18:52:42'),
(60, 38, 5, 1, 2, 4, NULL, '2016-05-18 18:52:48'),
(61, 9, 5, 1, 2, 3, NULL, '2016-05-18 18:52:54'),
(62, 32, 5, 1, 2, 10, NULL, '2016-05-18 18:52:57'),
(63, 1, 5, 1, 2, 6, NULL, '2016-05-18 18:53:02'),
(64, 24, 5, 1, 2, 3, NULL, '2016-05-18 18:53:12'),
(65, 34, 5, 1, 2, 9, NULL, '2016-05-18 18:53:16'),
(66, 33, 5, 1, 2, 4, NULL, '2016-05-18 18:53:20'),
(67, 19, 5, 1, 2, 6, NULL, '2016-05-18 18:53:31'),
(68, 2, 5, 1, 2, 7, NULL, '2016-05-18 18:53:34'),
(70, 21, 5, 1, 2, 7, NULL, '2016-05-18 18:53:49'),
(71, 13, 5, 1, 2, 124, NULL, '2016-05-18 18:53:52'),
(72, 26, 2, 1, 2, 125, NULL, '2016-05-18 19:03:06'),
(73, 26, 1, 1, 2, 125, NULL, '2016-05-18 19:02:37'),
(74, 26, 4, 1, 2, 125, NULL, '2016-05-18 19:03:53'),
(75, 26, 3, 1, 2, 125, NULL, '2016-05-18 19:03:31'),
(76, 8, 3, 1, 2, 12, NULL, '2016-05-18 18:48:40'),
(77, 6, 3, 1, 2, 4, NULL, '2016-05-18 18:48:44'),
(78, 13, 3, 1, 2, 124, NULL, '2016-05-18 18:48:50'),
(79, 48, 1, 1, 2, NULL, NULL, NULL),
(80, 47, 1, 1, 2, NULL, NULL, NULL),
(81, 48, 2, 1, 2, NULL, NULL, NULL),
(82, 47, 2, 1, 2, NULL, NULL, NULL),
(83, 48, 3, 1, 2, NULL, NULL, NULL),
(84, 47, 3, 1, 2, NULL, NULL, NULL),
(85, 48, 4, 1, 2, NULL, NULL, NULL),
(86, 47, 4, 1, 2, NULL, NULL, NULL),
(87, 7, 4, 1, 2, NULL, NULL, NULL),
(88, 47, 5, 1, 2, NULL, NULL, NULL);

--
-- Truncate table before insert `sub_menu_items`
--

TRUNCATE TABLE `sub_menu_items`;
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
(14, 'SUBJECT GROUPS', '/subject-groups', 'fa fa-table', 1, '3', 1, 7, '2016-05-13 02:44:42', '2016-05-13 02:44:42'),
(15, 'SUBJECTS', '/subjects', 'fa fa-book', 1, '4', 1, 7, '2016-05-13 02:44:42', '2016-05-13 02:44:42'),
(16, 'MANAGE', '/school-subjects', 'fa fa-plus-square', 1, '1', 1, 15, '2016-05-13 02:48:47', '2016-05-13 02:48:47'),
(17, 'VIEW LIST', '/school-subjects/view', 'fa fa-eye', 1, '2', 1, 15, '2016-05-13 02:48:47', '2016-05-13 02:48:47'),
(18, 'ACADEMIC YEAR', '/academic-years', 'fa fa-plus', 1, '1', 1, 16, '2016-05-13 17:10:24', '2016-05-13 17:11:39'),
(19, 'ACADEMIC TERM', '/academic-terms', 'fa fa-plus', 1, '2', 1, 16, '2016-05-13 17:11:39', '2016-05-13 17:11:39'),
(20, 'CLASS GROUP', '/class-groups', 'fa fa-plus', 1, '1', 1, 17, '2016-05-13 17:13:48', '2016-05-13 17:13:48'),
(21, 'CLASS LEVEL', '/class-levels', 'fa fa-plus', 1, '2', 1, 17, '2016-05-13 17:13:48', '2016-05-13 17:13:48'),
(22, 'CLASS ROOMS', '/class-rooms', 'fa fa-plus', 1, '3', 1, 17, '2016-05-13 17:13:48', '2016-05-13 17:13:48');

--
-- Truncate table before insert `sub_most_menu_items`
--

TRUNCATE TABLE `sub_most_menu_items`;
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

--
-- Truncate table before insert `users`
--

TRUNCATE TABLE `users`;
--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `password`, `phone_no`, `email`, `first_name`, `last_name`, `middle_name`, `gender`, `dob`, `phone_no2`, `user_type_id`, `lga_id`, `salutation_id`, `verified`, `status`, `avatar`, `verification_code`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, '$2y$10$WgHQSaszEOSJpz2HsoUToeJoyCxh7fGuc3ZoLJA.NubXU42L6E3SG', '08011223344', 'admin@gmail.com', 'Emma', 'Okafor', '', 'Male', '2016-04-05', '', 1, 0, 1, 1, 1, '1_avatar.jpg', NULL, 'KTi4iA5PkcLlkLcsBM89GAH973RgE8SyBrBkqjiyGxMj7PvgqAPhs8QnFNmO', NULL, '2016-05-26 00:11:07'),
(2, '$2y$10$/VnAIZSpHw2o042t.bmP7eqCPAN/imxPcaAaTkTfno1uPi8BaOQCa', '08161730788', 'bamidelemike2003@yahoo.com', 'Bamidele', 'Micheal', '', 'Male', '1976-02-11', '08066303843', 2, 476, 1, 1, 1, '2_avatar.jpg', 'x9pxH08aB60ZKwe12DDKbiD3V5628TyGMd1v8Q5I', 'hij7WxD51AFXCOHRDddxdezuedlC9l1fxqrczMYBOKwg4X96PqTb4s71mqm3', '2016-04-28 22:21:05', '2016-05-23 20:47:00'),
(3, '$2y$10$Pc9CBBOKkpbTAlnoxc0iveGkS5xRKREBYlwyPRzxSUxsb.9nNJ9cS', '08186644996', 'onegirl2004@yahoo.com', 'Emina', 'Omotolani', NULL, NULL, NULL, NULL, 4, NULL, NULL, 1, 1, '3_avatar.png', 'sJkNJULOX0XDBVHoG929c8zOuHvuQJ8taqOE4MK7', NULL, '2016-05-05 19:29:34', '2016-05-21 00:00:37'),
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
(17, '$2y$10$.z5L2MNbvDG2of0jTpU0KONZXSj6Zrbq525c4p6yPNknjBxODppUq', '07037746048', 'solidstepsch@yahoo.com', 'Okpor', 'Julianah', NULL, NULL, NULL, NULL, 4, NULL, NULL, 1, 1, NULL, '5a9v460AasF26HHSh59ewkK3JZ6M0nmKlYvd0xzO', 'Zblv6AgLZd7pPToYfoxFcB1NgSbEA2TRMP8untXOtUKwkOalipCzDuUQUIwW', '2016-05-05 19:52:07', '2016-05-17 19:24:02'),
(18, '$2y$10$JUNQaU7D.PNaYtQLFfACHePT6F6y1mNHRKTpwKQgHqGSDQGkJ0tW.', '08035423767', 'abayomi@yahoo.com', 'Abayomi', 'Abayomi', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'h9vO4igUqSOLJl1MgI9Zl8hVIgMQSkxtuPjCVxB5', NULL, '2016-05-10 19:04:01', '2016-05-10 19:04:01'),
(19, '$2y$10$bJjJRYzohk2vF5DUZZfOjeSOvITzu654hN2DYMoPu/GNLLtp7c0M.', '08034811290', 'adebayo@yahoo.com', 'adebayo', 'adebayo', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'tDvbmB4nNhENIbvbT5mwj9v4MOsY4NEl0PKW2WUC', NULL, '2016-05-10 19:05:04', '2016-05-10 19:05:04'),
(20, '$2y$10$ka1poX6/MRQMppAMmsthhOHJcqB9kXcw1u.WnZoXIKMyj7PPSM5pS', '07087886188', 'adebiyi@yahoo.com', 'adebiyi', 'adebiyi', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'JHZJEPT3cNjjp1dlWlX8XJWoJAUJNvKg6fg25wSt', NULL, '2016-05-10 19:06:39', '2016-05-10 19:06:39'),
(21, '$2y$10$VkOuEo.sWHGSuqKCU8iff.NJ2G2Bq3TuYUxXUY7sdSR984A9pSIhu', '08036000828', 'adelola@yahoo.com', 'adelola', 'adelola', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'KXnd6uuSsfXRLmRHeq9i3TkgVZYihrXScCXsxaMB', NULL, '2016-05-10 19:08:26', '2016-05-10 19:08:26'),
(22, '$2y$10$QNj7V0BJg34D2CdIsd8A3.IgsQiB.NP31ICjv80C6.DsBFoYzPqB6', '08112000692', 'adesida@yahoo.com', 'adesida', 'adesida', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'LsSAlLsp9awyMemCbabGP255Ag1OAFelXPKYk4ZA', NULL, '2016-05-10 19:09:15', '2016-05-10 19:09:15'),
(23, '$2y$10$o.I3jxl6b0fLlOq6j7i.ruFe8YWuvhbHU0k.SUG6K84he.BdF0W6a', '08023019212', 'adewumi@yahoo.com', 'adewumi', 'adewumi', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'LMEFt232rTF9xZ73Krv6BBwXVMPpXCh3rb2TMTxw', NULL, '2016-05-10 19:10:17', '2016-05-10 19:10:17'),
(24, '$2y$10$tCo6l397eJvSXNX2mHRw6.513v7dnIOFasAk2DwBXDlhaNxNTxGLK', '08067151353', 'adeyemi@yahoo.com', 'adeyemi', 'adeyemi', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'IWRH6Ij4Mh7qzaA0HQ208NcLTbJehSFUvgUxp0b7', NULL, '2016-05-10 19:11:43', '2016-05-10 19:11:43'),
(25, '$2y$10$uag5F5RE9WauCOzbEr44g.dfTkxV.JBJwF2gWV1wqAw.qj5gavzFG', '08034730900', 'adeyemi1@yahoo.com', 'adeyemi', 'adeyemi', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'qpZEX3b04G4EhTAixG5U21SOpdat5FbwQiKsvsYI', NULL, '2016-05-10 19:14:01', '2016-05-10 19:14:01'),
(26, '$2y$10$zCmlJws2.7GNyeLTjLVKherZd2faZl9W8FoJcxwQP/TVai4pdlvna', '08038666239', 'adeyemi2@yahoo.com', 'adeyemi', 'adeyemi', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'uZEZIJvQioqHfboZwjYzozKNk3nSBdiPrNZtBwNj', NULL, '2016-05-10 19:15:18', '2016-05-10 19:15:18'),
(27, '$2y$10$2hFNokJGmVd82yeHfmcIY.AjgcYMWNhTUDfiv6x72hHx27Zx/QmdS', '08034133636', 'adoye@yahoo.com', 'adoye', 'adoye', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'cHaVWYEZAAGISfPs9s2qenYJPhnZIDrfzeQfTmVr', NULL, '2016-05-10 19:16:24', '2016-05-10 19:16:24'),
(28, '$2y$10$th.a78j6wsAgZLQxt1jel.UzBudpc.sTxsDssREp5HLUvMq7HbAWi', '07039691870', 'afolabi@yahoo.com', 'afolabi', 'afolabi', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'mYFmbJLKnlr2epos1u3p5LWq92SF6NKub14y5VPp', NULL, '2016-05-10 19:24:01', '2016-05-10 19:24:01'),
(29, '$2y$10$IPusZZItK7vJLLR9fDwMJu5HGEkWyY/P3hcK2jjOZukw1XrudtgEu', '08028615460', 'aje@yahoo.com', 'aje', 'aje', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'mbSCBmd7uq5QlDyXday6XWmyDeHqf5Ua19gYSGhy', NULL, '2016-05-10 19:26:30', '2016-05-10 19:26:30'),
(30, '$2y$10$waV0OGh/L4pa2B31vOh9ZuinCRDuhbONTLbvvcySB4UBoVkapJvG.', '08055802718', 'akinbunu@yahoo.com', 'akinbinu', 'akinbunu', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'HMChYqs4vyx6gDvY93Lpu1v7qqG0gZId44tvcEcN', NULL, '2016-05-10 19:30:19', '2016-05-10 19:30:19'),
(31, '$2y$10$pNq5CjlYXD1ZJco3cszVGuSyGUwYD6.4zBfeMwS49BEcTfDJME8I6', '08035131207', 'akinyosoye@yahoo.com', 'akinyosoye', 'akinyosoye', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'Qbw5ZuxWcnLHfuemMx9nJs1HqRVDfUn9LQhkBgZ8', NULL, '2016-05-10 19:32:11', '2016-05-10 19:32:11'),
(32, '$2y$10$6LsehSGgyGr3q1YqLdjAgu3POHj7qU0R7AiGtH2Cs7ufN5trc4o3W', '08058044900', 'alabi@yahoo.com', 'alabi', 'alabi', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'GUQ03ULqXdikZudKvHlqY2U4bD4eO5ztSkmB6iTf', NULL, '2016-05-10 19:37:53', '2016-05-10 19:37:53'),
(33, '$2y$10$nrIpQLpZ8bOMijlp2sjWO.BOMloOqHZ0A4X8hQXAROCAHVYq1QzXu', '08034750375', 'anaele@yahoo.com', 'anaele', 'anaele', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'fb84h2u0bJNqiG9oHed5rAmcU6U09VArk6R9bYs1', NULL, '2016-05-10 19:39:12', '2016-05-10 19:39:12'),
(34, '$2y$10$/mlX27x3JB/UDLFNj4y.aO36/LEARXpT1GqxAYn2BSH2fLZyKpKcy', '08023029379', 'animashaun@yahoo.com', 'animashaun', 'animashaun', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'Z4csXQZI8Zbd4VNCJHYrm4FjJXqkUFF9LDpsJH0y', NULL, '2016-05-10 19:40:27', '2016-05-10 19:40:27'),
(35, '$2y$10$rQq2j2zB3pJkU8PRDu5r3O9yrI5syRgOWNFEc4IJX92jAxEb0NKJi', '08027563905', 'anomkfueme@yahoo.com', 'anomfueme', 'anomfueme', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'k6QjpShgv8C2h3VSWzOjpnhI2Veuw9l6hAKSpYtz', NULL, '2016-05-10 19:44:56', '2016-05-10 19:44:56'),
(36, '$2y$10$8BpkSKRmSDmQ6nCmjvDwEOs29JLBUW69pChelaO1D0PloMqWo12dy', '08052900879', 'ayanleke@yahoo.com', 'ayanleke', 'ayanleke', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, '3b9DrSNrPtOqJQi9sE1syUXa90Dj6ZhTi2KsS07h', NULL, '2016-05-10 19:45:53', '2016-05-10 19:45:53'),
(37, '$2y$10$jTWopfaPWBZNWScQ/gmpFuHTy46WICNXnRSUhIKnfddX9Xi34feuW', '08025224666', 'ayeni@yahoo.com', 'ayeni', 'ayeni', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'xv5NI55Ac1lUgCwfjg98hwITrYCKZCjVBp3KKrNU', NULL, '2016-05-10 19:47:18', '2016-05-10 19:47:18'),
(38, '$2y$10$yvfrq7C3S8EaIrrvCVcXm.Q/VvjmpsjUKtr5qYLuuCEsxbGnj7VGK', '08096473502', 'badmus@yahoo.com', 'badmus', 'badmus', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'lof3V3KVjZZMHYnJIgM0YTNXW1oO4O5mkowegS96', NULL, '2016-05-10 19:49:14', '2016-05-10 19:49:14'),
(39, '$2y$10$ql3eCFR2s85XmkmQCc5b0.bbKCuSaGDWQrgn8fE/2h.xIrFEyW8qu', '08039268308', 'bakare@yahoo.com', 'bakare', 'bakare', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, '49PBJZUvyduz6vanCscWObn8L1jfq0vEl4pEKqej', NULL, '2016-05-10 19:50:00', '2016-05-10 19:50:00'),
(40, '$2y$10$kcGSAt7BrFHaxqMhRVBSueHhXTc.JVMyZE6K6Vu5zN4hS68MdN0b6', '08132188828', 'bashorun@yahoo.com', 'bashorun', 'bashorun', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, '3WFgSvYPvV163r7YOBtron6ytFbKaOD0GF4bH01Y', NULL, '2016-05-10 19:50:57', '2016-05-10 19:50:57'),
(41, '$2y$10$PhweNSMhK90zrSJ7MP27sucJAKA6sTpu465lt1RrCCLnZvdNC.RvC', '08034813126', 'bello@yahoo.com', 'bello', 'bello', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'A2DBJVt31zPmVY4UwY2HLzViDa1nrtndzHy2LTAO', NULL, '2016-05-10 19:51:47', '2016-05-10 19:51:47'),
(42, '$2y$10$QQF0WWbNYAgN1XIlS1rC/OFT8MdY/4tHvO367G1t7aOj0kewNohiW', '07083940215', 'biobaku@yahoo.com', 'biobaku', 'biobaku', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'rW7TOBT67LeMH7SCWHNdVd5KpvXxjERQiJd5BPw0', NULL, '2016-05-10 19:52:40', '2016-05-10 19:52:40'),
(43, '$2y$10$ckC.qCqdZJzZgfL.ByGpVueDrHv.5N6wTVPQFiA8J9x4AAYXlQaK6', '08172677798', 'brain@yahoo.com', 'brain', 'brain', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'qUb4UwWcrYbHRusZG79HIYXqsW83Yq1lUJxKJhiM', NULL, '2016-05-10 19:53:49', '2016-05-10 19:53:49'),
(44, '$2y$10$qp2XtyrQBQsTDSi9YoXtKOdIzZpVN0eWBwyhy.vYF8jL1a23jq4qW', '07063617473', 'bruce@yahoo.com', 'bruce', 'bruce', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'V8QuL3PG1GJaTIPuaZoG8lLXg3M5coX9hIIyouKI', NULL, '2016-05-10 19:55:16', '2016-05-10 19:55:16'),
(45, '$2y$10$DP1dEwtOnF.XMQb9GMBOougSh9v6o6lrmZaNLvjWC35q9myLm8t4i', '08034296363', 'chidiebere@yahoo.com', 'chidiebere', 'chidiebere', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'zjnGBFkgdsCsqVLWQqw63PErBhqnHcfBHlwJDnyJ', NULL, '2016-05-10 20:25:39', '2016-05-10 20:25:39'),
(46, '$2y$10$5ukDHYe12ODSN3HQgQop3.WncXbm3Nwt3mhKka2sG3wHR0Wi1UM2G', '07083207650', 'chima@yahoo.com', 'chima', 'chima', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'e4Y28mn2re0Yt5eeNjBAldR0Y5oZ8cvbDRPiQfU2', NULL, '2016-05-10 20:26:32', '2016-05-10 20:26:32'),
(47, '$2y$10$xB98h9pIi.h1mlbNHjPQSeideB/gKc46jbkx7/KLTDcE5iZxfLh5S', '08023285514', 'durojaiye@yahoo.com', 'durojaiye', 'durojaiye', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'jgDK9RyS0cIRCwCP7lzZCa288qtzqpQeBqiHPwUD', NULL, '2016-05-10 20:34:17', '2016-05-10 20:34:17'),
(48, '$2y$10$Wgouy2/T9v5IxU5uyyPL8OZTIVvn6hmlytEikeS6drQ/OeHP826/6', '08126274482', 'ebukanson@yahoo.com', 'ebukanson', 'ebukanson', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'qUqTcEpMpsAAQYk7SoIUJTCtv1WgaXE8qIVbLhqd', NULL, '2016-05-10 20:35:13', '2016-05-10 20:35:13'),
(49, '$2y$10$ZhQkomsaVc2A2VLwYFRp5Oe48XlOzqVnMeGm7WUybRu0pzSlkWXMm', '08033809777', 'echika@yahoo.com', 'echika', 'echika', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'khBBYujZ5pPiuQJrFprmkYb4sO0x5PfXGeuIkORZ', NULL, '2016-05-10 20:35:55', '2016-05-10 20:35:55'),
(50, '$2y$10$W4K8cP4l.l8HOX3yhM6EZOw4satUWhka3R57vYSUyAB35aC1sFWOW', '08033142304', 'edeh@yahoo.com', 'edeh', 'edeh', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, '4hFcwgwhtC8vwvFPSjLRWQDQ9GQ9kC4WAST6MYwy', NULL, '2016-05-10 20:36:38', '2016-05-10 20:36:38'),
(51, '$2y$10$pF6QOxqTB7J/w9AyppMVFOSNUVV.0Q.OY4quNkxuNkIKPRClxt8oe', '08073144124', 'ediae@yahoo.com', 'ediae', 'ediae', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'Gae0BO0nzd5f2Ehj3KnvGOBCR0gLXgjjlsZDfKyq', NULL, '2016-05-10 20:37:37', '2016-05-10 20:37:37'),
(52, '$2y$10$qyBxG.7NX32BlG7U8h0y7OIAlzhkZcPZIbHiR2vxhlFHhcVdmdy32', '08133380216', 'edward@yahoo.com', 'edward', 'edward', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, '7t4whVIXO7dZdJyQKTTnTu2FqDHT2qkJn7DUFH2B', NULL, '2016-05-10 20:38:25', '2016-05-10 20:38:25'),
(53, '$2y$10$d7ljeq19C.2CVOfBCCr3nuAECKB1Txk8yit5NcK.ez3TdYdP7dpMm', '08056078540', 'efurhievwe@yahoo.com', 'efurhievwe', 'efurhievwe', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'm5ofdmU3icPkqyUxoFlbTjMfgxS0uL31yuAKO0At', NULL, '2016-05-10 20:40:00', '2016-05-10 20:40:00'),
(54, '$2y$10$D5sAmPuRd.INbeYbFHbSQO.8Oazamo0g.sQBn8FrMRswz8kPg02PO', '08057356449', 'ejeh@yahoo.com', 'ejeh', 'ejeh', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'LsSLsgSo7ROLXfKq4v5FwzIWGOfPGUpbK92sBCqC', NULL, '2016-05-10 20:42:24', '2016-05-10 20:42:24'),
(55, '$2y$10$RCtsJRjW/C5gPsXb2wL5tebyPUYqhW5RDeGHrjH0bE1D5UFzVi9fG', '08094410200', 'ekaIdara@yahoo.com', 'eka - Idara', 'eka - Idara', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, '6dBZEOoPcodWmKwz0K1tgon7s1OLFpl0WkzkEixV', NULL, '2016-05-10 20:43:34', '2016-05-10 20:43:34'),
(56, '$2y$10$0oe.sOh1fHivHMZpb8VMQeQXeaFGOYRAuMdHfOEbKCHIfxtXbaeRi', '08033717878', 'ekoh@yahoo.com', 'ekoh', 'ekoh', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'S1UzLule8v5OWyS0Hjz2PRqdVndrlsKLMMzyrrJH', NULL, '2016-05-10 20:47:13', '2016-05-10 20:47:13'),
(57, '$2y$10$9oVxmSPf.gh3nwJuZWVASu2ZyQ39IVu3JsjUOEZK/t5AE/19qEYZG', '080223425178', 'emissiri@yahoo.com', 'emissiri', 'emissiri', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'SWcFgLU4tpJw3DMHkQTzLwPCVE65KKhywFtwAPky', NULL, '2016-05-10 20:48:05', '2016-05-10 20:48:05'),
(58, '$2y$10$m.pjNtzG9d4JBK5AfO0v8eSdGn2BaMOceU2Jg8iiZ98Nu0Qk8aciW', '08035832784', 'emina@yahoo.com', 'emina', 'emina', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'Z5o2zl1k2ZACMVQq4NG16r4Wsw3Urk0pBszz7jJj', NULL, '2016-05-10 20:51:01', '2016-05-10 20:51:01'),
(59, '$2y$10$SsmzDvx5P12MqR5LmGWJfOhdP17y31tEdQpBOqq3RsJc/75D/40Fa', '07086722363', 'enifeni@yahoo.com', 'enifeni', 'enifeni', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, '3d0dqPKzdDtvwHqSNnIiurgTzZamiFEo7T4SiZy0', NULL, '2016-05-10 20:51:47', '2016-05-10 20:51:47'),
(60, '$2y$10$2ZPjTUNby7Cw8WRaFrm1be42wF4vIAq4g/FuIU7zfH3KCXn2M/voe', '08033607182', 'enugo@yahoo.com', 'enugo', 'enugo', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'Tfhh6UbZHRf00b27eTkbfsM3YXmIRJ8mY0ksRktk', NULL, '2016-05-10 20:52:57', '2016-05-10 20:52:57'),
(61, '$2y$10$Iwa0cRYYB5bqRP6k6rnAMeCYIkApwx3zuoty.LvXY3/Eckk7rNG2e', '08037746392', 'eyoita@yahoo.com', 'eyo - ita', 'eyo - ita', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'FIbNeX9T1LxSHl4jSyMc1JHdOZMR1RYAY8BpdJ1e', NULL, '2016-05-10 20:53:56', '2016-05-10 20:53:56'),
(62, '$2y$10$hFPbPodAc16lP0nQfB6jTen17BCeLO4eUVjjPjdsXkmO4zzEemtOq', '08023656161', 'ezechinyere@yahoo.com', 'ezechinyere', 'ezechinyere', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'ZSLXnQrFSLtarQQN75tjnhLGKbLyWajwsaOX0vnJ', NULL, '2016-05-10 20:55:21', '2016-05-10 20:55:21'),
(63, '$2y$10$a9V5hvnwJtVngL13DPD01ubIyoUKWspbz7MaPMTnZ.9MhoXOo0v3a', '08052519669', 'ezelie@yahoo.com', 'ezelie', 'ezelie', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, '5zjXL1XBHQ2uw508NDKeoqQA6d58WCpgxxhUbsuj', NULL, '2016-05-10 20:56:18', '2016-05-10 20:56:18'),
(64, '$2y$10$IKWLQ9u7qebUFa8fqJkeIeUmfBFVG3cN4DF.VabLkh3kikGPnyAj6', '08146228602', 'fawale@yahoo.com', 'fawale', 'fawale', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'dpfOQe1GGOfmAKmk4xsjb12lFUr3n1Oe4xDiTA7x', NULL, '2016-05-10 20:57:41', '2016-05-10 20:57:41'),
(65, '$2y$10$JoYRvIbWX1zSXYzMhV2Mm.9zOidTFQP6dvu.Pk8KGU2zA4EdjduHK', '08029184841', 'fayemiwo@yahoo.com', 'fayemiwo', 'fayemiwo', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'SkfLbeToScPk5eRTkAw6A34R1akkEYI9FSj967bN', NULL, '2016-05-10 20:58:34', '2016-05-10 20:58:34'),
(66, '$2y$10$jHDLhhUQ32NTxJm/YSstUO7gz220leRxMc2ING50o1kVbhT/22DgS', '08033005954', 'gbadamosi@yahoo.com', 'gbadamosi', 'gbadamosi', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'vqY9UjzBNVN970mD0giGzkSB2AYtZxASuDqXrqIt', NULL, '2016-05-10 20:59:40', '2016-05-10 20:59:40'),
(67, '$2y$10$QysusTE6lbxBcuZ7/an69uiDCHJi4TRiFhLCgfQKKd50NK0PAP6x2', '08066669355', 'george@yahoo.com', 'george', 'george', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'JSB4UbHynn9HLUTZtouglImPZwtv1FWXqAQJhhiF', NULL, '2016-05-10 21:00:38', '2016-05-10 21:00:38'),
(68, '$2y$10$EbRPRr6k7agvz/LwABDlHext4zRnEJUNWIR.P5nXdOpFSuBgzq4/2', '08036147829', 'george2@yahoo.com', 'george', 'george', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, '77e9haqMYHv2lu05V5lBwc2Yz572Y88KbAymYAsW', NULL, '2016-05-10 21:02:05', '2016-05-10 21:02:05'),
(69, '$2y$10$vZnah5noa7rINQCsRMWaAOlvEgQCKSODLMCouW.mOfeeFqLkgOwHC', '08187212908', 'hundeyin@yahoo.com', 'hundeyin', 'hundeyin', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'RERZgbNsXhlyM7WNvQ367pQpQaU1ehyZlpJJABau', NULL, '2016-05-10 21:03:46', '2016-05-10 21:03:46'),
(70, '$2y$10$.dqY0bkizxlnpbBYBbrc6OvDyMdkLhwljYXlrBxzBL4jAr/6UelpG', '08138241771', 'ibiam@yahoo.com', 'ibiam', 'ibiam', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'POLFCnzHNG4qxEvgLExY0svr3fDmVaJnj7iU9SRN', NULL, '2016-05-10 21:05:09', '2016-05-10 21:05:09'),
(71, '$2y$10$esH08gF0915EQWf7KzRGXuUZi5bYs.TBJKc7IauTCG.V.MbGAUvBC', '08034720645', 'idahosa@yahoo.com', 'idahosa', 'idahosa', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'VmVnWXMWOtK0iTwOsdRrYLT8rxhxei3YQTQnapRz', NULL, '2016-05-10 21:06:07', '2016-05-10 21:06:07'),
(72, '$2y$10$xY0ftJPdgpm3oOwPOBAv.e2jeGEK.3d.snETxPmGQvBErZB.hgC6C', '08036739601', 'ifedayoadesida@yahoo.com', 'ifedayo', 'adesida', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'vvmJzzqs3GytTq4QUTzGeBF75Cei8LtUFvnv11Wg', NULL, '2016-05-10 21:07:40', '2016-05-10 21:07:40'),
(73, '$2y$10$T85f487R2du0rm3CzXYg6udLAmt9eiDLaZYgIWpNYjAwph.Zsf5ja', '08028317642', 'igure@yahoo.com', 'igure', 'igure', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'fSj7BCxE5NRoP9eMHIkBBNgUuiz9WIIexXoi3C0p', NULL, '2016-05-10 21:10:26', '2016-05-10 21:10:26'),
(74, '$2y$10$9pb/leHA3ehRg1pueFpmAewxB/1ho9Zif8gKvJFAGiVM4undoPF/.', '08055679974', 'igweonwu@yahoo.com', 'igweonwu', 'igweonwu', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'vXFSjGTydHAyTFMMYwcwRP1gqym8flzXsWc8i3Gk', NULL, '2016-05-10 21:11:21', '2016-05-10 21:11:21'),
(75, '$2y$10$F75owQiktNMMXrXdp2xRl.KbzbPnRCyogsxgMWvD0aT54/6BBmydm', '07066162027', 'iheoma@yahoo.com', 'iheoma', 'iheoma', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'lgLdeNmp6RfOPDyj2UjcNo1Hnls77xASMzp3jjW3', NULL, '2016-05-10 21:13:53', '2016-05-10 21:13:53'),
(76, '$2y$10$Lsym346pUquXjlUaSdVwA.qR63fR.xTOtiMtSYAuy/34CeQtPut6.', '08165474936', 'ita2@yahoo.com', 'ita', 'ita', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'adlxxQnevd4SMGKrOfAr9iieYmCzY9nQETiKD5j6', NULL, '2016-05-10 21:15:13', '2016-05-10 21:15:13'),
(77, '$2y$10$NYo.LZyeeBPK7m3DLm3Gr.KE9R9R8dsa.xlKAF0cWWAtT4EWCwKnK', '08023002650', 'komolafe@yahoo.com', 'komolafe', 'komolafe', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'UM43G82UjylzNcoDbCcB3Fq9vFjO09Ihf7DFrmcG', NULL, '2016-05-10 21:18:48', '2016-05-10 21:18:48'),
(78, '$2y$10$Cxq67yMz/UOLRZrgaLKEju3vjpC2e2PBdqYXD02OAajctnBf76Tn2', '07032096056', 'lewis@yahoo.com', 'lewis', 'lewis', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'jwv5HisFhdY94QFsTSbzr8xJgaC21bwSwo6jQVK2', NULL, '2016-05-10 21:19:57', '2016-05-10 21:19:57'),
(79, '$2y$10$mXtUW1DL4r2CCohhsIvLT.c4RmDXrbbubxxW9sdJiCuaROgLlC5tC', '08167550222', 'mabinuori@yahoo.com', 'mabinuori', 'mabinuori', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'MomoFIx3tbKjzGlInEUBS2vgJx9VnWXnH9VLVYyw', NULL, '2016-05-10 21:21:16', '2016-05-10 21:21:16'),
(80, '$2y$10$afSKfOl/vqexeDIhsGV6vO0ym3ArjfUL8kBk.NPfmthSMHLfsAM3y', '08063977980', 'madu@yahoo.com', 'madu', 'madu', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'CRUUN94B7Norh7MvflRmandjTbeIGzZn1mfPg6Nf', NULL, '2016-05-10 21:22:07', '2016-05-10 21:22:07'),
(81, '$2y$10$XpakwwBkQFsBMnmNAi7Z3.k8Ha2SS9Nz/7KAyJnzaX1ZN0hgWyexK', '08038024291', 'mmadu@yahoo.com', 'mmadu', 'mmadu', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'hibJgGKd4ZDBzyUXZHLX7vt0YXxvxeUU9c7SBkDS', NULL, '2016-05-10 21:23:09', '2016-05-10 21:23:09'),
(82, '$2y$10$zPRR.ipwWcrE0ddcXBOZSOtB5yY5tkRsDkaTEPA1TFHFVrVWgxK9i', '08033776967', 'nnaji@yahoo.com', 'nnaji', 'nnaji', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'A5DA56uQNcr2Mtm32D4IalX85DScp9hC1O48EDT4', NULL, '2016-05-10 21:23:55', '2016-05-10 21:23:55'),
(83, '$2y$10$1ToqVcVtu56CS27LvXGZRODc8iiS0cOhfTs8OcctWwkVt9xt4EKEC', '09098837414', 'nwachukwu@yahoo.com', 'nwachukwu', 'nwachukwu', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'xXX6BoNhTLoTvYCekcz4XWnX18GHhAPmVHPuU4JX', NULL, '2016-05-10 21:24:50', '2016-05-10 21:24:50'),
(84, '$2y$10$HSVOCw35bKO0/jbP0bmvxOQcqvSze4PPZ/p1YuSdNUwxLNiDYXCzu', '08035747450', 'nwokedi@yahoo.com', 'nwokedi', 'nwokedi', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'Nub23jEA57quQtXA8NrjZpMP6zcQpT7Jtng3hxaJ', NULL, '2016-05-10 21:25:33', '2016-05-10 21:25:33'),
(85, '$2y$10$yR3N.s1FuOJ4gNgHeqUqFOWcJqOYc8JuwEI5PGzWieiKSdbgvNvBO', '08035034468', 'nwoko@yahoo.com', 'nwoko', 'nwoko', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, '4UQ9KGagHFvX6svhvUCfdi2rKOW6TBx6w2cjsfJc', NULL, '2016-05-10 21:26:14', '2016-05-10 21:26:14'),
(86, '$2y$10$WRS6Nx9me1/vIb18Qog6D.lOvkxuBKtP0a94GUulpD6NCvNBN40FS', '08037166828', 'nwokolo@yahoo.com', 'nwokolo', 'nwokolo', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'LNIrhXe5JviimpWGwno5BJMv6Gey6VTjNMTUUJjY', NULL, '2016-05-10 21:26:58', '2016-05-10 21:26:58'),
(87, '$2y$10$Nbrv9yyfe1DQfppBYy9QAuv1QWxZtcBEyUMCPr4I7m3/DV/DbNP8S', '08109588383', 'obi@yahoo.com', 'obi', 'obi', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'fAbxjpSxkNFTaI93ETPlW7LeoAdyHuYBT4dWAP8T', NULL, '2016-05-10 21:27:47', '2016-05-10 21:27:47'),
(88, '$2y$10$034w0cXpEXdxx4OHXS6RfervxXn2bOrcwdyeKL1b547KAtLTZ75Wu', '08037143393', 'obialo@yahoo.com', 'obialo', 'obialo', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'M6iaN4GfYXctTrRjvMnlERGjUDuvMdahmsB97tJF', NULL, '2016-05-10 21:28:24', '2016-05-10 21:28:24'),
(89, '$2y$10$tURfIshQ7E1ka39L.BHG9efO7G9dxlBatWdFvoNDMVsH168BAs9e.', '07033507011', 'oboh@yahoo.com', 'oboh', 'oboh', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'mrpKWz0VvG75QNy8hMi4mYgfo7LKA312CzKDlWU6', NULL, '2016-05-10 21:29:05', '2016-05-10 21:29:05'),
(90, '$2y$10$QSdgZ1rf5BhuPw.XbaXgzu1RC8LCMwpvhFKT4Br2sv.4nJZhKmtMi', '08064428179', 'odubanjo@yahoo.com', 'odubanjo', 'odubanjo', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'mr4APScaNyWhGHJwocPjxTDpe7VqTKPy86lPDqSK', NULL, '2016-05-10 21:29:54', '2016-05-10 21:29:54'),
(91, '$2y$10$Bt2fnterlvSOXRANihB5pOwzxeSFs0b.Dr1LUY/W9mPGV9NxjEile', '08033040413', 'ogale@yahoo.com', 'ogale', 'ogale', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, '4wGxEGsOTGVek0MkhajsMY7P38IdUUl6JD6zXP7I', NULL, '2016-05-10 21:30:37', '2016-05-10 21:30:37'),
(92, '$2y$10$0vBArfICPrbz.h.OKvpdzuW/IFxUwebUlpgtRT/9.3k47x3VNKnCq', '08034166664', 'ogunleye@yahoo.com', 'ogunleye', 'ogunleye', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'ZseLgmMne0o4e1ux7RyiH80hsxPdwSQaTYR1pg56', NULL, '2016-05-10 21:31:26', '2016-05-10 21:31:26'),
(93, '$2y$10$PJZslNw3Ggp0fPzgfdPCAO65v5lzwE9S.0HxqX.ZxUsc0MddpRiU6', '08131081768', 'ogunmuko@yahoo.com', 'ogunmuko', 'ogunmuko', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'kCqBSLrd5Zjg6sW4z4QaW0Fff5E8xsf0pOy0kY4n', NULL, '2016-05-10 21:32:25', '2016-05-10 21:32:25'),
(94, '$2y$10$obNKrxuLo0Qvt3qF9R.ZSOMkhwr7C3CtmCJ1wOlCTbwyZ3NGlxofu', '08131964766', 'ojopoke@yahoo.com', 'ojopoke', 'ojopoke', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'mpzhsjT334G1utH9CIDpzV0d2OleADL09dlv9Her', NULL, '2016-05-10 21:33:29', '2016-05-10 21:33:29'),
(95, '$2y$10$wM/fYuLo3GzgzMHt4jFTWefGUk.QS3QwzcWVLhct4.b3ZKCYd2Yz2', '08032235812', 'okoye@yahoo.com', 'okoye', 'okoye', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'WrTNoCx1goU29Ty6oRpEvLigdJBfqE0fZHTM2sOn', NULL, '2016-05-10 21:34:13', '2016-05-10 21:34:13'),
(96, '$2y$10$MCCRd0.LYhttAbaD6/HsDukBclgjUa/qw/.s6wXimASYggQxM407C', '08023180841', 'okpor@yahoo.com', 'okpor', 'okpor', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'hJrtzyVjGSbhpbbmDbpiYsW1JGjfs13T1lW44B10', NULL, '2016-05-10 21:35:16', '2016-05-10 21:35:16'),
(97, '$2y$10$m/7SO5VCIUsZcx7A4zsED.vlhKuIiUrCA72Yb5zYPPF9C1UI6eb4e', '08023388331', 'okunola@yahoo.com', 'okunola', 'okunola', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'ANjJmznLrz2gF5urPXrRw4Wwmxm5MvMlUO9BhAlV', NULL, '2016-05-10 21:36:00', '2016-05-10 21:36:00'),
(98, '$2y$10$Ah1KsPN.zF/jD.ERJaTVYu2B9..ek.GIBzyviTF6tZDdztaKpR1RG', '08096742166', 'oladele@yahoo.com', 'oladele', 'oladele', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'Q1CIRvfnBtlUdAk0hE4n2CqZK8KhrRxPAxLtl4Ww', NULL, '2016-05-10 21:36:45', '2016-05-10 21:36:45'),
(99, '$2y$10$ulv1vRUM86q31wddB95FkOP.s/T2itRZjxOXBc6PbfK5sZJy3YfCS', '08037158695', 'oloye@yahoo.com', 'oloye', 'oloye', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'f2ZN6BnmQjoanoeUYh25zzVPQoCdTRUHHYYk1hRu', NULL, '2016-05-10 21:37:31', '2016-05-10 21:37:31'),
(100, '$2y$10$vs8jMJifrQ.H9XUaL2N2i.qo0FSUndfwBkwxuXSm75JjTZur083Ca', '07062058069', 'olugbemi@yahoo.com', 'olugbemi', 'olugbemi', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, '30hOWgyTlP6zbgvNffMLU5b8x5KUCi7qLmM14cIH', NULL, '2016-05-10 21:38:22', '2016-05-10 21:38:22'),
(101, '$2y$10$cnELvjRsjuCTPvbr5jbbYeQlzq.yPUyxqUCEDyQhFP.216TgxyJMu', '08038277868', 'oluwamola@yahoo.com', 'oluwamola', 'oluwamola', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'EqGpTFUyxqu4ShcMoPIpv0xd6zuRkT07MiaPBvz7', NULL, '2016-05-10 21:39:19', '2016-05-10 21:39:19'),
(102, '$2y$10$3biRDHGpctFP/5BzFiIEYOPaDpvycUa.JcB.b1WB3b4wDB018QvrS', '08028343733', 'omotosho@yahoo.com', 'omotosho', 'omotosho', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'I4JRvK4hjIPZdxMwRtiDVxudB8YOLLNiXNW9m4Y2', NULL, '2016-05-10 21:40:10', '2016-05-10 21:40:10'),
(103, '$2y$10$IRxifIL/6T282LSe0xslueXOadGrTT/6H2FK6SxfXu.Kx1.CFLQJm', '08038182294', 'omotosho2@yahoo.com', 'omotosho', 'omotosho', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'cNfxgq0VGCixpld2lnH4i1Ty4KfRGws6KFUvcUN9', NULL, '2016-05-10 21:42:08', '2016-05-10 21:42:08'),
(104, '$2y$10$p9f.cWLK/MbSBPZGxUEfrumiarCgkVlOFH5G8Z5j7hZWX1KGlhe2a', '08034486444', 'onuisile@yahoo.com', 'onisile', 'onisile', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, '81uvddfKtf0T5w2bGsFreDaN2nNArGE5i2HHnBvU', NULL, '2016-05-10 21:44:29', '2016-05-10 21:44:29'),
(105, '$2y$10$fZodq0NSjz3lxbYwBxN7QeNgkhvKt/lgQ9BtzNYoOW4QjTsjLhzDS', '08038071133', 'onyemaechi@yahoo.com', 'onyemaechi', 'onyemaechi', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'ELWUGgCkNKBWfxDC4PbvPWi2an8oWQ42EOUncnpy', NULL, '2016-05-10 21:45:22', '2016-05-10 21:45:22'),
(106, '$2y$10$kobFC86aIeaIixUoGXn01e6yyAZ/GdRz2WcFp3CqIO/suc5eCl.46', '08023324754', 'orisunmibare@yahoo.com', 'orisunmibare', 'orisunmibare', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'pE6Xm0FqxuhNRlzk1L8xrkSYLmv3yTNZU7P6g7si', NULL, '2016-05-10 21:46:05', '2016-05-10 21:46:05'),
(107, '$2y$10$BxSU.Awvy3V0pZLKYBdazegLA.Baad3Pmy8Xi080Y1nwu6LkY.xnW', '08024791052', 'osariemen@yahoo.com', 'osariemen', 'osariemen', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'okMHN1fJVnB766aRzD16XazOn4DfwhGzAoLoF3p8', NULL, '2016-05-10 21:47:25', '2016-05-10 21:47:25'),
(108, '$2y$10$iow7IWkhLFW11f12F0lTgOfpmL/Ph/oASRdXDZ9PmFFrMePnllYAW', '08022315335', 'oshinonwu@yahoo.com', 'oshinonwu', 'oshinonwu', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'vYeq2bXgEacKnmdUT0W3ZeEeA35S8QqyFLM4GXiP', NULL, '2016-05-10 21:48:33', '2016-05-10 21:48:33'),
(109, '$2y$10$vsCUD81G.ElTVKAP3ioeg.qRWbALH1EJoB6te0vsNYQrJQCx0SayW', '08026952105', 'oyewunmi@yahoo.com', 'oyewunmi', 'oyewunmi', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'vew419QwTwTTxuK2IdVRrTJIAanDkxIqMXcZT3P7', NULL, '2016-05-10 21:49:39', '2016-05-10 21:49:39'),
(110, '$2y$10$2T6WLdiIGG23e3owNgfMxOdOIZKrVB8r9bqTy2jSLu0x0TyETgPhW', '08062061635', 'rahmon@yahoo.com', 'rahmon', 'rahmon', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'bB06ZyxbpCyCMdZxMqdN04HMBVnkrXOO1wwFpr1Y', NULL, '2016-05-10 21:50:30', '2016-05-10 21:50:30'),
(111, '$2y$10$9jqLLPY8mEnr6c71ejfMkObDq3UsjzB0aS.AmWulDCoo3he6xBuYO', '08057564138', 'sanusi@yahoo.com', 'sanusi', 'sanusi', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, '78PlzgmXUBVtEpjbQQcUUQZs39j0oi9pCUxPMlVk', NULL, '2016-05-10 21:51:12', '2016-05-10 21:51:12'),
(112, '$2y$10$YKL9IhtC84cnxOQXnn/ZP.crbaipX.7EvM7jI0cCgUKK9gl9rbvAK', '08034905466', 'shittu@yahoo.com', 'shittu', 'shittu', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'DM5GY8Kv9KOHriZlIlKW6bSVxHoEkvQlDkS0nXta', NULL, '2016-05-10 21:51:58', '2016-05-10 21:51:58'),
(113, '$2y$10$wo0m1..N1ek27j2cVUsdpeZJEZj1LJ/Sd4fOuro6kn.hKgFdUrZ2u', '08149095219', 'shotolu@yahoo.com', 'shotolu', 'shotolu', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'S7k4nvCEAnWwIo6OMsUivb3L5iCZKOWXDbOruNwM', NULL, '2016-05-10 21:52:57', '2016-05-10 21:52:57'),
(114, '$2y$10$iePQAszmX8CgoJlC7rPJLu.4GoExn6VDbzptRTQuAVvCnwDcCJcOO', '07064484612', 'tamunokubie@yahoo.com', 'tamunokubie', 'tamunokubie', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, '9k2aOQAAagCtbO86vVp1g19Vii0IVC5VVALtmwTS', NULL, '2016-05-10 21:54:00', '2016-05-10 21:54:00'),
(115, '$2y$10$5KRaqzDxxpnrQYZVdr.zLONqLsbam20UqzubMrwVhEWr/EkRbaLyi', '08023068758', 'uba@yahoo.com', 'uba', 'uba', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'e7ljS1H9iNSAAq65IpOQaq7RoR04JNIa1x3oMpH7', NULL, '2016-05-10 21:54:46', '2016-05-10 21:54:46'),
(116, '$2y$10$bFhrlo3THpxlm1OaxYsZdOWxZKoeLVvV7VF.nvoJZ6w/Upn3bjEE.', '08030612120', 'ubah@yahoo.com', 'ubah', 'ubah', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, '2SO3D3XjKW6ptnNq4HosflkVYAkE9AOfk1Vt8hD9', NULL, '2016-05-10 21:55:51', '2016-05-10 21:55:51'),
(117, '$2y$10$jutfWA1dK4HIFO6wQ8r1V.G2HZ5pS/hM1cvcVFEakXrPX5b5Xx672', '08132321240', 'udemgba@yahoo.com', 'udemgba', 'udemgba', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'OOhu19mmZoQQn3H92hB91srj1jEcKLzPE0w2crEH', NULL, '2016-05-10 21:57:11', '2016-05-10 21:57:11'),
(118, '$2y$10$Me.kHoPOaxYLLDVb17HExeTZvf8pdPCYLwMqodrSJ3PfkI4Q6nSyi', '08037207868', 'udensi@yahoo.com', 'udensi', 'udensi', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'VzeEqfJS3NJU9ceMXDmP1lxdzLKuabQKjbrxGOsA', NULL, '2016-05-10 21:58:06', '2016-05-10 21:58:06'),
(119, '$2y$10$H/QJfNMJIEFGONs6vSR0/eJO34ahWABVQYM/kDK1gTtyLJfzkpcZa', '08032007206', 'umoh@yahoo.com', 'umoh', 'umoh', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, '1aE5zy8qppj0bTym67IRbnx1cIRZqcOGIlHxPO6u', NULL, '2016-05-10 21:58:52', '2016-05-10 21:58:52'),
(120, '$2y$10$YGGwwLlc7IfX5zvLFuSrfOPZ28dpvwl8K9V0vAi1xXsXublfDVQjW', '08037090486', 'woko@yaho.com', 'woko', 'woko', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, '08YV7FIuJQvQH3eP21onJM0Jqg6DEgZjjmXp5nbh', NULL, '2016-05-10 21:59:31', '2016-05-10 21:59:31'),
(121, '$2y$10$znjpgI3jhGeGCkWn3nPiluHHFbSsiLIwBI313XRQd4g1/BVAWzIk2', '08028676935', 'yusuf@yahoo.com', 'yusuf', 'yusuf', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, '85xGYILEtZVW5L9vhh09X5jUlSewy3opGOBAkiPF', NULL, '2016-05-10 22:00:16', '2016-05-10 22:00:16'),
(122, '$2y$10$1WQElj5amNIeFIsf6WIQ7.bLPS/8h2cGh.0SpqwyArSiG6X2dD8IC', '08136969411', 'akinwaletaiwo@yahoo.com', 'Akinwale', 'Taiwo', NULL, NULL, NULL, NULL, 4, NULL, NULL, 1, 1, NULL, 'L9SmifbnzA0q9gLAzRPXnLW8u0FkpblHKzoOYjJu', NULL, '2016-05-18 18:36:29', '2016-05-18 18:36:29'),
(123, '$2y$10$gCkX.1F71ET93FD9YDjoH.T.xjzk4U19ouUEQBqKE0QDbA3hXmShu', '08171344786', 'adenayasamson@yahoo.com', 'Adenaya', 'Samson', NULL, NULL, NULL, NULL, 4, NULL, NULL, 1, 1, NULL, 'FF1etPRq12Fz7Q6JPyQoeaIO8eepSORxodYzP5j4', NULL, '2016-05-18 18:39:13', '2016-05-18 18:39:13'),
(124, '$2y$10$VHtEZrUSJ3MurEgK7bD2neeaNNvhy3KpyJHfdOh0XqJLV1DQsZy0W', '08062101137', 'ogunlekeolufunke@yahoo.com', 'Ogunleke', 'Olufunke', NULL, NULL, NULL, NULL, 4, NULL, NULL, 1, 1, NULL, 'txTzSbMdu025aynsR6LqzChsOWAqsAZn5ulmUrOp', NULL, '2016-05-18 18:40:12', '2016-05-18 18:40:12'),
(125, '$2y$10$lkgkw77NXaKqnFSRJx51.ub5Y/XYDZIFAtyS0TAXSNNrJHVScxLke', '08039404007', 'princekehinde@yahoo.com', 'Famoroti', 'Kehinde', NULL, NULL, NULL, NULL, 4, NULL, NULL, 1, 1, NULL, 'hIGpSYzf0hM32ulRD8cWiJR43hyNxnLHHlCpFFsF', NULL, '2016-05-18 19:01:40', '2016-05-18 19:01:40');

--
-- Truncate table before insert `user_types`
--

TRUNCATE TABLE `user_types`;
--
-- Dumping data for table `user_types`
--

INSERT INTO `user_types` (`user_type_id`, `user_type`, `type`, `created_at`, `updated_at`) VALUES
(1, 'Developer', 1, NULL, NULL),
(2, 'Super Admin', 2, NULL, NULL),
(3, 'Sponsor', 2, '2016-04-28 21:35:55', '2016-04-28 21:35:55'),
(4, 'Staff', 2, '2016-04-28 21:35:15', '2016-04-28 21:35:15');
SET FOREIGN_KEY_CHECKS=1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
