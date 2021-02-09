<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentDepositRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Http\Resources\TokenResource as TokenResource;
use App\Http\Resources\StudentResource as StudentResource;
use App\Http\Requests\DepositeRequest as DepositeRequest;
use Auth;
use Validator;
use JWTFactory;
use JWTAuth;
use JWTAuthException;
use App\Models\Student;
use Carbon\Carbon;

class StudentDepositRequestController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth:student', ['except' => ['index']]);
      $this->guard = "student";
    }
    public function index(Request $request)
    {
        $headers = apache_request_headers();
        $request->headers->set('Authorization', $headers['Authorization']);
        $token = $request->headers->get('Authorization');
        JWTAuth::setToken($token);
        $std = auth('student')->user();
        return response()->json([
            'deposites'=> $std->deposites
        ]);
    }
    public function store(DepositeRequest $request)
    {
        //$token = $request->token;
        $data = [
            //'token' => $request->token,
            'bank_id' => $request->bank_id,
            'office_id' => $request->office_id,
            'study_year_id' => '',
            'semester_id' => '',
            'requested_hours' => $request->requested_hours,
            'student_id' => current_student()->id,
            'request_status' => '0'
        ];
        /*if(! $token = JWTAuth::parseToken()->authenticate())
        {
            return response()->json
            ([
                'status' => 'error',
                'message' => 'not authorized',
                'data' => [],
                'action'=> ''
             ]);
        } else {*/
            $std_deposite = StudentDepositRequest::create($data);
            return response()->json
           ([
               'status' => 'success',
               'message' => 'deposite request',
               'data' => [],
               'action'=> 'store'
            ]);
        //}
    }
}
