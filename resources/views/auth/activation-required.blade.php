@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Login') }}</div>

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

                        <a href="{{ route('authentification.client') }}" class="btn btn-primary">Etes-vous client? Cliquez ici</a>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
