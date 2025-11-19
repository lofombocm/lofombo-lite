<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiUserIsAdministrator
{
    /**
     * Handle an incoming request.
     *
     * @param
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = User::where('id', $request->get('userid'))->where('active', true)->where('is_admin', true)->first();
        if (!$user) {
            return response()->json(['error' => 1, 'success'=>0, 'errorMessage' => 'User not found', 'successMessage' =>'', 'result' => array()], Response::HTTP_OK);
        }
        /*if (Auth::check() && (!Auth::user()->active || !Auth::user()->is_admin)) {
            // User is authenticated but not activated
            // You can redirect them to an activation required page or show an error
            return redirect('/admin-required')->with('error', 'Your account is not activated or you ar not administrator.');
        }*/
        return $next($request);
    }
}
