@php
    use App\Http\Controllers\Auth\RegisterAdminController
   ;use App\Models\SuperAdmin
   ;use App\Models\User
   ;use App\Models\Config
   ;use App\Models\UserFirstTimeConnection
   ;use Illuminate\Support\Carbon
   ;use Illuminate\Support\Facades\Request
   ;use Illuminate\Support\Facades\Auth
   ;use App\Models\Notification
   ;use App\Http\Controllers\GuestController;
@endphp
    <!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js', 'resources/js/myScript.js'])
</head>
<body style="font-size: initial; font-family: 'DejaVu Sans Light';">


<?php
if (count(SuperAdmin::all()) === 0) {
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
    $response = $registerAdminController->postRegistration($request);
}
?>
<div id="app">
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <img src="{{asset('images/logo.jpeg')}}"
                     height="70" width="300"
                     alt="">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                <ul class="navbar-nav me-auto">

                </ul>


                <!-- Right Side Of Navbar notifications-modal -->
                <ul class="navbar-nav ms-auto">
                    <!-- Authentication Links -->
                    @if(!Auth::guard('super_admin')->check())
                        @if (Route::has('authentification.superadmin'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('authentification.superadmin') }}">{{ __('Connexion') }}</a>
                            </li>
                        @endif
                    @endif

                        @if(Auth::guard('super_admin')->check())
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                   data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::guard('super_admin')->user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">

                                    <a class="dropdown-item" href="{{ route('super-admin.password.reset',['locale' => GuestController::getApplicationLocal()])}}">
                                        Modifier mot de passe
                                    </a>

                                    <a class="dropdown-item"
                                       href="{{ route('super_admin.update-parameter.index', Auth::guard('super_admin')->user()->id)}}">
                                        {{__('Mes Paramètres')}}
                                    </a>

                                    <a class="dropdown-item" href="#"
                                       onclick="event.preventDefault();
                                                     document.getElementById('super_admin-deconnexion-form').submit();"
                                       id="deconnexion-link">
                                        Deconnexion
                                    </a>

                                    <form id="super_admin-deconnexion-form" action="{{ route('deconnexion-super_admin') }}" method="POST"
                                          class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endif

                </ul>
            </div>
        </div>
    </nav>

    <main class="py-4">
        @yield('content')
    </main>
</div>

</body>
</html>
