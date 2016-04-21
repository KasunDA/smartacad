-- phpMyAdmin SQL Dump
-- version 4.4.14
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 21, 2016 at 11:51 PM
-- Server version: 5.6.26
-- PHP Version: 5.6.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `schools`
--
DROP DATABASE `schools`;
CREATE DATABASE IF NOT EXISTS `schools` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `schools`;

DELIMITER $$
--
-- Procedures
--
DROP PROCEDURE IF EXISTS `proc_annualClassPositionViews`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `proc_annualClassPositionViews`(IN `ClassID` INT, IN `AcademicYearID` INT)
BEGIN
#Create a Temporary Table to Hold The Values
		DROP TEMPORARY TABLE IF EXISTS AnnualClassPositionResultTable;
		CREATE TEMPORARY TABLE IF NOT EXISTS AnnualClassPositionResultTable
		(
-- Add the column definitions for the TABLE variable here
			row_id int AUTO_INCREMENT,
			student_id INT,
			full_name VARCHAR(100),
			class_id INT,
			class_name VARCHAR(50),
			academic_year_id int,
			academic_year varchar(80),
			student_annual_total_score Decimal(6, 2),
			exam_annual_perfect_score Decimal(6, 2),
			class_annual_position int,
			class_size int, PRIMARY KEY (row_id)
		);

-- cursor block for calculating the students annual exam total scores
			Block1: BEGIN
			DECLARE done1 BOOLEAN DEFAULT FALSE;
			DECLARE StudentID, ClassRoomID, YearID INT;
			DECLARE StudentName VARCHAR(150);
			DECLARE ClassName, YearName VARCHAR(50);
			DECLARE cur1 CURSOR FOR SELECT student_id, student_name, class_id, class_name, academic_year_id,academic_year
															FROM students_classlevelviews WHERE class_id=ClassID AND academic_year_id=AcademicYearID
															GROUP BY student_id, student_name, class_id, class_name, academic_year_id,academic_year;
			DECLARE CONTINUE HANDLER FOR NOT FOUND SET done1 = TRUE;

#Open The Cursor For Iterating Through The Recordset cur1
			OPEN cur1;
			REPEAT
				FETCH cur1 INTO StudentID, StudentName, ClassRoomID, ClassName, YearID, YearName;
				IF NOT done1 THEN
					BEGIN
-- Function Call to the records
						SET @Res = (SELECT func_annualExamsViews(StudentID, AcademicYearID));

						IF @Res > 0 THEN
							BEGIN
								INSERT INTO AnnualClassPositionResultTable(student_id, full_name, class_id, class_name, academic_year_id, academic_year, student_annual_total_score, exam_annual_perfect_score)
									SELECT StudentID, StudentName, ClassRoomID, ClassName, YearID, YearName, CAST(SUM(annual_average) AS Decimal(6, 2)), (COUNT(annual_average) * 100)
									FROM AnnualSubjectViewsResultTable;
							END;
						END IF;
					END;
				END IF;
			UNTIL done1 END REPEAT;
			CLOSE cur1;
		END Block1;

-- cursor block for calculating the students annual class Position
			Block2: BEGIN
-- Get the number of students in the class
			SET @ClassSize = (SELECT COUNT(*) FROM students_classlevelviews
			WHERE class_id = ClassID AND academic_year_id = AcademicYearID
			);
			SET @TempPosition = 1;
			SET @TempStudentScore = 0;
			SET @Position = 0;

				Block3: BEGIN
				DECLARE done2 BOOLEAN DEFAULT FALSE;
				DECLARE RowID INT;
				DECLARE StudentAnnualTotal Decimal(6, 2);
				DECLARE cur2 CURSOR FOR SELECT row_id, student_annual_total_score
																FROM AnnualClassPositionResultTable WHERE class_id=ClassID AND academic_year_id=AcademicYearID
																GROUP BY row_id, student_annual_total_score
																ORDER BY student_annual_total_score DESC;
				DECLARE CONTINUE HANDLER FOR NOT FOUND SET done2 = TRUE;

#Open The Cursor For Iterating Through The Recordset cur1
				OPEN cur2;
				REPEAT
					FETCH cur2 INTO RowID, StudentAnnualTotal;
					IF NOT done2 THEN
						BEGIN
-- IF the current student total is equal to the next student's total
							IF @TempStudentScore = StudentAnnualTotal THEN
-- Add one to the temp variable position
								SET @TempPosition = @TempPosition + 1;
-- Else if they are not equal
							ELSE
								BEGIN
-- Set the current student's position to be that of the temp variable
									SET @Position = @TempPosition;
-- Add one to the temp variable position
									SET @TempPosition = @TempPosition + 1;
								END;
							END IF;
							BEGIN
-- update the resultant table that will display the computed class position results
								UPDATE AnnualClassPositionResultTable SET class_annual_position=@Position, class_size=@ClassSize
								WHERE row_id=RowID;
							END;
-- Get the current student total score and set it the variable for the next comparism
							SET @TempStudentScore = StudentAnnualTotal;
						END;
					END IF;
				UNTIL done2 END REPEAT;
				CLOSE cur2;
			END Block3;
		END Block2;
	END$$

DROP PROCEDURE IF EXISTS `proc_assignSubject2Classlevels`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `proc_assignSubject2Classlevels`(IN `LevelID` INT, `TermID` INT, `SubjectIDs` VARCHAR(225))
BEGIN
		DECLARE done1 BOOLEAN DEFAULT FALSE;
		DECLARE ClassID INT;
		DECLARE cur1 CURSOR FOR SELECT class_id FROM classrooms WHERE classlevel_id=LevelID;
		DECLARE CONTINUE HANDLER FOR NOT FOUND SET done1 = TRUE;

#Open The Cursor For Iterating Through The Recordset cur1
		OPEN cur1;
		REPEAT
			FETCH cur1 INTO ClassID;
			IF NOT done1 THEN
				BEGIN
-- Procedure Call -- To register the subjects to the students in that classroom
					CALL `proc_assignSubject2Classrooms`(ClassID, LevelID, TermID, SubjectIDs);
				END;
			END IF;
		UNTIL done1 END REPEAT;
		CLOSE cur1;
	END$$

DROP PROCEDURE IF EXISTS `proc_assignSubject2Classrooms`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `proc_assignSubject2Classrooms`(IN `ClassID` INT, `LevelID` INT, `TermID` INT, `SubjectIDs` VARCHAR(225))
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
			DELETE FROM subject_students_registers WHERE subject_classlevel_id IN
																									 (
																										 SELECT subject_classlevel_id FROM subject_classlevels WHERE class_id=ClassID AND classlevel_id=LevelID AND academic_term_id=TermID AND subject_id
																																																																																														NOT IN (SELECT subject_id FROM SubjectTemp)
																									 );

			DELETE FROM subject_classlevels WHERE class_id=ClassID AND classlevel_id=LevelID AND academic_term_id=TermID AND subject_id
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
							SET @Exist = (SELECT COUNT(*) FROM subject_classlevels WHERE subject_id=SubjectID AND class_id=ClassID AND classlevel_id=LevelID AND academic_term_id=TermID);
							IF @Exist = 0 THEN
								BEGIN
# Insert into subject classlevel those newly assigned subjects
									INSERT INTO subject_classlevels(subject_id, classlevel_id, class_id, academic_term_id)
									VALUES(SubjectID, LevelID, ClassID, TermID);

-- Procedure Call -- To register the subjects to the students in that classroom
									CALL proc_assignSubject2Students(LAST_INSERT_ID());
								END;
							END IF;
						END;
					END IF;
				UNTIL done1 END REPEAT;
				CLOSE cur1;
			END Block2;

-- Delete the teachers_subjects record that has no id in subjects classlevel table
			DELETE FROM teachers_subjects WHERE subject_classlevel_id
																					NOT IN (SELECT subject_classlevel_id FROM subject_classlevels);
		END Block1;
	END$$

DROP PROCEDURE IF EXISTS `proc_assignSubject2Students`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `proc_assignSubject2Students`(IN `subjectClasslevelID` INT)
BEGIN
		SELECT classlevel_id, class_id, academic_term_id
		INTO @ClassLevelID, @ClassID, @AcademicTermID
		FROM subject_classlevels WHERE subject_classlevel_id=subjectClasslevelID LIMIT 1;
		SET @SubjectClasslevelID = subjectClasslevelID;


		SELECT COUNT(*) INTO @Exist FROM subject_students_registers WHERE subject_classlevel_id=subjectClasslevelID LIMIT 1;
		IF @Exist > 0 THEN
			BEGIN
				DELETE FROM subject_students_registers WHERE subject_classlevel_id=subjectClasslevelID;
			END;
		END IF;


		IF @ClassID IS NULL OR @ClassID = -1 THEN
			BEGIN
				INSERT INTO subject_students_registers(student_id, class_id, subject_classlevel_id)
					SELECT	b.student_id, b.class_id, @SubjectClasslevelID
					FROM	students a INNER JOIN students_classes b ON a.student_id=b.student_id INNER JOIN
						classrooms c ON c.class_id = b.class_id
					WHERE 	c.classlevel_id = @ClassLevelID  AND a.student_status_id = 1
								 AND 	b.academic_year_id = (SELECT academic_year_id FROM academic_terms WHERE academic_term_id = @AcademicTermID LIMIT 1);
			END;
		ELSE
			BEGIN
				INSERT INTO subject_students_registers(student_id, class_id, subject_classlevel_id)
					SELECT	b.student_id, b.class_id, @SubjectClasslevelID
					FROM	students a INNER JOIN students_classes b ON a.student_id=b.student_id INNER JOIN
						classrooms c ON c.class_id = b.class_id
					WHERE	b.class_id = @ClassID AND a.student_status_id = 1
								 AND 	b.academic_year_id = (SELECT academic_year_id FROM academic_terms WHERE academic_term_id = @AcademicTermID LIMIT 1);
			END;
		END IF;

	END$$

DROP PROCEDURE IF EXISTS `proc_cloneSubjectsAssigned`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `proc_cloneSubjectsAssigned`(IN `TermFromID` INT, IN `TermToID` INT)
BEGIN
-- Check to see if records already exist in subject classlevel table for the TermToID academic term
		SET @Exist = (SELECT COUNT(*) FROM subject_classlevels WHERE academic_term_id=TermToID);

		IF @Exist = 0 THEN
				Block1: BEGIN
				DECLARE done1 BOOLEAN DEFAULT FALSE;
				DECLARE SubClassLevlID, SubjectID, LevelID, ClassID INT;
				DECLARE cur1 CURSOR FOR
					SELECT subject_classlevel_id, subject_id, classlevel_id, class_id FROM subject_classlevels
					WHERE academic_term_id=TermFromID ORDER BY subject_classlevel_id;
				DECLARE CONTINUE HANDLER FOR NOT FOUND SET done1 = TRUE;

#Open The Cursor For Iterating Through The Recordset cur1
				OPEN cur1;
				REPEAT
					FETCH cur1 INTO SubClassLevlID, SubjectID, LevelID, ClassID;
					IF NOT done1 THEN
						BEGIN
-- Test to see if the record does not exist before inserting
							SET @chk = (SELECT COUNT(*) FROM subject_classlevels WHERE subject_id=SubjectID AND class_id=ClassID AND classlevel_id=LevelID AND academic_term_id=TermToID);
							IF @chk = 0 THEN
								BEGIN
-- Get the teacher for that subject from the TermFromID academic term
									SET @EmployeeID = (SELECT employee_id FROM teachers_subjects WHERE subject_classlevel_id=SubClassLevlID AND class_id=ClassID LIMIT 1);

# Insert into subject classlevel those newly assigned subjects
									INSERT INTO subject_classlevels(subject_id, classlevel_id, class_id, academic_term_id)
									VALUES(SubjectID, LevelID, ClassID, TermToID);

-- Get the newly inserted subject classlevel id
									SET @New_ID = LAST_INSERT_ID();

-- insert into teachers subjects table with the new id
									INSERT INTO teachers_subjects(employee_id, class_id, subject_classlevel_id)
									VALUES(@EmployeeID, ClassID, @New_ID);

