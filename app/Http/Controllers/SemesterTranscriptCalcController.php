<?php

namespace App\Http\Controllers;

use App\Models\SemesterTranscript;
use App\Models\Student;
use App\Models\StudentSemesterTranscript;
use Illuminate\Http\Request;

class SemesterTranscriptCalcController extends Controller
{
   public function semesterTranscript($student_id , $study_year_id ,$semester_id){
       $transcript = new TranscriptFunctionController();

       $semester_mark = $transcript->getSemesterResults($student_id,$study_year_id,$semester_id);

       $I = $semester_mark->contains('mark','=','I') || $semester_mark->contains('mark','=','i') ;
       if ($I)
       {
           $save = StudentSemesterTranscript::create([
               'student_id'         => $student_id,
               'study_year_id'      => $study_year_id,
               'semester_id'        => $semester_id,
               's_completed_hours'  => 'I',
               'a_completed_hours'  => 'I',
               's_registered_hours' => 'I',
               'a_registered_hours' => 'I',
               'gpa'                => 'I',
               'agpa'               => 'I'
           ]);
       }
       else{
           $student = Student::find($student_id);

           $previous_max_marks = $transcript->getMaxResultOfPrevious($student_id,$study_year_id,$semester_id);

           $s_completed_hours = $transcript->getCompletedHours($semester_mark);
           $a_completed_hours = $transcript->getCompletedHours($previous_max_marks) + $transcript->getStudentModifiedHours($student_id);

           $s_registered_hours = $transcript->getRegisteredHours($semester_mark);
           $a_registered_hours = $transcript->getRegisteredHours($previous_max_marks);

           $gpa = $transcript->GPA($semester_mark);

           $agpa = $transcript->AGPA($previous_max_marks);

          $oldStudentSemesterTranscript =StudentSemesterTranscript::where('student_id',$student_id)
              ->where('study_year_id',$study_year_id)
              ->where('semester_id',$semester_id)
              ->first();
          if($oldStudentSemesterTranscript){
              $save = $oldStudentSemesterTranscript->update([
                  's_completed_hours' => $s_completed_hours,
                  'a_completed_hours' => $a_completed_hours,
                  's_registered_hours' => $s_registered_hours,
                  'a_registered_hours' => $a_registered_hours,
                  'gpa' => $gpa,
                  'agpa' => $agpa
              ]);
          }else {
              $save = StudentSemesterTranscript::create([
                  'student_id' => $student_id,
                  'study_year_id' => $study_year_id,
                  'semester_id' => $semester_id,
                  's_completed_hours' => $s_completed_hours,
                  'a_completed_hours' => $a_completed_hours,
                  's_registered_hours' => $s_registered_hours,
                  'a_registered_hours' => $a_registered_hours,
                  'gpa' => $gpa,
                  'agpa' => $agpa
              ]);
          }
           $final = $student->studentFinalTranscript()->updateOrCreate(
               [],
               [
                   'agpa' => $agpa,
                   'a_completed_hours' => $a_completed_hours
               ]);


       }




   }
}
