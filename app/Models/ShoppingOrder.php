<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShoppingOrder extends Model
{
    protected $table='shopping_orders';
    protected $primaryKey='id';
    protected $fillable=['total_price', 'date','status', 'wallet_id'];


    public function order_items(){
        return $this->hasMany(ShoppingOrderItem::class,'product_id','id');
    }

    public function orderable()
    {
        return $this->morphTo();
    }

    public function wallet()
    {
        return $this->belongsTo(ShoppingWallet::class,'wallet_id','id');
    }
}
