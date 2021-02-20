<?php
namespace App\Traits;

use Illuminate\Http\Request;

trait AllRoutesTrait {

    public static function bootAllRoutesTrait()
    {
        //Log who insert data
        static::created(function ($model) {
            Log::info(
                'Token for ' . class_basename($model) . ' created by ' . auth('student')->user()->id
                //->getKey()
            );
        });

        //Log who alter data
        static::updating(function ($model) {
            Log::info(
                'Token for ' . class_basename($model) . ' updated by ' . auth('student')->user()->id
            //->getKey()
            );
        });
    }
}
?>
