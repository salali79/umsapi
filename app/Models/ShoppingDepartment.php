<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShoppingDepartment extends Model
{
    protected $table='shopping_departments';
    protected $primaryKey='id';
    protected $fillable=['store_id', 'title','image', 'status'];

    public function store(){
        
        return $this->belongsTo(ShoppingStore::class,'store_id','id');
    }
}
