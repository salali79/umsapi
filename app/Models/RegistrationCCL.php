<?php

namespace App\Models;



class RegistrationCCL extends AppModel
{
    protected $table = 'registration_c_c_ls';
    protected $fillable =[
        'registration_course_category_id','day', 'start_time','end_time','place',
        'created_by','updated_by','deleted_by'];

    public function courseCategory(){
        return $this->belongsTo(RegistrationCourseCategory::class,'registration_course_category_id');
    }
}
