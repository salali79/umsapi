<?php

namespace App\Models;


use Illuminate\Support\Facades\DB;

class StudyYear extends AppModel
{


    protected $fillable =['beginning','end','name','created_by','updated_by','deleted_by'];

    public function folderTypes(){

        return $this->hasMany(FolderType::class);
    }
    public function folderfiles(){

        $yearfile = DB::table('folder_file_folder_type')->where('study_year_id',$this->id)->get();
        return $yearfile ;
    }
    public function studyPlans(){
        return $this->hasMany(StudyPlan::class);
    }
    public function semesters(){
        return $this->belongsToMany(Semester::class,'study_year_semesters');
    }
}
