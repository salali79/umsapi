<?php

namespace App\Models;



class RegistrationPlan extends AppModel
{
    protected $fillable =[
        'study_year_semester_id','study_plan_id','faculty_id',
        'department_id', 'created_by','updated_by','deleted_by','status'] ;

    protected $appends = ['study_year'];
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
    public function getStudyYearAttribute(){
        return $this->studyYearSemester->studyYear;
    }
    public function studyPlan(){
        if(! $this->department_id == null)
        {
            $study_plan = $this->study_year->studyPlans
            ->where('faculty_id','=',$this->faculty_id)
            ->where('department_id', '=', $this->department_id);
        }
        else 
        {
            $study_plan = $this->study_year->studyPlans
            ->where('faculty_id','=',$this->faculty_id);
        }
        return $study_plan;
    }
    public function studyPlanCourses($study_year_id,$faculty_id ,$department_id = null){
        $study_year = StudyYear::find($study_year_id);

        $fac_study_plan = $study_year->studyPlans->where('faculty_id','=',$faculty_id)->first;

        if($department_id != null)
        {
            $dep_study_plan = $study_year->studyPlans->where('faculty_id','=',$faculty_id)
                ->where('department_id','=',$department_id)->first;

            return $dep_study_plan->details;
        }
        else return $fac_study_plan->details;
    }
}
