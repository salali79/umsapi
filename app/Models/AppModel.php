<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class AppModel extends Model
{

    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public function getImagePathAttribute()
    {
        return $this->image ? asset('uploads/'.$this->table.'/'.$this->image): null;
    }

    public function getImageThumbPathAttribute()
    {
        return $this->image ? asset('uploads/'.$this->table.'/thumbs/'.$this->image): null;
    }

    public function createdBy(){
        return $this->hasOne(User::class,'id','created_by')->select('id','name');
    }

    public function updatedBy(){
        return $this->hasOne(User::class,'id','updated_by');
    }

    public function deletedBy() {
        return $this->hasOne(User::class, 'id', 'deleted_by');
    }

    /**
     * Scope a query to only include active items.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function getDomainImagePathAttribute()
    {
        return $this->image ? ('http://ums.demo.hpuhospital.sy/uploads/'.$this->table.'/'.$this->image): null;
    }

}
