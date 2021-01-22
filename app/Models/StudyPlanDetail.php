<?php

namespace App\Models;

class StudyPlanDetail extends AppModel
{
    protected $fillable = [
        'course_id', 'is_uc', 'is_fc', 'is_sc', 'mandatory', 'prerequisite_ids', 'prerequisite_hours', 'credit_hours', 'theo_hours', 'practice_hours', 'level_id', 'created_by','updated_by','deleted_by'];

    protected $appends = ['prerequisite_courses'];

    public function course(){
        return $this->belongsTo(Course::class);
    }

    public function level(){
        return $this->belongsTo(StudyPlanCourseLevel::class);
    }

    public function getPrerequisiteCoursesAttribute(){
        return Course::whereIn('id',explode(',',$this->prerequisite_ids))->select('id', 'code')->get();
    }
}
