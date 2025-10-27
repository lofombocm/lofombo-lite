@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">

            @include('layouts.menu')

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">{{ 'Enregistrer une conversion' }}</div>
                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('conversions-amount-points.index.post') }}">
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
                                <label for="min_amount" class="col-md-5 col-form-label text-md-end">{{ 'Mntant minimal' }}
                                    <b class="" style="color: red;">*</b></label>

                                <div class="col-md-7">
                                    <input id="min_amount" type="number" class="form-control @error('min_amount') is-invalid @enderror"
                                           name="min_amount" value="{{ old('min_amount') }}" required autocomplete="min_amount" autofocus
                                    placeholder="Quel mntant donne droit a un point?">
                                    @error('min_amount')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="birthdate_rate" class="col-md-5 col-form-label text-md-end">{{ 'Coefficient de bonification d\'Anniversaire' }}
                                    <b class="" style="color: red;">*</b>
                                </label>

                                <div class="col-md-7">
                                    <input id="birthdate_rate" type="number" step="0.01" class="form-control @error('birthdate_rate') is-invalid @enderror" name="birthdate_rate"
                                           value="{{ old('birthdate_rate') }}" required autocomplete="birthdate_rate" autofocus
                                           placeholder="{{ 'Bonification en point sur tous les achats  du client effectues son jour d\'anniversaire' }}">
                                    @error('birthdate_rate')
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
                        {{'Footer'}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


{{--
--}}
