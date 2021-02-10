<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\OpeningHours\OpeningHours;
use App\Models\ProgramSchedule;
use Auth;
use Validator;
use JWTFactory;
use JWTAuth;
use JWTAuthException;
use Carbon\Carbon;

class ProgramController extends Controller
{

    public function test_schedule_conflict($id)
    {
        try{
            $program = ProgramSchedule::find($id);
            dd($program);
            $obj = $program->openingHours();
            return var_dump( $program->free_hours['sunday']);
        } catch(\Spatie\OpeningHours\Exceptions\OverlappingTimeRanges $ex){
            return "conflict";
        }
    }
    public function add_course_time(Request $request)
    {
        $std = current_student($request);
        $program = ProgramSchedule::where('student_id', $std->id)->first();
        if(is_null($program))
        {
            $program = new ProgramSchedule();
            $program->student_id = $std->id; //3466

            //dd($program);
            $program_tmp = $program;
            $id = $program->id;
            $program->free_hours =[
                //$request['day'] => [$request['start'].'-'.$request['end']],
                'sunday' =>
                 [$request['MOsun'].'-'.$request['MCsun']],
                 'monday' =>
                 [$request['MOmon'].'-'.$request['MCmon']],
            ];
            $program_tmp = $program;
            $id = $program->id;
            dd($program->id);
            $program->save();
            $conflict = $this->test_schedule_conflict($id);
            if($conflict == "conflict") 
            {
                $program->update([
                    'free_hours' => $program_tmp->free_hours ?: null,
                ]);
            }
            return $program->free_hours;
        }


        $hours = [
            //$request['day'] => [$request['start'].'-'.$request['end']],
            'sunday' =>
            [$request['MOsun'].'-'.$request['MCsun'], $request['MOsun'].'-'.$request['MCsun']],
            'friday' => [$request['MOfri'].'-'.$request['MCfri']],
            ];

        $all_hours = array_merge($program->free_hours,$hours);
        $program_tmp = $program;
        $id = $program->id;
        $program->update([
            'free_hours' => $all_hours ?: null,
        ]);
        $conflict = $this->test_schedule_conflict($id);
        if($conflict == "conflict") 
        {
            $program->update([
                'free_hours' => $program_tmp->free_hours ?: null,
            ]);
        }
        return $program->free_hours;
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