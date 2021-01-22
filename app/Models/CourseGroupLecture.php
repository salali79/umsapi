<?php

namespace App\Models;


class CourseGroupLecture extends AppModel
{
    protected $fillable =[
        'course_group_id','day', 'start_time','end_time','place',
        'created_by','updated_by','deleted_by'];

    public function courseGroup(){
        return $this->belongsTo(CourseGroup::class);
    }
}
