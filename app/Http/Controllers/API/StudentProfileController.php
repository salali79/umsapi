<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterCourseRequest as RegisterCourseRequest;
use Intervention\Image\ImageManagerStatic as Image;
use Response;
use Auth;
use Validator;
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

class StudentProfileController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth:student');
      $this->guard = "student";
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
    public function info(Request $request)
    {
        $std = $this->current_student($request);
        $std->load('faculty', 'department', 'contact',
        'emergency', 'medicals', 'folderType',
        'studentFiles'
       );
        return $std;
    }
    public function personal_info(Request $request)
    {
        $std = $this->current_student($request);
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
        $std = $this->current_student($request);
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
        $std = $this->current_student($request)->emergency;
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
        $std = $this->current_student($request);
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
    public function register_course(RegisterCourseRequest $request)
    {
        $std = $this->current_student($request);
        $plans = $std->StudentStudyPlan()->get();
        $finance_account = FinanceAccount::where('student_id', $std->id)->first();
        $finance_allowed = FinanceAllowedHours::where('finance_account_id', $finance_account->id)
                           ->where('study_year_id', 12)
                           ->where('semester_id', 2)
                           ->first();

        $finance_allow_hours = $finance_allowed->hours;
        $academic_supervision = AcademicSupervision::where('student_id',$std->id)
                          ->where('study_year_id', 12)
                          ->where('semester_id', 2)
                          ->first();
        $academic_hours = $academic_supervision->academicStatus->hours;
        $minimum = min($finance_allow_hours, $academic_hours);
        $all_hours = 0;
        $t = 1;
        $objects = $request->all();
        foreach($objects as $object){
            foreach($plans as $plan)
            {
                $course_plans_details = $plan->courseDetails($object->course_id);
                if($course_plans_details != null) break;
            }
            $course_hours = $course_plans_details->credit_hours;
            $all_hours = $all_hours + $course_hours;
            $all_hours <= $minimum ? '':$t=0 ;
            if($t == 0) break;
        }

        if($t == 1)
        {
            foreach($objects as $object)
            {
                $student_registered_course = new StudentRegisteredCourse();
                $student_registered_course->student_id = $std->id;
                $student_registered_course->course_id = $object->course_id;
                $student_registered_course->registration_course_category_id = $object->category_id;
                $student_registered_course->registration_course_group_id = $object->group_id;
                $student_registered_course->registration_plan_id = $object->registration_plan_id;
                $student_registered_course->save();
            }
            return response()->json([
                'status' => 'success',
                'message' => 'register successfully',
                //'info' => $student_registered_course,
                'action' => ''
            ]);
        } else{
            return response()->json([
                'status' => 'error',
                'message' => 'cross the finance or academic hours',
                //'info' => $student_registered_course,
                'action' => ''
            ]);
        }
    }
    ///////////////////////////////////////////////////////////////////////////////////////

    //http://127.0.0.1:8000/uploads/students/students_1612604821.jpg
    /*protected function upload_image($item = null, $img = null)
    {
        $image = $img ?? request('image') ;
        if ($image) {
            
            $extension = $image->getClientOriginalExtension();

            $filenametostore = 'students_' . time() . '.' . $extension;

            if (!file_exists(public_path('uploads'))) {
                mkdir(public_path('uploads'), 0755);
            }

            if (!file_exists(public_path('uploads/students'))) {
                mkdir(public_path('uploads' . '/students'), 0755);
            }

            $img = Image::make($image)->save(public_path('uploads/students/' . $filenametostore));
            if($img){
                return $filenametostore;
            }
        }
        return false;

    }*/

}
