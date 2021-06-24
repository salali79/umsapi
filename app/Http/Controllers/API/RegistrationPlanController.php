<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\finalRegisterRequest as finalRegisterRequest;
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
use App\Models\StudentContact;
use App\Models\RegistrationCourseCategory;
use App\Models\RegistrationCourseGroup;
use App\Http\Controllers\API\ProgramController as ProgramController;


class RegistrationPlanController extends Controller
{
    public $current_study_year_id = 20 ;
    public $current_semester_id = 3 ;
	public $previous_semester_id = 2;
    public $minimum_registered_hours = 2;
    public $required_courses_ids = [];//[467,468,469];
    public $default_finance_hours = 0 ;
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
    public $program_days =[
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
      $this->middleware('auth:student', ['except' => ['show_student_registered', 'handle', 'do_handle', 'do_handle_all']]);
      $this->guard = "student";
      $this->request = $request;
    }
    public function current_student(Request $request)
    {
        if(!is_null($request->lang)) app()->setLocale($request->lang);
        $headers = apache_request_headers();
        $request->headers->set('Authorization', $headers['Authorization']);
        $token = $request->headers->get('Authorization');
        JWTAuth::setToken($token);
        $std = auth('student')->user();
        return $std;
    }
    //-1 study_year_semester problem
    //-2 last_registration_plan problem
    public function studyYearSemesterId(){

        $study_year_semester = StudyYearSemester::where('study_year_id',$this->current_study_year_id)
            ->where('semester_id',$this->current_semester_id)->first();

        if($study_year_semester) return $study_year_semester->id ;
        else return -1;
    }
    public function get_last_registration_plan_id(Request $request){
        $std = $this->current_student($request);
        if($this->studyYearSemesterId() != -1)
        {
            if($std->department)
            {
                $reg_plan = RegistrationPlan::where('study_year_semester_id', $this->studyYearSemesterId())
                    ->where('faculty_id', $std->faculty->id)
                    ->where('department_id', $std->department->id)->first();
            }
            else
            {
                $reg_plan = RegistrationPlan::where('study_year_semester_id', $this->studyYearSemesterId())
                    ->where('faculty_id', $std->faculty->id)->first();
            }
            if($reg_plan) return $reg_plan->id;
            else return -2;
        }
        else return -1;
    }
    public function get_category_and_group_id_from_registered_course(Request $request)
    {
        $std = $this->current_student($request);
        $registered_course = StudentRegisteredCourse::where('student_id', $std->id)
            ->where('course_id', $request->course_id)->first();
        $group_id = null;
        $category_id = null;
        if($registered_course)
        {
            $group_id = $registered_course->registration_course_group_id;
            $category_id = $registered_course->registration_course_category_id;
            return response()->json([
                'status' => 'success',
                'group_id' => $group_id,
                'category_id' => $category_id
            ]);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'المادة غير مسجلة'
        ]);
    }
    public function index(Request $request)
    {
        $student = $this->current_student($request);

        $student_finance_hours = $student->studentFinanceAllowedHours($this->current_study_year_id,$this->current_semester_id) == 0 ?
            $this->default_finance_hours :
            $student->studentFinanceAllowedHours($this->current_study_year_id,$this->current_semester_id)  ;

            
        $student_academic_hours = $student->studentAcademicAllowedHours($this->current_study_year_id,$this->previous_semester_id);
        $student_registered_hours = $student->StudentRegisteredCoursesHours();
        $minimum_registered_hours = $this->minimum_registered_hours;

        if($this->studyYearSemesterId() == -1)
        {
            return response()->json([
                'status' => 'error',
                'message' => 'خطأ في العام او الفصل الدراسي'
            ]);
        }
        $registration_plan = RegistrationPlan::where('faculty_id',$student->faculty_id)
            ->where('department_id',$student->department_id)
            ->where('study_year_semester_id',$this->studyYearSemesterId())->first();

        if(is_null($registration_plan))
        {
            return response()->json([
                'status' => 'error',
                'message' => 'خطة التسجيل غير متاحة'
            ]);
        }
         if($registration_plan->status == 0){

             return response()->json([
                 'status' => 'error',
                 'message' => 'التسجيل مغلق الآن ',
             ]);
         }
         else {
             $registration_plan_courses =  $registration_plan->registrationCourses;

             $student_study_plan = $student->StudentStudyPlan();

             $student_opened_courses = $student->studentOpenedCourses;

             $stud_opened_courses_ids = $student_opened_courses ? $student_opened_courses->pluck('course_id') : [];
             $reg_course = [];
             $registration_course_arr = [];
             if (count($stud_opened_courses_ids) !=0 && count($registration_plan_courses) != 0){

                 $student_registration_courses = $registration_plan_courses->whereIn('course_id',$stud_opened_courses_ids) ;


                 foreach ($student_registration_courses as  $registration_course ){
                     if ($student_study_plan && (!$registration_course->groups_count == 0 || !$registration_course->categories_count == 0 )){
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
                     'minimum_registered_hours' => $minimum_registered_hours,
                     'student_registered_hours' => $student_registered_hours,
                     'registration_courses' => $registration_course_arr

                 ];
             }

             else { return response()->json([
                 'status' => 'error',
                 'message' => 'لا يوجد مقررات متاحة'
             ]); }
         }


    }
    public function store(Request $request)
    {
        $std = $this->current_student($request);

        $t = 1;
        ///---CHECK HOURS---///
        $finance_allow_hours =  $std->studentFinanceAllowedHours($this->current_study_year_id,$this->current_semester_id) == 0 ?
            $this->default_finance_hours :
            $std->studentFinanceAllowedHours($this->current_study_year_id,$this->current_semester_id)  ;

        $academic_allow_hours = $std->studentAcademicAllowedHours($this->current_study_year_id , $this->previous_semester_id);
        $minimum = min($finance_allow_hours, $academic_allow_hours);

        try {
            $course_hours = $std->StudentCourseHours($request->course_id);
        } catch(\Exception $ex){
            return response()->json([
                'status' => 'error',
                'message' => 'مقرر غير متاح'
            ]);
        }
        $course_hours + $std->StudentRegisteredCoursesHours() <= $minimum ? '':$t=0 ;

        ////cross hours////
        if($t == 0)
        {
            $cross = "";
            if($minimum == $finance_allow_hours) $cross = "المالية";
            else if($minimum == $academic_allow_hours) $cross = "الأكاديمية";
            return response()->json([
                'status' => 'error',
                'message' => 'تم تجاوز الساعات '.$cross,
            ]);
        }
        ///---CHECK DATE---///
        $category_hours = array();
        $group_hours = array();

        if($request->group_id != null)
        {
            $course_group = RegistrationCourseGroup::where('id', $request->group_id)->first();
            $check_capacity = null ;
            if($course_group) $check_capacity = $course_group->registered_student_count < $course_group->capacity;
            if($check_capacity)
            {
                if($course_group)
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
            else{
                return response()->json([
                    'status' => 'error',
                    'message' => 'المجموعة ممتلئة',
                ]);
            }
        }
        if($request->category_id != null)
        {
            $course_category = RegistrationCourseCategory::where('id', $request->category_id)->first();
            $check_capacity = null ;
            if($course_category) $check_capacity = $course_category->registered_student_count < $course_category->capacity;
            if($check_capacity)
            {
                if($course_category)
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
            else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'الفئة ممتلئة',
                ]);
            }
        }

        $hours = array_merge($group_hours, $category_hours);
        $ProgramController = new ProgramController();
        $conflicted_course = null;
        $check_hours = 0;
        $std_program = ProgramSchedule::where('student_id', $std->id)->first();
        foreach($hours as $hour)
        {
            //221 47 46
            $res = $ProgramController->add_course_time($hour, $std);
            $res = json_decode($res->getContent(), true);
            if($res['status'] == 'error')
            {
                $t=0;
                break;
            }
            $check_hours += 1;
        }


        if($t == 1 && $check_hours!=0)
        {
            $student_registered_course = new StudentRegisteredCourse();
            $student_registered_course->student_id = $std->id;
            $student_registered_course->course_id = $request->course_id;
            $student_registered_course->registration_course_category_id = $request->category_id;
            $student_registered_course->registration_course_group_id = $request->group_id;
            $student_registered_course->registration_plan_id = 1;
            //$this->get_last_registration_plan_id($request);
            $student_registered_course->status = '0';
            $student_registered_course->save();
            return response()->json([
                'status' => 'success',
                'message' => 'تم التسجيل بنجاح',
            ]);
        }
        else{
            $altered_std_program = ProgramSchedule::where('student_id', $std->id)->first();
            if($altered_std_program)
            {
                $altered_std_program->update([
                    'free_hours' => $std_program->free_hours ?: null,
                ]);
            }
            return response()->json([
                'status' => 'error',
                'message' => 'تعارض في المواعيد',
            ]);
        }

    }
    public function delete(Request $request)
    {
        $std = $this->current_student($request);
        $course_id = $request->course_id;

        $course = StudentRegisteredCourse::where('student_id', $std->id)->where('course_id', $course_id)->first();
        if(is_null($course))
        {
            return response()->json([
                'status' => 'error',
                'message' => 'المقرر غير مسجل مسبقا'
            ]);
        }

        $course->forceDelete();
        ///---HANDLE HOURS---///
        $reminded_courses = StudentRegisteredCourse::where('student_id', $std->id)->get();
        $groups = array();
        $categories = array();
        foreach ($reminded_courses as $reminded_course)
        {
            $reminded_course->registration_course_category_id ?
                array_push($categories, $reminded_course->registration_course_category_id) : '';
            $reminded_course->registration_course_group_id ?
                array_push($groups, $reminded_course->registration_course_group_id) : '';
        }

        $program  = ProgramSchedule::where('student_id', $std->id)->first();
        $t = 1;
        if($program)
        {
            $program->forceDelete();
            $category_hours = array();
            $group_hours = array();
            $hours = array();

            foreach($groups as $group)
            {
                $course_group = RegistrationCourseGroup::where('id', $group)->first();
                if($course_group)
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
                    array_push($hours,$group_hours);
                }
            }
            foreach($categories as $category)
            {
                $course_category = RegistrationCourseCategory::where('id', $category)->first();
                if($course_category)
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
                    array_push($hours,$category_hours);
                }
            }

            $ProgramController = new ProgramController();
            $conflicted_course = null;
            $check_hours = 0;
            foreach($hours as $hour)
            {
                //dd($hour[0]['day']);
                $res = $ProgramController->add_course_time($hour[0], $std);
                $res = json_decode($res->getContent(), true);
                if($res['status'] == 'error')
                {
                    $t=0;
                    break;
                }
                $check_hours = 1;
            }
        }

        if($t == 1)
        {
            return response()->json([
                'status' => 'success',
                'message' => 'تم الحذف بنجاح'
            ]);
        } else{
            return response()->json([
                'status' => 'error',
                'message' => 'لم يتم الحذف بنجاح'
            ]);
        }

    }
    public function update(Request $request)
    {
        $std = $this->current_student($request);
        $backup_program = $std->programSchedule;
        $backup_registered_course = StudentRegisteredCourse::where('student_id', $std->id)
                                                              ->where('course_id', $request->course_id)
                                                              ->first();
        $transaction = 1; 
        $msg = "";
        $del_res = $this->delete($request);
        $del_res = json_decode($del_res->getContent(), true);
        if($del_res['status'] == 'error')
        {
            $transaction = 0;
            $msg = 'خطأ في الحذف ';
        }
        $store_res = $this->store($request);
        $store_res = json_decode($store_res->getContent(), true);
        if($store_res['status'] == 'error')
        {
            $transaction = 0;
            $msg = 'خطأ في التخزين';
        }
        if($transaction ==0 )
        {
            /*$program = $std->programSchedule;
            $program->update([$backup_program]);
            StudentRegisteredCourse::create([$backup_registered_course]);*/
            return response()->json([
                'status' => 'error',
                'message' => $msg
            ]);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'تم التعديل بنجاح'
        ]);
    }
    public function delete_all_student_registered_courses(Request $request){

        $student = $this->current_student($request);
        $program = $student->programSchedule;
        $clear = StudentRegisteredCourse::where('student_id',$student->id)->forceDelete();
        if($program)
        {
            $program->forceDelete();
        }
        return response()->json([
            'status' => 'success'
        ]);

    }
    public function final_add_course(finalRegisterRequest $requests)
    {
        $std = $this->current_student($requests);

        $required_courses = $this->required_courses_ids;


       if(is_null($std->StudentStudyPlan()))
       {
           return response()->json([
               'status' => 'error',
               'message' => 'لا يوجد خطة دراسية'
           ]);
       }
       
       if($std->StudentRegisteredCoursesHours() < $this->minimum_registered_hours)
       {
        return response()->json([
            'status' => 'error',
          'message' => 'عدد الساعات المسجلة أقل من الحد الادنى ',
        ]);
       }
        else
        {
            $student_required_course =[];
            foreach ($required_courses as $course_id){

                $opened_course = $std->hasOpenedCourse($course_id);
                if ( $opened_course != null){
                    if(($opened_course->course_status == 3 || $opened_course->course_status == 2 ) && $std->studentRegisteredCourses->where('course_id',$course_id)->first() == null)
                    {
                        $required = $opened_course->course->name ;
                        array_push($student_required_course,$required);
                    }
                }
            }
            if(count($student_required_course) > 0)

                return response()->json([
                    'status' => 'error',
                    'message' => 'يجب عليك تسجيل ' ,
                    'required_courses' => $student_required_course
                ]);

            else 
            {
                $student_registered_course = StudentRegisteredCourse::where('student_id', $std->id);

                if ($student_registered_course != null)
                    $student_registered_course->update([
                        'status' => '1',
                        'updated_by' => $std->id
                    ]);

                    $contact = StudentContact::where('student_id', $std->id)->first();
                    if($contact)
                    {
                        $contact->update([
                            'mobile_1' => $requests->mobile,
                            'email' => $requests->email
                        ]);
                    }
                    else 
                    {
                        StudentContact::create([
                            'student_id' => $std->id,
                            'mobile_1' => $requests->mobile,
                            'email' => $requests->email
                        ]);
                    }
                return response()->json([
                    'status' => 'success',
                    'message' => 'تم تثبيت التسجيل بنجاح',
                ]);
            }
        }

    }
    public function get_student_program(Request $request)
    {
        $std = $this->current_student($request);
        $registered_courses  = $std->studentRegisteredCourses;
        if(count($registered_courses) > 0 )
        {
            $program = $registered_courses->map(function ($registered_course){
                $course = $registered_course->course;

                $group = $registered_course->registrationCourseGroup;
                $course_group_lectures =[];
                if($group != null){
                    $group_lectures = $group->lectures->map(function ($lecture) {

                        return [
                            'start_time' => $lecture->start_time,
                            'end_time' => $lecture->end_time,
                            'day' => $lecture->day,
                            'place' => $lecture->place
                        ];


                    });
                    $course_group_lectures = [
                        'group_name' => $group->name,
                        'group_lectures' => $group_lectures
                    ] ;
                }
                $category = $registered_course->registrationCourseCategory;
                $course_category_lectures =[];
                if($category != null){
                    $category_lectures = $category->lectures->map(function ($lecture) {

                        return [
                            'start_time' => $lecture->start_time,
                            'end_time' => $lecture->end_time,
                            'day' => $lecture->day,
                            'place' => $lecture->place
                        ];
                    });
                    $course_category_lectures = [
                        'category_name' => $category->name,
                        'category_lectures' => $category_lectures
                    ] ;
                }

                return [
                    'course_code' => $course->code,
                    'course_name' => $course->name,
                    'course_group' => $course_group_lectures,
                    'course_category' => $course_category_lectures,

                ];
            });

            $week_program = [] ;
            $group_program = [] ;
            $category_program = [] ;
            if (count($registered_courses) > 0 ) {

                foreach ($registered_courses as $registered_course){
                    $course = $registered_course->course;
                    $group = $registered_course->registrationCourseGroup;
                    if ($group != null) {

                        $group_lectures = $group->lectures ;
                        foreach ($group_lectures as $lecture){

                            $group_program =  [
                                'course_code' => $course->code,
                                'course_name' => $course->name,
                                'group_name' => $group->name,
                                'start_time' => $lecture->start_time,
                                'end_time' => $lecture->end_time,
                                'place' => $lecture->place,

                            ];

                            
                            $week_program[$this->program_days[$lecture->day]][] = $group_program;

                        }
                    }
                    $category = $registered_course->registrationCourseCategory;
                    if ($category != null) {

                        $category_lectures = $category->lectures ;
                        foreach ($category_lectures as $lecture){

                            $category_program =  [
                                'course_code' => $course->code,
                                'course_name' => $course->name,
                                'category_name' => $category->name ,
                                'start_time' => $lecture->start_time,
                                'end_time' => $lecture->end_time,
                                'place' => $lecture->place ,
                            ];

                            $week_program[$this->program_days[$lecture->day]][] = $category_program;

                        }
                    }
                }
            }
            return response()->json([
                'status' => 'success',
                'program' => $program ,
                'program_days' => $this->program_days,
                'mobile_program' => $week_program
            ]);
        } else{
            return response()->json([
                'status' => 'success',
                'program' => [] ,
                'program_days' => [],
                'mobile_program' => []
            ]);
        }

    }
    public function handle($academic_number)
    {
        $std = Student::where('academic_number', $academic_number)->first();
        $registered_courses = $std->studentRegisteredCourses;

        if (count($registered_courses) > 0) {

            $program = $registered_courses->map(function ($registered_course) {
                $course = $registered_course->course;

                $group = $registered_course->registrationCourseGroup;
                $course_group_lectures = [];
                if ($group != null) {
                    $group_times = array();
                    $group_lectures = $group->lectures->map(function ($lecture) {

                        return [
                            'start' => $lecture->start_time,
                            'end' => $lecture->end_time,
                            'day' => $lecture->day,
                        ];


                    });

                    $course_group_lectures = [
                        'group_name' => $group->name,
                        'group_lectures' => $group_lectures
                    ];
                }
                $category = $registered_course->registrationCourseCategory;
                $course_category_lectures = [];
                if ($category != null) {

                    $category_lectures = $category->lectures->map(function ($lecture) {

                        return [
                            'start' => $lecture->start_time,
                            'end' => $lecture->end_time,
                            'day' => $lecture->day,
                        ];
                    });

                    $course_category_lectures = [
                        'category_name' => $category->name,
                        'category_lectures' => $category_lectures
                    ];
                }

                return [
                    'course_name' => $course->name,
                    'course_id' => $course->id,
                    'course_group' => $course_group_lectures,
                    'course_category' => $course_category_lectures,
                ];
            });

            return response()->json([
                'program' => $program
            ]);
        }
    }
    public function do_handle($academic_number)
    {
        $std = Student::where('academic_number', $academic_number)->first();
        $registered_courses = $std->studentRegisteredCourses;
        $compare_all = array();
        $t = 0;

        $check_cat = 0;
        $check_gr = 0;
        if (count($registered_courses) > 0) {

            $program = $registered_courses->map(function ($registered_course ) use ($check_cat,$check_gr) {
                $course = $registered_course->course;

                $group = $registered_course->registrationCourseGroup;
                $course_group_lectures = [];
                if ($group != null) {
                    $check_gr = 1;
                    $group_times = array();
                    $group_lectures = $group->lectures->map(function ($lecture) {


                        $start_time = substr($lecture->start_time,0,-3);
                        $end_time = substr($lecture->end_time,0,-3);
                        $day = $this->days[$lecture->day];
                        return [
                            'start' => $start_time,
                            'end' => $end_time,
                            'day' => $day,
                        ];


                    });

                    $course_group_lectures = [
                        'group_id' => $group->id,
                        'group_name' => $group->name,
                        'group_lectures' => $group_lectures
                    ];
                }
                $category = $registered_course->registrationCourseCategory;
                $course_category_lectures = [];
                if ($category != null) {

                    $check_cat  = 1;
                    $category_lectures = $category->lectures->map(function ($lecture) {


                        $start_time = substr($lecture->start_time,0,-3);
                        $end_time = substr($lecture->end_time,0,-3);
                        $day = $this->days[$lecture->day];
                        return [
                            'start' => $start_time,
                            'end' => $end_time,
                            'day' => $day,
                        ];
                    });

                    $course_category_lectures = [
                        'category_id' => $category->id,
                        'category_name' => $category->name,
                        'category_lectures' => $category_lectures
                    ];
                }

                return [
                    'course_name' => $course->name,
                    'course_id' => $course->id,
                    'course_group' => $course_group_lectures,
                    'course_category' => $course_category_lectures,
                    'check_category' => $check_cat,
                    'check_group' => $check_gr
                ];
            });

            //return $program[1]['course_group'];
            $group_times = array();
            $category_times = array();

            foreach ($program as $item) {

                $check_category = $item['check_category'];
                $check_group = $item['check_group'];
                //return $item['course_group']['group_lectures'];
                //if($program[0]['course_category']) if($program[0]['course_group'])
                if($check_group > 0)
                {
                    foreach($item['course_group']['group_lectures'] as $lecture)
                    {
                        array_push($group_times,  $lecture);
                    }
                }
                if($check_category > 0)
                {
                    foreach($item['course_category']['category_lectures'] as $lecture)
                    {
                        array_push($category_times,  $lecture);
                    }
                }
            }

            $hours = array_merge($group_times, $category_times);


            $ProgramController = new ProgramController();
            $conflicted_course = null;
            $check_hours = 0;
            $old_std_program = ProgramSchedule::where('student_id', $std->id)->first();
            $compare = array();
            $compare['std_id'] = $std->id;
            if($old_std_program)
            {
                $compare['old_std_program'] = $old_std_program->free_hours;
                $old_std_program->forceDelete();
            }
            foreach($hours as $hour)
            {
                $res = $ProgramController->add_course_time($hour, $std);
                $res = json_decode($res->getContent(), true);
                if($res['status'] == 'error')
                {
                    $t=1;
                    break;
                }
                $check_hours += 1;
            }
            if($t == 1)
            {
                return response()->json([
                    'status' => 'error',
                    'message' => 'conflict'
                ]);
            }
            $new_std_program = ProgramSchedule::where('student_id', $std->id)->first();
            $compare['new_std_program'] = $new_std_program->free_hours;

            array_push($compare_all, $compare);
            return response()->json([
                'status' => 'success',
                'compare all' => $compare_all
            ]);
        }
        else {
            $std_program = ProgramSchedule::where('student_id', $std->id)->first();
            if($std_program)
            {
                $std_program->forceDelete();
            }
            return response()->json([
                'sattus' => 'success',
                'message' => 'program deleted'
            ]);
        }

    }
    public function show_student_registered($faculty_id, $department_id)
    {
        $stds = Student::where('faculty_id', $faculty_id)
            ->where('department_id', $department_id)
            ->get();
        $all_programs = array();
        foreach($stds as $std)
        {
            $t = 0;
            $registered_courses = $std->studentRegisteredCourses;

            if (count($registered_courses) > 0) {

                $check_cat = 0;
                $program = $registered_courses->map(function ($registered_course) use($check_cat) {
                    $course = $registered_course->course;

                    $group = $registered_course->registrationCourseGroup;
                    $course_group_lectures = [];
                    if ($group != null) {
                        $group_times = array();
                        $group_lectures = $group->lectures->map(function ($lecture) {


                            $start_time = substr($lecture->start_time,0,-3);
                            $end_time = substr($lecture->end_time,0,-3);
                            $day = $this->days[$lecture->day];
                            return [
                                'start' => $start_time,
                                'end' => $end_time,
                                'day' => $day,
                            ];


                        });

                        $course_group_lectures = [
                            'group_id' => $group->id,
                            'group_name' => $group->name,
                            'group_lectures' => $group_lectures
                        ];
                    }
                    $category = $registered_course->registrationCourseCategory;
                    $course_category_lectures = [];
                    if ($category != null) {
                        $check_cat  = 1;

                        $category_lectures = $category->lectures->map(function ($lecture) {


                            $start_time = substr($lecture->start_time,0,-3);
                            $end_time = substr($lecture->end_time,0,-3);
                            $day = $this->days[$lecture->day];
                            return [
                                'start' => $start_time,
                                'end' => $end_time,
                                'day' => $day,
                            ];
                        });

                        $course_category_lectures = [
                            'category_id' => $category->id,
                            'category_name' => $category->name,
                            'category_lectures' => $category_lectures
                        ];
                    }

                    return [
                        'course_name' => $course->name,
                        'course_id' => $course->id,
                        'course_group' => $course_group_lectures,
                        'course_category' => $course_category_lectures,
                        'check category' => $check_cat
                    ];
                });

                $program['academic_number'] = $std->academic_number;
                $program['student_id'] = $std->id;
                $program['status'] = $registered_courses->first()->status;
                $old_program  = ProgramSchedule::where('student_id', $std->id)->first();
                $program['old_program'] = $old_program->free_hours;
                array_push($all_programs, $program);

                //return $program;
            }

            //$all_programs['old_program'] = $old_program;
        }
        return response()->json([
            'all_programs' => $all_programs
        ]);
    }
    public function do_handle_all($faculty_id, $department_id)
    {
        if(is_null($department_id))
        {
            $stds = Student::where('faculty_id', $faculty_id)
                ->where('department_id', $department_id)
                ->get();
        }
        else
        {
            $stds = Student::where('faculty_id', $faculty_id)
                ->get();
        }
        $done = 1;
        $compare_all = array();
        foreach($stds as $std)
        {
            $t = 0;
            $registered_courses = $std->studentRegisteredCourses;

            $check_cat = 0;
            $check_gr = 0;
            if (count($registered_courses) > 0) {

                $program = $registered_courses->map(function ($registered_course ) use ($check_cat,$check_gr) {
                    $course = $registered_course->course;

                    $group = $registered_course->registrationCourseGroup;
                    $course_group_lectures = [];
                    if ($group != null) {
                        $check_gr = 1;
                        $group_times = array();
                        $group_lectures = $group->lectures->map(function ($lecture) {


                            $start_time = substr($lecture->start_time,0,-3);
                            $end_time = substr($lecture->end_time,0,-3);
                            $day = $this->days[$lecture->day];
                            return [
                                'start' => $start_time,
                                'end' => $end_time,
                                'day' => $day,
                            ];


                        });

                        $course_group_lectures = [
                            'group_id' => $group->id,
                            'group_name' => $group->name,
                            'group_lectures' => $group_lectures
                        ];
                    }
                    $category = $registered_course->registrationCourseCategory;
                    $course_category_lectures = [];
                    if ($category != null) {

                        $check_cat  = 1;
                        $category_lectures = $category->lectures->map(function ($lecture) {


                            $start_time = substr($lecture->start_time,0,-3);
                            $end_time = substr($lecture->end_time,0,-3);
                            $day = $this->days[$lecture->day];
                            return [
                                'start' => $start_time,
                                'end' => $end_time,
                                'day' => $day,
                            ];
                        });

                        $course_category_lectures = [
                            'category_id' => $category->id,
                            'category_name' => $category->name,
                            'category_lectures' => $category_lectures
                        ];
                    }

                    return [
                        'course_name' => $course->name,
                        'course_id' => $course->id,
                        'course_group' => $course_group_lectures,
                        'course_category' => $course_category_lectures,
                        'check_category' => $check_cat,
                        'check_group' => $check_gr
                    ];
                });

                //return $program[1]['course_group'];
                $group_times = array();
                $category_times = array();

                foreach ($program as $item) {

                    $check_category = $item['check_category'];
                    $check_group = $item['check_group'];
                    //return $item['course_group']['group_lectures'];
                    //if($program[0]['course_category']) if($program[0]['course_group'])
                    if($check_group > 0)
                    {
                        foreach($item['course_group']['group_lectures'] as $lecture)
                        {
                            array_push($group_times,  $lecture);
                        }
                    }
                    if($check_category > 0)
                    {
                        foreach($item['course_category']['category_lectures'] as $lecture)
                        {
                            array_push($category_times,  $lecture);
                        }
                    }
                }

                $hours = array_merge($group_times, $category_times);


                $ProgramController = new ProgramController();
                $conflicted_course = null;
                $check_hours = 0;
                $old_std_program = ProgramSchedule::where('student_id', $std->id)->first();
                $compare = array();
                $compare['std_id'] = $std->id;
                if($old_std_program)
                {
                    $compare['old_std_program'] = $old_std_program->free_hours;
                    $old_std_program->forceDelete();
                }
                foreach($hours as $hour)
                {
                    $res = $ProgramController->add_course_time($hour, $std);
                    $res = json_decode($res->getContent(), true);
                    if($res['status'] == 'error')
                    {
                        $t=0;
                        break;
                    }
                    $check_hours += 1;
                }
                $t = 1;
                $new_std_program = ProgramSchedule::where('student_id', $std->id)->first();
                $compare['new_std_program'] = $new_std_program->free_hours;

                array_push($compare_all, $compare);
            }
            else {
                $std_program = ProgramSchedule::where('student_id', $std->id)->first();
                if($std_program)
                {
                    $std_program->forceDelete();
                }
                $t = 1;
                /*return response()->json([
                    'sattus' => 'success',
                    'message' => 'program deleted'
                ]);*/
            }
            if($t != 1)
            {
                $done = 0;
                break;
            }
        }
        if($done != 1)
        {
            return response()->json([
                'status' => 'error',
            ]);
        }
        else
        {
            return response()->json([
                'status' => 'success',
                'compare all' => $compare_all
            ]);
        }
    }
}
