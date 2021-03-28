<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShoppingStoreType extends AppModel
{
    protected $table='shopping_store_types';
    protected $primaryKey='id';
    protected $fillable=['name','description', 'status'
    ,'created_by','updated_by','deleted_by'];

    public function stores(){
        return $this->hasMany(ShoppingStore::class,'store_type_id','id');
    }

    public function departments(){
        return $this->hasMany(ShoppingDepartment::class,'store_type_id','id');
    }

    public function product_attributes()
    {
        return $this->hasMany(ShoppingProductAttribute::class,'store_id','id');
    }


}
