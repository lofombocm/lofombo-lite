@php
     use App\Http\Controllers\Auth\RegisterController
    ;use App\Models\User
    ;use App\Models\Config
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
<body style="font-size: initial;">



<?php
    if (count(User::all()) === 0) {
        $registerController = new RegisterController();
        $request = Request::create('/registration', 'POST');
        $request->merge([
            'name' => env('ADMIN_NAME'),
            'email' => env('ADMIN_EMAIL'),
            'password' => env('ADMIN_PWD'),
            'password_confirmation' =>env('ADMIN_PWD'),
            'is_admin' => 'on'
        ]);
        $response = $registerController->postRegistration($request);
    }

    if (Auth::check()){
        $notifications = Notification::where('sender_address', Auth::user()->email)->where('read', false)->get();
        $unreadMsgNum = count($notifications);
    }

?>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    @if(count(Config::all()) > 0)
                        @php
                            $config = Config::where('is_applicable', true)->first();
                        @endphp
                        @if($config != null)
                            <img src="{{asset('storage/' .$config->enterprise_logo)}}" style="margin-top: -20px; margin-bottom: -20px; border-radius: 50%;" height="65" width="65" alt=""> &nbsp; &nbsp;{{ $config->enterprise_name }}
                        @else
                        <img src="{{asset('images/logo.png')}}" style="margin-top: -20px; margin-bottom: -20px; border-radius: 50%;" height="65" width="65" alt=""> &nbsp; &nbsp;{{ config('app.name', 'Laravel') }}
                        @endif
                    @else
                        <img src="{{asset('images/logo.png')}}" style="margin-top: -20px; margin-bottom: -20px; border-radius: 50%;" height="65" width="65" alt=""> &nbsp; &nbsp;{{ config('app.name', 'Laravel') }}
                    @endif

                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
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
                                <a class="list-group-item list-group-item-action nav-link"
                                   href="{{ route('authentification') }}"
                                   data-bs-toggle="modal" data-bs-target="#notifications-modal"
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

                            @if (Route::has('enregistrement'))
                                @if(count(User::all()) === 0)
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('enregistrement') }}">{{ __('Register') }}</a>
                                    </li>
                                @endif
                            @endif
                        @else

                            @if(Auth::check())
                                <li class="nav-item dropdown">
                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                        {{ Auth::user()->name }}
                                    </a>

                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                        <a class="dropdown-item" href="{{ route('deconnexion') }}"
                                           onclick="event.preventDefault();
                                                     document.getElementById('deconnexion-form').submit();" id="deconnexion-link">
                                            Deconnexion
                                        </a>

                                        <a class="dropdown-item" href="{{ url('password-reset')}}">
                                            Modifier mot de passe
                                        </a>

                                        <a class="dropdown-item" href="{{ route('user.update-parameter.index', Auth::user()->id)}}">
                                            Modifier mes parametres
                                        </a>

                                        <form id="deconnexion-form" action="{{ route('deconnexion') }}" method="POST" class="d-none">
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

<?php
if (count(User::all()) === 1) {
?>
    <script type="text/javascript">
        //deconnexion-link
        /*const link = document.getElementById('deconnexion-link');
        if (link) {
            const clickEvent = new MouseEvent('click', {
                bubbles: true,
                cancelable: true,
                view: window
            });
            link.dispatchEvent(clickEvent);
        }*/
    </script>

<?php
}
?>

</body>
</html>
