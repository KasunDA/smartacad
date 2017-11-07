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


//Authentication Route
Auth::routes();
Route::get('/logout', 'Auth\LoginController@logout');
Route::get('/joker', 'Auth\LoginController@joker');
Route::post('/joker', 'Auth\LoginController@jokerIn');

