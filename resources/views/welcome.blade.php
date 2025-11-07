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
                            Vous etes la Bienvenue sur LOFOMBO
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
                        <a href="{{ route('authentification.client') }}" class="btn btn-primary btn-lg">
                            <strong style="font-size: xx-large;">Etes-vous client? Cliquez ici</strong>
                        </a>
                        <br><br><br>
                        <h1 style="font-size: xx-large;">
                            Nos recompenses
                        </h1>
                        <br>
                        @include('reward.list')
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
