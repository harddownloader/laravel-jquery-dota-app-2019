<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class Ban
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
            if($request->ajax()) return response()->json(['success' => false, 'msg' => 'Access Denied'])->setStatusCode(200);
            abort(404);
        } else if(Auth::user()->is_banned == 1) {
            if($request->ajax()) return response()->json(['success' => false, 'msg' => 'Вы заблокированы! Для уточнения причины обратитесь в SUPPORT!'])->setStatusCode(200);
            abort(404);
        }
        return $next($request);
    }
}
