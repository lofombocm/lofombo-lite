@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">

            @include('layouts.menu')

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">{{ 'Enregistrer un type de transaction' }}</div>
                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('transactiontype.post') }}">
                            @csrf
                            <div><h5>Les champs marques par <b class="" style="color: red;">*</b> sont obligatoires</h5></div>
                            <br>

                            <input type="hidden" name="error" id="error" class="form-control @error('error') is-invalid @enderror">
                            @error('error')
                            <span class="invalid-feedback" role="alert" style="position: relative; width: 100%; text-align: center;">
                                        <strong>{{ $message }}</strong>
                                    </span> <br/>
                            @enderror

                            <div class="row mb-3">
                                <label for="name" class="col-md-4 col-form-label text-md-end">{{ 'Nom' }}
                                    <b class="" style="color: red;">*</b></label>

                                <div class="col-md-8">
                                    <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name') }}" required autocomplete="name" autofocus />
                                    @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="description" class="col-md-4 col-form-label text-md-end">{{ 'Description' }}
                                    <b class="" style="color: red;">*</b>
                                </label>

                                <div class="col-md-8">
                                    <textarea id="description"  class="form-control @error('amount') is-invalid @enderror" name="description"  required autocomplete="description" autofocus>{{ old('description') }}</textarea>
                                    @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="credite_ou_debite" class="col-md-4 col-form-label text-md-end">
                                    {{ 'Credite ou debite le compte de fidelite' }}
                                    <b class="" style="color: red;">*</b>
                                </label>

                                <div class="col-md-8">
                                    <div class="row" id="credite_ou_debite">
                                        <div class="alert alert-light col-md-6">
                                            <label for="credit">Credite le compte</label> &nbsp; &nbsp;
                                            <input type="radio" id="credit" name="signe" value="1" style="height: 20px; width: 20px;"/>
                                        </div>
                                        <div class="alert alert-light col-md-6">
                                            <label for="debit">Debite le compte</label> &nbsp; &nbsp;
                                            <input type="radio" id="debit" name="signe" value="-1"  style="height: 20px; width: 20px;"/>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-0">
                                <div class="col-md-6 offset-md-3">
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
