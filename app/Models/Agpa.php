<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Agpa extends Model
{
    protected $table = 'agpa';
    protected $fillable  =[ 'courscode', 'eqv', 'dgree', 'rate', 'semster', 'academic_num', 'year', 'filename', 'agpa', 'fauclty', 'eqv_of_eqv', 'study_year_id', 'semester_id', 'study_year_semester_id', 'faculty_id', 'department_id','course_id', 'student_id', 'mark', 'points'];

    public function student(){
        return $this->belongsTo(Student::class,'academic_num','academic_number')->select(['id','academic_number']);
    }

    public function course(){
        return $this->belongsTo(Course::class,'courscode','code');
    }

    public function StudyYear(){
        return $this->belongsTo(StudyYear::class,'year','code');
    }

    public function semester(){
        return $this->belongsTo(Semester::class);
    }

    public function studyYearSemester(){
        return $this->belongsTo(StudyYearSemester::class);
    }

}
