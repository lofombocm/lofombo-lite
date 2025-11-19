<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActivated
{
    /**
     * Handle an incoming request.
     *
     * @param
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && ! Auth::user()->active) {
            // User is authenticated but not activated
            // You can redirect them to an activation required page or show an error
            return redirect('/activation-required')->with('error', 'Your account is not activated.');
        }
        return $next($request);
    }
}
