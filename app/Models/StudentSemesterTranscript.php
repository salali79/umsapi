<?php

namespace App\Models;



class StudentSemesterTranscript extends AppModel
{
    protected $fillable = [
        'student_id','study_year_id','semester_id',
        's_completed_hours','a_completed_hours',
        's_registered_hours', 'a_registered_hours','gpa','agpa',
        'created_by','updated_by','deleted_by'];

    public function student(){
        return $this->belongsTo(Student::class);
    }
    public function semester(){
        return $this->belongsTo(Semester::class);
    }
    public function studyYear(){
        return $this->belongsTo(StudyYear::class);
    }
    public function semesterMarks(){
        $semester_id=$this->semester_id;
        $study_year_id=$this->study_year_id;
        $marks = ExamPlanFinalMark::query()
          ->where('student_id',$this->student_id)
          ->whereHas(
              'examPlanCourse.examPlan.semester',function($q) use ($semester_id){
              $q->where('id', $semester_id); })
          ->whereHas(
              'examPlanCourse.examPlan.studyYear',function($q) use ($study_year_id){
              $q->where('id', $study_year_id); })
          ->get();

         /* ->where('study_year_id',$this->study_year_id)
          ->where('semester_id',$this->semester_id)
          ->where('student_id',$this->student_id)->get();*/
//      $courses =  $marks->map(function ($mark){
//          return $mark->course;

//      }) ;
      return $marks ;
    }
    public function scopeStudent($query,$student_id){
        return $query->where('student_id','=',$student_id);
    }
    public function scopeLastStudyYear($query){
        $last_study_year = StudyYear::query()->orderBy('end','desc')->first();
        return $query->where('study_year_id','=',$last_study_year->id);
    }
    public function scopeSemester($query){
        $last_semester = Semester::query()->orderBy('end','desc')->first();
        return $query->where('semester_id','=',$last_semester->id);
    }
}
