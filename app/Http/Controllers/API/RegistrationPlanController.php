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
use App\Models\StudyYearSemester;
use App\Models\Student;

class RegistrationPlanController extends Controller
{
    public $current_study_year_id = 20 ;
    public $current_semester_id = 2 ;

    public function __construct(Request $request)
    {
      $this->middleware('auth:student');
      $this->guard = "student";
    }
    public function studyYearSemesterId(){

        $study_year_semester = StudyYearSemester::where('study_year_id',$this->current_study_year_id)
            ->where('semester_id',$this->current_semester_id)->first();
        return $study_year_semester->id ;

    }
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
    public function index(Request $request)
    {

        $student = $this->current_student($request);
        //Student::find(3466); //authenticated user


        $student_finance_hours = $student->studentFinanceAllowedHours($this->current_study_year_id,$this->current_semester_id);
        $student_academic_hours = $student->studentAcademicAllowedHours($this->current_study_year_id,$this->current_semester_id);

        $registration_plan = RegistrationPlan::where('faculty_id',$student->faculty_id)
             ->where('department_id',$student->department_id)
             ->where('study_year_semester_id',$this->studyYearSemesterId())->first();


        $registration_plan_courses =  $registration_plan->registrationCourses;



         $student_study_plan = $student->StudentStudyPlan();


        $student_opened_courses = $student->studentOpenedCourses;
        $student_registered_courses = $student->studentRegisteredCourses ;

        $stud_opened_courses_ids = $student_opened_courses ? $student_opened_courses->pluck('course_id') : [];
        $reg_course = [];
        $registration_course_arr = [];
        if (count($stud_opened_courses_ids) !=0 ){

             $student_registration_courses = $registration_plan_courses->whereIn('course_id',$stud_opened_courses_ids) ;


             foreach ($student_registration_courses as  $registration_course ){
                 if ( (!$registration_course->groups_count == 0 || !$registration_course->categories_count == 0 )){
                     $course_credit_hours =  $student_study_plan->courseDetails($registration_course->course_id)->credit_hours;

                     $course_status = $student_opened_courses->where('course_id',$registration_course->course_id)->first()->course_status;

                     //$chosen = $student_opened_courses->where('course_id',$registration_course->course_id)->first()->isChosenForRegistration();

                     $course = [
                         'course_id' => $registration_course->course_id,
                         'course_code' => $registration_course->course->code ,
                         'course_name' => $registration_course->course->name ,
                         'course_credit_hours' => $course_credit_hours ,
                         'course_status' => $course_status 
                         //'chosen' => $chosen 
                         ]  ;

                     $groups =  $registration_course->courseGroups->map(function ($group){

                         $lectures = $group->lectures->map(function ($lecture){
                             return [
                                 'day' => $lecture->day,
                                 'start_time' => $lecture->start_time,
                                 'end_time' => $lecture->end_time,
                                 'place' => $lecture->place,

                             ];
                         });
                         return ['id' => $group->id , 'name' => $group->name , 'capacity' => $group->capacity ,'lectures' => $lectures];
                     });
                     $categories = $registration_course->courseCategories->map(function ($category){
                         $lectures = $category->lectures->map(function ($lecture){
                             return [
                                 'day' => $lecture->day,
                                 'start_time' => $lecture->start_time,
                                 'end_time' => $lecture->end_time,
                                 'place' => $lecture->place,

                             ];
                         });
                         return [
                             'id' => $category->id ,  'name' => $category->name , 'capacity' => $category->capacity ,'lectures' => $lectures];
                     });
                     $reg_course = $course;
                     $reg_course['groups_count'] = $registration_course->groups_count;
                     $reg_course['categories_count'] = $registration_course->categories_count;
                     $reg_course['groups'] = $groups;
                     $reg_course['categories'] = $categories;

                     array_push($registration_course_arr,$reg_course);

             }
             }

             return [
                 'finance_allowed_hours' => $student_finance_hours,
                 'academic_allowed_hours' => $student_academic_hours ,
                 'registration_courses' => $registration_course_arr,
                 ];
        }
        else {
                return [];
            }
    }

    
    
    
    
    
    /////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function courses()
    {
        $reg = RegistrationPlan::select('id', 'study_year_semester_id','study_plan_id', 'faculty_id', 'department_id')
        ->with(['registrationCourses' => function($registrationCourse){
            $registrationCourse->with([/*'course' => function($course){
                    $course->select('id', 'code');
                },*/ 'courseGroups' => function($courseGroup){
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


        $open_courses = StudentOpenedCourse::all();
        $opens = $open_courses->pluck('course_id')->toArray();
        $courses_from_reg = array();
        foreach($reg->registrationCourses as $registrationCourse)
        {
            if(in_array($registrationCourse->course_id, $opens))
            {
                array_push($courses_from_reg, $registrationCourse->course_id);
            }
        }

        /*if($reg->studyPlan != null)
        {
            foreach($reg->studyPlan->details as $detail)
            {
                if(in_array($detail->course_id, $opens))
                {
                    $pre_courses[$detail->course_id] = array();
                    foreach($detail->prerequisite_courses as $pre_req_course)
                    {
                        array_push($pre_courses[$detail->course_id], $pre_req_course->id);
                    }
                }
            }
        }*/



        $courses = array();
        $pre_courses = array();
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
            $courses_prerequist = $course_plans_details->prerequisite_courses;
            $pre_courses[$course_id] = array();
            foreach($courses_prerequist as $course_prerequist)
            {
                array_push($pre_courses[$course_id], $course_prerequist->id);
            }
        }
        $reg['courses_hours'] = $courses;
        $reg['pre_courses'] = $pre_courses;

        //relations.studyPlan
        //$reg->forget('studyPlan');
        //dd($reg->studyPlan);
        //$reg = \Arr::forget($reg->toArray(), $reg->studyPlan);
        //$reg = $reg->except('study_plans');

        $reg = $reg->only(['courses_hours', 'pre_courses', 'registrationCourses']);
        return $reg;
    }


}


