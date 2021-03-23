<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShoppingProduct extends Model
{
    protected $table='shopping_products';
    protected $primaryKey='id';
    protected $fillable=['product_attribute_id', 'name', 'barcode',
     'name', 'description', 'price', 'image', 'status'];

    public function product_attribute()
    {
        return $this->belongsTo(ShoppingProductAttribute::class,'product_attribute_id','id');
    }
    public function store()
    {
        return $this->hasOneThrough(ShoppingStore::class, ShoppingDepartment::class);
    }
}
