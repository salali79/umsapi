<?php

namespace App\Models;



class RegistrationCourseGroup extends AppModel
{
    protected $fillable =[
        'registration_course_id','name', 'capacity',
        'created_by','updated_by','deleted_by'];

    protected $appends = ['registered_student_count'];

    public function getRegisteredStudentCountAttribute(){

        return $this->hasMany(StudentRegisteredCourse::class)->count();
    }
    public function registrationCourse(){
        return $this->belongsTo(RegistrationCourse::class);
    }
    public function lectures(){
        return $this->hasMany(RegistrationCGL::class);
    }
}
