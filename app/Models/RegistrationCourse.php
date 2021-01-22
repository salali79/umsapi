<?php

namespace App\Models;



class RegistrationCourse extends AppModel
{
    protected $fillable = ['course_id','registration_plan_id', 'created_by','updated_by','deleted_by'];

    public function course(){
        return $this->belongsTo(Course::class);
    }
    public function registrationPlan(){
        return $this->belongsTo(RegistrationPlan::class);
    }
    public function courseGroups(){
        return $this->hasMany(CourseGroup::class);
    }
    public function courseCategories(){
        return $this->hasMany(CourseCategory::class);
    }


//    public function studentRegistration(){
//        $this->hasManyThrough(StudentRegistration::class,CourseGroup::class);
//        $this->hasManyThrough(StudentRegistration::class,CourseCategory::class);
//    }
}
