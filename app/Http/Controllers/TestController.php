<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentOpenedCourse;
use App\Models\RegistrationPlan;

class TestController extends Controller
{
    public function open_table()
    {
        //course_id student_id course_status
        $reg = RegistrationPlan::find(1);
        
        $courses_from_reg = array();
        foreach($reg->registrationCourses as $registrationCourse)
        {
            array_push($courses_from_reg, $registrationCourse->course->id);
        }
        
    } 
}
