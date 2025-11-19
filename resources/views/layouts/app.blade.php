@php
    use App\Http\Controllers\Auth\RegisterController
   ;use App\Models\User
   ;use App\Models\Config
   ;use Illuminate\Support\Carbon
   ;use Illuminate\Support\Facades\Request
   ;use Illuminate\Support\Facades\Auth
   ;use App\Models\Notification
   ;
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
if (count(User::all()) === 0) {
    $registerController = new RegisterController();
    $request = Request::create('/registration', 'POST');
    $request->merge([
        'name' => env('ADMIN_NAME'),
        'email' => env('ADMIN_EMAIL'),
        'username' => env('ADMIN_USERNAME'),
        'password' => env('ADMIN_PWD'),
        'password_confirmation' => env('ADMIN_PWD'),
        'is_admin' => 'on'
    ]);
    $response = $registerController->postRegistration($request);
}

if (Auth::check()) {
    $notifications = Notification::where('sender_address', Auth::user()->email)->orWhere('recipient_address', Auth::user()->email)->where('read', false)->get();
    $unreadMsgNum = count($notifications);
}

?>
<div id="app">
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="{{ url('') }}">
                @if(count(Config::all()) > 0)
                    @php
                        $config = Config::where('is_applicable', true)->first();
                    @endphp
                    @if($config != null)
                        <img src="{{asset('storage/' .$config->enterprise_logo)}}"
                             style="margin-top: -20px; margin-bottom: -20px; border-radius: 50%;" height="65" width="65"
                             alt=""> &nbsp; &nbsp;<strong>{{ $config->enterprise_name }}</strong>
                    @else
                        <img src="{{asset('images/logo.png')}}"
                             style="margin-top: -20px; margin-bottom: -20px; border-radius: 50%;" height="65" width="65"
                             alt=""> &nbsp; &nbsp;<strong>{{ config('app.name', 'Laravel') }}</strong>
                    @endif
                @else
                    <img src="{{asset('images/logo.png')}}"
                         style="margin-top: -20px; margin-bottom: -20px; border-radius: 50%;" height="65" width="65"
                         alt=""> &nbsp; &nbsp;<strong>{{ config('app.name', 'Laravel') }}</strong>
                @endif

            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            @if(Auth::check())
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                    </ul>
                </div>
            @endif
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                @if(Auth::check())
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="list-group-item list-group-item-action nav-link" href="{{route('notifs.index', Auth::user()->id)}}"
                               {{--data-bs-toggle="modal" data-bs-target="#notifications-modal"--}}
                               style="font-size: initial;">
                                {{ 'Notifications' }} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <span class="badge bg-success position-absolute top|start-*"
                                      style="
                                            position: relative;
                                            right: 0;
                                            border-radius: 50%;
                                            line-height: 20px;
                                            display: inline-block;
                                            text-align: center;
                                            margin-top: -10px;"
                                >{{$unreadMsgNum}}</span>
                            </a>
                            {{--<div class="modal fade modal-lg" id="notifications-modal" data-bs-backdrop="static"
                                 data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                                 aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
                                     style="overflow-y: initial; width: 75%;">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="staticBackdropLabel"
                                                style="border: 0 red solid; width: 100%;">
                                                <strong
                                                    style="color: darkred;">Notifications</strong>
                                                <span style="
                                            position:relative;
                                            right: 0;
                                            float: right;
                                            color: darkred;
                                            font-size: x-large;"><strong>{{$unreadMsgNum}}</strong></span>
                                            </h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                        </div>
                                        --}}{{--<form method="POST" action="{{route('configuration.post')}}"
                                              enctype="multipart/form-data" onsubmit="return true;" >--}}{{--
                                        <div class="modal-body" style="height: 80vh; overflow-y: auto;">

                                            <input type="hidden" name="error" id="error"
                                                   class="form-control @error('error') is-invalid @enderror">
                                            @error('error')
                                            <span class="invalid-feedback" role="alert"
                                                  style="position: relative; width: 100%; text-align: center;">
                                                                    <strong>{{ $message }}</strong>
                                                                </span> <br/>
                                            @enderror

                                            @csrf

                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="list-group list-group-flush">
                                                        @foreach($notifications as $notification)
                                                            <adiv class="list-group-item list-group-item-action">
                                                                <h5>
                                                                    Objet: <strong>{{$notification->subject}}</strong>
                                                                    &nbsp; &nbsp;
                                                                    <span
                                                                        class="badge bg-primary position-absolute top|start-*"
                                                                        style="position: relative; right: 0; font-size: small;">
                                                        @php
                                                            $sent_at = Carbon::parse($notification->sent_at);
                                                        @endphp
                                                        Le: {{$sent_at->day . '-' . $sent_at->month . '-' . $sent_at->year . ' a ' . $sent_at->hour . ':' . $sent_at->minute . ':' . $sent_at->second}}
                                                        </span>
                                                                </h5>
                                                                <h5 style="font-size: small;">
                                                                    De: {{$notification->sender}}
                                                                    <a href="{{route('notifications.index', $notification->id)}}"
                                                                       style="position: relative; right: 0; float:right; text-decoration: none; margin-top: 5px;">
                                                                        {{'Details'}}
                                                                    </a>
                                                                </h5>
                                                                --}}{{--<br><br>--}}{{--
                                                            </adiv>

                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        --}}{{--<div class="modal-footer">
                                            <button type="button" class="btn btn-danger"
                                                    data-bs-dismiss="modal">Annuler
                                            </button>
                                            <button type="submit" class="btn btn-success">Enregistrer
                                            </button>
                                        </div>--}}{{--
                                        --}}{{--</form>--}}{{--


                                    </div>
                                </div>
                            </div>--}}
                        </li>
                    </ul>
                @endif

                <!-- Right Side Of Navbar notifications-modal -->
                <ul class="navbar-nav ms-auto">
                    <!-- Authentication Links -->
                    @guest
                        @if (Route::has('authentification'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('authentification') }}">{{ __('Login') }}</a>
                            </li>
                        @endif

                        @if (Route::has('_enregistrement_'))
                            @if(count(User::all()) === 0)
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('_enregistrement_') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @endif
                    @else

                        @if(Auth::check())
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                   data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">

                                    <a class="dropdown-item" href="{{ url('password-reset')}}">
                                        Modifier mot de passe
                                    </a>

                                    <a class="dropdown-item"
                                       href="{{ route('user.update-parameter.index', Auth::user()->id)}}">
                                        Modifier mes parametres
                                    </a>

                                    {{--<a class="dropdown-item"
                                       href="{{ route('user.list')}}">
                                        Collaborateur
                                    </a>--}}


                                    <a class="dropdown-item" href="{{ route('deconnexion') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('deconnexion-form').submit();"
                                       id="deconnexion-link">
                                        Deconnexion
                                    </a>

                                    <form id="deconnexion-form" action="{{ route('deconnexion') }}" method="POST"
                                          class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endif
                    @endguest
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
