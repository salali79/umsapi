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
use Session;
use DB;
use Mail;

class StudentController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth:student', ['except' => ['login','getToken','reset_password', 'deposite', 'student_deposite']]);
      $this->guard = "student";
    }
    public function login(Request $request)
    {
        try{
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
        } catch (ModelNotFoundException $ex) { // User not found
            return response()->json
            ([
                'status' => 'error',
                'message' => 'username notfound',
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
        Session::flush();
        return response()->json([
            'status'=> 'success',
            'message'=>'successfully logged out',
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
                 'financeDetails', 'hourPrice', 'studentRegistration',
                 'financialBalance', 'modifiedCourses'
                );
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
                    'password' => bcrypt($request->password)
            ]);
            auth('student')->attempt(['username' => auth('student')->user()->username, 'password' => $request->password], true);
            return response()->json([
                'status' => 'success',
                'message' => 'reset password successfully',
                'data' => [],
                'action' => 'reset password'
            ]);
	}
    ///////////////////////////////////////////////////////////////////////////////

}
