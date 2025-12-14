<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdministrator
{
    /**
     * Handle an incoming request.
     *
     * @param
     */
    public function handle(Request $request, Closure $next): Response
    {
        EnsureUserIsActivated::checkLicences();

        if (Auth::check() && (!Auth::user()->active || !Auth::user()->is_admin)) {
            // User is authenticated but not activated
            // You can redirect them to an activation required page or show an error
            //Session::flush();
            Auth::logout();
            return redirect('/admin-required')->with('error', __("Votre compte n'est pas actif."));
        }
        return $next($request);
    }
}
