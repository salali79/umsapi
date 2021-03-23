<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShoppingCharge extends Model
{
    protected $table='shopping_charges';
    protected $primaryKey='id';
    protected $fillable=['wallet_id', 'value', 'date', 'status'];

    public function wallet()
    {
        return $this->belongsTo(ShoppingWallet::class,'wallet_id','id');
    }
}
