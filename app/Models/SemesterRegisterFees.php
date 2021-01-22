<?php

namespace App\Models;



class SemesterRegisterFees extends AppModel
{
   protected $fillable =['study_year_semester_id','value','coin','created_by','updated_by','deleted_by' ] ;

   public function studyYearSemester(){
       return $this->belongsTo(StudyYearSemester::class);
   }

}
