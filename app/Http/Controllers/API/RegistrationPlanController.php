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
use App\Models\RegistrationCourseGroup;

class RegistrationPlanController extends Controller
{
    public function courses()
    {
        $reg = RegistrationPlan::select('id', 'study_year_semester_id','study_plan_id', 'faculty_id', 'department_id')
        ->with(['registrationCourses' => function($registrationCourse){
            $registrationCourse->with(['course' => function($course){
                    $course->select('id', 'code');
                }, 'courseGroups' => function($courseGroup){
                    $courseGroup->select('id', 'name', 'capacity', 'registration_course_id')
                    ->with('lectures');
                }, 'courseCategories' => function($courseCategorie){
                    $courseCategorie->select('id', 'name', 'capacity', 'registration_course_id')->with(['lectures' => function($lecture){
                        $lecture->select('id', 'registration_course_category_id', 'day', 'start_time','end_time','place');
                    }]);
            }])->select('id','registration_plan_id', 'course_id');
        }])
        ->find(1);
       // $course_group = RegistrationCourseGroup::with('lectures')->find(1);
        //return $course_group;
        
        return $reg;
    }
}
