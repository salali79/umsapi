<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\OpeningHours\OpeningHours;

class ProgramSchedule extends Model
{
    protected $table =  "program_schedule";
        
    
    protected $fillable = [
        'id',
        'student_id',
        'free_hours'
    ];


    protected $casts = [
        'free_hours' => 'array'
    ];
    //////accessor//////
    public function openingHours()
    {
        return OpeningHours::create($this->free_hours ?: []);
    }

}
