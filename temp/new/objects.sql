DELIMITER ;

--
-- Views
--
CREATE
    ALGORITHM = UNDEFINED
    DEFINER = `ekaruztech_user`@`%`
    SQL SECURITY DEFINER
VIEW `assessment_detailsviews` AS
    SELECT
        `f`.`assessment_id` AS `assessment_id`,
        `f`.`subject_classroom_id` AS `subject_classroom_id`,
        `f`.`assessment_setup_detail_id` AS `assessment_setup_detail_id`,
        `f`.`marked` AS `marked`,
        `g`.`assessment_detail_id` AS `assessment_detail_id`,
        `g`.`student_id` AS `student_id`,
        `j`.`student_no` AS `student_no`,
        CONCAT(`j`.`first_name`, ' ', `j`.`last_name`) AS `student_name`,
        `j`.`gender` AS `gender`,
        `g`.`score` AS `score`,
        `h`.`weight_point` AS `weight_point`,
        `h`.`number` AS `number`,
        `h`.`percentage` AS `percentage`,
        `h`.`description` AS `description`,
        `h`.`submission_date` AS `submission_date`,
        `i`.`assessment_setup_id` AS `assessment_setup_id`,
        `i`.`assessment_no` AS `assessment_no`,
        `m`.`ca_weight_point` AS `ca_weight_point`,
        `m`.`exam_weight_point` AS `exam_weight_point`,
        `j`.`sponsor_id` AS `sponsor_id`,
        `k`.`phone_no` AS `phone_no`,
        `k`.`email` AS `email`,
        CONCAT(`k`.`first_name`, ' ', `k`.`last_name`) AS `sponsor_name`,
        `a`.`subject_id` AS `subject_id`,
        `a`.`classroom_id` AS `classroom_id`,
        `a`.`tutor_id` AS `tutor_id`,
        CONCAT(`n`.`first_name`, ' ', `n`.`last_name`) AS `tutor`,
        `c`.`classroom` AS `classroom`,
        `c`.`classlevel_id` AS `classlevel_id`,
        `d`.`classlevel` AS `classlevel`,
        `d`.`classgroup_id` AS `classgroup_id`,
        `a`.`academic_term_id` AS `academic_term_id`,
        `e`.`academic_term` AS `academic_term`
    FROM
        (((((((((((`subject_classrooms` `a`
        JOIN `classrooms` `c` ON ((`a`.`classroom_id` = `c`.`classroom_id`)))
        JOIN `classlevels` `d` ON ((`c`.`classlevel_id` = `d`.`classlevel_id`)))
        JOIN `academic_terms` `e` ON ((`a`.`academic_term_id` = `e`.`academic_term_id`)))
        JOIN `assessments` `f` ON ((`a`.`subject_classroom_id` = `f`.`subject_classroom_id`)))
        JOIN `assessment_details` `g` ON ((`f`.`assessment_id` = `g`.`assessment_id`)))
        JOIN `assessment_setup_details` `h` ON ((`f`.`assessment_setup_detail_id` = `h`.`assessment_setup_detail_id`)))
        JOIN `assessment_setups` `i` ON ((`h`.`assessment_setup_id` = `i`.`assessment_setup_id`)))
        JOIN `students` `j` ON ((`g`.`student_id` = `j`.`student_id`)))
        LEFT JOIN `users` `k` ON ((`j`.`sponsor_id` = `k`.`user_id`)))
        JOIN `classgroups` `m` ON ((`d`.`classgroup_id` = `m`.`classgroup_id`)))
        LEFT JOIN `users` `n` ON ((`a`.`tutor_id` = `n`.`user_id`)));



CREATE
    ALGORITHM = UNDEFINED
    DEFINER = `ekaruztech_user`@`%`
    SQL SECURITY DEFINER
