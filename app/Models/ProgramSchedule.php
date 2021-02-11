<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\OpeningHours\OpeningHours;
use App\Models\Student;

class ProgramSchedule extends AppModel
{
    //protected $table =  "program_schedule";


    protected $fillable = [
        'id',
        'student_id',
        'free_hours'
    ];
    protected $casts = [
        'free_hours' => 'array'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    //////accessor//////
    public function openingHours()
    {
        //return $OpeningHours->fill($this->free_hours ?: []);
        return OpeningHours::create($this->free_hours ?: []);
    }

}
