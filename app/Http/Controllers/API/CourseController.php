<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudyPlan;
use App\Models\Course;

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


}
