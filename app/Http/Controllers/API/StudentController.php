<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Http\Resources\TokenResource as TokenResource;
use App\Http\Resources\StudentResource as StudentResource;
use App\Http\Requests\DepositeRequest as DepositeRequest;
use App\Http\Requests\ResetPasswordRequest as ResetPasswordRequest;
use App\Mail\ResetPassword;
use Auth;
use Validator;
use JWTFactory;
use JWTAuth;
use JWTAuthException;
use App\Models\Student;
use App\Models\StudentDepositRequest;
use Carbon\Carbon;
use DB;
use Mail;

class StudentController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth:student', ['except' => ['login','getToken','reset_password', 'deposite']]);
      $this->guard = "student";
    }
    public function login(Request $request)
    {
        $password = $request->password;
        $username = $request->username;
        $credentials = ['username' => $username , 'password' => $password];
        $std = Student::where('username',$username)->firstOrFail();
        //if($std->password_status=='1')
        {
            if (!$token = auth('student')->attempt($credentials)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'wrong password or username',
                    'action' => 'login',
                    'data' => [],
                     401
                ]);
            }
            $std = Student::where('username',$username)->firstOrFail();
            return $this->respondWithToken($token,$std);
        }
        /*else {
            return response()->json([
                'status' => 'success',
                'message' => 'reset password',
                'action' => 'reset password',
                'data' => [],
            ]);
        }*/
    }

    public function getAuthUser(Request $request)
    {
        return response()->json(auth('student')->user());
    }

    public function logout()
    {
        auth('student')->logout();
        return response()->json([
            'status'=> 'success',
            'message'=>'successfully logged out',
            'action' => 'logout',
            'data' => []
        ]);
    }

    protected function respondWithToken($token, $std)
    {
      return response()->json([
        'status' => 'success',
        'message' => 'token response',
        'id' => $std->id,
        'response' => [
            'student' => $std->toArray(),
            'token' => $token
        ],
        'action' => 'response'
      ]);
    }

    public function TestAuth()
    {
       return JWTAuth::parseToken()->authenticate();
    }

    public function reset_password_student(ResetPasswordRequest $request) {

		$validated = $request->validated();
		$std = Student::where('username', auth('student')->user()->username)->update([
				'password' => bcrypt($validated->password)
		]);
		auth('student')->attempt(['username' => auth('student')->user()->username, 'password' => $validated->password], true);
        return response()->json([
            'status' => 'success',
            'message' => 'reset password successfully',
            'data' => [],
            'action' => 'reset password'
        ]);
	}
    ///////////////////////////////////////////////////////////////////////////////
    public function deposite(DepositeRequest $request)
    {
        $validated = $request->validated();
        $token = $request->stud_token;
        $data = [
            'token' => $request->stud_token,
            'bank_id' => $request->bank_id,
            'study_year_id' => $request->study_year_id,
            'semester_id' => $request->semester_id,
            'requested_hours' => $request->requested_hours,
            'student_id' => $request->student_id,
            'request_status' => '0'
        ];
        if(! $token = JWTAuth::parseToken()->authenticate())
        {
            return response()->json
            ([
                'status' => 'error',
                'message' => 'not authorized',
                'data' => [],
                'action'=> ''
             ]);
        } else {
            $std_deposite = StudentDepositRequest::create($data);
            return response()->json
           ([
               'status' => 'success',
               'message' => 'deposite request',
               'data' => [],
               'action'=> 'store'
            ]);
        }
    }
    public function student_deposite(Request $request)
    {
        $headers = apache_request_headers();
        $request->headers->set('Authorization', $headers['Authorization']);
        $token = $request->headers->get('Authorization');
        JWTAuth::setToken($token);
        $std = auth('student')->user();
        return response()->json([
            'deposites'=> $std
        ]);
    }
}
