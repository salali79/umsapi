<?php

namespace App\Models;

class StudentOpenedCourse extends AppModel
{
     protected $fillable =['student_id','course_id','course_status','created_by','updated_by','deleted_by'];

     public function student(){
         return $this->belongsTo(Student::class);
     }
     public function course(){
         return $this->belongsTo(Course::class);
     }
}
