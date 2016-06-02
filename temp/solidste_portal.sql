-- phpMyAdmin SQL Dump
-- version 4.0.10.14
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Jun 01, 2016 at 02:22 PM
-- Server version: 5.5.49-cll
-- PHP Version: 5.4.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `solidste_portal`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`solidsteps`@`localhost` PROCEDURE `sp_deleteSubjectClassRoom`(IN `subjectClassroomID` INT)
BEGIN
	-- Delete Assessment Details Corresponding to the subject_classroom_id in assessments
    DELETE FROM assessment_details WHERE assessment_id IN 
    (SELECT assessment_id FROM assessments WHERE subject_classroom_id = subjectClassroomID);
    
    -- Delete Assessments Corresponding to the subject_classroom_id
    DELETE FROM assessments WHERE subject_classroom_id = subjectClassroomID;
    
    -- Delete the subject the students registered Corresponding to the subject_classroom_id
    DELETE FROM student_subjects WHERE subject_classroom_id = subjectClassroomID;
    
    -- Delete the subject in the classroom Corresponding to the subject_classroom_id
    DELETE FROM subject_classrooms WHERE subject_classroom_id = subjectClassroomID;
END$$

CREATE DEFINER=`solidsteps`@`localhost` PROCEDURE `sp_modifyStudentsSubject`(IN `SubjectClassRoomID` INT, `StudentIDs` VARCHAR(225))
BEGIN
	#Create a Temporary Table to Hold The Values
	DROP TEMPORARY TABLE IF EXISTS StudentTemp;
	CREATE TEMPORARY TABLE IF NOT EXISTS StudentTemp
	(
		-- Add the column definitions for the TABLE variable here
		row_id int AUTO_INCREMENT,
		student_id INT, PRIMARY KEY (row_id)
	);

	IF StudentIDs IS NOT NULL THEN
		BEGIN
			DECLARE count INT Default 0 ;
			DECLARE student_id VARCHAR(255);
			simple_loop: LOOP
				SET count = count + 1;
				SET student_id = SPLIT_STR(StudentIDs, ',', count);
				IF student_id = '' THEN
					LEAVE simple_loop;
				END IF;
				# Insert into the attend details table those present
				INSERT INTO StudentTemp(student_id) SELECT student_id;
			END LOOP simple_loop;
		END;
	END IF;
    
    Block1: BEGIN
		-- Delete All the students that have been removed from the subjects
        DELETE FROM student_subjects WHERE subject_classroom_id=SubjectClassRoomID
        AND student_id NOT IN (SELECT student_id FROM StudentTemp);
        
        -- Insert the newly added students that are not in the list of students
        INSERT INTO student_subjects(subject_classroom_id, student_id)
        SELECT SubjectClassRoomID, student_id FROM StudentTemp 
        WHERE student_id NOT IN (SELECT student_id FROM student_subjects WHERE subject_classroom_id=SubjectClassRoomID);
    END Block1;
END$$

CREATE DEFINER=`solidsteps`@`localhost` PROCEDURE `sp_populateAssessmentDetail`(IN `AssessmentID` INT)
BEGIN
	SELECT subject_classroom_id, marked INTO @SCR_ID, @MarkStatus
	FROM assessments WHERE assessment_id=AssessmentID;

	# Check if the continuous assessment has not been marked
	-- IF @MarkStatus = 2 THEN
	BEGIN
		# Insert into the assessment details table the students students that registered the subjects
		INSERT INTO assessment_details(assessment_id, student_id)
			SELECT AssessmentID, student_id FROM student_subjects WHERE subject_classroom_id=@SCR_ID
			AND student_id NOT IN (SELECT student_id FROM assessment_details WHERE assessment_id=AssessmentID);

		# remove the students that was just removed from the list of students to offer the subject
		DELETE FROM assessment_details WHERE assessment_id=AssessmentID AND student_id NOT IN
		(SELECT student_id FROM student_subjects WHERE subject_classroom_id=@SCR_ID);
	END;
	-- END IF;
END$$

CREATE DEFINER=`solidsteps`@`localhost` PROCEDURE `sp_subject2Classlevels`(IN `LevelID` INT, `TermID` INT, `SubjectIDs` VARCHAR(225))
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
					CALL `sp_subject2Classrooms`(ClassID, TermID, SubjectIDs);
				END;
			END IF;
		UNTIL done1 END REPEAT;
		CLOSE cur1;
	END$$

CREATE DEFINER=`solidsteps`@`localhost` PROCEDURE `sp_subject2Classrooms`(IN `ClassID` INT, `TermID` INT, `SubjectIDs` VARCHAR(225))
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
									CALL sp_subject2Students(LAST_INSERT_ID());
								END;
							END IF;
						END;
					END IF;
				UNTIL done1 END REPEAT;
				CLOSE cur1;
			END Block2;
            
		END Block1;
	END$$

CREATE DEFINER=`solidsteps`@`localhost` PROCEDURE `sp_subject2Students`(IN `subjectClassroomID` INT)
BEGIN
		SELECT classroom_id, academic_term_id INTO @ClassID, @AcademicTermID
		FROM subject_classrooms WHERE subject_classroom_id=subjectClassroomID LIMIT 1;
		SET @SubjectClassroomID = subjectClassroomID;
		
        -- Check if the record exist in subjects students register
		SELECT COUNT(*) INTO @Exist FROM student_subjects WHERE subject_classroom_id = subjectClassroomID LIMIT 1;
		IF @Exist > 0 THEN
			BEGIN
				DELETE FROM student_subjects WHERE subject_classroom_id = subjectClassroomID;
			END;
		END IF;


		BEGIN
			-- Register the subjects to all the active students in the class room
			INSERT INTO student_subjects(student_id, subject_classroom_id)
			SELECT	b.student_id, @SubjectClassroomID
			FROM	students a INNER JOIN student_classes b ON a.student_id=b.student_id 
            INNER JOIN classrooms c ON c.classroom_id = b.classroom_id
			WHERE	b.classroom_id = @ClassID AND a.status_id = 1
			AND b.academic_year_id = (SELECT academic_year_id FROM academic_terms WHERE academic_term_id = @AcademicTermID LIMIT 1);
		END;
	END$$

CREATE DEFINER=`solidsteps`@`localhost` PROCEDURE `temp_student_subjects`()
BEGIN
	
Block2: BEGIN
	DECLARE done1 BOOLEAN DEFAULT FALSE;
	DECLARE ID INT;
	DECLARE cur1 CURSOR FOR SELECT subject_classroom_id FROM subject_classrooms;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done1 = TRUE;

#Open The Cursor For Iterating Through The Recordset cur1
	OPEN cur1;
	REPEAT
		FETCH cur1 INTO ID;
		IF NOT done1 THEN
			BEGIN
				CALL sp_subject2Students(ID);
				
			END;
		END IF;
	UNTIL done1 END REPEAT;
	CLOSE cur1;
END Block2;

END$$

