<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Validator;
use JWTFactory;
use JWTAuth;
use JWTAuthException;
use App\Models\RegistrationPlan;
use App\Models\Course;
use App\Models\StudentOpenedCourse;

class RegistrationPlanController extends Controller
{
    public function courses()
    {
        $reg = RegistrationPlan::select('id', 'study_year_semester_id','study_plan_id', 'faculty_id', 'department_id')
        ->with(['registrationCourses' => function($registrationCourse){
            $registrationCourse->with(['course' => function($course){
                    $course->select('id', 'code');
                }, 'courseGroups' => function($courseGroup){
                    $courseGroup->select('id', 'name', 'capacity', 'registration_course_id')->with(['lectures' => function($lecture){
                        $lecture->select('id', 'registration_course_group_id', 'day', 'start_time','end_time','place');
                    }]);
                }, 'courseCategories' => function($courseCategorie){
                    $courseCategorie->select('id', 'name', 'capacity', 'registration_course_id')->with(['lectures' => function($lecture){
                        $lecture->select('id', 'registration_course_category_id', 'day', 'start_time','end_time','place');
                    }]);
            }])->select('id','registration_plan_id', 'course_id');
        }
        ])
        ->find(1);
        
        //return $reg->studyPlan()->details[5]->prerequisite_courses;
        $pre_courses = array();
        //return $reg->studyPlan;
        if($reg->studyPlan != null)
        {
            foreach($reg->studyPlan->details as $detail)
            {
                $pre_courses[$detail->course_id] = array();
                foreach($detail->prerequisite_courses as $pre_req_course)
                {
                    array_push($pre_courses[$detail->course_id], $pre_req_course->id);
                }
            }
        }
        $reg['pre_courses'] = $pre_courses;


        $courses_from_reg = array();
        foreach($reg->registrationCourses as $registrationCourse)
        {
            array_push($courses_from_reg, $registrationCourse->course->id);
        }

        $courses_hours = array();
        $courses = array();
        $plans = $reg->studyPlan->get();
        foreach($courses_from_reg as $course_id)
        {
            foreach($plans as $plan)
            {
                $course_plans_details = $plan->courseDetails($course_id);
                if($course_plans_details != null) break;
            }
            $courses_hours = $course_plans_details->credit_hours;
            $courses[$course_id] = $courses_hours;
        }
        $reg['courses_hours'] = $courses;

        /*$open_courses = StudentOpenedCourse::all();
        ->pluck('course_id');
        dd($open_courses);
        foreach($courses_from_reg as $course_from_reg)
        {
            if(in_array($course_from_reg, $open_courses))
            {
                dd($course_from_reg);
            }
        }*/
        return $reg;
    }
}


