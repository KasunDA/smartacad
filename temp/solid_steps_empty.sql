-- phpMyAdmin SQL Dump
-- version 4.5.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 24, 2016 at 11:46 AM
-- Server version: 10.1.13-MariaDB
-- PHP Version: 5.6.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `solid_steps_empty`
--

DELIMITER $$
--
-- Procedures
--
CREATE PROCEDURE `sp_cloneSubjectsAssigned` (IN `TermFromID` INT, IN `TermToID` INT)  BEGIN
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

CREATE PROCEDURE `sp_deleteSubjectClassRoom` (IN `subjectClassroomID` INT)  BEGIN

	    DELETE FROM exam_details WHERE exam_id IN 
    (SELECT exam_id FROM exams WHERE subject_classroom_id = subjectClassroomID);
    
        DELETE FROM exams WHERE subject_classroom_id = subjectClassroomID;
    
	    DELETE FROM assessment_details WHERE assessment_id IN 
    (SELECT assessment_id FROM assessments WHERE subject_classroom_id = subjectClassroomID);
    
        DELETE FROM assessments WHERE subject_classroom_id = subjectClassroomID;
    
        DELETE FROM student_subjects WHERE subject_classroom_id = subjectClassroomID;
    
        DELETE FROM subject_classrooms WHERE subject_classroom_id = subjectClassroomID;
END$$

CREATE PROCEDURE `sp_modifyStudentsSubject` (IN `SubjectClassRoomID` INT, `StudentIDs` VARCHAR(225))  BEGIN
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

CREATE PROCEDURE `sp_populateAssessmentDetail` (IN `AssessmentID` INT)  BEGIN
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

CREATE PROCEDURE `sp_processAssessmentCA` (IN `TermID` INT, IN `TutorID` INT)  Block0: BEGIN
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

CREATE PROCEDURE `sp_processExams` (IN `TermID` INT, IN `TutorID` INT)  BEGIN
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

CREATE PROCEDURE `sp_subject2Classlevels` (IN `LevelID` INT, `TermID` INT, `SubjectIDs` VARCHAR(225))  BEGIN
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

CREATE PROCEDURE `sp_subject2Classrooms` (IN `ClassID` INT, `TermID` INT, `SubjectIDs` VARCHAR(225))  BEGIN
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

CREATE PROCEDURE `sp_subject2Students` (IN `subjectClassroomID` INT)  BEGIN
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

CREATE PROCEDURE `sp_terminalClassPosition` (IN `AcademicTermID` INT, IN `ClassroomID` INT, IN `StudentID` INT)  BEGIN
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

