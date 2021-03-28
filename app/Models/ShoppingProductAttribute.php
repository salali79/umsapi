<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShoppingProductAttribute extends AppModel
{
    protected $table='shopping_product_attributes';
    protected $primaryKey='id';
    protected $fillable=['product_id', 'department_id', 'store_type_id', 'stock', 'status'
    ,'created_by','updated_by','deleted_by'];


    public function product()
    {
        return $this->belongsTo(ShoppingProduct::class,'product_id','id');
    }
    public function department()
    {
        return $this->belongsTo(ShoppingDepartment::class,'department_id','id');
    }
    public function store()
    {
        return $this->belongsTo(ShoppingStoreType::class,'store_type_id','id');
    }

}
