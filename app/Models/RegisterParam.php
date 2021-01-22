<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;


class RegisterParam extends AppModel
{
    use Translatable;
    protected $appends = ['param_value'];
    protected $with = ['translations'];

    protected $translatedAttributes = ['name','options'];

    protected $fillable  =['type','register_way_id','created_by','updated_by','deleted_by'];

    public function registerWay(){
        return $this->belongsTo(RegisterWay::class);
    }
    public function options(){
        return $this->hasMany(ParamOption::class);
    }

    public function studentsParamValue(){
        return $this->hasMany(StudentRegisterParam::class);
    }
    /*public function studentsParamValue(){
        return $this->hasMany(StudentRegisterParam::class);
    }*/
    public function studentParamValue($student){
        return $this->hasOne(StudentRegisterParam::class)->where('student_id',$student)->get(['value'])->first();
    }
    public function getParamValueAttribute($st){
        return $this->studentParamValue->where('student_id',$st);
    }
}
