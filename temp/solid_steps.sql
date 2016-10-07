-- phpMyAdmin SQL Dump
-- version 4.5.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 30, 2016 at 01:31 PM
-- Server version: 10.1.13-MariaDB
-- PHP Version: 5.6.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `solid_steps`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`ekaruztech_user`@`%` PROCEDURE `sp_cloneSubjectsAssigned` (IN `TermFromID` INT, IN `TermToID` INT)  BEGIN
		SET @Exist = (SELECT COUNT(*) FROM subject_classrooms WHERE academic_term_id=TermToID);

	IF @Exist = 0 THEN
		Block1: BEGIN
			DECLARE done1 BOOLEAN DEFAULT FALSE;
			DECLARE SubClassRoomID, SubjectID, ClassRoomID, TutorID INT;
			DECLARE cur1 CURSOR FOR
				SELECT subject_classroom_id, subject_id, classroom_id, tutor_id FROM subject_classrooms
				WHERE academic_term_id=TermFromID ORDER BY subject_classroom_id;
			DECLARE CONTINUE HANDLER FOR NOT FOUND SET done1 = TRUE;

						OPEN cur1;
				REPEAT
					FETCH cur1 INTO SubClassRoomID, SubjectID, ClassRoomID, TutorID;
					IF NOT done1 THEN
						BEGIN
														SET @chk = (SELECT COUNT(*) FROM subject_classrooms WHERE subject_id=SubjectID AND classroom_id=ClassRoomID AND academic_term_id=TermToID);
							IF @chk = 0 THEN
								BEGIN
																		INSERT INTO subject_classrooms(subject_id, classroom_id, academic_term_id, tutor_id)
									VALUES(SubjectID, ClassRoomID, TermToID, TutorID);

																		SET @New_ID = LAST_INSERT_ID();

																		CALL sp_subject2Students(@New_ID);
								END;
							END IF;
						END;
					END IF;
				UNTIL done1 END REPEAT;
			CLOSE cur1;
		END Block1;
	END IF;
END$$

CREATE DEFINER=`ekaruztech_user`@`%` PROCEDURE `sp_deleteSubjectClassRoom` (IN `subjectClassroomID` INT)  BEGIN

	    DELETE FROM exam_details WHERE exam_id IN 
    (SELECT exam_id FROM exams WHERE subject_classroom_id = subjectClassroomID);
    
        DELETE FROM exams WHERE subject_classroom_id = subjectClassroomID;
    
	    DELETE FROM assessment_details WHERE assessment_id IN 
    (SELECT assessment_id FROM assessments WHERE subject_classroom_id = subjectClassroomID);
    
        DELETE FROM assessments WHERE subject_classroom_id = subjectClassroomID;
    
        DELETE FROM student_subjects WHERE subject_classroom_id = subjectClassroomID;
    
        DELETE FROM subject_classrooms WHERE subject_classroom_id = subjectClassroomID;
END$$

