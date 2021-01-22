<?php

namespace App\Models;


use Astrotomic\Translatable\Translatable;
class Department extends AppModel
{
    use Translatable;

    protected $with = ['translations'];

    protected $translatedAttributes = ['name'];

    protected $fillable  =['logo','code','faculty_id','color','created_by','updated_by','deleted_by'];

    public function faculty()
    {
       return $this->belongsTo(Faculty::class);
    }

    public function courses(){
        return $this->hasMany(Course::class);
    }

}
