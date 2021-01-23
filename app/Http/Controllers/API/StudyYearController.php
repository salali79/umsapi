<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudyYear;

class StudyYearController extends Controller
{
    public function index()
    {
        $years = StudyYear::all()->sortByDesc('code');
        return response()->json([
            'status' => 'success',
            'message' => 'return all years successfully',
            'data' => $years,
            'action' => 'index'
        ]);
    }
}
