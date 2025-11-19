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
                        <h5 style="display: inline; float: right;">
                            {{--@if(count(Config::where('is_applicable', true)->get()) > 0)--}}
                                <a href="{{ route('enregistrement')}}"
                                   style="text-decoration: none; font-size: x-large; color: green;"
                                   id="add_level_field"
                                   title="Enregistrer un utilisateur">
                                    <strong><span class="glyphicon glyphicon-plus">+</span></strong>
                                    <span style="font-size: initial;">{{ 'Ajouter' }}</span>
                                </a>
                            {{--@endif--}}
                        </h5>
                    </div>
                    <div class="card-body" >

                        @if (session('error'))
                            <div class="alert alert-danger" role="alert">
                                {{ session('error') }}
                            </div>
                        @endif
                        @if (session('status'))
                            <div class="alert alert-success" role="alert" style="text-align: center;">
                                <h5>{{ session('status') }}</h5>
                            </div>
                        @endif

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
                                                <a class="btn btn-sm btn-link" href="#" data-bs-toggle="modal"
                                                   data-bs-target="#confirm-remove-admin-role-modal" style="text-decoration: none;">
                                                    <b style="color: red;">{{'Retirer'}}</b>
                                                </a>
                                                <div class="modal fade" id="confirm-remove-admin-role-modal"
                                                     data-bs-backdrop="static"
                                                     data-bs-keyboard="false" tabindex="-1"
                                                     aria-labelledby="staticBackdropLabel"
                                                     aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h1 class="modal-title fs-5" id="staticBackdropLabel">
                                                                    Vous confirmer que {{$user->name}}  n'est plus Administrateur
                                                                </h1>
                                                                <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal"
                                                                        aria-label="Close"></button>
                                                            </div>
                                                            <form method="POST"
                                                                  action="{{route('utilisateurs.admin.ad.or.remove.role', $user->id)}}"
                                                                  onsubmit="return true;">
                                                                <div class="modal-body">

                                                                    <input type="hidden" name="error" id="error"
                                                                           class="form-control @error('error') is-invalid @enderror">
                                                                    @error('error')
                                                                    <span class="invalid-feedback" role="alert"
                                                                          style="position: relative; width: 100%; text-align: center;">
                                                                    <strong>{{ $message }}</strong>
                                                                </span> <br/>
                                                                    @enderror

                                                                    @csrf
                                                                    <input type="hidden" name="operation" value="remove">

                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-danger"
                                                                            data-bs-dismiss="modal">Annuler
                                                                    </button>
                                                                    <button type="submit" class="btn btn-success">
                                                                        Confirmer
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else

                                                <a class="btn btn-sm btn-link" href="#" data-bs-toggle="modal"
                                                   data-bs-target="#confirm-add-admin-role-modal" style="text-decoration: none;">
                                                    <b style="color: limegreen;">{{'Ajouter'}}</b>
                                                </a>
                                                <div class="modal fade" id="confirm-add-admin-role-modal"
                                                     data-bs-backdrop="static"
                                                     data-bs-keyboard="false" tabindex="-1"
                                                     aria-labelledby="staticBackdropLabel"
                                                     aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h1 class="modal-title fs-5" id="staticBackdropLabel">
                                                                    Vous confirmer que {{$user->name}} devient Administrateur
                                                                </h1>
                                                                <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal"
                                                                        aria-label="Close"></button>
                                                            </div>
                                                            <form method="POST"
                                                                  action="{{route('utilisateurs.admin.ad.or.remove.role', $user->id)}}"
                                                                  onsubmit="return true;">
                                                                <div class="modal-body">

                                                                    <input type="hidden" name="error" id="error"
                                                                           class="form-control @error('error') is-invalid @enderror">
                                                                    @error('error')
                                                                    <span class="invalid-feedback" role="alert"
                                                                          style="position: relative; width: 100%; text-align: center;">
                                                                    <strong>{{ $message }}</strong>
                                                                </span> <br/>
                                                                    @enderror

                                                                    @csrf
                                                                    <input type="hidden" name="operation" value="add">

                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-danger"
                                                                            data-bs-dismiss="modal">Annuler
                                                                    </button>
                                                                    <button type="submit" class="btn btn-success">
                                                                        Confirmer
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>

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

