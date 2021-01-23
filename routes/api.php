<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('a', function(){
    dd('hi');
});

Route::group(['namespace' => 'API'], function(){
    Route::post('study_year_semesters', 'SemesterController@semesterStudyYear');
    Route::post('student_deposit_requests', 'StudentController@deposite');
    Route::get('student_deposit_request', 'StudentController@student_deposite')->name('deposite');
    Route::post('login', 'StudentController@login')->name('login');
});

Route::group(['middleware' => ['auth:student','jwt.auth'], 'namespace' => 'API'],function ()
{
    Route::post('reset_password','StudentController@reset_password_student');
    Route::get('logout', 'StudentController@logout')->name('logout');
    Route::get('student', 'StudentController@getAuthUser');
});
