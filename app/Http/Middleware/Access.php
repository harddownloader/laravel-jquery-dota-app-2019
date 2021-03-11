<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class Access
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string $role
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(Auth::guest()) {
            if($request->ajax()) return response('Access Denied')->setStatusCode(403);
            abort(404);
        } else if(Auth::user()->permission != 2) {
            if($request->ajax()) return response('Access Denied')->setStatusCode(403);
            abort(404);
        }
        return $next($request);
    }
}
