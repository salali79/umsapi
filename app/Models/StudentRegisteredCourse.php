<?php

namespace App\Models;



class StudentRegisteredCourse extends AppModel
{
    protected $fillable =['student_id','course_id','registration_plan_id','registration_course_category_id',
                                'registration_course_group_id','created_by','updated_by','deleted_by'];

    public function student(){
        return $this->belongsTo(Student::class);
    }
    public function course(){
        return $this->belongsTo(Course::class);
    }
    public function registrationPlan(){
        return $this->belongsTo(RegistrationPlan::class);
    }
    public function registrationCourseCategory(){
        return $this->belongsTo(RegistrationCourseCategory::class);
    }
    public function registrationCourseGroup(){
        return $this->belongsTo(RegistrationCourseGroup::class);
    }
}
