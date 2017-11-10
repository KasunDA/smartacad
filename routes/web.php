<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

//Route::get('/', function () {
//    if(\Illuminate\Support\Facades\Auth::check()){
//        return (\Illuminate\Support\Facades\Auth::user()->user_type_id == \App\Models\Admin\Accounts\Sponsor::USER_TYPE)
//            ? redirect('/home') : redirect('/dashboard');
//    }else{
//        return redirect('/login');
//    }
//});

//Dashboard Route
Route::get('/', 'Admin\Utilities\DashboardController@index');
Route::get('/home', 'Admin\Utilities\DashboardController@index');
Route::group(['namespace' => 'Admin\Utilities', 'prefix'=>'/dashboard'], function() {
    Route::get('/', 'DashboardController@index');
    Route::get('/students-gender', 'DashboardController@studentsGender');
    Route::get('/students-classlevel', 'DashboardController@studentsClasslevel');
    Route::get('/subject-tutor/{userId}', 'DashboardController@subjectTutor');
    Route::get('/class-teacher/{userId}', 'DashboardController@classTeacher');
});

//ListBoxes Routes
Route::group(['namespace' => 'Admin\Utilities', 'prefix'=>'/list-box'], function() {
    Route::get('/lga/{id}', 'ListBoxController@lga');
    Route::get('/academic-term/{id}', 'ListBoxController@academicTerm');
    Route::get('/classroom/{id}', 'ListBoxController@classroom');
});

//PHP Info
Route::get('/phpinfo', function () {
    return view('/phpinfo');
});

//Authentication Route
Auth::routes();
Route::get('/logout', 'Auth\LoginController@logout');
Route::get('/joker', 'Auth\LoginController@joker');
Route::post('/joker', 'Auth\LoginController@jokerIn');

//Sponsors Route
Route::group(['namespace' => 'Admin\Accounts', 'prefix'=>'/sponsors'], function() {
    Route::get('/', 'SponsorController@index');
    Route::post('/all-sponsors', 'SponsorController@data');
    Route::get('/view/{id}', 'SponsorController@view');
    Route::get('/edit/{id}', 'SponsorController@edit');
    Route::post('/edit/{id?}', 'SponsorController@update');
});

//Staffs Route
Route::group(['namespace' => 'Admin\Accounts', 'prefix'=>'/staffs'], function() {
    Route::get('/', 'StaffController@index');
    Route::get('/view/{id}', 'StaffController@view');
    Route::get('/edit/{id}', 'StaffController@edit');
    Route::post('/edit/{id?}', 'StaffController@update');
    Route::post('/all-staffs', 'StaffController@allStaffs');
    Route::post('/staff-subjects', 'StaffController@staffSubjects');

    Route::get('/dashboard/{staffId}', 'StaffController@dashboard');
    Route::get('/marked/{staffId}', 'StaffController@marked');
    Route::get('/unmarked/{staffId}/', 'StaffController@unmarked');

    Route::get('/subject/{staffId}/', 'StaffController@subject');
    Route::get('/subject-details/{subjectId}', 'StaffController@subjectDetails');
    Route::get('/classroom/{staffId}/', 'StaffController@classroom');
});

//Students Route
Route::group(['namespace' => 'Admin\Accounts', 'prefix'=>'/students'], function() {
    Route::get('/', 'StudentController@index');
    Route::post('/all-students', 'StudentController@data');
    Route::get('/view/{id}', 'StudentController@view');
    Route::get('/edit/{id}', 'StudentController@edit');
    Route::post('/edit/{id?}', 'StudentController@update');
    Route::get('/create', 'StudentController@create');
    Route::post('/create', 'StudentController@save');

    Route::get('/delete/{id}', 'StudentController@delete');
    Route::get('/sponsors', 'StudentController@sponsors');
    Route::post('/avatar', 'StudentController@avatar');
});

