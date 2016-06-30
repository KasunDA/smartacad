-- phpMyAdmin SQL Dump
-- version 4.0.10.14
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Jun 28, 2016 at 01:05 PM
-- Server version: 5.5.50-cll
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

$$

$$

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=29 ;

--
-- Dumping data for table `assessments`
--

INSERT INTO `assessments` (`assessment_id`, `subject_classroom_id`, `assessment_setup_detail_id`, `marked`) VALUES
(1, 5, 1, 1),
(2, 1, 1, 2),
(3, 3, 1, 2),
(4, 15, 1, 2),
(5, 65, 5, 2),
(6, 5, 2, 1),
(7, 5, 3, 1),
(8, 13, 1, 1),
(9, 13, 2, 1),
(10, 13, 3, 1),
(11, 19, 1, 1),
(12, 19, 2, 1),
(13, 19, 3, 1),
(14, 27, 1, 1),
(15, 27, 2, 1),
(16, 27, 3, 1),
(17, 33, 1, 1),
(18, 33, 2, 1),
(19, 33, 3, 1),
(20, 34, 1, 1),
(21, 34, 2, 1),
(22, 34, 3, 1),
(23, 77, 1, 1),
(24, 77, 2, 1),
(25, 77, 3, 1),
(26, 66, 5, 1),
(27, 66, 6, 1),
(28, 66, 7, 1);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=389 ;

--
-- Dumping data for table `assessment_details`
--

