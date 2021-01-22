<?php

namespace App\Models;



class StudyYearSemester extends AppModel
{
   protected $fillable =['study_year_id','semester_id','beginning','end','created_by','updated_by','deleted_by'];

   public function studyYear(){
       return $this->belongsTo(StudyYear::class);
   }
   public function semesters(){
       return $this->belongsTo(Semester::class);
   }

}
