<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShoppingOrder extends AppModel
{
    protected $table='shopping_orders';
    protected $primaryKey='id';
    protected $fillable=['total_price', 'date','status', 'wallet_id'
    ,'created_by','updated_by','deleted_by'];


    public function order_items()
    {
        return $this->hasMany(ShoppingOrderItem::class,'order_id','id');
    }

    public function wallet()
    {
        return $this->belongsTo(ShoppingWallet::class,'wallet_id','id');
    }
}
