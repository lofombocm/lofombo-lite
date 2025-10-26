<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        if (Auth::guard('client')->check() && !Auth::guard('client')->user()->active) {
            // User is authenticated but not activated
            // You can redirect them to an activation required page or show an error
            return redirect('/activation-required')->with('error', 'Your account is not activated.');
        }
        return $next($request);
    }
}
