@extends('layouts.email-template')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Connexion') }}</div>

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

                        <a href="{{ route('authentification') }}" class="btn btn-primary btn-lg ">
                            {{__('Cliquez ici pour vous reconnecter') }}
                        </a>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
