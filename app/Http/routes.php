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
        return view('welcome');
    });

    Route::get('/home', function () {
        return view('front.index');
    });

    Route::controllers([
        'home' => 'Front\HomeController'
    ]);

    Route::auth();

    Route::controllers([
        'auth' => 'Auth\AuthController',
        'dashboard' => 'Admin\DashboardController',

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
    ]);
});

///////////////////////User/Project APIs///////////////////////////////
Route::group(array('prefix'=>'/api'),function(){

});