<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudyPlan;
use App\Models\Course;

class CourseController extends Controller
{
    public function index()
    {
        $study_plans = StudyPlan::paginate(10);
        $res = array();
        foreach($study_plans as $study_plan)
        {
            array_push($res, $study_plan->courses());
        }
        return $res;
    }
}
