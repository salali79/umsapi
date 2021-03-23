<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShoppingStore extends Model
{
    protected $table='shopping_stores';
    protected $primaryKey='id';
    protected $fillable=['title','type','image', 'status', 'store_type_id'];


    public function departments(){
        return $this->hasMany(ShoppingDepartment::class,'store_id','id');
    }

    public function type(){
        return $this->belongsTo(ShoppingStoreType::class,'store_type_id','id');
    }
}
