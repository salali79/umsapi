<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShoppingProductAttribute extends Model
{
    protected $table='shopping_product_attributes';
    protected $primaryKey='id';
    protected $fillable=['department_id', 'stock', 'status'];

    public function department()
    {
        return $this->belongsTo(ShoppingDepartment::class,'department_id','id');
    }

}
