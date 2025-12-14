<?php

namespace App\Http\Middleware;

use App\Http\Controllers\SuperAdminController;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureLicenseIsActive
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
            session()->flash('error', __("Aucune licence active."));
            return redirect()->route('activation-required')->with('error', __("Aucune licence active."));
        }
        return $next($request);
    }
}
