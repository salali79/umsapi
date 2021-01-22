<?php

namespace App\Models;



class CourseGroup extends AppModel
{
    protected $fillable =[
        'registration_course_id','name','day','start_time','end_time','place',
        'capacity', 'created_by','updated_by','deleted_by'];

    public function registrationCourse(){
        return $this->belongsTo(RegistrationCourses::class);
    }
}
