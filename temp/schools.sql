-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Apr 29, 2016 at 08:31 AM
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
  `lga_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lga` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `state_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`lga_id`),
  KEY `lgas_state_id_index` (`state_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=781 ;

--
-- Dumping data for table `lgas`
--

INSERT INTO `lgas` (`lga_id`, `lga`, `state_id`, `created_at`, `updated_at`) VALUES
(1, 'Aba North', 1, NULL, NULL),
(2, 'Aba South', 1, NULL, NULL),
(3, 'Arochukwu', 1, NULL, NULL),
(4, 'Bende', 1, NULL, NULL),
(5, 'Ikwuano', 1, NULL, NULL),
(6, 'Isiala-Ngwa North', 1, NULL, NULL),
(7, 'Isiala-Ngwa South', 1, NULL, NULL),
(8, 'Isuikwato', 1, NULL, NULL),
(9, 'Ngwa', 1, NULL, NULL),
(10, 'Obi Nwa', 1, NULL, NULL),
(11, 'Ohafia', 1, NULL, NULL),
(12, 'Osisioma', 1, NULL, NULL),
(13, 'Ugwunagbo', 1, NULL, NULL),
(14, 'Ukwa East', 1, NULL, NULL),
(15, 'Ukwa West', 1, NULL, NULL),
(16, 'Umuahia North', 1, NULL, NULL),
(17, 'Umuahia South', 1, NULL, NULL),
(18, 'Umu-Neochi', 1, NULL, NULL),
(19, 'Demsa', 2, NULL, NULL),
(20, 'Fufore', 2, NULL, NULL),
(21, 'Ganaye', 2, NULL, NULL),
(22, 'Gireri', 2, NULL, NULL),
(23, 'Gombi', 2, NULL, NULL),
(24, 'Guyuk', 2, NULL, NULL),
(25, 'Hong', 2, NULL, NULL),
(26, 'Jada', 2, NULL, NULL),
(27, 'Lamurde', 2, NULL, NULL),
(28, 'Madagali', 2, NULL, NULL),
(29, 'Maiha ', 2, NULL, NULL),
(30, 'Mayo-Belwa', 2, NULL, NULL),
(31, 'Michika', 2, NULL, NULL),
(32, 'Mubi North', 2, NULL, NULL),
(33, 'Mubi South', 2, NULL, NULL),
(34, 'Numan', 2, NULL, NULL),
(35, 'Shelleng', 2, NULL, NULL),
(36, 'Song', 2, NULL, NULL),
(37, 'Toungo', 2, NULL, NULL),
(38, 'Yola North', 2, NULL, NULL),
(39, 'Yola South', 2, NULL, NULL),
(40, 'Abak', 3, NULL, NULL),
(41, 'Eastern Obolo', 3, NULL, NULL),
(42, 'Eket', 3, NULL, NULL),
(43, 'Esit Eket', 3, NULL, NULL),
(44, 'Essien Udim', 3, NULL, NULL),
(45, 'Etim Ekpo', 3, NULL, NULL),
(46, 'Etinan', 3, NULL, NULL),
(47, 'Ibeno', 3, NULL, NULL),
(48, 'Ibesikpo Asutan', 3, NULL, NULL),
(49, 'Ibiono Ibom', 3, NULL, NULL),
(50, 'Ika', 3, NULL, NULL),
(51, 'Ikono', 3, NULL, NULL),
(52, 'Ikot Abasi', 3, NULL, NULL),
(53, 'Ikot Ekpene', 3, NULL, NULL),
(54, 'Ini', 3, NULL, NULL),
(55, 'Itu', 3, NULL, NULL),
(56, 'Mbo', 3, NULL, NULL),
(57, 'Mkpat Enin', 3, NULL, NULL),
(58, 'Nsit Atai', 3, NULL, NULL),
(59, 'Nsit Ibom', 3, NULL, NULL),
(60, 'Nsit Ubium', 3, NULL, NULL),
(61, 'Obot Akara', 3, NULL, NULL),
(62, 'Okobo', 3, NULL, NULL),
(63, 'Onna', 3, NULL, NULL),
(64, 'Oron ', 3, NULL, NULL),
(65, 'Oruk Anam', 3, NULL, NULL),
(66, 'Udung Uko', 3, NULL, NULL),
(67, 'Ukanafun', 3, NULL, NULL),
(68, 'Uruan', 3, NULL, NULL),
(69, 'Urue-Offong/Oruko', 3, NULL, NULL),
(70, 'Uyo', 3, NULL, NULL),
(71, 'Aguata', 4, NULL, NULL),
(72, 'Anambra East', 4, NULL, NULL),
(73, 'Anambra West', 4, NULL, NULL),
(74, 'Anaocha', 4, NULL, NULL),
(75, 'Awka North', 4, NULL, NULL),
(76, 'Awka South', 4, NULL, NULL),
(77, 'Ayamelum', 4, NULL, NULL),
(78, 'Dunukofia', 4, NULL, NULL),
(79, 'Ekwusigo', 4, NULL, NULL),
(80, 'Idemili North', 4, NULL, NULL),
(81, 'Idemili South', 4, NULL, NULL),
(82, 'Ihiala', 4, NULL, NULL),
(83, 'Njikoka', 4, NULL, NULL),
(84, 'Nnewi North', 4, NULL, NULL),
(85, 'Nnewi South', 4, NULL, NULL),
(86, 'Ogbaru', 4, NULL, NULL),
(87, 'Onitsha North', 4, NULL, NULL),
(88, 'Onitsha South', 4, NULL, NULL),
(89, 'Orumba North', 4, NULL, NULL),
(90, 'Orumba South', 4, NULL, NULL),
(91, 'Oyi ', 4, NULL, NULL),
(92, 'Alkaleri', 5, NULL, NULL),
(93, 'Bauchi', 5, NULL, NULL),
(94, 'Bogoro', 5, NULL, NULL),
(95, 'Damban', 5, NULL, NULL),
(96, 'Darazo', 5, NULL, NULL),
(97, 'Dass', 5, NULL, NULL),
(98, 'Ganjuwa', 5, NULL, NULL),
(99, 'Giade', 5, NULL, NULL),
(100, 'Itas/Gadau', 5, NULL, NULL),
(101, 'Jama''Are', 5, NULL, NULL),
(102, 'Katagum', 5, NULL, NULL),
(103, 'Kirfi', 5, NULL, NULL),
(104, 'Misau', 5, NULL, NULL),
(105, 'Ningi', 5, NULL, NULL),
(106, 'Shira', 5, NULL, NULL),
(107, 'Tafawa-Balewa', 5, NULL, NULL),
(108, 'Toro', 5, NULL, NULL),
(109, 'Warji', 5, NULL, NULL),
(110, 'Zaki ', 5, NULL, NULL),
(111, 'Brass', 32, NULL, NULL),
(112, 'Ekeremor', 32, NULL, NULL),
(113, 'Kolokuma/Opokuma', 32, NULL, NULL),
(114, 'Nembe', 32, NULL, NULL),
(115, 'Ogbia', 32, NULL, NULL),
(116, 'Sagbama', 32, NULL, NULL),
(117, 'Southern Jaw', 32, NULL, NULL),
(118, 'Yenegoa ', 32, NULL, NULL),
(119, 'Ado', 6, NULL, NULL),
(120, 'Agatu', 6, NULL, NULL),
(121, 'Apa', 6, NULL, NULL),
(122, 'Buruku', 6, NULL, NULL),
(123, 'Gboko', 6, NULL, NULL),
(124, 'Guma', 6, NULL, NULL),
(125, 'Gwer East', 6, NULL, NULL),
(126, 'Gwer West', 6, NULL, NULL),
(127, 'Katsina-Ala', 6, NULL, NULL),
(128, 'Konshisha', 6, NULL, NULL),
(129, 'Kwande', 6, NULL, NULL),
(130, 'Logo', 6, NULL, NULL),
(131, 'Makurdi', 6, NULL, NULL),
(132, 'Obi', 6, NULL, NULL),
(133, 'Ogbadibo', 6, NULL, NULL),
(134, 'Ohimini', 6, NULL, NULL),
(135, 'Oju', 6, NULL, NULL),
(136, 'Okpokwu', 6, NULL, NULL),
(137, 'Oturkpo', 6, NULL, NULL),
(138, 'Tarka', 6, NULL, NULL),
(139, 'Ukum', 6, NULL, NULL),
(140, 'Ushongo', 6, NULL, NULL),
(141, 'Vandeikya ', 6, NULL, NULL),
(142, 'Abadam', 7, NULL, NULL),
(143, 'Askira/Uba', 7, NULL, NULL),
(144, 'Bama', 7, NULL, NULL),
(145, 'Bayo', 7, NULL, NULL),
(146, 'Biu', 7, NULL, NULL),
(147, 'Chibok', 7, NULL, NULL),
(148, 'Damboa', 7, NULL, NULL),
(149, 'Dikwa', 7, NULL, NULL),
(150, 'Gubio', 7, NULL, NULL),
(151, 'Guzamala', 7, NULL, NULL),
(152, 'Gwoza', 7, NULL, NULL),
(153, 'Hawul', 7, NULL, NULL),
(154, 'Jere', 7, NULL, NULL),
(155, 'Kaga', 7, NULL, NULL),
(156, 'Kala/Balge', 7, NULL, NULL),
(157, 'Konduga', 7, NULL, NULL),
(158, 'Kukawa', 7, NULL, NULL),
(159, 'Kwaya Kusar', 7, NULL, NULL),
(160, 'Mafa', 7, NULL, NULL),
(161, 'Magumeri', 7, NULL, NULL),
(162, 'Maiduguri', 7, NULL, NULL),
(163, 'Marte', 7, NULL, NULL),
(164, 'Mobbar', 7, NULL, NULL),
(165, 'Monguno', 7, NULL, NULL),
(166, 'Ngala', 7, NULL, NULL),
(167, 'Nganzai', 7, NULL, NULL),
(168, 'Shani ', 7, NULL, NULL),
(169, 'Abi', 8, NULL, NULL),
(170, 'Akamkpa', 8, NULL, NULL),
(171, 'Akpabuyo', 8, NULL, NULL),
(172, 'Bakassi', 8, NULL, NULL),
(173, 'Bekwara', 8, NULL, NULL),
(174, 'Biase', 8, NULL, NULL),
(175, 'Boki', 8, NULL, NULL),
(176, 'Calabar Municipality', 8, NULL, NULL),
(177, 'Calabar South', 8, NULL, NULL),
(178, 'Etung', 8, NULL, NULL),
(179, 'Ikom', 8, NULL, NULL),
(180, 'Obanliku', 8, NULL, NULL),
(181, 'Obudu', 8, NULL, NULL),
(182, 'Odubra', 8, NULL, NULL),
(183, 'Odukpani', 8, NULL, NULL),
(184, 'Ogoja', 8, NULL, NULL),
(185, 'Yala', 8, NULL, NULL),
(186, 'Yarkur', 8, NULL, NULL),
(187, 'Aniocha', 9, NULL, NULL),
(188, 'Aniocha South', 9, NULL, NULL),
(189, 'Bomadi', 9, NULL, NULL),
(190, 'Burutu', 9, NULL, NULL),
(191, 'Ethiope East', 9, NULL, NULL),
(192, 'Ethiope West', 9, NULL, NULL),
(193, 'Ika North-East', 9, NULL, NULL),
(194, 'Ika South', 9, NULL, NULL),
(195, 'Isoko North', 9, NULL, NULL),
(196, 'Isoko South', 9, NULL, NULL),
(197, 'Ndokwa East', 9, NULL, NULL),
(198, 'Ndokwa West', 9, NULL, NULL),
(199, 'Okpe', 9, NULL, NULL),
(200, 'Oshimili', 9, NULL, NULL),
(201, 'Oshimili North', 9, NULL, NULL),
(202, 'Patani', 9, NULL, NULL),
(203, 'Sapele', 9, NULL, NULL),
(204, 'Udu', 9, NULL, NULL),
(205, 'Ughelli North', 9, NULL, NULL),
(206, 'Ughelli South', 9, NULL, NULL),
(207, 'Ukwani', 9, NULL, NULL),
(208, 'Uvwie', 9, NULL, NULL),
(209, 'Warri Central', 9, NULL, NULL),
(210, 'Warri North', 9, NULL, NULL),
(211, 'Warri South', 9, NULL, NULL),
(212, 'Abakaliki', 37, NULL, NULL),
(213, 'Afikpo North', 37, NULL, NULL),
(214, 'Afikpo South', 37, NULL, NULL),
(215, 'Ebonyi', 37, NULL, NULL),
(216, 'Ezza', 37, NULL, NULL),
(217, 'Ezza South', 37, NULL, NULL),
(218, 'Ishielu', 37, NULL, NULL),
(219, 'Ivo ', 37, NULL, NULL),
(220, 'Lkwo', 37, NULL, NULL),
(221, 'Ohaozara', 37, NULL, NULL),
(222, 'Ohaukwu', 37, NULL, NULL),
(223, 'Onicha', 37, NULL, NULL),
(224, 'Central', 10, NULL, NULL),
(225, 'Egor', 10, NULL, NULL),
(226, 'Esan Central', 10, NULL, NULL),
(227, 'Esan North-East', 10, NULL, NULL),
(228, 'Esan South-East ', 10, NULL, NULL),
(229, 'Esan West', 10, NULL, NULL),
(230, 'Etsako Central', 10, NULL, NULL),
(231, 'Etsako East ', 10, NULL, NULL),
(232, 'Igueben', 10, NULL, NULL),
(233, 'Oredo', 10, NULL, NULL),
(234, 'Orhionwon', 10, NULL, NULL),
(235, 'Ovia South-East', 10, NULL, NULL),
(236, 'Ovia Southwest', 10, NULL, NULL),
(237, 'Uhunmwonde', 10, NULL, NULL),
(238, 'Ukpoba', 10, NULL, NULL),
(239, 'Ado', 36, NULL, NULL),
(240, 'Efon', 36, NULL, NULL),
(241, 'Ekiti South-West', 36, NULL, NULL),
(242, 'Ekiti-East', 36, NULL, NULL),
(243, 'Ekiti-West ', 36, NULL, NULL),
(244, 'Emure/Ise/Orun', 36, NULL, NULL),
(245, 'Gbonyin', 36, NULL, NULL),
(246, 'Ido/Osi', 36, NULL, NULL),
(247, 'Ijero', 36, NULL, NULL),
(248, 'Ikare', 36, NULL, NULL),
(249, 'Ikole', 36, NULL, NULL),
(250, 'Ilejemeje.', 36, NULL, NULL),
(251, 'Irepodun', 36, NULL, NULL),
(252, 'Ise/Orun ', 36, NULL, NULL),
(253, 'Moba', 36, NULL, NULL),
(254, 'Oye', 36, NULL, NULL),
(255, 'Aninri', 11, NULL, NULL),
(256, 'Enugu Eas', 11, NULL, NULL),
(257, 'Enugu North', 11, NULL, NULL),
(258, 'Enugu South', 0, NULL, NULL),
(259, 'Ezeagu', 11, NULL, NULL),
(260, 'Igbo-Ekiti', 11, NULL, NULL),
(261, 'Igboeze North', 11, NULL, NULL),
(262, 'Igbo-Eze South', 11, NULL, NULL),
(263, 'Isi-Uzo', 11, NULL, NULL),
(264, 'Nkanu', 11, NULL, NULL),
(265, 'Nkanu East', 11, NULL, NULL),
(266, 'Nsukka', 11, NULL, NULL),
(267, 'Oji-River', 11, NULL, NULL),
(268, 'Udenu. ', 11, NULL, NULL),
(269, 'Udi Agwu', 11, NULL, NULL),
(270, 'Uzo-Uwani', 11, NULL, NULL),
(271, 'Abaji', 31, NULL, NULL),
(272, 'Abuja Municipal', 31, NULL, NULL),
(273, 'Bwari', 31, NULL, NULL),
(274, 'Gwagwalada', 31, NULL, NULL),
(275, 'Kuje', 31, NULL, NULL),
(276, 'Kwali', 31, NULL, NULL),
(277, 'Akko', 33, NULL, NULL),
(278, 'Balanga', 33, NULL, NULL),
(279, 'Billiri', 33, NULL, NULL),
(280, 'Dukku', 33, NULL, NULL),
(281, 'Funakaye', 33, NULL, NULL),
(282, 'Gombe', 33, NULL, NULL),
(283, 'Kaltungo', 33, NULL, NULL),
(284, 'Kwami', 33, NULL, NULL),
(285, 'Nafada/Bajoga ', 33, NULL, NULL),
(286, 'Shomgom', 33, NULL, NULL),
(287, 'Yamaltu/Delta. ', 33, NULL, NULL),
(288, 'Aboh-Mbaise', 12, NULL, NULL),
(289, 'Ahiazu-Mbaise', 12, NULL, NULL),
(290, 'Ehime-Mbano', 12, NULL, NULL),
(291, 'Ezinihitte', 12, NULL, NULL),
(292, 'Ideato North', 12, NULL, NULL),
(293, 'Ideato South', 12, NULL, NULL),
(294, 'Ihitte/Uboma', 12, NULL, NULL),
(295, 'Ikeduru', 12, NULL, NULL),
(296, 'Isiala Mbano', 12, NULL, NULL),
(297, 'Isu', 12, NULL, NULL),
(298, 'Mbaitoli', 12, NULL, NULL),
(299, 'Mbaitoli', 12, NULL, NULL),
(300, 'Ngor-Okpala', 12, NULL, NULL),
(301, 'Njaba', 12, NULL, NULL),
(302, 'Nkwerre', 12, NULL, NULL),
(303, 'Nwangele', 12, NULL, NULL),
(304, 'Obowo', 12, NULL, NULL),
(305, 'Oguta', 12, NULL, NULL),
(306, 'Ohaji/Egbema', 12, NULL, NULL),
(307, 'Okigwe', 12, NULL, NULL),
(308, 'Orlu', 12, NULL, NULL),
(309, 'Orsu', 12, NULL, NULL),
(310, 'Oru East', 12, NULL, NULL),
(311, 'Oru West', 12, NULL, NULL),
(312, 'Owerri North', 12, NULL, NULL),
(313, 'Owerri West ', 12, NULL, NULL),
(314, 'Owerri-Municipal', 12, NULL, NULL),
(315, 'Auyo', 13, NULL, NULL),
(316, 'Babura', 13, NULL, NULL),
(317, 'Biriniwa', 13, NULL, NULL),
(318, 'Birni Kudu', 13, NULL, NULL),
(319, 'Buji', 13, NULL, NULL),
(320, 'Dutse', 13, NULL, NULL),
(321, 'Gagarawa', 13, NULL, NULL),
(322, 'Garki', 13, NULL, NULL),
(323, 'Gumel', 13, NULL, NULL),
(324, 'Guri', 13, NULL, NULL),
(325, 'Gwaram', 13, NULL, NULL),
(326, 'Gwiwa', 13, NULL, NULL),
(327, 'Hadejia', 13, NULL, NULL),
(328, 'Jahun', 13, NULL, NULL),
(329, 'Kafin Hausa', 13, NULL, NULL),
(330, 'Kaugama Kazaure', 13, NULL, NULL),
(331, 'Kiri Kasamma', 13, NULL, NULL),
(332, 'Kiyawa', 13, NULL, NULL),
(333, 'Maigatari', 13, NULL, NULL),
(334, 'Malam Madori', 13, NULL, NULL),
(335, 'Miga', 13, NULL, NULL),
(336, 'Ringim', 13, NULL, NULL),
(337, 'Roni', 13, NULL, NULL),
(338, 'Sule-Tankarkar', 13, NULL, NULL),
(339, 'Taura ', 13, NULL, NULL),
(340, 'Yankwashi ', 13, NULL, NULL),
(341, 'Birni-Gwari', 15, NULL, NULL),
(342, 'Chikun', 15, NULL, NULL),
(343, 'Giwa', 15, NULL, NULL),
(344, 'Igabi', 15, NULL, NULL),
(345, 'Ikara', 15, NULL, NULL),
(346, 'Jaba', 15, NULL, NULL),
(347, 'Jema''A', 15, NULL, NULL),
(348, 'Kachia', 15, NULL, NULL),
(349, 'Kaduna North', 15, NULL, NULL),
(350, 'Kaduna South', 15, NULL, NULL),
(351, 'Kagarko', 15, NULL, NULL),
(352, 'Kajuru', 15, NULL, NULL),
(353, 'Kaura', 15, NULL, NULL),
(354, 'Kauru', 15, NULL, NULL),
(355, 'Kubau', 15, NULL, NULL),
(356, 'Kudan', 15, NULL, NULL),
(357, 'Lere', 15, NULL, NULL),
(358, 'Makarfi', 15, NULL, NULL),
(359, 'Sabon-Gari', 15, NULL, NULL),
(360, 'Sanga', 15, NULL, NULL),
(361, 'Soba', 15, NULL, NULL),
(362, 'Zango-Kataf', 15, NULL, NULL),
(363, 'Zaria ', 15, NULL, NULL),
(364, 'Ajingi', 17, NULL, NULL),
(365, 'Albasu', 17, NULL, NULL),
(366, 'Bagwai', 17, NULL, NULL),
(367, 'Bebeji', 17, NULL, NULL),
(368, 'Bichi', 17, NULL, NULL),
(369, 'Bunkure', 17, NULL, NULL),
(370, 'Dala', 17, NULL, NULL),
(371, 'Dambatta', 17, NULL, NULL),
(372, 'Dawakin Kudu', 17, NULL, NULL),
(373, 'Dawakin Tofa', 17, NULL, NULL),
(374, 'Doguwa', 17, NULL, NULL),
(375, 'Fagge', 17, NULL, NULL),
(376, 'Gabasawa', 17, NULL, NULL),
(377, 'Garko', 17, NULL, NULL),
(378, 'Garum', 17, NULL, NULL),
(379, 'Gaya', 17, NULL, NULL),
(380, 'Gezawa', 17, NULL, NULL),
(381, 'Gwale', 17, NULL, NULL),
(382, 'Gwarzo', 17, NULL, NULL),
(383, 'Kabo', 17, NULL, NULL),
(384, 'Kano Municipal', 17, NULL, NULL),
(385, 'Karaye', 17, NULL, NULL),
(386, 'Kibiya', 17, NULL, NULL),
(387, 'Kiru', 17, NULL, NULL),
(388, 'Kumbotso', 17, NULL, NULL),
(389, 'Kunchi', 17, NULL, NULL),
(390, 'Kura', 17, NULL, NULL),
(391, 'Madobi', 17, NULL, NULL),
(392, 'Makoda', 17, NULL, NULL),
(393, 'Mallam', 17, NULL, NULL),
(394, 'Minjibir', 17, NULL, NULL),
(395, 'Nasarawa', 17, NULL, NULL),
(396, 'Rano', 17, NULL, NULL),
(397, 'Rimin Gado', 17, NULL, NULL),
(398, 'Rogo', 17, NULL, NULL),
(399, 'Shanono', 17, NULL, NULL),
(400, 'Sumaila', 17, NULL, NULL),
(401, 'Takali', 17, NULL, NULL),
(402, 'Tarauni', 17, NULL, NULL),
(403, 'Tofa', 17, NULL, NULL),
(404, 'Tsanyawa', 17, NULL, NULL),
(405, 'Tudun Wada', 17, NULL, NULL),
(406, 'Ungogo', 17, NULL, NULL),
(407, 'Warawa', 17, NULL, NULL),
(408, 'Wudil', 17, NULL, NULL),
(409, 'Bakori', 18, NULL, NULL),
(410, 'Batagarawa', 18, NULL, NULL),
(411, 'Batsari', 18, NULL, NULL),
(412, 'Baure', 18, NULL, NULL),
(413, 'Bindawa', 18, NULL, NULL),
(414, 'Charanchi', 18, NULL, NULL),
(415, 'Dan Musa', 18, NULL, NULL),
(416, 'Dandume', 18, NULL, NULL),
(417, 'Danja', 18, NULL, NULL),
(418, 'Daura', 18, NULL, NULL),
(419, 'Dutsi', 18, NULL, NULL),
(420, 'Dutsin-Ma', 18, NULL, NULL),
(421, 'Faskari', 18, NULL, NULL),
(422, 'Funtua', 18, NULL, NULL),
(423, 'Ingawa', 18, NULL, NULL),
(424, 'Jibia', 18, NULL, NULL),
(425, 'Kafur', 18, NULL, NULL),
(426, 'Kaita', 18, NULL, NULL),
(427, 'Kankara', 18, NULL, NULL),
(428, 'Kankia', 18, NULL, NULL),
(429, 'Katsina', 18, NULL, NULL),
(430, 'Kurfi', 18, NULL, NULL),
(431, 'Kusada', 18, NULL, NULL),
(432, 'Mai''Adua', 18, NULL, NULL),
(433, 'Malumfashi', 18, NULL, NULL),
(434, 'Mani', 18, NULL, NULL),
(435, 'Mashi', 18, NULL, NULL),
(436, 'Matazuu', 18, NULL, NULL),
(437, 'Musawa', 18, NULL, NULL),
(438, 'Rimi', 18, NULL, NULL),
(439, 'Sabuwa', 18, NULL, NULL),
(440, 'Safana', 18, NULL, NULL),
(441, 'Sandamu', 18, NULL, NULL),
(442, 'Zango ', 18, NULL, NULL),
(443, 'Aleiro', 14, NULL, NULL),
(444, 'Arewa-Dandi', 14, NULL, NULL),
(445, 'Argungu', 14, NULL, NULL),
(446, 'Augie', 14, NULL, NULL),
(447, 'Bagudo', 14, NULL, NULL),
(448, 'Birnin Kebbi', 14, NULL, NULL),
(449, 'Bunza', 14, NULL, NULL),
(450, 'Dandi ', 14, NULL, NULL),
(451, 'Fakai', 14, NULL, NULL),
(452, 'Gwandu', 14, NULL, NULL),
(453, 'Jega', 14, NULL, NULL),
(454, 'Kalgo ', 14, NULL, NULL),
(455, 'Koko/Besse', 14, NULL, NULL),
(456, 'Maiyama', 14, NULL, NULL),
(457, 'Ngaski', 14, NULL, NULL),
(458, 'Sakaba', 14, NULL, NULL),
(459, 'Shanga', 14, NULL, NULL),
(460, 'Suru', 14, NULL, NULL),
(461, 'Wasagu/Danko', 14, NULL, NULL),
(462, 'Yauri', 14, NULL, NULL),
(463, 'Zuru ', 14, NULL, NULL),
(464, 'Adavi', 16, NULL, NULL),
(465, 'Ajaokuta', 16, NULL, NULL),
(466, 'Ankpa', 16, NULL, NULL),
(467, 'Bassa', 16, NULL, NULL),
(468, 'Dekina', 16, NULL, NULL),
(469, 'Ibaji', 16, NULL, NULL),
(470, 'Idah', 16, NULL, NULL),
(471, 'Igalamela-Odolu', 16, NULL, NULL),
(472, 'Ijumu', 16, NULL, NULL),
(473, 'Kabba/Bunu', 16, NULL, NULL),
(474, 'Kogi', 16, NULL, NULL),
(475, 'Lokoja', 16, NULL, NULL),
(476, 'Mopa-Muro', 16, NULL, NULL),
(477, 'Ofu', 16, NULL, NULL),
(478, 'Ogori/Mangongo', 16, NULL, NULL),
(479, 'Okehi', 16, NULL, NULL),
(480, 'Okene', 16, NULL, NULL),
(481, 'Olamabolo', 16, NULL, NULL),
(482, 'Omala', 16, NULL, NULL),
(483, 'Yagba East ', 16, NULL, NULL),
(484, 'Yagba West', 16, NULL, NULL),
(485, 'Asa', 19, NULL, NULL),
(486, 'Baruten', 19, NULL, NULL),
(487, 'Edu', 19, NULL, NULL),
(488, 'Ekiti', 19, NULL, NULL),
(489, 'Ifelodun', 19, NULL, NULL),
(490, 'Ilorin East', 19, NULL, NULL),
(491, 'Ilorin West', 19, NULL, NULL),
(492, 'Irepodun', 19, NULL, NULL),
(493, 'Isin', 19, NULL, NULL),
(494, 'Kaiama', 19, NULL, NULL),
(495, 'Moro', 19, NULL, NULL),
(496, 'Offa', 19, NULL, NULL),
(497, 'Oke-Ero', 19, NULL, NULL),
(498, 'Oyun', 19, NULL, NULL),
(499, 'Pategi ', 19, NULL, NULL),
(500, 'Agege', 20, NULL, NULL),
(501, 'Ajeromi-Ifelodun', 20, NULL, NULL),
(502, 'Alimosho', 20, NULL, NULL),
(503, 'Amuwo-Odofin', 20, NULL, NULL),
(504, 'Apapa', 20, NULL, NULL),
(505, 'Badagry', 20, NULL, NULL),
(506, 'Epe', 20, NULL, NULL),
(507, 'Eti-Osa', 20, NULL, NULL),
(508, 'Ibeju/Lekki', 20, NULL, NULL),
(509, 'Ifako-Ijaye ', 20, NULL, NULL),
(510, 'Ikeja', 20, NULL, NULL),
(511, 'Ikorodu', 20, NULL, NULL),
(512, 'Kosofe', 20, NULL, NULL),
(513, 'Lagos Island', 20, NULL, NULL),
(514, 'Lagos Mainland', 20, NULL, NULL),
(515, 'Mushin', 20, NULL, NULL),
(516, 'Ojo', 20, NULL, NULL),
(517, 'Oshodi-Isolo', 20, NULL, NULL),
(518, 'Shomolu', 20, NULL, NULL),
(519, 'Surulere', 20, NULL, NULL),
(520, 'Akwanga', 34, NULL, NULL),
(521, 'Awe', 34, NULL, NULL),
(522, 'Doma', 34, NULL, NULL),
(523, 'Karu', 34, NULL, NULL),
(524, 'Keana', 34, NULL, NULL),
(525, 'Keffi', 34, NULL, NULL),
(526, 'Kokona', 34, NULL, NULL),
(527, 'Lafia', 34, NULL, NULL),
(528, 'Nasarawa', 34, NULL, NULL),
(529, 'Nasarawa-Eggon', 34, NULL, NULL),
(530, 'Obi', 34, NULL, NULL),
(531, 'Toto', 34, NULL, NULL),
(532, 'Wamba ', 34, NULL, NULL),
(533, 'Agaie', 21, NULL, NULL),
(534, 'Agwara', 21, NULL, NULL),
(535, 'Bida', 21, NULL, NULL),
(536, 'Borgu', 21, NULL, NULL),
(537, 'Bosso', 21, NULL, NULL),
(538, 'Chanchaga', 21, NULL, NULL),
(539, 'Edati', 21, NULL, NULL),
(540, 'Gbako', 21, NULL, NULL),
(541, 'Gurara', 21, NULL, NULL),
(542, 'Katcha', 21, NULL, NULL),
(543, 'Kontagora ', 21, NULL, NULL),
(544, 'Lapai', 21, NULL, NULL),
(545, 'Lavun', 21, NULL, NULL),
(546, 'Magama', 21, NULL, NULL),
(547, 'Mariga', 21, NULL, NULL),
(548, 'Mashegu', 21, NULL, NULL),
(549, 'Mokwa', 21, NULL, NULL),
(550, 'Muya', 21, NULL, NULL),
(551, 'Paikoro', 21, NULL, NULL),
(552, 'Rafi', 21, NULL, NULL),
(553, 'Rijau', 21, NULL, NULL),
(554, 'Shiroro', 21, NULL, NULL),
(555, 'Suleja', 21, NULL, NULL),
(556, 'Tafa', 21, NULL, NULL),
(557, 'Wushishi', 21, NULL, NULL),
(558, 'Abeokuta North', 23, NULL, NULL),
(559, 'Abeokuta South', 23, NULL, NULL),
(560, 'Ado-Odo/Ota', 23, NULL, NULL),
(561, 'Egbado North', 23, NULL, NULL),
(562, 'Egbado South', 23, NULL, NULL),
(563, 'Ewekoro', 23, NULL, NULL),
(564, 'Ifo', 23, NULL, NULL),
(565, 'Ijebu East', 23, NULL, NULL),
(566, 'Ijebu North', 23, NULL, NULL),
(567, 'Ijebu North East', 23, NULL, NULL),
(568, 'Ijebu Ode', 23, NULL, NULL),
(569, 'Ikenne', 23, NULL, NULL),
(570, 'Imeko-Afon', 23, NULL, NULL),
(571, 'Ipokia', 23, NULL, NULL),
(572, 'Obafemi-Owode', 23, NULL, NULL),
(573, 'Odeda', 23, NULL, NULL),
(574, 'Odogbolu', 23, NULL, NULL),
(575, 'Ogun Waterside', 23, NULL, NULL),
(576, 'Remo North', 23, NULL, NULL),
(577, 'Shagamu', 23, NULL, NULL),
(578, 'Akoko North East', 22, NULL, NULL),
(579, 'Akoko North West', 22, NULL, NULL),
(580, 'Akoko South Akure East', 22, NULL, NULL),
(581, 'Akoko South West', 22, NULL, NULL),
(582, 'Akure North', 22, NULL, NULL),
(583, 'Akure South', 22, NULL, NULL),
(584, 'Ese-Odo', 22, NULL, NULL),
(585, 'Idanre', 22, NULL, NULL),
(586, 'Ifedore', 22, NULL, NULL),
(587, 'Ilaje', 22, NULL, NULL),
(588, 'Ile-Oluji', 22, NULL, NULL),
(589, 'Irele', 22, NULL, NULL),
(590, 'Odigbo', 22, NULL, NULL),
(591, 'Okeigbo', 22, NULL, NULL),
(592, 'Okitipupa', 22, NULL, NULL),
(593, 'Ondo East', 22, NULL, NULL),
(594, 'Ondo West', 22, NULL, NULL),
(595, 'Ose', 22, NULL, NULL),
(596, 'Owo ', 22, NULL, NULL),
(597, 'Aiyedade', 24, NULL, NULL),
(598, 'Aiyedire', 24, NULL, NULL),
(599, 'Atakumosa East', 24, NULL, NULL),
(600, 'Atakumosa West', 24, NULL, NULL),
(601, 'Boluwaduro', 24, NULL, NULL),
(602, 'Boripe', 24, NULL, NULL),
(603, 'Ede North', 24, NULL, NULL),
(604, 'Ede South', 24, NULL, NULL),
(605, 'Egbedore', 24, NULL, NULL),
(606, 'Ejigbo', 24, NULL, NULL),
(607, 'Ife Central', 24, NULL, NULL),
(608, 'Ife East', 24, NULL, NULL),
(609, 'Ife North', 24, NULL, NULL),
(610, 'Ife South', 24, NULL, NULL),
(611, 'Ifedayo', 24, NULL, NULL),
(612, 'Ifelodun', 24, NULL, NULL),
(613, 'Ila', 24, NULL, NULL),
(614, 'Ilesha East', 24, NULL, NULL),
(615, 'Ilesha West', 24, NULL, NULL),
(616, 'Irepodun', 24, NULL, NULL),
(617, 'Irewole', 24, NULL, NULL),
(618, 'Isokan', 24, NULL, NULL),
(619, 'Iwo', 24, NULL, NULL),
(620, 'Obokun', 24, NULL, NULL),
(621, 'Odo-Otin', 24, NULL, NULL),
(622, 'Ola-Oluwa', 24, NULL, NULL),
(623, 'Olorunda', 24, NULL, NULL),
(624, 'Oriade', 24, NULL, NULL),
(625, 'Orolu', 24, NULL, NULL),
(626, 'Osogbo', 24, NULL, NULL),
(627, 'Afijio', 25, NULL, NULL),
(628, 'Akinyele', 25, NULL, NULL),
(629, 'Atiba', 25, NULL, NULL),
(630, 'Atigbo', 25, NULL, NULL),
(631, 'Egbeda', 25, NULL, NULL),
(632, 'Ibadan North', 25, NULL, NULL),
(633, 'Ibadan North West', 25, NULL, NULL),
(634, 'Ibadan South East', 25, NULL, NULL),
(635, 'Ibadan South West', 25, NULL, NULL),
(636, 'Ibadan Central', 25, NULL, NULL),
(637, 'Ibarapa Central', 25, NULL, NULL),
(638, 'Ibarapa East', 25, NULL, NULL),
(639, 'Ibarapa North', 25, NULL, NULL),
(640, 'Ido', 25, NULL, NULL),
(641, 'Irepo', 25, NULL, NULL),
(642, 'Iseyin', 25, NULL, NULL),
(643, 'Itesiwaju', 25, NULL, NULL),
(644, 'Iwajowa', 25, NULL, NULL),
(645, 'Kajola', 25, NULL, NULL),
(646, 'Lagelu Ogbomosho North', 25, NULL, NULL),
(647, 'Ogbmosho South', 25, NULL, NULL),
(648, 'Ogo Oluwa', 25, NULL, NULL),
(649, 'Olorunsogo', 25, NULL, NULL),
(650, 'Oluyole', 25, NULL, NULL),
(651, 'Ona-Ara', 25, NULL, NULL),
(652, 'Orelope', 25, NULL, NULL),
(653, 'Ori Ire', 25, NULL, NULL),
(654, 'Oyo East', 25, NULL, NULL),
(655, 'Oyo West', 25, NULL, NULL),
(656, 'Saki East', 25, NULL, NULL),
(657, 'Saki West', 25, NULL, NULL),
(658, 'Surulere', 25, NULL, NULL),
(659, 'Barikin Ladi', 26, NULL, NULL),
(660, 'Bassa', 26, NULL, NULL),
(661, 'Bokkos', 26, NULL, NULL),
(662, 'Jos East', 26, NULL, NULL),
(663, 'Jos North', 26, NULL, NULL),
(664, 'Jos South', 26, NULL, NULL),
(665, 'Kanam', 26, NULL, NULL),
(666, 'Kanke', 26, NULL, NULL),
(667, 'Langtang North', 26, NULL, NULL),
(668, 'Langtang South', 26, NULL, NULL),
(669, 'Mangu', 26, NULL, NULL),
(670, 'Mikang', 26, NULL, NULL),
(671, 'Pankshin', 26, NULL, NULL),
(672, 'Qua''An Pan', 26, NULL, NULL),
(673, 'Riyom', 26, NULL, NULL),
(674, 'Shendam', 26, NULL, NULL),
(675, 'Wase', 26, NULL, NULL),
(676, 'Abua/Odual', 27, NULL, NULL),
(677, 'Ahoada East', 27, NULL, NULL),
(678, 'Ahoada West', 27, NULL, NULL),
(679, 'Akuku Toru', 27, NULL, NULL),
(680, 'Andoni', 27, NULL, NULL),
(681, 'Asari-Toru', 27, NULL, NULL),
(682, 'Bonny', 27, NULL, NULL),
(683, 'Degema', 27, NULL, NULL),
(684, 'Eleme', 27, NULL, NULL),
(685, 'Emohua', 27, NULL, NULL),
(686, 'Etche', 27, NULL, NULL),
(687, 'Gokana', 27, NULL, NULL),
(688, 'Ikwerre', 27, NULL, NULL),
(689, 'Khana', 27, NULL, NULL),
(690, 'Obia/Akpor', 27, NULL, NULL),
(691, 'Ogba/Egbema/Ndoni', 27, NULL, NULL),
(692, 'Ogu/Bolo', 27, NULL, NULL),
(693, 'Okrika', 27, NULL, NULL),
(694, 'Omumma', 27, NULL, NULL),
(695, 'Opobo/Nkoro', 27, NULL, NULL),
(696, 'Oyigbo', 27, NULL, NULL),
(697, 'Port-Harcourt', 27, NULL, NULL),
(698, 'Tai ', 27, NULL, NULL),
(699, 'Binji', 28, NULL, NULL),
(700, 'Bodinga', 28, NULL, NULL),
(701, 'Dange-Shnsi', 28, NULL, NULL),
(702, 'Gada', 28, NULL, NULL),
(703, 'Gawabawa', 28, NULL, NULL),
(704, 'Goronyo', 28, NULL, NULL),
(705, 'Gudu', 28, NULL, NULL),
(706, 'Illela', 28, NULL, NULL),
(707, 'Isa', 28, NULL, NULL),
(708, 'Kebbe', 28, NULL, NULL),
(709, 'Kware', 28, NULL, NULL),
(710, 'Rabah', 28, NULL, NULL),
(711, 'Sabon Birni', 28, NULL, NULL),
(712, 'Shagari', 28, NULL, NULL),
(713, 'Silame', 28, NULL, NULL),
(714, 'Sokoto North', 28, NULL, NULL),
(715, 'Sokoto South', 28, NULL, NULL),
(716, 'Tambuwal', 28, NULL, NULL),
(717, 'Tangaza', 28, NULL, NULL),
(718, 'Tureta', 28, NULL, NULL),
(719, 'Wamako', 28, NULL, NULL),
(720, 'Wurno', 28, NULL, NULL),
(721, 'Yabo', 28, NULL, NULL),
(722, 'Ardo-Kola', 29, NULL, NULL),
(723, 'Bali', 29, NULL, NULL),
(724, 'Cassol', 29, NULL, NULL),
(725, 'Donga', 29, NULL, NULL),
(726, 'Gashaka', 29, NULL, NULL),
(727, 'Ibi', 29, NULL, NULL),
(728, 'Jalingo', 29, NULL, NULL),
(729, 'Karin-Lamido', 29, NULL, NULL),
(730, 'Kurmi', 29, NULL, NULL),
(731, 'Lau', 29, NULL, NULL),
(732, 'Sardauna', 29, NULL, NULL),
(733, 'Takum', 29, NULL, NULL),
(734, 'Ussa', 29, NULL, NULL),
(735, 'Wukari', 29, NULL, NULL),
(736, 'Yorro', 29, NULL, NULL),
(737, 'Zing', 29, NULL, NULL),
(738, 'Bade', 30, NULL, NULL),
(739, 'Bursari', 30, NULL, NULL),
(740, 'Damaturu', 30, NULL, NULL),
(741, 'Fika', 30, NULL, NULL),
(742, 'Fune', 30, NULL, NULL),
(743, 'Geidam', 30, NULL, NULL),
(744, 'Gujba', 30, NULL, NULL),
(745, 'Gulani', 30, NULL, NULL),
(746, 'Jakusko', 30, NULL, NULL),
(747, 'Karasuwa', 30, NULL, NULL),
(748, 'Karawa', 30, NULL, NULL),
(749, 'Machina', 30, NULL, NULL),
(750, 'Nangere', 30, NULL, NULL),
(751, 'Nguru Potiskum', 30, NULL, NULL),
(752, 'Tarmua', 30, NULL, NULL),
(753, 'Yunusari', 30, NULL, NULL),
(754, 'Yusufari', 30, NULL, NULL),
(755, 'Anka ', 35, NULL, NULL),
(756, 'Bakura', 35, NULL, NULL),
(757, 'Birnin Magaji', 35, NULL, NULL),
(758, 'Bukkuyum', 35, NULL, NULL),
(759, 'Bungudu', 35, NULL, NULL),
(760, 'Gummi', 35, NULL, NULL),
(761, 'Gusau', 35, NULL, NULL),
(762, 'Kaura', 35, NULL, NULL),
(763, 'Maradun', 35, NULL, NULL),
(764, 'Maru', 35, NULL, NULL),
(765, 'Namoda', 35, NULL, NULL),
(766, 'Shinkafi', 35, NULL, NULL),
(767, 'Talata Mafara', 35, NULL, NULL),
(768, 'Tsafe', 35, NULL, NULL),
(769, 'Zurmi ', 35, NULL, NULL),
(770, 'Akoko Edo', 10, NULL, NULL),
(771, 'Etsako West', 10, NULL, NULL),
(772, 'Potiskum', 30, NULL, NULL),
(773, 'Owan East', 10, NULL, NULL),
(774, 'Ilorin South', 19, NULL, NULL),
(775, 'Kazaure', 13, NULL, NULL),
(776, 'Gamawa', 5, NULL, NULL),
(777, 'Owan West', 10, NULL, NULL),
(778, 'Awgu', 11, NULL, NULL),
(779, 'Ogbomosho-North', 25, NULL, NULL),
(780, 'Yamaltu Deba', 33, NULL, NULL);

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
-- Table structure for table `schools`
--

