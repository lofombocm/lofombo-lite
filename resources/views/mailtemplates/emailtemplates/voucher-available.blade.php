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
                        <h1>{{ 'Disponibilite de bon de type ' . $data['type'] }} <br></h1>
                    {{ 'Mme/M. ' . $data['name'] }}
                    </div>

                    <div class="card-body">
                        <p>
                            L'entreprise  <strong>{{env('ENTERPRISE')}} </strong> vous informe que vous disposez des
                            points vous permettant de generer un  bon d'achat de type {{$data['type']}}.<br>

                        </p>
                        <p>
                            Pour le faire vous devez vous connecter en cliquant sur le lien:
                            <a href="{{$data['clientLoginUrl']}}" class="btn btn-primary" >{{'Cliquez ici pour vous connecter.'}}</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


