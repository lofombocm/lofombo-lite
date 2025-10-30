@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Vous etes la Bienvenue sur LOFOMBO</div>

                    <div class="card-body">

                        <a href="{{ route('authentification.client') }}" class="btn btn-primary">Etes-vous client?
                            Cliquez ici</a>
                        <br>

                        <h1 style="font-size: xxx-large;">
                            Vous etes la Bienvenue sur LOFOMBO
                        </h1>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
