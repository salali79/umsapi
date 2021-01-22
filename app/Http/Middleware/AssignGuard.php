<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Response;

class AssignGuard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if($guard != null){
            if ($guard == "student" && !Auth::guard($guard)->check()) {
                return Response::json(array(
                    'status'  =>  'error',
                    'message'   =>  'unauthenticated'
                ), 401);
            }
        }
        $response = $next($request);
        return $response;
    }
}
