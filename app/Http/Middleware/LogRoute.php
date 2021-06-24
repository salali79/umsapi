<?php

namespace App\Http\Middleware;

use Closure;
//use Illuminate\Support\Facades\Log;
use App\Models\Log;

class LogRoute
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $is_developer = \Config::get('app.is_developer');
        if($is_developer == 1){
            $user_id = 'DEVELOPER';
        } else{
            $user_id = auth('student')->user() ? auth('student')->user()->id : 'GUEST';
        }

        $log = [
            'url' => $request->getUri(),
            'user_id' => $user_id,
            'method' => $request->getMethod(),
            'request_body' => $request->all(),
            'response' => $response->getContent(),
        ];
        Log::create($log);

        return $response;
    }
}
