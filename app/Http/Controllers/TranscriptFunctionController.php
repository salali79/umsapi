<?php

namespace App\Http\Controllers;

use App\Models\ExamPlanFinalMark;
use App\Models\FinalMark;
use App\Models\Equivalent;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudyYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TranscriptFunctionController extends Controller
{
    public function lastStudyYear(){
        return StudyYear::query()->orderBy('end','desc')->first();
    }
    public function getCharEquivalent($points){
          $equivalent = Equivalent::where('point_equivalent','=',$points)->get();
          if($equivalent != null)
              return $equivalent->char_equivalent ;
    }

    public function getStudentID($academic_number){
        $student = Student::where('academic_number','=',$academic_number)->first();
        if($student)
            return $student->id;

    }
    public function getStudentModifiedHours($student_id){
        $student = Student::find($student_id);
        $hours = $student->modifiedCoursesHours();
        return $hours;
    }
      // should correctly pass the right mark array to the GPA
     //  this array is the mark of this semester with semester_id and study_year_id
    //   by using the getSemesterResults()

    public function GPA($marks){

            $mark_point_hours = array_sum( $marks->map(function ($mark) {
                if($mark->mark == 'w' ||$mark->mark == 'W' ) {}
                else return $mark->CoursePointsInHours();  })->toArray());

            $mark_hours =array_sum( $marks->map(function ($mark){
                if($mark->mark == 'w' || $mark->mark == 'W'  ) {}
                else return $mark->courseCreditHours() ;     })->toArray());

            $mark_hours =   $mark_hours == 0 ? 1 : $mark_hours ;
            $GPA = number_format(floatval( $mark_point_hours / $mark_hours), 3, '.', '') ;
            return $GPA  ;
    }
    public function AGPA($marks){
          //pass semester_id and study_year_id of the current semester to =>
         //get previous results , then get the max result of grouped course
        // calculate the AGPA by passing the array to GPA function

          $agpa = $this->GPA($marks);
          return $agpa ;
    }


    public function getPreviousSemesters($semester_id){
        $previous_ids = Semester::query()
            ->where('id','<',$semester_id)->pluck('id');
        return $previous_ids;

    }

    public function getPreviousStudyYears($study_year_id){
        $previous_study_year  =
            StudyYear::query()
                ->where('id','<',$study_year_id)->pluck('id');
        return $previous_study_year;
    }

    public function getSemesterResults($student_id,$study_year_id,$semester_id){

        $semester_marks = ExamPlanFinalMark::where('student_id','=',$student_id)
            ->whereHas(
                'examPlanCourse.examPlan.semester',function($q) use ($semester_id){
                $q->where('id', $semester_id); })
            ->whereHas(
                'examPlanCourse.examPlan.studyYear',function($q) use ($study_year_id){
                $q->where('id', $study_year_id); })
            ->get();
        return $semester_marks ;

    }
    public function getPreviousResults($student_id,$study_year_id ,$semester_id){

        $semester_result = $this->getSemesterResults($student_id,$study_year_id ,$semester_id);
        $prev_semester_ids = $this->getPreviousSemesters($semester_id);
        $prev_study_year_ids = $this->getPreviousStudyYears($study_year_id);
        $previous_s_marks =  ExamPlanFinalMark::query()->Student($student_id)

            ->whereHas('examPlanCourse.examPlan.semester',function($q) use ($prev_semester_ids){
                $q->whereIn('id', $prev_semester_ids); })
            ->with(['examPlanCourse'])
            ->whereHas('examPlanCourse.examPlan.studyYear',function($q) use ($study_year_id){
                $q->where('id',$study_year_id); })
            ->with(['examPlanCourse'])->get();

        $previous_y_marks =  ExamPlanFinalMark::query()->Student($student_id)
            ->whereHas('examPlanCourse.examPlan.studyYear',function($q) use ($prev_study_year_ids){
                $q->whereIn('id', $prev_study_year_ids); })
            ->with(['examPlanCourse'])->get();

        $previous_marks = $semester_result->merge($previous_s_marks)->merge($previous_y_marks);
        return  $previous_marks;

    }

    public function getMaxResultOfPrevious($student_id,$study_year_id ,$semester_id){
       $previous_result = $this->getPreviousResults($student_id,$study_year_id ,$semester_id);
        return $previous_result->groupBy('course_id')->map(function ($group){
                              $group_max_mark = max($group->map(function ($mark){
                                  return (int)$mark->mark;
                              })->toArray());//$group_max_mark = $group->max('mark') ;

                              $max_result = $group->where('mark','=',$group_max_mark)->first();
                return $max_result;
            });
    }

    //we can use this function to calculate credit hours for the semester
    // and for A_credit_hours by passing the previous results grouped and maxed
    public function getRegisteredHours($marks){
        $registered_hours = array_sum( $marks->map(function ($mark){
            if($mark->mark == 'w' ||$mark->mark == 'W' ) {}
            else return $mark->courseCreditHours();
        })->toArray() ) ;
        return $registered_hours ;
    }

    //we can use this function to calculate completed hours for the semester
    // and for A_completed_hours by passing the previous results grouped and maxed
    public function getCompletedHours($marks){

        $completed_hours = array_sum( $marks->map(function ($mark){
            if($mark->pass())
                return $mark->courseCreditHours();
        })->toArray() ) ;
        return $completed_hours ;
    }


    /* these two functions are old and work on final mark table */
    public function semesterGPA ($study_year_id , $semester_id , $student_id ){

    $finalMarkForStudent =
        ExamPlanFinalMark::all()
            ->where('student_id','=',$student_id)
            ->where('study_year_id','=',$study_year_id)
            ->where('semester_id','=',$semester_id);

    $semester_GPA = $this->GPA($finalMarkForStudent) ;
    return $semester_GPA ;
}

    public function getMaxCourseResult($student_id,$study_year_ids ,$semester_ids){
        /* this is an old function accourding to old table in database */

        $marks = ExamPlanFinalMark::query()
            ->where('student_id',$student_id)
            ->whereIn('study_year_id',$study_year_ids)
            ->whereIn('semester_id',$semester_ids)
            ->select(DB::raw('* , max(points) as points '))
            ->groupBy('course_id')->get();
        return $marks;
    }

}
