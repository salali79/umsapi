<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class RegisterWay extends AppModel
{
    use Translatable;

    protected $with = ['translations'];

    protected $translatedAttributes = ['name','description'];

    protected $fillable  =['created_by','updated_by','deleted_by'];

    public  function registerParams(){
        return $this->hasMany(RegisterParam::class);
    }
    public function studentRegisterWayParam(){
        return $this->hasManyThrough(StudentRegisterParam::class,RegisterParam::class);
    }
}
