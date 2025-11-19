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
                        <h3>{{ 'Invitation a s\'enregistrer  au systeme de fidelite de ' . $data['enterprise'] }} <br></h3>
                    {{ 'Mme/M. ' . $data['name'] }}
                    </div>

                    <div class="card-body">
                        <p>
                            Vous avez ete invite par le systeme de fidelite de l'entreprise  <strong>{{$data['enterprise']}} </strong>.
                            a vous enregistrer.
                            <br>

                        </p>
                        <p>
                            Vous pouvez acceder au systeme en utilisant le lien suivants: &nbsp; <a href="{{$data['invitation_url']}}">{{$data['invitation_url']}}</a>
                            <br>
                        </p>

                            <h5>Merci pour votre fidelite.</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


