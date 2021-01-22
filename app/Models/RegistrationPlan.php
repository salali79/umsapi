<?php

namespace App\Models;



class RegistrationPlan extends AppModel
{
    protected $fillable =[
        'study_year_semester_id','faculty_id',
        'department_id', 'created_by','updated_by','deleted_by'] ;

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
    public function studyPlan(){
        return $this->studyYear->studyPlan;
    }
    public function studyPlanCourses($study_year_id,$faculty_id ,$department_id = null){
       $study_year = StudyYear::find($study_year_id);

       $fac_study_plan = $study_year->studyPlans->where('faculty_id','=',$faculty_id)->first;

       if($department_id != null)
       {
           $dep_study_plan = $study_year->studyPlans->where('faculty_id','=',$faculty_id)
               ->where('department_id','=',$department_id)->first;

           return $dep_study_plan->courses();
       }
        else return $fac_study_plan->courses();
    }
}
