<?php

namespace App\Models;



class StudentFinancialBalance extends AppModel
{
    protected $fillable = ['student_id','current_balance','coin','discount_rate',
        'created_by','updated_by','deleted_by'];

    public function student(){
        return $this->belongsTo(Student::class);
    }
}
