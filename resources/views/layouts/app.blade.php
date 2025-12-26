@php
    use App\Http\Controllers\Auth\RegisterAdminController
   ;use App\Models\User
   ;use App\Models\Config
   ;use App\Models\UserFirstTimeConnection
   ;use Illuminate\Support\Carbon
   ;use Illuminate\Support\Facades\Request
   ;use Illuminate\Support\Facades\Auth
   ;use App\Models\Notification
   ;use App\Http\Controllers\GuestController;

   $configuration = Config::where('is_applicable', true)->first();
@endphp
    <!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{--<title>{{ config('app.name', 'Laravel') }}</title>--}}
    @if($configuration !== null)
        <title>{{ $configuration->enterprise_name }}</title>
    @else
        <title>{{ config('app.name', 'Laravel') }}</title>
    @endif


    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'/*, 'resources/css/card.css'*/])
    <script type="text/javascript" src="{{asset('js/chart.min.js')}}"></script>

</head>
<body style="font-size: initial; font-family: 'DejaVu Sans Light'; margin: 0; padding: 0;">


<?php
    if (Auth::check()) {
        //$notifications = Notification::where('sender_address', Auth::user()->email)->where('read', false)->orWhere('recipient_address', Auth::user()->email)->get();
        //$unreadMsgNum = count($notifications);
        $userFirstTimeConnection = UserFirstTimeConnection::where('id', Auth::user()->id)->first();
    }
?>
<div id="app">
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                @if(count(Config::all()) > 0)
                    @php
                        $config = Config::where('is_applicable', true)->first();
                    @endphp
                    @if($config != null && $config->enterprise_logo)
                        {{--<img src="{{asset('storage/' . $config->enterprise_logo)}}"
                             style="margin-top: -20px; margin-bottom: -20px; border-radius: 50%;" height="65" width="65"
                             alt=""> &nbsp; &nbsp;<strong>{{ $config->enterprise_name }}</strong>--}}
                        <img src="{{asset('storage/' . $config->enterprise_logo)}}"
                             height="70" width="300"
                             alt="">
                    @else
                        <img src="{{asset('images/logo.jpeg')}}"
                             height="70" width="300"
                             alt=""> {{--&nbsp; &nbsp;<strong>{{ $config->enterprise_name != null ? $config->enterprise_name : config('app.name', 'Laravel') }}</strong>--}}
                    @endif
                @else
                    <img src="{{asset('images/logo.jpeg')}}"
                         height="70" width="300"
                         alt="">
                    {{--<img src="{{asset('images/logo.png')}}"
                         style="margin-top: -20px; margin-bottom: -20px; border-radius: 50%;" height="65" width="65"
                         alt=""> &nbsp; &nbsp;<strong>{{ config('app.name', 'Laravel') }}</strong>--}}
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
                {{--@if(Auth::check())
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="list-group-item list-group-item-action nav-link"
                               href="{{route('notifs.index', Auth::user()->id)}}"
                               --}}{{--data-bs-toggle="modal" data-bs-target="#notifications-modal"--}}{{--
                               style="font-size: initial;">
                                {{ __('Notifications') }} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
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
                @endif--}}

                <ul class="navbar-nav ms-auto">

                    <li class="nav-item">
                        <div class="form-row align-items-center">
                            <div class="col-auto">
                                <div class="input-group mb-2">
                                    <div class="input-group-prepend">
                                        <div class="">
                                            <img src="{{asset('images/fr.svg')}}" alt="" height="25" width="25" id="flag" style="margin-top: 5px;"> &nbsp;
                                        </div>
                                    </div>
                                    <select class="form-control" name="language" onchange="submitLanguageForm(this.value);"
                                            id="language_selector" style="display: inline; border: 0 white solid; background-color: #FFFFFF; font-size: 1em; font-weight: bolder;" >
                                        @foreach(config('app.available_locales') as $locale)
                                            <option value="{{ $locale }}"
                                                {{$locale === Request::segment(1) ? 'selected' : ''}}>
                                                {{ strtoupper($locale) === 'FR' ? "Français" : (strtoupper($locale) === 'EN' ? "English" : "Français") }}{{-- - {{Request::segment(1)}} - {{$locale}}--}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>



                        <form method="GET" action="" id="select_language_form">
                            {{--@csrf--}}
                        </form>

                        <script type="text/javascript">
                            function submitLanguageForm(locale){
                                var form = document.getElementById('select_language_form');
                                //var currentUrl = window.location.href;
                                var path = window.location.pathname;
                                if(path.length > 0){
                                    var pathWitoutTrailingSlash = path.substring(1, path.length);
                                    var pathArray = pathWitoutTrailingSlash.split('/');
                                    pathArray[0] = locale;
                                    var newPathname = "";
                                    for (i = 0; i < pathArray.length; i++) {
                                        newPathname += "/";
                                        newPathname += pathArray[i];
                                    }
                                    form.action = window.location.protocol + "//" + window.location.host + newPathname;
                                    form.submit();


                                }
                            }

                            function setFlag(){
                                let path = window.location.pathname;
                                if(path.length > 0){
                                    let pathWitoutTrailingSlash = path.substring(1, path.length);
                                    let pathArray = pathWitoutTrailingSlash.split('/');
                                    let  locale = pathArray[0];
                                    console.log("Local from url = " + locale);
                                    let language = locale.toUpperCase();
                                    let flagImg = document.getElementById("flag");
                                    let flagFile = "";
                                    if(language === "FR"){
                                        flagFile = "fr.svg";
                                    }else{
                                        flagFile = "gb.svg";
                                    }
                                    let imgsrc = window.location.protocol + "//" + window.location.host + "/images/" + flagFile;
                                    flagImg.setAttribute('src', imgsrc);
                                }
                            }
                            setFlag();
                        </script>

                    </li>
                </ul>

                <!-- Right Side Of Navbar notifications-modal -->
                <ul class="navbar-nav ms-auto">
                    <!-- Authentication Links -->
                    @guest
                        @if (Route::has('authentification'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('authentification') }}">{{ __('Connexion') }}</a>
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
                                    <strong>{{ Auth::user()->name }}</strong>
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown"
                                     style="border: 2px #164fa9 solid; font-weight: bold; font-size: 1em;">

                                    <a class="dropdown-item" href="{{ url('/'.GuestController::getApplicationLocal().'/password-reset')}}">
                                        <strong>{{ __('Modifier mot de passe') }}</strong>
                                    </a>


                                    <a class="dropdown-item"
                                       href="{{ route('user.update-parameter.index', Auth::user()->id)}}">
                                        <strong>{{ __('Mes Paramètres') }}</strong>
                                    </a>

                                    <a class="dropdown-item" href="#"
                                       onclick="event.preventDefault();
                                                     document.getElementById('deconnexion-form').submit();"
                                       id="deconnexion-link">
                                        <strong>{{ __('Déconnexion') }}</strong>
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
