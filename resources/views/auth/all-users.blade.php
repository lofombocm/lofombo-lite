@php

@endphp
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">

            @include('layouts.menu')

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">
                        <div id="top" style="display: inline; float: left;">
                            <a class="btn btn-link"  href="{{url('/home')}}" style="text-decoration: none; font-size: large;">&lt;</a>
                            <button class="btn btn-link" onclick="history.back();" style="text-decoration: none; font-size: large;"><<</button>
                            &nbsp;&nbsp;&nbsp;{{ 'Les Collaborateurs' }}</div>
                    </div>
                    <div class="card-body" >
                        @if(count($users) > 0)

                            <table class="table table-striped table-responsive table-bordered">
                                <thead class="" style="color: darkred;">
                                <th scope="col">
                                    {{ 'ID' }}
                                </th>

                                <th scope="col">
                                    {{ 'Name' }}
                                </th>

                                <th scope="col">
                                    {{ 'Email' }}
                                </th>
                                <th scope="col">
                                    {{ 'Pseudo' }}
                                </th>
                                <th scope="col">
                                    {{ 'Role' }}
                                </th>
                                <th scope="col">
                                    {{'Enregistre le'}}
                                </th>
                                <th scope="col">
                                    {{ 'Actions' }}
                                </th>
                                </thead>
                                <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <th scope="row">
                                            {{$user->id}}
                                        </th>

                                        <td >
                                            {{$user->name}}
                                        </td>

                                        <td >
                                            {{$user->email}}
                                        </td>
                                        <td >
                                            {{$user->username}}
                                        </td>
                                        <td >
                                            {{$user->is_admin? 'ADMIN' : 'SIMPLE UTILISATEUR'}}
                                        </td>

                                        <td >
                                            {{\Illuminate\Support\Carbon::parse($user->created_at)->format('d-m-Y H:i:s')}}
                                        </td>
                                        <td >
                                            @if($user->is_admin)

                                            @else

                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                            {{--<div class="list-group list-group-flush alert alert-light" style="padding-left: 15px;">
                                @foreach($users as $user)
                                    @php
                                        $date_enregistrement = \Illuminate\Support\Carbon::parse($user->created_at)->format('d-m-Y H:i:s');

                                        $bg = '';
                                        if (\Illuminate\Support\Facades\Auth::user()->id === $user->id){
                                            $bg = 'active';
                                        }else{
                                          $bg = $user->active?'list-group-item-success':'list-group-item-danger';
                                        }
                                    @endphp
                                    <a href="#{{$user->id}}" class="list-group-item list-group-item-action {{$bg}}" id="{{$user->id}}">
                                        <h6>
                                            ID: &nbsp; &nbsp; {{$user->id}}
                                        </h6>
                                        <h6 style="float: left;">
                                            Nom: &nbsp; &nbsp; {{$user->name}}
                                        </h6>
                                        <h6 style="display: inline; float: right;">
                                            Email: &nbsp; &nbsp; {{$user->email}}
                                        </h6>
                                        <br>
                                        <h6 style="float: left;">
                                            Administrateur: &nbsp; &nbsp; {{$user->is_admin? 'Oui' : 'Nom'}}
                                        </h6>
                                        <h6 style="display: inline; float: right;">
                                            Enregistre le: &nbsp; &nbsp; {{$date_enregistrement}}
                                        </h6>
                                    </a>
                                @endforeach
                            </div>--}}
                        @else
                            <h5>{{'Aucun utilisateur trouve !'}}</h5>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

