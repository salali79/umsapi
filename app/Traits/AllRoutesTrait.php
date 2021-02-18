<?php
namespace App\Traits;
 
use Illuminate\Http\Request;
 
trait AllRoutesTrait {

    public static function bootAllRoutesTrait()
    {
        //Log who created this model instance
        static::created(function ($model) {
            Log::info(
                'Token for ' . class_basename($model) . ' created by ' . auth('student')->user()->id
                //->getKey()
            );
        });
    }
}
?>