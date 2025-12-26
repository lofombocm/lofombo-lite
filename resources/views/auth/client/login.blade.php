@extends('layouts.app-client')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Connexion') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('authentification.client.post') }}">
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
                        <input type="hidden" name="error" id="error" class="form-control @error('error') is-invalid @enderror">
                        {{--@error('error')
                        <span class="invalid-feedback" role="alert" style="position: relative; width: 100%; text-align: center;">
                                        <strong>{{ $message }}</strong>
                                    </span> <br/>
                        @enderror--}}
                        @csrf

                        <div class="row mb-3">
                            <label for="telephone" class="col-md-4 col-form-label text-md-end">{{ __('Numéro Mobile') }}</label>
                            <div class="col-md-6">
                                <input id="telephone" type="tel"
                                       class="form-control @error('telephone') is-invalid @enderror" name="telephone"
                                       value="{{ old('telephone') }}" required autocomplete="telephone"
                                       placeholder="{{__("Exemple: ")}} +237691179154"
                                       onkeyup="removeNonNumericCharaters(this);"
                                       autofocus>

                                @error('telephone')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Mot de passe') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>


                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Connexion') }}
                                </button>

                               @if (Route::has('client.password.forgot'))
                                    <a class="btn btn-link" href="{{ route('client.password.forgot') }}">
                                        {{ __('Mot de passe oublié ?') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                    <script type="text/javascript">
                        function removeNonNumericCharaters(theInput){
                            theInput.value = "+" + theInput.value.replace(/\D/g, '');
                        }
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
