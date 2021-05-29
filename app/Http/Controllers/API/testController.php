<?php

namespace App\Http\Controllers\API;


use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;

class testController extends Controller
{
    
public function test()
{
    # Part 1 (get latitude & longitude)
    $ip = \Request::ip();
    $ip = "178.52.139.113";
    //"192.168.2.74";
    $api_1 = 'https://ipapi.com/' . $ip . '/latlong/';
    $location = file_get_contents($api_1);
    $point = explode(",", $location);

    dd($point);
    # Part 2 (get weather forecast)
    $API_KEY = "428070fbe6e58339e4a4cea49d7519cd";
    $api_2 = 'http://api.openweathermap.org/data/2.5/weather?lat=' . $point[0] . '&lon=' . $point[1] . '&appid=428070fbe6e58339e4a4cea49d7519cd';
    $weather = file_get_contents($api_2);

    return $weather;
}
}

