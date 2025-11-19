@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">

            @include('layouts.menu')

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">{{ 'Enregistrer une conversion Point-Recompense' }}</div>
                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('conversions-point-rewards.index.post') }}">
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
                                <label for="min_point" class="col-md-5 col-form-label text-md-end">{{ 'Nombre de point minimal' }}
                                    <b class="" style="color: red;">*</b></label>

                                <div class="col-md-7">
                                    <input id="min_point" type="number" class="form-control @error('min_point') is-invalid @enderror"
                                           name="min_point" value="{{ old('min_point') }}" required autocomplete="min_point" autofocus
                                           placeholder="Combien de point faut-il avoir au moins?">
                                    @error('min_point')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="reward" class="col-md-5 col-form-label text-md-end">{{ 'Coefficient de bonification d\'Anniversaire' }}
                                    <b class="" style="color: red;">*</b>
                                </label>

                                <div class="col-md-7">
                                    <select id="reward"  class="form-control @error('reward') is-invalid @enderror" name="reward"
                                           required >
                                        <option value="">{{ '-- Choisir ici --' }}</option>
                                        @foreach(\App\Models\Reward::all() as $reward)
                                            <option value="{{$reward->id}}">{{ $reward->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('reward')
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
