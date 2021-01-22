<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;

class FolderFile extends AppModel
{
    use Translatable ;

    protected $with = ['translations'];

    protected $translatedAttributes = ['name' ,'description'];


    protected $fillable  =['created_by','updated_by','deleted_by' ];

    public function folders()
    {
      return $this->belongsToMany(FolderType::class);
    }



}
