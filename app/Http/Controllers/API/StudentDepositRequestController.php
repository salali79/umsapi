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
    public function index(Request $request)
    {
        $headers = apache_request_headers();
        $request->headers->set('Authorization', $headers['Authorization']);
        $token = $request->headers->get('Authorization');
        JWTAuth::setToken($token);
        $std = auth('student')->user();
        return response()->json([
            'deposites'=> $std->deposites->toArray()
        ]);
    }
    public function store(DepositeRequest $request)
    {
        $validated = $request->validated();
        $token = $request->stud_token;
        $data = [
            'token' => $request->stud_token,
            'bank_id' => $request->bank_id,
            'office_id' => $request->office_id,
            'study_year_id' => $request->study_year_id,
            'semester_id' => $request->semester_id,
            'requested_hours' => $request->requested_hours,
            'student_id' => $request->student_id,
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
