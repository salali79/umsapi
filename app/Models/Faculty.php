<?php

namespace App\Models;
use Astrotomic\Translatable\Translatable;

class Faculty extends AppModel
{
   use Translatable;

   protected $with = ['translations'];

   protected $translatedAttributes = ['name'];


   protected $fillable  =['logo','code','color','created_by','updated_by','deleted_by' ];


   public function departments()
   {
    return $this->hasMany(Department::class);
   }

   public function courses(){
       return $this->hasMany(Course::class);
   }

}
