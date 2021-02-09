<?php

use App\Models\Student as Student;
use App\Models\StudyYearSemester as StudyYearSemester;
use Illuminate\Http\Request as Request;


if (!function_exists('current_student')) {

    function current_student(Request $request)
    {
        if(!is_null($request->lang)) app()->setLocale($request->lang);
        $headers = apache_request_headers();
        $request->headers->set('Authorization', $headers['Authorization']);
        $token = $request->headers->get('Authorization');
        JWTAuth::setToken($token);
        $std = auth('student')->user();
        return $std;
    }
}
if (!function_exists('current_study_year_id')) {
    function current_study_year_id()
    {
        return 20;
    }
}
if (!function_exists('current_semester_id')) {
    function current_semester_id()
    {
        return 2;
    }
}
if  (!function_exists('studyYearSemesterId')) {

}

?>
