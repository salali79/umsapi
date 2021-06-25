<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $table = "student_registered_logs";
    protected $fillable = ['user_id', 'url', 'method', 'request_body', 'response'];
    protected $casts = [
        'request_body' => 'array'
    ];
}
