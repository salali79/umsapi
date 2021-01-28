<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Response;
use Auth;
use Validator;
use JWTFactory;
use JWTAuth;
use JWTAuthException;
use App\Models\RegistrationPlan;
use App\Models\Course;

class RegistrationPlanController extends Controller
{
    public function courses()
    {
    /*"id": 1,
    "study_year_semester_id": 10,
    "study_plan_id": 9,
    "faculty_id": 5,
    "department_id": 2,*/
        $reg = RegistrationPlan::select('id', 'study_year_semester_id','study_plan_id', 'faculty_id', 'department_id')
        ->with(['registrationCourses' => function($registrationCourse){
            $registrationCourse->with('course')->select('id', 'code');
        }])->select('id', 'course_id')
        //->with([''])
        ->find(1);
        
        return $reg;
    }
}
