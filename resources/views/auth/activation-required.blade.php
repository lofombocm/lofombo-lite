@extends('layouts.email-template')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header"><h3 style="color: red;">{{ __('Attention!') }}</h3> </div>

                    <div class="card-body">
                        {{--@if(session('error'))
                            <span>{{session('error')}}</span>
                        @endif--}}
                            @if (session('error'))
                                <div class="alert alert-danger" role="alert">
                                    {{--{{ session('status') }}--}}
                                    {{ session('error') }}
                                </div>
                            @endif

                        <h4 style="color: red;">{{__("Aucune Licence Activée pour cette plateforme. Veuillez contacter le Super Administrateur.")}}</h4>
                        <br>
                        <h4 style="color: red;">{{__("No License Activated for this platform. Please contact the Super Administrator.")}}</h4>
                        {{--<a href="{{ route('authentification.client') }}" class="btn btn-primary btn-lg ">
                            {{'Etes-vous client? Cliquez ici'}}
                        </a>--}}

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