VIEW `exams_detailsviews` AS
    SELECT
        `exam_details`.`exam_detail_id` AS `exam_detail_id`,
        `exams`.`exam_id` AS `exam_id`,
        `subject_classrooms`.`subject_classroom_id` AS `subject_classroom_id`,
        `subject_classrooms`.`subject_id` AS `subject_id`,
        `subject_classrooms`.`tutor_id` AS `tutor_id`,
        `classrooms`.`classlevel_id` AS `classlevel_id`,
        `student_classes`.`classroom_id` AS `classroom_id`,
        `students`.`student_id` AS `student_id`,
        `classrooms`.`classroom` AS `classroom`,
        CONCAT(UCASE(`students`.`first_name`),
                ' ',
                LCASE(`students`.`last_name`)) AS `fullname`,
        `students`.`gender` AS `student_gender`,
        `students`.`student_no` AS `student_no`,
        `exam_details`.`ca` AS `ca`,
        `exam_details`.`exam` AS `exam`,
        (`exam_details`.`exam` + `exam_details`.`ca`) AS `student_total`,
        `classgroups`.`ca_weight_point` AS `ca_weight_point`,
        `classgroups`.`exam_weight_point` AS `exam_weight_point`,
        (`classgroups`.`exam_weight_point` + `classgroups`.`ca_weight_point`) AS `weight_point_total`,
        `academic_terms`.`academic_term_id` AS `academic_term_id`,
        `academic_terms`.`academic_term` AS `academic_term`,
        `exams`.`marked` AS `marked`,
        `academic_terms`.`academic_year_id` AS `academic_year_id`,
        `academic_years`.`academic_year` AS `academic_year`,
        `classlevels`.`classlevel` AS `classlevel`,
        `classlevels`.`classgroup_id` AS `classgroup_id`
    FROM
        (((((((((`exams`
        JOIN `exam_details` ON ((`exams`.`exam_id` = `exam_details`.`exam_id`)))
        JOIN `subject_classrooms` ON ((`exams`.`subject_classroom_id` = `subject_classrooms`.`subject_classroom_id`)))
        JOIN `students` ON ((`exam_details`.`student_id` = `students`.`student_id`)))
        JOIN `academic_terms` ON ((`subject_classrooms`.`academic_term_id` = `academic_terms`.`academic_term_id`)))
        JOIN `academic_years` ON ((`academic_years`.`academic_year_id` = `academic_terms`.`academic_year_id`)))
        JOIN `student_classes` ON ((`students`.`student_id` = `student_classes`.`student_id`)))
        JOIN `classrooms` ON ((`student_classes`.`classroom_id` = `classrooms`.`classroom_id`)))
        JOIN `classlevels` ON ((`classrooms`.`classlevel_id` = `classlevels`.`classlevel_id`)))
        JOIN `classgroups` ON ((`classgroups`.`classgroup_id` = `classlevels`.`classgroup_id`)));


CREATE
    ALGORITHM = UNDEFINED
    DEFINER = `ekaruztech_user`@`%`
    SQL SECURITY DEFINER
VIEW `exams_subjectviews` AS
    SELECT
        `a`.`exam_id` AS `exam_id`,
        `f`.`classroom_id` AS `classroom_id`,
        `f`.`classroom` AS `classroom`,
        `b`.`subject_id` AS `subject_id`,
        `a`.`subject_classroom_id` AS `subject_classroom_id`,
        `b`.`tutor_id` AS `tutor_id`,
        CONCAT(UCASE(`j`.`first_name`),
                ' ',
                `j`.`last_name`) AS `tutor`,
        `h`.`ca_weight_point` AS `ca_weight_point`,
        `h`.`exam_weight_point` AS `exam_weight_point`,
        `a`.`marked` AS `marked`,
        `f`.`classlevel_id` AS `classlevel_id`,
        `g`.`classlevel` AS `classlevel`,
        `b`.`academic_term_id` AS `academic_term_id`,
        `d`.`academic_term` AS `academic_term`,
        `d`.`academic_year_id` AS `academic_year_id`,
        `e`.`academic_year` AS `academic_year`
    FROM
        ((((((`exams` `a`
        JOIN `subject_classrooms` `b` ON ((`a`.`subject_classroom_id` = `b`.`subject_classroom_id`)))
        LEFT JOIN (`classlevels` `g`
        JOIN `classrooms` `f` ON ((`f`.`classlevel_id` = `g`.`classlevel_id`))) ON ((`b`.`classroom_id` = `f`.`classroom_id`)))
        JOIN `academic_terms` `d` ON ((`b`.`academic_term_id` = `d`.`academic_term_id`)))
        JOIN `academic_years` `e` ON ((`d`.`academic_year_id` = `e`.`academic_year_id`)))
        JOIN `classgroups` `h` ON ((`g`.`classgroup_id` = `h`.`classgroup_id`)))
        LEFT JOIN `users` `j` ON ((`b`.`tutor_id` = `j`.`user_id`)));


CREATE
    ALGORITHM = UNDEFINED
    DEFINER = `ekaruztech_user`@`%`
    SQL SECURITY DEFINER
