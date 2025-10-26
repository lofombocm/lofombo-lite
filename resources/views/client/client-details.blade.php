@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">

            @include('layouts.menu')

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header"><h3>{{ 'Details du client: ' . $client->name }}</h3></div>
                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <div class="list-group list-group-flush alert alert-{{($client->active)?'success':'danger'}}">
                            <a href="#" class="list-group-item list-group-item-action" >
                                <h4 >
                                    Nom:  &nbsp; &nbsp; {{$client->name}}
                                </h4>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action" >
                                <h4 >
                                    Telephone:  &nbsp; &nbsp; {{$client->telephone}}
                                </h4>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action" >
                                <h4 >
                                    Email:  &nbsp; &nbsp; {{(isset($client->email) or !empty($client->email)) ? $client->email : "N/D"}}
                                </h4>
                            </a>

                            <a href="#" class="list-group-item list-group-item-action" >
                                <h4 >
                                    Date de Naissance:  &nbsp; &nbsp;{{(isset($client->birthdate) or !empty($client->birthdate)) ? $client->birthdate : "N/D"}}
                                </h4>
                            </a>

                            <a href="#" class="list-group-item list-group-item-action" >
                                <h4 >
                                    Civilite:  &nbsp; &nbsp;{{(isset($client->gender) or !empty($client->gender)) ? $client->gender : "N/D"}}
                                </h4>
                            </a>

                            <a href="#" class="list-group-item list-group-item-action" >
                                <h4 >
                                    Ville:  &nbsp; &nbsp;{{(isset($client->city) or !empty($client->city)) ? $client->city : "N/D"}}
                                </h4>
                            </a>

                            <a href="#" class="list-group-item list-group-item-action" >
                                <h4 >
                                    Quarter:  &nbsp; &nbsp;{{(isset($client->quarter) or !empty($client->quarter)) ? $client->quarter : "N/D"}}
                                </h4>
                            </a>

                            <a href="#" class="list-group-item list-group-item-action" >
                                <h4 >
                                    Enregistre par:  &nbsp; &nbsp;{{ $user->name }}
                                </h4>
                            </a>


                        </div>
                    </div>

                    </div>

                    <div class="card-footer">
                        {{'Footer'}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