//Assessments Routes
Route::group(['namespace' => 'Admin\Assessments', 'prefix'=>'/assessments'], function () {
    Route::get('/', 'AssessmentsController@index');
    Route::post('/subject-assigned', 'AssessmentsController@subjectAssigned');
    Route::get('/subject-details/{id}', 'AssessmentsController@subjectDetails');
    Route::get('/input-scores/{setupId}/{subjectId}/{view?}', 'AssessmentsController@inputScores');
    Route::post('/input-scores/{setupId?}/{subjectId?}/{view?}', 'AssessmentsController@saveInputScores');
    Route::post('/search-students', 'AssessmentsController@searchStudents');
    
    Route::get('/report-details/{studId}/{termId}', 'AssessmentsController@reportDetails');
    Route::get('/print-report/{studId}/{termId}', 'AssessmentsController@printReport');
    Route::get('/view/{studentId}', 'AssessmentsController@view');
    Route::get('/details/{studentId}/{termId}', 'AssessmentsController@details');
});

//Domains Routes
Route::group(['namespace' => 'Admin\Assessments', 'prefix'=>'/domains'], function () {
    Route::get('/', 'DomainsController@index');
    Route::post('/classroom-assigned', 'DomainsController@classroomAssigned');
    Route::get('/view-students/{classId}/{termId}', 'DomainsController@viewStudents');
    
    Route::get('/assess/{studId}/{termId}', 'DomainsController@assess');
    Route::post('/assess/{studId?}/{termId?}', 'DomainsController@saveAssess');
    Route::get('/remark/{classId}/{termId}', 'DomainsController@remark');
    Route::post('/remark/{classId?}/{termId?}', 'DomainsController@saveRemark');
});

//Exams Routes
Route::group(['namespace' => 'Admin\Assessments', 'prefix'=>'/exams'], function () {
    Route::get('/', 'ExamsController@index');
    Route::get('/setup', 'ExamsController@setup');
    Route::post('/validate-all-setup', 'ExamsController@validateAllSetup');
    Route::post('/all-setup', 'ExamsController@allSetup');
    Route::post('/subject-assigned', 'ExamsController@subjectAssigned');
    Route::get('/input-scores/{id}', 'ExamsController@inputScores');
    Route::post('/input-scores/{id?}', 'ExamsController@saveScores');
    Route::get('/view-scores/{id}', 'ExamsController@viewScores');
    
    Route::post('/search-results', 'ExamsController@searchResults');
    Route::get('/student-terminal-result/{studId}/{termId}', 'ExamsController@studentTerminalResult');
    Route::get('/classroom-terminal-result/{classId}/{termId}', 'ExamsController@classroomTerminalResult');
    Route::post('/validate-my-setup', 'ExamsController@validateMySetup');
    Route::get('/compute-ca', 'ExamsController@computeCa');
    Route::post('/my-setup', 'ExamsController@mySetup');
    Route::get('/print-student-terminal-result/{studId}/{termId}', 'ExamsController@printStudentTerminalResult');
    
    Route::get('/view/{studentId}', 'ExamsController@view');
    Route::get('/details/{studentId}/{termId}', 'ExamsController@details');
});

//Attendance Routes
Route::group(['namespace' => 'Admin\Attendances', 'prefix'=>'/attendances'], function () {
    Route::get('/', 'AttendancesController@index');
    Route::get('/initiate/{classId}/{attendId?}', 'AttendancesController@initiate')->name('initiateAttendance');
    Route::post('/initiate/{classId}/{attendId?}', 'AttendancesController@take');
    Route::get('/adjust/{classId}', 'AttendancesController@adjust')->name('adjustAttendance');
    Route::post('/classroom', 'AttendancesController@classroom');
    Route::get('/classroom-details/{attendId}', 'AttendancesController@classroomDetails');
    Route::post('/student', 'AttendancesController@student');
    Route::get('/student-details/{studentClassId}/{termId}', 'AttendancesController@studentDetails');
    //Student
    Route::get('/view/{studentId}', 'AttendancesController@view');
    Route::get('/details/{studentId}/{attendId}', 'AttendancesController@details');
});

