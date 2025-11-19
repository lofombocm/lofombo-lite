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

@extends('layouts.voucher-template')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    {{--<div class="card-header">
                        <h3>{{ 'BON ' . $voucher->level }} <br></h3>
                    </div>--}}

                    <div class="card-body" style="margin: 50px;">
                        <br><br><br>
                        <h1 style="width: 100%; text-align: center; border-bottom: 1px black solid;">
                            {{ $voucher->enterprise }}
                        </h1><br>
                        <h2 style="width: 100%; text-align: center; border-bottom: 0px black solid;">
                            {{ 'BON DE TYPE ' . $voucher->level }}
                        </h2><br>
                        <div class="list-group list-group-flush alert alert-info" style="margin-top: -20px;">
                            <a href="#" class="list-group-item list-group-item-action"
                               style="margin-left: 15px; width: 98%; text-decoration: none; color: black;">
                                <span>
                                    No. de Serie: {{$voucher->serialnumber}}
                                    <span style="display: inline; position: relative; float:right; color: #000000;">
                                        {{ 'ID: ' }} {{$voucher->id}}
                                    </span>
                                </span>
                                <br>
                            </a>

                            <a href="#" class="list-group-item list-group-item-action"
                               style="margin-left: 15px; width: 98%; text-decoration: none; color: black;">
                                <br>
                                <span>
                                    Porteur: {{$client->name}}
                                    <span style="display: inline; position: relative; float:right; color: #000000;">
                                        {{ 'Tel: ' }} {{$client->telephone}}
                                    </span>
                                </span>
                                <br>
                            </a>

                            <a href="#" class="list-group-item list-group-item-action"
                               style="margin-left: 15px; width: 98%; text-decoration: none; color: black;">
                                <br>
                                <span>
                                    Point: {{$voucher->point}}
                                </span>
                                <br>
                            </a>

                            <a href="#" class="list-group-item list-group-item-action"
                               style="margin-left: 15px; width: 98%; text-decoration: none; color: black;">
                                <br>
                                <span>
                                    Emetteur: {{$voucher->enterprise}}
                                </span>
                                <br>
                            </a>

                            <a href="#" class="list-group-item list-group-item-action"
                               style="margin-left: 15px; width: 98%; text-decoration: none; color: black;">
                                <br>
                                <span>
                                    Emis le: &nbsp; &nbsp; {{\Illuminate\Support\Carbon::parse($voucher->created_at)->format('d-m-Y H:i:s')}}
                                </span>
                                <br>
                            </a>

                            <a href="#" class="list-group-item list-group-item-action"
                               style="margin-left: 15px; width: 98%; text-decoration: none; color: black;">
                                <br>
                                <span>
                                    Expire le: {{\Illuminate\Support\Carbon::parse($voucher->expirationdate)->format('d-m-Y H:i:s')}}
                                </span>
                                <br>
                            </a>

                            <a href="#" class="list-group-item list-group-item-action"
                               style="margin-left: 15px; width: 98%; text-decoration: none;">
                                <h5 style="display: inline; float: right; margin-top: -110px;">
                                    <?php

                                    $from = [255, 0, 0];
                                    $to = [0, 0, 255];
                                    $qrcode = QrCode::size(200)
                                        ->style('dot')
                                        ->eye('circle')
                                        ->gradient($from[0], $from[1], $from[2], $to[0], $to[1], $to[2], 'diagonal')
                                        ->margin(1)
                                        ->generate($voucher->serialnumber);
                                    ?>
                                    <span style="float: right; text-align: center;">
                                        {{$qrcode}}
                                    </span>
                                    <br>
                                    <span style="float: right; text-align: center; margin-top: 50px; color: black;">
                                        <span><b>{{$voucher->enterprise . ' Vous remercie de votre fidelite.'}}</b></span>
                                    </span>
                                </h5>
                            </a>
                            <br>

                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


