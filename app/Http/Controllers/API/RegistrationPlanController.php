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
        $reg = RegistrationPlan::with(['registrationCourses' => function($q){
            $q->with('course');
        }])->find(1);
        //with(['studyYearSemester', 'faculty', 'department'])
        //$reg;
        return $reg;
    }
}
