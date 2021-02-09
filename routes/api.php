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

Route::get('banks','API\BankController@index');
Route::get('study_years','API\StudyYearController@index');
Route::group(['namespace' => 'API'], function(){
    Route::post('login', 'StudentController@login')->name('login');
    Route::post('study_year_semesters', 'SemesterController@semesterStudyYear');
    Route::get('student_deposit_requests', 'StudentDepositRequestController@index')->name('deposite');
    Route::post('student_deposit_request/store', 'StudentDepositRequestController@store');
    Route::get('profile', 'StudentProfileController@info');
    /////////////////--profile--/////////////////
    Route::get('personal_info', 'StudentProfileController@personal_info');
    Route::get('contact_info', 'StudentProfileController@contact_info');
    Route::get('emergency_info', 'StudentProfileController@emergency_info');
    Route::get('registration_info', 'StudentProfileController@registration_info');
    /*Route::get('folders', 'FolderController@index');
    Route::post('files', 'FolderController@files');
    Route::get('student_files', 'FolderController@exist_files');*/
    Route::get('courses','CourseController@index');
    Route::get('student_courses', 'CourseController@student_courses');

    Route::get('finance_allowed_hours', 'StudentProfileController@finance_allowed_hours');
    Route::get('academic_allowed_hours', 'StudentProfileController@academic_allowed_hours');

    Route::post('register_course', 'StudentProfileController@add_course');
    Route::post('final_register_course', 'StudentProfileController@final_add_course');
    Route::post('delete_course', 'StudentProfileController@delete_course');

    Route::post('add_course_time', 'ProgramController@add_course_time');
    Route::get('get_course_conflict', 'ProgramController@get_course_conflict');

    Route::get('registration_plan', 'RegistrationPlanController@index');
});

Route::group(['middleware' => ['auth:student','jwt.auth'], 'namespace' => 'API'],function ()
{
    Route::post('reset_password','StudentController@reset_password_student');
    Route::get('logout', 'StudentController@logout')->name('logout');
    Route::get('student', 'StudentController@getAuthUser');
});

Route::fallback(function(){
    return response()->json([
        'status' => 'error',
        'message' => 'Route not found',
        'data' => [],
        'action'=> ''
    ], 404);
});
