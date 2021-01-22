<?php

namespace App\Models;


class StudentRegistration extends AppModel
{
 protected $fillable = ['student_id','course_group_id','course_category_id','course_state'];

 public function  student(){
     return $this->belongsTo(Student::class);
 }
 public function courseGroup(){
     return $this->belongsTo(CourseGroup::class);
 }
 public function courseCategory(){
        return $this->belongsTo(CourseCategory::class);
 }
}
