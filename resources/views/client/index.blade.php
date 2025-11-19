@php use Illuminate\Support\Carbon; @endphp
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            @include('layouts.menu')
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ 'Enregistrer un client'}}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('clients.index.post') }}">
                            @csrf
                            <div><h6>Les champs marques par <b class="" style="color: red;">*</b> sont obligatoires</h6></div>

                            <input type="hidden" name="error" id="error" class="form-control @error('error') is-invalid @enderror">
                            @error('error')
                            <span class="invalid-feedback" role="alert" style="position: relative; width: 100%; text-align: center;">
                                        <strong>{{ $message }}</strong>
                                    </span> <br/>
                            @enderror

                            {{--<div class="row mb-3">
                                @if (session('error'))
                                    <div class="alert alert-danger" role="alert">
                                        {{ session('error')['error'] }}
                                    </div>
                                @endif

                            </div>--}}
                            <div class="row mb-3">
                                <label for="name" class="col-md-4 col-form-label text-md-end">{{ 'Nom du Client' }}
                                    <b class="" style="color: red;">*</b>
                                </label>

                                <div class="col-md-6">
                                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                                    @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="telephone" class="col-md-4 col-form-label text-md-end">
                                    {{ 'Telephone' }}
                                    <b class="" style="color: red;">*</b>
                                </label>

                                <div class="col-md-6">
                                    <input id="telephone" type="tel" class="form-control @error('telephone') is-invalid @enderror" name="telephone" value="{{ old('telephone') }}" required autocomplete="telephone">

                                    @error('telephone')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="email" class="col-md-4 col-form-label text-md-end">{{ 'E-Mail'}}</label>

                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" autocomplete="email">

                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="birthdate" class="col-md-4 col-form-label text-md-end"><br>{{ 'Date de Naissance' }}</label>
                                <?php
                                    $date = Carbon::now();
                                    $thisyear = $date->year;

                                ?>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="day" >Jour</label>
                                            <select class="form-select" id="day" name="day">
                                                <option value="">--Choisir ici --</option>
                                                <option value="01">01</option>
                                                <option value="02">02</option>
                                                <option value="03">03</option>
                                                <option value="04">04</option>
                                                <option value="05">05</option>
                                                <option value="06">06</option>
                                                <option value="07">07</option>
                                                <option value="08">08</option>
                                                <option value="09">09</option>
                                                <option value="10">10</option>
                                                <option value="11">11</option>
                                                <option value="12">12</option>
                                                <option value="13">13</option>
                                                <option value="14">14</option>
                                                <option value="15">15</option>
                                                <option value="16">16</option>
                                                <option value="17">17</option>
                                                <option value="18">18</option>
                                                <option value="19">19</option>
                                                <option value="20">20</option>
                                                <option value="21">21</option>
                                                <option value="22">22</option>
                                                <option value="23">23</option>
                                                <option value="24">24</option>
                                                <option value="25">25</option>
                                                <option value="26">26</option>
                                                <option value="27">27</option>
                                                <option value="28">28</option>
                                                <option value="29">29</option>
                                                <option value="30">30</option>
                                                <option value="31">31</option>
                                            </select>

                                        </div>
                                        <div class="col-md-4">
                                            <label for="month" >Mois</label>
                                            <select class="form-select" id="month" name="month">
                                                <option value="">--Choisir ici --</option>
                                                <option value="01">Janvier</option>
                                                <option value="02">Fevrier</option>
                                                <option value="03">Mars</option>
                                                <option value="04">Avril</option>
                                                <option value="05">Mai</option>
                                                <option value="06">Juin</option>
                                                <option value="07">Juillet</option>
                                                <option value="08">Aout</option>
                                                <option value="09">Septembre</option>
                                                <option value="10">Octobre</option>
                                                <option value="11">Novembre</option>
                                                <option value="12">Decembre</option>
                                            </select>

                                        </div>

                                        <div class="col-md-4">
                                            <label for="year" >Annee</label>
                                            <select class="form-select" id="year" name="year">
                                                <option value="">--Choisir ici --</option>
                                                @for($i = $thisyear; $i >= 1900; $i--)
                                                    <option value="{{$i}}">{{$i}}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>


                                    {{--<input id="birthdate" type="date" class="form-control @error('birthdate') is-invalid @enderror" name="birthdate"  autocomplete="birthdate">--}}

                                    @error('birthdate')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="gender" class="col-md-4 col-form-label text-md-end">{{ 'Civilite' }}</label>

                                <div class="col-md-6">
                                    <select id="gender" class="form-control form-select form-select-lg @error('gender') is-invalid @enderror" name="gender" >
                                        <option value="">Choisissez ici</option>
                                        <option value="MONSIEUR">Monsieur</option>
                                        <option value="MADAME">Madame</option>
                                        <option value="MADEMOISELLE">Mademoiselle</option>
                                    </select>

                                    @error('gender')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="city" class="col-md-4 col-form-label text-md-end">{{ 'Ville' }}</label>

                                <div class="col-md-6">
                                    <input id="city" type="text" class="form-control @error('city') is-invalid @enderror" name="city" autocomplete="city">

                                    @error('city')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="quarter" class="col-md-4 col-form-label text-md-end">{{ 'Quartier' }}</label>

                                <div class="col-md-6">
                                    <input id="quarter" type="text" class="form-control @error('quarter') is-invalid @enderror" name="quarter" autocomplete="quarter">

                                    @error('quarter')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ 'Enregistrer' }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
