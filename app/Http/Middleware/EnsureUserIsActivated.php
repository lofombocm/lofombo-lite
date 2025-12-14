<?php

namespace App\Http\Middleware;

use App\Http\Controllers\SuperAdminController;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
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
        self::checkLicences();

        if (Auth::check() && ! Auth::user()->active) {
            // User is authenticated but not activated
            // You can redirect them to an activation required page or show an error
            //Session::flush();
            Auth::logout();
            return redirect('/activation-required')->with('error', __("Votre compte n'est pas actif."));
        }
        return $next($request);
    }


    public static function checkLicences(){
        $checkLicenses = SuperAdminController::checkLicences();
        if (!$checkLicenses['valid']) {
            //Session::flush();
            Auth::logout();
            return redirect('/activation-required')->with('error', __("Aucune licence active."));
        }

        $valid_licenses = $checkLicenses['valid_licenses'];
        $authUser = Auth::user();
        $foundAuthUser = false;
        if ($authUser) {
            foreach ($valid_licenses as $valid_license) {
                $metadataUsers = $valid_license->metadata['users'];
                foreach ($metadataUsers as $metadataUser) {
                    if ($metadataUser['id'] === $authUser->id) {
                        $foundAuthUser = true;
                        break;
                    }
                }
            }
        }


        if (!$foundAuthUser) {
            //Session::flush();
            Auth::logout();
            return redirect('/activation-required')->with('error', __("Vous avez besoin d'être ajouté a la licence."));
        }
        return true;
    }
}
