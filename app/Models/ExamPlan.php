<?php

namespace App\Models;


class ExamPlan extends AppModel
{
    protected $fillable =[
        'study_year_id','semester_id','faculty_id','department_id',
        'created_by','updated_by','deleted_by'] ;

    public function studyYear(){
        return $this->belongsTo(StudyYear::class);
    }
    public function semester(){
        return $this->belongsTo(Semester::class);
    }
    public function faculty(){
        return $this->belongsTo(Faculty::class);
    }
    public function department(){
        return $this->belongsTo(Department::class);
    }
    public function examPlanCourses(){
        return $this->hasMany(ExamPlanCourse::class);
    }
    public function examFinalMark(){
        return $this->hasManyThrough(ExamPlanFinalMark::class,ExamPlanCourse::class);
    }

    public function studyPlan(){
       // return $this->join('study_plans');
    }
}
