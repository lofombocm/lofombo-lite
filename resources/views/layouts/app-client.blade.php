@php
    use App\Models\Config
  ; use App\Models\Loyaltyaccount
  ; use App\Models\Notification
  ; use Illuminate\Support\Carbon;use Illuminate\Support\Facades\Auth
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
<body style="font-size: initial; font-family: 'DejaVu Sans Light';">
<div id="app">

    <?php
    if (Auth::guard('client')->check()) {
        $notifications0 = Notification:: where('recipient_address', Auth::guard('client')->user()->telephone)->where('read', false)->get();
        $notifications = [];
        foreach ($notifications0 as $notification){
            array_push($notifications, $notification);
        }

        if(Auth::guard('client')->user()->email != null){
            $notifications1 = Notification::
            where('recipient_address', Auth::guard('client')->user()->email)->orWhere('recipient_address', Auth::guard('client')->user()->email)->where('read', false)->get();
            foreach ($notifications1 as $notification){
                array_push($notifications, $notification);
            }
        }

        //$birthdate = (Auth::guard('client')->user()->birthdate != null) ? Carbon::parse(Auth::guard('client')->user()->birthdate)
        $incompleteProfile = false;
        if(Auth::guard('client')->user()->email == null
            || Auth::guard('client')->user()->birthdate == null
            || Auth::guard('client')->user()->gender == null
            || Auth::guard('client')->user()->quarter == null
            || Auth::guard('client')->user()->city == null){
            $incompleteProfile = true;
        }
        $incompleteProfileMsg = 'Les donnees suivantes sont a completer: ';
        if (Auth::guard('client')->user()->email == null){
            $incompleteProfileMsg .= 'Email';
        }
        if (Auth::guard('client')->user()->birthdate == null){
            $incompleteProfileMsg .= ', Date de naissance (jour en mois)';
        }
        if (Auth::guard('client')->user()->gender == null){
            $incompleteProfileMsg .= ', Civilite';
        }
        if (Auth::guard('client')->user()->quarter == null){
            $incompleteProfileMsg .= ', Quartier';
        }
        if (Auth::guard('client')->user()->city == null){
            $incompleteProfileMsg .= ', Ville';
        }
        $unreadMsgNum = count($notifications);

        $loyaltyaccount =  Loyaltyaccount::where('holderid', Auth::guard('client')->user()->id)->first();
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
                             style="margin-top: -20px; margin-bottom: -20px; border-radius: 50%;" height="65" width="65"
                             alt=""> &nbsp;
                        &nbsp;<strong>{{ $config->enterprise_name }}</strong>
                    @else
                        <img src="{{asset('images/logo.png')}}"
                             style="margin-top: -20px; margin-bottom: -20px; border-radius: 50%;"
                             height="65"
                             width="65" alt=""> &nbsp; &nbsp;<strong>{{ config('app.name', 'Laravel') }}</strong>
                    @endif

                @else
                    <img src="{{asset('images/logo.png')}}"
                         style="margin-top: -20px; margin-bottom: -20px; border-radius: 50%;" height="65"
                         width="65" alt=""> &nbsp; &nbsp;<strong>{{ config('app.name', 'Laravel') }}</strong>
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
                            <a class="list-group-item list-group-item-action nav-link"
                               href="{{ route('clients.notifs.index', Auth::guard('client')->user()->id) }}"
                               style="font-size: initial; display: inline;">
                               {{--data-bs-toggle="modal" data-bs-target="#notifications-modal"--}}
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

                            @if($incompleteProfile)
                                &nbsp;&nbsp;&nbsp;&nbsp;
                                <a  class="" href="{{ route('clients.form.update.client', Auth::guard('client')->user()->id)}}" style="display: inline;" title="{{$incompleteProfileMsg}}">
                                    <img src="{{asset('images/icons8-error-36.png')}}" alt="!" height="25" width="25">
                                </a>
                            @endif

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
                                                    style="color: darkred;">{{'Notifications'}}</strong>
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
                                        --}}{{--<div class="modal-body" style="height: 80vh; overflow-y: auto;">

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
                                                                --}}{{----}}{{--<br><br>--}}{{----}}{{--
                                                            </adiv>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>--}}{{--
                                    </div>
                                </div>
                            </div>--}}
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
