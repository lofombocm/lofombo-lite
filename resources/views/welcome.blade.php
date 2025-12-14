@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <br>
                        <h1 style="font-size: 75px; text-align: center;">
                            {{ __('Bienvenue sur votre plateforme de fidélité') }}
                            @if(count(\App\Models\Config::where('is_applicable', true)->get()) > 0)
                                @php
                                    $config = \App\Models\Config::where('is_applicable', true)->first();
                                @endphp
                                @if($config === null)
                                    {{ __('LOFOMBO') }}
                                @else
                                    {{$config->enterprise_name}}
                                @endif
                            @else
                                {{ __('LOFOMBO') }}
                            @endif
                        </h1>
                        <br>
                        <br>
                        @if (strlen($error) > 0)
                            <div class="alert alert-danger" role="alert">
                                {{ $error }}
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger" role="alert">
                                <h4>{{ session('error') }}</h4>
                            </div>
                        @endif
                        @if(count(\App\Models\Client::all()))
                            <a href="{{ route('authentification.client') }}" class="btn btn-primary btn-lg" style="background: #164fa9; border: 1px #164fa9 solid;">
                                <strong style="font-size: xx-large;">Etes-vous client? Cliquez ici</strong>
                            </a>
                        @endif

                        <br><br><br>
                        @include('reward.list-card')
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
