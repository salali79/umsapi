<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;class StudentRegisterParam extends AppModel
{
    protected $guarded  =[];
    protected $appends = ['param_name'];
    public function students(){
        return $this->belongsTo(Student::class,'student_id');
    }

    public function registerParam(){
        return $this->belongsTo(RegisterParam::class);
    }
    public function getParamNameAttribute(){
            return DB::table('register_param_translations')
                ->where('register_param_id',$this->register_param_id)
                ->where('locale','ar')->value('name');
    }

}
