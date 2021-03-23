<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShoppingWallet extends Model
{
    protected $table='shopping_wallets';
    protected $primaryKey='id';
    protected $fillable=['total_price', 'status'];

    public function walletable()
    {
        return $this->morphTo();
    }

}
