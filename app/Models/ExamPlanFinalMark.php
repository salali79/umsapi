<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamPlanFinalMark extends AppModel
{
    protected $fillable = ['student_id', 'exam_plan_course_id', 'mark', 'points', 'created_by', 'updated_by', 'deleted_by'];

    protected $appends = ['course_id'];

    public function getCourseIdAttribute(){
        return $this->examPlanCourse->course_id;
    }
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function examPlanCourse()
    {
        return $this->belongsTo(ExamPlanCourse::class);
    }

    public function studyYear()
    {
        return $this->examPlanCourse->examPlan->studyYear;
    }
    public function semester(){
        return $this->examPlanCourse->examPlan->semester;
    }
    public function scopeStudent($query,$student_id){
            return $query->where('student_id','=',$student_id);
    }
    public function coursePoints(){
//        return 2;
        return floatval($this->points) ;
    }
    public function courseCreditHours(){
        $stud_study_plan = $this->student->StudentStudyPlan();
        $course = $stud_study_plan->courseDetails($this->course_id);
        $hours =  $course == null ? '1' : $course->credit_hours ;

        return $hours ;
    }
    public function CoursePointsInHours()
    {
        return  floatval($this->coursePoints() * $this->courseCreditHours() ) ;
    }
    public function pass() {  return intval($this->mark) >= 50 ? true : false ;   }

}

