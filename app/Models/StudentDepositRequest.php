<?php

namespace App\Models;


class StudentDepositRequest extends AppModel
{
    protected $fillable = ['student_id', 'bank_id', 'study_year_id', 'requested_hours', 'request_status',
        'created_by','updated_by','deleted_by'];

    public function student(){
        return $this->belongsTo(Student::class);
    }

    public function bank(){
        return $this->belongsTo(Bank::class);
    }

    public function study_year(){
        return $this->belongsTo(StudyYear::class);
    }
}
