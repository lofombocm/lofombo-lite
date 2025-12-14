@php use SimpleSoftwareIO\QrCode\Facades\QrCode; @endphp
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
                        <h3>
                            {{ __("Invitation à rejoindre la plateforme de fidélité"). " " . $data['enterprise'] }} <br>

                        </h3>
                        <h5>{{ __("Par") }} {{$data['inviter']}}</h5>
                    {{ __('Cher'). ' ' . $data['name'] }}, <br>
                        {{__('Vous avez été invité par ')}} {{$data['inviter']}}
                        {{__("à rejoindre la plateforme de fidélité")}}  {{$data['enterprise']}}
                    </div>
                    <div class="card-body">
                        <p>
                            {{__("Pour nous rejoindre, veuillez cliquer sur le lien ci-dessous.")}}
                            <br>

                        </p>
                        <p>
                            <a href="{{$data['invitation_url']}}">{{$data['invitation_url']}}</a>
                            <br>
                        </p>

                            <h5>{{__("Merci pour votre fidélité.")}}</h5>

                        {{--<h5 style="display: inline; float: right; margin-top: 0px;">
                            <?php

                            $from = [255, 0, 0];
                            $to = [0, 0, 255];
                            $qrcode = QrCode::size(200)
                                ->style('dot')
                                ->eye('circle')
                                ->gradient($from[0], $from[1], $from[2], $to[0], $to[1], $to[2], 'diagonal')
                                ->margin(1)
                                ->generate($data['invitation_url']);
                            ?>
                            <span style="float: right; text-align: center;">
                                        {{$qrcode}}
                                    </span>
                            <br>
                            <span style="float: right; text-align: center; margin-top: 50px; color: black;">
                                        <span><b>{{$data['enterprise' . ' Vous remercie de votre fidelite.'}}</b></span>
                                    </span>
                        </h5>--}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


