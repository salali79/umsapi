<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShoppingWalletCharge extends Model
{
    protected $table='shopping_wallet_charges';
    protected $primaryKey='id';
    protected $fillable=['wallet_id', 'value', 'date', 'status'
    ,'created_by','updated_by','deleted_by'];

    public function wallet()
    {
        return $this->belongsTo(ShoppingWallet::class,'wallet_id','id');
    }
}
