<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class Proceed extends AppModel
{use Translatable;

    protected $with = ['translations'];

    protected $translatedAttributes = ['name'];

    protected $fillable  =['start_date','end_date','created_by','updated_by','deleted_by' ];

    protected $dates =['start_date','end_date'];
}
