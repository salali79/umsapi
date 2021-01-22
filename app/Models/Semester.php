<?php

namespace App\Models;



use Astrotomic\Translatable\Translatable;

class Semester extends AppModel
{
    use Translatable;

    protected $with = ['translations'];

    protected $translatedAttributes = ['name'];

    protected $fillable =['created_by','updated_by','deleted_by'];

    public function studyYears(){
        return $this->belongsToMany(StudyYear::class,'study_year_semesters');
    }

    public function semesterCoursesHours(){

    }
    /* public function examPlans(){
        return $this->hasMany(ExamPlan::class);
    }*/
    /*public function registrationPlans(){
        return $this->hasMany(RegistrationPlan::class);
    }*/
}
