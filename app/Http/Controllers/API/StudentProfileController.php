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
    public function info(Request $request)
    {
        $std = $this->current_student($request);
        $std->load('faculty', 'department', 'contact',
        'emergency', 'medicals', 'folderType',
        'studentFiles'
       );
        return $std;
    }
    ///////////////////////////////////////////////////////////////////////////////////////
    public function wallet_info(Request $request)
    {
        $std = $this->current_student($request);
        
        return response()->json([
            'status' => 'success',
            'money' => $std->walletable->total_money,
            'orders' => $std->walletable->orders,
            'charges' => $std->walletable->charges
        ]);
    }


}
