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
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
