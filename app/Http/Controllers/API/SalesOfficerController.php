<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ShoppingSalesOfficer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\ResetPasswordRequest as ResetPasswordRequest;
use JWTAuth;

class SalesOfficerController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth:sales_officer', ['except' => ['login']]);
      $this->guard = "sales_officer";
    }
    function current_sales_officer(Request $request)
    {
        if(!is_null($request->lang)) app()->setLocale($request->lang);
        $headers = apache_request_headers();
        $request->headers->set('Authorization', $headers['Authorization']);
        $token = $request->headers->get('Authorization');
        JWTAuth::setToken($token);
        $sales_officer = auth('sales_officer')->user();
        return $sales_officer;
    }

    public function login(Request $request)
    {
        try{
            $password = $request->password;
            $username = $request->username;
            $credentials = ['username' => $username , 'password' => $password];
            $sales_officer = ShoppingSalesOfficer::where('username',$username)->firstOrFail();
            {
                if (!$token = auth('sales_officer')->attempt($credentials)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'اسم المستخدم او كلمة المرور غير صحيحة',
                        'action' => 'login',
                        'data' => [],
                         401
                    ]);
                }
                $sales_officer = ShoppingSalesOfficer::where('username',$username)->firstOrFail();
                return $this->respondWithToken($token,$sales_officer);
            }
        } catch (ModelNotFoundException $ex) { 
            return response()->json
            ([
                'status' => 'error',
                'message' => 'اسم المستخدم غير متاح',
                'data' => [],
                'action'=> ''
            ]);
        }
    }
    public function logout()
    {
            auth('sales_officer')->logout();
            return response()->json([
                'status' => 'success',
                'message' => 'تم تسجيل الخروج بنجاح',
                'action' => 'logout',
                'data' => []
            ]);
    }
    protected function respondWithToken($token, $sales_officer)
    {
      return response()->json([
        'status' => 'success',
        'message' => 'token response',
        'id' => $sales_officer->id,
        'sales_officer' => $sales_officer->toArray(),
        'token' => $token,
        'action' => 'response'
      ]);
    }
    public function reset_password(ResetPasswordRequest $request) {
        $validated = $request->validated();
        $password = $request->password;
        $sales_officer = $this->current_sales_officer($request);
        $not_diff = \Hash::check($request->password , $sales_officer->password );
        if($not_diff==1)
        {
            return response()->json([
                'status' => 'error',
                'message' => 'قم بادخال كلمة مرور مختلفة'
            ]);
        }
        else
        {
            $sales_officer = ShoppingSalesOfficer::where('username', auth('sales_officer')->user()->username)->update([
                'password' => bcrypt($request->password),
                'updated_by' => auth('sales_officer')->user()->id
            ]);

            auth('sales_officer')->attempt(['username' => auth('sales_officer')->user()->username, 'password' => $request->password], true);
            return response()->json([
                'status' => 'success',
                'message' => 'تم اعادة تعيين كلمة المرور',
            ]);
        }
}
}
