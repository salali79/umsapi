<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\OpeningHours\OpeningHours;
use App\Models\ProgramSchedule;

class ProgramController extends Controller
{

    public function add_course_time(Request $request)
    {
        $program = new ProgramSchedule();
        $program->student_id="3472";
        $program->free_hours = [
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
        /*if(count($opening) > 0){
            $program->update([
                'free_hours' => $opening ? : null,
             ]); 
        }*/
        $program->save();
    }
}
