@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">

            @include('layouts.menu')

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">{{ __('Dashboard') }}</div>
                    <div class="card-body">
                        @if (session('error'))
                            <div class="alert alert-danger" role="alert">
                                {{ session('error') }}
                            </div>
                        @endif

                        {{-- <div class="row justify-content-center">--}}
                        @if (session('status'))
                            <div class="alert alert-success" role="alert" style="text-align: center;">
                                <h5>{{ session('status') }}</h5>
                            </div>
                        @endif



                        <div class="row justify-content-center">
                            {{--@if (session('status'))
                                <div class="alert alert-success" role="alert" style="text-align: center;">
                                    <h5>{{ session('status') }}</h5>
                                </div>
                            @endif--}}
                            <div class="col-md-7">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 style="display: inline; float: left;">{{ 'Recompenses' }}</h4>
                                        {{--<h5 style="display: inline; float: right;">
                                            @if(count(Config::where('is_applicable', true)->get()) > 0)
                                                <a href="{{ route('clients.index')}}" style="text-decoration: none; font-size: x-large; color: green;" id="add_level_field"
                                                   title="Ajouter un client">
                                                    <strong><span class="glyphicon glyphicon-plus">+</span></strong>
                                                    <span style="font-size: initial;">{{ 'Ajouter' }}</span>
                                                </a>
                                            @endif
                                        </h5>--}}
                                        <h5 style="display: inline; float: right;">
                                            @if(Auth::check() && Auth::user()->is_admin)
                                                <a href="{{ route('rewards.index')}}" style="text-decoration: none; font-size: x-large; color: green;" id="add_level_field"
                                                   title="Recompenses">
                                                    <strong><span class="glyphicon glyphicon-plus">+</span></strong>
                                                    <span style="font-size: initial;">{{ 'Ajouter' }}</span>
                                                </a>
                                            @endif
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="list-group list-group-flush">


                                            @include('reward.list')



                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="card">
                                    <div class="card-header"><h4>{{ 'Last Transaction' }}</h4></div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="card-footer">
                        {{' '}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
