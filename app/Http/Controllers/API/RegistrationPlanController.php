<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\RegistrationCourseCategory;
use App\Models\RegistrationCourseGroup;
use Illuminate\Http\Request;
use Auth;
use Validator;
use JWTFactory;
use JWTAuth;
use JWTAuthException;
use Spatie\OpeningHours\OpeningHours;
use App\Models\ProgramSchedule;
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
use App\Http\Controllers\API\ProgramController as ProgramController;


class RegistrationPlanController extends Controller
{
    public $current_study_year_id = 20 ;
    public $current_semester_id = 2 ;
    public $request;
    public $days = [
        '1' => 'saturday',
        '2' => 'sunday',
        '3' => 'monday',
        '4' => 'tuesday',
        '5' => 'wednesday',
        '6' => 'thursday',
        '7' => 'friday'
    ];

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


        $student_finance_hours = $student->studentFinanceAllowedHours($this->current_study_year_id,$this->current_semester_id);
        $student_academic_hours = $student->studentAcademicAllowedHours($this->current_study_year_id,$this->current_semester_id);

        $registration_plan = RegistrationPlan::where('faculty_id',$student->faculty_id)
            ->where('department_id',$student->department_id)
            ->where('study_year_semester_id',$this->studyYearSemesterId())->first();


        $registration_plan_courses =  $registration_plan->registrationCourses;

        $student_study_plan = $student->StudentStudyPlan();

        $student_opened_courses = $student->studentOpenedCourses;

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
                            ];});
                        return [
                            'id' => $group->id ,
                            'name' => $group->name ,
                            'capacity' => $group->capacity ,
                            'registered_student' => $group->registered_student_count ,
                            'lectures' => $lectures];
                    });
                    $categories = $registration_course->courseCategories->map(function ($category){
                        $lectures = $category->lectures->map(function ($lecture){
                            return [
                                'day' => $lecture->day,
                                'start_time' => $lecture->start_time,
                                'end_time' => $lecture->end_time,
                                'place' => $lecture->place,
                            ];});
                        return [
                            'id' => $category->id ,
                            'name' => $category->name ,
                            'capacity' => $category->capacity ,
                            'registered_student' => $category->registered_student_count ,
                            'lectures' => $lectures];
                    });
                    $reg_course = $course;
                    $reg_course['groups_count'] = $registration_course->groups_count;
                    $reg_course['categories_count'] = $registration_course->categories_count;
                    $reg_course['groups'] = $groups;
                    $reg_course['categories'] = $categories;

                    array_push($registration_course_arr,$reg_course);
                }}
            return [
                'finance_allowed_hours' => $student_finance_hours,
                'academic_allowed_hours' => $student_academic_hours ,
                'registration_courses' => $registration_course_arr,
            ];}

        else { return []; }

    }
    public function deleteStudentRegisteredCourses(Request $request){

        $student = $this->current_student($request);
        $program = $student->programSchedule;
        $clear = StudentRegisteredCourse::where('student_id',$student->id)->forceDelete();
        if(!is_null($program))
        {
            $program->forceDelete();
        }
        return response()->json([
            'status' => 'success'
        ]);

    }
    /////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function store(Request $request)
    {
        $std = $this->current_student($request);
        $t = 1;
        ///---CHECK HOURS---///
        $finance_allow_hours = $std->studentFinanceAllowedHours(20, 2);
        $academic_allow_hours = $std->studentAcademicAllowedHours(20, 2);
        $minimum = min($finance_allow_hours, $academic_allow_hours);

        $course_hours = $std->StudentCourseHours($request->course_id);
        $course_hours + $std->StudentRegisteredCoursesHours() <= $minimum ? '':$t=0 ;

        if($t == 0)
        {
            $cross = "";
            if($minimum == $finance_allow_hours) $cross = "finance";
            else if($minimum == $academic_allow_hours) $cross = "academic";
            return response()->json([
                'status' => 'error',
                'message' => 'cross the '.$cross.' hours',
            ]);
        }
        ///---CHECK DATE---///
        //$course = RegistrationCourse::where('course_id', $request->course_id)->first();
        $category_hours = array();
        $group_hours = array();

            if($request->group_id != null)
            {
                $course_group = RegistrationCourseGroup::where('id', $request->group_id)->first();
                    //$course->courseGroups->where('id', $request->group_id)->first();
                $check_capacity = $course_group->registered_student_count < $course_group->capacity;
                if(!is_null($course_group) && $check_capacity)
                {
                    $group_hours = $course_group->lectures->map( function($lecture){
                        $start_time = substr($lecture->start_time,0,-3);
                        $end_time = substr($lecture->end_time,0,-3);
                        $day = $this->days[$lecture->day];
                        $id = $lecture->id;
                        return [
                            'id' => $id,
                            'day' => $day,
                            'start' => $start_time,
                            'end' => $end_time
                        ];
                    });
                    $group_hours = $group_hours->toArray();
                }
            }
            if($request->category_id != null)
            {
                $course_category = RegistrationCourseCategory::where('id', $request->category_id)->first();
                    //$course->courseCategories->where('id', $request->category_id)->first();
                $check_capacity = $course_category->registered_student_count < $course_category->capacity;
                if(!is_null($course_category) && $check_capacity)
                {
                    $category_hours = $course_category->lectures->map( function($lecture){

                        $start_time = substr($lecture->start_time,0,-3);
                        $end_time = substr($lecture->end_time,0,-3);
                        $day = $this->days[$lecture->day];
                        $id = $lecture->id;
                        return [
                            'id' => $id,
                            'day' => $day,
                            'start' => $start_time,
                            'end' => $end_time
                        ];
                    });
                    $category_hours = $category_hours->toArray();
                }
            }
        $hours = array_merge($group_hours, $category_hours);
        $ProgramController = new ProgramController();
        $conflicted_course = null;
        $check_hours = 0;
        foreach($hours as $hour)
        {
            //221 47 46
            $res = $ProgramController->add_course_time($hour, $std);
            $res = json_decode($res->getContent(), true);
            if($res['status'] == 'error')
            {
                $t=0;
                //$conflicted_course = $hour->id;
                break;
            }
            $check_hours = 1;
        }

        if($t == 1 && $check_hours == 1)
        {
            $student_registered_course = new StudentRegisteredCourse();
            $student_registered_course->student_id = $std->id;
            $student_registered_course->course_id = $request->course_id;
            $student_registered_course->registration_course_category_id = $request->category_id;
            $student_registered_course->registration_course_group_id = $request->group_id;
            $student_registered_course->registration_plan_id = 1;
            $student_registered_course->status = '0';
            $student_registered_course->save();
            return response()->json([
                'status' => 'success',
                'message' => 'register successfully',
            ]);
        } else{
            return response()->json([
                'status' => 'error',
                'message' => 'conflict course dates',
            ]);
        }

    }

    public function delete(Request $request)
    {

    }
}


