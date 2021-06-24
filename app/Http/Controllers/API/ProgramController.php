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
    public function object_merge($obj1 = [], $obj2 = [])
    {
        $obj = [
        ];

        $ds1 = array();
        foreach($obj1 as $key=>$value)
        {
            array_push($ds1, $key);
        }
        $ds2 = array();
        foreach($obj2 as $key=>$value)
        {
            array_push($ds2, $key);
        }

        foreach($ds1 as $ds)
        {
            $obj[$ds] = $obj1[$ds];
        }
        foreach($ds2 as $ds)
        {
            //$obj[$ds] ? dd($obj[$ds]):dd('hi') ;
            $obj[$ds] ? array_merge($obj[$ds],$obj2[$ds]):$obj2[$ds];
            dd($obj[$ds]);
            dd(array_merge($obj[$ds],$obj2[$ds]));
        }

        return $obj;
    }
    public function test_schedule_conflict($id)
    {
        try{
            $program = ProgramSchedule::find($id);
            $obj = $program->openingHours();
            //dd($program->free_hours);
            return $program->id;
        } catch(\Spatie\OpeningHours\Exceptions\OverlappingTimeRanges $ex){
            return "conflict";
        }
    }
    public function add_course_time($request = [], $std=null)
    {
        $program = ProgramSchedule::where('student_id', $std->id)->first();
        $conflict = "";
        if(is_null($program))
        {
            $program = new ProgramSchedule();
            $program->student_id = $std->id; //3466

            $tmp_hours = $program->free_hours;
            $id = $program->id;
            $program->free_hours =[
                $request['day'] => [$request['start'].'-'.$request['end']]
            ];
            $program->created_by = $std->id;
            $program->save();
            $id = $program->id;
            //$this->test_schedule_conflict($id);
            if($conflict == "conflict")
            {
                $program->update([
                    'free_hours' => $tmp_hours ?: null,
                    'updated_by' => $std->id
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


        $day = $request['day'];
        $t = 0;
        foreach($program->free_hours as $key=>$value)
        {
            if($key == $day) $t = 1;
        }

        $all_hours = $t ? array_merge($program->free_hours[$request['day']] ? :[], $hours[$request['day']]) : $hours[$request['day']];

        $tmp_hours = $program->free_hours;
        $id = $program->id;
        $edited_hours = $tmp_hours;
        $edited_hours[$request['day']] = $all_hours;
        //dd($edited_hours);

        $program->update([
            'free_hours' => $edited_hours,
            'updated_by' => $std->id
            //[$request['day'] => $all_hours]
        ]);

        //dd($program->free_hours);
        $conflict = $this->test_schedule_conflict($id);
        if($conflict == "conflict")
        {
            //dd('conflict');
            $program->update([
                'free_hours' => $tmp_hours ?: null,
                'updated_by' => $std->id
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


