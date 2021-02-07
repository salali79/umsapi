<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Courseinfo extends Model
{

    protected $table ='courseinfo';

    protected $appends = ['faculty_id','department_id'];

    public function getFacultyIdAttribute()
    {
        $fauclty = $this->faculty;

        if($fauclty==10){
            $fauclty_id = 1;
        }elseif($fauclty==20 || $fauclty==21 || $fauclty==22){
            $fauclty_id = 2;
        }elseif($fauclty==30){
            $fauclty_id = 3;
        }elseif($fauclty==40){
            $fauclty_id = 4;
        }elseif($fauclty==51 || $fauclty==52 || $fauclty==53){
            $fauclty_id = 5;
        }elseif($fauclty==60){
            $fauclty_id = 7;
        }elseif($fauclty==70){
            $fauclty_id = 8;
        }else{
            $fauclty_id = null;
        }

        return $fauclty_id  ;
    }

    public function getDepartmentIdAttribute()
    {
        $fauclty = $this->faculty;

       if($fauclty==21){
            $department_id = 3;
        }elseif($fauclty==22){
            $department_id = 4;
        }elseif($fauclty==51){
            $department_id = 1;
        }elseif($fauclty==52){
            $department_id = 2;
        }elseif($fauclty==53){
            $department_id = 5;
        }else{
           $department_id = null;
       }

        return $department_id  ;
    }
}
