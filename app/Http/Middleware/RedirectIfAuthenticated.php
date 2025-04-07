<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;


class RedirectIfAuthenticated
{

/** 
 * 
 * @param \Closure(\Illuminate\Http\Request):(\Symfony\Component\HttpFoundation\Response) $next
*/

    
    public function handle(Request $request, Closure $next, string ...$guards):Response
    {

        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
               if(Auth::check()){
                return redirect(route('account.profile')); // 👈 Your custom redirect
               }
            }
        }

        return $next($request);
    }
}