<?php

namespace App\Http\Middleware;

use App\Http\Controllers\SuperAdminController;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class EnsureClientIsActivated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $checkLicenses = SuperAdminController::checkLicences();
        if (!$checkLicenses['valid']) {
            //Session::flush();
            Auth::guard('client')->logout();
            return redirect('/activation-required')->with('error', __("Aucune licence active."));
        }

        if (Auth::guard('client')->check() && !Auth::guard('client')->user()->active) {
            // User is authenticated but not activated
            // You can redirect them to an activation required page or show an error
            //Session::flush();
            Auth::guard('client')->logout();
            return redirect('/activation-required')->with('error', __("Votre compte n'est pas actif."));
        }

        return $next($request);
    }
}
