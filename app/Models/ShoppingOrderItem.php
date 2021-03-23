<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShoppingOrderItem extends Model
{
    protected $table='shopping_order_items';
    protected $primaryKey='id';
    protected $fillable=['order_id', 'product_id','status'];

    public function product(){
        return $this->belongsTo(ShoppingProduct::class,'product_id','id');
    }
    public function order(){
        return $this->belongsTo(ShoppingOrder::class,'order_id','id');
    }
}
