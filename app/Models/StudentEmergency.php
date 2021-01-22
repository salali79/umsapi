<?php

namespace App\Models;



use Astrotomic\Translatable\Translatable;

class StudentEmergency extends AppModel
{
    use Translatable;


    protected $with = ['translations'];

    protected $translatedAttributes = ['name','relationship','address'];


    protected $fillable  =['phone','mobile','email','student_id','created_by','updated_by','deleted_by' ];

    public function student(){

        return $this->belongsTo(Student::class);
    }

}
