<?php

namespace App\Models;



class RegistrationCourse extends AppModel
{
    protected $fillable = ['course_id','registration_plan_id', 'created_by','updated_by','deleted_by'];
    protected $appends = ['groups_count','categories_count'];

    public function course(){
        return $this->belongsTo(Course::class);
    }
    public function registrationPlan(){
        return $this->belongsTo(RegistrationPlan::class);
    }
    public function courseGroups(){
        return $this->hasMany(RegistrationCourseGroup::class);
    }
    public function courseCategories(){
        return $this->hasMany(RegistrationCourseCategory::class);
    }

    public function getGroupsCountAttribute(){
        return $this->courseGroups()->count();
    }

    public function getCategoriesCountAttribute(){
        return $this->courseCategories()->count();
    }

//    public function studentRegistration(){
//        $this->hasManyThrough(StudentRegistration::class,CourseGroup::class);
//        $this->hasManyThrough(StudentRegistration::class,CourseCategory::class);
//    }
}
