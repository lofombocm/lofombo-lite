@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">

            @include('layouts.menu')

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">{{ 'Enregistrer une recompense' }}</div>
                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('rewards.index.post') }}">
                            @csrf
                            <div><h5>Les champs marques par <b class="" style="color: red;">*</b> sont obligatoires</h5></div>
                            <br>

                            <input type="hidden" name="error" id="error" class="form-control @error('error') is-invalid @enderror">
                            @error('error')
                            <span class="invalid-feedback" role="alert" style="position: relative; width: 100%; text-align: center;">
                                        <strong>{{ $message }}</strong>
                                    </span> <br/>
                            @enderror

                            <div class="row mb-3" >
                                <label for="name" class="col-md-5 col-form-label text-md-end">{{ 'Nom de la recompense' }}
                                    <b class="" style="color: red;">*</b></label>

                                <div class="col-md-7">
                                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                           name="name" value="{{ old('name') }}" required autocomplete="name" autofocus
                                           placeholder="Nom de la recompense">
                                    @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3" >
                                <label for="nature" class="col-md-5 col-form-label text-md-end">{{ 'Nature de la recompense' }}
                                    <b class="" style="color: red;">*</b></label>

                                <div class="col-md-7">
                                    <select id="nature" type="text" class="form-control @error('nature') is-invalid @enderror"
                                           name="nature"  required  autofocus>
                                        <option value="">{{'-- Choisir ici --'}}</option>
                                        <option value="{{ 'MATERIAL' }}">{{ 'Materiel' }}</option>
                                        <option value="{{ 'FINANCIAL' }}">{{'Financiere'}}</option>
                                    </select>
                                    @error('nature')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3" >
                                <label for="value" class="col-md-5 col-form-label text-md-end">{{ 'Valeur financiere de la recompense' }}
                                    <b class="" style="color: red;">*</b></label>

                                <div class="col-md-7">
                                    <input id="value" type="number" class="form-control @error('value') is-invalid @enderror"
                                           name="value" value="{{ old('value') }}" required autocomplete="value" autofocus
                                           placeholder="Valeur financiere de la recompense">
                                    @error('value')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-0">
                                <div class="col-md-6 offset-md-5">
                                    <button type="submit" class="btn btn-primary">
                                        {{ 'Enregistrer' }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        {{'Footer'}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


{{--
--}}
