@extends('layouts.app-super_admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        @include('layouts.super-admin-menu')
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h5>{{ __("Mes Paramètres") }}</h5></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('super_admin.update-parameter.index.put', $superadmin->id) }}" enctype="multipart/form-data">
                        @csrf

                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{--{{ session('status') }}--}}
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
                            <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Nom utilisateur') }}</label>
                            <div class="col-md-6">
                                <input id="name"
                                       type="text"
                                       class="form-control @error('name') is-invalid @enderror"
                                       name="name" value="{{$superadmin->name}}" required autocomplete="name" autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email') }}</label>

                            <div class="col-md-6">
                                <input id="email"
                                       type="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       name="email"
                                       value="{{ $superadmin->email }}" required autocomplete="email">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="telephone" class="col-md-4 col-form-label text-md-end">{{ __("Téléphone") }}</label>

                            <div class="col-md-6">
                                <input id="telephone"
                                       type="tel"
                                       class="form-control @error('telephone') is-invalid @enderror"
                                       name="telephone"
                                       value="{{ $superadmin->telephone }}" required autocomplete="telephone">

                                @error('telephone')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __("Modifier") }}
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
                            //
                        </script>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