INSERT INTO `assessment_details` (`assessment_detail_id`, `student_id`, `score`, `assessment_id`) VALUES
(1, 1, 8.00, 1),
(2, 2, 8.00, 1),
(4, 4, 8.00, 1),
(5, 5, 8.00, 1),
(6, 6, 8.00, 1),
(7, 7, 7.00, 1),
(8, 8, 8.00, 1),
(9, 9, 8.00, 1),
(10, 10, 2.00, 1),
(11, 44, 8.00, 1),
(16, 1, 0.00, 2),
(17, 2, 0.00, 2),
(18, 4, 0.00, 2),
(19, 5, 0.00, 2),
(20, 6, 0.00, 2),
(21, 7, 0.00, 2),
(22, 8, 0.00, 2),
(23, 9, 0.00, 2),
(24, 10, 0.00, 2),
(25, 44, 0.00, 2),
(31, 1, 0.00, 3),
(32, 2, 0.00, 3),
(33, 3, 0.00, 3),
(34, 4, 0.00, 3),
(35, 5, 0.00, 3),
(36, 6, 0.00, 3),
(37, 7, 0.00, 3),
(38, 8, 0.00, 3),
(39, 9, 0.00, 3),
(40, 10, 0.00, 3),
(41, 44, 0.00, 3),
(46, 11, 0.00, 4),
(47, 12, 0.00, 4),
(48, 13, 0.00, 4),
(49, 14, 0.00, 4),
(50, 15, 0.00, 4),
(51, 17, 0.00, 4),
(52, 19, 0.00, 4),
(53, 45, 0.00, 4),
(54, 47, 0.00, 4),
(61, 38, 0.00, 5),
(62, 39, 0.00, 5),
(63, 40, 0.00, 5),
(64, 41, 0.00, 5),
(65, 42, 0.00, 5),
(66, 43, 0.00, 5),
(67, 46, 0.00, 5),
(68, 1, 10.00, 6),
(69, 2, 17.00, 6),
(71, 4, 10.00, 6),
(72, 5, 17.00, 6),
(73, 6, 12.00, 6),
(74, 7, 13.00, 6),
(75, 8, 12.00, 6),
(76, 9, 15.00, 6),
(77, 10, 2.00, 6),
(78, 44, 13.00, 6),
(83, 1, 10.00, 7),
(84, 2, 10.00, 7),
(86, 4, 10.00, 7),
(87, 5, 10.00, 7),
(88, 6, 10.00, 7),
(89, 7, 10.00, 7),
(90, 8, 10.00, 7),
(91, 9, 10.00, 7),
(92, 10, 10.00, 7),
(93, 44, 10.00, 7),
(98, 1, 7.00, 8),
(99, 2, 10.00, 8),
(100, 3, 0.00, 8),
(101, 4, 4.00, 8),
(102, 5, 9.00, 8),
(103, 6, 8.00, 8),
(104, 7, 9.00, 8),
(105, 8, 8.00, 8),
(106, 9, 9.00, 8),
(107, 10, 2.00, 8),
(108, 44, 6.00, 8),
(113, 1, 10.00, 9),
(114, 2, 14.00, 9),
(115, 3, 0.00, 9),
(116, 4, 8.00, 9),
(117, 5, 10.00, 9),
(118, 6, 10.00, 9),
(119, 7, 8.00, 9),
(120, 8, 18.00, 9),
(121, 9, 8.00, 9),
(122, 10, 2.00, 9),
(123, 44, 10.00, 9),
(128, 1, 10.00, 10),
(129, 2, 10.00, 10),
(131, 4, 10.00, 10),
(132, 5, 10.00, 10),
(133, 6, 10.00, 10),
(134, 7, 10.00, 10),
(135, 8, 10.00, 10),
(136, 9, 10.00, 10),
(137, 10, 10.00, 10),
(138, 44, 10.00, 10),
(143, 11, 6.00, 11),
(144, 12, 8.00, 11),
(145, 13, 8.00, 11),
(146, 14, 4.00, 11),
(147, 15, 7.00, 11),
(148, 17, 8.00, 11),
(149, 19, 7.00, 11),
(150, 45, 7.00, 11),
(151, 47, 7.00, 11),
(158, 11, 14.00, 12),
(159, 12, 17.00, 12),
(160, 13, 15.00, 12),
(161, 14, 10.00, 12),
(162, 15, 12.00, 12),
(163, 17, 13.00, 12),
(164, 19, 10.00, 12),
(165, 45, 12.00, 12),
(166, 47, 14.00, 12),
(173, 11, 10.00, 13),
(174, 12, 10.00, 13),
(175, 13, 10.00, 13),
(176, 14, 10.00, 13),
(177, 15, 10.00, 13),
(178, 17, 10.00, 13),
(179, 19, 10.00, 13),
(180, 45, 10.00, 13),
(181, 47, 10.00, 13),
(188, 11, 8.00, 14),
(189, 12, 9.00, 14),
(190, 13, 8.00, 14),
(191, 14, 6.00, 14),
(192, 15, 7.00, 14),
(193, 17, 6.00, 14),
(194, 19, 7.00, 14),
(195, 45, 8.00, 14),
(196, 47, 8.00, 14),
(203, 11, 15.00, 15),
(204, 12, 19.00, 15),
(205, 13, 16.00, 15),
(206, 14, 6.00, 15),
(207, 15, 10.00, 15),
(208, 17, 14.00, 15),
(209, 19, 18.00, 15),
(210, 45, 13.00, 15),
(211, 47, 18.00, 15),
(218, 11, 10.00, 16),
(219, 12, 10.00, 16),
(220, 13, 10.00, 16),
(221, 14, 10.00, 16),
(222, 15, 10.00, 16),
(223, 17, 10.00, 16),
(224, 19, 10.00, 16),
(225, 45, 10.00, 16),
(226, 47, 10.00, 16),
(233, 20, 6.00, 17),
(234, 21, 9.00, 17),
(235, 22, 5.00, 17),
(237, 24, 10.00, 17),
(238, 25, 5.00, 17),
(239, 26, 9.00, 17),
(240, 27, 6.00, 17),
(241, 28, 8.00, 17),
(248, 20, 13.00, 18),
(249, 21, 19.00, 18),
(250, 22, 16.00, 18),
(252, 24, 19.00, 18),
(253, 25, 16.00, 18),
(254, 26, 16.00, 18),
(255, 27, 6.00, 18),
(256, 28, 16.00, 18),
(263, 20, 10.00, 19),
(264, 21, 10.00, 19),
(265, 22, 10.00, 19),
(267, 24, 10.00, 19),
(268, 25, 10.00, 19),
(269, 26, 10.00, 19),
(270, 27, 10.00, 19),
(271, 28, 10.00, 19),
(278, 20, 5.00, 20),
(279, 21, 9.00, 20),
(280, 22, 7.00, 20),
(281, 23, 5.00, 20),
(282, 24, 9.00, 20),
(283, 25, 10.00, 20),
(284, 26, 4.00, 20),
(285, 27, 5.00, 20),
(286, 28, 6.00, 20),
(293, 20, 9.00, 21),
(294, 21, 17.00, 21),
(295, 22, 15.00, 21),
(296, 23, 8.00, 21),
(297, 24, 19.00, 21),
(298, 25, 17.00, 21),
(299, 26, 10.00, 21),
(300, 27, 8.00, 21),
(301, 28, 17.00, 21),
(308, 20, 10.00, 22),
(309, 21, 10.00, 22),
(310, 22, 10.00, 22),
(311, 23, 10.00, 22),
(312, 24, 10.00, 22),
(313, 25, 10.00, 22),
(314, 26, 10.00, 22),
(315, 27, 10.00, 22),
(316, 28, 10.00, 22),
(323, 20, 5.00, 23),
(324, 21, 9.00, 23),
(325, 22, 8.00, 23),
(326, 23, 6.00, 23),
(327, 24, 9.00, 23),
(328, 25, 10.00, 23),
(329, 26, 6.00, 23),
(330, 27, 6.00, 23),
(331, 28, 7.00, 23),
(338, 20, 14.00, 24),
(339, 21, 17.00, 24),
(340, 22, 14.00, 24),
(341, 23, 14.00, 24),
(342, 24, 18.00, 24),
(343, 25, 17.00, 24),
(344, 26, 12.00, 24),
(345, 27, 14.00, 24),
(346, 28, 13.00, 24),
(353, 20, 10.00, 25),
(354, 21, 10.00, 25),
(355, 22, 10.00, 25),
(356, 23, 10.00, 25),
(357, 24, 10.00, 25),
(358, 25, 10.00, 25),
(359, 26, 10.00, 25),
(360, 27, 10.00, 25),
(361, 28, 10.00, 25),
(368, 38, 7.00, 26),
(369, 40, 4.00, 26),
(370, 41, 3.00, 26),
(371, 42, 0.00, 26),
(375, 38, 14.00, 27),
(376, 40, 16.00, 27),
(377, 41, 10.00, 27),
(378, 42, 0.00, 27),
(382, 38, 10.00, 28),
(383, 40, 10.00, 28),
(384, 41, 10.00, 28),
(385, 42, 10.00, 28);

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
(3, 3, 1, 1),
(4, 3, 2, 1);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=8 ;

--
-- Dumping data for table `assessment_setup_details`
--

INSERT INTO `assessment_setup_details` (`assessment_setup_detail_id`, `number`, `weight_point`, `percentage`, `assessment_setup_id`, `submission_date`, `description`) VALUES
(1, 1, 10.00, 25, 3, '2016-07-15', 'first test'),
(2, 2, 20.00, 50, 3, '2016-07-15', 'mid-term test'),
(3, 3, 10.00, 25, 3, '2016-07-15', 'final test'),
(5, 1, 10.00, 25, 4, '2016-07-15', 'first test'),
(6, 2, 20.00, 50, 4, '2016-07-15', 'mid-term test'),
(7, 3, 10.00, 25, 4, '2016-07-15', 'final test');

--
-- Constraints for dumped tables
--

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

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
