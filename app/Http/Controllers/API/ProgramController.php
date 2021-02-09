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

class ProgramController extends Controller
{

    function current_student(Request $request)
    {
        if(!is_null($request->lang)) app()->setLocale($request->lang);
        $headers = apache_request_headers();
        $request->headers->set('Authorization', $headers['Authorization']);
        $token = $request->headers->get('Authorization');
        JWTAuth::setToken($token);
        $std = auth('student')->user();
        return $std;
    }
    public function add_course_time(Request $request)
    {
        $program = new ProgramSchedule();
        $program->student_id="3472";
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
        $program = ProgramSchedule::find(1);
        $year = "2021"; $month="2"; $day="sunday"; $tz="11:00";
        return $program->isOpenAt(\Carbon::createFromDate($year, $month, $day, $tz));
        //return $program->isOpenAt(new DateTime('2021-26-09 12:00'));
        //isOpenOn('sunday');
    }
}
