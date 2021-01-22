<?php

namespace App\Models;


class StudentModifiedCourse extends AppModel
{
    protected $fillable = ['student_id','course_id','created_by','updated_by','deleted_by' ] ;

    public function student(){
        return $this->belongsTo(Student::class);
    }
    public function course(){
        return $this->belongsTo(Course::class);
    }
}
