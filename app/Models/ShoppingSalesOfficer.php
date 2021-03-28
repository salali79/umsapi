<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Astrotomic\Translatable\Translatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class ShoppingSalesOfficer extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $table='shopping_sales_officers';
    protected $primaryKey='id';
    protected $fillable=['name', 'username', 'password', 'store_id', 'status'
                        ,'created_by','updated_by','deleted_by'];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }
    public function store()
    {
        return $this->belongsTo(ShoppingStore::class,'store_id','id');
    }
}

