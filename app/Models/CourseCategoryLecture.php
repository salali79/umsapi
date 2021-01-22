<?php

namespace App\Models;



class CourseCategoryLecture extends AppModel
{
    protected $fillable =[
        'course_category_id','day', 'start_time','end_time','place',
        'created_by','updated_by','deleted_by'];

    public function courseCategory(){
        return $this->belongsTo(CourseCategory::class);
    }
}
