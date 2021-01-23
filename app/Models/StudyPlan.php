<?php

namespace App\Models;


class StudyPlan extends AppModel
{

    protected $fillable = ['version_date','study_year_id','uoc_min_count','foc_min_count','faculty_id','department_id','status', 'created_by','updated_by','deleted_by'];


    public function faculty(){
        return $this->belongsTo(Faculty::class);
    }

    public function department(){
        return $this->belongsTo(Department::class);
    }

    public function study_year(){
        return $this->belongsTo(StudyYear::class);
    }

    public function details(){
        return $this->hasMany(StudyPlanDetail::class);
    }

    public function courseDetails($course_id){
        return $this->details()->where('course_id','=', $course_id)->first();
    }

    public function courses(){
        $course_ids =  $this->details()->pluck('course_id');
        $courses = Course::find($course_ids);
        return $courses;
    }


}