VIEW `students_classroomviews` AS
    SELECT
        CONCAT(UCASE(`students`.`first_name`),
                ' ',
                `students`.`last_name`) AS `fullname`,
        `students`.`student_no` AS `student_no`,
        `classrooms`.`classroom` AS `classroom`,
        `classrooms`.`classroom_id` AS `classroom_id`,
        `students`.`student_id` AS `student_id`,
        `classlevels`.`classlevel` AS `classlevel`,
        `classrooms`.`classlevel_id` AS `classlevel_id`,
        `students`.`sponsor_id` AS `sponsor_id`,
        CONCAT(UCASE(`users`.`first_name`),
                ' ',
                `users`.`last_name`) AS `sponsor_name`,
        `student_classes`.`academic_year_id` AS `academic_year_id`,
        `academic_years`.`academic_year` AS `academic_year`,
        `students`.`status_id` AS `status_id`
    FROM
        (((((`students`
        JOIN `student_classes` ON ((`student_classes`.`student_id` = `students`.`student_id`)))
        JOIN `classrooms` ON ((`student_classes`.`classroom_id` = `classrooms`.`classroom_id`)))
        JOIN `classlevels` ON ((`classlevels`.`classlevel_id` = `classrooms`.`classlevel_id`)))
        JOIN `academic_years` ON ((`student_classes`.`academic_year_id` = `academic_years`.`academic_year_id`)))
        JOIN `users` ON ((`students`.`sponsor_id` = `users`.`user_id`)));


CREATE
    ALGORITHM = UNDEFINED
    DEFINER = `ekaruztech_user`@`%`
    SQL SECURITY DEFINER
VIEW `subjects_classroomviews` AS
    SELECT
        CONCAT(UCASE(`e`.`first_name`),
                ' ',
                `e`.`last_name`) AS `tutor`,
        `e`.`user_id` AS `tutor_id`,
        `a`.`classroom_id` AS `classroom_id`,
        `a`.`subject_classroom_id` AS `subject_classroom_id`,
        `a`.`subject_id` AS `subject_id`,
        `d`.`subject` AS `subject`,
        `d`.`subject_group_id` AS `subject_group_id`,
        `a`.`academic_term_id` AS `academic_term_id`,
        `b`.`academic_term` AS `academic_term`,
        `a`.`exam_status_id` AS `exam_status_id`,
        (CASE `a`.`exam_status_id`
            WHEN 1 THEN 'Marked'
            WHEN 2 THEN 'Not Marked'
        END) AS `exam_status`,
        `c`.`classlevel_id` AS `classlevel_id`,
        `c`.`classroom` AS `classroom`
    FROM
        ((((`subject_classrooms` `a`
        JOIN `academic_terms` `b` ON ((`a`.`academic_term_id` = `b`.`academic_term_id`)))
        JOIN `classrooms` `c` ON ((`a`.`classroom_id` = `c`.`classroom_id`)))
        JOIN `smartschools`.`subjects` `d` ON ((`d`.`subject_id` = `a`.`subject_id`)))
        LEFT JOIN `users` `e` ON ((`a`.`tutor_id` = `e`.`user_id`)));



CREATE
    ALGORITHM = UNDEFINED
    DEFINER = `ekaruztech_user`@`%`
    SQL SECURITY DEFINER
VIEW `subjects_assessmentsviews` AS
    SELECT
        `a`.`tutor` AS `tutor`,
        `a`.`tutor_id` AS `tutor_id`,
        `a`.`classroom_id` AS `classroom_id`,
        `a`.`subject_classroom_id` AS `subject_classroom_id`,
        `a`.`subject_id` AS `subject_id`,
        `a`.`subject` AS `subject`,
        `a`.`subject_group_id` AS `subject_group_id`,
        `a`.`academic_term_id` AS `academic_term_id`,
        `a`.`academic_term` AS `academic_term`,
        `a`.`exam_status_id` AS `exam_status_id`,
        `a`.`exam_status` AS `exam_status`,
        `a`.`classlevel_id` AS `classlevel_id`,
        `a`.`classroom` AS `classroom`,
        `b`.`assessment_id` AS `assessment_id`,
        `b`.`marked` AS `marked`,
        `c`.`assessment_setup_detail_id` AS `assessment_setup_detail_id`,
        `c`.`number` AS `number`,
        `c`.`weight_point` AS `weight_point`,
        `c`.`percentage` AS `percentage`,
        `c`.`assessment_setup_id` AS `assessment_setup_id`,
        `c`.`submission_date` AS `submission_date`,
        `c`.`description` AS `description`
    FROM
        ((`subjects_classroomviews` `a`
        LEFT JOIN `assessments` `b` ON ((`a`.`subject_classroom_id` = `b`.`subject_classroom_id`)))
        LEFT JOIN `assessment_setup_details` `c` ON ((`b`.`assessment_setup_detail_id` = `c`.`assessment_setup_detail_id`)));


-- --------------------------------------------------------



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

-- --------------------------------------------------------