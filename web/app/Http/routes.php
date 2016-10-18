<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
App::setLocale(config('app.locale'));

Route::auth();

Route::get('/', function () {
	return view('welcome');
});

Route::get('/home', 'HomeController@index');

Route::get('/depart', 'ProDepartmentController@grid');
Route::get('/depart/edit/{id?}', 'ProDepartmentController@form');
Route::post('/depart/edit/{id?}', 'ProDepartmentController@form');
Route::get('/depart/export/{template?}', 'ProDepartmentController@export');
Route::get('/depart/import', 'ProDepartmentController@import');
Route::post('/depart/import', 'ProDepartmentController@import');

Route::get('/subject', 'SubjectController@grid');
Route::get('/subject/edit/{id?}', 'SubjectController@form');
Route::post('/subject/edit/{id?}', 'SubjectController@form');
Route::get('/subject/export/{template?}', 'SubjectController@export');
Route::get('/subject/import', 'SubjectController@import');
Route::post('/subject/import', 'SubjectController@import');

Route::get('/classroom', 'ClassRoomController@grid');
Route::get('/classroom/edit/{id?}', 'ClassRoomController@form');
Route::post('/classroom/edit/{id?}', 'ClassRoomController@form');
Route::get('/classroom/export/{template?}', 'ClassRoomController@export');
Route::get('/classroom/import', 'ClassRoomController@import');
Route::post('/classroom/import', 'ClassRoomController@import');

Route::get('/classteacher', 'ClassTeacherController@grid');
Route::get('/classteacher/edit/{id?}', 'ClassTeacherController@form');
Route::post('/classteacher/edit/{id?}', 'ClassTeacherController@form');
Route::get('/classteacher/export/{template?}', 'ClassTeacherController@export');
Route::get('/classteacher/import', 'ClassTeacherController@import');
Route::post('/classteacher/import', 'ClassTeacherController@import');

Route::get('/student', 'StudentController@grid');
Route::get('/student/edit/{id?}', 'StudentController@form');
Route::post('/student/edit/{id?}', 'StudentController@form');
Route::get('/student/export/{template?}', 'StudentController@export');
Route::get('/student/import', 'StudentController@import');
Route::post('/student/import', 'StudentController@import');

Route::get('/term', 'SchoolTermController@form');
Route::post('/term', 'SchoolTermController@form');
