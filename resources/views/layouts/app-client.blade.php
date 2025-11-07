@php
    use App\Models\Config
  ; use App\Models\Loyaltyaccount
  ; use App\Models\Notification
  ; use Illuminate\Support\Facades\Auth
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
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body style="font-size: initial;">
<div id="app">

    <?php
    if (Auth::guard('client')->check()) {
        $notifications = Notification::
            where('recipient_address', Auth::guard('client')->user()->telephone)->where('read', false)->get();
        $unreadMsgNum = count($notifications);
    }
    ?>

    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                @if(count(Config::all()) > 0)
                    @php
                        $config = Config::where('is_applicable', true)->first();
                    @endphp
                    @if($config != null)
                        <img src="{{asset('storage/' .$config->enterprise_logo)}}"
                             style="margin-top: -20px; margin-bottom: -20px; border-radius: 50%;" height="65" width="65" alt=""> &nbsp;
                        &nbsp;{{ $config->enterprise_name }}
                    @else
                        <img src="{{asset('images/logo.png')}}" style="margin-top: -20px; margin-bottom: -20px; border-radius: 50%;"
                             height="65"
                             width="65" alt=""> &nbsp; &nbsp;{{ config('app.name', 'Laravel') }}
                    @endif

                @else
                    <img src="{{asset('images/logo.png')}}" style="margin-top: -20px; margin-bottom: -20px; border-radius: 50%;" height="65"
                         width="65" alt=""> &nbsp; &nbsp;{{ config('app.name', 'Laravel') }}
                @endif

            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            @if(Auth::guard('client')->check())
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                    </ul>
                </div>
            @endif

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                @if(Auth::guard('client')->check())
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="list-group-item list-group-item-action nav-link" href="{{ route('authentification') }}"
                               style="font-size: initial;"
                               data-bs-toggle="modal" data-bs-target="#notifications-modal">
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
                        </li>
                    </ul>
                @endif
                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ms-auto">
                    <!-- Authentication Links -->
                    {{--@guest--}}
                    {{--@if (Route::has('enregistrement'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('enregistrement') }}">{{ __('Register') }}</a>
                        </li>
                    @endif--}}
                    {{-- @else--}}

                    @if(Auth::guard('client')->user() != null)
                            <?php

                            $client = Auth::guard('client')->user();
                            $loyaltyaccount = Loyaltyaccount::where('holderid', $client->id)->first();

                            ?>
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                               data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                <strong>Solde: {{$loyaltyaccount->point_balance}} points
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                </strong>
                                <strong>{{ Auth::guard('client')->user()->name }}</strong>
                            </a>

                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">

                                <a class="dropdown-item"
                                   href="{{ route('clients.form.update.client', Auth::guard('client')->user()->id)}}">
                                    Modifier Mes parametres
                                </a>
                                <a class="dropdown-item" href="{{ url('password-reset-client')}}">
                                    Modifier mot de passe
                                </a>

                                <a class="dropdown-item" href="{{ route('deconnexion') }}"
                                   onclick="event.preventDefault();
                                                     document.getElementById('deconnexion-client-form').submit();">
                                    Deconnexion
                                </a>

                                <form id="deconnexion-client-form" action="{{ route('deconnexion.client') }}"
                                      method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @endif
                    {{--@endguest--}}
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
