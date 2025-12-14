<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\RegisterAdminController;
use App\Models\Reward;
use App\Models\SuperAdmin;
use App\Models\User;
use Illuminate\Http\Request;

class GuestController extends Controller
{

    public function __construct()
    {
    }

    public function index(string $locale){

        if (count(SuperAdmin::all()) === 0) {
            $registerAdminController = new RegisterAdminController();
            $request = Request::create('/registration-super-admin', 'POST');
            $request->merge([
                'name' => env('SUPER_ADMIN_NAME'),
                'email' => env('SUPER_ADMIN_EMAIL'),
                'username' => env('SUPER_ADMIN_USERNAME'),
                'telephone' => env('SUPER_ADMIN_TELEPHONE')
            ]);
            $response = $registerAdminController->postRegistrationSuperAdmin($request);
        }


        if (count(User::all()) === 0) {
            $registerAdminController = new RegisterAdminController();
            $request = Request::create('/registration-admin', 'POST');
            $request->merge([
                'name' => env('ADMIN_NAME'),
                'email' => env('ADMIN_EMAIL'),
                'username' => env('ADMIN_USERNAME'),
                'password' => env('ADMIN_PWD'),
                'password_confirmation' => env('ADMIN_PWD'),
                'is_admin' => 'on'
            ]);
            $registerAdminController->postRegistration($request);
        }

        /*if (isset($locale) && in_array($locale, config('app.available_locales'))) {
            app()->setLocale($locale);
        }*/
        //return redirect()->route('welcome', ['locale' => $locale]);
        return view('welcome',['rewards' => Reward::all(), 'error' => '']);
    }

    public static function getApplicationLocal():string
    {
        $locale = app()->getLocale();
        if ($locale === null || $locale === '') {
            $defaultLocale = app('config')->get('app.locale');
            if ($defaultLocale === null) {
                $defaultLocale = app('config')->get('app.fallback_locale');
                if ($defaultLocale === null) {
                    $defaultLocale = 'en';
                }
            }
            return $defaultLocale;
        }
        return $locale;
    }
}
