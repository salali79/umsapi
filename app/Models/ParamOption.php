<?php

namespace App\Models;



class ParamOption extends AppModel
{
    protected $with = ['translations'];

    protected $translatedAttributes = ['name'];

    protected $fillable  =['register_param_id','created_by','updated_by','deleted_by' ];

    public  function registerParam(){
        return $this->belongsTo(RegisterParam::class);
    }

}
