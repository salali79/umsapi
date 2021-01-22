<?php

namespace App\Models;


class ExamPlanCourse extends AppModel
{
    protected $fillable =['exam_plan_id','course_id','exam_plan_form_id','created_by','updated_by','deleted_by'];

    public function examPlan(){
        return $this->belongsTo(ExamPlan::class);
    }

    public function course(){
        return $this->belongsTo(Course::class);
    }

    public function courseFinalMark(){
        return $this->hasMany(ExamPlanFinalMark::class);
    }

}
