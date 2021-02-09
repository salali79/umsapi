<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FolderType;
use Response;
use Auth;
use Validator;
use JWTFactory;
use JWTAuth;
use JWTAuthException;
use App\Models\Student;

class FolderController extends Controller
{

    public function index(Request $request)
    {
        if(!is_null($request->lang)) app()->setLocale($request->lang);
        $res = FolderType::all();
        return response()->json([
            'status' => 'success',
            'message' => 'personal info',
            'data' => $res,
            'action' => ''
        ]);
    }
    public function files(Request $request)
    {
        if(!is_null($request->lang)) app()->setLocale($request->lang);
        $folder = FolderType::find($request->folder_type_id)->firstOrFail();
        $res = $folder->papers;
        return response()->json([
            'status' => 'success',
            'message' => 'personal info',
            'data' => $res,
            'action' => ''
        ]);
    }
    public function exist_files(Request $request)
    {
        $std = current_student($request);
        if(!is_null($std))
        return response()->json([
            'status' => 'success',
            'message' => 'personal info',
            'data' => $std->studentFiles,
            'action' => ''
        ]);
    }
}
