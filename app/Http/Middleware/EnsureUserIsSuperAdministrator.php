<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsSuperAdministrator
{
    /**
     * Handle an incoming request.
     *
     * @param
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('super_admin')->check() && (!Auth::guard('super_admin')->user()->active || !Auth::guard('super_admin')->user()->is_super_admin)) {
            // User is authenticated but not activated
            // You can redirect them to an activation required page or show an error
            Auth::guard('super_admin')->logout();
            return redirect('/super-admin-required')->with('error', __("Votre compte n'est pas actif."));
        }
        //dd(Auth::guard('super_admin')->user());
        return $next($request);
    }
}
