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

    public function add_course_time(Request $request)
    {
        $program = new ProgramSchedule();
        $program->student_id="3466";
        $free_hours = [
            'sunday' =>
             [$request['MOsun'].'-'.$request['MCsun'], $request['NOsun'].'-'.$request['NCsun']],
            'monday' =>
             [$request['MOmon'].'-'.$request['MCmon'], $request['NOmon'].'-'.$request['NCmon']],
            'tuesday' =>
             [$request['MOtus'].'-'.$request['MCtus'], $request['NOtus'].'-'.$request['NCtus']],
            'wednesday' =>
             [$request['MOwed'].'-'.$request['MCwed'], $request['NOwed'].'-'.$request['NCwed']],
            'thursday' =>
             [$request['MOthu'].'-'.$request['MCthu'], $request['NOthu'].'-'.$request['NCthu']],
            'friday' =>
             [$request['MOfri'].'-'.$request['MCfrin'], $request['NOfri'].'-'.$request['NCfri']],
            'saturday' =>
             [$request['MOsat'].'-'.$request['MCsat'], $request['NOsat'].'-'.$request['NCsatn']],
        ];


        $program->free_hours = json_encode($free_hours);
        /*if(count($opening) > 0){
            $program->update([
                'free_hours' => $opening ? : null,
             ]);
        }*/
        $program->save();
    }
    public function get_course_conflict()
    {
        $program = ProgramSchedule::find(2);
        $year = "2021"; $month="2"; $day="sunday"; $tz="11:00";
        return $program->isOpenAt(Carbon::now);
        //return $program->isOpenAt(new DateTime('2021-26-09 12:00'));
        //isOpenOn('sunday');
    }
}
