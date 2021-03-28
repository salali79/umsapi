<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShoppingProduct extends AppModel
{
    protected $table='shopping_products';
    protected $primaryKey='id';
    protected $fillable=['name', 'barcode',
     'name', 'description', 'price', 'image', 'department_id',
      'status', 'created_by','updated_by','deleted_by'];


    public function order_items()
    {
        return $this->hasMany(ShoppingOrderItem::class,'product_id','id');
    }
    public function attributes()
    {
        return $this->hasMany(ShoppingProductAttribute::class,'product_id','id');
    }
    public function department()
    {
        return $this->belongsTo(ShoppingDepartment::class,'department_id','id');
    }
}
