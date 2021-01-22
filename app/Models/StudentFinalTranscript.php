<?php

namespace App\Models;



class StudentFinalTranscript extends AppModel
{
  protected $fillable =[
      'student_id','student_current_state',
      'agpa','a_completed_hours'
      ,'created_by','updated_by','deleted_by'];

  public function student(){
      return $this->belongsTo(Student::class);
  }

}