-- Procedure Call -- To register the subjects to the students in that classroom
									CALL proc_assignSubject2Students(@New_ID);
								END;
							END IF;
						END;
					END IF;
				UNTIL done1 END REPEAT;
				CLOSE cur1;
			END Block1;
		END IF;
	END$$

DROP PROCEDURE IF EXISTS `proc_examsDetailsReportViews`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `proc_examsDetailsReportViews`(IN `AcademicID` INT, IN `TypeID` INT)
BEGIN
-- Create Temporary Table
		DROP TEMPORARY TABLE IF EXISTS ExamsDetailsResultTable;
		CREATE TEMPORARY TABLE IF NOT EXISTS ExamsDetailsResultTable
		(
-- Add the column definitions for the TABLE variable here
			row_id int AUTO_INCREMENT, exam_detail_id INT, exam_id int, subject_classlevel_id int, subject_id int, classlevel_id int,
			class_id int, student_id int, subject_name varchar(80), class_name varchar(80), student_fullname varchar(180),
			ca int, exam int, ca_weight_point int, exam_weight_point int,
			academic_term_id int, academic_term varchar(80), exammarked_status_id int, academic_year_id int,
			academic_year varchar(80), classlevel varchar(80), classgroup_id int,
			studentSubjectTotal Decimal(6, 2), studentPercentTotal Decimal(6, 2), weightageTotal Decimal(6, 2), grade varchar(20),
			grade_abbr varchar(5), student_sum_total Decimal(6, 2), exam_perfect_score int, PRIMARY KEY (row_id)
		);

-- TypeID values 1 for term while others for year
		IF TypeID = 1 THEN
-- Insert Into the temporary table
			INSERT INTO ExamsDetailsResultTable(exam_detail_id, exam_id, subject_classlevel_id, subject_id, classlevel_id,
																					class_id, student_id, subject_name, class_name, student_fullname, ca, exam, ca_weight_point, exam_weight_point,
																					academic_term_id, academic_term, exammarked_status_id, academic_year_id, academic_year, classlevel, classgroup_id)
				SELECT * FROM examsdetails_reportviews
				WHERE exammarked_status_id=1 AND academic_term_id=AcademicID;
		ELSE
-- Insert Into the temporary table
			INSERT INTO ExamsDetailsResultTable(exam_detail_id, exam_id, subject_classlevel_id, subject_id, classlevel_id,
																					class_id, student_id, subject_name, class_name, student_fullname, ca, exam, ca_weight_point, exam_weight_point,
																					academic_term_id, academic_term, exammarked_status_id, academic_year_id, academic_year, classlevel, classgroup_id)
				SELECT * FROM examsdetails_reportviews
				WHERE exammarked_status_id=1 AND academic_term_id IN
																				 (SELECT academic_term_id FROM academic_terms WHERE academic_year_id=AcademicID);
		END IF;
-- cursor block for calculating the students exam total scores
			Block1: BEGIN
			DECLARE done1 BOOLEAN DEFAULT FALSE;
			DECLARE RowID, StudentID, SubjectID, TermID INT;
			DECLARE cur1 CURSOR FOR SELECT row_id, student_id, subject_id, academic_term_id
															FROM ExamsDetailsResultTable;
			DECLARE CONTINUE HANDLER FOR NOT FOUND SET done1 = TRUE;

#Open The Cursor For Iterating Through The Recordset cur1
			OPEN cur1;
			REPEAT
				FETCH cur1 INTO RowID, StudentID, SubjectID, TermID;
				IF NOT done1 THEN
					BEGIN
						SELECT CAST((ca + exam) AS Decimal(6, 2)), CAST((((ca + exam) / (ca_weight_point + exam_weight_point)) * 100)
																														AS Decimal(6, 2)), CAST((ca_weight_point + exam_weight_point) AS Decimal(6, 2)) INTO @StudentSubjectTotal,  @StudentPercentTotal, @WeightageTotal
						FROM exam_details INNER JOIN exams ON exam_details.exam_id = exams.exam_id INNER JOIN
							subject_classlevels ON exams.subject_classlevel_id = subject_classlevels.subject_classlevel_id INNER JOIN
							classlevels ON subject_classlevels.classlevel_id = classlevels.classlevel_id INNER JOIN
							classgroups ON classlevels.classgroup_id = classgroups.classgroup_id
						WHERE  exam_details.student_id = StudentID AND subject_classlevels.subject_id=SubjectID AND subject_classlevels.academic_term_id = TermID
						GROUP BY exam_details.student_id;
-- update the temporary table with the new calculated values
						BEGIN
							UPDATE ExamsDetailsResultTable SET
								studentSubjectTotal=@StudentSubjectTotal, studentPercentTotal=@StudentPercentTotal, weightageTotal=@WeightageTotal
							WHERE row_id=RowID AND student_id = StudentID AND subject_id=SubjectID AND academic_term_id = TermID;
						END;
					END;
				END IF;
			UNTIL done1 END REPEAT;
			CLOSE cur1;
		END Block1;

-- cursor for calculating the students grade base on the scores
			Block2: BEGIN
			DECLARE done2 BOOLEAN DEFAULT FALSE;
			DECLARE RowID, StudentID, SubjectID, TermID, ClassGroupID INT;
			DECLARE StudentPercentT Decimal(6, 2);
			DECLARE cur2 CURSOR FOR SELECT row_id, student_id, subject_id, academic_term_id, studentPercentTotal, classgroup_id
															FROM ExamsDetailsResultTable;
			DECLARE CONTINUE HANDLER FOR NOT FOUND SET done2 = TRUE;

#Open The Cursor For Iterating Through The Recordset cur1
			OPEN cur2;
			REPEAT
				FETCH cur2 INTO RowID, StudentID, SubjectID, TermID, StudentPercentT, ClassGroupID;
				IF NOT done2 THEN
					BEGIN
						SELECT grade, grade_abbr INTO @Grade, @GradeAbbr FROM grades
						WHERE CEIL(StudentPercentT) BETWEEN lower_bound AND upper_bound AND classgroup_id = ClassGroupID;

						SELECT CAST(SUM(studentPercentTotal)AS Decimal(6, 2)), (COUNT(studentPercentTotal) * 100) INTO @StudentSumTotal, @ExamPerfectScore
						FROM ExamsDetailsResultTable WHERE student_id = StudentID AND academic_term_id = TermID GROUP BY student_id;
-- update the temporary table with the calculated values
						BEGIN
							UPDATE ExamsDetailsResultTable SET grade=@Grade, grade_abbr=@GradeAbbr,
								student_sum_total=@StudentSumTotal, exam_perfect_score=@ExamPerfectScore
							WHERE row_id=RowID AND student_id = StudentID AND subject_id=SubjectID AND academic_term_id = TermID;
						END;
					END;
				END IF;
			UNTIL done2 END REPEAT;
			CLOSE cur2;
		END Block2;
	END$$

DROP PROCEDURE IF EXISTS `proc_insertAttendDetails`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `proc_insertAttendDetails`(IN `AttendID` INT, `StudentIDS` VARCHAR(225))
BEGIN
# Delete The Record if it exists
		SELECT COUNT(*) INTO @Exist FROM attend_details WHERE attend_id=AttendID;
		IF @Exist > 0 THEN
			BEGIN
				DELETE FROM attend_details WHERE attend_id=AttendID;
			END;
		END IF;

		IF StudentIDS IS NOT NULL THEN
			BEGIN
				DECLARE count INT Default 0 ;
				DECLARE student_id VARCHAR(255);
				simple_loop: LOOP
					SET count = count + 1;
					SET student_id = SPLIT_STR(StudentIDS, ',', count);
					IF student_id = '' THEN
						LEAVE simple_loop;
					END IF;
# Insert into the attend details table those present
					INSERT INTO attend_details(attend_id, student_id)
						SELECT AttendID, student_id;
				END LOOP simple_loop;
			END;
		END IF;
	END$$

DROP PROCEDURE IF EXISTS `proc_insertWeeklyReportDetail`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `proc_insertWeeklyReportDetail`(IN `WeeklyReportID` INT)
BEGIN
# Delete The Record if it exists
		SELECT weekly_detail_setup_id, subject_classlevel_id, marked_status, notification_status
		INTO @WDS_ID, @SCL_ID, @MStatus, @NStatus
		FROM weekly_reports WHERE weekly_report_id=WeeklyReportID;

# Check if the weekly report has been marked before
		IF @NStatus = 2 THEN
			BEGIN
# Insert into the weekly reports details table the students
				INSERT INTO weekly_report_details(weekly_report_id, student_id)
					SELECT WeeklyReportID, student_id FROM subject_students_registers WHERE subject_classlevel_id=@SCL_ID
																																									AND student_id NOT IN (SELECT student_id FROM weekly_report_details WHERE weekly_report_id=WeeklyReportID);

# remove the students that was just removed from the list of students to offer the subject
				DELETE FROM weekly_report_details WHERE weekly_report_id=WeeklyReportID AND student_id NOT IN
																																										(SELECT student_id FROM subject_students_registers WHERE subject_classlevel_id=@SCL_ID);
			END;
		END IF;
	END$$

DROP PROCEDURE IF EXISTS `proc_processExams`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `proc_processExams`(IN `TermID` INT)
BEGIN
			Block0: BEGIN
-- Delete the exams details record for that term if its has not been marked already
			DELETE FROM exam_details WHERE exam_id IN
																		 (SELECT exam_id FROM exam_subjectviews WHERE academic_term_id=TermID AND exammarked_status_id=2);

-- Delete the exams record for that term if its has not been marked already
			DELETE FROM exams WHERE exammarked_status_id=2 AND subject_classlevel_id IN
																												 (SELECT subject_classlevel_id FROM subject_classlevels WHERE academic_term_id=TermID);
		END Block0;

			Block1: BEGIN
-- Insert into exams table with all the subjects that has assigned to a class room with students offering them
-- also skip those records that exist already to avoid duplicates in terms of class_id and subject_classlevel_id
			INSERT INTO exams(class_id, subject_classlevel_id)
				SELECT a.class_id, a.subject_classlevel_id FROM classroom_subjectregisterviews a
				WHERE a.academic_term_id=TermID AND a.class_id NOT IN
																						(SELECT class_id FROM exams b WHERE academic_term_id=TermID AND b.subject_classlevel_id = a.subject_classlevel_id);

-- Update the exam setup status_id = 1
			UPDATE subject_classlevels set examstatus_id=1;
		END Block1;

-- insert into exams details the students offering such subjects in the class room using the exams assigned
-- cursor block for inserting exam details from exams and subject_students_registers
			Block2: BEGIN
			DECLARE done1 BOOLEAN DEFAULT FALSE;
			DECLARE ExamID, ClassID, SubjectClasslevelID, ExamMarkStatusID INT;
-- DECLARE cur1 CURSOR FOR SELECT a.exam_id, a.class_id, a.subject_classlevel_id, a.exammarked_status_id
			DECLARE cur1 CURSOR FOR SELECT a.*
															FROM exams a INNER JOIN subject_classlevels b ON a.subject_classlevel_id=b.subject_classlevel_id
															WHERE b.academic_term_id=TermID;
			DECLARE CONTINUE HANDLER FOR NOT FOUND SET done1 = TRUE;

#Open The Cursor For Iterating Through The Recordset cur1
			OPEN cur1;
			REPEAT
				FETCH cur1 INTO ExamID, ClassID, SubjectClasslevelID, ExamMarkStatusID;
				IF NOT done1 THEN
					BEGIN
						IF ExamMarkStatusID = 2 THEN
							BEGIN
# Insert into the details table all the students that registered the subject
								INSERT INTO exam_details(exam_id, student_id)
									SELECT	ExamID, student_id
									FROM	subject_students_registers
									WHERE 	class_id=ClassID AND subject_classlevel_id=SubjectClasslevelID;
							END;
						ELSE
							BEGIN
# Insert into the details table the students that was just added to offer the subject
								INSERT INTO exam_details(exam_id, student_id)
									SELECT	ExamID, student_id
									FROM 	subject_students_registers
									WHERE 	class_id=ClassID AND subject_classlevel_id=SubjectClasslevelID AND student_id NOT IN
																																														(SELECT student_id FROM exam_details WHERE exam_id=ExamID);

