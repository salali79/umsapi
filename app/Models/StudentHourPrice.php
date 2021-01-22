<?php

namespace App\Models;



class StudentHourPrice extends AppModel
{
    protected $fillable =['student_id','hour_price','coin','created_by','updated_by','deleted_by'];

    public function student(){
        return $this->belongsTo(Student::class);
    }
}