CREATE DEFINER=`ekaruztech_user`@`%` PROCEDURE `sp_modifyStudentsSubject` (IN `SubjectClassRoomID` INT, `StudentIDs` VARCHAR(225))  BEGIN
		DROP TEMPORARY TABLE IF EXISTS StudentTemp;
	CREATE TEMPORARY TABLE IF NOT EXISTS StudentTemp
	(
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
								INSERT INTO StudentTemp(student_id) SELECT student_id;
			END LOOP simple_loop;
		END;
	END IF;
    
    Block1: BEGIN
		        DELETE FROM student_subjects WHERE subject_classroom_id=SubjectClassRoomID
        AND student_id NOT IN (SELECT student_id FROM StudentTemp);
        
                DELETE FROM assessment_details WHERE assessment_id IN 
		(SELECT assessment_id FROM assessments WHERE subject_classroom_id = SubjectClassRoomID)
        AND student_id NOT IN (SELECT student_id FROM StudentTemp);
        
        		DELETE FROM exam_details WHERE exam_id IN
		(SELECT exam_id FROM exams WHERE subject_classroom_id = SubjectClassRoomID)
		AND student_id NOT IN (SELECT student_id FROM StudentTemp);

        
                INSERT INTO student_subjects(subject_classroom_id, student_id)
        SELECT SubjectClassRoomID, student_id FROM StudentTemp 
        WHERE student_id NOT IN (SELECT student_id FROM student_subjects WHERE subject_classroom_id=SubjectClassRoomID);
    END Block1;
END$$

CREATE DEFINER=`ekaruztech_user`@`%` PROCEDURE `sp_populateAssessmentDetail` (IN `AssessmentID` INT)  BEGIN
	SELECT subject_classroom_id, marked INTO @SCR_ID, @MarkStatus
	FROM assessments WHERE assessment_id=AssessmentID;

			BEGIN
				INSERT INTO assessment_details(assessment_id, student_id)
			SELECT AssessmentID, student_id FROM student_subjects WHERE subject_classroom_id=@SCR_ID
			AND student_id NOT IN (SELECT student_id FROM assessment_details WHERE assessment_id=AssessmentID);

				DELETE FROM assessment_details WHERE assessment_id=AssessmentID AND student_id NOT IN
		(SELECT student_id FROM student_subjects WHERE subject_classroom_id=@SCR_ID);
	END;
	END$$

CREATE DEFINER=`ekaruztech_user`@`%` PROCEDURE `sp_processAssessmentCA` (IN `TermID` INT, IN `TutorID` INT)  Block0: BEGIN
			Block1: BEGIN
				DECLARE done1 BOOLEAN DEFAULT FALSE;
		DECLARE StudentID, ClassID, CA_WP INT;
		DECLARE StudentName VARCHAR(100);

		DECLARE cur1 CURSOR FOR
		SELECT student_id, classroom_id, ca_weight_point, student_name FROM assessment_detailsviews
        WHERE academic_term_id=TermID AND (tutor_id = TutorID OR ISNULL(TutorID) = 1) 
        GROUP BY student_id, classroom_id, ca_weight_point;

		  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done1 = TRUE;
		  		  OPEN cur1;
		  REPEAT
			FETCH cur1 INTO StudentID, ClassID, CA_WP, StudentName;
			IF NOT done1 THEN

			  				Block2: BEGIN
								DECLARE done2 BOOLEAN DEFAULT FALSE;
				DECLARE SubjectID, SubClassroom int;

				DECLARE cur2 CURSOR FOR
				  SELECT subject_id, subject_classroom_id FROM assessment_detailsviews
				  WHERE student_id=StudentID AND classroom_id=ClassID AND academic_term_id=TermID
                  AND (tutor_id = TutorID OR ISNULL(TutorID) = 1) AND marked=1
				  GROUP BY subject_id, subject_classroom_id;

					DECLARE CONTINUE HANDLER FOR NOT FOUND SET done2 = TRUE;
										OPEN cur2;
					REPEAT
					  FETCH cur2 INTO SubjectID, SubClassroom;
					  IF NOT done2 THEN

						SET @TEMP_SUM = 0.0;
												  Block3: BEGIN
						  						  DECLARE done3 BOOLEAN DEFAULT FALSE;
						  DECLARE W_CA, WW_Point, WW_Percent FLOAT;

						  DECLARE cur3 CURSOR FOR
							SELECT  score, weight_point, percentage FROM assessment_detailsviews
							WHERE student_id=StudentID AND classroom_id=ClassID AND academic_term_id=TermID AND marked=1
							AND subject_id=SubjectID AND subject_classroom_id=SubClassroom;

						  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done3 = TRUE;
						  						  OPEN cur3;
						  REPEAT
							FETCH cur3 INTO W_CA, WW_Point, WW_Percent;
							IF NOT done3 THEN
							  BEGIN
																SET @PercentSUM = (SELECT SUM(percentage) FROM assessment_detailsviews
								WHERE student_id=StudentID AND classroom_id=ClassID AND academic_term_id=TermID AND marked=1
								AND subject_id=SubjectID AND subject_classroom_id=SubClassroom);

																								SET @Temp_WP = ROUND(((WW_Percent / @PercentSUM) * CA_WP), 2);
																								SET @Temp_CA = ROUND(((W_CA / WW_Point) * @Temp_WP), 2);
																SET @TEMP_SUM = @TEMP_SUM + @TEMP_CA;

							  END;

							END IF;
						  UNTIL done3 END REPEAT;
						  CLOSE cur3;
						END Block3;
					  
						  Block3_1: BEGIN
						  						  SET @ExamDetailID = (SELECT exam_detail_id FROM exams_detailsviews
						  WHERE student_id=StudentID AND classroom_id=ClassID AND academic_term_id=TermID
								AND subject_id=SubjectID AND subject_classroom_id=SubClassroom);

						  						  UPDATE exam_details SET ca=@TEMP_SUM WHERE exam_detail_id=@ExamDetailID;

						END Block3_1;
					  END IF;
					UNTIL done2 END REPEAT;
					CLOSE cur2;
			  END Block2;

			END IF;
		  UNTIL done1 END REPEAT;
      CLOSE cur1;
    END Block1;
  END Block0$$

CREATE DEFINER=`ekaruztech_user`@`%` PROCEDURE `sp_processExams` (IN `TermID` INT, IN `TutorID` INT)  BEGIN
	    	Block0: BEGIN
				DELETE FROM exam_details WHERE exam_id IN
        (SELECT exam_id FROM exams_subjectviews WHERE academic_term_id=TermID
        AND (tutor_id = TutorID OR ISNULL(TutorID) = 1) AND marked <> 1);

				DELETE FROM exams WHERE marked <> 1 AND subject_classroom_id IN
		(SELECT subject_classroom_id FROM subject_classrooms WHERE academic_term_id=TermID 
        AND (tutor_id = TutorID OR ISNULL(TutorID) = 1) );
    END Block0;

	Block1: BEGIN
						INSERT IGNORE INTO exams(subject_classroom_id)
        SELECT a.subject_classroom_id FROM student_subjects a JOIN subject_classrooms b
		ON a.subject_classroom_id = b.subject_classroom_id
		WHERE b.academic_term_id=TermID AND (b.tutor_id = TutorID OR ISNULL(TutorID) = 1)
        AND a.subject_classroom_id NOT IN (SELECT subject_classroom_id FROM exams)
        GROUP BY a.subject_classroom_id ORDER BY a.subject_classroom_id;
    END Block1;

			Block2: BEGIN
		DECLARE done1 BOOLEAN DEFAULT FALSE;
		DECLARE ExamID, SubjectClassRoomID, ExamMarkStatusID INT;
				  DECLARE cur1 CURSOR FOR SELECT a.exam_id, a.subject_classroom_id, a.marked
		  FROM exams a INNER JOIN subject_classrooms b ON a.subject_classroom_id=b.subject_classroom_id
		  WHERE b.academic_term_id=TermID AND (tutor_id = TutorID OR ISNULL(TutorID) = 1);
		  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done1 = TRUE;

		  		  OPEN cur1;
		  REPEAT
			FETCH cur1 INTO ExamID, SubjectClassRoomID, ExamMarkStatusID;
			IF NOT done1 THEN
			  BEGIN
				IF ExamMarkStatusID <> 1 THEN
					BEGIN
												INSERT IGNORE INTO exam_details(exam_id, student_id)
						SELECT ExamID, student_id FROM	student_subjects WHERE subject_classroom_id=SubjectClassRoomID;
					END;
				ELSE
					BEGIN
												INSERT IGNORE INTO exam_details(exam_id, student_id)
						SELECT ExamID, student_id FROM 	student_subjects
						WHERE subject_classroom_id=SubjectClassRoomID AND student_id NOT IN
						(SELECT student_id FROM exam_details WHERE exam_id=ExamID);

												DELETE FROM exam_details WHERE exam_id=ExamID AND student_id NOT IN
						(SELECT student_id FROM student_subjects WHERE subject_classroom_id=SubjectClassRoomID);
					END;
				END IF;
				
										UPDATE subject_classrooms set exam_status_id=1 WHERE subject_classroom_id = SubjectClassRoomID;
			  END;
			END IF;
		  UNTIL done1 END REPEAT;
		  CLOSE cur1;
    END Block2;

    	call sp_processAssessmentCA(TermID, TutorID);

  END$$

CREATE DEFINER=`ekaruztech_user`@`%` PROCEDURE `sp_subject2Classlevels` (IN `LevelID` INT, `TermID` INT, `SubjectIDs` VARCHAR(225))  BEGIN
		DECLARE done1 BOOLEAN DEFAULT FALSE;
		DECLARE ClassID INT;
		DECLARE cur1 CURSOR FOR SELECT classroom_id FROM classrooms WHERE classlevel_id=LevelID;
		DECLARE CONTINUE HANDLER FOR NOT FOUND SET done1 = TRUE;

		OPEN cur1;
		REPEAT
			FETCH cur1 INTO ClassID;
			IF NOT done1 THEN
				BEGIN
					CALL `sp_subject2Classrooms`(ClassID, TermID, SubjectIDs);
				END;
			END IF;
		UNTIL done1 END REPEAT;
		CLOSE cur1;
	END$$

CREATE DEFINER=`ekaruztech_user`@`%` PROCEDURE `sp_subject2Classrooms` (IN `ClassID` INT, `TermID` INT, `SubjectIDs` VARCHAR(225))  BEGIN
		DROP TEMPORARY TABLE IF EXISTS SubjectTemp;
		CREATE TEMPORARY TABLE IF NOT EXISTS SubjectTemp
		(
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

								OPEN cur1;
				REPEAT
					FETCH cur1 INTO SubjectID;
					IF NOT done1 THEN
						BEGIN
							SET @Exist = (SELECT COUNT(*) FROM subject_classrooms WHERE subject_id=SubjectID
                            AND classroom_id=ClassID AND academic_term_id=TermID);
							IF @Exist = 0 THEN
								BEGIN
																		INSERT INTO subject_classrooms(subject_id, classroom_id, academic_term_id)
									VALUES(SubjectID, ClassID, TermID);

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

CREATE DEFINER=`ekaruztech_user`@`%` PROCEDURE `sp_subject2Students` (IN `subjectClassroomID` INT)  BEGIN
		SELECT classroom_id, academic_term_id INTO @ClassID, @AcademicTermID
		FROM subject_classrooms WHERE subject_classroom_id=subjectClassroomID LIMIT 1;
		SET @SubjectClassroomID = subjectClassroomID;
		
        		SELECT COUNT(*) INTO @Exist FROM student_subjects WHERE subject_classroom_id = subjectClassroomID LIMIT 1;
		IF @Exist > 0 THEN
			BEGIN
				DELETE FROM student_subjects WHERE subject_classroom_id = subjectClassroomID;
			END;
		END IF;


		BEGIN
						INSERT INTO student_subjects(student_id, subject_classroom_id)
			SELECT	b.student_id, @SubjectClassroomID
			FROM	students a INNER JOIN student_classes b ON a.student_id=b.student_id 
            INNER JOIN classrooms c ON c.classroom_id = b.classroom_id
			WHERE	b.classroom_id = @ClassID AND a.status_id = 1
			AND b.academic_year_id = (SELECT academic_year_id FROM academic_terms WHERE academic_term_id = @AcademicTermID LIMIT 1);
		END;
	END$$

CREATE DEFINER=`ekaruztech_user`@`%` PROCEDURE `sp_terminalClassPosition` (IN `AcademicTermID` INT, IN `ClassroomID` INT, IN `StudentID` INT)  BEGIN
	Block0: BEGIN
		SET @Output = 0;
		SET @Average = 0;
		SET @Count = 0;

				DROP TEMPORARY TABLE IF EXISTS TerminalClassPositionResultTable;
		CREATE TEMPORARY TABLE IF NOT EXISTS TerminalClassPositionResultTable
		(
						student_id int,
			full_name varchar(80),
            gender varchar(10),
            student_no varchar(10),
			class_id int,
			class_name varchar(50),
			academic_term_id int,
			academic_term varchar(50),
			student_sum_total float,
			exam_perfect_score int,
			class_position int,
			class_size int,
			class_average float

		);
		Block1: BEGIN
                           
						SET @ClassSize = (SELECT COUNT(*) FROM students_classroomviews
			WHERE classroom_id = ClassroomID AND academic_year_id = (
				SELECT academic_year_id FROM academic_terms WHERE academic_term_id=AcademicTermID)
			);
			SET @TempPosition = 1;
			SET @TempStudentScore = 0;
			SET @Position = 0;

				Block2: BEGIN
								DECLARE done1 BOOLEAN DEFAULT FALSE;
				DECLARE StudentID, ClassID, TermID INT;
                DECLARE Gender, StudentNo VARCHAR(10);
				DECLARE StudentName, ClassName, TermName VARCHAR(60);
				DECLARE StudentSumTotal, ExamPerfectScore FLOAT;
				
				DECLARE cur1 CURSOR FOR
					SELECT student_id, fullname, student_gender, student_no, classroom_id, classroom, academic_term_id, academic_term, SUM(student_total), SUM(weight_point_total)
					FROM exams_detailsviews WHERE academic_term_id = AcademicTermID and classroom_id = ClassroomID AND marked = 1
					GROUP BY student_id, fullname, classroom_id, classroom, academic_term_id, academic_term
					ORDER BY SUM(student_total) DESC;

				DECLARE CONTINUE HANDLER FOR NOT FOUND SET done1 = TRUE;
								OPEN cur1;
				REPEAT
					FETCH cur1 INTO StudentID, StudentName, Gender, StudentNo, ClassID, ClassName, TermID, TermName, StudentSumTotal, ExamPerfectScore;
					IF NOT done1 THEN
						BEGIN
														IF @TempStudentScore = StudentSumTotal THEN
																SET @TempPosition = @TempPosition + 1;
														ELSE
								BEGIN
																		SET @Position = @TempPosition;
																		SET @TempPosition = @TempPosition + 1;
								END;
							END IF;
							BEGIN
																INSERT INTO TerminalClassPositionResultTable
								VALUES(StudentID, StudentName, Gender, StudentNo, ClassID, ClassName, TermID, TermName, StudentSumTotal, ExamPerfectScore, @Position, @ClassSize, @Average);
							END;
														SET @TempStudentScore = StudentSumTotal;

														SET @Average = @Average + StudentSumTotal;
														SET @Count = @Count + 1;
						END;
					END IF;
				UNTIL done1 END REPEAT;
				CLOSE cur1;
			END Block2;
		END Block1;
                UPDATE TerminalClassPositionResultTable SET class_average = (@Average / @Count);
	END Block0;	
    
    IF StudentID > 0 THEN
		SELECT * FROM TerminalClassPositionResultTable WHERE student_id = StudentID;
	ELSE
		SELECT * FROM TerminalClassPositionResultTable;
    END IF;
END$$

CREATE DEFINER=`ekaruztech_user`@`%` PROCEDURE `temp_student_subjects` ()  BEGIN
	
Block2: BEGIN
	DECLARE done1 BOOLEAN DEFAULT FALSE;
	DECLARE ID INT;
	DECLARE cur1 CURSOR FOR SELECT subject_classroom_id FROM subject_classrooms;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done1 = TRUE;

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
CREATE DEFINER=`ekaruztech_user`@`%` FUNCTION `SPLIT_STR` (`x` VARCHAR(255), `delim` VARCHAR(12), `pos` INT) RETURNS VARCHAR(255) CHARSET latin1 RETURN REPLACE(SUBSTRING(SUBSTRING_INDEX(x, delim, pos),
													 LENGTH(SUBSTRING_INDEX(x, delim, pos -1)) + 1),
								 delim, '')$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `academic_terms`
--

CREATE TABLE `academic_terms` (
  `academic_term_id` int(10) UNSIGNED NOT NULL,
  `academic_term` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `status` int(10) UNSIGNED NOT NULL DEFAULT '2',
  `academic_year_id` int(10) UNSIGNED NOT NULL,
  `term_type_id` int(10) UNSIGNED NOT NULL,
  `term_begins` date DEFAULT NULL,
  `term_ends` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `academic_terms`
--

INSERT INTO `academic_terms` (`academic_term_id`, `academic_term`, `status`, `academic_year_id`, `term_type_id`, `term_begins`, `term_ends`, `created_at`, `updated_at`) VALUES
(1, 'Third Term 2015 - 2016', 2, 1, 3, '2016-04-15', '2016-07-15', '2016-05-11 15:41:04', '2016-07-25 21:46:58'),
(2, 'Second Term 2015 - 2016', 1, 1, 2, '2016-01-11', '2016-04-01', '2016-07-15 17:49:18', '2016-07-25 21:46:58'),
(3, 'First Term 2015 - 2016', 2, 1, 1, '2015-09-21', '2015-12-18', '2016-07-18 12:14:01', '2016-07-25 21:46:58');

-- --------------------------------------------------------

--
-- Table structure for table `academic_years`
--

CREATE TABLE `academic_years` (
  `academic_year_id` int(10) UNSIGNED NOT NULL,
  `academic_year` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `status` int(10) UNSIGNED NOT NULL DEFAULT '2',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `academic_years`
--

INSERT INTO `academic_years` (`academic_year_id`, `academic_year`, `status`, `created_at`, `updated_at`) VALUES
(1, '2015 - 2016', 1, '2016-05-11 15:37:05', '2016-05-11 15:37:05'),
(2, '2016 - 2017', 2, '2016-08-03 03:41:33', '2016-08-03 03:41:33');

-- --------------------------------------------------------

--
-- Table structure for table `assessments`
--

CREATE TABLE `assessments` (
  `assessment_id` int(10) UNSIGNED NOT NULL,
  `subject_classroom_id` int(10) UNSIGNED NOT NULL,
  `assessment_setup_detail_id` int(10) UNSIGNED NOT NULL,
  `marked` int(10) UNSIGNED NOT NULL DEFAULT '2'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `assessments`
--

INSERT INTO `assessments` (`assessment_id`, `subject_classroom_id`, `assessment_setup_detail_id`, `marked`) VALUES
(1, 5, 1, 1),
(2, 1, 1, 1),
(3, 3, 1, 1),
(4, 15, 1, 1),
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
(28, 66, 7, 1),
(29, 68, 5, 1),
(30, 68, 6, 1),
(31, 68, 7, 1),
(32, 70, 5, 1),
(33, 70, 6, 1),
(34, 70, 7, 1),
(35, 7, 1, 1),
(36, 3, 2, 1),
(37, 3, 3, 1),
(38, 17, 1, 1),
(39, 7, 2, 1),
(40, 17, 2, 1),
(41, 7, 3, 1),
(42, 17, 3, 1),
(43, 10, 1, 1),
(44, 31, 1, 1),
(45, 10, 2, 1),
(46, 10, 3, 1),
(47, 31, 2, 1),
(48, 31, 3, 1),
(49, 48, 1, 1),
(50, 21, 1, 1),
(51, 39, 1, 1),
(52, 21, 2, 1),
(53, 21, 3, 1),
(54, 48, 2, 1),
(55, 39, 2, 1),
(56, 24, 1, 1),
(57, 48, 3, 1),
(58, 39, 3, 1),
(59, 24, 2, 1),
(60, 24, 3, 1),
(61, 1, 2, 1),
(62, 55, 1, 1),
(63, 1, 3, 1),
(64, 2, 1, 1),
(65, 25, 1, 1),
(66, 2, 2, 1),
(67, 25, 2, 1),
(68, 55, 2, 1),
(69, 55, 3, 1),
(70, 2, 3, 1),
(71, 25, 3, 1),
(72, 11, 1, 1),
(73, 11, 2, 1),
(74, 15, 2, 1),
(75, 11, 3, 1),
(76, 15, 3, 1),
(77, 16, 1, 1),
(78, 16, 2, 1),
(79, 16, 3, 1),
(80, 30, 1, 1),
(81, 30, 2, 1),
(82, 30, 3, 1),
(83, 57, 5, 2),
(84, 45, 1, 1),
(85, 45, 2, 1),
(86, 45, 3, 1),
(87, 53, 1, 1),
(88, 53, 2, 1),
(89, 53, 3, 1),
(90, 29, 1, 1),
(91, 29, 2, 1),
(92, 29, 3, 1),
(93, 35, 1, 1),
(94, 35, 2, 1),
(95, 35, 3, 1),
(96, 38, 1, 1),
(97, 38, 2, 1),
(98, 38, 3, 1),
(99, 89, 8, 1),
(100, 90, 8, 1),
(101, 91, 8, 1),
(102, 92, 8, 1),
(103, 93, 8, 1),
(104, 94, 8, 1),
(105, 95, 8, 1),
(106, 96, 8, 1),
(107, 97, 8, 1),
(108, 98, 8, 1),
(109, 100, 8, 1),
(110, 101, 8, 1),
(111, 102, 8, 1),
(112, 157, 8, 1),
(113, 164, 8, 1),
(114, 103, 8, 1),
(115, 104, 8, 1),
(116, 105, 8, 1),
(117, 106, 8, 1),
(118, 107, 8, 1),
(119, 108, 8, 1),
(120, 110, 8, 1),
(121, 113, 8, 1),
(122, 115, 8, 1),
(123, 109, 8, 1),
(124, 111, 8, 1),
(125, 112, 8, 1),
(126, 114, 8, 1),
(127, 116, 8, 1),
(128, 156, 8, 1),
(129, 166, 8, 1),
(130, 117, 8, 1),
(131, 118, 8, 1),
(132, 119, 8, 1),
(133, 120, 8, 1),
(134, 121, 8, 1),
(135, 122, 8, 1),
(136, 123, 8, 1),
(137, 124, 8, 1),
(138, 125, 8, 1),
(139, 126, 8, 1),
(140, 127, 8, 1),
(141, 159, 8, 1),
(142, 162, 8, 1),
(143, 160, 8, 1),
(144, 161, 8, 1),
(145, 168, 8, 1),
(146, 135, 8, 1),
(147, 138, 8, 1),
(148, 129, 8, 1),
(149, 130, 8, 1),
(150, 131, 8, 1),
(151, 133, 8, 1),
(152, 140, 8, 1),
(153, 132, 8, 1),
(154, 128, 8, 1),
(155, 137, 8, 1),
(156, 136, 8, 1),
(157, 134, 8, 1),
(158, 141, 8, 1),
(159, 158, 8, 1),
(160, 139, 8, 1),
(161, 171, 8, 1),
(162, 170, 8, 1),
(163, 142, 9, 1),
(164, 150, 9, 1),
(165, 143, 9, 1),
(166, 144, 9, 1),
(167, 145, 9, 1),
(168, 147, 9, 1),
(169, 148, 9, 1),
(170, 151, 9, 1),
(171, 152, 9, 1),
(172, 153, 9, 1),
(173, 154, 9, 1),
(174, 155, 9, 1),
(175, 172, 9, 1),
(176, 99, 8, 1);

-- --------------------------------------------------------

--
-- Table structure for table `assessment_details`
--

CREATE TABLE `assessment_details` (
  `assessment_detail_id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `score` double(8,2) UNSIGNED NOT NULL DEFAULT '0.00',
  `assessment_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
(16, 1, 5.00, 2),
(17, 2, 7.00, 2),
(18, 4, 2.00, 2),
(19, 5, 7.00, 2),
(20, 6, 6.00, 2),
(21, 7, 7.00, 2),
(22, 8, 6.00, 2),
(23, 9, 7.00, 2),
(24, 10, 2.00, 2),
(25, 44, 5.00, 2),
(31, 1, 10.00, 3),
(32, 2, 10.00, 3),
(34, 4, 10.00, 3),
(35, 5, 10.00, 3),
(36, 6, 10.00, 3),
(37, 7, 10.00, 3),
(38, 8, 10.00, 3),
(39, 9, 10.00, 3),
(40, 10, 10.00, 3),
(41, 44, 10.00, 3),
(46, 11, 9.00, 4),
(47, 12, 6.00, 4),
(48, 13, 8.00, 4),
(49, 14, 4.00, 4),
(50, 15, 8.00, 4),
(51, 17, 8.00, 4),
(52, 19, 7.00, 4),
(53, 45, 8.00, 4),
(54, 47, 6.00, 4),
(61, 38, 0.00, 5),
(62, 39, 0.00, 5),
(63, 40, 0.00, 5),
(64, 41, 0.00, 5),
(65, 42, 0.00, 5),
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
(385, 42, 10.00, 28),
(386, 38, 8.00, 29),
(387, 39, 7.00, 29),
(388, 40, 8.00, 29),
(389, 41, 6.00, 29),
(390, 42, 7.00, 29),
(393, 43, 9.00, 29),
(394, 38, 15.00, 30),
(395, 39, 14.00, 30),
(396, 40, 16.00, 30),
(397, 41, 12.00, 30),
(398, 42, 13.00, 30),
(399, 43, 17.00, 30),
(401, 38, 7.00, 31),
(402, 39, 7.00, 31),
(403, 40, 7.00, 31),
(404, 41, 5.00, 31),
(405, 42, 5.00, 31),
(406, 43, 7.00, 31),
(408, 39, 7.00, 32),
(409, 43, 8.00, 32),
(411, 39, 13.00, 33),
(412, 43, 16.00, 33),
(414, 39, 7.00, 34),
(415, 43, 8.00, 34),
(417, 1, 8.00, 35),
(418, 2, 9.00, 35),
(419, 4, 4.00, 35),
(420, 5, 8.00, 35),
(421, 6, 6.00, 35),
(422, 7, 9.00, 35),
(423, 8, 9.00, 35),
(424, 9, 8.00, 35),
(425, 10, 3.00, 35),
(426, 44, 8.00, 35),
(432, 1, 10.00, 36),
(433, 2, 18.00, 36),
(434, 4, 10.00, 36),
(435, 5, 12.00, 36),
(436, 6, 14.00, 36),
(437, 7, 12.00, 36),
(438, 8, 17.00, 36),
(439, 9, 18.00, 36),
(440, 10, 10.00, 36),
(441, 44, 14.00, 36),
(447, 1, 6.00, 37),
(448, 2, 9.00, 37),
(449, 4, 4.00, 37),
(450, 5, 7.00, 37),
(451, 6, 8.00, 37),
(452, 7, 7.00, 37),
(453, 8, 7.00, 37),
(454, 9, 8.00, 37),
(455, 10, 5.00, 37),
(456, 44, 8.00, 37),
(462, 11, 10.00, 38),
(463, 12, 10.00, 38),
(464, 13, 10.00, 38),
(465, 14, 10.00, 38),
(466, 15, 10.00, 38),
(467, 17, 10.00, 38),
(468, 19, 10.00, 38),
(469, 45, 10.00, 38),
(470, 47, 10.00, 38),
(477, 1, 16.00, 39),
(478, 2, 9.00, 39),
(479, 4, 8.00, 39),
(480, 5, 16.00, 39),
(481, 6, 15.00, 39),
(482, 7, 15.00, 39),
(483, 8, 18.00, 39),
(484, 9, 16.00, 39),
(485, 10, 9.00, 39),
(486, 44, 16.00, 39),
(492, 11, 15.00, 40),
(493, 12, 20.00, 40),
(494, 13, 19.00, 40),
(495, 14, 12.00, 40),
(496, 15, 13.00, 40),
(497, 17, 18.00, 40),
(498, 19, 20.00, 40),
(499, 45, 11.00, 40),
(500, 47, 12.00, 40),
(507, 1, 8.00, 41),
(508, 2, 8.00, 41),
(509, 4, 4.00, 41),
(510, 5, 8.00, 41),
(511, 6, 7.00, 41),
(512, 7, 5.00, 41),
(513, 8, 8.00, 41),
(514, 9, 7.00, 41),
(515, 10, 3.00, 41),
(516, 44, 9.00, 41),
(522, 11, 9.00, 42),
(523, 12, 6.00, 42),
(524, 13, 7.00, 42),
(525, 14, 7.00, 42),
(526, 15, 6.00, 42),
(527, 17, 7.00, 42),
(528, 19, 9.00, 42),
(529, 45, 8.00, 42),
(530, 47, 7.00, 42),
(537, 1, 9.00, 43),
(538, 2, 9.00, 43),
(539, 4, 9.00, 43),
(540, 5, 8.00, 43),
(541, 6, 10.00, 43),
(542, 7, 8.00, 43),
(543, 8, 8.00, 43),
(544, 9, 8.00, 43),
(545, 10, 8.00, 43),
(546, 44, 8.00, 43),
(552, 20, 9.00, 44),
(553, 21, 9.00, 44),
(554, 22, 8.00, 44),
(555, 23, 6.00, 44),
(556, 24, 9.00, 44),
(557, 25, 8.00, 44),
(558, 26, 7.00, 44),
(559, 27, 7.00, 44),
(560, 28, 7.00, 44),
(567, 1, 15.00, 45),
(568, 2, 18.00, 45),
(569, 4, 15.00, 45),
(570, 5, 16.00, 45),
(571, 6, 17.00, 45),
(572, 7, 16.00, 45),
(573, 8, 16.00, 45),
(574, 9, 16.00, 45),
(575, 10, 12.00, 45),
(576, 44, 16.00, 45),
(582, 1, 5.00, 46),
(583, 2, 8.00, 46),
(584, 4, 5.00, 46),
(585, 5, 9.00, 46),
(586, 6, 10.00, 46),
(587, 7, 9.00, 46),
(588, 8, 9.00, 46),
(589, 9, 9.00, 46),
(590, 10, 7.00, 46),
(591, 44, 8.00, 46),
(597, 20, 16.00, 47),
(598, 21, 18.00, 47),
(599, 22, 15.00, 47),
(600, 23, 13.00, 47),
(601, 24, 16.00, 47),
(602, 25, 16.00, 47),
(603, 26, 14.00, 47),
(604, 27, 14.00, 47),
(605, 28, 13.00, 47),
(612, 20, 8.00, 48),
(613, 21, 9.00, 48),
(614, 22, 7.00, 48),
(615, 23, 6.00, 48),
(616, 24, 8.00, 48),
(617, 25, 7.00, 48),
(618, 26, 6.00, 48),
(619, 27, 7.00, 48),
(620, 28, 6.00, 48),
(627, 29, 7.00, 49),
(628, 30, 10.00, 49),
(629, 32, 7.00, 49),
(630, 33, 9.00, 49),
(631, 34, 8.00, 49),
(632, 35, 6.00, 49),
(633, 36, 10.00, 49),
(634, 37, 9.00, 49),
(642, 11, 5.00, 50),
(643, 12, 5.00, 50),
(644, 13, 10.00, 50),
(645, 14, 4.00, 50),
(646, 15, 5.00, 50),
(647, 17, 5.00, 50),
(648, 19, 7.00, 50),
(649, 45, 8.00, 50),
(650, 47, 5.00, 50),
(657, 20, 10.00, 51),
(658, 21, 10.00, 51),
(659, 22, 10.00, 51),
(660, 23, 10.00, 51),
(661, 24, 10.00, 51),
(662, 25, 10.00, 51),
(663, 26, 10.00, 51),
(664, 27, 10.00, 51),
(665, 28, 10.00, 51),
(672, 11, 15.00, 52),
(673, 12, 18.00, 52),
(674, 13, 14.00, 52),
(675, 14, 10.00, 52),
(676, 15, 10.00, 52),
(677, 17, 12.00, 52),
(678, 19, 20.00, 52),
(679, 45, 15.00, 52),
(680, 47, 13.00, 52),
(687, 11, 5.00, 53),
(688, 12, 5.00, 53),
(689, 13, 10.00, 53),
(690, 14, 4.00, 53),
(691, 15, 4.00, 53),
(692, 17, 5.00, 53),
(693, 19, 7.00, 53),
(694, 45, 8.00, 53),
(695, 47, 5.00, 53),
(702, 29, 14.00, 54),
(703, 30, 12.00, 54),
(704, 32, 14.00, 54),
(705, 33, 10.00, 54),
(706, 34, 10.00, 54),
(707, 35, 12.00, 54),
(708, 36, 14.00, 54),
(709, 37, 14.00, 54),
(717, 20, 10.00, 55),
(718, 21, 16.00, 55),
(719, 22, 12.00, 55),
(720, 23, 10.00, 55),
(721, 24, 14.00, 55),
(722, 25, 13.00, 55),
(723, 26, 9.00, 55),
(724, 27, 10.00, 55),
(725, 28, 10.00, 55),
(732, 11, 9.00, 56),
(733, 12, 8.00, 56),
(734, 13, 9.00, 56),
(735, 14, 5.00, 56),
(736, 15, 5.00, 56),
(737, 17, 5.00, 56),
(738, 19, 5.00, 56),
(739, 45, 5.00, 56),
(740, 47, 5.00, 56),
(747, 29, 7.00, 57),
(748, 30, 10.00, 57),
(749, 32, 7.00, 57),
(750, 33, 10.00, 57),
(751, 34, 7.00, 57),
(752, 35, 6.00, 57),
(753, 36, 10.00, 57),
(754, 37, 9.00, 57),
(762, 20, 5.00, 58),
(763, 21, 8.00, 58),
(764, 22, 0.00, 58),
(765, 23, 5.00, 58),
(766, 24, 6.00, 58),
(767, 25, 5.00, 58),
(768, 26, 5.00, 58),
(769, 27, 5.00, 58),
(770, 28, 5.00, 58),
(777, 11, 15.00, 59),
(778, 12, 16.00, 59),
(779, 13, 20.00, 59),
(780, 14, 10.00, 59),
(781, 15, 10.00, 59),
(782, 17, 15.00, 59),
(783, 19, 15.00, 59),
(784, 45, 10.00, 59),
(785, 47, 10.00, 59),
(792, 11, 8.00, 60),
(793, 12, 9.00, 60),
(794, 13, 9.00, 60),
(795, 14, 5.00, 60),
(796, 15, 4.00, 60),
(797, 17, 4.00, 60),
(798, 19, 4.00, 60),
(799, 45, 3.00, 60),
(800, 47, 5.00, 60),
(807, 1, 16.00, 61),
(808, 2, 19.00, 61),
(809, 4, 15.00, 61),
(810, 5, 19.00, 61),
(811, 6, 18.00, 61),
(812, 7, 18.00, 61),
(813, 8, 18.00, 61),
(814, 9, 18.00, 61),
(815, 10, 10.00, 61),
(816, 44, 19.00, 61),
(822, 29, 5.00, 62),
(823, 30, 8.00, 62),
(824, 32, 4.00, 62),
(825, 33, 7.00, 62),
(826, 34, 5.00, 62),
(827, 35, 5.00, 62),
(828, 36, 8.00, 62),
(829, 37, 7.00, 62),
(837, 1, 5.00, 63),
(838, 2, 10.00, 63),
(839, 4, 3.00, 63),
(840, 5, 10.00, 63),
(841, 6, 10.00, 63),
(842, 7, 7.00, 63),
(843, 8, 6.00, 63),
(844, 9, 7.00, 63),
(845, 10, 3.00, 63),
(846, 44, 5.00, 63),
(852, 1, 8.00, 64),
(853, 2, 8.00, 64),
(854, 4, 4.00, 64),
(855, 5, 7.00, 64),
(856, 6, 7.00, 64),
(857, 7, 6.00, 64),
(858, 8, 7.00, 64),
(859, 9, 8.00, 64),
(860, 10, 2.00, 64),
(861, 44, 4.00, 64),
(867, 11, 10.00, 65),
(868, 12, 10.00, 65),
(869, 13, 10.00, 65),
(870, 14, 10.00, 65),
(871, 15, 10.00, 65),
(872, 17, 10.00, 65),
(873, 19, 10.00, 65),
(874, 45, 10.00, 65),
(875, 47, 10.00, 65),
(882, 1, 16.00, 66),
(883, 2, 19.00, 66),
(884, 4, 16.00, 66),
(885, 5, 19.00, 66),
(886, 6, 19.00, 66),
(887, 7, 19.00, 66),
(888, 8, 19.00, 66),
(889, 9, 19.00, 66),
(890, 10, 10.00, 66),
(891, 44, 19.00, 66),
(897, 11, 14.00, 67),
(898, 12, 16.00, 67),
(899, 13, 11.00, 67),
(900, 14, 10.00, 67),
(901, 15, 9.00, 67),
(902, 17, 13.00, 67),
(903, 19, 18.00, 67),
(904, 45, 12.00, 67),
(905, 47, 15.00, 67),
(912, 29, 14.00, 68),
(913, 30, 15.00, 68),
(914, 32, 10.00, 68),
(915, 33, 10.00, 68),
(916, 34, 9.00, 68),
(917, 35, 11.00, 68),
(918, 36, 15.00, 68),
(919, 37, 13.00, 68),
(927, 29, 5.00, 69),
(928, 30, 7.00, 69),
(929, 32, 4.00, 69),
(930, 33, 8.00, 69),
(931, 34, 4.00, 69),
(932, 35, 5.00, 69),
(933, 36, 9.00, 69),
(934, 37, 7.00, 69),
(942, 1, 8.00, 70),
(943, 2, 8.00, 70),
(944, 4, 4.00, 70),
(945, 5, 9.00, 70),
(946, 6, 7.00, 70),
(947, 7, 6.00, 70),
(948, 8, 7.00, 70),
(949, 9, 8.00, 70),
(950, 10, 2.00, 70),
(951, 44, 4.00, 70),
(957, 11, 6.00, 71),
(958, 12, 7.00, 71),
(959, 13, 5.00, 71),
(960, 14, 5.00, 71),
(961, 15, 5.00, 71),
(962, 17, 5.00, 71),
(963, 19, 8.00, 71),
(964, 45, 5.00, 71),
(965, 47, 7.00, 71),
(972, 1, 10.00, 72),
(973, 2, 10.00, 72),
(974, 4, 10.00, 72),
(975, 5, 10.00, 72),
(976, 6, 10.00, 72),
(977, 7, 10.00, 72),
(978, 8, 10.00, 72),
(979, 9, 10.00, 72),
(980, 10, 10.00, 72),
(981, 44, 10.00, 72),
(987, 1, 10.00, 73),
(988, 2, 14.00, 73),
(989, 4, 7.00, 73),
(990, 5, 15.00, 73),
(991, 6, 11.00, 73),
(992, 7, 15.00, 73),
(993, 8, 17.00, 73),
(994, 9, 16.00, 73),
(995, 10, 13.00, 73),
(996, 44, 13.00, 73),
(1002, 11, 19.00, 74),
(1003, 12, 19.00, 74),
(1004, 13, 19.00, 74),
(1005, 14, 19.00, 74),
(1006, 15, 19.00, 74),
(1007, 17, 19.00, 74),
(1008, 19, 19.00, 74),
(1009, 45, 19.00, 74),
(1010, 47, 19.00, 74),
(1017, 1, 5.00, 75),
(1018, 2, 6.00, 75),
(1019, 4, 5.00, 75),
(1020, 5, 7.00, 75),
(1021, 6, 5.00, 75),
(1022, 7, 8.00, 75),
(1023, 8, 8.00, 75),
(1024, 9, 7.00, 75),
(1025, 10, 6.00, 75),
(1026, 44, 6.00, 75),
(1032, 11, 9.00, 76),
(1033, 12, 6.00, 76),
(1034, 13, 8.00, 76),
(1035, 14, 4.00, 76),
(1036, 15, 8.00, 76),
(1037, 17, 9.00, 76),
(1038, 19, 8.00, 76),
(1039, 45, 8.00, 76),
(1040, 47, 6.00, 76),
(1047, 11, 7.00, 77),
(1048, 12, 5.00, 77),
(1049, 13, 9.00, 77),
(1050, 14, 6.00, 77),
(1051, 15, 5.00, 77),
(1052, 17, 7.00, 77),
(1053, 19, 7.00, 77),
(1054, 45, 8.00, 77),
(1062, 11, 19.00, 78),
(1063, 12, 18.00, 78),
(1064, 13, 19.00, 78),
(1065, 14, 19.00, 78),
(1066, 15, 16.00, 78),
(1067, 17, 18.00, 78),
(1068, 19, 19.00, 78),
(1069, 45, 17.00, 78),
(1077, 11, 8.00, 79),
(1078, 12, 5.00, 79),
(1079, 13, 9.00, 79),
(1080, 14, 6.00, 79),
(1081, 15, 5.00, 79),
(1082, 17, 8.00, 79),
(1083, 19, 7.00, 79),
(1084, 45, 8.00, 79),
(1092, 20, 5.00, 80),
(1093, 21, 7.00, 80),
(1094, 22, 8.00, 80),
(1095, 23, 5.00, 80),
(1096, 24, 9.00, 80),
(1097, 25, 5.00, 80),
(1098, 26, 3.00, 80),
(1099, 27, 4.00, 80),
(1100, 28, 6.00, 80),
(1107, 20, 15.00, 81),
(1108, 21, 20.00, 81),
(1109, 22, 18.00, 81),
(1110, 23, 18.00, 81),
(1111, 24, 20.00, 81),
(1112, 25, 18.00, 81),
(1113, 26, 15.00, 81),
(1114, 27, 15.00, 81),
(1115, 28, 17.00, 81),
(1122, 20, 5.00, 82),
(1123, 21, 7.00, 82),
(1124, 22, 7.00, 82),
(1125, 23, 5.00, 82),
(1126, 24, 9.00, 82),
(1127, 25, 5.00, 82),
(1128, 26, 3.00, 82),
(1129, 27, 3.00, 82),
(1130, 28, 6.00, 82),
(1137, 38, 0.00, 83),
(1138, 39, 0.00, 83),
(1139, 40, 0.00, 83),
(1140, 41, 0.00, 83),
(1141, 42, 0.00, 83),
(1142, 43, 0.00, 83),
(1144, 29, 5.00, 84),
(1145, 30, 9.00, 84),
(1146, 32, 8.00, 84),
(1147, 33, 5.00, 84),
(1148, 34, 2.00, 84),
(1149, 35, 9.00, 84),
(1150, 36, 9.00, 84),
(1151, 37, 9.00, 84),
(1159, 29, 10.00, 85),
(1160, 30, 19.00, 85),
(1161, 32, 10.00, 85),
(1162, 33, 14.00, 85),
(1163, 34, 8.00, 85),
(1164, 35, 14.00, 85),
(1165, 36, 12.00, 85),
(1166, 37, 10.00, 85),
(1174, 29, 5.00, 86),
(1175, 30, 9.00, 86),
(1176, 32, 8.00, 86),
(1177, 33, 6.00, 86),
(1178, 34, 2.00, 86),
(1179, 35, 8.00, 86),
(1180, 36, 9.00, 86),
(1181, 37, 7.00, 86),
(1189, 29, 2.00, 87),
(1190, 30, 8.00, 87),
(1191, 32, 4.00, 87),
(1192, 33, 5.00, 87),
(1193, 34, 6.00, 87),
(1194, 35, 5.00, 87),
(1195, 36, 7.00, 87),
(1196, 37, 5.00, 87),
(1204, 29, 10.00, 88),
(1205, 30, 19.00, 88),
(1206, 32, 10.00, 88),
(1207, 33, 10.00, 88),
(1208, 34, 12.00, 88),
(1209, 35, 11.00, 88),
(1210, 36, 15.00, 88),
(1211, 37, 10.00, 88),
(1219, 29, 2.00, 89),
(1220, 30, 6.00, 89),
(1221, 32, 6.00, 89),
(1222, 33, 5.00, 89),
(1223, 34, 7.00, 89),
(1224, 35, 6.00, 89),
(1225, 36, 8.00, 89),
(1226, 37, 5.00, 89),
(1234, 20, 10.00, 90),
(1235, 21, 10.00, 90),
(1236, 22, 10.00, 90),
(1237, 23, 9.00, 90),
(1238, 24, 9.00, 90),
(1239, 25, 9.00, 90),
(1240, 26, 9.00, 90),
(1241, 27, 9.00, 90),
(1242, 28, 10.00, 90),
(1249, 20, 15.00, 91),
(1250, 21, 15.00, 91),
(1251, 22, 15.00, 91),
(1252, 23, 9.00, 91),
(1253, 24, 15.00, 91),
(1254, 25, 15.00, 91),
(1255, 26, 12.00, 91),
(1256, 27, 10.00, 91),
(1257, 28, 15.00, 91),
(1264, 20, 10.00, 92),
(1265, 21, 10.00, 92),
(1266, 22, 9.00, 92),
(1267, 23, 9.00, 92),
(1268, 24, 10.00, 92),
(1269, 25, 9.00, 92),
(1270, 26, 4.00, 92),
(1271, 27, 10.00, 92),
(1272, 28, 10.00, 92),
(1279, 20, 5.00, 93),
(1280, 21, 10.00, 93),
(1281, 22, 9.00, 93),
(1282, 23, 9.00, 93),
(1283, 24, 9.00, 93),
(1284, 25, 9.00, 93),
(1285, 26, 4.00, 93),
(1286, 27, 5.00, 93),
(1287, 28, 4.00, 93),
(1294, 20, 9.00, 94),
(1295, 21, 16.00, 94),
(1296, 22, 10.00, 94),
(1297, 23, 11.00, 94),
(1298, 24, 18.00, 94),
(1299, 25, 11.00, 94),
(1300, 26, 9.00, 94),
(1301, 27, 7.00, 94),
(1302, 28, 10.00, 94),
(1309, 20, 6.00, 95),
(1310, 21, 10.00, 95),
(1311, 22, 10.00, 95),
(1312, 23, 4.00, 95),
(1313, 24, 10.00, 95),
(1314, 25, 4.00, 95),
(1315, 26, 3.00, 95),
(1316, 27, 5.00, 95),
(1317, 28, 5.00, 95),
(1324, 20, 5.00, 96),
(1325, 21, 10.00, 96),
(1326, 22, 9.00, 96),
(1327, 23, 9.00, 96),
(1328, 24, 9.00, 96),
(1329, 25, 9.00, 96),
(1330, 26, 4.00, 96),
(1331, 27, 5.00, 96),
(1332, 28, 4.00, 96),
(1339, 20, 9.00, 97),
(1340, 21, 16.00, 97),
(1341, 22, 10.00, 97),
(1342, 23, 11.00, 97),
(1343, 24, 18.00, 97),
(1344, 25, 11.00, 97),
(1345, 26, 9.00, 97),
(1346, 27, 7.00, 97),
(1347, 28, 10.00, 97),
(1354, 20, 6.00, 98),
(1355, 21, 10.00, 98),
(1356, 22, 10.00, 98),
(1357, 23, 4.00, 98),
(1358, 24, 10.00, 98),
(1359, 25, 4.00, 98),
(1360, 26, 3.00, 98),
(1361, 27, 5.00, 98),
(1362, 28, 5.00, 98),
(1369, 1, 24.00, 99),
(1370, 2, 32.00, 99),
(1371, 4, 24.00, 99),
(1372, 5, 34.00, 99),
(1373, 6, 29.00, 99),
(1374, 7, 31.00, 99),
(1375, 8, 26.00, 99),
(1376, 9, 36.00, 99),
(1377, 10, 12.00, 99),
(1378, 44, 33.00, 99),
(1384, 1, 33.00, 100),
(1385, 2, 38.00, 100),
(1386, 4, 21.00, 100),
(1387, 5, 38.00, 100),
(1388, 6, 36.00, 100),
(1389, 7, 35.00, 100),
(1390, 8, 37.00, 100),
(1391, 9, 35.00, 100),
(1392, 10, 10.00, 100),
(1393, 44, 36.00, 100),
(1399, 1, 20.00, 101),
(1400, 2, 36.00, 101),
(1401, 4, 20.00, 101),
(1402, 5, 26.00, 101),
(1403, 6, 30.00, 101),
(1404, 7, 26.00, 101),
(1405, 8, 34.00, 101),
(1406, 9, 36.00, 101),
(1407, 10, 20.00, 101),
(1408, 44, 30.00, 101),
(1414, 1, 20.00, 102),
(1415, 2, 34.00, 102),
(1416, 4, 19.00, 102),
(1417, 5, 25.00, 102),
(1418, 6, 28.00, 102),
(1419, 7, 36.00, 102),
(1420, 8, 22.00, 102),
(1421, 9, 29.00, 102),
(1422, 10, 8.00, 102),
(1423, 44, 23.00, 102),
(1429, 1, 24.00, 103),
(1430, 2, 32.00, 103),
(1431, 4, 24.00, 103),
(1432, 5, 34.00, 103),
(1433, 6, 29.00, 103),
(1434, 7, 31.00, 103),
(1435, 8, 26.00, 103),
(1436, 9, 36.00, 103),
(1437, 10, 12.00, 103),
(1438, 44, 26.00, 103),
(1444, 1, 22.00, 104),
(1445, 2, 25.00, 104),
(1446, 4, 24.00, 104),
(1447, 5, 25.00, 104),
(1448, 6, 25.00, 104),
(1449, 7, 24.00, 104),
(1450, 8, 24.00, 104),
(1451, 9, 27.00, 104),
(1452, 10, 29.00, 104),
(1453, 44, 26.00, 104),
(1459, 1, 34.00, 105),
(1460, 2, 32.00, 105),
(1461, 4, 19.00, 105),
(1462, 5, 40.00, 105),
(1463, 6, 36.00, 105),
(1464, 7, 34.00, 105),
(1465, 8, 36.00, 105),
(1466, 9, 38.00, 105),
(1467, 10, 16.00, 105),
(1468, 44, 30.00, 105),
(1474, 1, 30.00, 106),
(1475, 2, 23.00, 106),
(1476, 4, 25.00, 106),
(1477, 5, 26.00, 106),
(1478, 6, 26.00, 106),
(1479, 7, 26.00, 106),
(1480, 8, 22.00, 106),
(1481, 9, 26.00, 106),
(1482, 10, 13.00, 106),
(1483, 44, 21.00, 106),
(1489, 1, 25.00, 107),
(1490, 2, 30.00, 107),
(1491, 4, 25.00, 107),
(1492, 5, 23.00, 107),
(1493, 6, 30.00, 107),
(1494, 7, 31.00, 107),
(1495, 8, 25.00, 107),
(1496, 9, 35.00, 107),
(1497, 10, 26.00, 107),
(1498, 44, 28.00, 107),
(1504, 1, 32.00, 108),
(1505, 2, 36.00, 108),
(1506, 4, 26.00, 108),
(1507, 5, 30.00, 108),
(1508, 6, 40.00, 108),
(1509, 7, 36.00, 108),
(1510, 8, 25.00, 108),
(1511, 9, 35.00, 108),
(1512, 10, 26.00, 108),
(1513, 44, 26.00, 108),
(1519, 1, 20.00, 109),
(1520, 2, 40.00, 109),
(1521, 4, 10.00, 109),
(1522, 5, 35.00, 109),
(1523, 6, 38.00, 109),
(1524, 7, 36.00, 109),
(1525, 8, 34.00, 109),
(1526, 9, 30.00, 109),
(1527, 10, 26.00, 109),
(1528, 44, 30.00, 109),
(1534, 1, 21.00, 110),
(1535, 2, 35.00, 110),
(1536, 4, 26.00, 110),
(1537, 5, 28.00, 110),
(1538, 6, 31.00, 110),
(1539, 7, 38.00, 110),
(1540, 8, 31.00, 110),
(1541, 9, 28.00, 110),
(1542, 10, 12.00, 110),
(1543, 44, 22.00, 110),
(1549, 1, 32.00, 111),
(1550, 2, 33.00, 111),
(1551, 4, 30.00, 111),
(1552, 5, 36.00, 111),
(1553, 6, 32.00, 111),
(1554, 7, 30.00, 111),
(1555, 8, 36.00, 111),
(1556, 9, 33.00, 111),
(1557, 10, 31.00, 111),
(1558, 44, 35.00, 111),
(1564, 1, 40.00, 112),
(1565, 2, 40.00, 112),
(1566, 4, 40.00, 112),
(1567, 5, 40.00, 112),
(1568, 6, 40.00, 112),
(1569, 7, 40.00, 112),
(1570, 8, 40.00, 112),
(1571, 9, 40.00, 112),
(1572, 10, 40.00, 112),
(1573, 44, 40.00, 112),
(1579, 1, 10.00, 113),
(1580, 2, 20.00, 113),
(1581, 4, 10.00, 113),
(1582, 5, 28.00, 113),
(1583, 6, 10.00, 113),
(1584, 7, 20.00, 113),
(1585, 8, 20.00, 113),
(1586, 9, 15.00, 113),
(1587, 10, 6.00, 113),
(1588, 44, 20.00, 113),
(1594, 11, 36.00, 114),
(1595, 12, 31.00, 114),
(1596, 13, 34.00, 114),
(1597, 14, 24.00, 114),
(1598, 15, 29.00, 114),
(1599, 17, 30.00, 114),
(1600, 19, 38.00, 114),
(1601, 45, 33.00, 114),
(1602, 47, 30.00, 114),
(1609, 11, 36.00, 115),
(1610, 12, 29.00, 115),
(1611, 13, 37.00, 115),
(1612, 14, 32.00, 115),
(1613, 15, 21.00, 115),
(1614, 17, 35.00, 115),
(1615, 19, 39.00, 115),
(1616, 45, 35.00, 115),
(1617, 47, 33.00, 115),
(1624, 11, 30.00, 116),
(1625, 12, 34.00, 116),
(1626, 13, 34.00, 116),
(1627, 14, 24.00, 116),
(1628, 15, 26.00, 116),
(1629, 17, 32.00, 116),
(1630, 19, 38.00, 116),
(1631, 45, 24.00, 116),
(1632, 47, 24.00, 116),
(1639, 11, 23.00, 117),
(1640, 12, 22.00, 117),
(1641, 13, 33.00, 117),
(1642, 14, 18.00, 117),
(1643, 15, 27.00, 117),
(1644, 17, 25.00, 117),
(1645, 19, 27.00, 117),
(1646, 45, 18.00, 117),
(1647, 47, 29.00, 117),
(1654, 11, 35.00, 118),
(1655, 12, 30.00, 118),
(1656, 13, 27.00, 118),
(1657, 14, 31.00, 118),
(1658, 15, 27.00, 118),
(1659, 17, 26.00, 118),
(1660, 19, 31.00, 118),
(1661, 45, 26.00, 118),
(1662, 47, 31.00, 118),
(1669, 11, 24.00, 119),
(1670, 12, 24.00, 119),
(1671, 13, 24.00, 119),
(1672, 14, 23.00, 119),
(1673, 15, 24.00, 119),
(1674, 17, 23.00, 119),
(1675, 19, 25.00, 119),
(1676, 45, 29.00, 119),
(1677, 47, 26.00, 119),
(1684, 11, 24.00, 120),
(1685, 12, 19.00, 120),
(1686, 13, 25.00, 120),
(1687, 14, 28.00, 120),
(1688, 15, 22.00, 120),
(1689, 17, 23.00, 120),
(1690, 19, 27.00, 120),
(1691, 45, 17.00, 120),
(1692, 47, 17.00, 120),
(1699, 11, 30.00, 121),
(1700, 12, 28.00, 121),
(1701, 13, 27.00, 121),
(1702, 14, 26.00, 121),
(1703, 15, 22.00, 121),
(1704, 17, 30.00, 121),
(1705, 19, 34.00, 121),
(1706, 45, 31.00, 121),
(1707, 47, 31.00, 121),
(1714, 11, 31.00, 122),
(1715, 12, 35.00, 122),
(1716, 13, 33.00, 122),
(1717, 14, 18.00, 122),
(1718, 15, 20.00, 122),
(1719, 17, 26.00, 122),
(1720, 19, 31.00, 122),
(1721, 45, 27.00, 122),
(1722, 47, 27.00, 122),
(1729, 11, 30.00, 123),
(1730, 12, 34.00, 123),
(1731, 13, 32.00, 123),
(1732, 14, 14.00, 123),
(1733, 15, 22.00, 123),
(1734, 17, 26.00, 123),
(1735, 19, 34.00, 123),
(1736, 45, 20.00, 123),
(1737, 47, 22.00, 123),
(1744, 11, 31.00, 124),
(1745, 12, 28.00, 124),
(1746, 13, 35.00, 124),
(1747, 14, 25.00, 124),
(1748, 15, 26.00, 124),
(1749, 17, 29.00, 124),
(1750, 19, 33.00, 124),
(1751, 45, 26.00, 124),
(1752, 47, 25.00, 124),
(1759, 11, 40.00, 125),
(1760, 12, 40.00, 125),
(1761, 13, 40.00, 125),
(1762, 14, 16.00, 125),
(1763, 15, 40.00, 125),
(1764, 17, 25.00, 125),
(1765, 19, 40.00, 125),
(1766, 45, 18.00, 125),
(1767, 47, 38.00, 125),
(1774, 11, 40.00, 126),
(1775, 12, 40.00, 126),
(1776, 13, 40.00, 126),
(1777, 14, 20.00, 126),
(1778, 15, 30.00, 126),
(1779, 17, 28.00, 126),
(1780, 19, 38.00, 126),
(1781, 45, 35.00, 126),
(1782, 47, 35.00, 126),
(1789, 11, 34.00, 127),
(1790, 12, 32.00, 127),
(1791, 13, 31.00, 127),
(1792, 14, 31.00, 127),
(1793, 15, 36.00, 127),
(1794, 17, 35.00, 127),
(1795, 19, 37.00, 127),
(1796, 45, 32.00, 127),
(1797, 47, 34.00, 127),
(1798, 11, 40.00, 128),
(1799, 12, 40.00, 128),
(1800, 13, 40.00, 128),
(1801, 14, 40.00, 128),
(1802, 15, 40.00, 128),
(1803, 17, 40.00, 128),
(1804, 19, 40.00, 128),
(1805, 45, 40.00, 128),
(1806, 47, 40.00, 128),
(1813, 11, 20.00, 129),
(1814, 12, 28.00, 129),
(1815, 13, 31.00, 129),
(1816, 14, 20.00, 129),
(1817, 15, 15.00, 129),
(1818, 17, 20.00, 129),
(1819, 19, 31.00, 129),
(1820, 45, 20.00, 129),
(1821, 47, 20.00, 129),
(1828, 20, 29.00, 130),
(1829, 21, 40.00, 130),
(1830, 22, 33.00, 130),
(1831, 23, 30.00, 130),
(1832, 24, 38.00, 130),
(1833, 25, 29.00, 130),
(1834, 26, 27.00, 130),
(1835, 27, 30.00, 130),
(1836, 28, 30.00, 130),
(1843, 20, 25.00, 131),
(1844, 21, 39.00, 131),
(1845, 22, 33.00, 131),
(1846, 23, 19.00, 131),
(1847, 24, 38.00, 131),
(1848, 25, 23.00, 131),
(1849, 26, 20.00, 131),
(1850, 27, 24.00, 131),
(1851, 28, 23.00, 131),
(1858, 20, 28.00, 132),
(1859, 21, 34.00, 132),
(1860, 22, 26.00, 132),
(1861, 23, 20.00, 132),
(1862, 24, 30.00, 132),
(1863, 25, 21.00, 132),
(1864, 26, 20.00, 132),
(1865, 27, 22.00, 132),
(1866, 28, 21.00, 132),
(1873, 20, 23.00, 133),
(1874, 21, 35.00, 133),
(1875, 22, 30.00, 133),
(1876, 23, 23.00, 133),
(1877, 24, 37.00, 133),
(1878, 25, 31.00, 133),
(1879, 26, 23.00, 133),
(1880, 27, 24.00, 133),
(1881, 28, 32.00, 133),
(1888, 20, 17.00, 134),
(1889, 21, 33.00, 134),
(1890, 22, 32.00, 134),
(1892, 24, 36.00, 134),
(1893, 25, 29.00, 134),
(1894, 26, 20.00, 134),
(1895, 27, 24.00, 134),
(1896, 28, 26.00, 134),
(1903, 20, 25.00, 135),
(1904, 21, 38.00, 135),
(1905, 22, 37.00, 135),
(1906, 23, 27.00, 135),
(1907, 24, 37.00, 135),
(1908, 25, 32.00, 135),
(1909, 26, 28.00, 135),
(1910, 27, 28.00, 135),
(1911, 28, 28.00, 135),
(1918, 20, 27.00, 136),
(1919, 21, 40.00, 136),
(1920, 22, 26.00, 136),
(1921, 23, 28.00, 136),
(1922, 24, 39.00, 136),
(1923, 25, 27.00, 136),
(1924, 26, 26.00, 136),
(1925, 27, 25.00, 136),
(1926, 28, 35.00, 136),
(1933, 20, 22.00, 137),
(1934, 21, 29.00, 137),
(1935, 22, 23.00, 137),
(1936, 23, 26.00, 137),
(1937, 24, 28.00, 137),
(1938, 25, 23.00, 137),
(1939, 26, 28.00, 137),
(1940, 27, 20.00, 137),
(1941, 28, 24.00, 137),
(1948, 20, 31.00, 138),
(1949, 21, 35.00, 138),
(1950, 22, 32.00, 138),
(1951, 23, 31.00, 138),
(1952, 24, 35.00, 138),
(1953, 25, 30.00, 138),
(1954, 26, 29.00, 138),
(1955, 27, 26.00, 138),
(1956, 28, 30.00, 138),
(1963, 20, 22.00, 139),
(1964, 21, 40.00, 139),
(1965, 22, 34.00, 139),
(1966, 23, 22.00, 139),
(1967, 24, 38.00, 139),
(1968, 25, 34.00, 139),
(1969, 26, 24.00, 139),
(1970, 27, 16.00, 139),
(1971, 28, 31.00, 139),
(1978, 20, 30.00, 140),
(1979, 21, 35.00, 140),
(1980, 22, 32.00, 140),
(1981, 23, 21.00, 140),
(1982, 24, 28.00, 140),
(1983, 25, 25.00, 140),
(1984, 26, 20.00, 140),
(1985, 27, 27.00, 140),
(1986, 28, 28.00, 140),
(1993, 20, 40.00, 141),
(1994, 21, 40.00, 141),
(1995, 22, 40.00, 141),
(1996, 23, 40.00, 141),
(1997, 24, 40.00, 141),
(1998, 25, 40.00, 141),
(1999, 26, 40.00, 141),
(2000, 27, 40.00, 141),
(2001, 28, 40.00, 141),
(2008, 20, 31.00, 142),
(2009, 21, 35.00, 142),
(2010, 22, 31.00, 142),
(2011, 23, 32.00, 142),
(2012, 24, 32.00, 142),
(2013, 25, 31.00, 142),
(2014, 26, 30.00, 142),
(2015, 27, 31.00, 142),
(2016, 28, 25.00, 142),
(2023, 20, 28.00, 143),
(2024, 21, 36.00, 143),
(2025, 22, 30.00, 143),
(2026, 23, 24.00, 143),
(2027, 24, 34.00, 143),
(2028, 25, 28.00, 143),
(2029, 26, 30.00, 143),
(2030, 27, 10.00, 143),
(2031, 28, 30.00, 143),
(2038, 20, 26.00, 144),
(2039, 21, 39.00, 144),
(2040, 22, 37.00, 144),
(2041, 23, 26.00, 144),
(2042, 24, 39.00, 144),
(2043, 25, 32.00, 144),
(2044, 26, 26.00, 144),
(2045, 27, 25.00, 144),
(2046, 28, 31.00, 144),
(2053, 20, 15.00, 145),
(2054, 21, 30.00, 145),
(2055, 22, 20.00, 145),
(2056, 23, 15.00, 145),
(2057, 24, 28.00, 145),
(2058, 25, 20.00, 145),
(2059, 26, 20.00, 145),
(2060, 27, 10.00, 145),
(2061, 28, 15.00, 145),
(2068, 29, 20.00, 146),
(2069, 30, 26.00, 146),
(2070, 32, 24.00, 146),
(2071, 33, 24.00, 146),
(2072, 34, 24.00, 146),
(2073, 35, 28.00, 146),
(2074, 36, 31.00, 146),
(2075, 37, 22.00, 146),
(2083, 29, 23.00, 147),
(2084, 30, 30.00, 147),
(2085, 32, 29.00, 147),
(2086, 33, 29.00, 147),
(2087, 34, 30.00, 147),
(2088, 35, 29.00, 147),
(2089, 36, 31.00, 147),
(2090, 37, 28.00, 147),
(2098, 29, 19.00, 148),
(2099, 30, 31.00, 148),
(2100, 32, 20.00, 148),
(2101, 33, 33.00, 148),
(2102, 34, 24.00, 148),
(2103, 35, 35.00, 148),
(2104, 36, 34.00, 148),
(2105, 37, 30.00, 148),
(2113, 29, 33.00, 149),
(2114, 30, 29.00, 149),
(2115, 32, 25.00, 149),
(2116, 33, 33.00, 149),
(2117, 34, 31.00, 149),
(2118, 35, 33.00, 149),
(2119, 36, 33.00, 149),
(2120, 37, 23.00, 149),
(2128, 29, 23.00, 150),
(2129, 30, 20.00, 150),
(2130, 32, 18.00, 150),
(2131, 33, 25.00, 150),
(2132, 34, 20.00, 150),
(2133, 35, 29.00, 150),
(2134, 36, 24.00, 150),
(2135, 37, 21.00, 150),
(2143, 29, 26.00, 151),
(2144, 30, 28.00, 151),
(2145, 32, 22.00, 151),
(2146, 33, 27.00, 151),
(2147, 34, 19.00, 151),
(2148, 35, 31.00, 151),
(2149, 36, 34.00, 151),
(2150, 37, 33.00, 151),
(2158, 29, 31.00, 152),
(2159, 30, 27.00, 152),
(2160, 32, 18.00, 152),
(2161, 33, 23.00, 152),
(2162, 34, 24.00, 152),
(2163, 35, 30.00, 152),
(2164, 36, 28.00, 152),
(2165, 37, 30.00, 152),
(2173, 29, 30.00, 153),
(2175, 32, 30.00, 153),
(2176, 33, 32.00, 153),
(2177, 34, 30.00, 153),
(2178, 35, 33.00, 153),
(2179, 36, 35.00, 153),
(2180, 37, 39.00, 153),
(2188, 29, 32.00, 154),
(2189, 30, 40.00, 154),
(2190, 32, 34.00, 154),
(2191, 33, 32.00, 154),
(2192, 34, 32.00, 154),
(2193, 35, 40.00, 154),
(2194, 36, 32.00, 154),
(2195, 37, 36.00, 154),
(2203, 29, 24.00, 155),
(2204, 30, 40.00, 155),
(2205, 32, 30.00, 155),
(2206, 33, 26.00, 155),
(2207, 34, 26.00, 155),
(2208, 35, 20.00, 155),
(2209, 36, 34.00, 155),
(2210, 37, 32.00, 155),
(2218, 29, 29.00, 156),
(2219, 30, 34.00, 156),
(2220, 32, 29.00, 156),
(2221, 33, 28.00, 156),
(2222, 34, 30.00, 156),
(2223, 35, 36.00, 156),
(2224, 36, 35.00, 156),
(2225, 37, 32.00, 156),
(2233, 29, 35.00, 157),
(2234, 30, 38.00, 157),
(2235, 32, 28.00, 157),
(2236, 33, 32.00, 157),
(2237, 34, 38.00, 157),
(2238, 35, 33.00, 157),
(2239, 36, 37.00, 157),
(2240, 37, 31.00, 157),
(2248, 29, 30.00, 158),
(2249, 30, 33.00, 158),
(2250, 32, 35.00, 158),
(2251, 33, 35.00, 158),
(2252, 34, 30.00, 158),
(2253, 35, 32.00, 158),
(2254, 36, 34.00, 158),
(2255, 37, 36.00, 158),
(2263, 29, 40.00, 159),
(2264, 30, 40.00, 159),
(2265, 32, 40.00, 159),
(2266, 33, 40.00, 159),
(2267, 34, 40.00, 159),
(2268, 35, 40.00, 159),
(2269, 36, 40.00, 159),
(2270, 37, 40.00, 159),
(2278, 29, 38.00, 160),
(2279, 30, 30.00, 160),
(2280, 32, 20.00, 160),
(2281, 33, 35.00, 160),
(2282, 34, 25.00, 160),
(2283, 35, 40.00, 160),
(2284, 36, 40.00, 160),
(2285, 37, 30.00, 160),
(2293, 29, 26.00, 161),
(2294, 30, 26.00, 161),
(2295, 32, 23.00, 161),
(2296, 33, 24.00, 161),
(2297, 34, 23.00, 161),
(2298, 35, 23.00, 161),
(2299, 36, 24.00, 161),
(2300, 37, 24.00, 161),
(2308, 29, 26.00, 162),
(2309, 30, 28.00, 162),
(2310, 32, 20.00, 162),
(2311, 33, 21.00, 162),
(2312, 34, 26.00, 162),
(2313, 35, 26.00, 162),
(2314, 36, 35.00, 162),
(2315, 37, 20.00, 162),
(2323, 38, 24.00, 163),
(2324, 39, 24.00, 163),
(2325, 40, 35.00, 163),
(2326, 41, 12.00, 163),
(2327, 42, 14.00, 163),
(2330, 38, 20.00, 164),
(2331, 39, 20.00, 164),
(2332, 40, 20.00, 164),
(2333, 41, 20.00, 164),
(2334, 42, 20.00, 164),
(2338, 39, 25.00, 165),
(2344, 38, 38.00, 166),
(2346, 40, 36.00, 166),
(2347, 41, 32.00, 166),
(2348, 42, 34.00, 166),
(2351, 38, 19.00, 167),
(2352, 39, 23.00, 167),
(2353, 40, 22.00, 167),
(2354, 41, 26.00, 167),
(2355, 42, 19.00, 167),
(2358, 38, 37.00, 168),
(2359, 39, 25.00, 168),
(2360, 40, 35.00, 168),
(2361, 41, 21.00, 168),
(2362, 42, 25.00, 168),
(2365, 38, 21.00, 169),
(2366, 39, 22.00, 169),
(2367, 40, 26.00, 169),
(2368, 41, 26.00, 169),
(2369, 42, 22.00, 169),
(2372, 38, 28.00, 170),
(2374, 40, 32.00, 170),
(2375, 41, 20.00, 170),
(2376, 42, 22.00, 170),
(2379, 38, 19.00, 171),
(2381, 40, 22.00, 171),
(2382, 41, 20.00, 171),
(2383, 42, 20.00, 171),
(2386, 38, 30.00, 172),
(2387, 39, 28.00, 172),
(2388, 40, 31.00, 172),
(2389, 41, 23.00, 172),
(2390, 42, 25.00, 172),
(2394, 39, 22.00, 173),
(2400, 38, 30.00, 174),
(2401, 39, 32.00, 174),
(2402, 40, 37.00, 174),
(2403, 41, 33.00, 174),
(2404, 42, 30.00, 174),
(2407, 38, 30.00, 175),
(2408, 39, 32.00, 175),
(2409, 40, 37.00, 175),
(2410, 41, 33.00, 175),
(2411, 42, 30.00, 175),
(2414, 1, 31.00, 176),
(2415, 2, 31.00, 176),
(2416, 4, 23.00, 176),
(2417, 5, 31.00, 176),
(2418, 6, 29.00, 176),
(2419, 7, 29.00, 176),
(2420, 8, 32.00, 176),
(2421, 9, 33.00, 176),
(2422, 10, 20.00, 176),
(2423, 44, 33.00, 176);

-- --------------------------------------------------------

--
-- Stand-in structure for view `assessment_detailsviews`
--
CREATE TABLE `assessment_detailsviews` (
`assessment_id` int(10) unsigned
,`subject_classroom_id` int(10) unsigned
,`assessment_setup_detail_id` int(10) unsigned
,`marked` int(10) unsigned
,`assessment_detail_id` int(10) unsigned
,`student_id` int(10) unsigned
,`student_no` varchar(10)
,`student_name` varchar(101)
,`gender` varchar(10)
,`score` double(8,2) unsigned
,`weight_point` double(8,2) unsigned
,`number` tinyint(4)
,`percentage` int(10) unsigned
,`description` varchar(255)
,`submission_date` date
,`assessment_setup_id` int(10) unsigned
,`assessment_no` tinyint(4)
,`ca_weight_point` int(10) unsigned
,`exam_weight_point` int(10) unsigned
,`sponsor_id` int(10) unsigned
,`phone_no` varchar(20)
,`email` varchar(255)
,`sponsor_name` varchar(91)
,`subject_id` int(10) unsigned
,`classroom_id` int(10) unsigned
,`tutor_id` int(10) unsigned
,`tutor` varchar(91)
,`classroom` varchar(255)
,`classlevel_id` int(10) unsigned
,`classlevel` varchar(255)
,`classgroup_id` int(10) unsigned
,`academic_term_id` int(10) unsigned
,`academic_term` varchar(100)
);

-- --------------------------------------------------------

--
-- Table structure for table `assessment_setups`
--

CREATE TABLE `assessment_setups` (
  `assessment_setup_id` int(10) UNSIGNED NOT NULL,
  `assessment_no` tinyint(4) NOT NULL,
  `classgroup_id` int(10) UNSIGNED NOT NULL,
  `academic_term_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `assessment_setups`
--

INSERT INTO `assessment_setups` (`assessment_setup_id`, `assessment_no`, `classgroup_id`, `academic_term_id`) VALUES
(3, 3, 1, 1),
(4, 3, 2, 1),
(5, 1, 1, 2),
(6, 1, 2, 2),
(7, 1, 1, 3),
(8, 1, 2, 3);

-- --------------------------------------------------------

--
-- Table structure for table `assessment_setup_details`
--

CREATE TABLE `assessment_setup_details` (
  `assessment_setup_detail_id` int(10) UNSIGNED NOT NULL,
  `number` tinyint(4) NOT NULL,
  `weight_point` double(8,2) UNSIGNED NOT NULL,
  `percentage` int(10) UNSIGNED NOT NULL,
  `assessment_setup_id` int(10) UNSIGNED NOT NULL,
  `submission_date` date DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `assessment_setup_details`
--

INSERT INTO `assessment_setup_details` (`assessment_setup_detail_id`, `number`, `weight_point`, `percentage`, `assessment_setup_id`, `submission_date`, `description`) VALUES
(1, 1, 10.00, 25, 3, '2016-07-15', 'first test'),
(2, 2, 20.00, 50, 3, '2016-07-15', 'mid-term test'),
(3, 3, 10.00, 25, 3, '2016-07-15', 'final test'),
(5, 1, 10.00, 25, 4, '2016-07-15', 'first test'),
(6, 2, 20.00, 50, 4, '2016-07-15', 'mid-term test'),
(7, 3, 10.00, 25, 4, '2016-07-15', 'final test'),
(8, 1, 40.00, 100, 5, '2016-02-26', 'Second Term C.A'),
(9, 1, 40.00, 100, 6, '2016-02-26', 'Second Term C.A'),
(10, 1, 40.00, 100, 7, '2015-11-13', 'First Term C.A'),
(11, 1, 40.00, 100, 8, '2015-11-13', 'First Term C.A');

-- --------------------------------------------------------

--
-- Table structure for table `classgroups`
--

CREATE TABLE `classgroups` (
  `classgroup_id` int(10) UNSIGNED NOT NULL,
  `classgroup` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ca_weight_point` int(10) UNSIGNED DEFAULT '0',
  `exam_weight_point` int(10) UNSIGNED DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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

CREATE TABLE `classlevels` (
  `classlevel_id` int(10) UNSIGNED NOT NULL,
  `classlevel` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `classgroup_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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

CREATE TABLE `classrooms` (
  `classroom_id` int(10) UNSIGNED NOT NULL,
  `classroom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `class_size` int(11) DEFAULT NULL,
  `class_status` int(10) UNSIGNED NOT NULL DEFAULT '1',
  `classlevel_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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

CREATE TABLE `class_masters` (
  `class_master_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `classroom_id` int(10) UNSIGNED NOT NULL,
  `academic_year_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `class_masters`
--

INSERT INTO `class_masters` (`class_master_id`, `user_id`, `classroom_id`, `academic_year_id`, `created_at`, `updated_at`) VALUES
(1, 5, 2, 1, '2016-05-31 21:02:05', '2016-05-31 21:07:41'),
(2, 12, 1, 1, '2016-05-31 21:03:43', '2016-05-31 21:06:44'),
(3, 4, 4, 1, '2016-08-20 09:57:02', '2016-08-20 09:57:02'),
(4, 4, 5, 1, '2016-08-20 09:57:13', '2016-08-20 09:57:13');

-- --------------------------------------------------------

--
-- Table structure for table `domains`
--

CREATE TABLE `domains` (
  `domain_id` int(10) UNSIGNED NOT NULL,
  `domain` varchar(100) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `domains`
--

INSERT INTO `domains` (`domain_id`, `domain`) VALUES
(1, 'Neatness'),
(2, 'Punctuality'),
(3, 'Attentiveness'),
(4, 'Staff / Students Interaction');

-- --------------------------------------------------------

--
-- Table structure for table `domain_assessments`
--

CREATE TABLE `domain_assessments` (
  `domain_assessment_id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `academic_term_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `domain_assessments`
--

INSERT INTO `domain_assessments` (`domain_assessment_id`, `student_id`, `academic_term_id`) VALUES
(1, 13, 2),
(2, 12, 2),
(3, 11, 2),
(4, 19, 2),
(5, 47, 2);

-- --------------------------------------------------------

--
-- Table structure for table `domain_details`
--

CREATE TABLE `domain_details` (
  `domain_detail_id` int(10) UNSIGNED NOT NULL,
  `domain_id` int(10) UNSIGNED NOT NULL,
  `domain_assessment_id` int(10) UNSIGNED NOT NULL,
  `option` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `domain_details`
--

INSERT INTO `domain_details` (`domain_detail_id`, `domain_id`, `domain_assessment_id`, `option`) VALUES
(1, 3, 1, 4),
(2, 1, 1, 3),
(3, 2, 1, 5),
(4, 4, 1, 2),
(5, 3, 2, 3),
(6, 1, 2, 2),
(7, 2, 2, 4),
(8, 4, 2, 3),
(9, 3, 3, 5),
(10, 1, 3, 3),
(11, 2, 3, 4),
(12, 4, 3, 1),
(13, 3, 4, 5),
(14, 1, 4, 4),
(15, 2, 4, 3),
(16, 4, 4, 2),
(17, 3, 5, 1),
(18, 1, 5, 2),
(19, 2, 5, 3),
(20, 4, 5, 4);

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

CREATE TABLE `exams` (
  `exam_id` int(10) UNSIGNED NOT NULL,
  `subject_classroom_id` int(10) UNSIGNED NOT NULL,
  `marked` int(10) UNSIGNED NOT NULL DEFAULT '2'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `exams`
--

INSERT INTO `exams` (`exam_id`, `subject_classroom_id`, `marked`) VALUES
(1, 89, 1),
(2, 90, 1),
(3, 91, 1),
(4, 92, 1),
(5, 93, 1),
(6, 94, 1),
(7, 95, 1),
(8, 96, 1),
(9, 97, 1),
(10, 98, 1),
(11, 99, 1),
(12, 100, 1),
(13, 101, 1),
(14, 102, 1),
(15, 103, 1),
(16, 104, 1),
(17, 105, 1),
(18, 106, 1),
(19, 107, 1),
(20, 108, 1),
(21, 109, 1),
(22, 110, 1),
(23, 111, 1),
(24, 112, 1),
(25, 113, 1),
(26, 114, 1),
(27, 115, 1),
(28, 116, 1),
(29, 117, 1),
(30, 118, 1),
(31, 119, 1),
(32, 120, 1),
(33, 121, 1),
(34, 122, 1),
(35, 123, 1),
(36, 124, 1),
(37, 125, 1),
(38, 126, 1),
(39, 127, 1),
(40, 128, 1),
(41, 129, 1),
(42, 130, 1),
(43, 131, 1),
(44, 132, 1),
(45, 133, 1),
(46, 134, 1),
(47, 135, 1),
(48, 136, 1),
(49, 137, 1),
(50, 138, 1),
(51, 139, 1),
(52, 140, 1),
(53, 141, 1),
(54, 142, 1),
(55, 143, 1),
(56, 144, 1),
(57, 145, 1),
(58, 147, 1),
(59, 148, 1),
(60, 150, 1),
(61, 151, 1),
(62, 152, 1),
(63, 153, 1),
(64, 154, 1),
(65, 155, 1),
(66, 156, 1),
(67, 157, 1),
(68, 158, 1),
(69, 159, 1),
(70, 160, 1),
(71, 161, 1),
(72, 162, 1),
(74, 164, 1),
(76, 166, 1),
(78, 168, 1),
(80, 170, 1),
(81, 171, 1),
(82, 172, 1);

-- --------------------------------------------------------

--
-- Stand-in structure for view `exams_detailsviews`
--
CREATE TABLE `exams_detailsviews` (
`exam_detail_id` int(10) unsigned
,`exam_id` int(10) unsigned
,`subject_classroom_id` int(10) unsigned
,`subject_id` int(10) unsigned
,`tutor_id` int(10) unsigned
,`classlevel_id` int(10) unsigned
,`classroom_id` int(10) unsigned
,`student_id` int(10) unsigned
,`classroom` varchar(255)
,`fullname` varchar(101)
,`student_gender` varchar(10)
,`student_no` varchar(10)
,`ca` double(5,2) unsigned
,`exam` double(5,2) unsigned
,`student_total` double(19,2)
,`ca_weight_point` int(10) unsigned
,`exam_weight_point` int(10) unsigned
,`weight_point_total` bigint(11) unsigned
,`academic_term_id` int(10) unsigned
,`academic_term` varchar(100)
,`marked` int(10) unsigned
,`academic_year_id` int(10) unsigned
,`academic_year` varchar(100)
,`classlevel` varchar(255)
,`classgroup_id` int(10) unsigned
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `exams_subjectviews`
--
CREATE TABLE `exams_subjectviews` (
`exam_id` int(10) unsigned
,`classroom_id` int(10) unsigned
,`classroom` varchar(255)
,`subject_id` int(10) unsigned
,`subject_classroom_id` int(10) unsigned
,`tutor_id` int(10) unsigned
,`tutor` varchar(91)
,`ca_weight_point` int(10) unsigned
,`exam_weight_point` int(10) unsigned
,`marked` int(10) unsigned
,`classlevel_id` int(10) unsigned
,`classlevel` varchar(255)
,`academic_term_id` int(10) unsigned
,`academic_term` varchar(100)
,`academic_year_id` int(10) unsigned
,`academic_year` varchar(100)
);

-- --------------------------------------------------------

--
-- Table structure for table `exam_details`
--

CREATE TABLE `exam_details` (
  `exam_detail_id` int(10) UNSIGNED NOT NULL,
  `exam_id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `ca` double(5,2) UNSIGNED NOT NULL DEFAULT '0.00',
  `exam` double(5,2) UNSIGNED NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `exam_details`
--

INSERT INTO `exam_details` (`exam_detail_id`, `exam_id`, `student_id`, `ca`, `exam`) VALUES
(1, 1, 1, 24.00, 44.00),
(2, 1, 2, 32.00, 51.00),
(3, 1, 4, 24.00, 46.00),
(4, 1, 5, 34.00, 50.00),
(5, 1, 6, 29.00, 50.00),
(6, 1, 7, 31.00, 51.00),
(7, 1, 8, 26.00, 47.00),
(8, 1, 9, 36.00, 50.00),
(9, 1, 10, 12.00, 37.00),
(10, 1, 44, 33.00, 50.00),
(16, 2, 1, 33.00, 46.00),
(17, 2, 2, 38.00, 47.00),
(18, 2, 4, 21.00, 28.00),
(19, 2, 5, 38.00, 54.00),
(20, 2, 6, 36.00, 47.00),
(21, 2, 7, 35.00, 48.00),
(22, 2, 8, 37.00, 51.00),
(23, 2, 9, 35.00, 46.00),
(24, 2, 10, 10.00, 35.00),
(25, 2, 44, 36.00, 49.00),
(31, 3, 1, 20.00, 38.00),
(32, 3, 2, 36.00, 45.00),
(33, 3, 4, 20.00, 30.00),
(34, 3, 5, 26.00, 39.00),
(35, 3, 6, 30.00, 35.00),
(36, 3, 7, 26.00, 41.00),
(37, 3, 8, 34.00, 44.00),
(38, 3, 9, 36.00, 43.00),
(39, 3, 10, 20.00, 14.00),
(40, 3, 44, 30.00, 47.00),
(46, 4, 1, 20.00, 32.00),
(47, 4, 2, 34.00, 49.00),
(48, 4, 4, 19.00, 29.00),
(49, 4, 5, 25.00, 43.00),
(50, 4, 6, 28.00, 41.00),
(51, 4, 7, 36.00, 47.00),
(52, 4, 8, 22.00, 29.00),
(53, 4, 9, 29.00, 32.00),
(54, 4, 10, 8.00, 6.00),
(55, 4, 44, 23.00, 26.00),
(61, 5, 1, 24.00, 47.00),
(62, 5, 2, 32.00, 52.00),
(63, 5, 4, 24.00, 42.00),
(64, 5, 5, 34.00, 43.00),
(65, 5, 6, 29.00, 50.00),
(66, 5, 7, 31.00, 31.00),
(67, 5, 8, 26.00, 46.00),
(68, 5, 9, 36.00, 50.00),
(69, 5, 10, 12.00, 29.00),
(70, 5, 44, 26.00, 43.00),
(76, 6, 1, 22.00, 33.00),
(77, 6, 2, 25.00, 44.00),
(78, 6, 4, 24.00, 32.00),
(79, 6, 5, 25.00, 41.00),
(80, 6, 6, 25.00, 34.00),
(81, 6, 7, 24.00, 34.00),
(82, 6, 8, 24.00, 38.00),
(83, 6, 9, 27.00, 37.00),
(84, 6, 10, 29.00, 21.00),
(85, 6, 44, 26.00, 36.00),
(91, 7, 1, 34.00, 36.00),
(92, 7, 2, 32.00, 50.00),
(93, 7, 4, 19.00, 19.00),
(94, 7, 5, 40.00, 40.00),
(95, 7, 6, 36.00, 50.00),
(96, 7, 7, 34.00, 46.00),
(97, 7, 8, 36.00, 41.00),
(98, 7, 9, 38.00, 42.00),
(99, 7, 10, 16.00, 39.00),
(100, 7, 44, 30.00, 47.00),
(106, 8, 1, 30.00, 20.00),
(107, 8, 2, 23.00, 31.00),
(108, 8, 4, 25.00, 25.00),
(109, 8, 5, 26.00, 38.00),
(110, 8, 6, 26.00, 42.00),
(111, 8, 7, 26.00, 36.00),
(112, 8, 8, 22.00, 31.00),
(113, 8, 9, 26.00, 30.00),
(114, 8, 10, 13.00, 23.00),
(115, 8, 44, 21.00, 31.00),
(121, 9, 1, 25.00, 25.00),
(122, 9, 2, 30.00, 42.00),
(123, 9, 4, 25.00, 20.00),
(124, 9, 5, 23.00, 28.00),
(125, 9, 6, 30.00, 30.00),
(126, 9, 7, 31.00, 34.00),
(127, 9, 8, 25.00, 37.00),
(128, 9, 9, 35.00, 35.00),
(129, 9, 10, 26.00, 15.00),
(130, 9, 44, 28.00, 29.00),
(136, 10, 1, 32.00, 47.00),
(137, 10, 2, 36.00, 56.00),
(138, 10, 4, 26.00, 38.00),
(139, 10, 5, 30.00, 46.00),
(140, 10, 6, 40.00, 52.00),
(141, 10, 7, 36.00, 54.00),
(142, 10, 8, 25.00, 54.00),
(143, 10, 9, 35.00, 44.00),
(144, 10, 10, 26.00, 25.00),
(145, 10, 44, 26.00, 50.00),
(151, 11, 1, 31.00, 31.00),
(152, 11, 2, 31.00, 36.00),
(153, 11, 4, 23.00, 27.00),
(154, 11, 5, 31.00, 43.00),
(155, 11, 6, 29.00, 32.00),
(156, 11, 7, 29.00, 42.00),
(157, 11, 8, 32.00, 52.00),
(158, 11, 9, 33.00, 48.00),
(159, 11, 10, 20.00, 25.00),
(160, 11, 44, 33.00, 48.00),
(166, 12, 1, 20.00, 35.00),
(167, 12, 2, 40.00, 56.00),
(168, 12, 4, 10.00, 30.00),
(169, 12, 5, 35.00, 58.00),
(170, 12, 6, 38.00, 47.00),
(171, 12, 7, 36.00, 48.00),
(172, 12, 8, 34.00, 36.00),
(173, 12, 9, 30.00, 47.00),
(174, 12, 10, 26.00, 20.00),
(175, 12, 44, 30.00, 47.00),
(181, 13, 1, 21.00, 36.00),
(182, 13, 2, 35.00, 51.00),
(183, 13, 4, 26.00, 27.00),
(184, 13, 5, 28.00, 46.00),
(185, 13, 6, 31.00, 37.00),
(186, 13, 7, 38.00, 52.00),
(187, 13, 8, 31.00, 50.00),
(188, 13, 9, 28.00, 50.00),
(189, 13, 10, 12.00, 21.00),
(190, 13, 44, 22.00, 47.00),
(196, 14, 1, 32.00, 9.00),
(197, 14, 2, 33.00, 40.00),
(198, 14, 4, 30.00, 22.00),
(199, 14, 5, 36.00, 30.00),
(200, 14, 6, 32.00, 22.00),
(201, 14, 7, 30.00, 32.00),
(202, 14, 8, 36.00, 28.00),
(203, 14, 9, 33.00, 17.00),
(204, 14, 10, 31.00, 13.00),
(205, 14, 44, 35.00, 22.00),
(211, 15, 11, 36.00, 47.00),
(212, 15, 12, 31.00, 41.00),
(213, 15, 13, 34.00, 43.00),
(214, 15, 14, 24.00, 43.00),
(215, 15, 15, 29.00, 41.00),
(216, 15, 17, 30.00, 45.00),
(217, 15, 19, 38.00, 55.00),
(218, 15, 45, 33.00, 45.00),
(219, 15, 47, 30.00, 43.00),
(226, 16, 11, 36.00, 45.00),
(227, 16, 12, 29.00, 43.00),
(228, 16, 13, 37.00, 52.00),
(229, 16, 14, 32.00, 46.00),
(230, 16, 15, 21.00, 42.00),
(231, 16, 17, 35.00, 51.00),
(232, 16, 19, 39.00, 53.00),
(233, 16, 45, 35.00, 46.00),
(234, 16, 47, 33.00, 46.00),
(241, 17, 11, 30.00, 42.00),
(242, 17, 12, 34.00, 36.00),
(243, 17, 13, 34.00, 52.00),
(244, 17, 14, 24.00, 31.00),
(245, 17, 15, 26.00, 35.00),
(246, 17, 17, 32.00, 33.00),
(247, 17, 19, 38.00, 46.00),
(248, 17, 45, 24.00, 27.00),
(249, 17, 47, 24.00, 41.00),
(256, 18, 11, 23.00, 32.00),
(257, 18, 12, 22.00, 28.00),
(258, 18, 13, 33.00, 42.00),
(259, 18, 14, 18.00, 28.00),
(260, 18, 15, 27.00, 35.00),
(261, 18, 17, 25.00, 37.00),
(262, 18, 19, 27.00, 33.00),
(263, 18, 45, 18.00, 33.00),
(264, 18, 47, 29.00, 38.00),
(271, 19, 11, 35.00, 55.00),
(272, 19, 12, 30.00, 46.00),
(273, 19, 13, 27.00, 38.00),
(274, 19, 14, 31.00, 30.00),
(275, 19, 15, 27.00, 38.00),
(276, 19, 17, 26.00, 56.00),
(277, 19, 19, 31.00, 53.00),
(278, 19, 45, 26.00, 42.00),
(279, 19, 47, 31.00, 47.00),
(286, 20, 11, 24.00, 43.00),
(287, 20, 12, 24.00, 38.00),
(288, 20, 13, 24.00, 44.00),
(289, 20, 14, 23.00, 28.00),
(290, 20, 15, 24.00, 27.00),
(291, 20, 17, 23.00, 43.00),
(292, 20, 19, 25.00, 40.00),
(293, 20, 45, 29.00, 43.00),
(294, 20, 47, 26.00, 36.00),
(301, 21, 11, 30.00, 42.00),
(302, 21, 12, 34.00, 45.00),
(303, 21, 13, 32.00, 47.00),
(304, 21, 14, 14.00, 31.00),
(305, 21, 15, 22.00, 30.00),
(306, 21, 17, 26.00, 39.00),
(307, 21, 19, 34.00, 39.00),
(308, 21, 45, 20.00, 31.00),
(309, 21, 47, 22.00, 26.00),
(316, 22, 11, 24.00, 42.00),
(317, 22, 12, 19.00, 36.00),
(318, 22, 13, 25.00, 37.00),
(319, 22, 14, 28.00, 22.00),
(320, 22, 15, 22.00, 28.00),
(321, 22, 17, 23.00, 34.00),
(322, 22, 19, 27.00, 44.00),
(323, 22, 45, 17.00, 34.00),
(324, 22, 47, 17.00, 34.00),
(331, 23, 11, 31.00, 39.00),
(332, 23, 12, 28.00, 29.00),
(333, 23, 13, 35.00, 35.00),
(334, 23, 14, 25.00, 28.00),
(335, 23, 15, 26.00, 28.00),
(336, 23, 17, 29.00, 31.00),
(337, 23, 19, 33.00, 41.00),
(338, 23, 45, 26.00, 30.00),
(339, 23, 47, 25.00, 25.00),
(346, 24, 11, 40.00, 50.00),
(347, 24, 12, 40.00, 54.00),
(348, 24, 13, 40.00, 51.00),
(349, 24, 14, 16.00, 37.00),
(350, 24, 15, 40.00, 42.00),
(351, 24, 17, 25.00, 37.00),
(352, 24, 19, 40.00, 49.00),
(353, 24, 45, 18.00, 33.00),
(354, 24, 47, 38.00, 48.00),
(361, 25, 11, 30.00, 25.00),
(362, 25, 12, 28.00, 36.00),
(363, 25, 13, 27.00, 35.00),
(364, 25, 14, 26.00, 28.00),
(365, 25, 15, 22.00, 26.00),
(366, 25, 17, 30.00, 24.00),
(367, 25, 19, 34.00, 50.00),
(368, 25, 45, 31.00, 36.00),
(369, 25, 47, 31.00, 34.00),
(376, 26, 11, 40.00, 47.00),
(377, 26, 12, 40.00, 47.00),
(378, 26, 13, 40.00, 46.00),
(379, 26, 14, 20.00, 33.00),
(380, 26, 15, 30.00, 25.00),
(381, 26, 17, 28.00, 50.00),
(382, 26, 19, 38.00, 47.00),
(383, 26, 45, 35.00, 41.00),
(384, 26, 47, 35.00, 51.00),
(391, 27, 11, 31.00, 44.00),
(392, 27, 12, 35.00, 43.00),
(393, 27, 13, 33.00, 50.00),
(394, 27, 14, 18.00, 28.00),
(395, 27, 15, 20.00, 24.00),
(396, 27, 17, 26.00, 48.00),
(397, 27, 19, 31.00, 43.00),
(398, 27, 45, 27.00, 37.00),
(399, 27, 47, 27.00, 40.00),
(406, 28, 11, 34.00, 29.00),
(407, 28, 12, 32.00, 21.00),
(408, 28, 13, 31.00, 21.00),
(409, 28, 14, 31.00, 12.00),
(410, 28, 15, 36.00, 28.00),
(411, 28, 17, 35.00, 25.00),
(412, 28, 19, 37.00, 40.00),
(413, 28, 45, 32.00, 15.00),
(414, 28, 47, 34.00, 31.00),
(421, 29, 20, 29.00, 19.00),
(422, 29, 21, 40.00, 48.00),
(423, 29, 22, 33.00, 39.00),
(424, 29, 23, 30.00, 11.00),
(425, 29, 24, 38.00, 47.00),
(426, 29, 25, 29.00, 41.00),
(427, 29, 26, 27.00, 17.00),
(428, 29, 27, 30.00, 25.00),
(429, 29, 28, 30.00, 29.00),
(436, 30, 20, 25.00, 30.00),
(437, 30, 21, 39.00, 56.00),
(438, 30, 22, 33.00, 45.00),
(439, 30, 23, 19.00, 28.00),
(440, 30, 24, 38.00, 50.00),
(441, 30, 25, 23.00, 38.00),
(442, 30, 26, 20.00, 25.00),
(443, 30, 27, 24.00, 31.00),
(444, 30, 28, 23.00, 43.00),
(451, 31, 20, 28.00, 31.00),
(452, 31, 21, 34.00, 46.00),
(453, 31, 22, 26.00, 41.00),
(454, 31, 23, 20.00, 23.00),
(455, 31, 24, 30.00, 43.00),
(456, 31, 25, 21.00, 27.00),
(457, 31, 26, 20.00, 27.00),
(458, 31, 27, 22.00, 28.00),
(459, 31, 28, 21.00, 20.00),
(466, 32, 20, 23.00, 29.00),
(467, 32, 21, 35.00, 52.00),
(468, 32, 22, 30.00, 47.00),
(469, 32, 23, 23.00, 16.00),
(470, 32, 24, 37.00, 51.00),
(471, 32, 25, 31.00, 39.00),
(472, 32, 26, 23.00, 25.00),
(473, 32, 27, 24.00, 34.00),
(474, 32, 28, 32.00, 36.00),
(481, 33, 20, 17.00, 40.00),
(482, 33, 21, 33.00, 51.00),
(483, 33, 22, 32.00, 52.00),
(484, 33, 24, 36.00, 54.00),
(485, 33, 25, 29.00, 39.00),
(486, 33, 26, 20.00, 24.00),
(487, 33, 27, 24.00, 48.00),
(488, 33, 28, 26.00, 46.00),
(496, 34, 20, 25.00, 37.00),
(497, 34, 21, 38.00, 56.00),
(498, 34, 22, 37.00, 50.00),
(499, 34, 23, 27.00, 30.00),
(500, 34, 24, 37.00, 54.00),
(501, 34, 25, 32.00, 47.00),
(502, 34, 26, 28.00, 30.00),
(503, 34, 27, 28.00, 36.00),
(504, 34, 28, 28.00, 45.00),
(511, 35, 20, 27.00, 33.00),
(512, 35, 21, 40.00, 54.00),
(513, 35, 22, 26.00, 39.00),
(514, 35, 23, 28.00, 30.00),
(515, 35, 24, 39.00, 53.00),
(516, 35, 25, 27.00, 33.00),
(517, 35, 26, 26.00, 19.00),
(518, 35, 27, 25.00, 34.00),
(519, 35, 28, 35.00, 30.00),
(526, 36, 20, 22.00, 28.00),
(527, 36, 21, 29.00, 44.00),
(528, 36, 22, 23.00, 39.00),
(529, 36, 23, 26.00, 25.00),
(530, 36, 24, 28.00, 40.00),
(531, 36, 25, 23.00, 31.00),
(532, 36, 26, 28.00, 22.00),
(533, 36, 27, 20.00, 31.00),
(534, 36, 28, 24.00, 33.00),
(541, 37, 20, 31.00, 30.00),
(542, 37, 21, 35.00, 46.00),
(543, 37, 22, 32.00, 40.00),
(544, 37, 23, 31.00, 32.00),
(545, 37, 24, 35.00, 45.00),
(546, 37, 25, 30.00, 33.00),
(547, 37, 26, 29.00, 21.00),
(548, 37, 27, 26.00, 27.00),
(549, 37, 28, 30.00, 31.00),
(556, 38, 20, 22.00, 51.00),
(557, 38, 21, 40.00, 58.00),
(558, 38, 22, 34.00, 47.00),
(559, 38, 23, 22.00, 36.00),
(560, 38, 24, 38.00, 56.00),
(561, 38, 25, 34.00, 53.00),
(562, 38, 26, 24.00, 31.00),
(563, 38, 27, 16.00, 38.00),
(564, 38, 28, 31.00, 51.00),
(571, 39, 20, 30.00, 21.00),
(572, 39, 21, 35.00, 51.00),
(573, 39, 22, 32.00, 40.00),
(574, 39, 23, 21.00, 23.00),
(575, 39, 24, 28.00, 48.00),
(576, 39, 25, 25.00, 35.00),
(577, 39, 26, 20.00, 26.00),
(578, 39, 27, 27.00, 23.00),
(579, 39, 28, 28.00, 25.00),
(586, 40, 29, 32.00, 49.00),
(587, 40, 30, 40.00, 44.00),
(588, 40, 32, 34.00, 29.00),
(589, 40, 33, 32.00, 40.00),
(590, 40, 34, 32.00, 30.00),
(591, 40, 35, 40.00, 46.00),
(592, 40, 36, 32.00, 29.00),
(593, 40, 37, 36.00, 44.00),
(601, 41, 29, 19.00, 25.00),
(602, 41, 30, 31.00, 45.00),
(603, 41, 32, 20.00, 30.00),
(604, 41, 33, 33.00, 24.00),
(605, 41, 34, 24.00, 19.00),
(606, 41, 35, 35.00, 42.00),
(607, 41, 36, 34.00, 47.00),
(608, 41, 37, 30.00, 39.00),
(616, 42, 29, 33.00, 15.00),
(617, 42, 30, 29.00, 40.00),
(618, 42, 32, 25.00, 25.00),
(619, 42, 33, 33.00, 32.00),
(620, 42, 34, 31.00, 18.00),
(621, 42, 35, 33.00, 36.00),
(622, 42, 36, 33.00, 41.00),
(623, 42, 37, 23.00, 27.00),
(631, 43, 29, 23.00, 32.00),
(632, 43, 30, 20.00, 39.00),
(633, 43, 32, 18.00, 34.00),
(634, 43, 33, 25.00, 49.00),
(635, 43, 34, 20.00, 32.00),
(636, 43, 35, 29.00, 44.00),
(637, 43, 36, 24.00, 48.00),
(638, 43, 37, 21.00, 45.00),
(646, 44, 29, 30.00, 25.00),
(647, 44, 32, 30.00, 34.00),
(648, 44, 33, 32.00, 31.00),
(649, 44, 34, 30.00, 25.00),
(650, 44, 35, 33.00, 31.00),
(651, 44, 36, 35.00, 38.00),
(652, 44, 37, 39.00, 41.00),
(653, 45, 29, 26.00, 30.00),
(654, 45, 30, 28.00, 53.00),
(655, 45, 32, 22.00, 40.00),
(656, 45, 33, 27.00, 46.00),
(657, 45, 34, 19.00, 34.00),
(658, 45, 35, 31.00, 51.00),
(659, 45, 36, 34.00, 53.00),
(660, 45, 37, 33.00, 49.00),
(668, 46, 29, 35.00, 40.00),
(669, 46, 30, 38.00, 45.00),
(670, 46, 32, 28.00, 35.00),
(671, 46, 33, 32.00, 41.00),
(672, 46, 34, 38.00, 38.00),
(673, 46, 35, 33.00, 52.00),
(674, 46, 36, 37.00, 55.00),
(675, 46, 37, 31.00, 45.00),
(683, 47, 29, 20.00, 30.00),
(684, 47, 30, 26.00, 38.00),
(685, 47, 32, 24.00, 30.00),
(686, 47, 33, 24.00, 34.00),
(687, 47, 34, 24.00, 30.00),
(688, 47, 35, 28.00, 37.00),
(689, 47, 36, 31.00, 45.00),
(690, 47, 37, 22.00, 33.00),
(698, 48, 29, 29.00, 40.00),
(699, 48, 30, 34.00, 45.00),
(700, 48, 32, 29.00, 35.00),
(701, 48, 33, 28.00, 41.00),
(702, 48, 34, 30.00, 38.00),
(703, 48, 35, 36.00, 52.00),
(704, 48, 36, 35.00, 55.00),
(705, 48, 37, 32.00, 45.00),
(713, 49, 29, 24.00, 47.00),
(714, 49, 30, 40.00, 35.00),
(715, 49, 32, 30.00, 35.00),
(716, 49, 33, 26.00, 37.00),
(717, 49, 34, 26.00, 21.00),
(718, 49, 35, 20.00, 47.00),
(719, 49, 36, 34.00, 37.00),
(720, 49, 37, 32.00, 45.00),
(728, 50, 29, 23.00, 18.00),
(729, 50, 30, 30.00, 43.00),
(730, 50, 32, 29.00, 24.00),
(731, 50, 33, 29.00, 18.00),
(732, 50, 34, 30.00, 33.00),
(733, 50, 35, 29.00, 34.00),
(734, 50, 36, 31.00, 45.00),
(735, 50, 37, 28.00, 21.00),
(743, 51, 29, 38.00, 35.00),
(744, 51, 30, 30.00, 40.00),
(745, 51, 32, 20.00, 41.00),
(746, 51, 33, 35.00, 30.00),
(747, 51, 34, 25.00, 35.00),
(748, 51, 35, 40.00, 43.00),
(749, 51, 36, 40.00, 37.00),
(750, 51, 37, 30.00, 37.00),
(758, 52, 29, 31.00, 46.00),
(759, 52, 30, 27.00, 48.00),
(760, 52, 32, 18.00, 44.00),
(761, 52, 33, 23.00, 41.00),
(762, 52, 34, 24.00, 38.00),
(763, 52, 35, 30.00, 45.00),
(764, 52, 36, 28.00, 52.00),
(765, 52, 37, 30.00, 47.00),
(773, 53, 29, 30.00, 32.00),
(774, 53, 30, 33.00, 34.00),
(775, 53, 32, 35.00, 34.00),
(776, 53, 33, 35.00, 30.00),
(777, 53, 34, 30.00, 27.00),
(778, 53, 35, 32.00, 30.00),
(779, 53, 36, 34.00, 33.00),
(780, 53, 37, 36.00, 32.00),
(788, 54, 38, 24.00, 47.00),
(789, 54, 39, 24.00, 41.00),
(790, 54, 40, 35.00, 44.00),
(791, 54, 41, 12.00, 20.00),
(792, 54, 42, 14.00, 23.00),
(795, 55, 39, 25.00, 29.00),
(796, 56, 38, 38.00, 40.00),
(797, 56, 40, 36.00, 35.00),
(798, 56, 41, 32.00, 17.00),
(799, 56, 42, 34.00, 33.00),
(803, 57, 38, 19.00, 43.00),
(804, 57, 39, 23.00, 27.00),
(805, 57, 40, 22.00, 41.00),
(806, 57, 41, 26.00, 24.00),
(807, 57, 42, 19.00, 36.00),
(810, 58, 38, 37.00, 38.00),
(811, 58, 39, 25.00, 20.00),
(812, 58, 40, 35.00, 44.00),
(813, 58, 41, 21.00, 15.00),
(814, 58, 42, 25.00, 20.00),
(817, 59, 38, 21.00, 40.00),
(818, 59, 39, 22.00, 30.00),
(819, 59, 40, 26.00, 45.00),
(820, 59, 41, 26.00, 24.00),
(821, 59, 42, 22.00, 35.00),
(824, 60, 38, 20.00, 30.00),
(825, 60, 39, 20.00, 30.00),
(826, 60, 40, 20.00, 30.00),
(827, 60, 41, 20.00, 20.00),
(828, 60, 42, 20.00, 24.00),
(831, 61, 38, 28.00, 30.00),
(832, 61, 40, 32.00, 40.00),
(833, 61, 41, 20.00, 30.00),
(834, 61, 42, 22.00, 33.00),
(838, 62, 38, 19.00, 31.00),
(839, 62, 40, 22.00, 38.00),
(840, 62, 41, 20.00, 22.00),
(841, 62, 42, 20.00, 30.00),
(845, 63, 38, 30.00, 38.00),
(846, 63, 39, 28.00, 39.00),
(847, 63, 40, 31.00, 37.00),
(848, 63, 41, 23.00, 23.00),
(849, 63, 42, 25.00, 21.00),
(852, 64, 39, 22.00, 29.00),
(853, 65, 38, 30.00, 11.00),
(854, 65, 39, 32.00, 8.00),
(855, 65, 40, 37.00, 35.00),
(856, 65, 41, 33.00, 7.00),
(857, 65, 42, 30.00, 20.00),
(860, 66, 11, 40.00, 45.00),
(861, 66, 12, 40.00, 36.00),
(862, 66, 13, 40.00, 36.00),
(863, 66, 14, 40.00, 33.00),
(864, 66, 15, 40.00, 36.00),
(865, 66, 17, 40.00, 42.00),
(866, 66, 19, 40.00, 33.00),
(867, 66, 45, 40.00, 39.00),
(868, 66, 47, 40.00, 24.00),
(875, 67, 1, 40.00, 15.00),
(876, 67, 2, 40.00, 36.00),
(877, 67, 4, 40.00, 30.00),
(878, 67, 5, 40.00, 42.00),
(879, 67, 6, 40.00, 30.00),
(880, 67, 7, 40.00, 39.00),
(881, 67, 8, 40.00, 54.00),
(882, 67, 9, 40.00, 42.00),
(883, 67, 10, 40.00, 12.00),
(884, 67, 44, 40.00, 42.00),
(890, 68, 29, 40.00, 33.00),
(891, 68, 30, 40.00, 36.00),
(892, 68, 32, 40.00, 39.00),
(893, 68, 33, 40.00, 30.00),
(894, 68, 34, 40.00, 21.00),
(895, 68, 35, 40.00, 33.00),
(896, 68, 36, 40.00, 33.00),
(897, 68, 37, 40.00, 39.00),
(905, 69, 20, 40.00, 21.00),
(906, 69, 21, 40.00, 42.00),
(907, 69, 22, 40.00, 51.00),
(908, 69, 23, 40.00, 27.00),
(909, 69, 24, 40.00, 45.00),
(910, 69, 25, 40.00, 33.00),
(911, 69, 26, 40.00, 21.00),
(912, 69, 27, 40.00, 18.00),
(913, 69, 28, 40.00, 39.00),
(920, 70, 20, 28.00, 41.00),
(921, 70, 21, 36.00, 54.00),
(922, 70, 22, 30.00, 40.00),
(923, 70, 23, 24.00, 32.00),
(924, 70, 24, 34.00, 50.00),
(925, 70, 25, 28.00, 45.00),
(926, 70, 26, 30.00, 45.00),
(927, 70, 27, 10.00, 35.00),
(928, 70, 28, 30.00, 40.00),
(935, 71, 20, 26.00, 42.00),
(936, 71, 21, 39.00, 56.00),
(937, 71, 22, 37.00, 45.00),
(938, 71, 23, 26.00, 34.00),
(939, 71, 24, 39.00, 58.00),
(940, 71, 25, 32.00, 43.00),
(941, 71, 26, 26.00, 31.00),
(942, 71, 27, 25.00, 38.00),
(943, 71, 28, 31.00, 48.00),
(950, 72, 20, 31.00, 27.00),
(951, 72, 21, 35.00, 43.00),
(952, 72, 22, 31.00, 32.00),
(953, 72, 23, 32.00, 31.00),
(954, 72, 24, 32.00, 36.00),
(955, 72, 25, 31.00, 35.00),
(956, 72, 26, 30.00, 27.00),
(957, 72, 27, 31.00, 25.00),
(958, 72, 28, 25.00, 26.00),
(980, 74, 1, 10.00, 30.00),
(981, 74, 2, 20.00, 35.00),
(982, 74, 4, 10.00, 30.00),
(983, 74, 5, 28.00, 35.00),
(984, 74, 6, 10.00, 42.00),
(985, 74, 7, 20.00, 20.00),
(986, 74, 8, 20.00, 38.00),
(987, 74, 9, 15.00, 45.00),
(988, 74, 10, 6.00, 32.00),
(989, 74, 44, 20.00, 30.00),
(1010, 76, 11, 20.00, 44.00),
(1011, 76, 12, 28.00, 36.00),
(1012, 76, 13, 31.00, 32.00),
(1013, 76, 14, 20.00, 45.00),
(1014, 76, 15, 15.00, 33.00),
(1015, 76, 17, 20.00, 30.00),
(1016, 76, 19, 31.00, 38.00),
(1017, 76, 45, 20.00, 36.00),
(1018, 76, 47, 20.00, 35.00),
(1040, 78, 20, 15.00, 35.00),
(1041, 78, 21, 30.00, 50.00),
(1042, 78, 22, 20.00, 50.00),
(1043, 78, 23, 15.00, 25.00),
(1044, 78, 24, 28.00, 53.00),
(1045, 78, 25, 20.00, 38.00),
(1046, 78, 26, 20.00, 39.00),
(1047, 78, 27, 10.00, 45.00),
(1048, 78, 28, 15.00, 46.00),
(1070, 80, 29, 26.00, 53.00),
(1071, 80, 30, 28.00, 37.00),
(1072, 80, 32, 20.00, 38.00),
(1073, 80, 33, 21.00, 21.00),
(1074, 80, 34, 26.00, 30.00),
(1075, 80, 35, 26.00, 40.00),
(1076, 80, 36, 35.00, 50.00),
(1077, 80, 37, 20.00, 42.00),
(1085, 81, 29, 26.00, 17.00),
(1086, 81, 30, 26.00, 20.00),
(1087, 81, 32, 23.00, 20.00),
(1088, 81, 33, 24.00, 17.00),
(1089, 81, 34, 23.00, 25.00),
(1090, 81, 35, 23.00, 30.00),
(1091, 81, 36, 24.00, 31.00),
(1092, 81, 37, 24.00, 22.00),
(1100, 82, 38, 30.00, 38.00),
(1101, 82, 39, 32.00, 36.00),
(1102, 82, 40, 37.00, 50.00),
(1103, 82, 41, 33.00, 35.00),
(1104, 82, 42, 30.00, 40.00);

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

CREATE TABLE `grades` (
  `grade_id` int(10) UNSIGNED NOT NULL,
  `grade` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `grade_abbr` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `upper_bound` double(8,2) UNSIGNED NOT NULL,
  `lower_bound` double(8,2) UNSIGNED NOT NULL,
  `classgroup_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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

CREATE TABLE `menus` (
  `menu_id` int(10) UNSIGNED NOT NULL,
  `menu` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `menu_url` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `active` int(10) UNSIGNED NOT NULL DEFAULT '1',
  `sequence` int(10) UNSIGNED NOT NULL,
  `type` int(10) UNSIGNED NOT NULL DEFAULT '1',
  `icon` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `menu_header_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `menus`
--

INSERT INTO `menus` (`menu_id`, `menu`, `menu_url`, `active`, `sequence`, `type`, `icon`, `menu_header_id`, `created_at`, `updated_at`) VALUES
(1, 'SYSTEM', '#', 1, 1, 1, 'fa fa-television', 1, '2016-03-29 22:33:49', '2016-03-29 22:33:49'),
(2, 'PROFILE', '#', 1, 4, 1, 'fa fa-user', 2, '2016-03-30 19:33:36', '2016-07-10 15:25:10'),
(4, 'SPONSORS', '#', 1, 2, 1, 'fa fa-users', 2, '2016-04-17 07:01:21', '2016-05-23 14:16:17'),
(6, 'STAFFS', '#', 1, 3, 1, 'fa fa-users', 2, '2016-04-18 19:51:00', '2016-05-23 14:16:17'),
(7, 'MASTER RECORDS', '#', 1, 2, 1, 'fa fa-book', 1, '2016-05-10 03:53:29', '2016-05-10 03:53:29'),
(8, 'STUDENTS', '#', 1, 1, 1, 'fa fa-users', 2, '2016-05-23 14:16:17', '2016-05-23 14:16:17'),
(9, 'ASSESSMENTS', '#', 1, 2, 1, 'fa fa-book', 5, '2016-06-03 08:17:10', '2016-07-10 15:23:25'),
(10, 'MANAGE STUDENT', '/subject-tutors', 1, 1, 1, 'fa fa-group', 5, '2016-06-03 08:17:10', '2016-07-10 15:19:59'),
(11, 'EXAMS SETUP', '/exams/setup', 1, 4, 1, 'fa fa-hourglass-2', 1, '2016-06-15 07:47:20', '2016-08-29 10:02:50'),
(13, 'CLASS TEACHER', '#', 1, 3, 1, 'fa fa-creative-commons', 5, '2016-08-20 09:40:06', '2016-08-20 09:40:06'),
(14, 'EDIT', '/profiles/edit', 1, 3, 2, 'fa fa-edit', 6, '2016-08-27 12:29:03', '2016-08-29 17:17:40'),
(15, 'VIEW', '/profiles', 1, 2, 2, 'fa fa-user', 6, '2016-08-27 12:44:46', '2016-08-29 17:17:40'),
(17, 'MESSAGING', '#', 1, 1, 1, 'fa fa-envelope', 9, '2016-08-29 10:02:29', '2016-08-29 10:18:51');

-- --------------------------------------------------------

--
-- Table structure for table `menu_headers`
--

CREATE TABLE `menu_headers` (
  `menu_header_id` int(10) UNSIGNED NOT NULL,
  `menu_header` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `active` int(10) UNSIGNED NOT NULL DEFAULT '1',
  `icon` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sequence` int(10) UNSIGNED NOT NULL,
  `type` int(10) UNSIGNED NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `menu_headers`
--

INSERT INTO `menu_headers` (`menu_header_id`, `menu_header`, `active`, `icon`, `sequence`, `type`, `created_at`, `updated_at`) VALUES
(1, 'SETUPS', 1, '', 10, 1, '2016-03-29 22:30:39', '2016-08-27 13:18:04'),
(2, 'ACCOUNTS', 1, '', 3, 1, '2016-03-30 19:33:06', '2016-08-27 13:18:04'),
(4, 'WARDS', 1, 'fa fa-users', 2, 2, '2016-04-15 09:41:26', '2016-08-29 17:21:30'),
(5, 'MY CLASS', 1, '', 6, 1, '2016-06-03 08:13:52', '2016-08-27 13:18:04'),
(6, 'PROFILE', 1, 'fa fa-user', 1, 2, '2016-08-27 12:26:59', '2016-08-29 17:21:30'),
(7, 'ASSESSMENTS', 1, 'fa fa-book', 4, 2, '2016-08-27 13:09:09', '2016-08-27 13:18:04'),
(9, 'UTILITIES', 1, 'fa fa-object-group', 8, 1, '2016-08-29 10:18:00', '2016-08-29 10:18:00');

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `menu_item_id` int(10) UNSIGNED NOT NULL,
  `menu_item` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `menu_item_url` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `menu_item_icon` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `active` int(10) UNSIGNED NOT NULL DEFAULT '1',
  `sequence` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` int(10) UNSIGNED NOT NULL DEFAULT '1',
  `menu_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
(17, 'CLASS', '#', 'fa fa-university', 1, '2', 1, 7, '2016-05-13 17:09:07', '2016-07-15 18:51:06'),
(20, 'GRADE GROUPING', '/grades', 'fa fa-check', 1, '6', 1, 7, '2016-05-19 03:54:12', '2016-05-19 03:54:12'),
(21, 'CREATE', '/students/create', 'fa fa-plus', 1, '1', 1, 8, '2016-05-23 14:17:34', '2016-05-23 14:40:11'),
(22, 'MANAGE', '/students', 'fa fa-list', 1, '2', 1, 8, '2016-05-23 14:19:07', '2016-05-23 14:19:07'),
(23, 'ASSESSMENTS', '#', 'fa fa-briefcase', 1, '5', 1, 7, '2016-05-26 08:00:04', '2016-05-26 08:00:04'),
(24, 'CONTINUOUS', '/assessments', 'fa fa-check', 1, '1', 1, 9, '2016-07-10 15:20:36', '2016-07-10 15:23:03'),
(25, 'EXAMS', '/exams', 'fa fa-folder-open-o', 1, '2', 1, 9, '2016-07-10 15:20:36', '2016-07-10 15:22:24'),
(26, 'CLONE RECORDS', '/academic-terms/clones', 'fa fa-clone', 1, '8', 1, 7, '2016-07-15 18:47:37', '2016-08-19 14:34:38'),
(28, 'CLASS TEACHER', '/class-rooms/assign-students', 'fa fa-map-signs', 1, '6', 1, 7, '2016-08-19 14:33:10', '2016-08-20 09:26:23'),
(29, 'ASSESS / REMARKS', '/domains', 'fa fa-comments', 1, '1', 1, 13, '2016-08-20 09:42:21', '2016-08-20 09:42:21'),
(30, 'EXAMS', '/exams', 'fa fa-folder-open-o', 1, '1', 2, 15, '2016-08-27 13:25:33', '2016-08-27 13:25:33'),
(31, 'SEND S.M.S', '/messages', 'fa fa-send', 1, '1', 1, 17, '2016-08-29 10:03:41', '2016-08-29 10:03:41');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
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
('2016_05_18_182321_create_grades_table', 4),
('2016_05_31_205123_create_class_masters_table', 6),
('2016_05_17_184714_create_students_table', 4),
('2016_05_20_130901_create_assessments_tables', 5),
('2016_06_13_084933_create_exams_table', 7),
('2016_08_18_185246_create_domains_table', 8);

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `permission_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `display_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `uri` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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

CREATE TABLE `permission_role` (
  `permission_id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `permission_role`
--

INSERT INTO `permission_role` (`permission_id`, `role_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(2, 1),
(2, 2),
(2, 3),
(3, 1),
(3, 2),
(4, 1),
(4, 2),
(4, 3),
(5, 1),
(5, 2),
(6, 1),
(6, 2),
(7, 1),
(7, 2),
(8, 1),
(8, 2),
(9, 1),
(9, 2),
(9, 3),
(10, 1),
(10, 2),
(10, 3),
(11, 1),
(11, 2),
(12, 1),
(12, 2),
(13, 1),
(13, 2),
(14, 1),
(14, 2),
(15, 1),
(15, 2),
(16, 1),
(16, 2),
(17, 1),
(17, 2),
(18, 1),
(18, 2),
(19, 1),
(19, 2),
(20, 1),
(20, 2),
(21, 1),
(21, 2),
(22, 1),
(22, 2),
(23, 1),
(23, 2),
(24, 1),
(24, 2),
(25, 1),
(25, 2),
(26, 1),
(26, 2),
(27, 1),
(27, 2),
(28, 1),
(28, 2),
(29, 1),
(29, 2),
(30, 1),
(30, 2),
(31, 1),
(31, 2),
(32, 1),
(32, 2),
(33, 1),
(33, 2),
(34, 1),
(34, 2),
(35, 1),
(35, 2),
(36, 1),
(36, 2),
(37, 1),
(37, 2),
(38, 1),
(38, 2),
(39, 1),
(39, 2),
(40, 1),
(40, 2),
(41, 1),
(41, 2),
(42, 1),
(42, 2),
(43, 1),
(43, 2),
(44, 1),
(44, 2),
(45, 1),
(45, 2),
(46, 1),
(46, 2),
(47, 1),
(47, 2),
(48, 1),
(48, 2),
(49, 1),
(49, 2),
(50, 1),
(50, 2),
(51, 1),
(51, 2),
(52, 1),
(52, 2),
(53, 1),
(53, 2),
(54, 1),
(54, 2),
(55, 1),
(55, 2),
(56, 1),
(56, 2),
(57, 1),
(57, 2),
(58, 1),
(58, 2),
(59, 1),
(59, 2),
(60, 1),
(60, 2),
(61, 1),
(61, 2),
(62, 1),
(62, 2),
(63, 1),
(63, 2),
(64, 1),
(64, 2),
(65, 1),
(65, 2),
(66, 1),
(66, 2),
(67, 1),
(68, 1),
(69, 1),
(70, 1),
(71, 1),
(72, 1),
(73, 1),
(74, 1),
(75, 1),
(75, 2),
(76, 1),
(77, 1),
(77, 2),
(78, 1),
(78, 2),
(79, 1),
(80, 1),
(80, 2),
(81, 1),
(81, 2),
(82, 1),
(82, 2),
(83, 1),
(83, 2),
(84, 1),
(84, 2),
(85, 1),
(85, 2),
(86, 1),
(86, 2),
(87, 1),
(87, 2),
(88, 1),
(88, 2),
(89, 1),
(89, 2),
(90, 1),
(90, 2),
(91, 1),
(92, 1),
(93, 1),
(94, 1),
(95, 1),
(96, 1),
(97, 1),
(97, 2),
(98, 1),
(98, 2),
(99, 1),
(99, 2),
(100, 1),
(100, 2),
(101, 1),
(101, 2),
(102, 1),
(102, 2),
(103, 1),
(103, 2),
(104, 1),
(104, 2),
(105, 1),
(105, 2),
(106, 1),
(107, 1),
(107, 2),
(108, 1),
(109, 1),
(110, 1),
(110, 2),
(111, 1),
(112, 1),
(113, 1),
(113, 2),
(114, 1),
(115, 1);

-- --------------------------------------------------------

--
-- Table structure for table `remarks`
--

CREATE TABLE `remarks` (
  `remark_id` int(10) UNSIGNED NOT NULL,
  `class_teacher` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `principal` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `academic_term_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `remarks`
--

INSERT INTO `remarks` (`remark_id`, `class_teacher`, `principal`, `student_id`, `academic_term_id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'y6utg', 'jhcfg', 11, 2, 1, '2016-08-19 11:51:13', '2016-08-19 11:55:44'),
(2, 'jvgn uiyuv diuyd dydv dhyg jdhuvd d yuv d huydv jdhyuvd jhdyuvdv 67565768786sv 97d6857svusu8', '9786uv siud7986fcv adi8dvu jadi8v d86davu jda7b6a8vdu 8dadv ud86advu daa78davuyadafvuad8 a a8diai uadgb vjgn', 12, 2, 1, '2016-08-19 11:51:13', '2016-08-19 12:43:45'),
(3, NULL, NULL, 13, 2, 1, '2016-08-19 11:51:13', '2016-08-19 11:55:44'),
(4, NULL, NULL, 14, 2, 1, '2016-08-19 11:51:13', '2016-08-19 11:51:13'),
(5, 'uyjv', 'bjh', 15, 2, 1, '2016-08-19 11:51:13', '2016-08-19 11:51:13'),
(6, '7867', 'kjvhg', 17, 2, 1, '2016-08-19 11:51:13', '2016-08-19 11:55:44'),
(7, 'jvg', 'jhvg', 19, 2, 1, '2016-08-19 11:51:13', '2016-08-19 11:51:13'),
(8, 'jvgh', 'bjhvg', 45, 2, 1, '2016-08-19 11:51:13', '2016-08-19 11:51:13'),
(9, NULL, NULL, 47, 2, 1, '2016-08-19 11:51:13', '2016-08-19 11:51:13'),
(10, 'yvsm ssiysvj jyvjs syvjh', 'yvsj syuv ssmhfvs jsk', 1, 2, 1, '2016-08-19 11:57:06', '2016-08-19 11:57:06'),
(11, 'vshygs svysjhs suyj', NULL, 2, 2, 1, '2016-08-19 11:57:06', '2016-08-19 11:57:06'),
(12, NULL, 'shvsuj ssysiyukjsbys ssvj', 5, 2, 1, '2016-08-19 11:57:06', '2016-08-19 11:57:06'),
(13, 'ysuvjs siukjs ysiukvs ssy', 'v sysiukjsb sysisukbs siu', 6, 2, 1, '2016-08-19 11:57:07', '2016-08-19 11:57:07'),
(14, 'bs iiusbsyskju', 'ubs hyisuskj', 7, 2, 1, '2016-08-19 11:57:07', '2016-08-19 11:57:07'),
(15, 'usjbsugiu', 'bsisukbjssik usib sus7', 8, 2, 1, '2016-08-19 11:57:07', '2016-08-19 11:57:07'),
(16, ' sud biudub duis7ubjb', 'uib suisb dd89d usbd ', 9, 2, 1, '2016-08-19 11:57:07', '2016-08-19 11:57:07'),
(17, 'jsjkuddid d8odindud768ybs7 u7', 'v sidy8dg didh79dyb d7vd,djku', 10, 2, 1, '2016-08-19 11:57:07', '2016-08-19 11:57:07'),
(18, ' hsidybjdud7ub du7utw d768dh ', 'qu7b kdud7b ddi86ddd', 44, 2, 1, '2016-08-19 11:57:07', '2016-08-19 11:57:07');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `display_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_type_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `name`, `display_name`, `description`, `user_type_id`, `created_at`, `updated_at`) VALUES
(1, 'developer', 'Developer', 'The software developer', 1, '2016-03-29 22:30:11', '2016-04-28 21:36:59'),
(2, 'super_admin', 'Super Admin', 'System Administrator', 2, '2016-03-30 09:51:57', '2016-04-28 22:33:03'),
(3, 'sponsor', 'Sponsor', 'Sponsor', 3, '2016-04-16 17:25:54', '2016-04-28 21:36:59'),
(4, 'staff', 'Staff', 'Staff', 4, '2016-04-16 17:25:54', '2016-04-28 21:36:59'),
(5, 'class_teacher', 'Class Teacher', 'Class Teacher', 4, '2016-08-20 09:15:27', '2016-08-20 09:15:27');

-- --------------------------------------------------------

--
-- Table structure for table `roles_menus`
--

CREATE TABLE `roles_menus` (
  `role_id` int(10) UNSIGNED NOT NULL,
  `menu_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `roles_menus`
--

INSERT INTO `roles_menus` (`role_id`, `menu_id`) VALUES
(1, 1),
(1, 2),
(1, 4),
(2, 4),
(1, 6),
(2, 6),
(2, 1),
(4, 2),
(2, 2),
(1, 7),
(2, 7),
(1, 8),
(2, 8),
(1, 9),
(4, 9),
(1, 10),
(4, 10),
(1, 11),
(2, 9),
(2, 10),
(5, 2),
(5, 9),
(5, 10),
(5, 13),
(1, 13),
(2, 13),
(1, 14),
(3, 14),
(1, 15),
(3, 15),
(1, 17),
(2, 17);

-- --------------------------------------------------------

--
-- Table structure for table `roles_menu_headers`
--

CREATE TABLE `roles_menu_headers` (
  `role_id` int(10) UNSIGNED NOT NULL,
  `menu_header_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `roles_menu_headers`
--

INSERT INTO `roles_menu_headers` (`role_id`, `menu_header_id`) VALUES
(1, 1),
(1, 2),
(1, 4),
(4, 2),
(2, 2),
(2, 1),
(1, 5),
(4, 5),
(2, 5),
(5, 2),
(5, 5),
(1, 6),
(3, 6),
(3, 4),
(3, 7),
(1, 9),
(2, 9);

-- --------------------------------------------------------

--
-- Table structure for table `roles_menu_items`
--

CREATE TABLE `roles_menu_items` (
  `role_id` int(10) UNSIGNED NOT NULL,
  `menu_item_id` int(10) UNSIGNED DEFAULT NULL
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
(1, 20),
(2, 20),
(1, 21),
(2, 21),
(1, 22),
(2, 22),
(1, 23),
(2, 23),
(1, 24),
(4, 24),
(1, 25),
(4, 25),
(1, 26),
(2, 26),
(2, 24),
(2, 25),
(1, 28),
(2, 28),
(5, 29),
(1, 29),
(2, 29),
(5, 24),
(5, 25),
(5, 4),
(3, 30),
(1, 31),
(2, 31);

-- --------------------------------------------------------

--
-- Table structure for table `roles_sub_menu_items`
--

CREATE TABLE `roles_sub_menu_items` (
  `role_id` int(10) UNSIGNED NOT NULL,
  `sub_menu_item_id` int(10) UNSIGNED DEFAULT NULL
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
(1, 24),
(2, 24),
(1, 25),
(2, 25),
(5, 9),
(5, 10);

-- --------------------------------------------------------

--
-- Table structure for table `roles_sub_most_menu_items`
--

CREATE TABLE `roles_sub_most_menu_items` (
  `role_id` int(10) UNSIGNED NOT NULL,
  `sub_most_menu_item_id` int(10) UNSIGNED DEFAULT NULL
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

CREATE TABLE `role_user` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `role_user`
--

INSERT INTO `role_user` (`user_id`, `role_id`) VALUES
(1, 1),
(2, 2),
(3, 4),
(4, 5),
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
(122, 4),
(123, 4),
(124, 4),
(125, 4),
(126, 3),
(127, 3),
(128, 3);

-- --------------------------------------------------------

--
-- Table structure for table `sms`
--

CREATE TABLE `sms` (
  `sms_id` int(11) NOT NULL,
  `unit_bought` float NOT NULL,
  `unit_used` float NOT NULL,
  `status` int(2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sms`
--

INSERT INTO `sms` (`sms_id`, `unit_bought`, `unit_used`, `status`, `created_at`, `updated_at`) VALUES
(1, 5500, 13191.6, 2, '2015-11-25 16:22:46', '2016-01-27 05:45:56'),
(2, 5141.1, 1438.2, 1, '2016-03-03 07:17:46', '2016-09-20 17:44:18');

-- --------------------------------------------------------

--
-- Table structure for table `sponsors`
--

CREATE TABLE `sponsors` (
  `sponsor_id` int(10) UNSIGNED NOT NULL,
  `sponsor_no` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `first_name` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `other_name` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `phone_no` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone_no2` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8_unicode_ci,
  `lga_id` int(10) UNSIGNED DEFAULT NULL,
  `salutation_id` int(10) UNSIGNED DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staffs`
--

CREATE TABLE `staffs` (
  `staff_id` int(10) UNSIGNED NOT NULL,
  `staff_no` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `first_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `other_name` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `gender` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone_no` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone_no2` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8_unicode_ci,
  `lga_id` int(10) UNSIGNED DEFAULT NULL,
  `salutation_id` int(10) UNSIGNED DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(10) UNSIGNED NOT NULL,
  `first_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `middle_name` varchar(70) COLLATE utf8_unicode_ci DEFAULT NULL,
  `student_no` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gender` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `dob` date DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8_unicode_ci,
  `sponsor_id` int(10) UNSIGNED NOT NULL,
  `classroom_id` int(10) UNSIGNED NOT NULL,
  `status_id` int(10) UNSIGNED NOT NULL DEFAULT '1',
  `admitted_term_id` int(10) UNSIGNED NOT NULL,
  `lga_id` int(10) UNSIGNED DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `first_name`, `last_name`, `middle_name`, `student_no`, `gender`, `dob`, `avatar`, `address`, `sponsor_id`, `classroom_id`, `status_id`, `admitted_term_id`, `lga_id`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Chijioke', 'casmir', NULL, 'STD00001', 'Male', NULL, NULL, NULL, 62, 1, 1, 1, NULL, 2, '2016-05-31 14:04:09', '2016-05-31 14:04:10'),
(2, 'Omotoke', 'Elizabeth', NULL, 'STD00002', 'Female', NULL, NULL, NULL, 102, 1, 1, 1, NULL, 2, '2016-05-31 14:06:36', '2016-05-31 14:06:36'),
(4, 'Sharon', 'Ajoke', NULL, 'STD00004', 'Female', NULL, NULL, NULL, 54, 1, 2, 1, NULL, 2, '2016-05-31 14:34:39', '2016-05-31 14:34:39'),
(5, 'Stephanie', 'Uche', NULL, 'STD00005', 'Female', NULL, NULL, NULL, 74, 1, 1, 1, NULL, 2, '2016-05-31 14:36:08', '2016-05-31 14:36:08'),
(6, 'Naomi', 'Nkemjikam', NULL, 'STD00006', 'Female', NULL, NULL, NULL, 88, 1, 1, 1, NULL, 2, '2016-05-31 14:37:24', '2016-05-31 14:37:24'),
(7, 'Aanuoluwapo', 'Dorcas', NULL, 'STD00007', 'Female', NULL, NULL, NULL, 97, 1, 1, 1, NULL, 2, '2016-05-31 14:38:31', '2016-05-31 14:38:31'),
(8, 'Lauretta', 'Esther', NULL, 'STD00008', 'Female', NULL, NULL, NULL, 53, 1, 1, 1, NULL, 2, '2016-05-31 14:39:11', '2016-05-31 14:39:11'),
(9, 'wisdom', 'Chuibueze', NULL, 'STD00009', 'Male', NULL, NULL, NULL, 45, 1, 1, 1, NULL, 2, '2016-05-31 14:40:11', '2016-05-31 14:40:11'),
(10, 'Adebola', 'Favour', NULL, 'STD00010', 'Male', NULL, NULL, NULL, 21, 1, 1, 1, NULL, 2, '2016-05-31 14:41:15', '2016-05-31 14:41:15'),
(11, 'Eniola', 'Omotolani', '', 'STD00011', 'Female', '1999-11-25', NULL, NULL, 31, 2, 1, 1, 113, 2, '2016-05-31 14:51:36', '2016-08-29 18:01:05'),
(12, 'Chiamaka', 'Blessing', NULL, 'STD00012', 'Female', NULL, NULL, NULL, 118, 2, 1, 1, NULL, 2, '2016-05-31 14:52:41', '2016-05-31 14:52:41'),
(13, 'Osamudiameh', 'Queen', NULL, 'STD00013', 'Female', NULL, NULL, NULL, 71, 2, 1, 1, NULL, 2, '2016-05-31 14:53:34', '2016-05-31 14:53:34'),
(14, 'Benita', 'Omolola', NULL, 'STD00014', 'Female', NULL, NULL, NULL, 115, 2, 1, 1, NULL, 2, '2016-05-31 15:05:20', '2016-05-31 15:05:20'),
(15, 'Chidozie', 'Ephraim', NULL, 'STD00015', 'Male', NULL, NULL, NULL, 75, 2, 1, 1, NULL, 2, '2016-05-31 15:07:17', '2016-05-31 15:07:17'),
(17, 'Amaka', 'Deborah', NULL, 'STD00017', 'Female', NULL, NULL, NULL, 82, 2, 1, 1, NULL, 2, '2016-05-31 15:08:41', '2016-05-31 15:08:41'),
(19, 'Ibukunoluwa', 'Folarin', NULL, 'STD00019', 'Male', NULL, NULL, NULL, 39, 2, 1, 1, NULL, 2, '2016-05-31 15:10:24', '2016-05-31 15:10:24'),
(20, 'Emmanuella', 'Ayomikun', NULL, 'STD00020', 'Female', NULL, NULL, NULL, 78, 3, 1, 1, NULL, 2, '2016-05-31 15:25:42', '2016-05-31 15:25:42'),
(21, 'Angela', 'Damilola', NULL, 'STD00021', 'Female', NULL, NULL, NULL, 109, 3, 1, 1, NULL, 2, '2016-05-31 15:26:59', '2016-05-31 15:26:59'),
(22, 'Adeolu', 'Peter', NULL, 'STD00022', 'Male', NULL, NULL, NULL, 79, 3, 1, 1, NULL, 2, '2016-05-31 15:45:38', '2016-05-31 15:45:38'),
(23, 'Damilola', 'Abdul-Samad', NULL, 'STD00023', 'Male', NULL, NULL, NULL, 28, 3, 1, 1, NULL, 2, '2016-05-31 15:47:24', '2016-05-31 15:47:24'),
(24, 'Esther', 'Anita', NULL, 'STD00024', 'Female', NULL, NULL, NULL, 96, 3, 1, 1, NULL, 2, '2016-05-31 15:48:11', '2016-05-31 15:48:11'),
(25, 'Ayobami', 'Felix', NULL, 'STD00025', 'Male', NULL, NULL, NULL, 42, 3, 1, 1, NULL, 2, '2016-05-31 15:49:01', '2016-05-31 15:49:01'),
(26, 'Chidera', 'Marvelous', NULL, 'STD00026', 'Male', NULL, NULL, NULL, 33, 3, 1, 1, NULL, 2, '2016-05-31 15:49:47', '2016-05-31 15:49:47'),
(27, 'Sarah', 'Oyinenche', NULL, 'STD00027', 'Female', NULL, NULL, NULL, 91, 3, 1, 1, NULL, 2, '2016-05-31 15:50:42', '2016-05-31 15:50:42'),
(28, 'Donald', 'Isioma', NULL, 'STD00028', 'Male', NULL, NULL, NULL, 86, 3, 1, 1, NULL, 2, '2016-05-31 15:51:51', '2016-05-31 15:51:52'),
(29, 'Edna', 'Favour', NULL, 'STD00029', 'Female', NULL, NULL, NULL, 54, 4, 1, 1, NULL, 2, '2016-05-31 16:11:40', '2016-05-31 16:11:40'),
(30, 'Zainab', 'Adeola', NULL, 'STD00030', 'Female', NULL, NULL, NULL, 111, 4, 1, 1, NULL, 2, '2016-05-31 16:12:37', '2016-05-31 16:12:37'),
(32, 'Abiola', 'Precious', '', 'STD00032', 'Female', '2010-06-09', NULL, NULL, 31, 4, 1, 1, 0, 2, '2016-05-31 16:25:19', '2016-08-29 18:43:22'),
(33, 'Murede', 'Raheem', NULL, 'STD00033', 'Male', NULL, NULL, NULL, 79, 4, 1, 1, NULL, 2, '2016-05-31 16:26:32', '2016-05-31 16:26:32'),
(34, 'Destiny', 'Isosa', NULL, 'STD00034', 'Male', NULL, NULL, NULL, 51, 4, 1, 1, NULL, 2, '2016-05-31 16:34:03', '2016-05-31 16:34:03'),
(35, 'Precious', 'David', NULL, 'STD00035', 'Male', NULL, NULL, NULL, 96, 4, 1, 1, NULL, 2, '2016-05-31 16:34:54', '2016-05-31 16:34:54'),
(36, 'Angel', 'Mmesoma', NULL, 'STD00036', 'Female', NULL, NULL, NULL, 56, 4, 1, 1, NULL, 2, '2016-05-31 16:35:45', '2016-05-31 16:35:45'),
(37, 'Temiloluwa', 'Wuraola', NULL, 'STD00037', 'Female', NULL, NULL, NULL, 23, 4, 1, 1, NULL, 2, '2016-05-31 16:36:38', '2016-05-31 16:36:38'),
(38, 'Justin', 'Emmanuel', NULL, 'STD00038', 'Male', NULL, NULL, NULL, 54, 5, 1, 1, NULL, 2, '2016-05-31 16:37:19', '2016-05-31 16:37:19'),
(39, 'Divine', 'Obusor', NULL, 'STD00039', 'Female', NULL, NULL, NULL, 120, 5, 1, 1, NULL, 2, '2016-05-31 16:38:29', '2016-05-31 16:38:29'),
(40, 'Peace', 'Amaka', NULL, 'STD00040', 'Female', NULL, NULL, NULL, 107, 5, 1, 1, NULL, 2, '2016-05-31 16:39:11', '2016-05-31 16:39:11'),
(41, 'Boluwatife', 'Grace', NULL, 'STD00041', 'Female', NULL, NULL, NULL, 30, 5, 1, 1, NULL, 2, '2016-05-31 16:40:07', '2016-05-31 16:40:07'),
(42, 'Omolara', 'Joy', NULL, 'STD00042', 'Female', NULL, NULL, NULL, 77, 5, 1, 1, NULL, 2, '2016-05-31 16:40:57', '2016-05-31 16:40:57'),
(43, 'Oreoluwa', 'Mulikat', NULL, 'STD00043', 'Female', NULL, NULL, NULL, 126, 5, 1, 1, NULL, 2, '2016-05-31 17:09:13', '2016-05-31 17:09:13'),
(44, 'Adesope', 'Anthony', NULL, 'STD00044', 'Male', NULL, NULL, NULL, 128, 1, 1, 1, NULL, 2, '2016-05-31 17:19:06', '2016-05-31 17:19:06'),
(45, 'Suleiman', 'Ori-Owo', NULL, 'STD00045', 'Male', NULL, NULL, NULL, 112, 2, 1, 1, NULL, 2, '2016-05-31 17:22:01', '2016-05-31 17:22:01'),
(47, 'Tejumola', 'Durojaye', NULL, 'STD00047', 'Female', NULL, NULL, NULL, 47, 2, 1, 1, NULL, 1, '2016-06-03 16:57:51', '2016-06-03 16:57:51');

-- --------------------------------------------------------

--
-- Stand-in structure for view `students_classroomviews`
--
CREATE TABLE `students_classroomviews` (
`fullname` varchar(101)
,`student_no` varchar(10)
,`classroom` varchar(255)
,`classroom_id` int(10) unsigned
,`student_id` int(10) unsigned
,`classlevel` varchar(255)
,`classlevel_id` int(10) unsigned
,`sponsor_id` int(10) unsigned
,`sponsor_name` varchar(91)
,`academic_year_id` int(10) unsigned
,`academic_year` varchar(100)
,`status_id` int(10) unsigned
);

-- --------------------------------------------------------

--
-- Table structure for table `student_classes`
--

CREATE TABLE `student_classes` (
  `student_class_id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `classroom_id` int(10) UNSIGNED NOT NULL,
  `academic_year_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `student_classes`
--

INSERT INTO `student_classes` (`student_class_id`, `student_id`, `classroom_id`, `academic_year_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, '2016-05-31 14:04:09', '2016-05-31 14:04:09'),
(2, 2, 1, 1, '2016-05-31 14:06:36', '2016-05-31 14:06:36'),
(4, 4, 1, 1, '2016-05-31 14:34:39', '2016-05-31 14:34:39'),
(5, 5, 1, 1, '2016-05-31 14:36:08', '2016-05-31 14:36:08'),
(6, 6, 1, 1, '2016-05-31 14:37:24', '2016-05-31 14:37:24'),
(7, 7, 1, 1, '2016-05-31 14:38:31', '2016-05-31 14:38:31'),
(8, 8, 1, 1, '2016-05-31 14:39:11', '2016-05-31 14:39:11'),
(9, 9, 1, 1, '2016-05-31 14:40:11', '2016-05-31 14:40:11'),
(10, 10, 1, 1, '2016-05-31 14:41:15', '2016-05-31 14:41:15'),
(11, 11, 2, 1, '2016-05-31 14:51:36', '2016-05-31 14:51:36'),
(12, 12, 2, 1, '2016-05-31 14:52:41', '2016-05-31 14:52:41'),
(13, 13, 2, 1, '2016-05-31 14:53:34', '2016-05-31 14:53:34'),
(14, 14, 2, 1, '2016-05-31 15:05:20', '2016-05-31 15:05:20'),
(15, 15, 2, 1, '2016-05-31 15:07:17', '2016-05-31 15:07:17'),
(17, 17, 2, 1, '2016-05-31 15:08:41', '2016-05-31 15:08:41'),
(19, 19, 2, 1, '2016-05-31 15:10:24', '2016-05-31 15:10:24'),
(20, 20, 3, 1, '2016-05-31 15:25:42', '2016-05-31 15:25:42'),
(21, 21, 3, 1, '2016-05-31 15:26:59', '2016-05-31 15:26:59'),
(22, 22, 3, 1, '2016-05-31 15:45:38', '2016-05-31 15:45:38'),
(23, 23, 3, 1, '2016-05-31 15:47:24', '2016-05-31 15:47:24'),
(24, 24, 3, 1, '2016-05-31 15:48:11', '2016-05-31 15:48:11'),
(25, 25, 3, 1, '2016-05-31 15:49:01', '2016-05-31 15:49:01'),
(26, 26, 3, 1, '2016-05-31 15:49:47', '2016-05-31 15:49:47'),
(27, 27, 3, 1, '2016-05-31 15:50:42', '2016-05-31 15:50:42'),
(28, 28, 3, 1, '2016-05-31 15:51:52', '2016-05-31 15:51:52'),
(29, 29, 4, 1, '2016-05-31 16:11:40', '2016-05-31 16:11:40'),
(30, 30, 4, 1, '2016-05-31 16:12:37', '2016-05-31 16:12:37'),
(32, 32, 4, 1, '2016-05-31 16:25:19', '2016-05-31 16:25:19'),
(33, 33, 4, 1, '2016-05-31 16:26:32', '2016-05-31 16:26:32'),
(34, 34, 4, 1, '2016-05-31 16:34:03', '2016-05-31 16:34:03'),
(35, 35, 4, 1, '2016-05-31 16:34:54', '2016-05-31 16:34:54'),
(36, 36, 4, 1, '2016-05-31 16:35:45', '2016-05-31 16:35:45'),
(37, 37, 4, 1, '2016-05-31 16:36:38', '2016-05-31 16:36:38'),
(38, 38, 5, 1, '2016-05-31 16:37:19', '2016-05-31 16:37:19'),
(39, 39, 5, 1, '2016-05-31 16:38:29', '2016-05-31 16:38:29'),
(40, 40, 5, 1, '2016-05-31 16:39:11', '2016-05-31 16:39:11'),
(41, 41, 5, 1, '2016-05-31 16:40:07', '2016-05-31 16:40:07'),
(42, 42, 5, 1, '2016-05-31 16:40:57', '2016-05-31 16:40:57'),
(43, 43, 5, 1, '2016-05-31 17:09:13', '2016-05-31 17:09:13'),
(44, 44, 1, 1, '2016-05-31 17:19:06', '2016-05-31 17:19:06'),
(45, 45, 2, 1, '2016-05-31 17:22:01', '2016-05-31 17:22:01'),
(47, 47, 2, 1, '2016-06-03 16:57:51', '2016-06-03 16:57:51');

-- --------------------------------------------------------

--
-- Table structure for table `student_subjects`
--

CREATE TABLE `student_subjects` (
  `student_id` int(10) UNSIGNED NOT NULL,
  `subject_classroom_id` int(10) UNSIGNED NOT NULL
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
(1, 89),
(1, 90),
(1, 91),
(1, 92),
(1, 93),
(1, 94),
(1, 95),
(1, 96),
(1, 97),
(1, 98),
(1, 99),
(1, 100),
(1, 101),
(1, 102),
(1, 157),
(1, 164),
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
(2, 89),
(2, 90),
(2, 91),
(2, 92),
(2, 93),
(2, 94),
(2, 95),
(2, 96),
(2, 97),
(2, 98),
(2, 99),
(2, 100),
(2, 101),
(2, 102),
(2, 157),
(2, 164),
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
(4, 89),
(4, 90),
(4, 91),
(4, 92),
(4, 93),
(4, 94),
(4, 95),
(4, 96),
(4, 97),
(4, 98),
(4, 99),
(4, 100),
(4, 101),
(4, 102),
(4, 157),
(4, 164),
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
(5, 89),
(5, 90),
(5, 91),
(5, 92),
(5, 93),
(5, 94),
(5, 95),
(5, 96),
(5, 97),
(5, 98),
(5, 99),
(5, 100),
(5, 101),
(5, 102),
(5, 157),
(5, 164),
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
(6, 89),
(6, 90),
(6, 91),
(6, 92),
(6, 93),
(6, 94),
(6, 95),
(6, 96),
(6, 97),
(6, 98),
(6, 99),
(6, 100),
(6, 101),
(6, 102),
(6, 157),
(6, 164),
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
(7, 89),
(7, 90),
(7, 91),
(7, 92),
(7, 93),
(7, 94),
(7, 95),
(7, 96),
(7, 97),
(7, 98),
(7, 99),
(7, 100),
(7, 101),
(7, 102),
(7, 157),
(7, 164),
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
(8, 89),
(8, 90),
(8, 91),
(8, 92),
(8, 93),
(8, 94),
(8, 95),
(8, 96),
(8, 97),
(8, 98),
(8, 99),
(8, 100),
(8, 101),
(8, 102),
(8, 157),
(8, 164),
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
(9, 89),
(9, 90),
(9, 91),
(9, 92),
(9, 93),
(9, 94),
(9, 95),
(9, 96),
(9, 97),
(9, 98),
(9, 99),
(9, 100),
(9, 101),
(9, 102),
(9, 157),
(9, 164),
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
(10, 89),
(10, 90),
(10, 91),
(10, 92),
(10, 93),
(10, 94),
(10, 95),
(10, 96),
(10, 97),
(10, 98),
(10, 99),
(10, 100),
(10, 101),
(10, 102),
(10, 157),
(10, 164),
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
(11, 103),
(11, 104),
(11, 105),
(11, 106),
(11, 107),
(11, 108),
(11, 109),
(11, 110),
(11, 111),
(11, 112),
(11, 113),
(11, 114),
(11, 115),
(11, 116),
(11, 156),
(11, 166),
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
(12, 103),
(12, 104),
(12, 105),
(12, 106),
(12, 107),
(12, 108),
(12, 109),
(12, 110),
(12, 111),
(12, 112),
(12, 113),
(12, 114),
(12, 115),
(12, 116),
(12, 156),
(12, 166),
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
(13, 103),
(13, 104),
(13, 105),
(13, 106),
(13, 107),
(13, 108),
(13, 109),
(13, 110),
(13, 111),
(13, 112),
(13, 113),
(13, 114),
(13, 115),
(13, 116),
(13, 156),
(13, 166),
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
(14, 103),
(14, 104),
(14, 105),
(14, 106),
(14, 107),
(14, 108),
(14, 109),
(14, 110),
(14, 111),
(14, 112),
(14, 113),
(14, 114),
(14, 115),
(14, 116),
(14, 156),
(14, 166),
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
(15, 103),
(15, 104),
(15, 105),
(15, 106),
(15, 107),
(15, 108),
(15, 109),
(15, 110),
(15, 111),
(15, 112),
(15, 113),
(15, 114),
(15, 115),
(15, 116),
(15, 156),
(15, 166),
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
(17, 103),
(17, 104),
(17, 105),
(17, 106),
(17, 107),
(17, 108),
(17, 109),
(17, 110),
(17, 111),
(17, 112),
(17, 113),
(17, 114),
(17, 115),
(17, 116),
(17, 156),
(17, 166),
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
(19, 103),
(19, 104),
(19, 105),
(19, 106),
(19, 107),
(19, 108),
(19, 109),
(19, 110),
(19, 111),
(19, 112),
(19, 113),
(19, 114),
(19, 115),
(19, 116),
(19, 156),
(19, 166),
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
(20, 117),
(20, 118),
(20, 119),
(20, 120),
(20, 121),
(20, 122),
(20, 123),
(20, 124),
(20, 125),
(20, 126),
(20, 127),
(20, 159),
(20, 160),
(20, 161),
(20, 162),
(20, 168),
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
(21, 117),
(21, 118),
(21, 119),
(21, 120),
(21, 121),
(21, 122),
(21, 123),
(21, 124),
(21, 125),
(21, 126),
(21, 127),
(21, 159),
(21, 160),
(21, 161),
(21, 162),
(21, 168),
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
(22, 117),
(22, 118),
(22, 119),
(22, 120),
(22, 121),
(22, 122),
(22, 123),
(22, 124),
(22, 125),
(22, 126),
(22, 127),
(22, 159),
(22, 160),
(22, 161),
(22, 162),
(22, 168),
(23, 29),
(23, 30),
(23, 31),
(23, 32),
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
(23, 117),
(23, 118),
(23, 119),
(23, 120),
(23, 122),
(23, 123),
(23, 124),
(23, 125),
(23, 126),
(23, 127),
(23, 159),
(23, 160),
(23, 161),
(23, 162),
(23, 168),
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
(24, 117),
(24, 118),
(24, 119),
(24, 120),
(24, 121),
(24, 122),
(24, 123),
(24, 124),
(24, 125),
(24, 126),
(24, 127),
(24, 159),
(24, 160),
(24, 161),
(24, 162),
(24, 168),
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
(25, 117),
(25, 118),
(25, 119),
(25, 120),
(25, 121),
(25, 122),
(25, 123),
(25, 124),
(25, 125),
(25, 126),
(25, 127),
(25, 159),
(25, 160),
(25, 161),
(25, 162),
(25, 168),
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
(26, 117),
(26, 118),
(26, 119),
(26, 120),
(26, 121),
(26, 122),
(26, 123),
(26, 124),
(26, 125),
(26, 126),
(26, 127),
(26, 159),
(26, 160),
(26, 161),
(26, 162),
(26, 168),
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
(27, 117),
(27, 118),
(27, 119),
(27, 120),
(27, 121),
(27, 122),
(27, 123),
(27, 124),
(27, 125),
(27, 126),
(27, 127),
(27, 159),
(27, 160),
(27, 161),
(27, 162),
(27, 168),
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
(28, 117),
(28, 118),
(28, 119),
(28, 120),
(28, 121),
(28, 122),
(28, 123),
(28, 124),
(28, 125),
(28, 126),
(28, 127),
(28, 159),
(28, 160),
(28, 161),
(28, 162),
(28, 168),
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
(29, 128),
(29, 129),
(29, 130),
(29, 131),
(29, 132),
(29, 133),
(29, 134),
(29, 135),
(29, 136),
(29, 137),
(29, 138),
(29, 139),
(29, 140),
(29, 141),
(29, 158),
(29, 170),
(29, 171),
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
(30, 128),
(30, 129),
(30, 130),
(30, 131),
(30, 133),
(30, 134),
(30, 135),
(30, 136),
(30, 137),
(30, 138),
(30, 139),
(30, 140),
(30, 141),
(30, 158),
(30, 170),
(30, 171),
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
(32, 128),
(32, 129),
(32, 130),
(32, 131),
(32, 132),
(32, 133),
(32, 134),
(32, 135),
(32, 136),
(32, 137),
(32, 138),
(32, 139),
(32, 140),
(32, 141),
(32, 158),
(32, 170),
(32, 171),
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
(33, 128),
(33, 129),
(33, 130),
(33, 131),
(33, 132),
(33, 133),
(33, 134),
(33, 135),
(33, 136),
(33, 137),
(33, 138),
(33, 139),
(33, 140),
(33, 141),
(33, 158),
(33, 170),
(33, 171),
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
(34, 128),
(34, 129),
(34, 130),
(34, 131),
(34, 132),
(34, 133),
(34, 134),
(34, 135),
(34, 136),
(34, 137),
(34, 138),
(34, 139),
(34, 140),
(34, 141),
(34, 158),
(34, 170),
(34, 171),
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
(35, 128),
(35, 129),
(35, 130),
(35, 131),
(35, 132),
(35, 133),
(35, 134),
(35, 135),
(35, 136),
(35, 137),
(35, 138),
(35, 139),
(35, 140),
(35, 141),
(35, 158),
(35, 170),
(35, 171),
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
(36, 128),
(36, 129),
(36, 130),
(36, 131),
(36, 132),
(36, 133),
(36, 134),
(36, 135),
(36, 136),
(36, 137),
(36, 138),
(36, 139),
(36, 140),
(36, 141),
(36, 158),
(36, 170),
(36, 171),
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
(37, 128),
(37, 129),
(37, 130),
(37, 131),
(37, 132),
(37, 133),
(37, 134),
(37, 135),
(37, 136),
(37, 137),
(37, 138),
(37, 139),
(37, 140),
(37, 141),
(37, 158),
(37, 170),
(37, 171),
(38, 57),
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
(38, 71),
(38, 88),
(38, 142),
(38, 144),
(38, 145),
(38, 147),
(38, 148),
(38, 150),
(38, 151),
(38, 152),
(38, 153),
(38, 155),
(38, 172),
(39, 57),
(39, 58),
(39, 59),
(39, 60),
(39, 61),
(39, 62),
(39, 63),
(39, 64),
(39, 65),
(39, 67),
(39, 68),
(39, 70),
(39, 71),
(39, 88),
(39, 142),
(39, 143),
(39, 145),
(39, 147),
(39, 148),
(39, 150),
(39, 153),
(39, 154),
(39, 155),
(39, 172),
(40, 57),
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
(40, 71),
(40, 88),
(40, 142),
(40, 144),
(40, 145),
(40, 147),
(40, 148),
(40, 150),
(40, 151),
(40, 152),
(40, 153),
(40, 155),
(40, 172),
(41, 57),
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
(41, 71),
(41, 88),
(41, 142),
(41, 144),
(41, 145),
(41, 147),
(41, 148),
(41, 150),
(41, 151),
(41, 152),
(41, 153),
(41, 155),
(41, 172),
(42, 57),
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
(42, 71),
(42, 88),
(42, 142),
(42, 144),
(42, 145),
(42, 147),
(42, 148),
(42, 150),
(42, 151),
(42, 152),
(42, 153),
(42, 155),
(42, 172),
(43, 57),
(43, 58),
(43, 59),
(43, 60),
(43, 61),
(43, 62),
(43, 63),
(43, 64),
(43, 65),
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
(44, 89),
(44, 90),
(44, 91),
(44, 92),
(44, 93),
(44, 94),
(44, 95),
(44, 96),
(44, 97),
(44, 98),
(44, 99),
(44, 100),
(44, 101),
(44, 102),
(44, 157),
(44, 164),
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
(45, 103),
(45, 104),
(45, 105),
(45, 106),
(45, 107),
(45, 108),
(45, 109),
(45, 110),
(45, 111),
(45, 112),
(45, 113),
(45, 114),
(45, 115),
(45, 116),
(45, 156),
(45, 166),
(47, 15),
(47, 17),
(47, 19),
(47, 21),
(47, 24),
(47, 25),
(47, 27),
(47, 103),
(47, 104),
(47, 105),
(47, 106),
(47, 107),
(47, 108),
(47, 109),
(47, 110),
(47, 111),
(47, 112),
(47, 113),
(47, 114),
(47, 115),
(47, 116),
(47, 156),
(47, 166);

-- --------------------------------------------------------

--
-- Stand-in structure for view `subjects_assessmentsviews`
--
CREATE TABLE `subjects_assessmentsviews` (
`tutor` varchar(91)
,`tutor_id` int(10) unsigned
,`classroom_id` int(10) unsigned
,`subject_classroom_id` int(10) unsigned
,`subject_id` int(10) unsigned
,`subject` varchar(255)
,`subject_group_id` int(10) unsigned
,`academic_term_id` int(10) unsigned
,`academic_term` varchar(100)
,`exam_status_id` int(10) unsigned
,`exam_status` varchar(10)
,`classlevel_id` int(10) unsigned
,`classroom` varchar(255)
,`assessment_id` int(10) unsigned
,`marked` int(10) unsigned
,`assessment_setup_detail_id` int(10) unsigned
,`number` tinyint(4)
,`weight_point` double(8,2) unsigned
,`percentage` int(10) unsigned
,`assessment_setup_id` int(10) unsigned
,`submission_date` date
,`description` varchar(255)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `subjects_classroomviews`
--
CREATE TABLE `subjects_classroomviews` (
`tutor` varchar(91)
,`tutor_id` int(10) unsigned
,`classroom_id` int(10) unsigned
,`subject_classroom_id` int(10) unsigned
,`subject_id` int(10) unsigned
,`subject` varchar(255)
,`subject_group_id` int(10) unsigned
,`academic_term_id` int(10) unsigned
,`academic_term` varchar(100)
,`exam_status_id` int(10) unsigned
,`exam_status` varchar(10)
,`classlevel_id` int(10) unsigned
,`classroom` varchar(255)
);

-- --------------------------------------------------------

--
-- Table structure for table `subject_classrooms`
--

CREATE TABLE `subject_classrooms` (
  `subject_classroom_id` int(10) UNSIGNED NOT NULL,
  `subject_id` int(10) UNSIGNED NOT NULL,
  `classroom_id` int(10) UNSIGNED NOT NULL,
  `academic_term_id` int(10) UNSIGNED NOT NULL,
  `exam_status_id` int(10) UNSIGNED NOT NULL DEFAULT '2',
  `tutor_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `subject_classrooms`
--

INSERT INTO `subject_classrooms` (`subject_classroom_id`, `subject_id`, `classroom_id`, `academic_term_id`, `exam_status_id`, `tutor_id`, `created_at`, `updated_at`) VALUES
(1, 14, 1, 1, 2, 122, '2016-05-18 17:41:54', '2016-05-18 17:41:45'),
(2, 3, 1, 1, 2, 122, '2016-05-18 17:41:54', '2016-05-18 17:41:54'),
(3, 4, 1, 1, 2, 7, '2016-05-18 17:41:54', '2016-05-18 17:16:01'),
(4, 5, 1, 1, 2, 8, '2016-05-18 17:41:54', '2016-05-18 17:16:08'),
(5, 16, 1, 1, 2, 4, NULL, '2016-05-18 17:19:27'),
(6, 38, 1, 1, 2, 6, '2016-05-18 17:41:54', '2016-05-18 17:17:08'),
(7, 9, 1, 1, 2, 3, NULL, '2016-05-18 17:17:07'),
(8, 1, 1, 1, 2, 6, NULL, '2016-05-18 17:17:14'),
(9, 39, 1, 1, 2, 123, NULL, '2016-05-18 17:42:01'),
(10, 15, 1, 1, 2, 3, NULL, '2016-05-18 17:17:32'),
(11, 2, 1, 1, 2, 7, NULL, '2016-05-18 17:19:42'),
(12, 8, 1, 1, 2, 12, NULL, '2016-05-18 17:19:47'),
(13, 6, 1, 1, 2, 4, NULL, '2016-05-18 17:19:50'),
(14, 13, 1, 1, 2, 124, NULL, '2016-05-18 17:42:08'),
(15, 14, 2, 1, 2, 122, NULL, '2016-05-18 17:42:54'),
(16, 3, 2, 1, 2, 122, NULL, '2016-05-18 17:42:58'),
(17, 4, 2, 1, 2, 7, NULL, '2016-05-18 17:43:03'),
(18, 5, 2, 1, 2, 8, NULL, '2016-05-18 17:43:06'),
(19, 16, 2, 1, 2, 4, NULL, '2016-05-18 17:43:12'),
(20, 38, 2, 1, 2, 6, NULL, '2016-05-18 17:43:18'),
(21, 9, 2, 1, 2, 3, NULL, '2016-05-18 17:43:22'),
(22, 1, 2, 1, 2, 6, NULL, '2016-05-18 17:43:26'),
(23, 39, 2, 1, 2, 123, NULL, '2016-05-18 17:43:30'),
(24, 15, 2, 1, 2, 3, NULL, '2016-05-18 17:43:33'),
(25, 2, 2, 1, 2, 7, NULL, '2016-05-18 17:43:40'),
(26, 8, 2, 1, 2, 12, NULL, '2016-05-18 17:43:43'),
(27, 6, 2, 1, 2, 4, NULL, '2016-05-18 17:43:54'),
(28, 13, 2, 1, 2, 124, NULL, '2016-05-18 17:43:58'),
(29, 14, 3, 1, 2, 3, NULL, '2016-05-18 17:49:07'),
(30, 3, 3, 1, 2, 122, NULL, '2016-05-18 17:45:42'),
(31, 4, 3, 1, 2, 7, NULL, '2016-05-18 17:45:49'),
(32, 5, 3, 1, 2, 8, NULL, '2016-05-18 17:46:01'),
(33, 16, 3, 1, 2, 4, NULL, '2016-06-10 18:14:23'),
(34, 38, 3, 1, 2, 4, NULL, '2016-05-18 17:46:20'),
(35, 9, 3, 1, 2, 3, NULL, '2016-05-18 17:46:23'),
(36, 1, 3, 1, 2, 6, NULL, '2016-05-18 17:46:30'),
(37, 39, 3, 1, 2, 123, NULL, '2016-05-18 17:46:36'),
(38, 15, 3, 1, 2, 3, NULL, '2016-05-18 17:46:38'),
(39, 2, 3, 1, 2, 7, NULL, '2016-05-18 17:46:51'),
(43, 14, 4, 1, 2, 122, NULL, '2016-05-18 17:50:43'),
(44, 3, 4, 1, 2, 122, NULL, '2016-05-18 17:50:47'),
(45, 4, 4, 1, 2, 7, NULL, '2016-05-18 17:50:51'),
(46, 5, 4, 1, 2, 8, NULL, '2016-05-18 17:50:59'),
(47, 16, 4, 1, 2, 124, NULL, '2016-05-18 17:51:10'),
(48, 38, 4, 1, 2, 4, NULL, '2016-05-18 17:51:14'),
(49, 9, 4, 1, 2, 3, NULL, '2016-05-18 17:51:17'),
(50, 1, 4, 1, 2, 6, NULL, '2016-05-18 17:51:28'),
(51, 39, 4, 1, 2, 123, NULL, '2016-05-18 17:51:38'),
(52, 15, 4, 1, 2, 3, NULL, '2016-05-18 17:51:41'),
(53, 2, 4, 1, 2, 7, NULL, '2016-05-18 17:51:47'),
(54, 8, 4, 1, 2, 12, NULL, '2016-05-18 17:51:50'),
(55, 6, 4, 1, 2, 4, NULL, '2016-05-18 17:51:56'),
(56, 13, 4, 1, 2, 124, NULL, '2016-05-18 17:52:01'),
(57, 23, 5, 1, 2, 122, NULL, '2016-05-18 17:52:27'),
(58, 22, 5, 1, 2, 122, NULL, '2016-05-18 17:52:31'),
(59, 16, 5, 1, 2, 124, NULL, '2016-05-18 17:52:42'),
(60, 38, 5, 1, 2, 6, NULL, '2016-06-10 18:15:31'),
(61, 9, 5, 1, 2, 3, NULL, '2016-05-18 17:52:54'),
(62, 32, 5, 1, 2, 10, NULL, '2016-05-18 17:52:57'),
(63, 1, 5, 1, 2, 6, NULL, '2016-05-18 17:53:02'),
(64, 24, 5, 1, 2, 3, NULL, '2016-05-18 17:53:12'),
(65, 34, 5, 1, 2, 9, NULL, '2016-05-18 17:53:16'),
(66, 33, 5, 1, 2, 4, NULL, '2016-05-18 17:53:20'),
(67, 19, 5, 1, 2, 6, NULL, '2016-05-18 17:53:31'),
(68, 2, 5, 1, 2, 7, NULL, '2016-05-18 17:53:34'),
(70, 21, 5, 1, 2, 7, NULL, '2016-05-18 17:53:49'),
(71, 13, 5, 1, 2, 124, NULL, '2016-05-18 17:53:52'),
(72, 26, 2, 1, 2, 125, NULL, '2016-05-18 18:03:06'),
(73, 26, 1, 1, 2, 125, NULL, '2016-05-18 18:02:37'),
(74, 26, 4, 1, 2, 125, NULL, '2016-05-18 18:03:53'),
(75, 26, 3, 1, 2, 125, NULL, '2016-05-18 18:03:31'),
(76, 8, 3, 1, 2, 12, NULL, '2016-05-18 17:48:40'),
(77, 6, 3, 1, 2, 4, NULL, '2016-05-18 17:48:44'),
(78, 13, 3, 1, 2, 124, NULL, '2016-05-18 17:48:50'),
(79, 48, 1, 1, 2, NULL, NULL, NULL),
(80, 47, 1, 1, 2, NULL, NULL, NULL),
(81, 48, 2, 1, 2, NULL, NULL, NULL),
(82, 47, 2, 1, 2, NULL, NULL, NULL),
(83, 48, 3, 1, 2, NULL, NULL, NULL),
(84, 47, 3, 1, 2, NULL, NULL, NULL),
(85, 48, 4, 1, 2, NULL, NULL, NULL),
(86, 47, 4, 1, 2, NULL, NULL, NULL),
(87, 7, 4, 1, 2, NULL, NULL, NULL),
(88, 47, 5, 1, 2, NULL, NULL, NULL),
(89, 14, 1, 2, 1, 122, NULL, NULL),
(90, 3, 1, 2, 1, 122, NULL, NULL),
(91, 4, 1, 2, 1, 7, NULL, NULL),
(92, 5, 1, 2, 1, 8, NULL, NULL),
(93, 16, 1, 2, 1, 4, NULL, NULL),
(94, 38, 1, 2, 1, 6, NULL, NULL),
(95, 9, 1, 2, 1, 3, NULL, NULL),
(96, 1, 1, 2, 1, 6, NULL, NULL),
(97, 39, 1, 2, 1, 123, NULL, NULL),
(98, 15, 1, 2, 1, 3, NULL, NULL),
(99, 2, 1, 2, 1, 7, NULL, NULL),
(100, 8, 1, 2, 1, 12, NULL, NULL),
(101, 6, 1, 2, 1, 4, NULL, NULL),
(102, 13, 1, 2, 1, 124, NULL, NULL),
(103, 14, 2, 2, 1, 122, NULL, NULL),
(104, 3, 2, 2, 1, 122, NULL, NULL),
(105, 4, 2, 2, 1, 7, NULL, NULL),
(106, 5, 2, 2, 1, 8, NULL, NULL),
(107, 16, 2, 2, 1, 4, NULL, NULL),
(108, 38, 2, 2, 1, 6, NULL, NULL),
(109, 9, 2, 2, 1, 3, NULL, NULL),
(110, 1, 2, 2, 1, 6, NULL, NULL),
(111, 39, 2, 2, 1, 123, NULL, NULL),
(112, 15, 2, 2, 1, 3, NULL, NULL),
(113, 2, 2, 2, 1, 7, NULL, NULL),
(114, 8, 2, 2, 1, 12, NULL, NULL),
(115, 6, 2, 2, 1, 4, NULL, NULL),
(116, 13, 2, 2, 1, 124, NULL, NULL),
(117, 14, 3, 2, 1, 3, NULL, NULL),
(118, 3, 3, 2, 1, 122, NULL, NULL),
(119, 4, 3, 2, 1, 7, NULL, NULL),
(120, 5, 3, 2, 1, 8, NULL, NULL),
(121, 16, 3, 2, 1, 4, NULL, NULL),
(122, 38, 3, 2, 1, 4, NULL, NULL),
(123, 9, 3, 2, 1, 3, NULL, NULL),
(124, 1, 3, 2, 1, 6, NULL, NULL),
(125, 39, 3, 2, 1, 123, NULL, NULL),
(126, 15, 3, 2, 1, 3, NULL, NULL),
(127, 2, 3, 2, 1, 7, NULL, NULL),
(128, 14, 4, 2, 1, 122, NULL, NULL),
(129, 3, 4, 2, 1, 122, NULL, NULL),
(130, 4, 4, 2, 1, 7, NULL, NULL),
(131, 5, 4, 2, 1, 8, NULL, NULL),
(132, 16, 4, 2, 1, 124, NULL, NULL),
(133, 38, 4, 2, 1, 4, NULL, NULL),
(134, 9, 4, 2, 1, 3, NULL, NULL),
(135, 1, 4, 2, 1, 6, NULL, NULL),
(136, 39, 4, 2, 1, 123, NULL, NULL),
(137, 15, 4, 2, 1, 3, NULL, NULL),
(138, 2, 4, 2, 1, 7, NULL, NULL),
(139, 8, 4, 2, 1, 12, NULL, NULL),
(140, 6, 4, 2, 1, 4, NULL, NULL),
(141, 13, 4, 2, 1, 124, NULL, NULL),
(142, 23, 5, 2, 1, 122, NULL, NULL),
(143, 22, 5, 2, 1, 122, NULL, NULL),
(144, 16, 5, 2, 1, 124, NULL, NULL),
(145, 38, 5, 2, 1, 6, NULL, NULL),
(147, 32, 5, 2, 1, 10, NULL, NULL),
(148, 1, 5, 2, 1, 6, NULL, NULL),
(150, 34, 5, 2, 1, 9, NULL, NULL),
(151, 33, 5, 2, 1, 4, NULL, NULL),
(152, 19, 5, 2, 1, 6, NULL, NULL),
(153, 2, 5, 2, 1, 7, NULL, NULL),
(154, 21, 5, 2, 1, 7, NULL, NULL),
(155, 13, 5, 2, 1, 124, NULL, NULL),
(156, 26, 2, 2, 1, 125, NULL, NULL),
(157, 26, 1, 2, 1, 125, NULL, NULL),
(158, 26, 4, 2, 1, 125, NULL, NULL),
(159, 26, 3, 2, 1, 125, NULL, NULL),
(160, 8, 3, 2, 1, 12, NULL, NULL),
(161, 6, 3, 2, 1, 4, NULL, NULL),
(162, 13, 3, 2, 1, 124, NULL, NULL),
(164, 47, 1, 2, 1, NULL, NULL, NULL),
(166, 47, 2, 2, 1, NULL, NULL, NULL),
(168, 47, 3, 2, 1, NULL, NULL, NULL),
(170, 47, 4, 2, 1, NULL, NULL, NULL),
(171, 7, 4, 2, 1, NULL, NULL, NULL),
(172, 47, 5, 2, 1, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sub_menu_items`
--

CREATE TABLE `sub_menu_items` (
  `sub_menu_item_id` int(10) UNSIGNED NOT NULL,
  `sub_menu_item` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `sub_menu_item_url` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `sub_menu_item_icon` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `active` int(10) UNSIGNED NOT NULL DEFAULT '1',
  `sequence` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` int(10) UNSIGNED NOT NULL DEFAULT '1',
  `menu_item_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
(19, 'ACADEMIC TERM', '/academic-terms', 'fa fa-paperclip', 1, '2', 1, 16, '2016-05-13 17:11:39', '2016-07-15 18:50:48'),
(20, 'CLASS GROUP', '/class-groups', 'fa fa-plus', 1, '1', 1, 17, '2016-05-13 17:13:48', '2016-05-13 17:13:48'),
(21, 'CLASS LEVEL', '/class-levels', 'fa fa-plus', 1, '2', 1, 17, '2016-05-13 17:13:48', '2016-05-13 17:13:48'),
(22, 'CLASS ROOMS', '/class-rooms', 'fa fa-plus', 1, '3', 1, 17, '2016-05-13 17:13:48', '2016-05-13 17:13:48'),
(23, 'ASSIGN TO CLASS', '/subject-classrooms', 'fa fa-list', 1, '3', 1, 15, '2016-05-26 08:02:55', '2016-05-26 09:45:02'),
(24, 'SETUP', '/assessment-setups', 'fa fa-ticket', 1, '1', 1, 23, '2016-05-26 08:08:47', '2016-05-26 08:12:15'),
(25, 'SETUP DETAILS', '/assessment-setups/details', 'fa fa-list-alt', 1, '2', 1, 23, '2016-05-26 08:08:47', '2016-05-26 08:12:15');

-- --------------------------------------------------------

--
-- Table structure for table `sub_most_menu_items`
--

CREATE TABLE `sub_most_menu_items` (
  `sub_most_menu_item_id` int(10) UNSIGNED NOT NULL,
  `sub_most_menu_item` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `sub_most_menu_item_url` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `sub_most_menu_item_icon` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `active` int(10) UNSIGNED NOT NULL DEFAULT '1',
  `sequence` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` int(10) UNSIGNED NOT NULL DEFAULT '1',
  `sub_menu_item_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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

CREATE TABLE `users` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `password` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `phone_no` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `first_name` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `middle_name` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gender` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `phone_no2` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_type_id` int(10) UNSIGNED NOT NULL,
  `lga_id` int(10) UNSIGNED DEFAULT NULL,
  `salutation_id` int(10) UNSIGNED DEFAULT NULL,
  `verified` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `status` int(10) UNSIGNED NOT NULL DEFAULT '1',
  `avatar` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `verification_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `password`, `phone_no`, `email`, `first_name`, `last_name`, `middle_name`, `gender`, `dob`, `phone_no2`, `user_type_id`, `lga_id`, `salutation_id`, `verified`, `status`, `avatar`, `verification_code`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, '$2y$10$r7i.xoOrQP6n0B5JQLtmCuaY.MvCuNoEsinb6ALaNjov4Ck2Nfnx.', '081617307881', 'admin@gmail.com', 'Emma', 'Okafor', '', 'Male', '2016-04-05', '', 1, 0, 1, 1, 1, '1_avatar.jpg', NULL, 'h198OPEXf49Lc8ZPVJ5xnLmvU4l7sYbleb5TewvCwuKqnxwsvDeRwKiPhgvN', NULL, '2016-08-19 15:14:21'),
(2, '$2y$10$r7i.xoOrQP6n0B5JQLtmCuaY.MvCuNoEsinb6ALaNjov4Ck2Nfnx.', '08022020075', 'bamidelemike2003@yahoo.com', 'Bamidele', 'Micheal', '', 'Male', '1976-02-11', '08066303843', 2, 476, 1, 1, 1, '2_avatar.jpg', 'x9pxH08aB60ZKwe12DDKbiD3V5628TyGMd1v8Q5I', 'deA5BxN3cHFopMekF7A8LYatqlgV70UAHEQgZlKX8W4zY2rlv0BIo0nPL4tn', '2016-04-28 21:21:05', '2016-08-29 10:40:03'),
(3, '$2y$10$Pc9CBBOKkpbTAlnoxc0iveGkS5xRKREBYlwyPRzxSUxsb.9nNJ9cS', '08186644996', 'onegirl2004@yahoo.com', 'Emina', 'Omotolani', NULL, NULL, NULL, NULL, 4, NULL, NULL, 1, 1, '3_avatar.png', 'sJkNJULOX0XDBVHoG929c8zOuHvuQJ8taqOE4MK7', 'k8UmNk4kFLcPI7leXsYUVe0F7p33SMV6bcATiKe79UMg2doY9BmIC0s6G5ws', '2016-05-05 18:29:34', '2016-07-18 12:33:34'),
(4, '$2y$10$r7i.xoOrQP6n0B5JQLtmCuaY.MvCuNoEsinb6ALaNjov4Ck2Nfnx.', '08032492560', 'agiebabe2003@yahoo.comk', 'Agetu', 'Agnes', '', 'Female', '2016-07-13', '', 4, 0, 2, 1, 1, NULL, 'mBFoXfYp7feFMhsnzYWkh616IV2wq2e5LdtOOYRl', 'tKO4xIGZdOIzqE6b9VO63Kwbv4cSPSWV1bRfsBiirtuIt9b0FbPcYxAeGrwF', '2016-05-05 18:30:48', '2016-08-20 09:53:54'),
(5, '$2y$10$08ymddnGq3lEWheZSMe3Puc/fLtGo7pDZ5dm1Pmh9CupX3AV/KvO6', '08138281504', 'thesuccessor2020@yahoo.com', 'Akinremi', 'omobolaji', NULL, NULL, NULL, NULL, 4, NULL, NULL, 1, 1, NULL, 'kmmCEClYr018UobxCFrOHmDVVOaz1eaD60Nn2ow9', 'tnzLRSOXjdLubYWZnnJO9QSBzQc5QlFX07PncbLhlISjMc2K05t6TKvaDrDD', '2016-05-05 18:32:16', '2016-07-18 12:33:58'),
(6, '$2y$10$r7i.xoOrQP6n0B5JQLtmCuaY.MvCuNoEsinb6ALaNjov4Ck2Nfnx.', '08032984249', 'chukwuonyelilian@gmail.com', 'Chukwuka', 'Lilian', NULL, NULL, NULL, NULL, 4, NULL, NULL, 1, 1, NULL, 'rPp9ofMqUMCat73BPt0Pod2v2Rg362iUtO5QNzU0', 'aNqhAVZzfoHeoFv434K93jxrrPll8Fuhbm4vTqgD7knlwA4q7dGYHFrUVgka', '2016-05-05 18:34:16', '2016-08-20 09:54:07'),
(7, '$2y$10$MDDp2iaWLGqwbDvYBpsB/.YB12d45YNbknT6yEMWWPi5JRBPW9AiG', '08066451585', 'soldemo20042001@yahoo.com', 'Ademola', 'Solomon', NULL, NULL, NULL, NULL, 4, NULL, NULL, 1, 1, NULL, 'MZXJDhTOSnwR0ZXphZLBScldsf880b3vzyoHTrfK', 'd53mWOShoo72U5L0dtPGwRLTvdd2xfNblVng1BF4H4S8AsbH4ww0FMOlzZFE', '2016-05-05 18:36:04', '2016-07-18 12:34:32'),
(8, '$2y$10$.8x6F9cBdIHCbgy1WwT.nOZk7w5LDa75jATtatuORkejMemmr35yG', '07062175334', 'peroski4chuks@yahoo.com', 'Peter', 'Okuagu', NULL, NULL, NULL, NULL, 4, NULL, NULL, 1, 1, NULL, 'b8M3Gs5lGycCX3cYnH44gkUxmRxoVBCWLOQGDNK8', NULL, '2016-05-05 18:37:13', '2016-05-05 18:37:13'),
(9, '$2y$10$FC1BwB23i/GGqkhC6h1TIuN0.OjLUYkcu7MdT3xak4DrqXbZ/U6R2', '08090948734', 'okelolaademola@yahoo.com', 'Okelola', 'Ademola', NULL, NULL, NULL, NULL, 4, NULL, NULL, 1, 1, NULL, 'EbvbXeWe5EKkK4b7zKn6EWGZmbsNvGbAlRQIGI1S', '0UN1Y1G1EDfKsIchZEogD3aT6mRLFiCeVPgwJv13xfPnTdAA9gGBpBsze0yX', '2016-05-05 18:38:23', '2016-06-03 17:41:36'),
(10, '$2y$10$q5U4WE68xHhbqVK3gwkYjezapu3g/aB1nYTqhyRPHRFjXy9OpVpaS', '08035255510', 'allanzah5525@yahoo.com', 'Allanzah', 'Oluwabunmi', NULL, NULL, NULL, NULL, 4, NULL, NULL, 1, 1, NULL, 'rjcdhR2WJW27XocmNwoORcfERnQV1qoh3mTT3dK5', NULL, '2016-05-05 18:43:28', '2016-05-05 18:43:28'),
(11, '$2y$10$6xA35RW0HJcsAr9Bs9LfSO3wA8opLWEyZ1ztgypeC1ET1r73tuC7.', '08028167155', 'oluwatosinaroomotosho@yahoo.com', 'Omotosho', 'oluwatosin', NULL, NULL, NULL, NULL, 4, NULL, NULL, 1, 1, NULL, 'BPNbWERr3zwJMKVetEjUDc6akSLVQrnhPhRvwAg2', NULL, '2016-05-05 18:45:11', '2016-05-05 18:45:11'),
(12, '$2y$10$5dGmkChzVeFNuFie2vPDWO9X0k5PtafYoFVydeUtLR8NDwYMjFErW', '08121680773', 'kateokolie@yahoo.com', 'Okolie', 'Kate', NULL, NULL, NULL, NULL, 4, NULL, NULL, 1, 1, NULL, 'Di4CWeDOgqXqB4XbJdiDS16NjzrYVbBo9NJMLT5y', NULL, '2016-05-05 18:46:24', '2016-05-05 18:46:24'),
(13, '$2y$10$.x6aocbaec7cJgHxJlFTL.V7sSoWirznKVc61rgp/D68jon93oDvK', '08060633784', 'divinebazunu@yahoo.com', 'Oshoma', 'Angela', NULL, NULL, NULL, NULL, 4, NULL, NULL, 1, 1, NULL, 'sTFKNF2E2T3Cz5AM10B0G8Pz3s4jt5CX7gcJfzdW', NULL, '2016-05-05 18:47:38', '2016-05-05 18:47:38'),
(14, '$2y$10$3MShBlke130ZlDOi8cA/Yu2YixDiYVvPL9DqrJP/FJjed6r6PojGe', '08025307294', 'relatingus@yahoo.com', 'Adekoya', 'Biola', NULL, NULL, NULL, NULL, 4, NULL, NULL, 1, 1, NULL, 'xTJDhUgrzH5ywWfzHkbwKFs3ZgLrueRA3cXxzK2S', NULL, '2016-05-05 18:48:33', '2016-06-06 00:20:13'),
(15, '$2y$10$bQ9WAh/O8Q/21TUuXsaWbeqvOx3gJiN99QnahcWzfUg4VMURcsB6G', '07066847811', 'demolastar@gmail.com', 'Balogun', 'Ademola', NULL, NULL, NULL, NULL, 4, NULL, NULL, 1, 1, NULL, 'CAwD6crBPhv8WdtOrvrxHe5efULPZiObNcmkwIuM', NULL, '2016-05-05 18:49:33', '2016-05-05 18:49:33'),
(16, '$2y$10$KY9ccp6tm/85Iibc3lpAXeOVUtOiVGjmVqITVDJPHtYBXdzUKYE2q', '08020705912', 'olabisi_motunrayo@yahoo.com', 'Sobukanla', 'Olabisi', NULL, NULL, NULL, NULL, 4, NULL, NULL, 1, 1, '16_avatar.png', 'xq55STVDEya0pNSUnsYAyj5x68iPuC1ieBFmRYSc', NULL, '2016-05-05 18:50:41', '2016-05-10 02:36:31'),
(17, '$2y$10$.z5L2MNbvDG2of0jTpU0KONZXSj6Zrbq525c4p6yPNknjBxODppUq', '07037746048', 'solidstepsch@yahoo.com', 'Okpor', 'Julianah', NULL, NULL, NULL, NULL, 4, NULL, NULL, 1, 1, NULL, '5a9v460AasF26HHSh59ewkK3JZ6M0nmKlYvd0xzO', 'Zblv6AgLZd7pPToYfoxFcB1NgSbEA2TRMP8untXOtUKwkOalipCzDuUQUIwW', '2016-05-05 18:52:07', '2016-05-17 18:24:02'),
(18, '$2y$10$JUNQaU7D.PNaYtQLFfACHePT6F6y1mNHRKTpwKQgHqGSDQGkJ0tW.', '08035423767', 'abayomi@yahoo.com', 'Abayomi', 'Abayomi', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'h9vO4igUqSOLJl1MgI9Zl8hVIgMQSkxtuPjCVxB5', NULL, '2016-05-10 18:04:01', '2016-05-10 18:04:01'),
(19, '$2y$10$bJjJRYzohk2vF5DUZZfOjeSOvITzu654hN2DYMoPu/GNLLtp7c0M.', '08034811290', 'adebayo@yahoo.com', 'adebayo', 'adebayo', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'tDvbmB4nNhENIbvbT5mwj9v4MOsY4NEl0PKW2WUC', NULL, '2016-05-10 18:05:04', '2016-05-10 18:05:04'),
(20, '$2y$10$ka1poX6/MRQMppAMmsthhOHJcqB9kXcw1u.WnZoXIKMyj7PPSM5pS', '07087886188', 'adebiyi@yahoo.com', 'adebiyi', 'adebiyi', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'JHZJEPT3cNjjp1dlWlX8XJWoJAUJNvKg6fg25wSt', NULL, '2016-05-10 18:06:39', '2016-05-10 18:06:39'),
(21, '$2y$10$VkOuEo.sWHGSuqKCU8iff.NJ2G2Bq3TuYUxXUY7sdSR984A9pSIhu', '08036000828', 'adelola@yahoo.com', 'adelola', 'adelola', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'KXnd6uuSsfXRLmRHeq9i3TkgVZYihrXScCXsxaMB', NULL, '2016-05-10 18:08:26', '2016-05-10 18:08:26'),
(22, '$2y$10$QNj7V0BJg34D2CdIsd8A3.IgsQiB.NP31ICjv80C6.DsBFoYzPqB6', '08112000692', 'adesida@yahoo.com', 'adesida', 'adesida', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'LsSAlLsp9awyMemCbabGP255Ag1OAFelXPKYk4ZA', NULL, '2016-05-10 18:09:15', '2016-05-10 18:09:15'),
(23, '$2y$10$o.I3jxl6b0fLlOq6j7i.ruFe8YWuvhbHU0k.SUG6K84he.BdF0W6a', '08023019212', 'adewumi@yahoo.com', 'adewumi', 'adewumi', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'LMEFt232rTF9xZ73Krv6BBwXVMPpXCh3rb2TMTxw', NULL, '2016-05-10 18:10:17', '2016-05-10 18:10:17'),
(24, '$2y$10$tCo6l397eJvSXNX2mHRw6.513v7dnIOFasAk2DwBXDlhaNxNTxGLK', '08067151353', 'adeyemi@yahoo.com', 'adeyemi', 'adeyemi', '', 'Male', '1989-07-11', '', 3, 604, 1, 1, 1, '24_avatar.jpg', 'IWRH6Ij4Mh7qzaA0HQ208NcLTbJehSFUvgUxp0b7', '3Rw26x2F4ZK9x7bWTTGpaoSexDwJpxeoHiiJ3Y2YeK2Pn3vex3vWv2F404p3', '2016-05-10 18:11:43', '2016-08-29 17:42:45'),
(25, '$2y$10$uag5F5RE9WauCOzbEr44g.dfTkxV.JBJwF2gWV1wqAw.qj5gavzFG', '08034730900', 'adeyemi1@yahoo.com', 'adeyemi', 'adeyemi', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'qpZEX3b04G4EhTAixG5U21SOpdat5FbwQiKsvsYI', NULL, '2016-05-10 18:14:01', '2016-05-10 18:14:01'),
(26, '$2y$10$zCmlJws2.7GNyeLTjLVKherZd2faZl9W8FoJcxwQP/TVai4pdlvna', '08038666239', 'adeyemi2@yahoo.com', 'adeyemi', 'adeyemi', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'uZEZIJvQioqHfboZwjYzozKNk3nSBdiPrNZtBwNj', NULL, '2016-05-10 18:15:18', '2016-05-10 18:15:18'),
(27, '$2y$10$2hFNokJGmVd82yeHfmcIY.AjgcYMWNhTUDfiv6x72hHx27Zx/QmdS', '08034133636', 'adoye@yahoo.com', 'adoye', 'adoye', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'cHaVWYEZAAGISfPs9s2qenYJPhnZIDrfzeQfTmVr', NULL, '2016-05-10 18:16:24', '2016-05-10 18:16:24'),
(28, '$2y$10$th.a78j6wsAgZLQxt1jel.UzBudpc.sTxsDssREp5HLUvMq7HbAWi', '07039691870', 'afolabi@yahoo.com', 'afolabi', 'afolabi', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'mYFmbJLKnlr2epos1u3p5LWq92SF6NKub14y5VPp', NULL, '2016-05-10 18:24:01', '2016-05-10 18:24:01'),
(29, '$2y$10$IPusZZItK7vJLLR9fDwMJu5HGEkWyY/P3hcK2jjOZukw1XrudtgEu', '08028615460', 'aje@yahoo.com', 'aje', 'aje', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'mbSCBmd7uq5QlDyXday6XWmyDeHqf5Ua19gYSGhy', NULL, '2016-05-10 18:26:30', '2016-05-10 18:26:30'),
(30, '$2y$10$waV0OGh/L4pa2B31vOh9ZuinCRDuhbONTLbvvcySB4UBoVkapJvG.', '08055802718', 'akinbunu@yahoo.com', 'akinbinu', 'akinbunu', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'HMChYqs4vyx6gDvY93Lpu1v7qqG0gZId44tvcEcN', NULL, '2016-05-10 18:30:19', '2016-05-10 18:30:19'),
(31, '$2y$10$pNq5CjlYXD1ZJco3cszVGuSyGUwYD6.4zBfeMwS49BEcTfDJME8I6', '08035131207', 'akinyosoye@yahoo.com', 'akinyosoye', 'akinyosoye', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'Qbw5ZuxWcnLHfuemMx9nJs1HqRVDfUn9LQhkBgZ8', NULL, '2016-05-10 18:32:11', '2016-05-10 18:32:11'),
(32, '$2y$10$6LsehSGgyGr3q1YqLdjAgu3POHj7qU0R7AiGtH2Cs7ufN5trc4o3W', '08058044900', 'alabi@yahoo.com', 'alabi', 'alabi', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'GUQ03ULqXdikZudKvHlqY2U4bD4eO5ztSkmB6iTf', NULL, '2016-05-10 18:37:53', '2016-05-10 18:37:53'),
(33, '$2y$10$nrIpQLpZ8bOMijlp2sjWO.BOMloOqHZ0A4X8hQXAROCAHVYq1QzXu', '08034750375', 'anaele@yahoo.com', 'anaele', 'anaele', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'fb84h2u0bJNqiG9oHed5rAmcU6U09VArk6R9bYs1', NULL, '2016-05-10 18:39:12', '2016-05-10 18:39:12'),
(34, '$2y$10$/mlX27x3JB/UDLFNj4y.aO36/LEARXpT1GqxAYn2BSH2fLZyKpKcy', '08023029379', 'animashaun@yahoo.com', 'animashaun', 'animashaun', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'Z4csXQZI8Zbd4VNCJHYrm4FjJXqkUFF9LDpsJH0y', NULL, '2016-05-10 18:40:27', '2016-05-10 18:40:27'),
(35, '$2y$10$rQq2j2zB3pJkU8PRDu5r3O9yrI5syRgOWNFEc4IJX92jAxEb0NKJi', '08027563905', 'anomkfueme@yahoo.com', 'anomfueme', 'anomfueme', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'k6QjpShgv8C2h3VSWzOjpnhI2Veuw9l6hAKSpYtz', NULL, '2016-05-10 18:44:56', '2016-05-10 18:44:56'),
(36, '$2y$10$8BpkSKRmSDmQ6nCmjvDwEOs29JLBUW69pChelaO1D0PloMqWo12dy', '08052900879', 'ayanleke@yahoo.com', 'ayanleke', 'ayanleke', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, '3b9DrSNrPtOqJQi9sE1syUXa90Dj6ZhTi2KsS07h', NULL, '2016-05-10 18:45:53', '2016-05-10 18:45:53'),
(37, '$2y$10$jTWopfaPWBZNWScQ/gmpFuHTy46WICNXnRSUhIKnfddX9Xi34feuW', '08025224666', 'ayeni@yahoo.com', 'ayeni', 'ayeni', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'xv5NI55Ac1lUgCwfjg98hwITrYCKZCjVBp3KKrNU', NULL, '2016-05-10 18:47:18', '2016-05-10 18:47:18'),
(38, '$2y$10$yvfrq7C3S8EaIrrvCVcXm.Q/VvjmpsjUKtr5qYLuuCEsxbGnj7VGK', '08096473502', 'badmus@yahoo.com', 'badmus', 'badmus', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'lof3V3KVjZZMHYnJIgM0YTNXW1oO4O5mkowegS96', NULL, '2016-05-10 18:49:14', '2016-05-10 18:49:14'),
(39, '$2y$10$ql3eCFR2s85XmkmQCc5b0.bbKCuSaGDWQrgn8fE/2h.xIrFEyW8qu', '08039268308', 'bakare@yahoo.com', 'bakare', 'bakare', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, '49PBJZUvyduz6vanCscWObn8L1jfq0vEl4pEKqej', NULL, '2016-05-10 18:50:00', '2016-05-10 18:50:00'),
(40, '$2y$10$kcGSAt7BrFHaxqMhRVBSueHhXTc.JVMyZE6K6Vu5zN4hS68MdN0b6', '08132188828', 'bashorun@yahoo.com', 'bashorun', 'bashorun', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, '3WFgSvYPvV163r7YOBtron6ytFbKaOD0GF4bH01Y', NULL, '2016-05-10 18:50:57', '2016-05-10 18:50:57'),
(41, '$2y$10$PhweNSMhK90zrSJ7MP27sucJAKA6sTpu465lt1RrCCLnZvdNC.RvC', '08034813126', 'bello@yahoo.com', 'bello', 'bello', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'A2DBJVt31zPmVY4UwY2HLzViDa1nrtndzHy2LTAO', NULL, '2016-05-10 18:51:47', '2016-05-10 18:51:47'),
(42, '$2y$10$QQF0WWbNYAgN1XIlS1rC/OFT8MdY/4tHvO367G1t7aOj0kewNohiW', '07083940215', 'biobaku@yahoo.com', 'biobaku', 'biobaku', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'rW7TOBT67LeMH7SCWHNdVd5KpvXxjERQiJd5BPw0', NULL, '2016-05-10 18:52:40', '2016-05-10 18:52:40'),
(43, '$2y$10$ckC.qCqdZJzZgfL.ByGpVueDrHv.5N6wTVPQFiA8J9x4AAYXlQaK6', '08172677798', 'brain@yahoo.com', 'brain', 'brain', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'qUb4UwWcrYbHRusZG79HIYXqsW83Yq1lUJxKJhiM', NULL, '2016-05-10 18:53:49', '2016-05-10 18:53:49'),
(44, '$2y$10$qp2XtyrQBQsTDSi9YoXtKOdIzZpVN0eWBwyhy.vYF8jL1a23jq4qW', '07063617473', 'bruce@yahoo.com', 'bruce', 'bruce', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'V8QuL3PG1GJaTIPuaZoG8lLXg3M5coX9hIIyouKI', NULL, '2016-05-10 18:55:16', '2016-05-10 18:55:16'),
(45, '$2y$10$DP1dEwtOnF.XMQb9GMBOougSh9v6o6lrmZaNLvjWC35q9myLm8t4i', '08034296363', 'chidiebere@yahoo.com', 'chidiebere', 'chidiebere', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'zjnGBFkgdsCsqVLWQqw63PErBhqnHcfBHlwJDnyJ', NULL, '2016-05-10 19:25:39', '2016-05-10 19:25:39'),
(46, '$2y$10$5ukDHYe12ODSN3HQgQop3.WncXbm3Nwt3mhKka2sG3wHR0Wi1UM2G', '07083207650', 'chima@yahoo.com', 'chima', 'chima', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'e4Y28mn2re0Yt5eeNjBAldR0Y5oZ8cvbDRPiQfU2', NULL, '2016-05-10 19:26:32', '2016-05-10 19:26:32'),
(47, '$2y$10$xB98h9pIi.h1mlbNHjPQSeideB/gKc46jbkx7/KLTDcE5iZxfLh5S', '08023285514', 'durojaiye@yahoo.com', 'durojaiye', 'durojaiye', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'jgDK9RyS0cIRCwCP7lzZCa288qtzqpQeBqiHPwUD', NULL, '2016-05-10 19:34:17', '2016-05-10 19:34:17'),
(48, '$2y$10$Wgouy2/T9v5IxU5uyyPL8OZTIVvn6hmlytEikeS6drQ/OeHP826/6', '08126274482', 'ebukanson@yahoo.com', 'ebukanson', 'ebukanson', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'qUqTcEpMpsAAQYk7SoIUJTCtv1WgaXE8qIVbLhqd', NULL, '2016-05-10 19:35:13', '2016-05-10 19:35:13'),
(49, '$2y$10$ZhQkomsaVc2A2VLwYFRp5Oe48XlOzqVnMeGm7WUybRu0pzSlkWXMm', '08033809777', 'echika@yahoo.com', 'echika', 'echika', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'khBBYujZ5pPiuQJrFprmkYb4sO0x5PfXGeuIkORZ', NULL, '2016-05-10 19:35:55', '2016-05-10 19:35:55'),
(50, '$2y$10$W4K8cP4l.l8HOX3yhM6EZOw4satUWhka3R57vYSUyAB35aC1sFWOW', '08033142304', 'edeh@yahoo.com', 'edeh', 'edeh', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, '4hFcwgwhtC8vwvFPSjLRWQDQ9GQ9kC4WAST6MYwy', NULL, '2016-05-10 19:36:38', '2016-05-10 19:36:38'),
(51, '$2y$10$pF6QOxqTB7J/w9AyppMVFOSNUVV.0Q.OY4quNkxuNkIKPRClxt8oe', '08073144124', 'ediae@yahoo.com', 'ediae', 'ediae', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'Gae0BO0nzd5f2Ehj3KnvGOBCR0gLXgjjlsZDfKyq', NULL, '2016-05-10 19:37:37', '2016-05-10 19:37:37'),
(52, '$2y$10$qyBxG.7NX32BlG7U8h0y7OIAlzhkZcPZIbHiR2vxhlFHhcVdmdy32', '08133380216', 'edward@yahoo.com', 'edward', 'edward', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, '7t4whVIXO7dZdJyQKTTnTu2FqDHT2qkJn7DUFH2B', NULL, '2016-05-10 19:38:25', '2016-05-10 19:38:25'),
(53, '$2y$10$d7ljeq19C.2CVOfBCCr3nuAECKB1Txk8yit5NcK.ez3TdYdP7dpMm', '08056078540', 'efurhievwe@yahoo.com', 'efurhievwe', 'efurhievwe', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'm5ofdmU3icPkqyUxoFlbTjMfgxS0uL31yuAKO0At', NULL, '2016-05-10 19:40:00', '2016-05-10 19:40:00'),
(54, '$2y$10$D5sAmPuRd.INbeYbFHbSQO.8Oazamo0g.sQBn8FrMRswz8kPg02PO', '08057356449', 'ejeh@yahoo.com', 'ejeh', 'ejeh', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'LsSLsgSo7ROLXfKq4v5FwzIWGOfPGUpbK92sBCqC', NULL, '2016-05-10 19:42:24', '2016-05-10 19:42:24'),
(55, '$2y$10$RCtsJRjW/C5gPsXb2wL5tebyPUYqhW5RDeGHrjH0bE1D5UFzVi9fG', '08094410200', 'ekaIdara@yahoo.com', 'eka - Idara', 'eka - Idara', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, '6dBZEOoPcodWmKwz0K1tgon7s1OLFpl0WkzkEixV', NULL, '2016-05-10 19:43:34', '2016-05-10 19:43:34'),
(56, '$2y$10$0oe.sOh1fHivHMZpb8VMQeQXeaFGOYRAuMdHfOEbKCHIfxtXbaeRi', '08033717878', 'ekoh@yahoo.com', 'ekoh', 'ekoh', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'S1UzLule8v5OWyS0Hjz2PRqdVndrlsKLMMzyrrJH', NULL, '2016-05-10 19:47:13', '2016-05-10 19:47:13'),
(57, '$2y$10$9oVxmSPf.gh3nwJuZWVASu2ZyQ39IVu3JsjUOEZK/t5AE/19qEYZG', '080223425178', 'emissiri@yahoo.com', 'emissiri', 'emissiri', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'SWcFgLU4tpJw3DMHkQTzLwPCVE65KKhywFtwAPky', NULL, '2016-05-10 19:48:05', '2016-05-10 19:48:05'),
(58, '$2y$10$m.pjNtzG9d4JBK5AfO0v8eSdGn2BaMOceU2Jg8iiZ98Nu0Qk8aciW', '08035832784', 'emina@yahoo.com', 'emina', 'emina', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'Z5o2zl1k2ZACMVQq4NG16r4Wsw3Urk0pBszz7jJj', NULL, '2016-05-10 19:51:01', '2016-05-10 19:51:01'),
(59, '$2y$10$SsmzDvx5P12MqR5LmGWJfOhdP17y31tEdQpBOqq3RsJc/75D/40Fa', '07086722363', 'enifeni@yahoo.com', 'enifeni', 'enifeni', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, '3d0dqPKzdDtvwHqSNnIiurgTzZamiFEo7T4SiZy0', NULL, '2016-05-10 19:51:47', '2016-05-10 19:51:47'),
(60, '$2y$10$2ZPjTUNby7Cw8WRaFrm1be42wF4vIAq4g/FuIU7zfH3KCXn2M/voe', '08033607182', 'enugo@yahoo.com', 'enugo', 'enugo', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'Tfhh6UbZHRf00b27eTkbfsM3YXmIRJ8mY0ksRktk', NULL, '2016-05-10 19:52:57', '2016-05-10 19:52:57'),
(61, '$2y$10$Iwa0cRYYB5bqRP6k6rnAMeCYIkApwx3zuoty.LvXY3/Eckk7rNG2e', '08037746392', 'eyoita@yahoo.com', 'eyo - ita', 'eyo - ita', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'FIbNeX9T1LxSHl4jSyMc1JHdOZMR1RYAY8BpdJ1e', NULL, '2016-05-10 19:53:56', '2016-05-10 19:53:56'),
(62, '$2y$10$hFPbPodAc16lP0nQfB6jTen17BCeLO4eUVjjPjdsXkmO4zzEemtOq', '08023656161', 'ezechinyere@yahoo.com', 'ezechinyere', 'ezechinyere', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'ZSLXnQrFSLtarQQN75tjnhLGKbLyWajwsaOX0vnJ', NULL, '2016-05-10 19:55:21', '2016-05-10 19:55:21'),
(63, '$2y$10$a9V5hvnwJtVngL13DPD01ubIyoUKWspbz7MaPMTnZ.9MhoXOo0v3a', '08052519669', 'ezelie@yahoo.com', 'ezelie', 'ezelie', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, '5zjXL1XBHQ2uw508NDKeoqQA6d58WCpgxxhUbsuj', NULL, '2016-05-10 19:56:18', '2016-05-10 19:56:18'),
(64, '$2y$10$IKWLQ9u7qebUFa8fqJkeIeUmfBFVG3cN4DF.VabLkh3kikGPnyAj6', '08146228602', 'fawale@yahoo.com', 'fawale', 'fawale', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'dpfOQe1GGOfmAKmk4xsjb12lFUr3n1Oe4xDiTA7x', NULL, '2016-05-10 19:57:41', '2016-05-10 19:57:41'),
(65, '$2y$10$JoYRvIbWX1zSXYzMhV2Mm.9zOidTFQP6dvu.Pk8KGU2zA4EdjduHK', '08029184841', 'fayemiwo@yahoo.com', 'fayemiwo', 'fayemiwo', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'SkfLbeToScPk5eRTkAw6A34R1akkEYI9FSj967bN', NULL, '2016-05-10 19:58:34', '2016-05-10 19:58:34'),
(66, '$2y$10$jHDLhhUQ32NTxJm/YSstUO7gz220leRxMc2ING50o1kVbhT/22DgS', '08033005954', 'gbadamosi@yahoo.com', 'gbadamosi', 'gbadamosi', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'vqY9UjzBNVN970mD0giGzkSB2AYtZxASuDqXrqIt', NULL, '2016-05-10 19:59:40', '2016-05-10 19:59:40'),
(67, '$2y$10$QysusTE6lbxBcuZ7/an69uiDCHJi4TRiFhLCgfQKKd50NK0PAP6x2', '08066669355', 'george@yahoo.com', 'george', 'george', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'JSB4UbHynn9HLUTZtouglImPZwtv1FWXqAQJhhiF', NULL, '2016-05-10 20:00:38', '2016-05-10 20:00:38'),
(68, '$2y$10$EbRPRr6k7agvz/LwABDlHext4zRnEJUNWIR.P5nXdOpFSuBgzq4/2', '08036147829', 'george2@yahoo.com', 'george', 'george', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, '77e9haqMYHv2lu05V5lBwc2Yz572Y88KbAymYAsW', NULL, '2016-05-10 20:02:05', '2016-05-10 20:02:05'),
(69, '$2y$10$vZnah5noa7rINQCsRMWaAOlvEgQCKSODLMCouW.mOfeeFqLkgOwHC', '08187212908', 'hundeyin@yahoo.com', 'hundeyin', 'hundeyin', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'RERZgbNsXhlyM7WNvQ367pQpQaU1ehyZlpJJABau', NULL, '2016-05-10 20:03:46', '2016-05-10 20:03:46'),
(70, '$2y$10$.dqY0bkizxlnpbBYBbrc6OvDyMdkLhwljYXlrBxzBL4jAr/6UelpG', '08138241771', 'ibiam@yahoo.com', 'ibiam', 'ibiam', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'POLFCnzHNG4qxEvgLExY0svr3fDmVaJnj7iU9SRN', NULL, '2016-05-10 20:05:09', '2016-05-10 20:05:09'),
(71, '$2y$10$esH08gF0915EQWf7KzRGXuUZi5bYs.TBJKc7IauTCG.V.MbGAUvBC', '08034720645', 'idahosa@yahoo.com', 'idahosa', 'idahosa', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'VmVnWXMWOtK0iTwOsdRrYLT8rxhxei3YQTQnapRz', NULL, '2016-05-10 20:06:07', '2016-05-10 20:06:07'),
(72, '$2y$10$xY0ftJPdgpm3oOwPOBAv.e2jeGEK.3d.snETxPmGQvBErZB.hgC6C', '08036739601', 'ifedayoadesida@yahoo.com', 'ifedayo', 'adesida', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'vvmJzzqs3GytTq4QUTzGeBF75Cei8LtUFvnv11Wg', NULL, '2016-05-10 20:07:40', '2016-05-10 20:07:40'),
(73, '$2y$10$T85f487R2du0rm3CzXYg6udLAmt9eiDLaZYgIWpNYjAwph.Zsf5ja', '08028317642', 'igure@yahoo.com', 'igure', 'igure', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'fSj7BCxE5NRoP9eMHIkBBNgUuiz9WIIexXoi3C0p', NULL, '2016-05-10 20:10:26', '2016-05-10 20:10:26'),
(74, '$2y$10$9pb/leHA3ehRg1pueFpmAewxB/1ho9Zif8gKvJFAGiVM4undoPF/.', '08055679974', 'igweonwu@yahoo.com', 'igweonwu', 'igweonwu', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'vXFSjGTydHAyTFMMYwcwRP1gqym8flzXsWc8i3Gk', NULL, '2016-05-10 20:11:21', '2016-05-10 20:11:21'),
(75, '$2y$10$F75owQiktNMMXrXdp2xRl.KbzbPnRCyogsxgMWvD0aT54/6BBmydm', '07066162027', 'iheoma@yahoo.com', 'iheoma', 'iheoma', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'lgLdeNmp6RfOPDyj2UjcNo1Hnls77xASMzp3jjW3', NULL, '2016-05-10 20:13:53', '2016-05-10 20:13:53'),
(76, '$2y$10$Lsym346pUquXjlUaSdVwA.qR63fR.xTOtiMtSYAuy/34CeQtPut6.', '08165474936', 'ita2@yahoo.com', 'ita', 'ita', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'adlxxQnevd4SMGKrOfAr9iieYmCzY9nQETiKD5j6', NULL, '2016-05-10 20:15:13', '2016-05-10 20:15:13'),
(77, '$2y$10$NYo.LZyeeBPK7m3DLm3Gr.KE9R9R8dsa.xlKAF0cWWAtT4EWCwKnK', '08023002650', 'komolafe@yahoo.com', 'komolafe', 'komolafe', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'UM43G82UjylzNcoDbCcB3Fq9vFjO09Ihf7DFrmcG', NULL, '2016-05-10 20:18:48', '2016-05-10 20:18:48'),
(78, '$2y$10$Cxq67yMz/UOLRZrgaLKEju3vjpC2e2PBdqYXD02OAajctnBf76Tn2', '07032096056', 'lewis@yahoo.com', 'lewis', 'lewis', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'jwv5HisFhdY94QFsTSbzr8xJgaC21bwSwo6jQVK2', NULL, '2016-05-10 20:19:57', '2016-05-10 20:19:57'),
(79, '$2y$10$mXtUW1DL4r2CCohhsIvLT.c4RmDXrbbubxxW9sdJiCuaROgLlC5tC', '08167550222', 'mabinuori@yahoo.com', 'mabinuori', 'mabinuori', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'MomoFIx3tbKjzGlInEUBS2vgJx9VnWXnH9VLVYyw', NULL, '2016-05-10 20:21:16', '2016-05-10 20:21:16'),
(80, '$2y$10$afSKfOl/vqexeDIhsGV6vO0ym3ArjfUL8kBk.NPfmthSMHLfsAM3y', '08063977980', 'madu@yahoo.com', 'madu', 'madu', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'CRUUN94B7Norh7MvflRmandjTbeIGzZn1mfPg6Nf', NULL, '2016-05-10 20:22:07', '2016-05-10 20:22:07'),
(81, '$2y$10$XpakwwBkQFsBMnmNAi7Z3.k8Ha2SS9Nz/7KAyJnzaX1ZN0hgWyexK', '08038024291', 'mmadu@yahoo.com', 'mmadu', 'mmadu', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'hibJgGKd4ZDBzyUXZHLX7vt0YXxvxeUU9c7SBkDS', NULL, '2016-05-10 20:23:09', '2016-05-10 20:23:09'),
(82, '$2y$10$zPRR.ipwWcrE0ddcXBOZSOtB5yY5tkRsDkaTEPA1TFHFVrVWgxK9i', '08033776967', 'nnaji@yahoo.com', 'nnaji', 'nnaji', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'A5DA56uQNcr2Mtm32D4IalX85DScp9hC1O48EDT4', NULL, '2016-05-10 20:23:55', '2016-05-10 20:23:55'),
(83, '$2y$10$1ToqVcVtu56CS27LvXGZRODc8iiS0cOhfTs8OcctWwkVt9xt4EKEC', '09098837414', 'nwachukwu@yahoo.com', 'nwachukwu', 'nwachukwu', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'xXX6BoNhTLoTvYCekcz4XWnX18GHhAPmVHPuU4JX', NULL, '2016-05-10 20:24:50', '2016-05-10 20:24:50'),
(84, '$2y$10$HSVOCw35bKO0/jbP0bmvxOQcqvSze4PPZ/p1YuSdNUwxLNiDYXCzu', '08035747450', 'nwokedi@yahoo.com', 'nwokedi', 'nwokedi', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'Nub23jEA57quQtXA8NrjZpMP6zcQpT7Jtng3hxaJ', NULL, '2016-05-10 20:25:33', '2016-05-10 20:25:33'),
(85, '$2y$10$yR3N.s1FuOJ4gNgHeqUqFOWcJqOYc8JuwEI5PGzWieiKSdbgvNvBO', '08035034468', 'nwoko@yahoo.com', 'nwoko', 'nwoko', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, '4UQ9KGagHFvX6svhvUCfdi2rKOW6TBx6w2cjsfJc', NULL, '2016-05-10 20:26:14', '2016-05-10 20:26:14'),
(86, '$2y$10$WRS6Nx9me1/vIb18Qog6D.lOvkxuBKtP0a94GUulpD6NCvNBN40FS', '08037166828', 'nwokolo@yahoo.com', 'nwokolo', 'nwokolo', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'LNIrhXe5JviimpWGwno5BJMv6Gey6VTjNMTUUJjY', NULL, '2016-05-10 20:26:58', '2016-05-10 20:26:58'),
(87, '$2y$10$Nbrv9yyfe1DQfppBYy9QAuv1QWxZtcBEyUMCPr4I7m3/DV/DbNP8S', '08109588383', 'obi@yahoo.com', 'obi', 'obi', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'fAbxjpSxkNFTaI93ETPlW7LeoAdyHuYBT4dWAP8T', NULL, '2016-05-10 20:27:47', '2016-05-10 20:27:47'),
(88, '$2y$10$034w0cXpEXdxx4OHXS6RfervxXn2bOrcwdyeKL1b547KAtLTZ75Wu', '08037143393', 'obialo@yahoo.com', 'obialo', 'obialo', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'M6iaN4GfYXctTrRjvMnlERGjUDuvMdahmsB97tJF', NULL, '2016-05-10 20:28:24', '2016-05-10 20:28:24'),
(89, '$2y$10$tURfIshQ7E1ka39L.BHG9efO7G9dxlBatWdFvoNDMVsH168BAs9e.', '07033507011', 'oboh@yahoo.com', 'oboh', 'oboh', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'mrpKWz0VvG75QNy8hMi4mYgfo7LKA312CzKDlWU6', NULL, '2016-05-10 20:29:05', '2016-05-10 20:29:05'),
(90, '$2y$10$QSdgZ1rf5BhuPw.XbaXgzu1RC8LCMwpvhFKT4Br2sv.4nJZhKmtMi', '08064428179', 'odubanjo@yahoo.com', 'odubanjo', 'odubanjo', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'mr4APScaNyWhGHJwocPjxTDpe7VqTKPy86lPDqSK', NULL, '2016-05-10 20:29:54', '2016-05-10 20:29:54'),
(91, '$2y$10$Bt2fnterlvSOXRANihB5pOwzxeSFs0b.Dr1LUY/W9mPGV9NxjEile', '08033040413', 'ogale@yahoo.com', 'ogale', 'ogale', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, '4wGxEGsOTGVek0MkhajsMY7P38IdUUl6JD6zXP7I', NULL, '2016-05-10 20:30:37', '2016-05-10 20:30:37'),
(92, '$2y$10$0vBArfICPrbz.h.OKvpdzuW/IFxUwebUlpgtRT/9.3k47x3VNKnCq', '08034166664', 'ogunleye@yahoo.com', 'ogunleye', 'ogunleye', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'ZseLgmMne0o4e1ux7RyiH80hsxPdwSQaTYR1pg56', NULL, '2016-05-10 20:31:26', '2016-05-10 20:31:26'),
(93, '$2y$10$PJZslNw3Ggp0fPzgfdPCAO65v5lzwE9S.0HxqX.ZxUsc0MddpRiU6', '08131081768', 'ogunmuko@yahoo.com', 'ogunmuko', 'ogunmuko', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'kCqBSLrd5Zjg6sW4z4QaW0Fff5E8xsf0pOy0kY4n', NULL, '2016-05-10 20:32:25', '2016-05-10 20:32:25'),
(94, '$2y$10$obNKrxuLo0Qvt3qF9R.ZSOMkhwr7C3CtmCJ1wOlCTbwyZ3NGlxofu', '08131964766', 'ojopoke@yahoo.com', 'ojopoke', 'ojopoke', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'mpzhsjT334G1utH9CIDpzV0d2OleADL09dlv9Her', NULL, '2016-05-10 20:33:29', '2016-05-10 20:33:29'),
(95, '$2y$10$wM/fYuLo3GzgzMHt4jFTWefGUk.QS3QwzcWVLhct4.b3ZKCYd2Yz2', '08032235812', 'okoye@yahoo.com', 'okoye', 'okoye', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'WrTNoCx1goU29Ty6oRpEvLigdJBfqE0fZHTM2sOn', NULL, '2016-05-10 20:34:13', '2016-05-10 20:34:13'),
(96, '$2y$10$MCCRd0.LYhttAbaD6/HsDukBclgjUa/qw/.s6wXimASYggQxM407C', '08023180841', 'okpor@yahoo.com', 'okpor', 'okpor', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'hJrtzyVjGSbhpbbmDbpiYsW1JGjfs13T1lW44B10', NULL, '2016-05-10 20:35:16', '2016-05-10 20:35:16'),
(97, '$2y$10$m/7SO5VCIUsZcx7A4zsED.vlhKuIiUrCA72Yb5zYPPF9C1UI6eb4e', '08023388331', 'okunola@yahoo.com', 'okunola', 'okunola', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'ANjJmznLrz2gF5urPXrRw4Wwmxm5MvMlUO9BhAlV', NULL, '2016-05-10 20:36:00', '2016-05-10 20:36:00'),
(98, '$2y$10$Ah1KsPN.zF/jD.ERJaTVYu2B9..ek.GIBzyviTF6tZDdztaKpR1RG', '08096742166', 'oladele@yahoo.com', 'oladele', 'oladele', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'Q1CIRvfnBtlUdAk0hE4n2CqZK8KhrRxPAxLtl4Ww', NULL, '2016-05-10 20:36:45', '2016-05-10 20:36:45'),
(99, '$2y$10$ulv1vRUM86q31wddB95FkOP.s/T2itRZjxOXBc6PbfK5sZJy3YfCS', '08037158695', 'oloye@yahoo.com', 'oloye', 'oloye', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'f2ZN6BnmQjoanoeUYh25zzVPQoCdTRUHHYYk1hRu', NULL, '2016-05-10 20:37:31', '2016-05-10 20:37:31'),
(100, '$2y$10$vs8jMJifrQ.H9XUaL2N2i.qo0FSUndfwBkwxuXSm75JjTZur083Ca', '07062058069', 'olugbemi@yahoo.com', 'olugbemi', 'olugbemi', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, '30hOWgyTlP6zbgvNffMLU5b8x5KUCi7qLmM14cIH', NULL, '2016-05-10 20:38:22', '2016-05-10 20:38:22'),
(101, '$2y$10$cnELvjRsjuCTPvbr5jbbYeQlzq.yPUyxqUCEDyQhFP.216TgxyJMu', '08038277868', 'oluwamola@yahoo.com', 'oluwamola', 'oluwamola', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'EqGpTFUyxqu4ShcMoPIpv0xd6zuRkT07MiaPBvz7', NULL, '2016-05-10 20:39:19', '2016-05-10 20:39:19'),
(102, '$2y$10$3biRDHGpctFP/5BzFiIEYOPaDpvycUa.JcB.b1WB3b4wDB018QvrS', '08028343733', 'omotosho@yahoo.com', 'omotosho', 'omotosho', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'I4JRvK4hjIPZdxMwRtiDVxudB8YOLLNiXNW9m4Y2', NULL, '2016-05-10 20:40:10', '2016-05-10 20:40:10'),
(103, '$2y$10$IRxifIL/6T282LSe0xslueXOadGrTT/6H2FK6SxfXu.Kx1.CFLQJm', '08038182294', 'omotosho2@yahoo.com', 'omotosho', 'omotosho', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'cNfxgq0VGCixpld2lnH4i1Ty4KfRGws6KFUvcUN9', NULL, '2016-05-10 20:42:08', '2016-05-10 20:42:08'),
(104, '$2y$10$p9f.cWLK/MbSBPZGxUEfrumiarCgkVlOFH5G8Z5j7hZWX1KGlhe2a', '08034486444', 'onuisile@yahoo.com', 'onisile', 'onisile', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, '81uvddfKtf0T5w2bGsFreDaN2nNArGE5i2HHnBvU', NULL, '2016-05-10 20:44:29', '2016-05-10 20:44:29'),
(105, '$2y$10$fZodq0NSjz3lxbYwBxN7QeNgkhvKt/lgQ9BtzNYoOW4QjTsjLhzDS', '08038071133', 'onyemaechi@yahoo.com', 'onyemaechi', 'onyemaechi', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'ELWUGgCkNKBWfxDC4PbvPWi2an8oWQ42EOUncnpy', NULL, '2016-05-10 20:45:22', '2016-05-10 20:45:22'),
(106, '$2y$10$kobFC86aIeaIixUoGXn01e6yyAZ/GdRz2WcFp3CqIO/suc5eCl.46', '08023324754', 'orisunmibare@yahoo.com', 'orisunmibare', 'orisunmibare', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'pE6Xm0FqxuhNRlzk1L8xrkSYLmv3yTNZU7P6g7si', NULL, '2016-05-10 20:46:05', '2016-05-10 20:46:05'),
(107, '$2y$10$BxSU.Awvy3V0pZLKYBdazegLA.Baad3Pmy8Xi080Y1nwu6LkY.xnW', '08024791052', 'osariemen@yahoo.com', 'osariemen', 'osariemen', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'okMHN1fJVnB766aRzD16XazOn4DfwhGzAoLoF3p8', NULL, '2016-05-10 20:47:25', '2016-05-10 20:47:25'),
(108, '$2y$10$iow7IWkhLFW11f12F0lTgOfpmL/Ph/oASRdXDZ9PmFFrMePnllYAW', '08022315335', 'oshinonwu@yahoo.com', 'oshinonwu', 'oshinonwu', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'vYeq2bXgEacKnmdUT0W3ZeEeA35S8QqyFLM4GXiP', NULL, '2016-05-10 20:48:33', '2016-05-10 20:48:33'),
(109, '$2y$10$vsCUD81G.ElTVKAP3ioeg.qRWbALH1EJoB6te0vsNYQrJQCx0SayW', '08026952105', 'oyewunmi@yahoo.com', 'oyewunmi', 'oyewunmi', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'vew419QwTwTTxuK2IdVRrTJIAanDkxIqMXcZT3P7', NULL, '2016-05-10 20:49:39', '2016-05-10 20:49:39'),
(110, '$2y$10$2T6WLdiIGG23e3owNgfMxOdOIZKrVB8r9bqTy2jSLu0x0TyETgPhW', '08062061635', 'rahmon@yahoo.com', 'rahmon', 'rahmon', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'bB06ZyxbpCyCMdZxMqdN04HMBVnkrXOO1wwFpr1Y', NULL, '2016-05-10 20:50:30', '2016-05-10 20:50:30'),
(111, '$2y$10$9jqLLPY8mEnr6c71ejfMkObDq3UsjzB0aS.AmWulDCoo3he6xBuYO', '08057564138', 'sanusi@yahoo.com', 'sanusi', 'sanusi', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, '78PlzgmXUBVtEpjbQQcUUQZs39j0oi9pCUxPMlVk', NULL, '2016-05-10 20:51:12', '2016-05-10 20:51:12'),
(112, '$2y$10$YKL9IhtC84cnxOQXnn/ZP.crbaipX.7EvM7jI0cCgUKK9gl9rbvAK', '08034905466', 'shittu@yahoo.com', 'shittu', 'shittu', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'DM5GY8Kv9KOHriZlIlKW6bSVxHoEkvQlDkS0nXta', NULL, '2016-05-10 20:51:58', '2016-05-10 20:51:58'),
(113, '$2y$10$wo0m1..N1ek27j2cVUsdpeZJEZj1LJ/Sd4fOuro6kn.hKgFdUrZ2u', '08149095219', 'shotolu@yahoo.com', 'shotolu', 'shotolu', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'S7k4nvCEAnWwIo6OMsUivb3L5iCZKOWXDbOruNwM', NULL, '2016-05-10 20:52:57', '2016-05-10 20:52:57'),
(114, '$2y$10$iePQAszmX8CgoJlC7rPJLu.4GoExn6VDbzptRTQuAVvCnwDcCJcOO', '07064484612', 'tamunokubie@yahoo.com', 'tamunokubie', 'tamunokubie', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, '9k2aOQAAagCtbO86vVp1g19Vii0IVC5VVALtmwTS', NULL, '2016-05-10 20:54:00', '2016-05-10 20:54:00'),
(115, '$2y$10$5KRaqzDxxpnrQYZVdr.zLONqLsbam20UqzubMrwVhEWr/EkRbaLyi', '08023068758', 'uba@yahoo.com', 'uba', 'uba', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'e7ljS1H9iNSAAq65IpOQaq7RoR04JNIa1x3oMpH7', NULL, '2016-05-10 20:54:46', '2016-05-10 20:54:46'),
(116, '$2y$10$bFhrlo3THpxlm1OaxYsZdOWxZKoeLVvV7VF.nvoJZ6w/Upn3bjEE.', '08030612120', 'ubah@yahoo.com', 'ubah', 'ubah', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, '2SO3D3XjKW6ptnNq4HosflkVYAkE9AOfk1Vt8hD9', NULL, '2016-05-10 20:55:51', '2016-05-10 20:55:51'),
(117, '$2y$10$jutfWA1dK4HIFO6wQ8r1V.G2HZ5pS/hM1cvcVFEakXrPX5b5Xx672', '08132321240', 'udemgba@yahoo.com', 'udemgba', 'udemgba', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'OOhu19mmZoQQn3H92hB91srj1jEcKLzPE0w2crEH', NULL, '2016-05-10 20:57:11', '2016-05-10 20:57:11'),
(118, '$2y$10$Me.kHoPOaxYLLDVb17HExeTZvf8pdPCYLwMqodrSJ3PfkI4Q6nSyi', '08037207868', 'udensi@yahoo.com', 'udensi', 'udensi', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'VzeEqfJS3NJU9ceMXDmP1lxdzLKuabQKjbrxGOsA', NULL, '2016-05-10 20:58:06', '2016-05-10 20:58:06'),
(119, '$2y$10$H/QJfNMJIEFGONs6vSR0/eJO34ahWABVQYM/kDK1gTtyLJfzkpcZa', '08032007206', 'umoh@yahoo.com', 'umoh', 'umoh', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, '1aE5zy8qppj0bTym67IRbnx1cIRZqcOGIlHxPO6u', NULL, '2016-05-10 20:58:52', '2016-05-10 20:58:52'),
(120, '$2y$10$YGGwwLlc7IfX5zvLFuSrfOPZ28dpvwl8K9V0vAi1xXsXublfDVQjW', '08037090486', 'woko@yaho.com', 'woko', 'woko', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, '08YV7FIuJQvQH3eP21onJM0Jqg6DEgZjjmXp5nbh', NULL, '2016-05-10 20:59:31', '2016-05-10 20:59:31'),
(121, '$2y$10$znjpgI3jhGeGCkWn3nPiluHHFbSsiLIwBI313XRQd4g1/BVAWzIk2', '08028676935', 'yusuf@yahoo.com', 'yusuf', 'yusuf', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, '85xGYILEtZVW5L9vhh09X5jUlSewy3opGOBAkiPF', NULL, '2016-05-10 21:00:16', '2016-05-10 21:00:16'),
(122, '$2y$10$WgHQSaszEOSJpz2HsoUToeJoyCxh7fGuc3ZoLJA.NubXU42L6E3SG', '08136969411', 'akinwaletaiwo@yahoo.com', 'Akinwale', 'Taiwo', 'oluwakemi', 'Female', '2016-11-14', '08022019851', 4, 624, 2, 1, 1, NULL, 'L9SmifbnzA0q9gLAzRPXnLW8u0FkpblHKzoOYjJu', 'aWtcopdIr5TVYxzKMS4mMOX0p9NToFQo6gQTW4CU9gzHyolBskcgjzjiVV3P', '2016-05-18 17:36:29', '2016-06-28 16:26:32'),
(123, '$2y$10$gCkX.1F71ET93FD9YDjoH.T.xjzk4U19ouUEQBqKE0QDbA3hXmShu', '08171344786', 'adenayasamson@yahoo.com', 'Adenaya', 'Samson', NULL, NULL, NULL, NULL, 4, NULL, NULL, 1, 1, NULL, 'FF1etPRq12Fz7Q6JPyQoeaIO8eepSORxodYzP5j4', 'VH6A0dCGXNcf4ZSJnlTTFomHIvHnK5sfLt1LKxAxVK1BsaXUcOqkqkyEYuhX', '2016-05-18 17:39:13', '2016-08-19 15:21:42'),
(124, '$2y$10$VHtEZrUSJ3MurEgK7bD2neeaNNvhy3KpyJHfdOh0XqJLV1DQsZy0W', '08062101137', 'ogunlekeolufunke@yahoo.com', 'Ogunleke', 'Olufunke', NULL, NULL, NULL, NULL, 4, NULL, NULL, 1, 1, NULL, 'txTzSbMdu025aynsR6LqzChsOWAqsAZn5ulmUrOp', NULL, '2016-05-18 17:40:12', '2016-05-18 17:40:12'),
(125, '$2y$10$lkgkw77NXaKqnFSRJx51.ub5Y/XYDZIFAtyS0TAXSNNrJHVScxLke', '08039404007', 'princekehinde@yahoo.com', 'Famoroti', 'Kehinde', NULL, NULL, NULL, NULL, 4, NULL, NULL, 1, 1, NULL, 'hIGpSYzf0hM32ulRD8cWiJR43hyNxnLHHlCpFFsF', NULL, '2016-05-18 18:01:40', '2016-05-18 18:01:40'),
(126, '$2y$10$EYrAlY/Inycw1yvTWcEOmOfDl/pNEr25HkKl8fHJqZGSm/2nw3eDa', '08028820979', 'sanni@yahoo.com', 'Sanni', 'Sanni', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, '7YqtlQxqWz9dak1O7RD63rcN1jsDfPbUWThctugu', NULL, '2016-05-31 16:51:19', '2016-05-31 16:51:19'),
(127, '$2y$10$637kwDy8t8pDxUrNz0IR7e5s2Jt.KEQafMSX2A3NliGSc8D5yqHwa', '08033018178', 'adesope@yahoo.com', 'Adesope', 'Adesope', '', NULL, '1978-03-26', '', 3, 515, 1, 1, 1, NULL, 'Pa7LlEouJd3c00984C2F0JUScBYe6YGpnM58dnbc', NULL, '2016-05-31 16:56:59', '2016-05-31 17:15:16'),
(128, '$2y$10$mXa.IWAgm6iz3kG7PBqU1.y6DtRlMZ0b6i15niz4qx1KKtJCiVZkm', '08033018176', 'adegbite@yahoo.com', 'Adegbite', 'Adegbite', NULL, NULL, NULL, NULL, 3, NULL, NULL, 1, 1, NULL, 'DVr5D8TpCqjMkU00UhvMbRGjIpDDA6vK8QnxrBrD', 'MWZwS1RMW7LkL2b4XS7wdvm7GaCGVbtJZhbTiei3saWY4t9e9rr8QiCR1emg', '2016-05-31 17:16:49', '2016-08-19 15:15:03');

-- --------------------------------------------------------

--
-- Table structure for table `user_types`
--

CREATE TABLE `user_types` (
  `user_type_id` int(10) UNSIGNED NOT NULL,
  `user_type` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `type` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `user_types`
--

INSERT INTO `user_types` (`user_type_id`, `user_type`, `type`, `created_at`, `updated_at`) VALUES
(1, 'Developer', 1, NULL, NULL),
(2, 'Super Admin', 2, NULL, NULL),
(3, 'Sponsor', 2, '2016-04-28 21:35:55', '2016-04-28 21:35:55'),
(4, 'Staff', 2, '2016-04-28 21:35:15', '2016-04-28 21:35:15');

-- --------------------------------------------------------

--
-- Structure for view `assessment_detailsviews`
--
DROP TABLE IF EXISTS `assessment_detailsviews`;

CREATE DEFINER=`ekaruztech_user`@`%` SQL SECURITY DEFINER VIEW `assessment_detailsviews`  AS  select `f`.`assessment_id` AS `assessment_id`,`f`.`subject_classroom_id` AS `subject_classroom_id`,`f`.`assessment_setup_detail_id` AS `assessment_setup_detail_id`,`f`.`marked` AS `marked`,`g`.`assessment_detail_id` AS `assessment_detail_id`,`g`.`student_id` AS `student_id`,`j`.`student_no` AS `student_no`,concat(`j`.`first_name`,' ',`j`.`last_name`) AS `student_name`,`j`.`gender` AS `gender`,`g`.`score` AS `score`,`h`.`weight_point` AS `weight_point`,`h`.`number` AS `number`,`h`.`percentage` AS `percentage`,`h`.`description` AS `description`,`h`.`submission_date` AS `submission_date`,`i`.`assessment_setup_id` AS `assessment_setup_id`,`i`.`assessment_no` AS `assessment_no`,`m`.`ca_weight_point` AS `ca_weight_point`,`m`.`exam_weight_point` AS `exam_weight_point`,`j`.`sponsor_id` AS `sponsor_id`,`k`.`phone_no` AS `phone_no`,`k`.`email` AS `email`,concat(`k`.`first_name`,' ',`k`.`last_name`) AS `sponsor_name`,`a`.`subject_id` AS `subject_id`,`a`.`classroom_id` AS `classroom_id`,`a`.`tutor_id` AS `tutor_id`,concat(`n`.`first_name`,' ',`n`.`last_name`) AS `tutor`,`c`.`classroom` AS `classroom`,`c`.`classlevel_id` AS `classlevel_id`,`d`.`classlevel` AS `classlevel`,`d`.`classgroup_id` AS `classgroup_id`,`a`.`academic_term_id` AS `academic_term_id`,`e`.`academic_term` AS `academic_term` from (((((((((((`subject_classrooms` `a` join `classrooms` `c` on((`a`.`classroom_id` = `c`.`classroom_id`))) join `classlevels` `d` on((`c`.`classlevel_id` = `d`.`classlevel_id`))) join `academic_terms` `e` on((`a`.`academic_term_id` = `e`.`academic_term_id`))) join `assessments` `f` on((`a`.`subject_classroom_id` = `f`.`subject_classroom_id`))) join `assessment_details` `g` on((`f`.`assessment_id` = `g`.`assessment_id`))) join `assessment_setup_details` `h` on((`f`.`assessment_setup_detail_id` = `h`.`assessment_setup_detail_id`))) join `assessment_setups` `i` on((`h`.`assessment_setup_id` = `i`.`assessment_setup_id`))) join `students` `j` on((`g`.`student_id` = `j`.`student_id`))) left join `users` `k` on((`j`.`sponsor_id` = `k`.`user_id`))) join `classgroups` `m` on((`d`.`classgroup_id` = `m`.`classgroup_id`))) left join `users` `n` on((`a`.`tutor_id` = `n`.`user_id`))) ;

-- --------------------------------------------------------

--
-- Structure for view `exams_detailsviews`
--
DROP TABLE IF EXISTS `exams_detailsviews`;

CREATE DEFINER=`ekaruztech_user`@`%` SQL SECURITY DEFINER VIEW `exams_detailsviews`  AS  select `exam_details`.`exam_detail_id` AS `exam_detail_id`,`exams`.`exam_id` AS `exam_id`,`subject_classrooms`.`subject_classroom_id` AS `subject_classroom_id`,`subject_classrooms`.`subject_id` AS `subject_id`,`subject_classrooms`.`tutor_id` AS `tutor_id`,`classrooms`.`classlevel_id` AS `classlevel_id`,`student_classes`.`classroom_id` AS `classroom_id`,`students`.`student_id` AS `student_id`,`classrooms`.`classroom` AS `classroom`,concat(ucase(`students`.`first_name`),' ',lcase(`students`.`last_name`)) AS `fullname`,`students`.`gender` AS `student_gender`,`students`.`student_no` AS `student_no`,`exam_details`.`ca` AS `ca`,`exam_details`.`exam` AS `exam`,(`exam_details`.`exam` + `exam_details`.`ca`) AS `student_total`,`classgroups`.`ca_weight_point` AS `ca_weight_point`,`classgroups`.`exam_weight_point` AS `exam_weight_point`,(`classgroups`.`exam_weight_point` + `classgroups`.`ca_weight_point`) AS `weight_point_total`,`academic_terms`.`academic_term_id` AS `academic_term_id`,`academic_terms`.`academic_term` AS `academic_term`,`exams`.`marked` AS `marked`,`academic_terms`.`academic_year_id` AS `academic_year_id`,`academic_years`.`academic_year` AS `academic_year`,`classlevels`.`classlevel` AS `classlevel`,`classlevels`.`classgroup_id` AS `classgroup_id` from (((((((((`exams` join `exam_details` on((`exams`.`exam_id` = `exam_details`.`exam_id`))) join `subject_classrooms` on((`exams`.`subject_classroom_id` = `subject_classrooms`.`subject_classroom_id`))) join `students` on((`exam_details`.`student_id` = `students`.`student_id`))) join `academic_terms` on((`subject_classrooms`.`academic_term_id` = `academic_terms`.`academic_term_id`))) join `academic_years` on((`academic_years`.`academic_year_id` = `academic_terms`.`academic_year_id`))) join `student_classes` on((`students`.`student_id` = `student_classes`.`student_id`))) join `classrooms` on((`student_classes`.`classroom_id` = `classrooms`.`classroom_id`))) join `classlevels` on((`classrooms`.`classlevel_id` = `classlevels`.`classlevel_id`))) join `classgroups` on((`classgroups`.`classgroup_id` = `classlevels`.`classgroup_id`))) ;

-- --------------------------------------------------------

--
-- Structure for view `exams_subjectviews`
--
DROP TABLE IF EXISTS `exams_subjectviews`;

CREATE DEFINER=`ekaruztech_user`@`%` SQL SECURITY DEFINER VIEW `exams_subjectviews`  AS  select `a`.`exam_id` AS `exam_id`,`f`.`classroom_id` AS `classroom_id`,`f`.`classroom` AS `classroom`,`b`.`subject_id` AS `subject_id`,`a`.`subject_classroom_id` AS `subject_classroom_id`,`b`.`tutor_id` AS `tutor_id`,concat(ucase(`j`.`first_name`),' ',`j`.`last_name`) AS `tutor`,`h`.`ca_weight_point` AS `ca_weight_point`,`h`.`exam_weight_point` AS `exam_weight_point`,`a`.`marked` AS `marked`,`f`.`classlevel_id` AS `classlevel_id`,`g`.`classlevel` AS `classlevel`,`b`.`academic_term_id` AS `academic_term_id`,`d`.`academic_term` AS `academic_term`,`d`.`academic_year_id` AS `academic_year_id`,`e`.`academic_year` AS `academic_year` from ((((((`exams` `a` join `subject_classrooms` `b` on((`a`.`subject_classroom_id` = `b`.`subject_classroom_id`))) left join (`classlevels` `g` join `classrooms` `f` on((`f`.`classlevel_id` = `g`.`classlevel_id`))) on((`b`.`classroom_id` = `f`.`classroom_id`))) join `academic_terms` `d` on((`b`.`academic_term_id` = `d`.`academic_term_id`))) join `academic_years` `e` on((`d`.`academic_year_id` = `e`.`academic_year_id`))) join `classgroups` `h` on((`g`.`classgroup_id` = `h`.`classgroup_id`))) left join `users` `j` on((`b`.`tutor_id` = `j`.`user_id`))) ;

-- --------------------------------------------------------

--
-- Structure for view `students_classroomviews`
--
DROP TABLE IF EXISTS `students_classroomviews`;

CREATE DEFINER=`ekaruztech_user`@`%` SQL SECURITY DEFINER VIEW `students_classroomviews`  AS  select concat(ucase(`students`.`first_name`),' ',`students`.`last_name`) AS `fullname`,`students`.`student_no` AS `student_no`,`classrooms`.`classroom` AS `classroom`,`classrooms`.`classroom_id` AS `classroom_id`,`students`.`student_id` AS `student_id`,`classlevels`.`classlevel` AS `classlevel`,`classrooms`.`classlevel_id` AS `classlevel_id`,`students`.`sponsor_id` AS `sponsor_id`,concat(ucase(`users`.`first_name`),' ',`users`.`last_name`) AS `sponsor_name`,`student_classes`.`academic_year_id` AS `academic_year_id`,`academic_years`.`academic_year` AS `academic_year`,`students`.`status_id` AS `status_id` from (((((`students` join `student_classes` on((`student_classes`.`student_id` = `students`.`student_id`))) join `classrooms` on((`student_classes`.`classroom_id` = `classrooms`.`classroom_id`))) join `classlevels` on((`classlevels`.`classlevel_id` = `classrooms`.`classlevel_id`))) join `academic_years` on((`student_classes`.`academic_year_id` = `academic_years`.`academic_year_id`))) join `users` on((`students`.`sponsor_id` = `users`.`user_id`))) ;

-- --------------------------------------------------------

--
-- Structure for view `subjects_assessmentsviews`
--
DROP TABLE IF EXISTS `subjects_assessmentsviews`;

CREATE DEFINER=`ekaruztech_user`@`%` SQL SECURITY DEFINER VIEW `subjects_assessmentsviews`  AS  select `a`.`tutor` AS `tutor`,`a`.`tutor_id` AS `tutor_id`,`a`.`classroom_id` AS `classroom_id`,`a`.`subject_classroom_id` AS `subject_classroom_id`,`a`.`subject_id` AS `subject_id`,`a`.`subject` AS `subject`,`a`.`subject_group_id` AS `subject_group_id`,`a`.`academic_term_id` AS `academic_term_id`,`a`.`academic_term` AS `academic_term`,`a`.`exam_status_id` AS `exam_status_id`,`a`.`exam_status` AS `exam_status`,`a`.`classlevel_id` AS `classlevel_id`,`a`.`classroom` AS `classroom`,`b`.`assessment_id` AS `assessment_id`,`b`.`marked` AS `marked`,`c`.`assessment_setup_detail_id` AS `assessment_setup_detail_id`,`c`.`number` AS `number`,`c`.`weight_point` AS `weight_point`,`c`.`percentage` AS `percentage`,`c`.`assessment_setup_id` AS `assessment_setup_id`,`c`.`submission_date` AS `submission_date`,`c`.`description` AS `description` from ((`subjects_classroomviews` `a` left join `assessments` `b` on((`a`.`subject_classroom_id` = `b`.`subject_classroom_id`))) left join `assessment_setup_details` `c` on((`b`.`assessment_setup_detail_id` = `c`.`assessment_setup_detail_id`))) ;

-- --------------------------------------------------------

--
-- Structure for view `subjects_classroomviews`
--
DROP TABLE IF EXISTS `subjects_classroomviews`;

CREATE DEFINER=`ekaruztech_user`@`%` SQL SECURITY DEFINER VIEW `subjects_classroomviews`  AS  select concat(ucase(`e`.`first_name`),' ',`e`.`last_name`) AS `tutor`,`e`.`user_id` AS `tutor_id`,`a`.`classroom_id` AS `classroom_id`,`a`.`subject_classroom_id` AS `subject_classroom_id`,`a`.`subject_id` AS `subject_id`,`d`.`subject` AS `subject`,`d`.`subject_group_id` AS `subject_group_id`,`a`.`academic_term_id` AS `academic_term_id`,`b`.`academic_term` AS `academic_term`,`a`.`exam_status_id` AS `exam_status_id`,(case `a`.`exam_status_id` when 1 then 'Marked' when 2 then 'Not Marked' end) AS `exam_status`,`c`.`classlevel_id` AS `classlevel_id`,`c`.`classroom` AS `classroom` from ((((`subject_classrooms` `a` join `academic_terms` `b` on((`a`.`academic_term_id` = `b`.`academic_term_id`))) join `classrooms` `c` on((`a`.`classroom_id` = `c`.`classroom_id`))) join `smartschools`.`subjects` `d` on((`d`.`subject_id` = `a`.`subject_id`))) left join `users` `e` on((`a`.`tutor_id` = `e`.`user_id`))) ;

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
  ADD KEY `academic_terms_term_type_id_index` (`term_type_id`);

--
-- Indexes for table `academic_years`
--
ALTER TABLE `academic_years`
  ADD PRIMARY KEY (`academic_year_id`),
  ADD KEY `academic_years_status_index` (`status`);

--
-- Indexes for table `assessments`
--
ALTER TABLE `assessments`
  ADD PRIMARY KEY (`assessment_id`),
  ADD KEY `assessments_subject_classroom_id_index` (`subject_classroom_id`),
  ADD KEY `assessments_assessment_setup_detail_id_index` (`assessment_setup_detail_id`),
  ADD KEY `assessments_marked_index` (`marked`);

--
-- Indexes for table `assessment_details`
--
ALTER TABLE `assessment_details`
  ADD PRIMARY KEY (`assessment_detail_id`),
  ADD KEY `assessment_details_student_id_index` (`student_id`),
  ADD KEY `assessment_details_assessment_id_index` (`assessment_id`);

--
-- Indexes for table `assessment_setups`
--
ALTER TABLE `assessment_setups`
  ADD PRIMARY KEY (`assessment_setup_id`),
  ADD KEY `assessment_setups_classgroup_id_index` (`classgroup_id`),
  ADD KEY `assessment_setups_academic_term_id_index` (`academic_term_id`);

--
-- Indexes for table `assessment_setup_details`
--
ALTER TABLE `assessment_setup_details`
  ADD PRIMARY KEY (`assessment_setup_detail_id`),
  ADD KEY `assessment_setup_details_assessment_setup_id_index` (`assessment_setup_id`);

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
-- Indexes for table `class_masters`
--
ALTER TABLE `class_masters`
  ADD PRIMARY KEY (`class_master_id`),
  ADD KEY `class_masters_user_id_index` (`user_id`),
  ADD KEY `class_masters_classroom_id_index` (`classroom_id`),
  ADD KEY `class_masters_academic_year_id_index` (`academic_year_id`);

--
-- Indexes for table `domains`
--
ALTER TABLE `domains`
  ADD PRIMARY KEY (`domain_id`);

--
-- Indexes for table `domain_assessments`
--
ALTER TABLE `domain_assessments`
  ADD PRIMARY KEY (`domain_assessment_id`),
  ADD KEY `domain_assessments_student_id_index` (`student_id`),
  ADD KEY `domain_assessments_academic_term_id_index` (`academic_term_id`);

--
-- Indexes for table `domain_details`
--
ALTER TABLE `domain_details`
  ADD PRIMARY KEY (`domain_detail_id`),
  ADD KEY `domain_details_domain_id_index` (`domain_id`),
  ADD KEY `domain_details_domain_assessment_id_index` (`domain_assessment_id`),
  ADD KEY `domain_details_option_index` (`option`);

--
-- Indexes for table `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`exam_id`),
  ADD KEY `exams_subject_classroom_id_index` (`subject_classroom_id`),
  ADD KEY `exams_marked_index` (`marked`);

--
-- Indexes for table `exam_details`
--
ALTER TABLE `exam_details`
  ADD PRIMARY KEY (`exam_detail_id`),
  ADD KEY `exam_details_exam_id_index` (`exam_id`),
  ADD KEY `exam_details_student_id_index` (`student_id`);

--
-- Indexes for table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`grade_id`),
  ADD KEY `grades_classgroup_id_index` (`classgroup_id`);

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
-- Indexes for table `remarks`
--
ALTER TABLE `remarks`
  ADD PRIMARY KEY (`remark_id`),
  ADD KEY `remarks_student_id_index` (`student_id`),
  ADD KEY `remarks_academic_term_id_index` (`academic_term_id`),
  ADD KEY `remarks_user_id_index` (`user_id`);

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
-- Indexes for table `sms`
--
ALTER TABLE `sms`
  ADD PRIMARY KEY (`sms_id`);

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
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD KEY `students_sponsor_id_index` (`sponsor_id`),
  ADD KEY `students_classroom_id_index` (`classroom_id`),
  ADD KEY `students_status_id_index` (`status_id`),
  ADD KEY `students_admitted_term_id_index` (`admitted_term_id`),
  ADD KEY `students_lga_id_index` (`lga_id`),
  ADD KEY `students_created_by_index` (`created_by`);

--
-- Indexes for table `student_classes`
--
ALTER TABLE `student_classes`
  ADD PRIMARY KEY (`student_class_id`),
  ADD KEY `student_classes_student_id_index` (`student_id`),
  ADD KEY `student_classes_classroom_id_index` (`classroom_id`),
  ADD KEY `student_classes_academic_year_id_index` (`academic_year_id`);

--
-- Indexes for table `student_subjects`
--
ALTER TABLE `student_subjects`
  ADD PRIMARY KEY (`student_id`,`subject_classroom_id`),
  ADD KEY `student_subjects_student_id_index` (`student_id`),
  ADD KEY `student_subjects_subject_classroom_id_index` (`subject_classroom_id`);

--
-- Indexes for table `subject_classrooms`
--
ALTER TABLE `subject_classrooms`
  ADD PRIMARY KEY (`subject_classroom_id`),
  ADD KEY `subject_classrooms_subject_id_index` (`subject_id`),
  ADD KEY `subject_classrooms_classroom_id_index` (`classroom_id`),
  ADD KEY `subject_classrooms_academic_term_id_index` (`academic_term_id`),
  ADD KEY `subject_classrooms_exam_status_id_index` (`exam_status_id`),
  ADD KEY `subject_classrooms_tutor_id_index` (`tutor_id`);

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
  MODIFY `academic_term_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `academic_years`
--
ALTER TABLE `academic_years`
  MODIFY `academic_year_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `assessments`
--
ALTER TABLE `assessments`
  MODIFY `assessment_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=177;
--
-- AUTO_INCREMENT for table `assessment_details`
--
ALTER TABLE `assessment_details`
  MODIFY `assessment_detail_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2424;
--
-- AUTO_INCREMENT for table `assessment_setups`
--
ALTER TABLE `assessment_setups`
  MODIFY `assessment_setup_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `assessment_setup_details`
--
ALTER TABLE `assessment_setup_details`
  MODIFY `assessment_setup_detail_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT for table `classgroups`
--
ALTER TABLE `classgroups`
  MODIFY `classgroup_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `classlevels`
--
ALTER TABLE `classlevels`
  MODIFY `classlevel_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `classrooms`
--
ALTER TABLE `classrooms`
  MODIFY `classroom_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `class_masters`
--
ALTER TABLE `class_masters`
  MODIFY `class_master_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `domains`
--
ALTER TABLE `domains`
  MODIFY `domain_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `domain_assessments`
--
ALTER TABLE `domain_assessments`
  MODIFY `domain_assessment_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `domain_details`
--
ALTER TABLE `domain_details`
  MODIFY `domain_detail_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
--
-- AUTO_INCREMENT for table `exams`
--
ALTER TABLE `exams`
  MODIFY `exam_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;
--
-- AUTO_INCREMENT for table `exam_details`
--
ALTER TABLE `exam_details`
  MODIFY `exam_detail_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1105;
--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
  MODIFY `grade_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT for table `menus`
--
ALTER TABLE `menus`
  MODIFY `menu_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT for table `menu_headers`
--
ALTER TABLE `menu_headers`
  MODIFY `menu_header_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `menu_item_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;
--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `permission_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=116;
--
-- AUTO_INCREMENT for table `remarks`
--
ALTER TABLE `remarks`
  MODIFY `remark_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `sms`
--
ALTER TABLE `sms`
  MODIFY `sms_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `sponsors`
--
ALTER TABLE `sponsors`
  MODIFY `sponsor_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `staffs`
--
ALTER TABLE `staffs`
  MODIFY `staff_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;
--
-- AUTO_INCREMENT for table `student_classes`
--
ALTER TABLE `student_classes`
  MODIFY `student_class_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;
--
-- AUTO_INCREMENT for table `subject_classrooms`
--
ALTER TABLE `subject_classrooms`
  MODIFY `subject_classroom_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=173;
--
-- AUTO_INCREMENT for table `sub_menu_items`
--
ALTER TABLE `sub_menu_items`
  MODIFY `sub_menu_item_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;
--
-- AUTO_INCREMENT for table `sub_most_menu_items`
--
ALTER TABLE `sub_most_menu_items`
  MODIFY `sub_most_menu_item_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=129;
--
-- AUTO_INCREMENT for table `user_types`
--
ALTER TABLE `user_types`
  MODIFY `user_type_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
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
  ADD CONSTRAINT `assessments_assessment_setup_detail_id_foreign` FOREIGN KEY (`assessment_setup_detail_id`) REFERENCES `assessment_setup_details` (`assessment_setup_detail_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `assessments_subject_classroom_id_foreign` FOREIGN KEY (`subject_classroom_id`) REFERENCES `subject_classrooms` (`subject_classroom_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `assessment_details`
--
ALTER TABLE `assessment_details`
  ADD CONSTRAINT `assessment_details_assessment_id_foreign` FOREIGN KEY (`assessment_id`) REFERENCES `assessments` (`assessment_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `assessment_details_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `assessment_setups`
--
ALTER TABLE `assessment_setups`
  ADD CONSTRAINT `assessment_setups_academic_term_id_foreign` FOREIGN KEY (`academic_term_id`) REFERENCES `academic_terms` (`academic_term_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `assessment_setups_classgroup_id_foreign` FOREIGN KEY (`classgroup_id`) REFERENCES `classgroups` (`classgroup_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `assessment_setup_details`
--
ALTER TABLE `assessment_setup_details`
  ADD CONSTRAINT `assessment_setup_details_assessment_setup_id_foreign` FOREIGN KEY (`assessment_setup_id`) REFERENCES `assessment_setups` (`assessment_setup_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `exams`
--
ALTER TABLE `exams`
  ADD CONSTRAINT `exams_subject_classroom_id_foreign` FOREIGN KEY (`subject_classroom_id`) REFERENCES `subject_classrooms` (`subject_classroom_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `exam_details`
--
ALTER TABLE `exam_details`
  ADD CONSTRAINT `exam_details_exam_id_foreign` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`exam_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `exam_details_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
  ADD CONSTRAINT `student_classes_classroom_id_foreign` FOREIGN KEY (`classroom_id`) REFERENCES `classrooms` (`classroom_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `student_classes_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `student_subjects`
--
ALTER TABLE `student_subjects`
  ADD CONSTRAINT `student_subjects_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `student_subjects_subject_classroom_id_foreign` FOREIGN KEY (`subject_classroom_id`) REFERENCES `subject_classrooms` (`subject_classroom_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `subject_classrooms`
--
ALTER TABLE `subject_classrooms`
  ADD CONSTRAINT `subject_classrooms_classroom_id_foreign` FOREIGN KEY (`classroom_id`) REFERENCES `classrooms` (`classroom_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `subject_classrooms_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `smartschools`.`subjects` (`subject_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `subject_classrooms_tutor_id_foreign` FOREIGN KEY (`tutor_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