# remove the students that was just removed from the list of students to offer the subject
								DELETE FROM exam_details WHERE exam_id=ExamID AND student_id NOT IN
																																	(
																																		SELECT student_id FROM subject_students_registers
																																		WHERE class_id=ClassID AND subject_classlevel_id=SubjectClasslevelID
																																	);
							END;
						END IF;
					END;
				END IF;
			UNTIL done1 END REPEAT;
			CLOSE cur1;
		END Block2;

-- Update the C.A with the calculated values from the weekly reports
		call proc_processWeeklyReportCA(TermID);

	END$$

DROP PROCEDURE IF EXISTS `proc_processItemVariable`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `proc_processItemVariable`(IN `ItemVariableID` INT)
BEGIN
		SELECT item_id, student_id, class_id, academic_term_id, price
		INTO @ItemID, @StudentID, @ClassID, @AcademicTermID, @Price
		FROM item_variables WHERE item_variable_id = ItemVariableID LIMIT 1;

		SET @SponsorID = (SELECT sponsor_id FROM students WHERE student_id=@StudentID);
		SET @AcademicYearID = (SELECT academic_year_id FROM academic_terms WHERE academic_term_id=@AcademicTermID LIMIT 1);

			Block1: BEGIN
			IF @StudentID IS NOT NULL THEN
				BEGIN
					INSERT INTO orders(student_id, sponsor_id, academic_term_id)
					VALUES (@StudentID, @SponsorID, @AcademicTermID);

					SET @OrderID = (SELECT MAX(order_id) FROM orders LIMIT 1);

					INSERT INTO order_items(order_id, item_id, price)
					VALUES (@OrderID, @ItemID, @Price);
				END;
			ELSE
					Block2: BEGIN
-- Declare Variable to be used in looping through the recordset or cursor
					DECLARE done1 BOOLEAN DEFAULT FALSE;
					DECLARE StudentID, SponsorID INT;

-- Populate the cursor with the values in a record i want to iterate through
					DECLARE cur1 CURSOR FOR
						SELECT student_id, sponsor_id
						FROM students_classlevelviews
						WHERE student_status_id=1 AND class_id=@ClassID AND academic_year_id=@AcademicYearID;

					DECLARE CONTINUE HANDLER FOR NOT FOUND SET done1 = TRUE;
#Open The Cursor For Iterating Through The Recordset cur1
					OPEN cur1;
					REPEAT
						FETCH cur1 INTO StudentID, SponsorID;
						IF NOT done1 THEN
							BEGIN
								INSERT INTO orders(student_id, sponsor_id, academic_term_id)
									SELECT StudentID, SponsorID, @AcademicTermID;

								SET @OrderID = (SELECT MAX(order_id) FROM orders LIMIT 1);

								INSERT INTO order_items(order_id, item_id, price)
									SELECT @OrderID, @ItemID, @Price;
							END;
						END IF;
					UNTIL done1 END REPEAT;
					CLOSE cur1;
				END Block2;
			END IF;
		END Block1;
	END$$

DROP PROCEDURE IF EXISTS `proc_processTerminalFees`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `proc_processTerminalFees`(IN `ProcessID` INT)
BEGIN
		SELECT academic_term_id
		INTO @AcademicTermID
		FROM process_items WHERE process_item_id = ProcessID LIMIT 1;
		SET @TermTypeID = (SELECT term_type_id FROM academic_terms WHERE academic_term_id=@AcademicTermID);

			Block1: BEGIN
			INSERT INTO orders(student_id, sponsor_id, academic_term_id, process_item_id)
				SELECT student_id, sponsor_id, @AcademicTermID, ProcessID
				FROM students_classlevelviews WHERE student_status_id=1;

			if @TermTypeID = 1 THEN
				BEGIN
					INSERT INTO order_items(order_id, item_id, price)
						SELECT order_id, item_id, price FROM student_feesqueryviews
						WHERE process_item_id=ProcessID AND item_type_id <> 3 AND item_status_id=1;
				END;
			ELSEIF @TermTypeID = 3 THEN
				BEGIN
					INSERT INTO order_items(order_id, item_id, price)
						SELECT order_id, item_id, price FROM student_feesqueryviews
						WHERE process_item_id=ProcessID AND item_type_id <> 2 AND item_status_id=1;
				END;
			ELSE
				BEGIN
					INSERT INTO order_items(order_id, item_id, price)
						SELECT order_id, item_id, price FROM student_feesqueryviews
						WHERE process_item_id=ProcessID AND item_type_id = 1 AND item_status_id=1;
				END;
			END IF;
		END Block1;
	END$$

DROP PROCEDURE IF EXISTS `proc_processWeeklyReportCA`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `proc_processWeeklyReportCA`(IN `TermID` INT)
Block0: BEGIN
#Create a Temporary Table to Hold The Values for manupulations
/*DROP TEMPORARY TABLE IF EXISTS SubjectCAResultTable;
CREATE TEMPORARY TABLE IF NOT EXISTS SubjectCAResultTable
(
  -- Add the column definitions for the TABLE variable here
  row_id INT AUTO_INCREMENT,
  student_id INT,
      student_name VARCHAR(100),
      class_id INT,
  subject_id INT,
      subject_name VARCHAR(100),
  subject_classlevel_id INT,
  academic_term_id INT,
      calculated_ca DECIMAL(4,2),
  calculated_wp DECIMAL(4,1),
  PRIMARY KEY(row_id)
);*/

			Block1: BEGIN
-- Declare Variable to be used in looping through the recordset or cursor
			DECLARE done1 BOOLEAN DEFAULT FALSE;
			DECLARE StudentID, ClassID, CA_WP INT;
			DECLARE StudentName VARCHAR(100);

			DECLARE cur1 CURSOR FOR
				SELECT student_id, class_id, ca_weight_point, student_name FROM weeklyreport_studentdetailsviews
				WHERE academic_term_id=TermID GROUP BY student_id, class_id, ca_weight_point;

			DECLARE CONTINUE HANDLER FOR NOT FOUND SET done1 = TRUE;
#Open The Cursor For Iterating Through The Recordset cur1
			OPEN cur1;
			REPEAT
				FETCH cur1 INTO StudentID, ClassID, CA_WP, StudentName;
				IF NOT done1 THEN

-- Second Iteration get the subjects a student offered during the weekly report for the academic term
						Block2: BEGIN
-- Declare Variable to be used in looping through the recordset or cursor
						DECLARE done2 BOOLEAN DEFAULT FALSE;
						DECLARE SubjectID, SubClassLevel int;
						DECLARE SubjectName VARCHAR(100);

						DECLARE cur2 CURSOR FOR
							SELECT subject_id, subject_classlevel_id, subject_name FROM weeklyreport_studentdetailsviews
							WHERE student_id=StudentID AND class_id=ClassID AND academic_term_id=TermID AND marked_status=1
							GROUP BY subject_id, subject_classlevel_id, subject_name;

						DECLARE CONTINUE HANDLER FOR NOT FOUND SET done2 = TRUE;
#Open The Cursor For Iterating Through The Recordset cur1
						OPEN cur2;
						REPEAT
							FETCH cur2 INTO SubjectID, SubClassLevel, SubjectName;
							IF NOT done2 THEN

								SET @TEMP_SUM = 0.0;
-- Third Iteration computes each subjects score  student offered during the weekly report for the academic term
									Block3: BEGIN
-- Declare Variable to be used in looping through the recordset or cursor
									DECLARE done3 BOOLEAN DEFAULT FALSE;
									DECLARE W_CA, WW_Point, WW_Percent FLOAT;

									DECLARE cur3 CURSOR FOR
										SELECT  weekly_ca, weekly_weight_point, weekly_weight_percent FROM weeklyreport_studentdetailsviews
										WHERE student_id=StudentID AND class_id=ClassID AND academic_term_id=TermID AND marked_status=1
													AND subject_id=SubjectID AND subject_classlevel_id=SubClassLevel;

									DECLARE CONTINUE HANDLER FOR NOT FOUND SET done3 = TRUE;
#Open The Cursor For Iterating Through The Recordset cur1
									OPEN cur3;
									REPEAT
										FETCH cur3 INTO W_CA, WW_Point, WW_Percent;
										IF NOT done3 THEN
											BEGIN
-- Get the sum of the weight point percent (100)
												SET @PercentSUM = (SELECT SUM(weekly_weight_percent) FROM weeklyreport_studentdetailsviews
												WHERE student_id=StudentID AND class_id=ClassID AND academic_term_id=TermID AND marked_status=1
															AND subject_id=SubjectID AND subject_classlevel_id=SubClassLevel);

-- Get the new weight point assigned to the weeks report ((25/100) * 30)
-- i.e the (weekly weight point percentage (/) divides the sum of the percentages) (*) multiply by the original C.A weight point
												SET @Temp_WP = ((WW_Percent / @PercentSUM) * CA_WP);
-- Get the new calcualted C.A of the weeks report ((11/15) * (((25/100) * 30)))
-- i.e the (weekly C.A score (/) divides the weekly weight point) (*) multiply by the calculated weight point above
												SET @Temp_CA = ((W_CA / WW_Point) * @Temp_WP);
-- Sum up all the calculated C.A weekly reports for the subjects
												SET @TEMP_SUM = @TEMP_SUM + @TEMP_CA;

											END;

										END IF;
									UNTIL done3 END REPEAT;
									CLOSE cur3;
								END Block3;

									Block3_1: BEGIN
-- Get the exam details id for that subject
									SET @ExamDetailID = (SELECT exam_detail_id FROM examsdetails_reportviews
									WHERE student_id=StudentID AND class_id=ClassID AND academic_term_id=TermID
												AND subject_id=SubjectID AND subject_classlevel_id=SubClassLevel);

-- Udate the exam details table and set the students C.A for that subject with the calculated C.A score
									UPDATE exam_details SET ca=@TEMP_SUM WHERE exam_detail_id=@ExamDetailID;

-- Save the calculated C.A for each subjects for each student
/*INSERT INTO SubjectCAResultTable(
  student_id, student_name, class_id, subject_id, subject_name,
  subject_classlevel_id, academic_term_id, calculated_ca, calculated_wp
)
VALUES(StudentID, StudentName, ClassID, SubjectID, SubjectName, SubClassLevel, TermID, @TEMP_SUM, CA_WP);*/
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

DROP PROCEDURE IF EXISTS `proc_terminalClassPositionViews`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `proc_terminalClassPositionViews`(IN `cla_id` INT, IN `term_id` INT)
Block0: BEGIN
		SET @Output = 0;
		SET @Average = 0;
		SET @Count = 0;

