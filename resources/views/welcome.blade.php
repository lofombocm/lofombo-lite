@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    {{--<div class="card-header">Vous etes la Bienvenue sur LOFOMBO</div>--}}

                    <div class="card-body">

                        <br>
                        <h1 style="font-size: 75px; text-align: center;">
                            {{'Vous etes la Bienvenue sur '}}
                            @if(count(\App\Models\Config::where('is_applicable', true)->get()) > 0)
                                @php
                                    $config = \App\Models\Config::where('is_applicable', true)->first();
                                @endphp
                                @if($config === null)
                                    {{'LOFOMBO'}}
                                @else
                                    {{$config->enterprise_name}}
                                @endif
                            @else
                                {{'LOFOMBO'}}
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
                            <a href="{{ route('authentification.client') }}" class="btn btn-primary btn-lg">
                                <strong style="font-size: xx-large;">Etes-vous client? Cliquez ici</strong>
                            </a>
                        @endif

                        <br><br><br>
                        <h1 style="font-size: xx-large;">
                            @if(count($rewards) > 0) {{'Nos recompenses'}}@endif
                        </h1>
                        <br>
                        @include('reward.list')
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
