<?php

namespace App\Models;
use Astrotomic\Translatable\Translatable;


class Course extends AppModel
{
    use Translatable;

    protected $with = ['translations'];

    protected $translatedAttributes = ['name'];


    protected $fillable  =['code','faculty_id','department_id','created_by','updated_by','deleted_by' ];

    public function faculty(){
        return $this->belongsTo(Faculty::class);
    }

    public function department(){
        return $this->belongsTo(Department::class);
    }
    public function registrationCourses(){
        return $this->hasMany(RegistrationCourse::class);
    }
    public function courseCategory(){
        return $this->hasManyThrough(CourseCategory::class,RegistrationCourse::class);
    }
}
