@php use App\Models\Config; @endphp
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">

            @include('layouts.menu')

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">{{ __("Enregistrer une récompense") }}</div>
                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                            @if (session('error'))
                                <div class="alert alert-danger" role="alert">
                                    {{ session('error') }}
                                </div>
                            @endif

                        <form method="POST" action="{{ route('rewards.index.post') }}" enctype="multipart/form-data">
                            @csrf
                            <div><h5>{{__('Les champs marqués par ')}} <b class="" style="color: red;">*</b> {{__('sont obligatoires')}}</h5></div>
                            <br>

                            {{--<input type="hidden" name="error" id="error" class="form-control @error('error') is-invalid @enderror">
                            @error('error')
                            <span class="invalid-feedback" role="alert" style="position: relative; width: 100%; text-align: center;">
                                        <strong>{{ $message }}</strong>
                                    </span> <br/>
                            @enderror--}}

                            <div class="row mb-3" >
                                <label for="name" class="col-md-5 col-form-label text-md-end">{{ __('Nom') }}
                                    <b class="" style="color: red;">*</b></label>

                                <div class="col-md-7">
                                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                           name="name" value="{{ old('name') }}" required autocomplete="name" autofocus
                                           placeholder="{{ __("Nom de la récompense") }}">
                                    @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3" >
                                <label for="nature" class="col-md-5 col-form-label text-md-end">{{ __('Nature') }}
                                    <b class="" style="color: red;">*</b></label>

                                <div class="col-md-7">
                                    <select id="nature" type="text" class="form-control @error('nature') is-invalid @enderror"
                                           name="nature"  required  autofocus>
                                        <option value="">-- {{ __("Sélectionnez ici") }} --</option>
                                        <option value="{{ 'MATERIAL' }}">{{ __("Produit") }}</option>
                                        <option value="{{ 'FINANCIAL' }}">{{ __("Financière") }}</option>
                                        <option value="{{ 'SERVICE' }}">{{ __("Service") }}</option>
                                    </select>
                                    @error('nature')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3" >
                                <label for="value" class="col-md-5 col-form-label text-md-end">{{ __("Valeur financière") }}
                                    <b class="" style="color: red;">*</b></label>

                                <div class="col-md-7">
                                    <input id="value" type="number" class="form-control @error('value') is-invalid @enderror"
                                           name="value" value="{{ old('value') }}" required autocomplete="value" autofocus
                                           placeholder="">
                                    @error('value')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3" >
                                <label for="level" class="col-md-5 col-form-label text-md-end">{{ __("Type de Bon") }}
                                    <b class="" style="color: red;">*</b></label>
                                <div class="col-md-7">
                                    @php
                                        $config = Config::where('is_applicable', true)->first();
                                        $levels = json_decode($config->levels, true);
                                    @endphp
                                    <select id="level" type="text" class="form-control @error('level') is-invalid @enderror"
                                            name="level"  required >
                                        <option >-- {{ __("Sélectionnez ici") }} --</option>
                                        @foreach($levels as $level)
                                            <option value="{{$level['name']}}">{{$level['name']}}</option>
                                        @endforeach
                                    </select>
                                    @error('level')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3" >
                                <label for="image" class="col-md-5 col-form-label text-md-end">{{ __('Image') }}
                                    <b class="" style="color: red;"></b></label>

                                <div class="col-md-7">
                                    <input id="image" type="file" class="form-control @error('name') is-invalid @enderror"
                                           name="image" value="{{ old('image') }}" autofocus
                                           placeholder="{{ __("Image de la récompense") }}">
                                    @error('image')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-0">
                                <div class="col-md-6 offset-md-5">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Enregistrer') }}
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

