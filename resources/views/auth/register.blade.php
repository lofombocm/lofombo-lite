@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        @include('layouts.menu')
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h5>{{ __('Enregistrer un utilisateur') }}</h5></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('enregistrement.post') }}" >
                        <div><h5>{{__('Les champs marqués par ')}} <b class="" style="color: red;">*</b> {{__('sont obligatoires')}}</h5></div>
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
                                {{ __('Nom utilisateur') }}
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
                                {{ __('Email') }}
                                {{--<b class="" style="color: red;">*</b>--}}
                            </label>

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
                            <label for="username" class="col-md-4 col-form-label text-md-end">
                                {{ __('Pseudo') }}
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
                                {{ __('Rôle Administrateur ?') }}
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
                                <a {{--type="submit"--}} class="btn btn-primary" href="#" onclick="loadModal();"
                                   data-bs-toggle="modal" data-bs-target="#confirm-register-user-modal">
                                    {{ __('Enregistrer') }}
                                </a>

                                <div class="modal fade" id="confirm-register-user-modal" data-bs-backdrop="static"
                                     data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                                     aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="staticBackdropLabel">
                                                    {{ __("Confirmez les informations") }}
                                                </h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="list-group list-group-flush alert alert-info"
                                                     id="form-list-group">
                                                    <a href="#" class="list-group-item list-group-item-action"
                                                       style="margin-left: 15px; width: 98%;" id="name-displayer">
                                                    </a>
                                                    <a href="#" class="list-group-item list-group-item-action"
                                                       style="margin-left: 15px; width: 98%;" id="email-displayer">

                                                    </a>
                                                    <a href="#" class="list-group-item list-group-item-action"
                                                       style="margin-left: 15px; width: 98%;" id="username-displayer">

                                                    </a>
                                                    <a href="#" class="list-group-item list-group-item-action"
                                                       style="margin-left: 15px; width: 98%;" id="admin-role-displayer">

                                                    </a>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-danger"
                                                        data-bs-dismiss="modal">Annuler
                                                </button>
                                                <button type="submit" class="btn btn-primary">
                                                    {{__("Confirmer l'Enregistrement")}}
                                                </button>
                                            </div>
                                            {{--</form>--}}
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <script type="text/javascript">
                            function setIsAdmin(input, isAdmin) {


                                var isadmininput = document.getElementById(isAdmin);
                                isadmininput.setAttribute('value', input.checked ? 'on' : 'off');
                                //alert(input.value);
                                //return true;
                            }
                            function initiateCheckBox(){
                                var checkbox = document.getElementById("isadmin");
                                var hidden = document.getElementById("is_admin");
                                if(checkbox.checked){
                                    //checkbox.checked = false;
                                    //checkbox.setAttribute("value", "off");
                                    hidden.setAttribute("value", "on");
                                    //checkbox.click();
                                }else{
                                    hidden.setAttribute("value", "off");
                                }
                            }

                            initiateCheckBox();

                            function loadModal(){
                                var name = document.getElementById('name').value;
                                var email = document.getElementById('email').value;
                                var username = document.getElementById('username').value;
                                var is_admin = document.getElementById('is_admin').value;

                                document.getElementById('name-displayer').innerHTML =
                                    '<h5>{{__("Nom")}}: ' + name + '</h5>';
                                document.getElementById('email-displayer').innerHTML =
                                    '<h5>{{__("Email")}}: ' + email + '</h5>';
                                document.getElementById('username-displayer').innerHTML =
                                    '<h5>{{__("Pseudo")}}: ' + username + '</h5>';
                                if(is_admin === 'off'){
                                    document.getElementById('admin-role-displayer').innerHTML =
                                        '<h5>{{__("Rôle")}}: ' +  '{{__("Simple Utilisateur")}}' + '</h5>';
                                }else{
                                    document.getElementById('admin-role-displayer').innerHTML =
                                        '<h5>{{__("Rôle")}}: ' + '{{__("Administrateur")}}' + '</h5>';
                                }
                            }
                        </script>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
