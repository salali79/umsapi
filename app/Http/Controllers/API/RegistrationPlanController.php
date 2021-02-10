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
use App\Models\studyPlanDetail;
use App\Models\FinanceAccount;
use App\Models\FinanceAllowedHours;
use App\Models\AcademicStatus;
use App\Models\AcademicSupervision;
use App\Models\StudentRegisteredCourse;
use App\Models\RegistrationCGL;
use App\Models\RegistrationCCL;
use App\Models\RegistrationCourse;
use App\Http\Controllers\ProgramController as ProgramController;


class RegistrationPlanController extends Controller
{
    public $current_study_year_id = 20 ;
    public $current_semester_id = 2 ;
    public $request;

    public function __construct(Request $request)
    {
      $this->middleware('auth:student');
      $this->guard = "student";
      $this->request = $request;
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
        //dd($student);
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

                     $chosen = $student_opened_courses->where('course_id',$registration_course->course_id)->first()->isChosenForRegistration();

                     $course = [
                         'course_id' => $registration_course->course_id,
                         'course_code' => $registration_course->course->code ,
                         'course_name' => $registration_course->course->name ,
                         'course_credit_hours' => $course_credit_hours ,
                         'course_status' => $course_status ,
                         'chosen' => $chosen ]  ;

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

        /*public function academic_allowed_hours()
        {
            $std = current_student($this->request);
    
            $academic_supervision = $std->academicSupervision
                                   ->where('study_year_id', 20)
                                   ->where('semester_id', 2)
                                   ->first();
            $academic_hours = $academic_supervision->academicStatus->hours;
            return response()->json([
                'status' => 'success',
                'academic_hours' => $academic_hours
            ]);
        }
        public function finance_allowed_hours()
        {
            $std = current_student($this->request);
            $finance_allowed = $std->financeDetails
                               ->where('study_year_id', 20)
                               ->where('semester_id', 2)
                               ->first();
    
            $finance_allow_hours = $finance_allowed->hours;
            return response()->json([
                'status' => 'success',
                'finance_hours' => $finance_allow_hours
            ]);
        }
*/
    public function store(Request $request)
    {

        $std = $this->current_student($request);
        $plans = $std->StudentStudyPlan()->get();
        $t = 1;
        ///---CHECK HOURS---///
        $finance_allow_hours = $std->studentAcademicAllowedHours(20, 2);
        //$this->finance_allowed_hours()->getData()->finance_hours;
        $academic_hours = $std->studentFinanceAllowedHours(20, 2);
        //$this->academic_allowed_hours()->getData()->academic_hours;
        $minimum = min($finance_allow_hours, $academic_hours);

        foreach($plans as $plan)
        {
            $course_plans_details = $plan->courseDetails($request->course_id);
            if($course_plans_details != null) break;
        }
        $course_hours = $course_plans_details->credit_hours;
        $course_hours <= $minimum ? '':$t=0 ;

        ///---CHECK DATE---///
        /*$course = RegistrationCourse::where('course_id', $request->course_id)->first();
        $course_group = $course->courseGroups->where('id', $request->group_id)->first();
        $course_category = $course->ccourseCategories->where('id', $request->category_id);
        $group_hours = $course_group->lectures->map( function($lecture){
            $start_time = date('g:i', strtotime($lecture->start_time));
            $end_time = date('g:i', strtotime($lecture->end_time));
            $day = $lecture->day;
            $id = $lecture->id;
            return [
                'id' => $id,
                'day' => $day,
                'start' => $start_time,
                'end' => $end_time
            ];
        });
        $category_hours = $course_category->lectures->map( function($lecture){
            $start_time = date('g:i', strtotime($lecture->start_time));
            $end_time = date('g:i', strtotime($lecture->end_time));
            $day = $lecture->day;
            $id = $lecture->id;
            return [
                'id' => $id,
                'day' => $day,
                'start' => $start_time,
                'end' => $end_time
            ];
        });


        dd($group_hours);*/

        if($t == 1)
        {
            $student_registered_course = new StudentRegisteredCourse();
            $student_registered_course->student_id = $std->id;
            $student_registered_course->course_id = $request->course_id;
            $student_registered_course->registration_course_category_id = $request->category_id;
            $student_registered_course->registration_course_group_id = $request->group_id;
            $student_registered_course->registration_plan_id = $request->registration_plan_id;
            $student_registered_course->status = '2';
            $student_registered_course->save();
            return response()->json([
                'status' => 'success',
                'message' => 'register successfully',
            ]);
        } else{
            return response()->json([
                'status' => 'error',
                'message' => 'conflict dates or cross the finance or academic hours',
            ]);
        }

    }
}


