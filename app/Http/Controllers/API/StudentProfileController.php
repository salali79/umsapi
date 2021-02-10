<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterCourseRequest as RegisterCourseRequest;
use Intervention\Image\ImageManagerStatic as Image;
use Response;
use Auth;
use JWTFactory;
use JWTAuth;
use JWTAuthException;
use App\Models\Student;
use App\Models\Course;
use App\Models\studyPlanDetail;
use App\Models\FinanceAccount;
use App\Models\FinanceAllowedHours;
use App\Models\AcademicStatus;
use App\Models\AcademicSupervision;
use App\Models\StudentRegisteredCourse;
use App\Models\RegistrationCGL;
use App\Models\RegistrationCCL;

class StudentProfileController extends Controller
{
    protected $request;
    public function __construct(Request $request)
    {
      $this->middleware('auth:student');
      $this->guard = "student";
      $this->request = $request;
    }
    public function info(Request $request)
    {
        $std = current_student($request);
        $std->load('faculty', 'department', 'contact',
        'emergency', 'medicals', 'folderType',
        'studentFiles'
       );
        return $std;
    }
    public function personal_info(Request $request)
    {
        $std = current_student($request);
        $res = '{}';
        if(!is_null($std))
        {
            app()->setLocale('ar');
            $res = json_decode(json_encode(array(
                'first_name' => $std->first_name,
                'middle_name' => $std->middle_name,
                'last_name' => $std->last_name,
                'mother_name' => $std->mother_name,
                'birthplace' => $std->birthplace,
                'gender' => $std->gender,
                'nationality' => $std->nationality,
                'specific_features' => $std->specific_features,
                'civil_record' => $std->civil_record,
                'amaneh' => $std->amaneh,
                'writing_hand' => $std->writing_hand,
                'identity_number' => $std->identity_number,
                'national_number' => $std->national_number,
                'birthday_date' => $std->getBirthday()
            )));
        }
        return response()->json([
            'status' => 'success',
            'message' => 'personal info',
            'info' => $res,
            'action' => ''
        ]);
    }
    public function contact_info(Request $request)
    {
        $std = current_student($request);
        $res = '{}';
        if(!is_null($std->contact))
        {
            $std = $std->contact;
            app()->setLocale('ar');
            $res = json_decode(json_encode(array(
                'mobile_1' => $std->mobile_1,
                'mobile_2' => $std->mobile_2,
                'phone' => $std->phone,
                'father_mobile' => $std->father_mobile,
                'father_phone' => $std->father_phone,
                'mother_mobile' => $std->mother_mobile,
                'mother_phone' => $std->mother_phone,
                'facebook' => $std->facebook,
                'instagram' => $std->instagram,
                'whatsapp' => $std->whatsapp,
                'telegram' => $std->telegram,
                'imo' => $std->imo,
                'email' => $std->email,
                'current_address' => $std->current_address,
                'permanent_address' => $std->premanent_address,
            )));   
        }
        return response()->json([
            'status' => 'success',
            'message' => 'contact info',
            'info' => $res,
            'action' => ''
        ]);
    }
    public function emergency_info(Request $request)
    {
        $std = current_student($request)->emergency;
        $res = '{}';
        if(!is_null($std->emergency))
        {
            $std = $std->emergency;
            app()->setLocale('ar');
            $res = json_decode(json_encode(array(
                'phone' => $std->phone,
                'mobile' => $std->mobile,
                'email' => $std->email,
                'name' => $std->name,
                'relation_ship' => $std->relation_ship,
                'address' => $std->address
            )));  
        }
        return response()->json([
            'status' => 'success',
            'message' => 'emergency info',
            'info' => $res,
            'action' => ''
        ]);
    }
    public function registration_info(Request $request)
    {
        $std = current_student($request);
        $res = '{}';
        if(!is_null($std)) 
        {
            app()->setLocale('ar');
            $faculty_name = $std->faculty == null?  '':$std->faculty->name; 
            $department_name = $std->department == null?  '':$std->department->name; 
            $study_year_name = $std->StudentStudyYear($std->id) == null? '':$std->StudentStudyYear($std->id)->name;
            $register_way = $std->studentRegisterWay == null?  '':$std->studentRegisterWay->name; 
            $res = json_decode(json_encode(array(
                'faculty' => $faculty_name,
                'department' => $department_name,
                'study_year' => $study_year_name,
                'registration_date' => $std->registration_date,
                'academic_number' => $std->academic_number,
                'register_way' => $register_way
            )));
        }
        return response()->json([
            'status' => 'success',
            'message' => 'personal info',
            'info' => $res,
            'action' => ''
        ]);
    }

    ///////////////////////////////////////////////////////////////////////////////////////
    public function academic_allowed_hours()
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
    public function add_course(Request $request)
    {
        $std = current_student($request);
        $plans = $std->StudentStudyPlan()->get();
        $t = 1;
        ///---CHECK HOURS---///
        $finance_allow_hours = $this->finance_allowed_hours()->getData()->finance_hours;
        $academic_hours = $this->academic_allowed_hours()->getData()->academic_hours;
        $minimum = min($finance_allow_hours, $academic_hours);

        foreach($plans as $plan)
        {
            $course_plans_details = $plan->courseDetails($request->course_id);
            if($course_plans_details != null) break;
        }
        $course_hours = $course_plans_details->credit_hours;
        $course_hours <= $minimum ? '':$t=0 ;

        ///---CHECK DATE---///
        

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

    public function final_add_course(Request $requests)
    {
        $std = current_student($requests);
        //dd($requests[0]);
        $requests = $requests->all();
        foreach($requests as $request)
        {
            $student_registered_course = StudentRegisteredCourse::where('student_id', $std->id)
                                        ->where('course_id', $request['course_id'])
                                        ->where('registration_course_category_id', $request['category_id'])
                                        ->where('registration_course_group_id', $request['group_id'])
                                        ->where('registration_plan_id', $request['registration_plan_id']);
            $student_registered_course->update(['status' => '1']);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'final register successfully',
        ]);
    }

    public function delete_course(Request $request)
    {
        
    }

}
