<?php

namespace App\Models;



class AcademicSupervision extends AppModel
{
  protected $fillable = [
      'study_year_id','semester_id','student_id','academic_status_id','notes',
      'created_by','updated_by','deleted_by'
  ];

   public function student(){
          return $this->belongsTo(Student::class);
    }
    public function semester(){
        return $this->belongsTo(Semester::class);
    }
    public function studyYear(){
        return $this->belongsTo(StudyYear::class);
    }
    public function academicStatus(){
        return $this->belongsTo(AcademicStatus::class);
    }
}
