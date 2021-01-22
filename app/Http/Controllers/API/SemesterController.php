<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Semester;
use App\Models\StudyYear;

class SemesterController extends Controller
{
    public function semesterStudyYear(Request $request)
    {
        $study_year_id = $request->study_year_id;
        $study_year = StudyYear::where('id',$study_year_id)->first();
        $res = $study_year->semesters;
        return $res;
    }
}
