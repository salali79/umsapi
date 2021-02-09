<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\OpeningHours\OpeningHours;

class ProgramSchedule extends Model
{
    protected $table =  "program_schedule";
    public function openingHours()
    {
        return OpeningHours::create($this->opening_hours ?: []);
    }
}
