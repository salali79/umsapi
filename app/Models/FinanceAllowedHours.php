<?php

namespace App\Models;



class FinanceAllowedHours extends AppModel
{
   protected $fillable = [
       'study_year_id','semester_id','finance_account_id','hours',
       'created_by' ,'updated_by','deleted_by'];

   public function financeAccount(){
       return $this->belongsTo(FinanceAccount::class);
   }
    public function semester(){
        return $this->belongsTo(Semester::class);
    }
    public function studyYear(){
        return $this->belongsTo(StudyYear::class);
    }

}