DROP TABLE IF EXISTS `schools`;
CREATE TABLE IF NOT EXISTS `schools` (
  `schools_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `full_name` varchar(225) COLLATE utf8_unicode_ci NOT NULL,
  `phone_no` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `db_name` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `motto` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `website` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8_unicode_ci,
  `logo` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `admin_id` int(10) unsigned DEFAULT NULL,
  `status_id` int(10) unsigned NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`schools_id`),
  KEY `schools_admin_id_index` (`admin_id`),
  KEY `schools_status_id_index` (`status_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `schools`
--

INSERT INTO `schools` (`schools_id`, `name`, `full_name`, `phone_no`, `email`, `db_name`, `motto`, `website`, `address`, `logo`, `admin_id`, `status_id`, `created_at`, `updated_at`) VALUES
(1, 'Solid Steps', 'Solid Steps Memorial High', '02830374944', 'solid@steps.high', '', '', 'www.solidsteps.international', 'Ekotun Egbe, Lagos', '1_logo.png', 2, 1, '2016-04-17 14:18:14', '2016-04-17 14:18:14');

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
-- Table structure for table `states`
--

DROP TABLE IF EXISTS `states`;
CREATE TABLE IF NOT EXISTS `states` (
  `state_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `state` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `state_code` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`state_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=38 ;

--
-- Dumping data for table `states`
--

INSERT INTO `states` (`state_id`, `state`, `state_code`, `created_at`, `updated_at`) VALUES
(1, 'Abia', 'ABI\r', NULL, NULL),
(2, 'Adamawa', 'ADA\r', NULL, NULL),
(3, 'Akwa Ibom', 'AKW\r', NULL, NULL),
(4, 'Anambra', 'ANA\r', NULL, NULL),
(5, 'Bauchi', 'BAU\r', NULL, NULL),
(6, 'Benue', 'BEN\r', NULL, NULL),
(7, 'Borno', 'BOR\r', NULL, NULL),
(8, 'Cross-River', 'CRO\r', NULL, NULL),
(9, 'Delta', 'DEL\r', NULL, NULL),
(10, 'Edo', 'EDO\r', NULL, NULL),
(11, 'Enugu', 'ENU\r', NULL, NULL),
(12, 'Imo', 'IMO\r', NULL, NULL),
(13, 'Jigawa', 'JIG\r', NULL, NULL),
(14, 'Kebbi', 'KEB\r', NULL, NULL),
(15, 'Kaduna', 'KAD\r', NULL, NULL),
(16, 'Kogi', 'KOG\r', NULL, NULL),
(17, 'Kano', 'KAN\r', NULL, NULL),
(18, 'Katsina', 'KAT\r', NULL, NULL),
(19, 'Kwara', 'KWA\r', NULL, NULL),
(20, 'Lagos', 'LAG\r', NULL, NULL),
(21, 'Niger', 'NIG\r', NULL, NULL),
(22, 'Ondo', 'OND\r', NULL, NULL),
(23, 'Ogun', 'OGU\r', NULL, NULL),
(24, 'Osun', 'OSU\r', NULL, NULL),
(25, 'Oyo', 'OYO\r', NULL, NULL),
(26, 'Plateau', 'PLA\r', NULL, NULL),
(27, 'Rivers', 'RIV\r', NULL, NULL),
(28, 'Sokoto', 'SOK\r', NULL, NULL),
(29, 'Taraba', 'TAR\r', NULL, NULL),
(30, 'Yobe', 'YOB\r', NULL, NULL),
(31, 'FCT', 'FCT\r', NULL, NULL),
(32, 'Bayelsa', 'BAY\r', NULL, NULL),
(33, 'Gombe', 'GOM\r', NULL, NULL),
(34, 'Nasarawa', 'NAS\r', NULL, NULL),
(35, 'Zamfara', 'ZAM\r', NULL, NULL),
(36, 'Ekiti', 'EKI\r', NULL, NULL),
(37, 'Ebonyi', 'EBO\r', NULL, NULL);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
