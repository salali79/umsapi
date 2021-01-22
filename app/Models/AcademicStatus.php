<?php

namespace App\Models;


use Astrotomic\Translatable\Translatable;

class AcademicStatus extends AppModel
{
    use Translatable;

    protected $with = ['translations'];

    protected $translatedAttributes = ['name'];


    protected $fillable  =['hours','created_by','updated_by','deleted_by' ];

}
