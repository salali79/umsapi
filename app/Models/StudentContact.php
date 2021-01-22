<?php

namespace App\Models;



use Astrotomic\Translatable\Translatable;

class StudentContact extends AppModel
{
    use Translatable;

    protected $with = ['translations'];

    protected $translatedAttributes = ['current_address','permanent_address'];

    protected $guarded =[];

    public function student(){
        return $this->belongsTo(Student::class);
    }

}