#Create a Temporary Table to Hold The Values
		DROP TEMPORARY TABLE IF EXISTS TerminalClassPositionResultTable;
		CREATE TEMPORARY TABLE IF NOT EXISTS TerminalClassPositionResultTable
		(
-- Add the column definitions for the TABLE variable here
			student_id int,
			full_name varchar(80),
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

		CALL proc_examsDetailsReportViews(term_id, 1);

			Block1: BEGIN
-- CALL smartschool.proc_examsDetailsReportViews(term_id);
-- Get the number of students in the class
			SET @ClassSize = (SELECT COUNT(*) FROM students_classlevelviews
			WHERE class_id = cla_id AND academic_year_id = (
				SELECT academic_year_id FROM academic_terms WHERE academic_term_id=term_id)
			);
			SET @TempPosition = 1;
			SET @TempStudentScore = 0;
			SET @Position = 0;

				Block2: BEGIN
-- Declare Variable to be used in looping through the recordset or cursor
				DECLARE done1 BOOLEAN DEFAULT FALSE;
				DECLARE StudentID, ClassID, TermID INT;
				DECLARE StudentName, ClassName, TermName VARCHAR(60);
				DECLARE StudentSumTotal, ExamPerfectScore FLOAT;
-- Populate the cursor with the values in a record i want to iterate through

				DECLARE cur1 CURSOR FOR
					SELECT student_id, student_fullname, class_id, class_name, academic_term_id, academic_term, student_sum_total, exam_perfect_score
					FROM ExamsDetailsResultTable WHERE class_id = cla_id and academic_term_id = term_id
					GROUP BY student_id, student_fullname, class_name, academic_term
					ORDER BY student_sum_total DESC;

				DECLARE CONTINUE HANDLER FOR NOT FOUND SET done1 = TRUE;
#Open The Cursor For Iterating Through The Recordset cur1
				OPEN cur1;
				REPEAT
					FETCH cur1 INTO StudentID, StudentName, ClassID, ClassName, TermID, TermName, StudentSumTotal, ExamPerfectScore;
					IF NOT done1 THEN
						BEGIN
-- IF the current student total is equal to the next student's total
							IF @TempStudentScore = StudentSumTotal THEN
-- Add one to the temp variable position
								SET @TempPosition = @TempPosition + 1;
-- Else if they are not equal
							ELSE
								BEGIN
-- Set the current student's position to be that of the temp variable
									SET @Position = @TempPosition;
-- Add one to the temp variable position
									SET @TempPosition = @TempPosition + 1;
								END;
							END IF;
							BEGIN
-- Insert into the resultant table that will display the computed results
								INSERT INTO TerminalClassPositionResultTable
								VALUES(StudentID, StudentName, ClassID, ClassName, TermID, TermName, StudentSumTotal, ExamPerfectScore, @Position, @ClassSize, @Average);
							END;
-- Get the current student total score and set it the variable for the next comparism
							SET @TempStudentScore = @StudentSumTotal;

-- Get the average of the students scores
							SET @Average = @Average + StudentSumTotal;
-- Update Count
							SET @Count = @Count + 1;
-- Update the average scores of the students
							UPDATE TerminalClassPositionResultTable SET class_average = (@Average / @Count);
						END;
					END IF;
				UNTIL done1 END REPEAT;
				CLOSE cur1;
			END Block2;
		END Block1;
	END Block0$$

--
-- Functions
--
DROP FUNCTION IF EXISTS `func_annualExamsViews`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `func_annualExamsViews`(`StudentID` INT, `AcademicYearID` INT) RETURNS int(11)
BEGIN
    SET @Output = 0;
#Create a Temporary Table to Hold The Values
    DROP TEMPORARY TABLE IF EXISTS AnnualSubjectViewsResultTable;
    CREATE TEMPORARY TABLE IF NOT EXISTS AnnualSubjectViewsResultTable
    (
-- Add the column definitions for the TABLE variable here
      subject_id INT,
      subject_name VARCHAR(60),
      subject_classlevel_id INT,
      classgroup_id INT,
      first_term Decimal(6, 2),
      second_term Decimal(6, 2),
      third_term Decimal(6, 2),
      annual_average Decimal(6, 2),
      annual_grade VARCHAR(50),
      grade_abbr VARCHAR(3)
    );

    CALL proc_examsDetailsReportViews(AcademicYearID, 2);

      Block0: BEGIN
-- Set three of those variable to get the 1st, 2nd and 3rd terms in that year passed as parameter of the student
      SET @FirstTerm = (SELECT academic_term_id FROM academic_terms WHERE academic_year_id = AcademicYearID AND term_type_id = 1);
      SET @SecondTerm = (SELECT academic_term_id FROM academic_terms WHERE academic_year_id = AcademicYearID AND term_type_id = 2);
      SET @ThirdTerm = (SELECT academic_term_id FROM academic_terms WHERE academic_year_id = AcademicYearID AND term_type_id = 3);
      SET @ClassGroupID = (SELECT classgroup_id FROM ExamsDetailsResultTable WHERE academic_year_id=AcademicYearID AND student_id=StudentID LIMIT 1);

        Block1: BEGIN
-- Declare Variable to be used in looping through the recordset or cursor
        DECLARE done1 BOOLEAN DEFAULT FALSE;
        DECLARE SubjectID, SubjectClasslevelID int;
        DECLARE SubjectName, TermName varchar(60);
        DECLARE cur1 CURSOR FOR
-- Populate the cursor with the values in a record i want to iterate through
          SELECT subject_id, subject_classlevel_id, subject_name
          FROM ExamsDetailsResultTable WHERE student_id=StudentID AND academic_year_id=AcademicYearID
          GROUP BY subject_id, subject_name;

        DECLARE CONTINUE HANDLER FOR NOT FOUND SET done1 = TRUE;
#Open The Cursor For Iterating Through The Recordset cur1
        OPEN cur1;
        REPEAT
          FETCH cur1 INTO SubjectID, SubjectClasslevelID, SubjectName;
-- Test to check if the cursor still have a next record
          IF NOT done1 THEN
            BEGIN
-- Sets the students scores in a particular subject that he or she offered in that year(i.e 1st, 2nd and 3rd terms)
              SET @FirstTermSubjectScore = (SELECT studentPercentTotal FROM ExamsDetailsResultTable WHERE academic_term_id=@FirstTerm AND student_id=StudentID AND subject_id=SubjectID);
              SET @SecondTermSubjectScore = (SELECT studentPercentTotal FROM ExamsDetailsResultTable WHERE academic_term_id=@SecondTerm AND student_id=StudentID AND subject_id=SubjectID);
              SET @ThirdTermSubjectScore = (SELECT studentPercentTotal FROM ExamsDetailsResultTable WHERE academic_term_id=@ThirdTerm AND student_id=StudentID AND subject_id=SubjectID);

              BEGIN
-- Get the average of a particular subject that he or she offered in that year and also check if any term was missed
                IF @FirstTermSubjectScore IS NOT NULL AND @SecondTermSubjectScore IS NOT NULL AND @ThirdTermSubjectScore IS NOT NULL THEN
                  SET @AnnualSubjectAverage = (@FirstTermSubjectScore + @SecondTermSubjectScore + @ThirdTermSubjectScore) / 3;
                ElSEIF	@FirstTermSubjectScore IS NOT NULL AND @SecondTermSubjectScore IS NOT NULL AND @ThirdTermSubjectScore IS NULL THEN
                  SET @AnnualSubjectAverage = (@FirstTermSubjectScore + @SecondTermSubjectScore ) / 2;
                ElSEIF	@FirstTermSubjectScore IS NOT NULL AND @SecondTermSubjectScore IS NULL AND @ThirdTermSubjectScore IS NOT NULL THEN
                  SET @AnnualSubjectAverage = (@FirstTermSubjectScore + @ThirdTermSubjectScore ) / 2;
                ElSEIF	@FirstTermSubjectScore IS NULL AND @SecondTermSubjectScore IS NOT NULL AND @ThirdTermSubjectScore IS NOT NULL THEN
                  SET @AnnualSubjectAverage = (@SecondTermSubjectScore + @ThirdTermSubjectScore) / 2;
                ElSEIF	@FirstTermSubjectScore IS NOT NULL AND @SecondTermSubjectScore IS NULL AND @ThirdTermSubjectScore IS NULL THEN
                  SET @AnnualSubjectAverage = @FirstTermSubjectScore;
                ElSEIF	@FirstTermSubjectScore IS NULL AND @SecondTermSubjectScore IS NOT NULL AND @ThirdTermSubjectScore IS NULL THEN
                  SET @AnnualSubjectAverage = @SecondTermSubjectScore;
                ElSEIF	@FirstTermSubjectScore IS NULL AND @SecondTermSubjectScore IS NULL AND @ThirdTermSubjectScore IS NOT NULL THEN
                  SET @AnnualSubjectAverage = @ThirdTermSubjectScore;
                ELSE
                  SET @AnnualSubjectAverage = 0;
                END IF;
              END;
-- Set the annal grade for each subject
              BEGIN
                SET @AnnualGrade = (SELECT grade FROM grades WHERE @AnnualSubjectAverage BETWEEN lower_bound AND upper_bound AND classgroup_id=@ClassGroupID LIMIT 1);
                SET @AnnualGradeAbbr = (SELECT grade_abbr FROM grades WHERE @AnnualSubjectAverage BETWEEN lower_bound AND upper_bound AND classgroup_id=@ClassGroupID LIMIT 1);
              END;
              BEGIN
-- Insert into the resultant table that will display the computed results
                INSERT INTO AnnualSubjectViewsResultTable
                VALUES(SubjectID, SubjectName, SubjectClasslevelID, @ClassGroupID, @FirstTermSubjectScore, @SecondTermSubjectScore, @ThirdTermSubjectScore,	@AnnualSubjectAverage, @AnnualGrade, @AnnualGradeAbbr);
              END;
            END;
          END IF;
        UNTIL done1 END REPEAT;
        CLOSE cur1;
      END Block1;
    END Block0;
    SET @Output = (SELECT COUNT(*) FROM AnnualSubjectViewsResultTable);
    RETURN @Output;
	END$$

DROP FUNCTION IF EXISTS `fun_getAttendSummary`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `fun_getAttendSummary`(TermID INT, ClassID INT) RETURNS int(11)
Block0: BEGIN
		SET @Output = 0;
#Create a Temporary Table to Hold The Values
		DROP TEMPORARY TABLE IF EXISTS AttendSummaryResultTable;
		CREATE TEMPORARY TABLE IF NOT EXISTS AttendSummaryResultTable
		(
-- Add the column definitions for the TABLE variable here
			student_id INT,
			student_no VARCHAR(20),
			student_name VARCHAR(70),
			total_attendance INT,
			days_present INT,
			days_absent INT,
			class_name VARCHAR(50),
			head_tutor VARCHAR(50),
			academic_term VARCHAR(50)
		);
			Block2: BEGIN
			SET @TotalAttend = (SELECT COUNT(attend_id) FROM attends WHERE class_id=ClassID AND academic_term_id=TermID LIMIT 1);

			INSERT INTO AttendSummaryResultTable
				SELECT a.student_id, c.student_no, c.student_name, @TotalAttend, COUNT(a.student_id),
					(@TotalAttend - COUNT(a.student_id)), b.class_name, b.head_tutor, b.academic_term
				FROM students_classlevelviews c
					INNER JOIN attend_details a ON c.student_id=a.student_id
					INNER JOIN attend_headerviews b ON a.attend_id=b.attend_id
				WHERE b.class_id=ClassID AND b.academic_term_id=TermID GROUP BY student_id;

		END Block2;

		SET @Output = (SELECT COUNT(*) FROM AttendSummaryResultTable);
		RETURN @Output;
	END Block0$$

DROP FUNCTION IF EXISTS `fun_getClassHeadTutor`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `fun_getClassHeadTutor`(ClassLevelID INT, YearID INT) RETURNS int(3)
    DETERMINISTIC
Block0: BEGIN
		SET @Output = 0;
#Create a Temporary Table to Hold The Values
		DROP TEMPORARY TABLE IF EXISTS ClassHeadTutorResultTable;
		CREATE TEMPORARY TABLE IF NOT EXISTS ClassHeadTutorResultTable
		(
-- Add the column definitions for the TABLE variable here
			class_id INT,
			class_name VARCHAR(50),
			classlevel_id INT,
			student_count INT,
			teacher_class_id INT NULL,
			employee_id INT NULL,
			employee_name VARCHAR(80),
			academic_year_id INT
		);
			Block2: BEGIN
			DECLARE done1 BOOLEAN DEFAULT FALSE;
			DECLARE ClassID, Classlevel_ID INT;
			DECLARE ClassName VARCHAR(50);
			DECLARE cur1 CURSOR FOR
				SELECT a.class_id, a.class_name, a.classlevel_id
				FROM classrooms a WHERE a.classlevel_id=ClassLevelID;
			DECLARE CONTINUE HANDLER FOR NOT FOUND SET done1 = TRUE;

#Open The Cursor For Iterating Through The Recordset cur1
			OPEN cur1;
			REPEAT
				FETCH cur1 INTO ClassID, ClassName, Classlevel_ID;
				IF NOT done1 THEN
					BEGIN
						SET @StudentCount = (SELECT COUNT(*) FROM students_classes WHERE class_id=ClassID AND academic_year_id=YearID);
						SET @TeachClassID = (SELECT teacher_class_id FROM teachers_classes WHERE class_id=ClassID AND academic_year_id=YearID LIMIT 1);
						SET @EmployeeID = (SELECT employee_id FROM teachers_classes WHERE teacher_class_id=@TeachClassID);
						SET @EmployeeName = (SELECT CONCAT(first_name, ' ', other_name) FROM employees WHERE employee_id=@EmployeeID);

						INSERT INTO ClassHeadTutorResultTable(class_id, class_name, classlevel_id, student_count, teacher_class_id, employee_id, employee_name, academic_year_id)
							SELECT ClassID, ClassName, Classlevel_ID, @StudentCount, @TeachClassID, @EmployeeID, @EmployeeName, YearID;
					END;
				END IF;
			UNTIL done1 END REPEAT;
			CLOSE cur1;
		END Block2;

		SET @Output = (SELECT COUNT(*) FROM ClassHeadTutorResultTable);
		RETURN @Output;
	END Block0$$

DROP FUNCTION IF EXISTS `fun_getClasslevelSub`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `fun_getClasslevelSub`(`TermID` INT, `LevelID` INT) RETURNS int(11)
    DETERMINISTIC
Block0: BEGIN
		SET @Output = 0;
#Create a Temporary Table to Hold The Values
		DROP TEMPORARY TABLE IF EXISTS SubjectClasslevelTemp;
		CREATE TEMPORARY TABLE IF NOT EXISTS SubjectClasslevelTemp
		(
-- Add the column definitions for the TABLE variable here
			row_id INT AUTO_INCREMENT,
			subject_id INT,
			subject_name VARCHAR(50),
			academic_term_id INT,
			academic_term VARCHAR(50),
			class_id INT,
			class_name VARCHAR(50),
			classlevel_id INT,
			classlevel VARCHAR(50), PRIMARY KEY (row_id)
		);

			Block2: BEGIN
			DECLARE done1 BOOLEAN DEFAULT FALSE;
			DECLARE SubjectID, ClassID, ClasslevelID, AcademicID  INT;
			DECLARE SubjectName, ClassName, AcademicTerm VARCHAR(50);
			DECLARE cur1 CURSOR FOR SELECT subject_id, class_id, classlevel_id, academic_term_id, subject_name, class_name, academic_term
															FROM subject_classlevelviews WHERE academic_term_id=TermID AND classlevel_id=LevelID GROUP BY subject_id ORDER BY subject_name;
			DECLARE CONTINUE HANDLER FOR NOT FOUND SET done1 = TRUE;

#Open The Cursor For Iterating Through The Recordset cur1
			OPEN cur1;
			REPEAT
				FETCH cur1 INTO SubjectID, ClassID, ClasslevelID, AcademicID, SubjectName, ClassName, AcademicTerm;
				IF NOT done1 THEN
					BEGIN
						SET @ClassInLevel = (SELECT COUNT(*) FROM classrooms WHERE classlevel_id=LevelID);
						SET @SubjectInLevel = (SELECT COUNT(*) FROM subject_classlevelviews a WHERE academic_term_id=TermID AND classlevel_id=LevelID AND subject_id=SubjectID);

						IF @ClassInLevel = @SubjectInLevel THEN
-- Insert into the resultant table that will display the results
							INSERT INTO SubjectClasslevelTemp(subject_id, subject_name, academic_term_id, academic_term, class_id, class_name, classlevel_id, classlevel)
							VALUES(SubjectID, SubjectName, AcademicID, AcademicTerm, ClassID, ClassName, ClasslevelID, classlevel);
						END IF;
					END;
				END IF;
			UNTIL done1 END REPEAT;
			CLOSE cur1;
		END Block2;

		SET @Output = (SELECT COUNT(*) FROM SubjectClasslevelTemp);
		RETURN @Output;
	END Block0$$

DROP FUNCTION IF EXISTS `fun_getSubjectClasslevel`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `fun_getSubjectClasslevel`(`term_id` INT) RETURNS int(11)
    DETERMINISTIC
Block0: BEGIN
		SET @Output = 0;
#Create a Temporary Table to Hold The Values
		DROP TEMPORARY TABLE IF EXISTS SubjectClasslevelResultTable;
		CREATE TEMPORARY TABLE IF NOT EXISTS SubjectClasslevelResultTable
		(
-- Add the column definitions for the TABLE variable here
			class_name VARCHAR(50),
			subject_name VARCHAR(50),
			subject_id INT,
			class_id INT,
			classlevel_id INT,
			subject_classlevel_id INT,
			classlevel VARCHAR(50),
			examstatus_id INT,
			exam_status VARCHAR(50),
			academic_term_id INT,
			academic_term VARCHAR(50),
			academic_year_id INT,
			academic_year VARCHAR(50)
		);
			Block2: BEGIN
			DECLARE done1 BOOLEAN DEFAULT FALSE;
			DECLARE si, ci, cli, scli, esi, ati, ayi  INT;
			DECLARE cn, sn, cl, es, atn, ayn VARCHAR(30);
			DECLARE cur1 CURSOR FOR SELECT * FROM subject_classlevelviews WHERE academic_term_id=term_id;
			DECLARE CONTINUE HANDLER FOR NOT FOUND SET done1 = TRUE;

#Open The Cursor For Iterating Through The Recordset cur1
			OPEN cur1;
			REPEAT
				FETCH cur1 INTO cn, sn, si, ci, cli, scli, cl, esi, es, ati, atn, ayi, ayn;
				IF NOT done1 THEN
					BEGIN
						IF ci > 0 OR ci IS NOT NULL THEN
-- Insert into the resultant table that will display the results
							BEGIN
								INSERT INTO SubjectClasslevelResultTable VALUES(cn, sn, si, ci, cli, scli, cl, esi, es, ati, atn, ayi, ayn);
							END;
						ELSE
							BEGIN
								INSERT INTO SubjectClasslevelResultTable(class_name, subject_name,subject_id, class_id, classlevel_id, subject_classlevel_id,
																												 classlevel, examstatus_id, exam_status, academic_term_id, academic_term, academic_year_id, academic_year)
									SELECT a.class_name, sn, si, a.class_id, cli, scli, cl, esi, es, ati, atn, ayi, ayn
									FROM classroom_subjectregisterviews a
									WHERE a.subject_classlevel_id=scli AND a.academic_term_id=ati;

-- SELECT classrooms.class_name, sn, si, classrooms.class_id, cli, scli, cl, esi, es, ati, atn, ayi, ayn
-- FROM   classrooms INNER JOIN classlevels ON classrooms.classlevel_id = classlevels.classlevel_id
-- WHERE classrooms.classlevel_id = cli;
							END;
						END IF;
					END;
				END IF;
			UNTIL done1 END REPEAT;
			CLOSE cur1;
		END Block2;

		SET @Output = (SELECT COUNT(*) FROM SubjectClasslevelResultTable);
		RETURN @Output;
	END Block0$$

DROP FUNCTION IF EXISTS `getCurrentTermID`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `getCurrentTermID`() RETURNS int(11)
BEGIN
		RETURN (SELECT academic_term_id FROM academic_terms WHERE term_status_id=1 LIMIT 1);
	END$$

DROP FUNCTION IF EXISTS `getCurrentYearID`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `getCurrentYearID`() RETURNS int(11)
BEGIN
		RETURN (SELECT academic_year_id FROM academic_years WHERE year_status_id=1 LIMIT 1);
	END$$

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
-- Table structure for table `lgas`
--

DROP TABLE IF EXISTS `lgas`;
CREATE TABLE IF NOT EXISTS `lgas` (
  `lga_id` int(3) unsigned NOT NULL,
  `lga` varchar(50) DEFAULT NULL,
  `state_id` int(3) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=781 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `lgas`
--

INSERT INTO `lgas` (`lga_id`, `lga`, `state_id`) VALUES
(1, 'Aba North', 1),
(2, 'Aba South', 1),
(3, 'Arochukwu', 1),
(4, 'Bende', 1),
(5, 'Ikwuano', 1),
(6, 'Isiala-Ngwa North', 1),
(7, 'Isiala-Ngwa South', 1),
(8, 'Isuikwato', 1),
(9, 'Ngwa', 1),
(10, 'Obi Nwa', 1),
(11, 'Ohafia', 1),
(12, 'Osisioma', 1),
(13, 'Ugwunagbo', 1),
(14, 'Ukwa East', 1),
(15, 'Ukwa West', 1),
(16, 'Umuahia North', 1),
(17, 'Umuahia South', 1),
(18, 'Umu-Neochi', 1),
(19, 'Demsa', 2),
(20, 'Fufore', 2),
(21, 'Ganaye', 2),
(22, 'Gireri', 2),
(23, 'Gombi', 2),
(24, 'Guyuk', 2),
(25, 'Hong', 2),
(26, 'Jada', 2),
(27, 'Lamurde', 2),
(28, 'Madagali', 2),
(29, 'Maiha ', 2),
(30, 'Mayo-Belwa', 2),
(31, 'Michika', 2),
(32, 'Mubi North', 2),
(33, 'Mubi South', 2),
(34, 'Numan', 2),
(35, 'Shelleng', 2),
(36, 'Song', 2),
(37, 'Toungo', 2),
(38, 'Yola North', 2),
(39, 'Yola South', 2),
(40, 'Abak', 3),
(41, 'Eastern Obolo', 3),
(42, 'Eket', 3),
(43, 'Esit Eket', 3),
(44, 'Essien Udim', 3),
(45, 'Etim Ekpo', 3),
(46, 'Etinan', 3),
(47, 'Ibeno', 3),
(48, 'Ibesikpo Asutan', 3),
(49, 'Ibiono Ibom', 3),
(50, 'Ika', 3),
(51, 'Ikono', 3),
(52, 'Ikot Abasi', 3),
(53, 'Ikot Ekpene', 3),
(54, 'Ini', 3),
(55, 'Itu', 3),
(56, 'Mbo', 3),
(57, 'Mkpat Enin', 3),
(58, 'Nsit Atai', 3),
(59, 'Nsit Ibom', 3),
(60, 'Nsit Ubium', 3),
(61, 'Obot Akara', 3),
(62, 'Okobo', 3),
(63, 'Onna', 3),
(64, 'Oron ', 3),
(65, 'Oruk Anam', 3),
(66, 'Udung Uko', 3),
(67, 'Ukanafun', 3),
(68, 'Uruan', 3),
(69, 'Urue-Offong/Oruko', 3),
(70, 'Uyo', 3),
(71, 'Aguata', 4),
(72, 'Anambra East', 4),
(73, 'Anambra West', 4),
(74, 'Anaocha', 4),
(75, 'Awka North', 4),
(76, 'Awka South', 4),
(77, 'Ayamelum', 4),
(78, 'Dunukofia', 4),
(79, 'Ekwusigo', 4),
(80, 'Idemili North', 4),
(81, 'Idemili South', 4),
(82, 'Ihiala', 4),
(83, 'Njikoka', 4),
(84, 'Nnewi North', 4),
(85, 'Nnewi South', 4),
(86, 'Ogbaru', 4),
(87, 'Onitsha North', 4),
(88, 'Onitsha South', 4),
(89, 'Orumba North', 4),
(90, 'Orumba South', 4),
(91, 'Oyi ', 4),
(92, 'Alkaleri', 5),
(93, 'Bauchi', 5),
(94, 'Bogoro', 5),
(95, 'Damban', 5),
(96, 'Darazo', 5),
(97, 'Dass', 5),
(98, 'Ganjuwa', 5),
(99, 'Giade', 5),
(100, 'Itas/Gadau', 5),
(101, 'Jama''Are', 5),
(102, 'Katagum', 5),
(103, 'Kirfi', 5),
(104, 'Misau', 5),
(105, 'Ningi', 5),
(106, 'Shira', 5),
(107, 'Tafawa-Balewa', 5),
(108, 'Toro', 5),
(109, 'Warji', 5),
(110, 'Zaki ', 5),
(111, 'Brass', 32),
(112, 'Ekeremor', 32),
(113, 'Kolokuma/Opokuma', 32),
(114, 'Nembe', 32),
(115, 'Ogbia', 32),
(116, 'Sagbama', 32),
(117, 'Southern Jaw', 32),
(118, 'Yenegoa ', 32),
(119, 'Ado', 6),
(120, 'Agatu', 6),
(121, 'Apa', 6),
(122, 'Buruku', 6),
(123, 'Gboko', 6),
(124, 'Guma', 6),
(125, 'Gwer East', 6),
(126, 'Gwer West', 6),
(127, 'Katsina-Ala', 6),
(128, 'Konshisha', 6),
(129, 'Kwande', 6),
(130, 'Logo', 6),
(131, 'Makurdi', 6),
(132, 'Obi', 6),
(133, 'Ogbadibo', 6),
(134, 'Ohimini', 6),
(135, 'Oju', 6),
(136, 'Okpokwu', 6),
(137, 'Oturkpo', 6),
(138, 'Tarka', 6),
(139, 'Ukum', 6),
(140, 'Ushongo', 6),
(141, 'Vandeikya ', 6),
(142, 'Abadam', 7),
(143, 'Askira/Uba', 7),
(144, 'Bama', 7),
(145, 'Bayo', 7),
(146, 'Biu', 7),
(147, 'Chibok', 7),
(148, 'Damboa', 7),
(149, 'Dikwa', 7),
(150, 'Gubio', 7),
(151, 'Guzamala', 7),
(152, 'Gwoza', 7),
(153, 'Hawul', 7),
(154, 'Jere', 7),
(155, 'Kaga', 7),
(156, 'Kala/Balge', 7),
(157, 'Konduga', 7),
(158, 'Kukawa', 7),
(159, 'Kwaya Kusar', 7),
(160, 'Mafa', 7),
(161, 'Magumeri', 7),
(162, 'Maiduguri', 7),
(163, 'Marte', 7),
(164, 'Mobbar', 7),
(165, 'Monguno', 7),
(166, 'Ngala', 7),
(167, 'Nganzai', 7),
(168, 'Shani ', 7),
(169, 'Abi', 8),
(170, 'Akamkpa', 8),
(171, 'Akpabuyo', 8),
(172, 'Bakassi', 8),
(173, 'Bekwara', 8),
(174, 'Biase', 8),
(175, 'Boki', 8),
(176, 'Calabar Municipality', 8),
(177, 'Calabar South', 8),
(178, 'Etung', 8),
(179, 'Ikom', 8),
(180, 'Obanliku', 8),
(181, 'Obudu', 8),
(182, 'Odubra', 8),
(183, 'Odukpani', 8),
(184, 'Ogoja', 8),
(185, 'Yala', 8),
(186, 'Yarkur', 8),
(187, 'Aniocha', 9),
(188, 'Aniocha South', 9),
(189, 'Bomadi', 9),
(190, 'Burutu', 9),
(191, 'Ethiope East', 9),
(192, 'Ethiope West', 9),
(193, 'Ika North-East', 9),
(194, 'Ika South', 9),
(195, 'Isoko North', 9),
(196, 'Isoko South', 9),
(197, 'Ndokwa East', 9),
(198, 'Ndokwa West', 9),
(199, 'Okpe', 9),
(200, 'Oshimili', 9),
(201, 'Oshimili North', 9),
(202, 'Patani', 9),
(203, 'Sapele', 9),
(204, 'Udu', 9),
(205, 'Ughelli North', 9),
(206, 'Ughelli South', 9),
(207, 'Ukwani', 9),
(208, 'Uvwie', 9),
(209, 'Warri Central', 9),
(210, 'Warri North', 9),
(211, 'Warri South', 9),
(212, 'Abakaliki', 37),
(213, 'Afikpo North', 37),
(214, 'Afikpo South', 37),
(215, 'Ebonyi', 37),
(216, 'Ezza', 37),
(217, 'Ezza South', 37),
(218, 'Ishielu', 37),
(219, 'Ivo ', 37),
(220, 'Lkwo', 37),
(221, 'Ohaozara', 37),
(222, 'Ohaukwu', 37),
(223, 'Onicha', 37),
(224, 'Central', 10),
(225, 'Egor', 10),
(226, 'Esan Central', 10),
(227, 'Esan North-East', 10),
(228, 'Esan South-East ', 10),
(229, 'Esan West', 10),
(230, 'Etsako Central', 10),
(231, 'Etsako East ', 10),
(232, 'Igueben', 10),
(233, 'Oredo', 10),
(234, 'Orhionwon', 10),
(235, 'Ovia South-East', 10),
(236, 'Ovia Southwest', 10),
(237, 'Uhunmwonde', 10),
(238, 'Ukpoba', 10),
(239, 'Ado', 36),
(240, 'Efon', 36),
(241, 'Ekiti South-West', 36),
(242, 'Ekiti-East', 36),
(243, 'Ekiti-West ', 36),
(244, 'Emure/Ise/Orun', 36),
(245, 'Gbonyin', 36),
(246, 'Ido/Osi', 36),
(247, 'Ijero', 36),
(248, 'Ikare', 36),
(249, 'Ikole', 36),
(250, 'Ilejemeje.', 36),
(251, 'Irepodun', 36),
(252, 'Ise/Orun ', 36),
(253, 'Moba', 36),
(254, 'Oye', 36),
(255, 'Aninri', 11),
(256, 'Enugu Eas', 11),
(257, 'Enugu North', 11),
(258, 'Enugu South', 0),
(259, 'Ezeagu', 11),
(260, 'Igbo-Ekiti', 11),
(261, 'Igboeze North', 11),
(262, 'Igbo-Eze South', 11),
(263, 'Isi-Uzo', 11),
(264, 'Nkanu', 11),
(265, 'Nkanu East', 11),
(266, 'Nsukka', 11),
(267, 'Oji-River', 11),
(268, 'Udenu. ', 11),
(269, 'Udi Agwu', 11),
(270, 'Uzo-Uwani', 11),
(271, 'Abaji', 31),
(272, 'Abuja Municipal', 31),
(273, 'Bwari', 31),
(274, 'Gwagwalada', 31),
(275, 'Kuje', 31),
(276, 'Kwali', 31),
(277, 'Akko', 33),
(278, 'Balanga', 33),
(279, 'Billiri', 33),
(280, 'Dukku', 33),
(281, 'Funakaye', 33),
(282, 'Gombe', 33),
(283, 'Kaltungo', 33),
(284, 'Kwami', 33),
(285, 'Nafada/Bajoga ', 33),
(286, 'Shomgom', 33),
(287, 'Yamaltu/Delta. ', 33),
(288, 'Aboh-Mbaise', 12),
(289, 'Ahiazu-Mbaise', 12),
(290, 'Ehime-Mbano', 12),
(291, 'Ezinihitte', 12),
(292, 'Ideato North', 12),
(293, 'Ideato South', 12),
(294, 'Ihitte/Uboma', 12),
(295, 'Ikeduru', 12),
(296, 'Isiala Mbano', 12),
(297, 'Isu', 12),
(298, 'Mbaitoli', 12),
(299, 'Mbaitoli', 12),
(300, 'Ngor-Okpala', 12),
(301, 'Njaba', 12),
(302, 'Nkwerre', 12),
(303, 'Nwangele', 12),
(304, 'Obowo', 12),
(305, 'Oguta', 12),
(306, 'Ohaji/Egbema', 12),
(307, 'Okigwe', 12),
(308, 'Orlu', 12),
(309, 'Orsu', 12),
(310, 'Oru East', 12),
(311, 'Oru West', 12),
(312, 'Owerri North', 12),
(313, 'Owerri West ', 12),
(314, 'Owerri-Municipal', 12),
(315, 'Auyo', 13),
(316, 'Babura', 13),
(317, 'Biriniwa', 13),
(318, 'Birni Kudu', 13),
(319, 'Buji', 13),
(320, 'Dutse', 13),
(321, 'Gagarawa', 13),
(322, 'Garki', 13),
(323, 'Gumel', 13),
(324, 'Guri', 13),
(325, 'Gwaram', 13),
(326, 'Gwiwa', 13),
(327, 'Hadejia', 13),
(328, 'Jahun', 13),
(329, 'Kafin Hausa', 13),
(330, 'Kaugama Kazaure', 13),
(331, 'Kiri Kasamma', 13),
(332, 'Kiyawa', 13),
(333, 'Maigatari', 13),
(334, 'Malam Madori', 13),
(335, 'Miga', 13),
(336, 'Ringim', 13),
(337, 'Roni', 13),
(338, 'Sule-Tankarkar', 13),
(339, 'Taura ', 13),
(340, 'Yankwashi ', 13),
(341, 'Birni-Gwari', 15),
(342, 'Chikun', 15),
(343, 'Giwa', 15),
(344, 'Igabi', 15),
(345, 'Ikara', 15),
(346, 'Jaba', 15),
(347, 'Jema''A', 15),
(348, 'Kachia', 15),
(349, 'Kaduna North', 15),
(350, 'Kaduna South', 15),
(351, 'Kagarko', 15),
(352, 'Kajuru', 15),
(353, 'Kaura', 15),
(354, 'Kauru', 15),
(355, 'Kubau', 15),
(356, 'Kudan', 15),
(357, 'Lere', 15),
(358, 'Makarfi', 15),
(359, 'Sabon-Gari', 15),
(360, 'Sanga', 15),
(361, 'Soba', 15),
(362, 'Zango-Kataf', 15),
(363, 'Zaria ', 15),
(364, 'Ajingi', 17),
(365, 'Albasu', 17),
(366, 'Bagwai', 17),
(367, 'Bebeji', 17),
(368, 'Bichi', 17),
(369, 'Bunkure', 17),
(370, 'Dala', 17),
(371, 'Dambatta', 17),
(372, 'Dawakin Kudu', 17),
(373, 'Dawakin Tofa', 17),
(374, 'Doguwa', 17),
(375, 'Fagge', 17),
(376, 'Gabasawa', 17),
(377, 'Garko', 17),
(378, 'Garum', 17),
(379, 'Gaya', 17),
(380, 'Gezawa', 17),
(381, 'Gwale', 17),
(382, 'Gwarzo', 17),
(383, 'Kabo', 17),
(384, 'Kano Municipal', 17),
(385, 'Karaye', 17),
(386, 'Kibiya', 17),
(387, 'Kiru', 17),
(388, 'Kumbotso', 17),
(389, 'Kunchi', 17),
(390, 'Kura', 17),
(391, 'Madobi', 17),
(392, 'Makoda', 17),
(393, 'Mallam', 17),
(394, 'Minjibir', 17),
(395, 'Nasarawa', 17),
(396, 'Rano', 17),
(397, 'Rimin Gado', 17),
(398, 'Rogo', 17),
(399, 'Shanono', 17),
(400, 'Sumaila', 17),
(401, 'Takali', 17),
(402, 'Tarauni', 17),
(403, 'Tofa', 17),
(404, 'Tsanyawa', 17),
(405, 'Tudun Wada', 17),
(406, 'Ungogo', 17),
(407, 'Warawa', 17),
(408, 'Wudil', 17),
(409, 'Bakori', 18),
(410, 'Batagarawa', 18),
(411, 'Batsari', 18),
(412, 'Baure', 18),
(413, 'Bindawa', 18),
(414, 'Charanchi', 18),
(415, 'Dan Musa', 18),
(416, 'Dandume', 18),
(417, 'Danja', 18),
(418, 'Daura', 18),
(419, 'Dutsi', 18),
(420, 'Dutsin-Ma', 18),
(421, 'Faskari', 18),
(422, 'Funtua', 18),
(423, 'Ingawa', 18),
(424, 'Jibia', 18),
(425, 'Kafur', 18),
(426, 'Kaita', 18),
(427, 'Kankara', 18),
(428, 'Kankia', 18),
(429, 'Katsina', 18),
(430, 'Kurfi', 18),
(431, 'Kusada', 18),
(432, 'Mai''Adua', 18),
(433, 'Malumfashi', 18),
(434, 'Mani', 18),
(435, 'Mashi', 18),
(436, 'Matazuu', 18),
(437, 'Musawa', 18),
(438, 'Rimi', 18),
(439, 'Sabuwa', 18),
(440, 'Safana', 18),
(441, 'Sandamu', 18),
(442, 'Zango ', 18),
(443, 'Aleiro', 14),
(444, 'Arewa-Dandi', 14),
(445, 'Argungu', 14),
(446, 'Augie', 14),
(447, 'Bagudo', 14),
(448, 'Birnin Kebbi', 14),
(449, 'Bunza', 14),
(450, 'Dandi ', 14),
(451, 'Fakai', 14),
(452, 'Gwandu', 14),
(453, 'Jega', 14),
(454, 'Kalgo ', 14),
(455, 'Koko/Besse', 14),
(456, 'Maiyama', 14),
(457, 'Ngaski', 14),
(458, 'Sakaba', 14),
(459, 'Shanga', 14),
(460, 'Suru', 14),
(461, 'Wasagu/Danko', 14),
(462, 'Yauri', 14),
(463, 'Zuru ', 14),
(464, 'Adavi', 16),
(465, 'Ajaokuta', 16),
(466, 'Ankpa', 16),
(467, 'Bassa', 16),
(468, 'Dekina', 16),
(469, 'Ibaji', 16),
(470, 'Idah', 16),
(471, 'Igalamela-Odolu', 16),
(472, 'Ijumu', 16),
(473, 'Kabba/Bunu', 16),
(474, 'Kogi', 16),
(475, 'Lokoja', 16),
(476, 'Mopa-Muro', 16),
(477, 'Ofu', 16),
(478, 'Ogori/Mangongo', 16),
(479, 'Okehi', 16),
(480, 'Okene', 16),
(481, 'Olamabolo', 16),
(482, 'Omala', 16),
(483, 'Yagba East ', 16),
(484, 'Yagba West', 16),
(485, 'Asa', 19),
(486, 'Baruten', 19),
(487, 'Edu', 19),
(488, 'Ekiti', 19),
(489, 'Ifelodun', 19),
(490, 'Ilorin East', 19),
(491, 'Ilorin West', 19),
(492, 'Irepodun', 19),
(493, 'Isin', 19),
(494, 'Kaiama', 19),
(495, 'Moro', 19),
(496, 'Offa', 19),
(497, 'Oke-Ero', 19),
(498, 'Oyun', 19),
(499, 'Pategi ', 19),
(500, 'Agege', 20),
(501, 'Ajeromi-Ifelodun', 20),
(502, 'Alimosho', 20),
(503, 'Amuwo-Odofin', 20),
(504, 'Apapa', 20),
(505, 'Badagry', 20),
(506, 'Epe', 20),
(507, 'Eti-Osa', 20),
(508, 'Ibeju/Lekki', 20),
(509, 'Ifako-Ijaye ', 20),
(510, 'Ikeja', 20),
(511, 'Ikorodu', 20),
(512, 'Kosofe', 20),
(513, 'Lagos Island', 20),
(514, 'Lagos Mainland', 20),
(515, 'Mushin', 20),
(516, 'Ojo', 20),
(517, 'Oshodi-Isolo', 20),
(518, 'Shomolu', 20),
(519, 'Surulere', 20),
(520, 'Akwanga', 34),
(521, 'Awe', 34),
(522, 'Doma', 34),
(523, 'Karu', 34),
(524, 'Keana', 34),
(525, 'Keffi', 34),
(526, 'Kokona', 34),
(527, 'Lafia', 34),
(528, 'Nasarawa', 34),
(529, 'Nasarawa-Eggon', 34),
(530, 'Obi', 34),
(531, 'Toto', 34),
(532, 'Wamba ', 34),
(533, 'Agaie', 21),
(534, 'Agwara', 21),
(535, 'Bida', 21),
(536, 'Borgu', 21),
(537, 'Bosso', 21),
(538, 'Chanchaga', 21),
(539, 'Edati', 21),
(540, 'Gbako', 21),
(541, 'Gurara', 21),
(542, 'Katcha', 21),
(543, 'Kontagora ', 21),
(544, 'Lapai', 21),
(545, 'Lavun', 21),
(546, 'Magama', 21),
(547, 'Mariga', 21),
(548, 'Mashegu', 21),
(549, 'Mokwa', 21),
(550, 'Muya', 21),
(551, 'Paikoro', 21),
(552, 'Rafi', 21),
(553, 'Rijau', 21),
(554, 'Shiroro', 21),
(555, 'Suleja', 21),
(556, 'Tafa', 21),
(557, 'Wushishi', 21),
(558, 'Abeokuta North', 23),
(559, 'Abeokuta South', 23),
(560, 'Ado-Odo/Ota', 23),
(561, 'Egbado North', 23),
(562, 'Egbado South', 23),
(563, 'Ewekoro', 23),
(564, 'Ifo', 23),
(565, 'Ijebu East', 23),
(566, 'Ijebu North', 23),
(567, 'Ijebu North East', 23),
(568, 'Ijebu Ode', 23),
(569, 'Ikenne', 23),
(570, 'Imeko-Afon', 23),
(571, 'Ipokia', 23),
(572, 'Obafemi-Owode', 23),
(573, 'Odeda', 23),
(574, 'Odogbolu', 23),
(575, 'Ogun Waterside', 23),
(576, 'Remo North', 23),
(577, 'Shagamu', 23),
(578, 'Akoko North East', 22),
(579, 'Akoko North West', 22),
(580, 'Akoko South Akure East', 22),
(581, 'Akoko South West', 22),
(582, 'Akure North', 22),
(583, 'Akure South', 22),
(584, 'Ese-Odo', 22),
(585, 'Idanre', 22),
(586, 'Ifedore', 22),
(587, 'Ilaje', 22),
(588, 'Ile-Oluji', 22),
(589, 'Irele', 22),
(590, 'Odigbo', 22),
(591, 'Okeigbo', 22),
(592, 'Okitipupa', 22),
(593, 'Ondo East', 22),
(594, 'Ondo West', 22),
(595, 'Ose', 22),
(596, 'Owo ', 22),
(597, 'Aiyedade', 24),
(598, 'Aiyedire', 24),
(599, 'Atakumosa East', 24),
(600, 'Atakumosa West', 24),
(601, 'Boluwaduro', 24),
(602, 'Boripe', 24),
(603, 'Ede North', 24),
(604, 'Ede South', 24),
(605, 'Egbedore', 24),
(606, 'Ejigbo', 24),
(607, 'Ife Central', 24),
(608, 'Ife East', 24),
(609, 'Ife North', 24),
(610, 'Ife South', 24),
(611, 'Ifedayo', 24),
(612, 'Ifelodun', 24),
(613, 'Ila', 24),
(614, 'Ilesha East', 24),
(615, 'Ilesha West', 24),
(616, 'Irepodun', 24),
(617, 'Irewole', 24),
(618, 'Isokan', 24),
(619, 'Iwo', 24),
(620, 'Obokun', 24),
(621, 'Odo-Otin', 24),
(622, 'Ola-Oluwa', 24),
(623, 'Olorunda', 24),
(624, 'Oriade', 24),
(625, 'Orolu', 24),
(626, 'Osogbo', 24),
(627, 'Afijio', 25),
(628, 'Akinyele', 25),
(629, 'Atiba', 25),
(630, 'Atigbo', 25),
(631, 'Egbeda', 25),
(632, 'Ibadan North', 25),
(633, 'Ibadan North West', 25),
(634, 'Ibadan South East', 25),
(635, 'Ibadan South West', 25),
(636, 'Ibadan Central', 25),
(637, 'Ibarapa Central', 25),
(638, 'Ibarapa East', 25),
(639, 'Ibarapa North', 25),
(640, 'Ido', 25),
(641, 'Irepo', 25),
(642, 'Iseyin', 25),
(643, 'Itesiwaju', 25),
(644, 'Iwajowa', 25),
(645, 'Kajola', 25),
(646, 'Lagelu Ogbomosho North', 25),
(647, 'Ogbmosho South', 25),
(648, 'Ogo Oluwa', 25),
(649, 'Olorunsogo', 25),
(650, 'Oluyole', 25),
(651, 'Ona-Ara', 25),
(652, 'Orelope', 25),
(653, 'Ori Ire', 25),
(654, 'Oyo East', 25),
(655, 'Oyo West', 25),
(656, 'Saki East', 25),
(657, 'Saki West', 25),
(658, 'Surulere', 25),
(659, 'Barikin Ladi', 26),
(660, 'Bassa', 26),
(661, 'Bokkos', 26),
(662, 'Jos East', 26),
(663, 'Jos North', 26),
(664, 'Jos South', 26),
(665, 'Kanam', 26),
(666, 'Kanke', 26),
(667, 'Langtang North', 26),
(668, 'Langtang South', 26),
(669, 'Mangu', 26),
(670, 'Mikang', 26),
(671, 'Pankshin', 26),
(672, 'Qua''An Pan', 26),
(673, 'Riyom', 26),
(674, 'Shendam', 26),
(675, 'Wase', 26),
(676, 'Abua/Odual', 27),
(677, 'Ahoada East', 27),
(678, 'Ahoada West', 27),
(679, 'Akuku Toru', 27),
(680, 'Andoni', 27),
(681, 'Asari-Toru', 27),
(682, 'Bonny', 27),
(683, 'Degema', 27),
(684, 'Eleme', 27),
(685, 'Emohua', 27),
(686, 'Etche', 27),
(687, 'Gokana', 27),
(688, 'Ikwerre', 27),
(689, 'Khana', 27),
(690, 'Obia/Akpor', 27),
(691, 'Ogba/Egbema/Ndoni', 27),
(692, 'Ogu/Bolo', 27),
(693, 'Okrika', 27),
(694, 'Omumma', 27),
(695, 'Opobo/Nkoro', 27),
(696, 'Oyigbo', 27),
(697, 'Port-Harcourt', 27),
(698, 'Tai ', 27),
(699, 'Binji', 28),
(700, 'Bodinga', 28),
(701, 'Dange-Shnsi', 28),
(702, 'Gada', 28),
(703, 'Gawabawa', 28),
(704, 'Goronyo', 28),
(705, 'Gudu', 28),
(706, 'Illela', 28),
(707, 'Isa', 28),
(708, 'Kebbe', 28),
(709, 'Kware', 28),
(710, 'Rabah', 28),
(711, 'Sabon Birni', 28),
(712, 'Shagari', 28),
(713, 'Silame', 28),
(714, 'Sokoto North', 28),
(715, 'Sokoto South', 28),
(716, 'Tambuwal', 28),
(717, 'Tangaza', 28),
(718, 'Tureta', 28),
(719, 'Wamako', 28),
(720, 'Wurno', 28),
(721, 'Yabo', 28),
(722, 'Ardo-Kola', 29),
(723, 'Bali', 29),
(724, 'Cassol', 29),
(725, 'Donga', 29),
(726, 'Gashaka', 29),
(727, 'Ibi', 29),
(728, 'Jalingo', 29),
(729, 'Karin-Lamido', 29),
(730, 'Kurmi', 29),
(731, 'Lau', 29),
(732, 'Sardauna', 29),
(733, 'Takum', 29),
(734, 'Ussa', 29),
(735, 'Wukari', 29),
(736, 'Yorro', 29),
(737, 'Zing', 29),
(738, 'Bade', 30),
(739, 'Bursari', 30),
(740, 'Damaturu', 30),
(741, 'Fika', 30),
(742, 'Fune', 30),
(743, 'Geidam', 30),
(744, 'Gujba', 30),
(745, 'Gulani', 30),
(746, 'Jakusko', 30),
(747, 'Karasuwa', 30),
(748, 'Karawa', 30),
(749, 'Machina', 30),
(750, 'Nangere', 30),
(751, 'Nguru Potiskum', 30),
(752, 'Tarmua', 30),
(753, 'Yunusari', 30),
(754, 'Yusufari', 30),
(755, 'Anka ', 35),
(756, 'Bakura', 35),
(757, 'Birnin Magaji', 35),
(758, 'Bukkuyum', 35),
(759, 'Bungudu', 35),
(760, 'Gummi', 35),
(761, 'Gusau', 35),
(762, 'Kaura', 35),
(763, 'Maradun', 35),
(764, 'Maru', 35),
(765, 'Namoda', 35),
(766, 'Shinkafi', 35),
(767, 'Talata Mafara', 35),
(768, 'Tsafe', 35),
(769, 'Zurmi ', 35),
(770, 'Akoko Edo', 10),
(771, 'Etsako West', 10),
(772, 'Potiskum', 30),
(773, 'Owan East', 10),
(774, 'Ilorin South', 19),
(775, 'Kazaure', 13),
(776, 'Gamawa', 5),
(777, 'Owan West', 10),
(778, 'Awgu', 11),
(779, 'Ogbomosho-North', 25),
(780, 'Yamaltu Deba', 33);

-- --------------------------------------------------------

--
-- Table structure for table `marital_statuses`
--

DROP TABLE IF EXISTS `marital_statuses`;
CREATE TABLE IF NOT EXISTS `marital_statuses` (
  `marital_status_id` int(10) unsigned NOT NULL,
  `marital_status` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `marital_status_abbr` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
  `salutation_id` int(10) unsigned NOT NULL,
  `salutation` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `salutation_abbr` varchar(15) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
  `school_database_id` int(10) unsigned NOT NULL,
  `host` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `database` varchar(225) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `schools_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `schools`
--

DROP TABLE IF EXISTS `schools`;
CREATE TABLE IF NOT EXISTS `schools` (
  `schools_id` int(10) unsigned NOT NULL,
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
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `schools`
--

INSERT INTO `schools` (`schools_id`, `name`, `full_name`, `phone_no`, `email`, `motto`, `website`, `address`, `logo`, `admin_id`, `status_id`, `created_at`, `updated_at`) VALUES
(1, 'Solid Steps', 'Solid Steps Memorial High', '02830374944', 'solid@steps.high', '', 'www.solidsteps.international', 'Ekotun Egbe, Lagos', '1_logo.png', NULL, 1, '2016-04-17 15:18:14', '2016-04-17 15:18:14'),
(2, 'Jokers', 'Douche Bag', '01893044554', 'joker@douche.bag', 'Light is Power', 'www.joker.douche', 'Malawi.com', '2_logo.jpg', NULL, 2, '2016-04-17 14:57:27', '2016-04-17 15:48:05'),
(3, 'SolidSteps', 'Solid Steps International School', '+2348061539278', 'nondefyde@gmail.com', 'taking solid steps to our vision', 'www.solidsteps.com', '4 ikuna Street Liasu Rd.', '3_logo.png', NULL, 1, '2016-04-19 11:42:16', '2016-04-19 11:43:29');

-- --------------------------------------------------------

--
-- Table structure for table `states`
--

DROP TABLE IF EXISTS `states`;
CREATE TABLE IF NOT EXISTS `states` (
  `state_id` int(3) unsigned NOT NULL,
  `state` varchar(30) DEFAULT NULL,
  `state_code` varchar(5) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `states`
--

INSERT INTO `states` (`state_id`, `state`, `state_code`) VALUES
(1, 'Abia', 'ABI\r'),
(2, 'Adamawa', 'ADA\r'),
(3, 'Akwa Ibom', 'AKW\r'),
(4, 'Anambra', 'ANA\r'),
(5, 'Bauchi', 'BAU\r'),
(6, 'Benue', 'BEN\r'),
(7, 'Borno', 'BOR\r'),
(8, 'Cross-River', 'CRO\r'),
(9, 'Delta', 'DEL\r'),
(10, 'Edo', 'EDO\r'),
(11, 'Enugu', 'ENU\r'),
(12, 'Imo', 'IMO\r'),
(13, 'Jigawa', 'JIG\r'),
(14, 'Kebbi', 'KEB\r'),
(15, 'Kaduna', 'KAD\r'),
(16, 'Kogi', 'KOG\r'),
(17, 'Kano', 'KAN\r'),
(18, 'Katsina', 'KAT\r'),
(19, 'Kwara', 'KWA\r'),
(20, 'Lagos', 'LAG\r'),
(21, 'Niger', 'NIG\r'),
(22, 'Ondo', 'OND\r'),
(23, 'Ogun', 'OGU\r'),
(24, 'Osun', 'OSU\r'),
(25, 'Oyo', 'OYO\r'),
(26, 'Plateau', 'PLA\r'),
(27, 'Rivers', 'RIV\r'),
(28, 'Sokoto', 'SOK\r'),
(29, 'Taraba', 'TAR\r'),
(30, 'Yobe', 'YOB\r'),
(31, 'FCT', 'FCT\r'),
(32, 'Bayelsa', 'BAY\r'),
(33, 'Gombe', 'GOM\r'),
(34, 'Nasarawa', 'NAS\r'),
(35, 'Zamfara', 'ZAM\r'),
(36, 'Ekiti', 'EKI\r'),
(37, 'Ebonyi', 'EBO\r');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `lgas`
--
ALTER TABLE `lgas`
  ADD PRIMARY KEY (`lga_id`),
  ADD KEY `state_id` (`state_id`);

--
-- Indexes for table `marital_statuses`
--
ALTER TABLE `marital_statuses`
  ADD PRIMARY KEY (`marital_status_id`);

--
-- Indexes for table `salutations`
--
ALTER TABLE `salutations`
  ADD PRIMARY KEY (`salutation_id`);

--
-- Indexes for table `school_databases`
--
ALTER TABLE `school_databases`
  ADD PRIMARY KEY (`school_database_id`),
  ADD KEY `school_databases_schools_id_index` (`schools_id`);

--
-- Indexes for table `schools`
--
ALTER TABLE `schools`
  ADD PRIMARY KEY (`schools_id`),
  ADD KEY `status_id` (`status_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `states`
--
ALTER TABLE `states`
  ADD PRIMARY KEY (`state_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `lgas`
--
ALTER TABLE `lgas`
  MODIFY `lga_id` int(3) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=781;
--
-- AUTO_INCREMENT for table `marital_statuses`
--
ALTER TABLE `marital_statuses`
  MODIFY `marital_status_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `salutations`
--
ALTER TABLE `salutations`
  MODIFY `salutation_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `school_databases`
--
ALTER TABLE `school_databases`
  MODIFY `school_database_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `schools`
--
ALTER TABLE `schools`
  MODIFY `schools_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `states`
--
ALTER TABLE `states`
  MODIFY `state_id` int(3) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=38;--
-- Database: `solid_steps`
--
DROP DATABASE `solid_steps`;
CREATE DATABASE IF NOT EXISTS `solid_steps` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `solid_steps`;

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
  `type` tinyint(4) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
  `menu_item_id` int(10) unsigned NOT NULL,
  `menu_item` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `menu_item_url` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `menu_item_icon` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `active` int(10) unsigned NOT NULL DEFAULT '1',
  `sequence` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '1',
  `menu_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
  `menu_id` int(10) unsigned NOT NULL,
  `menu` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `menu_url` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `active` int(10) unsigned NOT NULL DEFAULT '1',
  `sequence` int(10) unsigned NOT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '1',
  `icon` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `menu_header_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
  `role_id` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `role_user`
--

INSERT INTO `role_user` (`user_id`, `role_id`) VALUES
(1, 1);

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
(1, 'developer', 'Developer', 'The software developer', 2, '2016-03-29 23:30:11', '2016-03-31 13:09:43'),
(2, 'super_admin', 'Super Admin', 'System Administrator', 1, '2016-03-30 10:51:57', '2016-03-31 13:08:59'),
(3, 'sponsor', 'Sponsor', 'Sponsor', 3, '2016-04-16 18:25:54', '2016-04-16 18:25:54'),
(4, 'staff', 'Staff', 'Staff', 4, '2016-04-16 18:25:54', '2016-04-16 18:25:54');

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
(2, 4);

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
(2, 6);

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
(1, 9);

-- --------------------------------------------------------

--
-- Table structure for table `sponsors`
--

DROP TABLE IF EXISTS `sponsors`;
CREATE TABLE IF NOT EXISTS `sponsors` (
  `sponsor_id` int(10) unsigned NOT NULL,
  `sponsor_no` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `first_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `other_name` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `dob` date DEFAULT NULL,
  `phone_no` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone_no2` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8_unicode_ci,
  `lga_id` tinyint(4) DEFAULT NULL,
  `salutation_id` tinyint(4) DEFAULT NULL,
  `created_by` int(10) NOT NULL,
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
  `email` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `dob` date DEFAULT NULL,
  `gender` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone_no` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone_no2` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8_unicode_ci,
  `lga_id` tinyint(4) DEFAULT NULL,
  `salutation_id` tinyint(4) DEFAULT NULL,
  `created_by` int(10) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `staffs`
--

INSERT INTO `staffs` (`staff_id`, `staff_no`, `first_name`, `other_name`, `email`, `dob`, `gender`, `phone_no`, `phone_no2`, `address`, `lga_id`, `salutation_id`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'STF00001', 'John', 'Doe', 'admin@gmail.com', '1994-03-16', 'Female', '01923893484', '', 'Ahmadu Bello Way, Kaduna, Kaduna State', 127, 3, 1, '2016-04-21 19:41:55', '2016-04-21 20:40:01');

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
  `type` tinyint(4) NOT NULL DEFAULT '1',
  `menu_item_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
  `sub_most_menu_item_id` int(10) unsigned NOT NULL,
  `sub_most_menu_item` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `sub_most_menu_item_url` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `sub_most_menu_item_icon` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `active` int(10) unsigned NOT NULL DEFAULT '1',
  `sequence` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '1',
  `sub_menu_item_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
  `user_id` int(10) unsigned NOT NULL,
  `username` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `display_name` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `avatar` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_type_id` int(10) unsigned NOT NULL,
  `verified` int(10) unsigned NOT NULL DEFAULT '0',
  `status` int(10) unsigned NOT NULL DEFAULT '1',
  `verification_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `display_name`, `password`, `email`, `avatar`, `user_type_id`, `verified`, `status`, `verification_code`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'STF00001', 'John Doe', '$2y$10$J6VM0.ySq0icTRaDtXjjI.i7MWJy6UUlPDgmJ3ygFUxDxJ/MeAk5G', 'admin@gmail.com', '1_avatar.jpg', 1, 1, 1, NULL, 'uPMv7hYCF6NeQ9xn6dFrzzaOOi69VWYqLtoGayAYcdUF588IVceA6zUzhdcC', NULL, '2016-04-21 20:40:01');

--
-- Indexes for dumped tables
--

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
-- Indexes for table `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`menu_id`),
  ADD KEY `menus_menu_header_id_index` (`menu_header_id`);

--
-- Indexes for table `permission_role`
--
ALTER TABLE `permission_role`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `permission_role_role_id_foreign` (`role_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`permission_id`),
  ADD UNIQUE KEY `permissions_name_unique` (`name`);

--
-- Indexes for table `role_user`
--
ALTER TABLE `role_user`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `role_user_role_id_foreign` (`role_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `roles_name_unique` (`name`),
  ADD KEY `roles_user_type_id_index` (`user_type_id`);

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
-- Indexes for table `roles_menus`
--
ALTER TABLE `roles_menus`
  ADD KEY `roles_menus_role_id_index` (`role_id`),
  ADD KEY `roles_menus_menu_id_index` (`menu_id`);

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
-- Indexes for table `sponsors`
--
ALTER TABLE `sponsors`
  ADD PRIMARY KEY (`sponsor_id`),
  ADD KEY `sponsor_no` (`sponsor_no`),
  ADD KEY `lga_id` (`lga_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `salutation_id` (`salutation_id`);

--
-- Indexes for table `staffs`
--
ALTER TABLE `staffs`
  ADD PRIMARY KEY (`staff_id`),
  ADD UNIQUE KEY `staff_no` (`staff_no`),
  ADD KEY `lga_id` (`lga_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `salutation_id` (`salutation_id`);

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
-- Indexes for table `user_types`
--
ALTER TABLE `user_types`
  ADD PRIMARY KEY (`user_type_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_user_type_id_index` (`user_type_id`),
  ADD KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `menu_headers`
--
ALTER TABLE `menu_headers`
  MODIFY `menu_header_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `menu_item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `menus`
--
ALTER TABLE `menus`
  MODIFY `menu_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
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
  MODIFY `staff_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `sub_menu_items`
--
ALTER TABLE `sub_menu_items`
  MODIFY `sub_menu_item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT for table `sub_most_menu_items`
--
ALTER TABLE `sub_most_menu_items`
  MODIFY `sub_most_menu_item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `user_types`
--
ALTER TABLE `user_types`
  MODIFY `user_type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
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
