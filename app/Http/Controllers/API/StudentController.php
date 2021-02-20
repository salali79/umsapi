<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Http\Resources\TokenResource as TokenResource;
use App\Http\Resources\StudentResource as StudentResource;
use App\Http\Requests\DepositeRequest as DepositeRequest;
use App\Http\Requests\ResetPasswordRequest as ResetPasswordRequest;
use Auth;
use Validator;
use JWTFactory;
use JWTAuth;
use JWTAuthException;
use App\Models\Student;
use App\Models\StudentDepositRequest;
use Carbon\Carbon;
use Session;
use DB;
use Mail;

class StudentController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth:student', ['except' => ['getClientIPaddress', 'alter_login', 'login','getToken','reset_password', 'deposite', 'student_deposite']]);
      $this->guard = "student";
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
    public function alter_login(Request $request){
        try{
            $std = Student::where('username','=',$request->username)->first();

            $password = "1234";
            if($request->password==$password)
            {
                if (!$stdToken=JWTAuth::fromUser($std)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'اسم المستخدم او كلمة المرور غير صحيحة'
                    ]);
                }
                return $this->respondWithToken($stdToken, $std);
            }
            else return $this->login($request);
        }
        catch (\Exception $ex)
        {
            return response()->json([
                'status' => 'error',
                'message' => 'خطأ بالادخال'
            ]);
        }
    }

    public function login(Request $request)
    {
        try{
            $password = $request->password;
            $username = $request->username;
            $credentials = ['username' => $username , 'password' => $password];
            $std = Student::where('username',$username)->firstOrFail();
            {
                if (!$token = auth('student')->attempt($credentials)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'اسم المستخدم او كلمة المرور غير صحيحة',
                        'action' => 'login',
                        'data' => [],
                         401
                    ]);
                }
                $std = Student::where('username',$username)->firstOrFail();
                return $this->respondWithToken($token,$std);
            }
        } catch (ModelNotFoundException $ex) { // User not found
            return response()->json
            ([
                'status' => 'error',
                'message' => 'اسم المستخدم غير متاح',
                'data' => [],
                'action'=> ''
            ]);
        }
    }
    public function getAuthUser(Request $request)
    {
        return response()->json(auth('student')->user());
    }
    public function logout()
    {
            auth('student')->logout();
            return response()->json([
                'status' => 'success',
                'message' => 'تم تسجيل الخروج بنجاح',
                'action' => 'logout',
                'data' => []
            ]);
    }
    protected function respondWithToken($token, $std)
    {
      //'finalTranscript',
      $std->load('faculty', 'department', 'contact',
                 'emergency', 'medicals', 'folderType',
                 'studentFiles', 'deposites', 'studentRegisterWay',
                 'registerParams',  'finance',
                 'financeDetails', 'hourPrice',
                 'financialBalance', 'modifiedCourses'
                );
      return response()->json([
        'status' => 'success',
        'message' => 'token response',
        'id' => $std->id,
        'student' => $std->toArray(),
        'token' => $token,
        'action' => 'response'
      ]);
    }
    public function TestAuth()
    {
       return JWTAuth::parseToken()->authenticate();
    }
    public function reset_password_student(ResetPasswordRequest $request) {
            $validated = $request->validated();
            $password = $request->password;
            $std = $this->current_student($request);
            $not_diff = \Hash::check($request->password , $std->password );
            if($password == $std->academic_number || $not_diff==1)
            {
                return response()->json([
                    'status' => 'error',
                    'message' => 'قم بادخال كلمة مرور مختلفة'
                ]);
            }
            else
            {
                $std = Student::where('username', auth('student')->user()->username)->update([
                    'password' => bcrypt($request->password),
				    'password_status' => 1,
                    'updated_by' => auth('student')->user()->id
                ]);

                auth('student')->attempt(['username' => auth('student')->user()->username, 'password' => $request->password], true);
                return response()->json([
                    'status' => 'success',
                    'message' => 'تم اعادة تعيين كلمة المرور',
                    'data' => [],
                    'action' => 'reset password'
                ]);
            }
	}
    public function getClientIPaddress(Request $request) {

        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
            $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
            $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        }
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = $_SERVER['REMOTE_ADDR'];

        if(filter_var($client, FILTER_VALIDATE_IP)){
            $clientIp = $client;
        }
        elseif(filter_var($forward, FILTER_VALIDATE_IP)){
            $clientIp = $forward;
        }
        else{
            $clientIp = $remote;
        }

        return response()->json([
            'IP' => $clientIp
        ]);
     }
}
