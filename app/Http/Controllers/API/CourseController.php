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
use App\Models\Student;
use App\Models\Course;
use App\Models\StudyPlan;


class CourseController extends Controller
{

    public function index(Request $request)
    {
        if(!is_null($request->lang)) app()->setLocale($request->lang);
        $study_plan = StudyPlan::latest()->first();
        $res = $study_plan->courses()->toArray();
        array_slice($res,1,10); 
        return $res;
    }

    public function student_courses(Request $request)
    {
        $std = current_student($request);
        $non_dep = 0; $non_faculty = 0;
        $faculty_id = $std->faculty == null?  $non_faculty = 1:$std->faculty->id; 
        $department_id = $std->department == null?  $non_dep = 1:$std->department->id; 
        $courses  = '{}';
        if(!$non_faculty && !$non_dep) 
        {
            $courses = Course::query()
            ->where('department_id', $faculty_id)
            ->where('faculty_id', $department_id)
            ->paginate(10);
        }
        else if(!$non_faculty && $non_dep)
        {
            $courses = Course::query()
            ->where('faculty_id', $department_id)
            ->paginate(10);
        }
        else 
        {
            $courses = Course::paginate(10);
        }
        return $courses;
    }

}
