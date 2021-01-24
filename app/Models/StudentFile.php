<?php

namespace App\Models;
use Illuminate\Support\Facades\DB;


class StudentFile extends AppModel
{
    protected $fillable =['student_id','file_id','image','date','count','pdf','notes','created_by','updated_by','deleted_by'];

    public function student(){
      return $this->belongsTo(Student::class);
    }
    
    public function fileName(){
      return $this->hasOne(FolderFile::class,'id','file_id');
    }

}
