<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\OpeningHours\OpeningHours;
use App\Models\ProgramSchedule;
use App\Models\Student;
use Auth;
use Validator;
use JWTFactory;
use JWTAuth;
use JWTAuthException;
use Carbon\Carbon;

class ProgramController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth:student');
      $this->guard = "student";
    }
    public function test_schedule_conflict($id)
    {
        try{
            $program = ProgramSchedule::find($id);
            $obj = $program->openingHours();
            return $program->id;
            //free_hours['sunday'];
        } catch(\Spatie\OpeningHours\Exceptions\OverlappingTimeRanges $ex){
            return "conflict";
        }
    }
    public function add_course_time($request = [], $std=null)
    {
        //$std = Student::find(3466);
        $program = ProgramSchedule::where('student_id', $std->id)->first();
        if(is_null($program))
        {
            $program = new ProgramSchedule();
            $program->student_id = $std->id; //3466

            $tmp_hours = $program->free_hours;
            $id = $program->id;
            $program->free_hours =[
                $request['day'] => [$request['start'].'-'.$request['end']]
            ];
            $program->save();
            $id = $program->id;
            $conflict = $this->test_schedule_conflict($id);
            if($conflict == "conflict") 
            {
                $program->update([
                    'free_hours' => $tmp_hours ?: null,
                ]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'time conflict'
                ]);
            }
            return response()->json([
                'status' => 'success',
                'message' => 'time doesnot conflict'
            ]);
            //return $program->free_hours;
        }


        $hours = [
            $request['day'] => [$request['start'].'-'.$request['end']]
        ];

        $all_hours = array_merge($program->free_hours,$hours);
        $tmp_hours = $program->free_hours;
        $id = $program->id;
        $program->update([
            'free_hours' => $all_hours ?: null,
        ]);
        $conflict = $this->test_schedule_conflict($id);
        if($conflict == "conflict") 
        {
            $program->update([
                'free_hours' => $tmp_hours ?: null,
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'time conflict'
            ]);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'time doesnot conflict'
        ]);
        //return $program->free_hours;
    }
}


/*
        return $d->OpeningHoursForDay;
        return $d->nextOpen('sunday');
        return $d->forDay('sunday');
        return $d->exceptions();
        $t = $d->isOpenOn('sunday');
        $t = $d->isOpenAt(new \DateTime('2021-12-02 10:00'));
        return $t;
        dd($t);
*/