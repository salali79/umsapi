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
        $reg = RegistrationPlan::find(1);
        $reg->with(['studyYearSemester', 'faculty', 'department'])->with(['registrationCourses' => function($q){
            $q->with('course');
        }]);
        return $reg;
    }
}
