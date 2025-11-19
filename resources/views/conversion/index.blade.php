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

                        <form method="POST" action="{{ route('conversions.index.post') }}">
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
                                <label for="amount_to_point_amount" class="col-md-5 col-form-label text-md-end">{{ 'Montan en Point: Montant' }}
                                    <b class="" style="color: red;">*</b></label>

                                <div class="col-md-7">
                                    <input id="amount_to_point_amount" type="number" class="form-control @error('amount_to_point_amount') is-invalid @enderror" name="amount_to_point_amount" value="{{ old('amount_to_point_amount') }}" required autocomplete="amount_to_point_amount" autofocus>
                                    @error('amount_to_point_amount')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="amount_to_point_point" class="col-md-5 col-form-label text-md-end">{{ 'Montan en Point: Point ' }}
                                    <b class="" style="color: red;">*</b>
                                </label>

                                <div class="col-md-7">
                                    <input id="amount_to_point_point" type="number" class="form-control @error('amount_to_point_point') is-invalid @enderror" name="amount_to_point_point" value="{{ old('amount_to_point_point') }}" required autocomplete="amount_to_point_point" autofocus>
                                    @error('amount_to_point_point')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3" >
                                <label for="point_to_amount_point" class="col-md-5 col-form-label text-md-end">{{ 'Point en Montant: Point' }}
                                    <b class="" style="color: red;">*</b></label>

                                <div class="col-md-7">
                                    <input id="point_to_amount_point" type="number" class="form-control @error('point_to_amount_point') is-invalid @enderror" name="point_to_amount_point" value="{{ old('point_to_amount_point') }}" required autocomplete="point_to_amount_point" autofocus>
                                    @error('point_to_amount_point')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="point_to_amount_amount" class="col-md-5 col-form-label text-md-end">{{ 'Point en Montant: Montant ' }}
                                    <b class="" style="color: red;">*</b>
                                </label>

                                <div class="col-md-7">
                                    <input id="point_to_amount_amount" type="number" class="form-control @error('point_to_amount_amount') is-invalid @enderror" name="point_to_amount_amount" value="{{ old('point_to_amount_amount') }}" required autocomplete="point_to_amount_amount" autofocus>
                                    @error('point_to_amount_amount')
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
                                    <input id="birthdate_rate" type="number" class="form-control @error('birthdate_rate') is-invalid @enderror" name="birthdate_rate" value="{{ old('birthdate_rate') }}" required autocomplete="birthdate_rate" autofocus>
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
                                    //alert(input.value);

                                }
                                //
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
