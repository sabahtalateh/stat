<?php

namespace App\Http\Middleware;

use Closure;

class SuperSimpleAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(
            $request->request->get('user') == 'admin' and
            $request->request->get('password') == 'admin'
        ) {
            return $next($request);
        }

        return \Redirect::back();
    }
}
