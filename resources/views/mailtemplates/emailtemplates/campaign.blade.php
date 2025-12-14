@php
    use App\Http\Controllers\GuestController;
@endphp
@extends('layouts.email-template')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3>{{ $data['subject'] }} <br></h3>
                    </div>

                    <div class="card-body">
                        <p>
                            {{$data['message']}}

                            <a href="{{url('/'.GuestController::getApplicationLocal())}}" class="btn btn-primary" >{{ __("Cliquez ici pour accéder a notre site web.") }}</a>
                        </p>

                        <h5> {{ __("Merci pour votre Fidélité.") }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


