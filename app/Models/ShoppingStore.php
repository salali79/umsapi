<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShoppingStore extends AppModel
{
    protected $table='shopping_stores';
    protected $primaryKey='id';
    protected $fillable=['title','store_type_id', 'image', 'status'
    ,'created_by','updated_by','deleted_by'];


    public function store_type()
    {
        return $this->belongsTo(ShoppingStoreType::class,'store_type_id','id');
    }
    public function sallers()
    {
        return $this->hasMany(ShoppingSalesOfficer::class,'store_id','id');
    }

}
