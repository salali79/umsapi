<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShoppingWallet extends AppModel
{
    protected $table='shopping_wallets';
    protected $primaryKey='id';
    protected $fillable=['total_price', 'status'
    ,'created_by','updated_by','deleted_by'];

    public function walletable()
    {
        return $this->morphTo();
    }

    public function charges()
    {
        return $this->hasMany(ShoppingWalletCharge::class,'wallet_id','id');
    }

    public function orders()
    {
        return $this->hasMany(ShoppingOrder::class,'wallet_id','id');
    }

    public function active_orders()
    {
        return $this->hasMany(ShoppingOrder::class,'wallet_id','id')->where('status', '0');
    }
}