--
-- Functions
--
CREATE DEFINER=`solidsteps`@`localhost` FUNCTION `SPLIT_STR`(
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

CREATE TABLE IF NOT EXISTS `academic_terms` (
  `academic_term_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`academic_term_id`),
  KEY `academic_terms_status_index` (`status`),
  KEY `academic_terms_academic_year_id_index` (`academic_year_id`),
  KEY `academic_terms_term_type_id_index` (`term_type_id`),
  KEY `academic_terms_exam_status_id_index` (`exam_status_id`),
  KEY `academic_terms_exam_setup_by_index` (`exam_setup_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `academic_terms`
--

INSERT INTO `academic_terms` (`academic_term_id`, `academic_term`, `status`, `academic_year_id`, `term_type_id`, `term_begins`, `term_ends`, `exam_status_id`, `exam_setup_by`, `exam_setup_date`, `created_at`, `updated_at`) VALUES
(1, 'Third Term 2015 - 2016', 1, 1, 3, '2016-04-15', '2016-07-15', 2, NULL, NULL, '2016-05-11 15:41:04', '2016-05-11 15:42:07');

-- --------------------------------------------------------

--
-- Table structure for table `academic_years`
--

CREATE TABLE IF NOT EXISTS `academic_years` (
  `academic_year_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `academic_year` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `status` int(10) unsigned NOT NULL DEFAULT '2',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`academic_year_id`),
  KEY `academic_years_status_index` (`status`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `academic_years`
--

INSERT INTO `academic_years` (`academic_year_id`, `academic_year`, `status`, `created_at`, `updated_at`) VALUES
(1, '2015 - 2016', 1, '2016-05-11 15:37:05', '2016-05-11 15:37:05');

-- --------------------------------------------------------

--
-- Table structure for table `assessments`
--

CREATE TABLE IF NOT EXISTS `assessments` (
  `assessment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `subject_classroom_id` int(10) unsigned NOT NULL,
  `assessment_setup_detail_id` int(10) unsigned NOT NULL,
  `marked` int(10) unsigned NOT NULL DEFAULT '2',
  PRIMARY KEY (`assessment_id`),
  KEY `assessments_subject_classroom_id_index` (`subject_classroom_id`),
  KEY `assessments_assessment_setup_detail_id_index` (`assessment_setup_detail_id`),
  KEY `assessments_marked_index` (`marked`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `assessment_details`
--

CREATE TABLE IF NOT EXISTS `assessment_details` (
  `assessment_detail_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` int(10) unsigned NOT NULL,
  `score` double(8,2) unsigned NOT NULL DEFAULT '0.00',
  `assessment_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`assessment_detail_id`),
  KEY `assessment_details_student_id_index` (`student_id`),
  KEY `assessment_details_assessment_id_index` (`assessment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `assessment_setups`
--

CREATE TABLE IF NOT EXISTS `assessment_setups` (
  `assessment_setup_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `assessment_no` tinyint(4) NOT NULL,
  `classgroup_id` int(10) unsigned NOT NULL,
  `academic_term_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`assessment_setup_id`),
  KEY `assessment_setups_classgroup_id_index` (`classgroup_id`),
  KEY `assessment_setups_academic_term_id_index` (`academic_term_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Dumping data for table `assessment_setups`
--

INSERT INTO `assessment_setups` (`assessment_setup_id`, `assessment_no`, `classgroup_id`, `academic_term_id`) VALUES
(3, 4, 1, 1),
(4, 4, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `assessment_setup_details`
--

CREATE TABLE IF NOT EXISTS `assessment_setup_details` (
  `assessment_setup_detail_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `number` tinyint(4) NOT NULL,
  `weight_point` double(8,2) unsigned NOT NULL,
  `percentage` int(10) unsigned NOT NULL,
  `assessment_setup_id` int(10) unsigned NOT NULL,
  `submission_date` date DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`assessment_setup_detail_id`),
  KEY `assessment_setup_details_assessment_setup_id_index` (`assessment_setup_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=9 ;

--
-- Dumping data for table `assessment_setup_details`
--

INSERT INTO `assessment_setup_details` (`assessment_setup_detail_id`, `number`, `weight_point`, `percentage`, `assessment_setup_id`, `submission_date`, `description`) VALUES
(1, 1, 10.00, 10, 3, '2016-07-15', 'first test'),
(2, 2, 20.00, 20, 3, '2016-07-15', 'mid-term test'),
(3, 3, 10.00, 10, 3, '2016-07-15', 'final test'),
(4, 4, 60.00, 60, 3, '2016-07-15', 'examination'),
(5, 1, 10.00, 10, 4, '2016-07-15', 'first test'),
(6, 2, 20.00, 20, 4, '2016-07-15', 'mid-term test'),
(7, 3, 10.00, 10, 4, '2016-07-15', 'final test'),
(8, 4, 60.00, 60, 4, '2016-07-15', 'examination');

-- --------------------------------------------------------

--
-- Table structure for table `classgroups`
--

CREATE TABLE IF NOT EXISTS `classgroups` (
  `classgroup_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `classgroup` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ca_weight_point` int(10) unsigned DEFAULT '0',
  `exam_weight_point` int(10) unsigned DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`classgroup_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `classgroups`
--

INSERT INTO `classgroups` (`classgroup_id`, `classgroup`, `ca_weight_point`, `exam_weight_point`, `created_at`, `updated_at`) VALUES
(1, 'Junior Secondary', 40, 60, '2016-05-11 15:50:22', '2016-05-19 17:13:20'),
(2, 'Senior Secondary', 40, 60, '2016-05-11 15:50:22', '2016-05-19 17:13:20');

-- --------------------------------------------------------

--
-- Table structure for table `classlevels`
--

CREATE TABLE IF NOT EXISTS `classlevels` (
  `classlevel_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `classlevel` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `classgroup_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`classlevel_id`),
  KEY `classlevels_classgroup_id_index` (`classgroup_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Dumping data for table `classlevels`
--

INSERT INTO `classlevels` (`classlevel_id`, `classlevel`, `classgroup_id`, `created_at`, `updated_at`) VALUES
(1, 'JSS 1', 1, '2016-05-11 15:52:27', '2016-05-11 15:52:27'),
(2, 'JSS 2', 1, '2016-05-11 15:52:27', '2016-05-11 15:52:27'),
(3, 'JSS 3', 1, '2016-05-11 15:52:27', '2016-05-11 15:52:27'),
(4, 'SS 1', 2, '2016-05-11 15:52:27', '2016-05-11 15:52:27');

-- --------------------------------------------------------

--
-- Table structure for table `classrooms`
--

CREATE TABLE IF NOT EXISTS `classrooms` (
  `classroom_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `classroom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `class_size` int(11) DEFAULT NULL,
  `class_status` int(10) unsigned NOT NULL DEFAULT '1',
  `classlevel_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`classroom_id`),
  KEY `classrooms_classlevel_id_index` (`classlevel_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

--
-- Dumping data for table `classrooms`
--

INSERT INTO `classrooms` (`classroom_id`, `classroom`, `class_size`, `class_status`, `classlevel_id`, `created_at`, `updated_at`) VALUES
(1, 'JSS 1 PEACE (A)', 9, 1, 1, '2016-05-11 15:59:09', '2016-05-13 15:23:53'),
(2, 'JSS 1 FAVOUR (B)', 9, 1, 1, '2016-05-11 15:59:09', '2016-05-13 15:23:54'),
(3, 'JSS 2 FAITH (A)', 12, 1, 2, '2016-05-11 15:59:09', '2016-05-13 15:23:54'),
(4, 'JSS 3 JOY (A)', 8, 1, 3, '2016-05-11 15:59:09', '2016-05-13 15:23:54'),
(5, 'SS 1 GOLD (A)', 6, 1, 4, '2016-05-13 15:23:54', '2016-05-13 15:23:54');

-- --------------------------------------------------------

--
-- Table structure for table `class_masters`
--

CREATE TABLE IF NOT EXISTS `class_masters` (
  `class_master_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `classroom_id` int(10) unsigned NOT NULL,
  `academic_year_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`class_master_id`),
  KEY `class_masters_user_id_index` (`user_id`),
  KEY `class_masters_classroom_id_index` (`classroom_id`),
  KEY `class_masters_academic_year_id_index` (`academic_year_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

CREATE TABLE IF NOT EXISTS `grades` (
  `grade_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `grade` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `grade_abbr` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `upper_bound` double(8,2) unsigned NOT NULL,
  `lower_bound` double(8,2) unsigned NOT NULL,
  `classgroup_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`grade_id`),
  KEY `grades_classgroup_id_index` (`classgroup_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=16 ;

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

-- --------------------------------------------------------

--
-- Table structure for table `menus`
--

CREATE TABLE IF NOT EXISTS `menus` (
  `menu_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `menu` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `menu_url` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `active` int(10) unsigned NOT NULL DEFAULT '1',
  `sequence` int(10) unsigned NOT NULL,
  `type` int(10) unsigned NOT NULL DEFAULT '1',
  `icon` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `menu_header_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`menu_id`),
  KEY `menus_menu_header_id_index` (`menu_header_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=9 ;

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

-- --------------------------------------------------------

--
-- Table structure for table `menu_headers`
--

CREATE TABLE IF NOT EXISTS `menu_headers` (
  `menu_header_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `menu_header` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `active` int(10) unsigned NOT NULL DEFAULT '1',
  `sequence` int(10) unsigned NOT NULL,
  `type` int(10) unsigned NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`menu_header_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Dumping data for table `menu_headers`
--

INSERT INTO `menu_headers` (`menu_header_id`, `menu_header`, `active`, `sequence`, `type`, `created_at`, `updated_at`) VALUES
(1, 'SETUPS', 1, 10, 1, '2016-03-29 22:30:39', '2016-03-30 19:33:06'),
(2, 'ACCOUNTS', 1, 9, 1, '2016-03-30 19:33:06', '2016-04-17 07:01:38'),
(3, 'RECORDS', 1, 8, 1, '2016-03-31 06:45:49', '2016-03-31 06:45:49'),
(4, 'PORTAL', 1, 1, 2, '2016-04-15 09:41:26', '2016-04-15 09:55:41');

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE IF NOT EXISTS `menu_items` (
  `menu_item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `menu_item` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `menu_item_url` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `menu_item_icon` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `active` int(10) unsigned NOT NULL DEFAULT '1',
  `sequence` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` int(10) unsigned NOT NULL DEFAULT '1',
  `menu_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`menu_item_id`),
  KEY `menu_items_menu_id_index` (`menu_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=23 ;

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
(19, 'ASSESSMENTS', '#', 'fa fa-briefcase', 1, '5', 1, 7, '2016-05-16 13:38:22', '2016-06-01 03:36:27'),
(20, 'GRADE GROUPING', '/grades', 'fa fa-check', 1, '6', 1, 7, '2016-05-19 03:54:12', '2016-05-19 03:54:12'),
(21, 'CREATE', '/students/create', 'fa fa-plus', 1, '1', 1, 8, '2016-05-23 14:17:34', '2016-05-23 14:40:11'),
(22, 'MANAGE', '/students', 'fa fa-list', 1, '2', 1, 8, '2016-05-23 14:19:07', '2016-05-23 14:19:07');

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
('2016_05_14_173333_create_subject_classes_table', 3),
('2016_05_17_184714_create_students_table', 4),
('2016_05_18_182321_create_grades_table', 4),
('2016_05_20_130901_create_assessments_tables', 5),
('2016_05_31_205123_create_class_masters_table', 6);

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=116 ;

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

-- --------------------------------------------------------

--
-- Table structure for table `permission_role`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

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
(2, 6),
(2, 1),
(4, 2),
(2, 2),
(3, 4),
(1, 7),
(2, 7),
(1, 8),
(2, 8);

-- --------------------------------------------------------

--
-- Table structure for table `roles_menu_headers`
--

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
(4, 2),
(2, 2),
(4, 1),
(2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `roles_menu_items`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `roles_sub_menu_items`
--

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
(2, 22),
(1, 23),
(2, 23),
(1, 26),
(2, 26),
(1, 27),
(2, 27);

-- --------------------------------------------------------

--
-- Table structure for table `roles_sub_most_menu_items`
--

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
(1, 9),
(2, 9);

-- --------------------------------------------------------

--
-- Table structure for table `role_user`
--

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
(126, 3),
(127, 3),
(128, 3),
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

-- --------------------------------------------------------

--
-- Table structure for table `sponsors`
--

CREATE TABLE IF NOT EXISTS `sponsors` (
  `sponsor_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`sponsor_id`),
  KEY `sponsors_lga_id_index` (`lga_id`),
  KEY `sponsors_salutation_id_index` (`salutation_id`),
  KEY `sponsors_created_by_index` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `staffs`
--

CREATE TABLE IF NOT EXISTS `staffs` (
  `staff_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`staff_id`),
  KEY `staffs_lga_id_index` (`lga_id`),
  KEY `staffs_salutation_id_index` (`salutation_id`),
  KEY `staffs_created_by_index` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE IF NOT EXISTS `students` (
  `student_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `middle_name` varchar(70) COLLATE utf8_unicode_ci DEFAULT NULL,
  `student_no` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gender` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `dob` date DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sponsor_id` int(10) unsigned NOT NULL,
  `classroom_id` int(10) unsigned NOT NULL,
  `status_id` int(10) unsigned NOT NULL DEFAULT '1',
  `admitted_term_id` int(10) unsigned NOT NULL,
  `lga_id` int(10) unsigned DEFAULT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`student_id`),
  KEY `students_sponsor_id_index` (`sponsor_id`),
  KEY `students_classroom_id_index` (`classroom_id`),
  KEY `students_status_id_index` (`status_id`),
  KEY `students_admitted_term_id_index` (`admitted_term_id`),
  KEY `students_lga_id_index` (`lga_id`),
  KEY `students_created_by_index` (`created_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=47 ;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `first_name`, `last_name`, `middle_name`, `student_no`, `gender`, `dob`, `avatar`, `sponsor_id`, `classroom_id`, `status_id`, `admitted_term_id`, `lga_id`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Chijioke', 'casmir', NULL, 'STD00001', 'Male', NULL, NULL, 62, 1, 1, 1, NULL, 2, '2016-05-31 15:04:09', '2016-05-31 15:04:10'),
(2, 'Omotoke', 'Elizabeth', NULL, 'STD00002', 'Female', NULL, NULL, 102, 1, 1, 1, NULL, 2, '2016-05-31 15:06:36', '2016-05-31 15:06:36'),
(3, 'Omotoke', 'Elizabeth', NULL, 'STD00003', 'Female', NULL, NULL, 102, 1, 1, 1, NULL, 2, '2016-05-31 15:06:39', '2016-05-31 15:06:39'),
(4, 'Sharon', 'Ajoke', NULL, 'STD00004', 'Female', NULL, NULL, 54, 1, 1, 1, NULL, 2, '2016-05-31 15:34:39', '2016-05-31 15:34:39'),
(5, 'Stephanie', 'Uche', NULL, 'STD00005', 'Female', NULL, NULL, 74, 1, 1, 1, NULL, 2, '2016-05-31 15:36:08', '2016-05-31 15:36:08'),
(6, 'Naomi', 'Nkemjikam', NULL, 'STD00006', 'Female', NULL, NULL, 88, 1, 1, 1, NULL, 2, '2016-05-31 15:37:24', '2016-05-31 15:37:24'),
(7, 'Aanuoluwapo', 'Dorcas', NULL, 'STD00007', 'Female', NULL, NULL, 97, 1, 1, 1, NULL, 2, '2016-05-31 15:38:31', '2016-05-31 15:38:31'),
(8, 'Lauretta', 'Esther', NULL, 'STD00008', 'Female', NULL, NULL, 53, 1, 1, 1, NULL, 2, '2016-05-31 15:39:11', '2016-05-31 15:39:11'),
(9, 'wisdom', 'Chuibueze', NULL, 'STD00009', 'Male', NULL, NULL, 45, 1, 1, 1, NULL, 2, '2016-05-31 15:40:11', '2016-05-31 15:40:11'),
(10, 'Adebola', 'Favour', NULL, 'STD00010', 'Male', NULL, NULL, 21, 1, 1, 1, NULL, 2, '2016-05-31 15:41:15', '2016-05-31 15:41:15'),
(11, 'Eniola', 'Omotolani', NULL, 'STD00011', 'Female', NULL, NULL, 31, 2, 1, 1, NULL, 2, '2016-05-31 15:51:36', '2016-05-31 15:51:36'),
(12, 'Chiamaka', 'Blessing', NULL, 'STD00012', 'Female', NULL, NULL, 118, 2, 1, 1, NULL, 2, '2016-05-31 15:52:41', '2016-05-31 15:52:41'),
(13, 'Osamudiameh', 'Queen', NULL, 'STD00013', 'Female', NULL, NULL, 71, 2, 1, 1, NULL, 2, '2016-05-31 15:53:34', '2016-05-31 15:53:34'),
(14, 'Benita', 'Omolola', NULL, 'STD00014', 'Female', NULL, NULL, 115, 2, 1, 1, NULL, 2, '2016-05-31 16:05:20', '2016-05-31 16:05:20'),
(15, 'Chidozie', 'Ephraim', NULL, 'STD00015', 'Male', NULL, NULL, 75, 2, 1, 1, NULL, 2, '2016-05-31 16:07:17', '2016-05-31 16:07:17'),
(16, 'Chidozie', 'Ephraim', NULL, 'STD00016', 'Male', NULL, NULL, 75, 2, 1, 1, NULL, 2, '2016-05-31 16:07:23', '2016-05-31 16:07:23'),
(17, 'Amaka', 'Deborah', NULL, 'STD00017', 'Female', NULL, NULL, 82, 2, 1, 1, NULL, 2, '2016-05-31 16:08:41', '2016-05-31 16:08:41'),
(18, 'Ibukunoluwa', 'Folarin', NULL, 'STD00018', 'Male', NULL, NULL, 39, 2, 1, 1, NULL, 2, '2016-05-31 16:09:49', '2016-05-31 16:09:49'),
(19, 'Ibukunoluwa', 'Folarin', NULL, 'STD00019', 'Male', NULL, NULL, 39, 2, 1, 1, NULL, 2, '2016-05-31 16:10:24', '2016-05-31 16:10:24'),
(20, 'Emmanuella', 'Ayomikun', NULL, 'STD00020', 'Female', NULL, NULL, 78, 3, 1, 1, NULL, 2, '2016-05-31 16:25:42', '2016-05-31 16:25:42'),
(21, 'Angela', 'Damilola', NULL, 'STD00021', 'Female', NULL, NULL, 109, 3, 1, 1, NULL, 2, '2016-05-31 16:26:59', '2016-05-31 16:26:59'),
(22, 'Adeolu', 'Peter', NULL, 'STD00022', 'Male', NULL, NULL, 79, 3, 1, 1, NULL, 2, '2016-05-31 16:45:38', '2016-05-31 16:45:38'),
(23, 'Damilola', 'Abdul-Samad', NULL, 'STD00023', 'Male', NULL, NULL, 28, 3, 1, 1, NULL, 2, '2016-05-31 16:47:24', '2016-05-31 16:47:24'),
(24, 'Esther', 'Anita', NULL, 'STD00024', 'Female', NULL, NULL, 96, 3, 1, 1, NULL, 2, '2016-05-31 16:48:11', '2016-05-31 16:48:11'),
(25, 'Ayobami', 'Felix', NULL, 'STD00025', 'Male', NULL, NULL, 42, 3, 1, 1, NULL, 2, '2016-05-31 16:49:01', '2016-05-31 16:49:01'),
(26, 'Chidera', 'Marvelous', NULL, 'STD00026', 'Male', NULL, NULL, 33, 3, 1, 1, NULL, 2, '2016-05-31 16:49:47', '2016-05-31 16:49:47'),
(27, 'Sarah', 'Oyinenche', NULL, 'STD00027', 'Female', NULL, NULL, 91, 3, 1, 1, NULL, 2, '2016-05-31 16:50:42', '2016-05-31 16:50:42'),
(28, 'Donald', 'Isioma', NULL, 'STD00028', 'Male', NULL, NULL, 86, 3, 1, 1, NULL, 2, '2016-05-31 16:51:51', '2016-05-31 16:51:52'),
(29, 'Edna', 'Favour', NULL, 'STD00029', 'Female', NULL, NULL, 54, 4, 1, 1, NULL, 2, '2016-05-31 17:11:40', '2016-05-31 17:11:40'),
(30, 'Zainab', 'Adeola', NULL, 'STD00030', 'Female', NULL, NULL, 111, 4, 1, 1, NULL, 2, '2016-05-31 17:12:37', '2016-05-31 17:12:37'),
(31, 'Zainab', 'Adeola', NULL, 'STD00031', 'Female', NULL, NULL, 111, 4, 1, 1, NULL, 2, '2016-05-31 17:24:06', '2016-05-31 17:24:06'),
(32, 'Abiola', 'Precious', NULL, 'STD00032', 'Female', NULL, NULL, 31, 4, 1, 1, NULL, 2, '2016-05-31 17:25:19', '2016-05-31 17:25:19'),
(33, 'Murede', 'Raheem', NULL, 'STD00033', 'Male', NULL, NULL, 79, 4, 1, 1, NULL, 2, '2016-05-31 17:26:32', '2016-05-31 17:26:32'),
(34, 'Destiny', 'Isosa', NULL, 'STD00034', 'Male', NULL, NULL, 51, 4, 1, 1, NULL, 2, '2016-05-31 17:34:03', '2016-05-31 17:34:03'),
(35, 'Precious', 'David', NULL, 'STD00035', 'Male', NULL, NULL, 96, 4, 1, 1, NULL, 2, '2016-05-31 17:34:54', '2016-05-31 17:34:54'),
(36, 'Angel', 'Mmesoma', NULL, 'STD00036', 'Female', NULL, NULL, 56, 4, 1, 1, NULL, 2, '2016-05-31 17:35:45', '2016-05-31 17:35:45'),
(37, 'Temiloluwa', 'Wuraola', NULL, 'STD00037', 'Female', NULL, NULL, 23, 4, 1, 1, NULL, 2, '2016-05-31 17:36:38', '2016-05-31 17:36:38'),
(38, 'Justin', 'Emmanuel', NULL, 'STD00038', 'Male', NULL, NULL, 54, 5, 1, 1, NULL, 2, '2016-05-31 17:37:19', '2016-05-31 17:37:19'),
(39, 'Divine', 'Obusor', NULL, 'STD00039', 'Female', NULL, NULL, 120, 5, 1, 1, NULL, 2, '2016-05-31 17:38:29', '2016-05-31 17:38:29'),
(40, 'Peace', 'Amaka', NULL, 'STD00040', 'Female', NULL, NULL, 107, 5, 1, 1, NULL, 2, '2016-05-31 17:39:11', '2016-05-31 17:39:11'),
(41, 'Boluwatife', 'Grace', NULL, 'STD00041', 'Female', NULL, NULL, 30, 5, 1, 1, NULL, 2, '2016-05-31 17:40:07', '2016-05-31 17:40:07'),
(42, 'Omolara', 'Joy', NULL, 'STD00042', 'Female', NULL, NULL, 77, 5, 1, 1, NULL, 2, '2016-05-31 17:40:57', '2016-05-31 17:40:57'),
(43, 'Oreoluwa', 'Mulikat', NULL, 'STD00043', 'Female', NULL, NULL, 126, 5, 1, 1, NULL, 2, '2016-05-31 18:09:13', '2016-05-31 18:09:13'),
(44, 'Adesope', 'Anthony', NULL, 'STD00044', 'Male', NULL, NULL, 128, 1, 1, 1, NULL, 2, '2016-05-31 18:19:06', '2016-05-31 18:19:06'),
(45, 'Suleiman', 'Ori-Owo', NULL, 'STD00045', 'Male', NULL, NULL, 112, 2, 1, 1, NULL, 2, '2016-05-31 18:22:01', '2016-05-31 18:22:01'),
(46, 'Oreoluwa', 'Mulikat', NULL, 'STD00046', 'Female', NULL, NULL, 126, 5, 1, 1, NULL, 2, '2016-05-31 18:26:19', '2016-05-31 18:26:19');

-- --------------------------------------------------------

--
-- Table structure for table `student_classes`
--

CREATE TABLE IF NOT EXISTS `student_classes` (
  `student_class_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` int(10) unsigned NOT NULL,
  `classroom_id` int(10) unsigned NOT NULL,
  `academic_year_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`student_class_id`),
  KEY `student_classes_student_id_index` (`student_id`),
  KEY `student_classes_classroom_id_index` (`classroom_id`),
  KEY `student_classes_academic_year_id_index` (`academic_year_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=47 ;

--
-- Dumping data for table `student_classes`
--

INSERT INTO `student_classes` (`student_class_id`, `student_id`, `classroom_id`, `academic_year_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, '2016-05-31 15:04:09', '2016-05-31 15:04:09'),
(2, 2, 1, 1, '2016-05-31 15:06:36', '2016-05-31 15:06:36'),
(3, 3, 1, 1, '2016-05-31 15:06:39', '2016-05-31 15:06:39'),
(4, 4, 1, 1, '2016-05-31 15:34:39', '2016-05-31 15:34:39'),
(5, 5, 1, 1, '2016-05-31 15:36:08', '2016-05-31 15:36:08'),
(6, 6, 1, 1, '2016-05-31 15:37:24', '2016-05-31 15:37:24'),
(7, 7, 1, 1, '2016-05-31 15:38:31', '2016-05-31 15:38:31'),
(8, 8, 1, 1, '2016-05-31 15:39:11', '2016-05-31 15:39:11'),
(9, 9, 1, 1, '2016-05-31 15:40:11', '2016-05-31 15:40:11'),
(10, 10, 1, 1, '2016-05-31 15:41:15', '2016-05-31 15:41:15'),
(11, 11, 2, 1, '2016-05-31 15:51:36', '2016-05-31 15:51:36'),
(12, 12, 2, 1, '2016-05-31 15:52:41', '2016-05-31 15:52:41'),
(13, 13, 2, 1, '2016-05-31 15:53:34', '2016-05-31 15:53:34'),
(14, 14, 2, 1, '2016-05-31 16:05:20', '2016-05-31 16:05:20'),
(15, 15, 2, 1, '2016-05-31 16:07:17', '2016-05-31 16:07:17'),
(16, 16, 2, 1, '2016-05-31 16:07:23', '2016-05-31 16:07:23'),
(17, 17, 2, 1, '2016-05-31 16:08:41', '2016-05-31 16:08:41'),
(18, 18, 2, 1, '2016-05-31 16:09:49', '2016-05-31 16:09:49'),
(19, 19, 2, 1, '2016-05-31 16:10:24', '2016-05-31 16:10:24'),
(20, 20, 3, 1, '2016-05-31 16:25:42', '2016-05-31 16:25:42'),
(21, 21, 3, 1, '2016-05-31 16:26:59', '2016-05-31 16:26:59'),
(22, 22, 3, 1, '2016-05-31 16:45:38', '2016-05-31 16:45:38'),
(23, 23, 3, 1, '2016-05-31 16:47:24', '2016-05-31 16:47:24'),
(24, 24, 3, 1, '2016-05-31 16:48:11', '2016-05-31 16:48:11'),
(25, 25, 3, 1, '2016-05-31 16:49:01', '2016-05-31 16:49:01'),
(26, 26, 3, 1, '2016-05-31 16:49:47', '2016-05-31 16:49:47'),
(27, 27, 3, 1, '2016-05-31 16:50:42', '2016-05-31 16:50:42'),
(28, 28, 3, 1, '2016-05-31 16:51:52', '2016-05-31 16:51:52'),
(29, 29, 4, 1, '2016-05-31 17:11:40', '2016-05-31 17:11:40'),
(30, 30, 4, 1, '2016-05-31 17:12:37', '2016-05-31 17:12:37'),
(31, 31, 4, 1, '2016-05-31 17:24:06', '2016-05-31 17:24:06'),
(32, 32, 4, 1, '2016-05-31 17:25:19', '2016-05-31 17:25:19'),
(33, 33, 4, 1, '2016-05-31 17:26:32', '2016-05-31 17:26:32'),
(34, 34, 4, 1, '2016-05-31 17:34:03', '2016-05-31 17:34:03'),
(35, 35, 4, 1, '2016-05-31 17:34:54', '2016-05-31 17:34:54'),
(36, 36, 4, 1, '2016-05-31 17:35:45', '2016-05-31 17:35:45'),
(37, 37, 4, 1, '2016-05-31 17:36:38', '2016-05-31 17:36:38'),
(38, 38, 5, 1, '2016-05-31 17:37:19', '2016-05-31 17:37:19'),
(39, 39, 5, 1, '2016-05-31 17:38:29', '2016-05-31 17:38:29'),
(40, 40, 5, 1, '2016-05-31 17:39:11', '2016-05-31 17:39:11'),
(41, 41, 5, 1, '2016-05-31 17:40:07', '2016-05-31 17:40:07'),
(42, 42, 5, 1, '2016-05-31 17:40:57', '2016-05-31 17:40:57'),
(43, 43, 5, 1, '2016-05-31 18:09:13', '2016-05-31 18:09:13'),
(44, 44, 1, 1, '2016-05-31 18:19:06', '2016-05-31 18:19:06'),
(45, 45, 2, 1, '2016-05-31 18:22:01', '2016-05-31 18:22:01'),
(46, 46, 5, 1, '2016-05-31 18:26:19', '2016-05-31 18:26:19');

-- --------------------------------------------------------

--
-- Table structure for table `student_subjects`
--

CREATE TABLE IF NOT EXISTS `student_subjects` (
  `student_id` int(10) unsigned NOT NULL,
  `subject_classroom_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`student_id`,`subject_classroom_id`),
  KEY `student_subjects_student_id_index` (`student_id`),
  KEY `student_subjects_subject_classroom_id_index` (`subject_classroom_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `student_subjects`
--

INSERT INTO `student_subjects` (`student_id`, `subject_classroom_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 12),
(1, 13),
(1, 14),
(1, 73),
(1, 79),
(1, 80),
(2, 1),
(2, 2),
(2, 3),
(2, 4),
(2, 5),
(2, 6),
(2, 7),
(2, 8),
(2, 9),
(2, 10),
(2, 11),
(2, 12),
(2, 13),
(2, 14),
(2, 73),
(2, 79),
(2, 80),
(3, 1),
(3, 2),
(3, 3),
(3, 4),
(3, 5),
(3, 6),
(3, 7),
(3, 8),
(3, 9),
(3, 10),
(3, 11),
(3, 12),
(3, 13),
(3, 14),
(3, 73),
(3, 79),
(3, 80),
(4, 1),
(4, 2),
(4, 3),
(4, 4),
(4, 5),
(4, 6),
(4, 7),
(4, 8),
(4, 9),
(4, 10),
(4, 11),
(4, 12),
(4, 13),
(4, 14),
(4, 73),
(4, 79),
(4, 80),
(5, 1),
(5, 2),
(5, 3),
(5, 4),
(5, 5),
(5, 6),
(5, 7),
(5, 8),
(5, 9),
(5, 10),
(5, 11),
(5, 12),
(5, 13),
(5, 14),
(5, 73),
(5, 79),
(5, 80),
(6, 1),
(6, 2),
(6, 3),
(6, 4),
(6, 5),
(6, 6),
(6, 7),
(6, 8),
(6, 9),
(6, 10),
(6, 11),
(6, 12),
(6, 13),
(6, 14),
(6, 73),
(6, 79),
(6, 80),
(7, 1),
(7, 2),
(7, 3),
(7, 4),
(7, 5),
(7, 6),
(7, 7),
(7, 8),
(7, 9),
(7, 10),
(7, 11),
(7, 12),
(7, 13),
(7, 14),
(7, 73),
(7, 79),
(7, 80),
(8, 1),
(8, 2),
(8, 3),
(8, 4),
(8, 5),
(8, 6),
(8, 7),
(8, 8),
(8, 9),
(8, 10),
(8, 11),
(8, 12),
(8, 13),
(8, 14),
(8, 73),
(8, 79),
(8, 80),
(9, 1),
(9, 2),
(9, 3),
(9, 4),
(9, 5),
(9, 6),
(9, 7),
(9, 8),
(9, 9),
(9, 10),
(9, 11),
(9, 12),
(9, 13),
(9, 14),
(9, 73),
(9, 79),
(9, 80),
(10, 1),
(10, 2),
(10, 3),
(10, 4),
(10, 5),
(10, 6),
(10, 7),
(10, 8),
(10, 9),
(10, 10),
(10, 11),
(10, 12),
(10, 13),
(10, 14),
(10, 73),
(10, 79),
(10, 80),
(11, 15),
(11, 16),
(11, 17),
(11, 18),
(11, 19),
(11, 20),
(11, 21),
(11, 22),
(11, 23),
(11, 24),
(11, 25),
(11, 26),
(11, 27),
(11, 28),
(11, 72),
(11, 81),
(11, 82),
(12, 15),
(12, 16),
(12, 17),
(12, 18),
(12, 19),
(12, 20),
(12, 21),
(12, 22),
(12, 23),
(12, 24),
(12, 25),
(12, 26),
(12, 27),
(12, 28),
(12, 72),
(12, 81),
(12, 82),
(13, 15),
(13, 16),
(13, 17),
(13, 18),
(13, 19),
(13, 20),
(13, 21),
(13, 22),
(13, 23),
(13, 24),
(13, 25),
(13, 26),
(13, 27),
(13, 28),
(13, 72),
(13, 81),
(13, 82),
(14, 15),
(14, 16),
(14, 17),
(14, 18),
(14, 19),
(14, 20),
(14, 21),
(14, 22),
(14, 23),
(14, 24),
(14, 25),
(14, 26),
(14, 27),
(14, 28),
(14, 72),
(14, 81),
(14, 82),
(15, 15),
(15, 16),
(15, 17),
(15, 18),
(15, 19),
(15, 20),
(15, 21),
(15, 22),
(15, 23),
(15, 24),
(15, 25),
(15, 26),
(15, 27),
(15, 28),
(15, 72),
(15, 81),
(15, 82),
(16, 15),
(16, 16),
(16, 17),
(16, 18),
(16, 19),
(16, 20),
(16, 21),
(16, 22),
(16, 23),
(16, 24),
(16, 25),
(16, 26),
(16, 27),
(16, 28),
(16, 72),
(16, 81),
(16, 82),
(17, 15),
(17, 16),
(17, 17),
(17, 18),
(17, 19),
(17, 20),
(17, 21),
(17, 22),
(17, 23),
(17, 24),
(17, 25),
(17, 26),
(17, 27),
(17, 28),
(17, 72),
(17, 81),
(17, 82),
(18, 15),
(18, 16),
(18, 17),
(18, 18),
(18, 19),
(18, 20),
(18, 21),
(18, 22),
(18, 23),
(18, 24),
(18, 25),
(18, 26),
(18, 27),
(18, 28),
(18, 72),
(18, 81),
(18, 82),
(19, 15),
(19, 16),
(19, 17),
(19, 18),
(19, 19),
(19, 20),
(19, 21),
(19, 22),
(19, 23),
(19, 24),
(19, 25),
(19, 26),
(19, 27),
(19, 28),
(19, 72),
(19, 81),
(19, 82),
(20, 29),
(20, 30),
(20, 31),
(20, 32),
(20, 33),
(20, 34),
(20, 35),
(20, 36),
(20, 37),
(20, 38),
(20, 39),
(20, 75),
(20, 76),
(20, 77),
(20, 78),
(20, 83),
(20, 84),
(21, 29),
(21, 30),
(21, 31),
(21, 32),
(21, 33),
(21, 34),
(21, 35),
(21, 36),
(21, 37),
(21, 38),
(21, 39),
(21, 75),
(21, 76),
(21, 77),
(21, 78),
(21, 83),
(21, 84),
(22, 29),
(22, 30),
(22, 31),
(22, 32),
(22, 33),
(22, 34),
(22, 35),
(22, 36),
(22, 37),
(22, 38),
(22, 39),
(22, 75),
(22, 76),
(22, 77),
(22, 78),
(22, 83),
(22, 84),
(23, 29),
(23, 30),
(23, 31),
(23, 32),
(23, 33),
(23, 34),
(23, 35),
(23, 36),
(23, 37),
(23, 38),
(23, 39),
(23, 75),
(23, 76),
(23, 77),
(23, 78),
(23, 83),
(23, 84),
(24, 29),
(24, 30),
(24, 31),
(24, 32),
(24, 33),
(24, 34),
(24, 35),
(24, 36),
(24, 37),
(24, 38),
(24, 39),
(24, 75),
(24, 76),
(24, 77),
(24, 78),
(24, 83),
(24, 84),
(25, 29),
(25, 30),
(25, 31),
(25, 32),
(25, 33),
(25, 34),
(25, 35),
(25, 36),
(25, 37),
(25, 38),
(25, 39),
(25, 75),
(25, 76),
(25, 77),
(25, 78),
(25, 83),
(25, 84),
(26, 29),
(26, 30),
(26, 31),
(26, 32),
(26, 33),
(26, 34),
(26, 35),
(26, 36),
(26, 37),
(26, 38),
(26, 39),
(26, 75),
(26, 76),
(26, 77),
(26, 78),
(26, 83),
(26, 84),
(27, 29),
(27, 30),
(27, 31),
(27, 32),
(27, 33),
(27, 34),
(27, 35),
(27, 36),
(27, 37),
(27, 38),
(27, 39),
(27, 75),
(27, 76),
(27, 77),
(27, 78),
(27, 83),
(27, 84),
(28, 29),
(28, 30),
(28, 31),
(28, 32),
(28, 33),
(28, 34),
(28, 35),
(28, 36),
(28, 37),
(28, 38),
(28, 39),
(28, 75),
(28, 76),
(28, 77),
(28, 78),
(28, 83),
(28, 84),
(29, 43),
(29, 44),
(29, 45),
(29, 46),
(29, 47),
(29, 48),
(29, 49),
(29, 50),
(29, 51),
(29, 52),
(29, 53),
(29, 54),
(29, 55),
(29, 56),
(29, 74),
(29, 85),
(29, 86),
(29, 87),
(30, 43),
(30, 44),
(30, 45),
(30, 46),
(30, 47),
(30, 48),
(30, 49),
(30, 50),
(30, 51),
(30, 52),
(30, 53),
(30, 54),
(30, 55),
(30, 56),
(30, 74),
(30, 85),
(30, 86),
(30, 87),
(31, 43),
(31, 44),
(31, 45),
(31, 46),
(31, 47),
(31, 48),
(31, 49),
(31, 50),
(31, 51),
(31, 52),
(31, 53),
(31, 54),
(31, 55),
(31, 56),
(31, 74),
(31, 85),
(31, 86),
(31, 87),
(32, 43),
(32, 44),
(32, 45),
(32, 46),
(32, 47),
(32, 48),
(32, 49),
(32, 50),
(32, 51),
(32, 52),
(32, 53),
(32, 54),
(32, 55),
(32, 56),
(32, 74),
(32, 85),
(32, 86),
(32, 87),
(33, 43),
(33, 44),
(33, 45),
(33, 46),
(33, 47),
(33, 48),
(33, 49),
(33, 50),
(33, 51),
(33, 52),
(33, 53),
(33, 54),
(33, 55),
(33, 56),
(33, 74),
(33, 85),
(33, 86),
(33, 87),
(34, 43),
(34, 44),
(34, 45),
(34, 46),
(34, 47),
(34, 48),
(34, 49),
(34, 50),
(34, 51),
(34, 52),
(34, 53),
(34, 54),
(34, 55),
(34, 56),
(34, 74),
(34, 85),
(34, 86),
(34, 87),
(35, 43),
(35, 44),
(35, 45),
(35, 46),
(35, 47),
(35, 48),
(35, 49),
(35, 50),
(35, 51),
(35, 52),
(35, 53),
(35, 54),
(35, 55),
(35, 56),
(35, 74),
(35, 85),
(35, 86),
(35, 87),
(36, 43),
(36, 44),
(36, 45),
(36, 46),
(36, 47),
(36, 48),
(36, 49),
(36, 50),
(36, 51),
(36, 52),
(36, 53),
(36, 54),
(36, 55),
(36, 56),
(36, 74),
(36, 85),
(36, 86),
(36, 87),
(37, 43),
(37, 44),
(37, 45),
(37, 46),
(37, 47),
(37, 48),
(37, 49),
(37, 50),
(37, 51),
(37, 52),
(37, 53),
(37, 54),
(37, 55),
(37, 56),
(37, 74),
(37, 85),
(37, 86),
(37, 87),
(38, 57),
(38, 58),
(38, 59),
(38, 60),
(38, 61),
(38, 62),
(38, 63),
(38, 64),
(38, 65),
(38, 66),
(38, 67),
(38, 68),
(38, 70),
(38, 71),
(38, 88),
(39, 57),
(39, 58),
(39, 59),
(39, 60),
(39, 61),
(39, 62),
(39, 63),
(39, 64),
(39, 65),
(39, 66),
(39, 67),
(39, 68),
(39, 70),
(39, 71),
(39, 88),
(40, 57),
(40, 58),
(40, 59),
(40, 60),
(40, 61),
(40, 62),
(40, 63),
(40, 64),
(40, 65),
(40, 66),
(40, 67),
(40, 68),
(40, 70),
(40, 71),
(40, 88),
(41, 57),
(41, 58),
(41, 59),
(41, 60),
(41, 61),
(41, 62),
(41, 63),
(41, 64),
(41, 65),
(41, 66),
(41, 67),
(41, 68),
(41, 70),
(41, 71),
(41, 88),
(42, 57),
(42, 58),
(42, 59),
(42, 60),
(42, 61),
(42, 62),
(42, 63),
(42, 64),
(42, 65),
(42, 66),
(42, 67),
(42, 68),
(42, 70),
(42, 71),
(42, 88),
(43, 57),
(43, 58),
(43, 59),
(43, 60),
(43, 61),
(43, 62),
(43, 63),
(43, 64),
(43, 65),
(43, 66),
(43, 67),
(43, 68),
(43, 70),
(43, 71),
(43, 88),
(44, 1),
(44, 2),
(44, 3),
(44, 4),
(44, 5),
(44, 6),
(44, 7),
(44, 8),
(44, 9),
(44, 10),
(44, 11),
(44, 12),
(44, 13),
(44, 14),
(44, 73),
(44, 79),
(44, 80),
(45, 15),
(45, 16),
(45, 17),
(45, 18),
(45, 19),
(45, 20),
(45, 21),
(45, 22),
(45, 23),
(45, 24),
(45, 25),
(45, 26),
(45, 27),
(45, 28),
(45, 72),
(45, 81),
(45, 82),
(46, 57),
(46, 58),
(46, 59),
(46, 60),
(46, 61),
(46, 62),
(46, 63),
(46, 64),
(46, 65),
(46, 66),
(46, 67),
(46, 68),
(46, 70),
(46, 71),
(46, 88);

-- --------------------------------------------------------

--
-- Table structure for table `subject_classrooms`
--

CREATE TABLE IF NOT EXISTS `subject_classrooms` (
  `subject_classroom_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `subject_id` int(10) unsigned NOT NULL,
  `classroom_id` int(10) unsigned NOT NULL,
  `academic_term_id` int(10) unsigned NOT NULL,
  `exam_status_id` int(10) unsigned NOT NULL DEFAULT '2',
  `tutor_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`subject_classroom_id`),
  KEY `subject_classrooms_subject_id_index` (`subject_id`),
  KEY `subject_classrooms_classroom_id_index` (`classroom_id`),
  KEY `subject_classrooms_academic_term_id_index` (`academic_term_id`),
  KEY `subject_classrooms_exam_status_id_index` (`exam_status_id`),
  KEY `subject_classrooms_tutor_id_index` (`tutor_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=89 ;

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

-- --------------------------------------------------------

--
-- Table structure for table `sub_menu_items`
--

CREATE TABLE IF NOT EXISTS `sub_menu_items` (
  `sub_menu_item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sub_menu_item` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `sub_menu_item_url` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `sub_menu_item_icon` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `active` int(10) unsigned NOT NULL DEFAULT '1',
  `sequence` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` int(10) unsigned NOT NULL DEFAULT '1',
  `menu_item_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`sub_menu_item_id`),
  KEY `sub_menu_items_menu_item_id_index` (`menu_item_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=28 ;

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
(22, 'CLASS ROOMS', '/class-rooms', 'fa fa-plus', 1, '3', 1, 17, '2016-05-13 17:13:48', '2016-05-13 17:13:48'),
(23, 'ASSIGN TO CLASS', '/subject-classrooms', 'fa fa-list', 1, '3', 1, 15, '2016-05-26 23:05:25', '2016-05-26 23:05:25'),
(26, 'SETUP', '/assessment-setups', 'fa fa-ticket', 1, '1', 1, 19, '2016-05-26 23:08:41', '2016-05-26 23:08:41'),
(27, 'SETUP DETAILS', '/assessment-setups/details', 'fa fa-list-alt', 1, '2', 1, 19, '2016-05-26 23:08:41', '2016-05-26 23:08:41');

-- --------------------------------------------------------

--
-- Table structure for table `sub_most_menu_items`
--

CREATE TABLE IF NOT EXISTS `sub_most_menu_items` (
  `sub_most_menu_item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sub_most_menu_item` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `sub_most_menu_item_url` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `sub_most_menu_item_icon` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `active` int(10) unsigned NOT NULL DEFAULT '1',
  `sequence` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` int(10) unsigned NOT NULL DEFAULT '1',
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

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `phone_no` (`phone_no`),
  KEY `users_user_type_id_index` (`user_type_id`),
  KEY `salutation_id` (`salutation_id`),
  KEY `lga_id` (`lga_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=129 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `password`, `phone_no`, `email`, `first_name`, `last_name`, `middle_name`, `gender`, `dob`, `phone_no2`, `user_type_id`, `lga_id`, `salutation_id`, `verified`, `status`, `avatar`, `verification_code`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, '$2y$10$WgHQSaszEOSJpz2HsoUToeJoyCxh7fGuc3ZoLJA.NubXU42L6E3SG', '08011223344', 'admin@gmail.com', 'Emma', 'Okafor', '', 'Male', '2016-04-05', '', 1, 0, 1, 1, 1, '1_avatar.jpg', NULL, 'KTi4iA5PkcLlkLcsBM89GAH973RgE8SyBrBkqjiyGxMj7PvgqAPhs8QnFNmO', NULL, '2016-05-26 00:11:07'),
(2, '$2y$10$/VnAIZSpHw2o042t.bmP7eqCPAN/imxPcaAaTkTfno1uPi8BaOQCa', '08161730788', 'bamidelemike2003@yahoo.com', 'Bamidele', 'Micheal', '', 'Male', '1976-02-11', '08066303843', 2, 476, 1, 1, 1, '2_avatar.jpg', 'x9pxH08aB60ZKwe12DDKbiD3V5628TyGMd1v8Q5I', 'PW92bmZmvXY2rJR4qfGVIvye8LmeLrZmAy0qjY3WyQI7209YNlRFyI3z6prV', '2016-04-28 22:21:05', '2016-05-31 18:30:23'),
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
(125, '$2y$10$lkgkw77NXaKqnFSRJx51.ub5Y/XYDZIFAtyS0TAXSNNrJHVScxLke', '08039404007', 'princekehinde@yahoo.com', 'Famoroti', 'Kehinde', NULL, NULL, NULL, NULL, 4, NULL, NULL, 1, 1, NULL, 'hIGpSYzf0hM32ulRD8cWiJR43hyNxnLHHlCpFFsF', NULL, '2016-05-18 19:01:40', '2016-05-18 19:01:40'),
(126, '$2y$10$EYrAlY/Inycw1yvTWcEOmOfDl/pNEr25HkKl8fHJqZGSm/2nw3eDa', '08028820979', 'sanni@yahoo.com', 'Sanni', 'Sanni', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, '7YqtlQxqWz9dak1O7RD63rcN1jsDfPbUWThctugu', NULL, '2016-05-31 17:51:19', '2016-05-31 17:51:19'),
(127, '$2y$10$637kwDy8t8pDxUrNz0IR7e5s2Jt.KEQafMSX2A3NliGSc8D5yqHwa', '08033018178', 'adesope@yahoo.com', 'Adesope', 'Adesope', '', NULL, '1978-03-26', '', 3, 515, 1, 1, 1, NULL, 'Pa7LlEouJd3c00984C2F0JUScBYe6YGpnM58dnbc', NULL, '2016-05-31 17:56:59', '2016-05-31 18:15:16'),
(128, '$2y$10$mXa.IWAgm6iz3kG7PBqU1.y6DtRlMZ0b6i15niz4qx1KKtJCiVZkm', '08033018176', 'adegbite@yahoo.com', 'Adegbite', 'Adegbite', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'DVr5D8TpCqjMkU00UhvMbRGjIpDDA6vK8QnxrBrD', NULL, '2016-05-31 18:16:49', '2016-05-31 18:16:49');

-- --------------------------------------------------------

--
-- Table structure for table `user_types`
--

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
(1, 'Developer', 1, NULL, NULL),
(2, 'Super Admin', 2, NULL, NULL),
(3, 'Sponsor', 2, '2016-04-28 21:35:55', '2016-04-28 21:35:55'),
(4, 'Staff', 2, '2016-04-28 21:35:15', '2016-04-28 21:35:15');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `academic_terms`
--
ALTER TABLE `academic_terms`
  ADD CONSTRAINT `academic_terms_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`academic_year_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `assessments`
--
ALTER TABLE `assessments`
  ADD CONSTRAINT `assessments_assessment_setup_detail_id_foreign` FOREIGN KEY (`assessment_setup_detail_id`) REFERENCES `assessment_setup_details` (`assessment_setup_detail_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `assessment_details`
--
ALTER TABLE `assessment_details`
  ADD CONSTRAINT `assessment_details_assessment_id_foreign` FOREIGN KEY (`assessment_id`) REFERENCES `assessments` (`assessment_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `assessment_setup_details`
--
ALTER TABLE `assessment_setup_details`
  ADD CONSTRAINT `assessment_setup_details_assessment_setup_id_foreign` FOREIGN KEY (`assessment_setup_id`) REFERENCES `assessment_setups` (`assessment_setup_id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
-- Constraints for table `student_classes`
--
ALTER TABLE `student_classes`
  ADD CONSTRAINT `student_classes_classroom_id_foreign` FOREIGN KEY (`classroom_id`) REFERENCES `classrooms` (`classroom_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `subject_classrooms`
--
ALTER TABLE `subject_classrooms`
  ADD CONSTRAINT `subject_classrooms_tutor_id_foreign` FOREIGN KEY (`tutor_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `subject_classrooms_classroom_id_foreign` FOREIGN KEY (`classroom_id`) REFERENCES `classrooms` (`classroom_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `subject_classrooms_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `solidste_portal_admin`.`subjects` (`subject_id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