//////////////////////// Master Records Routes ////////////////////////////////////////////////////////////
//Class Routes
Route::group(['namespace' => 'Admin\MasterRecords\Classes'], function () {
    //Class Groups Routes
    Route::group(['prefix'=>'/class-groups'], function () {
        Route::get('/', 'ClassGroupsController@index');
        Route::post('/', 'ClassGroupsController@save');
        Route::get('/delete/{id}', 'ClassGroupsController@delete');
    });

    //Class Level Routes
    Route::group(['prefix'=>'/class-levels'], function () {
        Route::get('/{groupId?}', 'ClassLevelsController@index');
        Route::post('/', 'ClassLevelsController@save');
        Route::get('/delete/{id}', 'ClassLevelsController@delete');
        Route::post('/class-groups', 'ClassLevelsController@classGroups');
    });

    //Class Rooms Routes
    Route::group(['prefix'=>'/class-rooms'], function () {
        Route::get('/{id?}', 'ClassRoomsController@index');
        Route::post('/', 'ClassRoomsController@save');
        Route::get('/delete/{id}', 'ClassRoomsController@delete');
        Route::post('/levels', 'ClassRoomsController@levels');
        Route::get('/class-teachers', 'ClassRoomsController@classTeachers');
        Route::post('/class-teachers', 'ClassRoomsController@teachers');
        Route::post('/assign-class-teachers', 'ClassRoomsController@assignClassTeachers');
    });

    //Class Students Routes
    Route::group(['prefix'=>'/class-students'], function () {
        Route::get('/', 'ClassStudentsController@index');
        Route::post('/search-students', 'ClassStudentsController@searchStudents');
        Route::post('/assign', 'ClassStudentsController@assign');
        Route::post('/view-students', 'ClassStudentsController@viewStudents');
        Route::post('/validate-clone', 'ClassStudentsController@validateClone');
        Route::post('/cloning', 'ClassStudentsController@cloning');
    });
});

//Items Routes
Route::group(['namespace' => 'Admin\MasterRecords\Items'], function () {
    //Items Quotes Routes
    Route::group(['prefix'=>'/item-quotes'], function () {
        Route::get('/{yearId?}/{termId?}', 'ItemQuotesController@index');
        Route::post('/{yearId?}/{termId?}', 'ItemQuotesController@save');
        Route::get('/delete/{id}', 'ItemQuotesController@delete');
        Route::post('/academic-years', 'ItemQuotesController@academicYears');
    });

    //Items Routes
    Route::group(['prefix'=>'/items'], function () {
        Route::get('/', 'ItemsController@index');
        Route::post('/', 'ItemsController@save');
        Route::get('/delete/{id}', 'ItemsController@delete');
    });

    //Item Types Routes
    Route::group(['prefix'=>'/item-types'], function () {
        Route::get('/', 'ItemTypesController@index');
        Route::post('/', 'ItemTypesController@save');
        Route::get('/delete/{id}', 'ItemTypesController@delete');
    });
});

//Academic Session Routes
Route::group(['namespace' => 'Admin\MasterRecords\Sessions'], function () {
    //Academic Terms Routes
    Route::group(['prefix'=>'/academic-terms'], function () {
        Route::get('/{yearId?}', 'AcademicTermsController@index');
        Route::post('/{yearId?}', 'AcademicTermsController@save');
        Route::get('/delete/{id}', 'AcademicTermsController@delete');
        Route::get('/clones', 'AcademicTermsController@clone');
        Route::post('/validate-clone', 'AcademicTermsController@validateClone');
        Route::post('/cloning', 'AcademicTermsController@cloning');
        Route::post('/academic-years', 'AcademicTermsController@academicYears');
    });

    //Academic Years Routes
    Route::group(['prefix'=>'/academic-years'], function () {
        Route::get('/', 'AcademicYearsController@index');
        Route::post('/', 'AcademicYearsController@save');
        Route::get('/delete/{id}', 'AcademicYearsController@delete');
    });
});

