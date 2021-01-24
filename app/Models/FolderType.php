<?php

namespace App\Models;


use Astrotomic\Translatable\Translatable;
class FolderType extends AppModel
{
    use Translatable ;

    protected $with = ['translations'];

    protected $translatedAttributes = ['name','description'];

    protected $fillable  =['study_year_id','created_by','updated_by','deleted_by' ];


    public function papers()
    {
      return $this->belongsToMany(FolderFile::class, 'folder_file_id', 'folder');
    }
    public function studyYear(){

      return $this->belongsTo(StudyYear::class,'study_year_id');
    }
}
