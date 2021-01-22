<?php

namespace App\Models;



use Astrotomic\Translatable\Translatable;

class StudentMedical extends AppModel
{
    use Translatable;

    protected $with = ['translations'];

    protected $translatedAttributes = ['diagnostic','doctor_name','treatment','notes'];

    protected $fillable  =['date','student_id','created_by','updated_by','deleted_by' ];

    public function student(){
        return $this->belongsTo(Student::class);
    }
}