CREATE PROCEDURE `temp_student_subjects` ()  BEGIN
	
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
CREATE FUNCTION `SPLIT_STR` (`x` VARCHAR(255), `delim` VARCHAR(12), `pos` INT) RETURNS VARCHAR(255) CHARSET latin1 RETURN REPLACE(SUBSTRING(SUBSTRING_INDEX(x, delim, pos),
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

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

CREATE TABLE `exams` (
  `exam_id` int(10) UNSIGNED NOT NULL,
  `subject_classroom_id` int(10) UNSIGNED NOT NULL,
  `marked` int(10) UNSIGNED NOT NULL DEFAULT '2'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
(1, 1);

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

-- --------------------------------------------------------

--
-- Table structure for table `student_subjects`
--

CREATE TABLE `student_subjects` (
  `student_id` int(10) UNSIGNED NOT NULL,
  `subject_classroom_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
(1, '$2y$10$r7i.xoOrQP6n0B5JQLtmCuaY.MvCuNoEsinb6ALaNjov4Ck2Nfnx.', '081617307881', 'admin@gmail.com', 'Emma', 'Okafor', '', 'Male', '2016-04-05', '', 1, 0, 1, 1, 1, '1_avatar.jpg', NULL, 'h198OPEXf49Lc8ZPVJ5xnLmvU4l7sYbleb5TewvCwuKqnxwsvDeRwKiPhgvN', NULL, '2016-08-19 15:14:21');

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

CREATE VIEW `assessment_detailsviews`  AS  select `f`.`assessment_id` AS `assessment_id`,`f`.`subject_classroom_id` AS `subject_classroom_id`,`f`.`assessment_setup_detail_id` AS `assessment_setup_detail_id`,`f`.`marked` AS `marked`,`g`.`assessment_detail_id` AS `assessment_detail_id`,`g`.`student_id` AS `student_id`,`j`.`student_no` AS `student_no`,concat(`j`.`first_name`,' ',`j`.`last_name`) AS `student_name`,`j`.`gender` AS `gender`,`g`.`score` AS `score`,`h`.`weight_point` AS `weight_point`,`h`.`number` AS `number`,`h`.`percentage` AS `percentage`,`h`.`description` AS `description`,`h`.`submission_date` AS `submission_date`,`i`.`assessment_setup_id` AS `assessment_setup_id`,`i`.`assessment_no` AS `assessment_no`,`m`.`ca_weight_point` AS `ca_weight_point`,`m`.`exam_weight_point` AS `exam_weight_point`,`j`.`sponsor_id` AS `sponsor_id`,`k`.`phone_no` AS `phone_no`,`k`.`email` AS `email`,concat(`k`.`first_name`,' ',`k`.`last_name`) AS `sponsor_name`,`a`.`subject_id` AS `subject_id`,`a`.`classroom_id` AS `classroom_id`,`a`.`tutor_id` AS `tutor_id`,concat(`n`.`first_name`,' ',`n`.`last_name`) AS `tutor`,`c`.`classroom` AS `classroom`,`c`.`classlevel_id` AS `classlevel_id`,`d`.`classlevel` AS `classlevel`,`d`.`classgroup_id` AS `classgroup_id`,`a`.`academic_term_id` AS `academic_term_id`,`e`.`academic_term` AS `academic_term` from (((((((((((`solid_steps`.`subject_classrooms` `a` join `solid_steps`.`classrooms` `c` on((`a`.`classroom_id` = `c`.`classroom_id`))) join `solid_steps`.`classlevels` `d` on((`c`.`classlevel_id` = `d`.`classlevel_id`))) join `solid_steps`.`academic_terms` `e` on((`a`.`academic_term_id` = `e`.`academic_term_id`))) join `solid_steps`.`assessments` `f` on((`a`.`subject_classroom_id` = `f`.`subject_classroom_id`))) join `solid_steps`.`assessment_details` `g` on((`f`.`assessment_id` = `g`.`assessment_id`))) join `solid_steps`.`assessment_setup_details` `h` on((`f`.`assessment_setup_detail_id` = `h`.`assessment_setup_detail_id`))) join `solid_steps`.`assessment_setups` `i` on((`h`.`assessment_setup_id` = `i`.`assessment_setup_id`))) join `solid_steps`.`students` `j` on((`g`.`student_id` = `j`.`student_id`))) left join `solid_steps`.`users` `k` on((`j`.`sponsor_id` = `k`.`user_id`))) join `solid_steps`.`classgroups` `m` on((`d`.`classgroup_id` = `m`.`classgroup_id`))) left join `solid_steps`.`users` `n` on((`a`.`tutor_id` = `n`.`user_id`))) ;

-- --------------------------------------------------------

--
-- Structure for view `exams_detailsviews`
--
DROP TABLE IF EXISTS `exams_detailsviews`;

CREATE VIEW `exams_detailsviews`  AS  select `solid_steps`.`exam_details`.`exam_detail_id` AS `exam_detail_id`,`solid_steps`.`exams`.`exam_id` AS `exam_id`,`solid_steps`.`subject_classrooms`.`subject_classroom_id` AS `subject_classroom_id`,`solid_steps`.`subject_classrooms`.`subject_id` AS `subject_id`,`solid_steps`.`subject_classrooms`.`tutor_id` AS `tutor_id`,`solid_steps`.`classrooms`.`classlevel_id` AS `classlevel_id`,`solid_steps`.`student_classes`.`classroom_id` AS `classroom_id`,`solid_steps`.`students`.`student_id` AS `student_id`,`solid_steps`.`classrooms`.`classroom` AS `classroom`,concat(ucase(`solid_steps`.`students`.`first_name`),' ',lcase(`solid_steps`.`students`.`last_name`)) AS `fullname`,`solid_steps`.`students`.`gender` AS `student_gender`,`solid_steps`.`students`.`student_no` AS `student_no`,`solid_steps`.`exam_details`.`ca` AS `ca`,`solid_steps`.`exam_details`.`exam` AS `exam`,(`solid_steps`.`exam_details`.`exam` + `solid_steps`.`exam_details`.`ca`) AS `student_total`,`solid_steps`.`classgroups`.`ca_weight_point` AS `ca_weight_point`,`solid_steps`.`classgroups`.`exam_weight_point` AS `exam_weight_point`,(`solid_steps`.`classgroups`.`exam_weight_point` + `solid_steps`.`classgroups`.`ca_weight_point`) AS `weight_point_total`,`solid_steps`.`academic_terms`.`academic_term_id` AS `academic_term_id`,`solid_steps`.`academic_terms`.`academic_term` AS `academic_term`,`solid_steps`.`exams`.`marked` AS `marked`,`solid_steps`.`academic_terms`.`academic_year_id` AS `academic_year_id`,`solid_steps`.`academic_years`.`academic_year` AS `academic_year`,`solid_steps`.`classlevels`.`classlevel` AS `classlevel`,`solid_steps`.`classlevels`.`classgroup_id` AS `classgroup_id` from (((((((((`solid_steps`.`exams` join `solid_steps`.`exam_details` on((`solid_steps`.`exams`.`exam_id` = `solid_steps`.`exam_details`.`exam_id`))) join `solid_steps`.`subject_classrooms` on((`solid_steps`.`exams`.`subject_classroom_id` = `solid_steps`.`subject_classrooms`.`subject_classroom_id`))) join `solid_steps`.`students` on((`solid_steps`.`exam_details`.`student_id` = `solid_steps`.`students`.`student_id`))) join `solid_steps`.`academic_terms` on((`solid_steps`.`subject_classrooms`.`academic_term_id` = `solid_steps`.`academic_terms`.`academic_term_id`))) join `solid_steps`.`academic_years` on((`solid_steps`.`academic_years`.`academic_year_id` = `solid_steps`.`academic_terms`.`academic_year_id`))) join `solid_steps`.`student_classes` on((`solid_steps`.`students`.`student_id` = `solid_steps`.`student_classes`.`student_id`))) join `solid_steps`.`classrooms` on((`solid_steps`.`student_classes`.`classroom_id` = `solid_steps`.`classrooms`.`classroom_id`))) join `solid_steps`.`classlevels` on((`solid_steps`.`classrooms`.`classlevel_id` = `solid_steps`.`classlevels`.`classlevel_id`))) join `solid_steps`.`classgroups` on((`solid_steps`.`classgroups`.`classgroup_id` = `solid_steps`.`classlevels`.`classgroup_id`))) ;

-- --------------------------------------------------------

--
-- Structure for view `exams_subjectviews`
--
DROP TABLE IF EXISTS `exams_subjectviews`;

CREATE VIEW `exams_subjectviews`  AS  select `a`.`exam_id` AS `exam_id`,`f`.`classroom_id` AS `classroom_id`,`f`.`classroom` AS `classroom`,`b`.`subject_id` AS `subject_id`,`a`.`subject_classroom_id` AS `subject_classroom_id`,`b`.`tutor_id` AS `tutor_id`,concat(ucase(`j`.`first_name`),' ',`j`.`last_name`) AS `tutor`,`h`.`ca_weight_point` AS `ca_weight_point`,`h`.`exam_weight_point` AS `exam_weight_point`,`a`.`marked` AS `marked`,`f`.`classlevel_id` AS `classlevel_id`,`g`.`classlevel` AS `classlevel`,`b`.`academic_term_id` AS `academic_term_id`,`d`.`academic_term` AS `academic_term`,`d`.`academic_year_id` AS `academic_year_id`,`e`.`academic_year` AS `academic_year` from ((((((`solid_steps`.`exams` `a` join `solid_steps`.`subject_classrooms` `b` on((`a`.`subject_classroom_id` = `b`.`subject_classroom_id`))) left join (`solid_steps`.`classlevels` `g` join `solid_steps`.`classrooms` `f` on((`f`.`classlevel_id` = `g`.`classlevel_id`))) on((`b`.`classroom_id` = `f`.`classroom_id`))) join `solid_steps`.`academic_terms` `d` on((`b`.`academic_term_id` = `d`.`academic_term_id`))) join `solid_steps`.`academic_years` `e` on((`d`.`academic_year_id` = `e`.`academic_year_id`))) join `solid_steps`.`classgroups` `h` on((`g`.`classgroup_id` = `h`.`classgroup_id`))) left join `solid_steps`.`users` `j` on((`b`.`tutor_id` = `j`.`user_id`))) ;

-- --------------------------------------------------------

--
-- Structure for view `students_classroomviews`
--
DROP TABLE IF EXISTS `students_classroomviews`;

CREATE VIEW `students_classroomviews`  AS  select concat(ucase(`solid_steps`.`students`.`first_name`),' ',`solid_steps`.`students`.`last_name`) AS `fullname`,`solid_steps`.`students`.`student_no` AS `student_no`,`solid_steps`.`classrooms`.`classroom` AS `classroom`,`solid_steps`.`classrooms`.`classroom_id` AS `classroom_id`,`solid_steps`.`students`.`student_id` AS `student_id`,`solid_steps`.`classlevels`.`classlevel` AS `classlevel`,`solid_steps`.`classrooms`.`classlevel_id` AS `classlevel_id`,`solid_steps`.`students`.`sponsor_id` AS `sponsor_id`,concat(ucase(`solid_steps`.`users`.`first_name`),' ',`solid_steps`.`users`.`last_name`) AS `sponsor_name`,`solid_steps`.`student_classes`.`academic_year_id` AS `academic_year_id`,`solid_steps`.`academic_years`.`academic_year` AS `academic_year`,`solid_steps`.`students`.`status_id` AS `status_id` from (((((`solid_steps`.`students` join `solid_steps`.`student_classes` on((`solid_steps`.`student_classes`.`student_id` = `solid_steps`.`students`.`student_id`))) join `solid_steps`.`classrooms` on((`solid_steps`.`student_classes`.`classroom_id` = `solid_steps`.`classrooms`.`classroom_id`))) join `solid_steps`.`classlevels` on((`solid_steps`.`classlevels`.`classlevel_id` = `solid_steps`.`classrooms`.`classlevel_id`))) join `solid_steps`.`academic_years` on((`solid_steps`.`student_classes`.`academic_year_id` = `solid_steps`.`academic_years`.`academic_year_id`))) join `solid_steps`.`users` on((`solid_steps`.`students`.`sponsor_id` = `solid_steps`.`users`.`user_id`))) ;

-- --------------------------------------------------------

--
-- Structure for view `subjects_assessmentsviews`
--
DROP TABLE IF EXISTS `subjects_assessmentsviews`;

CREATE VIEW `subjects_assessmentsviews`  AS  select `a`.`tutor` AS `tutor`,`a`.`tutor_id` AS `tutor_id`,`a`.`classroom_id` AS `classroom_id`,`a`.`subject_classroom_id` AS `subject_classroom_id`,`a`.`subject_id` AS `subject_id`,`a`.`subject` AS `subject`,`a`.`subject_group_id` AS `subject_group_id`,`a`.`academic_term_id` AS `academic_term_id`,`a`.`academic_term` AS `academic_term`,`a`.`exam_status_id` AS `exam_status_id`,`a`.`exam_status` AS `exam_status`,`a`.`classlevel_id` AS `classlevel_id`,`a`.`classroom` AS `classroom`,`b`.`assessment_id` AS `assessment_id`,`b`.`marked` AS `marked`,`c`.`assessment_setup_detail_id` AS `assessment_setup_detail_id`,`c`.`number` AS `number`,`c`.`weight_point` AS `weight_point`,`c`.`percentage` AS `percentage`,`c`.`assessment_setup_id` AS `assessment_setup_id`,`c`.`submission_date` AS `submission_date`,`c`.`description` AS `description` from ((`solid_steps`.`subjects_classroomviews` `a` left join `solid_steps`.`assessments` `b` on((`a`.`subject_classroom_id` = `b`.`subject_classroom_id`))) left join `solid_steps`.`assessment_setup_details` `c` on((`b`.`assessment_setup_detail_id` = `c`.`assessment_setup_detail_id`))) ;

-- --------------------------------------------------------

--
-- Structure for view `subjects_classroomviews`
--
DROP TABLE IF EXISTS `subjects_classroomviews`;

CREATE VIEW `subjects_classroomviews`  AS  select concat(ucase(`e`.`first_name`),' ',`e`.`last_name`) AS `tutor`,`e`.`user_id` AS `tutor_id`,`a`.`classroom_id` AS `classroom_id`,`a`.`subject_classroom_id` AS `subject_classroom_id`,`a`.`subject_id` AS `subject_id`,`d`.`subject` AS `subject`,`d`.`subject_group_id` AS `subject_group_id`,`a`.`academic_term_id` AS `academic_term_id`,`b`.`academic_term` AS `academic_term`,`a`.`exam_status_id` AS `exam_status_id`,(case `a`.`exam_status_id` when 1 then 'Marked' when 2 then 'Not Marked' end) AS `exam_status`,`c`.`classlevel_id` AS `classlevel_id`,`c`.`classroom` AS `classroom` from ((((`solid_steps`.`subject_classrooms` `a` join `solid_steps`.`academic_terms` `b` on((`a`.`academic_term_id` = `b`.`academic_term_id`))) join `solid_steps`.`classrooms` `c` on((`a`.`classroom_id` = `c`.`classroom_id`))) join `solidsteps_schools`.`subjects` `d` on((`d`.`subject_id` = `a`.`subject_id`))) left join `solid_steps`.`users` `e` on((`a`.`tutor_id` = `e`.`user_id`))) ;

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
  MODIFY `academic_term_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `academic_years`
--
ALTER TABLE `academic_years`
  MODIFY `academic_year_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `assessments`
--
ALTER TABLE `assessments`
  MODIFY `assessment_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `assessment_details`
--
ALTER TABLE `assessment_details`
  MODIFY `assessment_detail_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `assessment_setups`
--
ALTER TABLE `assessment_setups`
  MODIFY `assessment_setup_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `assessment_setup_details`
--
ALTER TABLE `assessment_setup_details`
  MODIFY `assessment_setup_detail_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `classgroups`
--
ALTER TABLE `classgroups`
  MODIFY `classgroup_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `classlevels`
--
ALTER TABLE `classlevels`
  MODIFY `classlevel_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `classrooms`
--
ALTER TABLE `classrooms`
  MODIFY `classroom_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `class_masters`
--
ALTER TABLE `class_masters`
  MODIFY `class_master_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `domains`
--
ALTER TABLE `domains`
  MODIFY `domain_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `domain_assessments`
--
ALTER TABLE `domain_assessments`
  MODIFY `domain_assessment_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `domain_details`
--
ALTER TABLE `domain_details`
  MODIFY `domain_detail_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `exams`
--
ALTER TABLE `exams`
  MODIFY `exam_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `exam_details`
--
ALTER TABLE `exam_details`
  MODIFY `exam_detail_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
  MODIFY `grade_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
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
  MODIFY `remark_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
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
  MODIFY `student_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `student_classes`
--
ALTER TABLE `student_classes`
  MODIFY `student_class_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `subject_classrooms`
--
ALTER TABLE `subject_classrooms`
  MODIFY `subject_classroom_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
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
  MODIFY `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
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
  ADD CONSTRAINT `subject_classrooms_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `solidsteps_schools`.`subjects` (`subject_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `subject_classrooms_tutor_id_foreign` FOREIGN KEY (`tutor_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
