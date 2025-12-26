{{--@extends('beautymail::templates.sunny', ['color' => '#4204a0'])

@section('content')

    @include ('beautymail::templates.sunny.heading' , [
        'heading' => 'Mme/M. ' . $data['name'] ,
        'level' => 'h1',
    ])

    @include('beautymail::templates.sunny.contentStart')

    <p>
        L'application {{env('APP_NAME')}} vient de recevoir votre demande de recuperation de votre mot de passe.
        Nous vous prions de cliquer sur le bouton ci-dessous pour poursuivre l'operation sollicitee. <br>
    </p>
    @include('beautymail::templates.sunny.contentEnd')

    @include('beautymail::templates.sunny.button', [
        	'title' => 'Cliquez ici pour recuperer votre mot de passe.',
        	'link' => $data['passwordRecoveringUrl']
    ])

    @include('beautymail::templates.sunny.contentStart')
    <p>
        <br>
    </p>
    @include('beautymail::templates.sunny.contentEnd')

    @include('beautymail::templates.sunny.button', [
        	'title' => env('APP_NAME') . ' Accueil/Home',
        	'link' => 'http://localhost:8000'
    ])

@stop--}}

@extends('layouts.email-template')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3>{{ __('Madame') . '/' . __('Monsieur'). ' ' . __("vous avez été enregistré à la plateforme de fidélité de") . ' '  . $data['enterprise'] }} <br></h3>
                    </div>

                    <div class="card-body">
                        <div>
                            {{ __("Vous pouvez accéder au système en utilisant le lien suivant") }}: &nbsp;
                            <a href="{{$data['login_url']}}">{{$data['login_url']}}</a>
                            <br>
                            {{ __("Au premier accès, vous utiliserez les identifiants suivants pour accéder à la plateforme.") }}
                            <ul>
                                <li>
                                    {{ __("Pseudo") }}: {{$data['pseudo']}}
                                </li>
                                <li>
                                    {{ __("Mot de passe") }}: {{'12345678'}}
                                </li>
                            </ul>
                            {{ __("Il vous sera demandé de changer ce mot de passe pour accéder aux multiples  fonctionnalités de la plateforme.") }}

                            <h5>{{ __("Merci pour votre collaboration.") }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


