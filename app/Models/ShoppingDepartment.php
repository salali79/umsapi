<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShoppingDepartment extends AppModel
{
    protected $table='shopping_departments';
    protected $primaryKey='id';
    protected $fillable=['store_type_id', 'title', 'description','image', 'status'
    ,'created_by','updated_by','deleted_by'];

    public function store_type()
    {
        return $this->belongsTo(ShoppingStoreType::class,'store_type_id','id');
    }

    public function product_attributes()
    {
        return $this->hasMany(ShoppingProductAttribute::class,'department_id','id');
    }

    /*public function products()
    {
        return $this->hasMany(ShoppingProduct::class, ShoppingProductAttribute::class,'department_id', 'product_id', 'id','id');
    }*/
}
