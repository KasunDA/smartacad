<?php

namespace App\Http\Middleware;

use App\Models\Admin\Users\UserType;
use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            if(Auth::user()->user_type_id === UserType::PARENT)
                // redirect to the PARENT / STUDENT page
                return redirect('/home');
            return redirect('/dashboard');
        }

        return $next($request);
    }
}
