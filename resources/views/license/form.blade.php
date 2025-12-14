@php use App\Models\Config; @endphp
@extends('layouts.app-super_admin')

@section('content')
    <div class="container">
        <div class="row justify-content-center">

            @include('layouts.super-admin-menu')

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">{{ 'Enregistrer une license' }}</div>
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

                        <form method="POST" action="{{ route('home-super-admin.license.form.index.post') }}">
                            @csrf
                            <div><h5>{{__('Les champs marqués par ')}} <b class="" style="color: red;">*</b> {{__('sont obligatoires')}}</h5></div>
                            <br>

                            <input type="hidden" name="error" id="error" class="form-control @error('error') is-invalid @enderror">
                            @error('error')
                            <span class="invalid-feedback" role="alert" style="position: relative; width: 100%; text-align: center;">
                                        <strong>{{ $message }}</strong>
                                    </span> <br/>
                            @enderror

                            <div class="row mb-3" >
                                <label for="duration" class="col-md-5 col-form-label text-md-end">{{ 'Duree' }}
                                    <b class="" style="color: red;">*</b></label>

                                <div class="col-md-7">
                                    <select id="duration"  class="form-control @error('duration') is-invalid @enderror"
                                           name="duration"  required autocomplete="duration" autofocus
                                    >
                                        <option value="">Choisir ici</option>
                                        <option value="MONTH">1 Mois</option>
                                        <option value="QUARTER">3 Mois</option>
                                        <option value="BIYEAR">6 Mois</option>
                                        <option value="YEAR">1 An</option>
                                    </select>
                                    @error('duration')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3" >
                                <label for="istrial" class="col-md-5 col-form-label text-md-end">{{ 'Essai' }}
                                    <b class="" style="color: red;">*</b></label>

                                <div class="col-md-7">
                                    <input id="istrial" type="checkbox" class="@error('istrial') is-invalid @enderror" name="istrial" value="off"  autocomplete="isadmin"
                                           style="height: 20px; width: 20px; margin-top: 10px;" onchange="setIsTrial(this, 'is_trial')">
                                    <input name="is_trial" id="is_trial" value="NO" type="hidden">
                                    @error('istrial')
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

                                function setIsTrial(input, isTrial) {

                                    /*if(input.value === 'NO') {
                                        input.setAttribute('value', 'NO');
                                    } else {
                                        input.setAttribute('value', 'NO');
                                    }*/

                                    var istrialininput = document.getElementById(isTrial);
                                    istrialininput.setAttribute('value', input.checked ? 'YES' : 'NO');
                                    console.log("Change: " + istrialininput.value);
                                    //alert(input.value);
                                    //return true;
                                }

                                function initiateCheckBox(){
                                    var checkbox = document.getElementById("istrial");
                                    var hidden = document.getElementById("is_trial");
                                    if(checkbox.checked){
                                        //checkbox.checked = false;
                                        //checkbox.setAttribute("value", "off");
                                        hidden.setAttribute("value", "YES");
                                        console.log('Setting to YES: ' + hidden.getAttribute('value'));
                                        //checkbox.click();
                                    }else{
                                        hidden.setAttribute("value", "NO");
                                        console.log('Setting to NO: ' + hidden.getAttribute('value'));
                                    }
                                }
                                initiateCheckBox();
                                console.log( 'Conclusion: ' + document.getElementById('is_trial').value);
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
