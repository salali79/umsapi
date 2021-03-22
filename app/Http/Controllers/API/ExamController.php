<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentFinalTranscript;
use App\Models\StudentSemesterTranscript;
use App\Models\Equivalent;
use App\Models\Student;
use JWTAuth;

class ExamController extends Controller
{
    public function __construct(Request $request)
    {
      $this->middleware('auth:student',);
      $this->guard = "student";
    }
    public function current_student(Request $request)
    {
        if(!is_null($request->lang)) app()->setLocale($request->lang);
        $headers = apache_request_headers();
        $request->headers->set('Authorization', $headers['Authorization']);
        $token = $request->headers->get('Authorization');
        JWTAuth::setToken($token);
        $std = auth('student')->user();
        return $std;
    }
    public function index(Request $request)
    {
        $std = $this->current_student($request);

        $final = [];
        $semester = [];

        $final['agpa'] = $std->finalTranscript->agpa;
        $final['a_completed_hours'] = $std->finalTranscript->a_completed_hours;
        
        $semester = $std->studentSemesterTranscript->map(function ($semesterTranscript) {
            $semester_marks = $semesterTranscript->semesterMarks();

            $courses = $semester_marks->map( function($semester_mark){
                $course_char_points = Equivalent::where('point_equivalent',$semester_mark->points)->first();
                if(!is_null($course_char_points)) $course_char_points = $course_char_points->char_equivalent;
                return [
                    'course_credit_hours' => $semester_mark->courseCreditHours(),
                    'course_names' => $semester_mark->examPlanCourse->course->name,
                    'course_equivalents' => $course_char_points,
                    'course_ids' => $semester_mark->examPlanCourse->course->id
                ];
            });


            return [
                's_completed_hours' => $semesterTranscript->s_completed_hours,
                'a_completed_hours' => $semesterTranscript->a_completed_hours,
                's_registered_hours' => $semesterTranscript->s_registered_hours,
                'a_registered_hours' => $semesterTranscript->a_registered_hours,
                'agpa' => $semesterTranscript->agpa,
                'gpa' => $semesterTranscript->gpa,
                'study_year' => $semesterTranscript->studyYear->name,
                'semester' => $semesterTranscript->semester->id,
                'courses' => $courses
            ];
        });

        return response()->json([
            'status' => 'success',
            'semester_data' => $semester,
            'final_data' => $final
        ]);
    }
}
