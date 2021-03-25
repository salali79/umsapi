<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShoppingOrderItem extends AppModel
{
    protected $table='shopping_order_items';
    protected $primaryKey='id';
    protected $fillable=['order_id', 'product_id', 'wallet_id','status'
    ,'created_by','updated_by','deleted_by'];

    public function product()
    {
        return $this->belongsTo(ShoppingProduct::class,'product_id','id');
    }
    
    public function order()
    {
        return $this->belongsTo(ShoppingOrder::class,'order_id','id');
    }

    public function wallet()
    {
        return $this->belongsTo(ShoppingWallet::class,'wallet_id','id');
    }
}
