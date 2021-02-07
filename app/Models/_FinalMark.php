<?php

namespace App\Models;



class FinalMark extends AppModel
{
    protected $fillable = [
        'study_year_id','semester_id','course_id',
        'student_id','faculty_id','department_id',
        'degree','points', 'created_by','updated_by'
        ,'deleted_by'] ;

    public function CoursePointHourAttribute()
    {
        return  ($this->coursePoints() * $this->courseCreditHours() ) ;
    }
    public function coursePoints(){
        return $this->points ;
    }
    public function courseCreditHours(){
    $stud_study_plan = Student::StudentStudyPlan($this->student_id);
    $course = $stud_study_plan->courseDetails($this->course_id);
    $hours =  $course == null ? '1' : $course->credit_hours ;
    return $hours ;
    }

    public function studyYear(){
        return $this->belongsTo(StudyYear::class);
    }
    public function semester(){
        return $this->belongsTo(Semester::class);
    }
    public function course(){
        return $this->belongsTo(Course::class);
    }
    public function student(){
        return $this->belongsTo(Student::class);
    }
    public function faculty(){
        return $this->belongsTo(Faculty::class);
    }
    public function department(){
        return $this->belongsTo(Department::class);
    }
    public function pass() {  return $this->points > 0 ? true : false ;   }

    public static function scopeStudent($query,$student_id){
        return $query->where('student_id','=',$student_id);
    }
    public static function scopeStudyYear($query,$study_year_id){
        return $query->where('study_year_id','=',$study_year_id);
    }
    public static function scopeSemester($query,$semester_id){
        return $query->where('semester_id','=',$semester_id);
    }
    public static function scopeCourse($query,$course_id){
        return $query->where('course_id','=',$course_id);
    }
    public static function scopeFaculty($query,$faculty_id){
        return $query->where('faculty_id','=',$faculty_id);
    }
    public static function scopeDepartment($query,$department_id){
        return $query->where('department_id','=',$department_id);
    }
   public static function finalMarks($study_year_id , $semester_id , $course_id , $faculty_id , $department_id){
       $marks = FinalMark::query()
           ->StudyYear($study_year_id)
           ->Semester($semester_id)
           ->Course($course_id)
           ->Faculty($faculty_id)
           ->Department($department_id)->pluck(student_id)->get();
       return $marks ;
   }
   public function CharEquivalent(){
        $equivalent = Equivalent::where('point_equivalent','=',$this->points)->get();
        if($equivalent != null)
            return $equivalent->char_equivalent ;

   }
}
