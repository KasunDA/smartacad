<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/


Route::group(['middleware' => ['web']], function () {

    Route::get('/', function () {
        return redirect('/dashboard');
    });

    Route::get('/home', function () {
        return view('front.index');
    });

    //Dependent List Box
//    Route::group(array('prefix'=>'list-box'), function(){
//        // Ajax Get Local Governments Based on the state
//        Route::get('/lga/{id}', 'Admin\ListBoxController@lga');
//        Route::get('/academic-term/{id}', 'Admin\ListBoxController@academicTerm');
//        Route::get('/classroom/{id}', 'Admin\ListBoxController@classroom');
//    });

    Route::controllers([
        'home' => 'Front\HomeController'
    ]);

    Route::auth();

    Route::controllers([
        'auth' => 'Auth\AuthController',
        'dashboard' => 'Admin\DashboardController',
        //Dependent List Box
        'list-box' => 'Admin\ListBoxController',

        'menu-headers' => 'Admin\Menus\MenuHeaderController',
        'menus' => 'Admin\Menus\MenuController',
        'menu-items' => 'Admin\Menus\MenuItemController',
        'sub-menu-items' => 'Admin\Menus\SubMenuItemController',
        'sub-most-menu-items' => 'Admin\Menus\SubMostMenuItemController',

        'users' => 'Admin\Users\UserController',
        'user-types' => 'Admin\Users\UserTypeController',

        'roles' => 'Admin\RolesAndPermission\RolesController',
        'permissions' => 'Admin\RolesAndPermission\PermissionsController',
        'profiles' => 'Admin\Users\ProfileController',

        'schools' => 'School\SchoolController',

        //setup records
        'salutations' => 'School\Setups\SalutationController',
        'marital-statuses' => 'School\Setups\MaritalStatusController',

        'accounts' => 'Admin\Accounts\AccountsController',
        'sponsors' => 'Admin\Accounts\SponsorController',
        'staffs' => 'Admin\Accounts\StaffController',
        'students' => 'Admin\Accounts\StudentController',

        //Master Record
        'academic-years' => 'Admin\MasterRecords\AcademicYearsController',
        'academic-terms' => 'Admin\MasterRecords\AcademicTermsController',
        'grades' => 'Admin\MasterRecords\GradesController',
        'class-groups' => 'Admin\MasterRecords\Classes\ClassGroupsController',
        'class-levels' => 'Admin\MasterRecords\Classes\ClassLevelsController',
        'class-rooms' => 'Admin\MasterRecords\Classes\ClassRoomsController',

        //Subjects
        'subject-groups' => 'School\Setups\Subjects\SubjectGroupsController',
        'subjects' => 'School\Setups\Subjects\SubjectsController',
        'subject-classrooms' => 'Admin\MasterRecords\Subjects\SubjectClassRoomsController',
        'school-subjects' => 'Admin\MasterRecords\Subjects\SchoolSubjectsController',

        //Assessment Setup
        'assessment-setups' => 'Admin\MasterRecords\AssessmentSetups\AssessmentSetupsController',


    ]);
});

///////////////////////User/Project APIs///////////////////////////////
Route::group(array('prefix'=>'/api'),function(){

});