//Subjects Routes
Route::group(['namespace' => 'Admin\MasterRecords\Subjects'], function () {
    //Custom Subjects Routes
    Route::group(['prefix'=>'/custom-subjects'], function () {
        Route::get('/', 'CustomSubjectsController@index');
        Route::get('/groupings', 'CustomSubjectsController@groupings');
        Route::post('/groupings', 'CustomSubjectsController@save');
        Route::get('/subjects', 'CustomSubjectsController@subjects');
        Route::post('/subjects', 'CustomSubjectsController@saveSubjects');
        Route::get('/delete/{id}', 'CustomSubjectsController@delete');
        
        Route::get('/clones', 'AcademicTermsController@clone');
        Route::post('/validate-clone', 'AcademicTermsController@validateClone');
        Route::post('/cloning', 'AcademicTermsController@cloning');
        Route::post('/academic-years', 'AcademicTermsController@academicYears');
    });

    //School Subjects Routes
    Route::group(['prefix'=>'/school-subjects'], function () {
        Route::get('/', 'SchoolSubjectsController@index');
        Route::post('/', 'SchoolSubjectsController@save');
        Route::get('/view', 'SchoolSubjectsController@view');
        Route::get('/rename', 'SchoolSubjectsController@rename');
        Route::post('/rename', 'SchoolSubjectsController@renaming');
    });
    
    //Subject Class Rooms Routes
    Route::group(['prefix'=>'/subject-classrooms'], function () {
        Route::get('/', 'SubjectClassRoomsController@index');
        Route::post('/search-assigned', 'SubjectClassRoomsController@searchAssigned');
        Route::post('/assign-subjects', 'SubjectClassRoomsController@assignSubjects');
        Route::post('/view-assigned', 'SubjectClassRoomsController@viewAssigned');
        Route::get('/assign-tutor/{classId}/{tutorId}', 'SubjectClassRoomsController@assignTutor');
        Route::post('/search-subjects', 'SubjectClassRoomsController@searchSubjects');
        Route::get('/manage-student/{id}/{termId}', 'SubjectClassRoomsController@manageStudent');
        Route::post('/manage-student/{id?}/{termId?}', 'SubjectClassRoomsController@saveStudents');
        Route::get('/delete/{id}', 'SubjectClassRoomsController@delete');
    });
    
    //Subject Tutors Routes
    Route::group(['prefix'=>'/subject-tutors'], function () {
        Route::get('/', 'SubjectTutorsController@index');
        Route::post('/search-subjects', 'SubjectTutorsController@searchSubjects');
        Route::get('/manage-student/{id}/{termId}', 'SubjectTutorsController@manageStudent');
        Route::post('/manage-student/{id?}/{termId?}', 'SubjectTutorsController@saveStudents');
        Route::post('/view-assigned', 'SubjectTutorsController@viewAssigned');
        
        Route::get('/assign-tutor/{classId}/{tutorId}', 'SubjectClassRoomsController@assignTutor');
        Route::post('/search-subjects', 'SubjectClassRoomsController@searchSubjects');
        Route::get('/delete/{id}', 'SubjectClassRoomsController@delete');
    });
    
});
//Master Records
Route::group(['namespace' => 'Admin\MasterRecords'], function () {
    //Assessment Setups Route
    Route::group(['prefix'=>'/assessment-setups'], function () {
        Route::get('/{yearId?}', 'AssessmentSetupsController@index');
        Route::post('/academic-years', 'AssessmentSetupsController@academicYears');
        Route::post('/{yearId?}', 'AssessmentSetupsController@save');
        Route::get('/delete/{id}', 'AssessmentSetupsController@delete');
        
        Route::get('/details/{termId?}/{yearId?}', 'AssessmentSetupsController@details');
        Route::post('/terms', 'AssessmentSetupsController@terms');
        Route::post('/details/{termId?}/{yearId?}', 'AssessmentSetupsController@saveDetails');
        Route::get('/details-delete/{id?}/{groupId?}', 'AssessmentSetupsController@deleteDetails');
        
        Route::get('/manage-student/{id}/{termId}', 'SubjectTutorsController@manageStudent');
        Route::post('/manage-student/{id?}/{termId?}', 'SubjectTutorsController@saveStudents');
        Route::post('/view-assigned', 'SubjectTutorsController@viewAssigned');
        
        Route::get('/assign-tutor/{classId}/{tutorId}', 'SubjectClassRoomsController@assignTutor');
        Route::post('/search-subjects', 'SubjectClassRoomsController@searchSubjects');
    });
});
