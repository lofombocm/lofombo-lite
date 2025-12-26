@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card" style="border: 0 red solid; padding: 0;">

                    <div class="card-body" style="border: 0 red solid; margin: 0; padding: 0;">
                        <div style="
                                    border: 0 red solid;
                                    padding: 10px;
                                    background-image: url('{{asset('images/bg.png')}}');
                                    background-repeat: no-repeat;
                                    background-size: cover;
                                    ">
                            <br>
                            <h1 style="font-size: 5em; text-align: center; font-weight: bold; color: #FFFFFF; margin: 10px 20px 10px 20px;">
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
                            {{--@if (strlen($error) > 0)
                                <div class="alert alert-danger" role="alert">
                                    {{ $error }}
                                </div>
                            @endif
                            @if (session('error'))
                                <div class="alert alert-danger" role="alert">
                                    <h4>{{ session('error') }}</h4>
                                </div>
                            @endif--}}
                            @if(count(\App\Models\Client::all()))
                                <a href="{{ route('authentification.client') }}" class="btn btn-primary btn-lg" style="background: #164fa9; border: 1px #164fa9 solid; font-weight: bold;">
                                    <strong style="font-size: xx-large; font-weight: bold;">Etes-vous client? Cliquez ici</strong>
                                </a>
                            @endif

                            <br><br><br>
                        </div>
                        @include('reward.list-card')

                        <br><br><br><br><br><br><br><br><br><br><br>
                        <br><br><br><br><br><br><br><br><br><br><br>
                </div>
            </div>
        </div>
    </div>

@endsection
