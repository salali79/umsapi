<?php

namespace App\Models;



class StudentFinancialBalanceOld extends AppModel
{
    protected $table = 'student_financial_balance_old';
    protected $fillable = ['student_id','current_balance','coin','discount_rate','discount_value',
        'created_by','updated_by','deleted_by'];

    public function student(){
        return $this->belongsTo(Student::class);
    }
}
