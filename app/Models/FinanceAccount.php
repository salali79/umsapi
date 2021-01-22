<?php

namespace App\Models;


class FinanceAccount extends AppModel
{
    protected $fillable = ['student_id','account_number','created_by','updated_by','deleted_by'] ;

    public function student(){
        return $this->belongsTo(Student::class);
    }
    public function financeDetails(){
        return $this->hasMany(FinanceAllowedHours::class);
    }
    public function financeByYearSemester($study_year_id,$semester_id){
        return $this->financeDetails()
            ->where('study_year_id', $study_year_id)
            ->where('semester_id',$semester_id)
            ->first();
    }
}
