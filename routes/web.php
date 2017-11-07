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
