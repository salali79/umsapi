<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShoppingStoreType extends Model
{
    protected $table='shopping_store_types';
    protected $primaryKey='id';
    protected $fillable=['name','description', 'status'];

    public function stores(){
        return $this->hasMany(ShoppingStore::class,'store_type_id','id');
    }

}
