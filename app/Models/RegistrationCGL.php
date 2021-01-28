<?php

namespace App\Models;


class RegistrationCGL extends AppModel
{
    protected $table ='registration_c_g_ls';
    protected $fillable =[
        'registration_course_group_id','day', 'start_time','end_time','place',
        'created_by','updated_by','deleted_by'];

    public function courseGroup(){
        return $this->belongsTo(RegistrationCourseGroup::class,'registration_course_group_id');
    }
}
