@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        @include('layouts.menu')
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ 'Enregistrer un collaborateur' }}</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('enregistrement.post') }}" >
                        <div><h5>Les champs marques par <b class="" style="color: red;">*</b> sont obligatoires</h5></div>
                        <br>
                        @csrf
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

                       {{-- @if (session('request'))
                            <div class="alert alert-danger" role="alert">
                                {{ session('request') }}
                            </div>
                        @endif--}}

                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">
                                {{ 'Nom complet' }}
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
                            <label for="email" class="col-md-4 col-form-label text-md-end">
                                {{ 'Adresse Email' }}
                                <b class="" style="color: red;">*</b>
                            </label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="username" class="col-md-4 col-form-label text-md-end">
                                {{ 'Nom d\'utilisateur' }}
                                <b class="" style="color: red;">*</b>
                            </label>

                            <div class="col-md-6">
                                <input id="username" type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" required autocomplete="username" autofocus>

                                @error('username')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        {{--<div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">
                                {{ 'Mot de passe' }}
                                <b class="" style="color: red;">*</b>
                            </label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end">
                                {{ 'Confirmer le mot de passe' }}
                                <b class="" style="color: red;">*</b>
                            </label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>--}}

                        <div class="row mb-3">
                            <label for="isadmin" class="col-md-4 col-form-label text-md-end">
                                {{ 'Utilisateur Administrateur?' }}
                                <b class="" style="color: red;">*</b>
                            </label>

                            <div class="col-md-6">

                                <input id="isadmin" type="checkbox" class="@error('isadmin') is-invalid @enderror" name="isadmin" value="off"  autocomplete="isadmin"
                                style="height: 20px; width: 20px; margin-top: 10px;" onchange="setIsAdmin(this, 'is_admin')">
                                <input name="is_admin" id="is_admin" value="off" type="hidden">
                                @error('isadmin')
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

                        <script type="text/javascript">


                            function setIsAdmin(input, isAdmin) {
                                if(input.value === 'off') {
                                    input.setAttribute('value', 'on');
                                } else {
                                    input.setAttribute('value', 'off');
                                }

                                var isadmininput = document.getElementById(isAdmin);
                                isadmininput.setAttribute('value', input.value);
                                //alert(input.value);
                                //return true;
                            }
                            function initiateCheckBox(){
                                var checkbox = document.getElementById("isadmin");
                                var hidden = document.getElementById("is_admin");
                                if(checkbox.value !== hidden.value){
                                    checkbox.checked = false;
                                    checkbox.setAttribute("value", "off");
                                    hidden.setAttribute("value", "off");
                                    checkbox.click();
                                }
                            }

                            initiateCheckBox();
                        </script>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
