<?php

namespace App\Models;



class RegistrationPlan extends AppModel
{
    protected $fillable =[
        'study_year_semester_id','study_plan_id','faculty_id',
        'department_id', 'created_by','updated_by','deleted_by','status'] ;

    public function studyYearSemester(){
        return $this->belongsTo(StudyYearSemester::class);
    }
    public function faculty(){
        return $this->belongsTo(Faculty::class);
    }
    public function department(){
        return $this->belongsTo(Department::class);
    }
    public function registrationCourses(){
        return $this->hasMany(RegistrationCourse::class);
    }

    /*public function studyPlan(){
        return $this->studyYear->studyPlan;
    }*/

    public function studyPlan(){
        return $this->belongsTo(StudyPlan::class);
    }
}
