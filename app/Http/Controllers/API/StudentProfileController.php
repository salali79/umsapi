<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterCourseRequest as RegisterCourseRequest;
use Response;
use Auth;
use Validator;
use JWTFactory;
use JWTAuth;
use JWTAuthException;
use App\Models\Student;

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
    public function register_course(RegisterCourseRequest $request)
    {
        return $request;
        return response()->json([
            'status' => 'success',
            'message' => 'register successfully',
            'info' => $request,
            'action' => ''
        ]);
    }

    //new_name file delete_file path 
    /*
      [
          'file'        => 'image',
	      'path'        => 'students',
          'delete_file' => Student::find($id)->logo
      ]
    */
    public function upload($data = []) {

		if (in_array('new_name', $data)) {
			$new_name = $data['new_name'] === null? time():$data['new_name'];
		}

		if (request()->hasFile($data['file'])) {
			Storage::has($data['delete_file'])? Storage::delete($data['delete_file']):'';
			return request()->file($data['file'])->store($data['path']);
		}
	}
}
