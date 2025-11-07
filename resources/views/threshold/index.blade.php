@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">

            @include('layouts.menu')

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">{{ 'Enregistrer les seuils de points pour les gategories de bons' }}</div>
                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('thresholds.index.post') }}">
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
                                <label for="classic_threshold" class="col-md-5 col-form-label text-md-end">{{ 'Seuil Categorie Classique' }}
                                    <b class="" style="color: red;">*</b></label>

                                <div class="col-md-7">
                                    <input id="classic_threshold" type="number" class="form-control @error('classic_threshold') is-invalid @enderror"
                                           name="classic_threshold" value="{{ old('classic_threshold') }}" required autocomplete="classic_threshold" autofocus
                                           placeholder="Montant Minimal pour obtenir un bon de type classique">
                                    @error('classic_threshold')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3" >
                                <label for="premium_threshold" class="col-md-5 col-form-label text-md-end">{{ 'Seuil Categorie Premium' }}
                                    <b class="" style="color: red;">*</b></label>

                                <div class="col-md-7">
                                    <input id="premium_threshold" type="number" class="form-control @error('premium_threshold') is-invalid @enderror"
                                           name="premium_threshold" value="{{ old('premium_threshold') }}" required autocomplete="premium_threshold" autofocus
                                           placeholder="Montant Minimal pour obtenir un bon de type Premium">
                                    @error('premium_threshold')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3" >
                                <label for="gold_threshold" class="col-md-5 col-form-label text-md-end">{{ 'Seuil Categorie Gold' }}
                                    <b class="" style="color: red;">*</b></label>

                                <div class="col-md-7">
                                    <input id="gold_threshold" type="number" class="form-control @error('gold_threshold') is-invalid @enderror"
                                           name="gold_threshold" value="{{ old('gold_threshold') }}" required autocomplete="gold_threshold" autofocus
                                           placeholder="Montant Minimal pour obtenir un bon de type Gold">
                                    @error('gold_threshold')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="isapplicable" class="col-md-5 col-form-label text-md-end">{{ 'Est applicable?' }}</label>
                                {{--<b class="" style="color: red;">*</b>--}}

                                <div class="col-md-7">
                                    <input id="isapplicable" type="checkbox" class="@error('isapplicable') is-invalid @enderror" name="isapplicable" value="off"  autocomplete="isapplicable"
                                           style="height: 20px; width: 20px; margin-top: 10px;" onchange="setIsApplicable(this, 'is_applicable')">
                                    <input name="is_applicable" id="is_applicable" value="off" type="hidden">
                                    @error('isapplicable')
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

                            <script type="text/javascript">
                                function setIsApplicable(input, isapplicable) {
                                    //alert(input.value);
                                    if(input.value === 'off') {
                                        input.setAttribute('value', 'on');
                                    } else {
                                        input.setAttribute('value', 'off');
                                    }
                                    var isapplicableinput = document.getElementById(isapplicable);
                                    isapplicableinput.setAttribute('value', input.value);
                                }
                            </script>
                        </form>
                    </div>
                    <div class="card-footer">
                        {{' '}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


{{--
--}}
