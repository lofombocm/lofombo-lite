@php
    use App\Models\Client;
    use Illuminate\Support\Carbon;
    use App\Http\Controllers\GuestController;
@endphp
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">

            @include('layouts.menu')

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">
                        <h5 style="display: inline;"><strong>{{ __("Bons du système") }}</strong></h5>
                    </div>
                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success alert-dismissible" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible" role="alert">
                                {{ session('error') }}
                            </div>
                        @endif

                        @if(count($vouchers) > 0)
                            <table class="table table-striped table-responsive table-bordered">
                                <thead class="" style="color: darkred;">
                                <th scope="col">
                                    {{ __("N° Série") }}
                                </th>
                                <th scope="col">
                                    {{ __("Client") }}
                                </th>
                                <th scope="col">
                                    {{ __("Type") }}
                                </th>
                                <th scope="col">
                                    {{ __('Points') }}
                                </th>
                                <th scope="col">
                                    {{ __('Expiration') }}
                                </th>
                                <th scope="col">
                                    {{ __("Statut") }}
                                </th>
                                <th scope="col">
                                    {{ __('Actions') }}
                                </th>
                                </thead>
                                <tbody>
                                @foreach($vouchers as $voucher)
                                        <?php
                                        $client = Client::where('id', $voucher->clientid)->first();
                                        //$type = $voucher->level === 'CLASSIC' ? 'alert-secondary' : ($voucher->level === 'PREMIUM' ? 'alert-success' : 'alert-warning');
                                        $type = 'alert-info';
                                        $validite = 'Valide';
                                        $expirationdate = Carbon::parse($voucher->expirationdate);
                                        $expired = false;
                                        $statut = '';
                                        if ($voucher->active){
                                            if ($voucher->is_used){
                                                $statut = __('UTILISE');
                                            }else{
                                                $statut = __('ACTIVE');
                                            }
                                        }else{
                                            if ($expirationdate->isBefore(Carbon::now())) {
                                                $statut = __('EXPIRE');
                                            }else{
                                                $statut = __('GENERE');
                                            }
                                        }

                                        if ($expirationdate->isBefore(Carbon::now())) {
                                            $validite = __('Invalide');
                                            $expired = true;
                                        }
                                        //$client = Client::where('id', $voucher->clientid)->first();
                                        //$reward = Reward::where('id', $voucher->reward)->first();
                                        ?>
                                    <tr >
                                        <th>
                                            <h5>{{$voucher->serialnumber}}</h5>
                                        </th>
                                        <td>
                                            <h5>{{$client->name}}</h5>
                                        </td>
                                        <td>
                                            <h5>{{$voucher->level}}</h5>
                                        </td>
                                        <td>
                                            <h5>{{$voucher->point}}</h5>
                                        </td>
                                        <td>
                                            <h5>{{$expirationdate->format('d-m-Y H:i:s')}}</h5>
                                        </td>
                                        <td>
                                            <h5>{{$statut}}</h5>
                                        </td>
                                        <td>
                                            <div  >
                                                <span style="float: right;">
                                                    @if($voucher->active === true)
                                                        @if($voucher->is_used)
                                                            <span class="position-relative top-0 start-100
                                                                                 translate-middle p-2 rounded-pill
                                                                                 bg-dark border border-light
                                                                                 rounded-circle badge">
                                                                    </span>
                                                        @else
                                                            <span class="position-relative top-0 start-100
                                                                                 translate-middle p-2 rounded-pill
                                                                                 bg-success border border-light
                                                                                 rounded-circle badge">

                                                                    </span>
                                                        @endif

                                                    @else

                                                        <span class="position-relative top-0 start-100
                                                                                 translate-middle p-2 rounded-pill
                                                                                 bg-danger border border-light
                                                                                 rounded-circle badge">

                                                        </span>
                                                    @endif
                                                </span>

                                                @if(!$voucher->active && !$voucher->is_used && !$expired)
                                                    <a class="btn btn-sm btn-link" href="#" data-bs-toggle="modal"
                                                       data-bs-target="#confirm-activate-voucher-modal" style="text-decoration: none;">
                                                        <b style="color: limegreen;">{{ __('Activer')}}</b>
                                                    </a>
                                                    <div class="modal fade" id="confirm-activate-voucher-modal"
                                                         data-bs-backdrop="static"
                                                         data-bs-keyboard="false" tabindex="-1"
                                                         aria-labelledby="staticBackdropLabel"
                                                         aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">
                                                                        {{__("Confirmez l'activation du bon")}}
                                                                        <strong
                                                                            style="color: darkred;">{{$voucher->serialnumber}}</strong>
                                                                    </h1>
                                                                    <button type="button" class="btn-close"
                                                                            data-bs-dismiss="modal"
                                                                            aria-label="Close"></button>
                                                                </div>
                                                                <form method="POST"
                                                                      action="{{url('/'.GuestController::getApplicationLocal().'/client/' . $client->id . '/vouchers/' . $voucher->id . '/activate')}}"
                                                                      onsubmit="return true;">
                                                                    <div class="modal-body">

                                                                        <input type="hidden" name="error" id="error"
                                                                               class="form-control @error('error') is-invalid @enderror">
                                                                        @error('error')
                                                                        <span class="invalid-feedback" role="alert"
                                                                              style="position: relative; width: 100%; text-align: center;">
                                                                    <strong>{{ $message }}</strong>
                                                                </span> <br/>
                                                                        @enderror

                                                                        @csrf

                                                                        <input type="hidden" name="clientid"
                                                                               value="{{$client->id}}">

                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-danger"
                                                                                data-bs-dismiss="modal"> {{__("Annuler")}}
                                                                        </button>
                                                                        <button type="submit" class="btn btn-success">
                                                                            {{__("Activer")}}
                                                                        </button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    @if(!$voucher->is_used && !$expired)

                                                        <a class="btn btn-sm btn-link" href="#" data-bs-toggle="modal"
                                                           data-bs-target="#confirm-deactivate-voucher-modal"
                                                           style="text-decoration: none;">
                                                            <b style="color: red;">{{ __("Désactiver") }}</b>
                                                        </a>
                                                        <div class="modal fade" id="confirm-deactivate-voucher-modal"
                                                             data-bs-backdrop="static"
                                                             data-bs-keyboard="false" tabindex="-1"
                                                             aria-labelledby="staticBackdropLabel"
                                                             aria-hidden="true">
                                                            <div
                                                                class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h1 class="modal-title fs-5" id="staticBackdropLabel">
                                                                            {{ __("Confirmez la désactivation du bon") }}
                                                                             <strong
                                                                                style="color: darkred;">{{$voucher->serialnumber}}</strong>
                                                                        </h1>
                                                                        <button type="button" class="btn-close"
                                                                                data-bs-dismiss="modal"
                                                                                aria-label="Close"></button>
                                                                    </div>
                                                                    <form method="POST"
                                                                          action="{{url('/'.GuestController::getApplicationLocal().'/client/' . $client->id . '/vouchers/' . $voucher->id . '/deactivate')}}"
                                                                          onsubmit="return true;">
                                                                        <div class="modal-body">
                                                                            <input type="hidden" name="error" id="error"
                                                                                   class="form-control @error('error') is-invalid @enderror">
                                                                            @error('error')
                                                                            <span class="invalid-feedback" role="alert"
                                                                                  style="position: relative; width: 100%; text-align: center;">
                                                                    <strong>{{ $message }}</strong>
                                                                </span> <br/>
                                                                            @enderror
                                                                            @csrf
                                                                            <input type="hidden" name="clientid"
                                                                                   value="{{$client->id}}">
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-danger"
                                                                                    data-bs-dismiss="modal">Annuler
                                                                            </button>
                                                                            <button type="submit" class="btn btn-success">
                                                                                {{__("Désactiver")}}
                                                                            </button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <a class="btn btn-sm btn-link" href="#" data-bs-toggle="modal"
                                                           data-bs-target="#confirm-use-voucher-modal" style="text-decoration: none;">
                                                            <b style="color: blue;">{{__('Utiliser')}}</b>
                                                        </a>
                                                        <div class="modal fade" id="confirm-use-voucher-modal"
                                                             data-bs-backdrop="static"
                                                             data-bs-keyboard="false" tabindex="-1"
                                                             aria-labelledby="staticBackdropLabel"
                                                             aria-hidden="true">
                                                            <div
                                                                class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title fs-5" id="staticBackdropLabel">
                                                                            {{__("Confirmez l'utilisation du bon")}}
                                                                             <strong
                                                                                style="color: darkgreen;">{{$voucher->serialnumber}}</strong>
                                                                        </h5>
                                                                        <button type="button" class="btn-close"
                                                                                data-bs-dismiss="modal"
                                                                                aria-label="Close"></button>
                                                                    </div>
                                                                    <form method="POST"
                                                                          action="{{url('/'.GuestController::getApplicationLocal().'/client/' . $client->id . '/vouchers/' . $voucher->id . '/use')}}"
                                                                          onsubmit="return true;">
                                                                        <div class="modal-body">

                                                                            <input type="hidden" name="error" id="error"
                                                                                   class="form-control @error('error') is-invalid @enderror">
                                                                            @error('error')
                                                                            <span class="invalid-feedback" role="alert"
                                                                                  style="position: relative; width: 100%; text-align: center;">
                                                                                        <strong>{{ $message }}</strong>
                                                                                    </span> <br/>
                                                                            @enderror

                                                                            @csrf

                                                                            <input type="hidden" id="userid" name="userid" value="{{\Illuminate\Support\Facades\Auth::user()->id}}">
                                                                            <input type="hidden" name="clientid"
                                                                                   value="{{$client->id}}">
                                                                            <h6><strong style="color: darkred;">
                                                                                    {{__("Après cette étape de confirmation, Aucun retour n'est possible pour l'utilisation du Bon. Rassurez vous auprès du client qu'il souhaite vraiment utiliser son BON")}}
                                                                                </strong></h6>

                                                                            <div class="row mb-3">
                                                                                <label for="code"
                                                                                       class="col-md-4 col-form-label text-md-end">{{ __("Code d'utilisation") }}
                                                                                    <b class=""
                                                                                       style="color: red;">*</b>
                                                                                </label>

                                                                                <div class="col-md-8">
                                                                                    <input id="code" type="text"
                                                                                           class="form-control @error('code') is-invalid @enderror"
                                                                                           name="code"
                                                                                           value="{{ old('code') }}"
                                                                                           required autocomplete="code"
                                                                                           autofocus
                                                                                           onkeyup="contolInputCharaters(this);"
                                                                                    >

                                                                                    @error('code')
                                                                                    <span class="invalid-feedback"
                                                                                          role="alert">
                                                                                            <strong>{{ $message }}</strong>
                                                                                        </span>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>

                                                                            {{--@if(count($rewards) > 0)--}}
                                                                            <div class="row mb-3">
                                                                                <label for="code"
                                                                                       class="col-md-4 col-form-label text-md-end">{{ __('Récompense') }}
                                                                                    {{--<b class="" style="color: red;">*</b>--}}
                                                                                </label>

                                                                                <div class="col-md-8">
                                                                                    <select id="reward"
                                                                                            class="form-control @error('reward') is-invalid @enderror"
                                                                                            name="reward"
                                                                                            autocomplete="reward"
                                                                                            autofocus>
                                                                                        <option value="">-- {{__("Sélectionnez ici")}} --</option>
                                                                                        @foreach($rewards as $reward)
                                                                                            <option
                                                                                                value="{{$reward->id}}">{{$reward->name}}</option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                    <span
                                                                                        style="float: right; color: green;">
                                                                                                <small><a href="#"
                                                                                                          onclick="toggleRewardForm();"
                                                                                                          style="text-decoration: none; font-size: small; color: green;"
                                                                                                          id="add_level_field">
                                                                                                    <strong><span
                                                                                                            class="glyphicon glyphicon-plus">+</span></strong>
                                                                                                    &nbsp;{{ __('Ajouter une récompense') }}
                                                                                                </a><br>
                                                                                                    <span id="add_reward_result"></span>
                                                                                                </small>
                                                                                            </span>

                                                                                    @error('reward')
                                                                                    <span class="invalid-feedback"
                                                                                          role="alert">
                                                                                                    <strong>{{ $message }}</strong>
                                                                                                </span>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>

                                                                            <fieldset id="reward_form" style="display: none; border: 1px darkblue solid; border-radius: 5px;">
                                                                                <legend><small style="font-size: small;">{{__("Ajout de récompense")}}</small></legend>
                                                                                <div class="row mb-3">
                                                                                    <label for="name"
                                                                                           class="col-md-5 col-form-label text-md-end">{{ __('Nom') }}
                                                                                        <b class=""
                                                                                           style="color: red;">*</b></label>

                                                                                    <div class="col-md-7">
                                                                                        <input id="name" type="text"
                                                                                               class="form-control @error('name') is-invalid @enderror"
                                                                                               name="name"
                                                                                               value="{{ old('name') }}"
                                                                                               autocomplete="name"
                                                                                               autofocus
                                                                                               placeholder="{{__("")}}">
                                                                                        @error('name')
                                                                                        <span
                                                                                            class="invalid-feedback"
                                                                                            role="alert">
                                                                                                        <strong>{{ $message }}</strong>
                                                                                                    </span>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>

                                                                                <div class="row mb-3">
                                                                                    <label for="nature"
                                                                                           class="col-md-5 col-form-label text-md-end">{{ __('Nature') }}
                                                                                        <b class=""
                                                                                           style="color: red;">*</b></label>

                                                                                    <div class="col-md-7">
                                                                                        <select id="nature"
                                                                                                type="text"
                                                                                                class="form-control @error('nature') is-invalid @enderror"
                                                                                                name="nature"
                                                                                                autofocus>
                                                                                            <option
                                                                                                value="">-- {{__('Sélectionnez ici')}} --</option>
                                                                                            <option
                                                                                                value="{{ 'MATERIAL' }}">{{ __('Materiel') }}</option>
                                                                                            <option
                                                                                                value="{{ 'FINANCIAL' }}">{{__('Financière')}}</option>
                                                                                            <option
                                                                                                value="{{ 'SERVICE' }}">{{__("Service")}}</option>
                                                                                        </select>
                                                                                        @error('nature')
                                                                                        <span
                                                                                            class="invalid-feedback"
                                                                                            role="alert">
                                                                                                        <strong>{{ $message }}</strong>
                                                                                                    </span>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>

                                                                                <div class="row mb-3">
                                                                                    <label for="value"
                                                                                           class="col-md-5 col-form-label text-md-end">{{ __('Valeur financière') }}
                                                                                        <b class=""
                                                                                           style="color: red;">*</b></label>

                                                                                    <div class="col-md-7">
                                                                                        <input id="value"
                                                                                               type="number"
                                                                                               class="form-control @error('value') is-invalid @enderror"
                                                                                               name="value"
                                                                                               value="{{ old('value') }}"
                                                                                               autocomplete="value"
                                                                                               autofocus
                                                                                               placeholder="{{__("Valeur financière")}}">
                                                                                        @error('value')
                                                                                        <span
                                                                                            class="invalid-feedback"
                                                                                            role="alert">
                                                                                                        <strong>{{ $message }}</strong>
                                                                                                    </span>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>

                                                                                <div class="row mb-3">
                                                                                    <label for="level"
                                                                                           class="col-md-5 col-form-label text-md-end">{{ __('Type de bon') }}
                                                                                        <b class=""
                                                                                           style="color: red;">*</b></label>
                                                                                    <div class="col-md-7">
                                                                                        @php
                                                                                            //$config = Config::where('is_applicable', true)->first();
                                                                                            $levels = json_decode($config->levels, true);
                                                                                        @endphp
                                                                                        <select id="level"
                                                                                                type="text"
                                                                                                class="form-control @error('level') is-invalid @enderror"
                                                                                                name="level">
                                                                                            <option>{{__("Sélectionnez ici")}}</option>
                                                                                            @foreach($levels as $level)
                                                                                                <option
                                                                                                    value="{{$level['name']}}">{{$level['name']}}</option>
                                                                                            @endforeach
                                                                                        </select>
                                                                                        @error('level')
                                                                                        <span
                                                                                            class="invalid-feedback"
                                                                                            role="alert">
                                                                                                        <strong>{{ $message }}</strong>
                                                                                                    </span>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>

                                                                                <div class="row mb-0">
                                                                                    <div
                                                                                        class="col-md-6 offset-md-5">
                                                                                        <a class="btn btn-link"
                                                                                           href="#save-reword"
                                                                                           id="save-reword" onclick="postRewardForm();"
                                                                                           style="text-decoration: none; font-size: large;">
                                                                                            {{ __('Enregistrer') }}
                                                                                        </a>
                                                                                        <span style="display: none;" id="loader"
                                                                                              class="spinner-grow text-info"
                                                                                              role="status">
                                                                                                <span class="sr-only"></span>
                                                                                            </span>
                                                                                    </div>
                                                                                </div>
                                                                                <span style="display: none" id="alert_length_code_error">
                                                                                    {{__("Le code a exactement 8 caractères. L'insertion ou l'omission du caractère '-' n'a pas d'effet.")}}
                                                                                </span>
                                                                            </fieldset>

                                                                            {{--@endif--}}
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-danger"
                                                                                    data-bs-dismiss="modal">{{__("Annuler")}}

                                                                            </button>
                                                                            <button type="submit"
                                                                                    class="btn btn-success">
                                                                                {{__("Confirmez l'utilisation du bon")}}
                                                                            </button>
                                                                        </div>
                                                                        <script type="text/javascript">

                                                                            function contolInputCharaters(theInput){
                                                                                if(theInput.value.length <= 4){
                                                                                    theInput.value = theInput.value.replace(/\D/g, '');
                                                                                }
                                                                                if(theInput.value.length === 5){
                                                                                    theInput.value = theInput.value.substring(0, 4).replace(/\D/g, '') + "-";
                                                                                }
                                                                                if(theInput.value.length > 5){
                                                                                    theInput.value = theInput.value.substring(0, 4).replace(/\D/g, '') + "-" + theInput.value.substring(5, 9).replace(/\D/g, '') ;
                                                                                }
                                                                            }

                                                                            function validateCode() {
                                                                                var codeElem = document.getElementById('code');
                                                                                var code = codeElem.value;
                                                                                var codeArray = code.split('-');
                                                                                var codestr = '';
                                                                                if (codeArray.length > 1) {
                                                                                    codestr = codeArray[0] + codeArray[1];
                                                                                } else {
                                                                                    codestr = codeArray[0];
                                                                                }
                                                                                var lengthiHeigt = codestr.length === 8;
                                                                                if (lengthiHeigt === false) {
                                                                                    alert(document.getElementById("alert_length_code_error").innerHTML);
                                                                                    return false;
                                                                                }
                                                                                return true;
                                                                            }

                                                                            function toggleRewardForm(){
                                                                                var rewardForm = document.getElementById('reward_form');
                                                                                if(rewardForm.style.display === 'none'){
                                                                                    rewardForm.setAttribute('style', 'display:block');
                                                                                }else{
                                                                                    rewardForm.setAttribute('style', 'display:none');
                                                                                }
                                                                            }

                                                                            function postRewardForm(){
                                                                                var saveReward = document.getElementById('save-reword');
                                                                                saveReward.setAttribute('style', 'display:none');
                                                                                var loader = document.getElementById('loader');
                                                                                loader.setAttribute('style', 'display:block');
                                                                                ///api/rewards
                                                                                var baseUrl = window.location.origin;
                                                                                console.log('baseUrl' + baseUrl);
                                                                                var url = baseUrl + '/api/rewards'
                                                                                console.log('URL: ' + url);

                                                                                var data = {
                                                                                    name: document.getElementById('name').value,
                                                                                    nature: document.getElementById('nature').value,
                                                                                    value: document.getElementById('value').value,
                                                                                    level: document.getElementById('level').value,
                                                                                    userid: document.getElementById('userid').value
                                                                                };

                                                                                const jsonData = JSON.stringify(data);


                                                                                fetch(url, {
                                                                                    method: 'POST',
                                                                                    headers: {
                                                                                        'Content-Type': 'application/json',
                                                                                    },
                                                                                    body: jsonData,
                                                                                })
                                                                                    .then(response => {
                                                                                        if (!response.ok) {
                                                                                            throw new Error(`HTTP error! status: ${response.status}`);
                                                                                        }

                                                                                        saveReward.setAttribute('style', 'display:block');
                                                                                        loader.setAttribute('style', 'display:nne');
                                                                                        return response.json();
                                                                                    })
                                                                                    .then(data => {
                                                                                        console.log('Success:', data);
                                                                                        if(data.error === 1){
                                                                                            console.error('Error:', data.errorMessage);
                                                                                            saveReward.setAttribute('style', 'display:block');
                                                                                            loader.setAttribute('style', 'display:none');

                                                                                            toggleRewardForm();
                                                                                            var add_reward_result = document.getElementById('add_reward_result');
                                                                                            add_reward_result.innerHTML = error.message;
                                                                                            add_reward_result.setAttribute('style', 'color:darkred;');

                                                                                        }else{
                                                                                            toggleRewardForm();
                                                                                            //document.getElementById('reward_form').setAttribute('display', 'none');
                                                                                            var add_reward_result = document.getElementById('add_reward_result');
                                                                                            add_reward_result.innerHTML = data.successMessage;
                                                                                            add_reward_result.setAttribute('style', 'color:darkgreen;');
                                                                                            const result = data.result;
                                                                                            saveReward.setAttribute('style', 'display:block');
                                                                                            loader.setAttribute('style', 'display:none');
                                                                                            var selectReward = document.getElementById('reward');
                                                                                            //reward
                                                                                            var option = document.createElement('option');
                                                                                            option.value = result.id;
                                                                                            option.text = result.name;
                                                                                            selectReward.appendChild(option);
                                                                                        }

                                                                                    })
                                                                                    .catch(error => {
                                                                                        console.error('Error:', error);
                                                                                        saveReward.setAttribute('style', 'display:block');
                                                                                        loader.setAttribute('style', 'display:none');

                                                                                        document.getElementById('reward_form').setAttribute('display', 'none');
                                                                                        var add_reward_result = document.getElementById('add_reward_result');
                                                                                        add_reward_result.innerHTML = error.message;
                                                                                        add_reward_result.setAttribute('style', 'color:darkred;');
                                                                                    });

                                                                            }
                                                                        </script>
                                                                    </form>
                                                                    {{--<form method="POST"
                                                                          action="{{url('/client/' . $client->id . '/vouchers/' . $voucher->id . '/use')}}"
                                                                          onsubmit="return validateCode();">
                                                                        <div class="modal-body">

                                                                            <input type="hidden" name="error" id="error"
                                                                                   class="form-control @error('error') is-invalid @enderror">
                                                                            @error('error')
                                                                                <span class="invalid-feedback" role="alert"
                                                                                      style="position: relative; width: 100%; text-align: center;">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span> <br/>
                                                                            @enderror

                                                                            @csrf

                                                                            <input type="hidden" name="clientid"
                                                                                   value="{{$client->id}}">
                                                                            <h5><strong style="color: darkred;">
                                                                                {{__("Après cette étape de confirmation, Aucun retour n'est possible pour l'utilisation du Bon. Rassurez vous auprès du client qu'il souhaite vraiment utiliser son BON")}}
                                                                                </strong></h5>


                                                                            <div class="row mb-3">
                                                                                <label for="code" class="col-md-4 col-form-label text-md-end">{{ __("Code d'utilisation") }}
                                                                                    <b class="" style="color: red;">*</b>
                                                                                </label>

                                                                                <div class="col-md-6">
                                                                                    <input id="code" type="text" class="form-control @error('code') is-invalid @enderror"
                                                                                           name="code" value="{{ old('code') }}" required autocomplete="code" autofocus>

                                                                                    @error('code')
                                                                                        <span class="invalid-feedback" role="alert">
                                                                                            <strong>{{ $message }}</strong>
                                                                                        </span>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>

                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-danger"
                                                                                    data-bs-dismiss="modal">Annuler
                                                                            </button>
                                                                            <button type="submit" class="btn btn-success">
                                                                                {{__("Confirmer")}}
                                                                            </button>
                                                                        </div>
                                                                        <script type="text/javascript">
                                                                            function validateCode() {
                                                                                var codeElem = document.getElementById('code');
                                                                                var code = codeElem.value;
                                                                                var codeArray = code.split('-');
                                                                                var codestr = '';
                                                                                if (codeArray.length > 1) {
                                                                                    codestr = codeArray[0] + codeArray[1];
                                                                                }else{
                                                                                    codestr = codeArray[0];
                                                                                }
                                                                                var lengthiHeigt = codestr.length === 8;
                                                                                if (lengthiHeigt === false) {
                                                                                    alert('Le code a exactement 8 caracteres. L\'insertion ou l\'omission du caractere "-" n\'a pas d\'effet.');
                                                                                    return false;
                                                                                }
                                                                                return true;
                                                                            }
                                                                        </script>
                                                                    </form>--}}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        {{--<div>{{'Rien a faire'}}</div>--}}
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                                {{--<tfoot>Pied de table</tfoot>--}}
                            </table>
                        @else
                            <h5>{{__("Aucun bon trouvé")}}</h5>
                        @endif

                    </div>

                    {{--<div class="card-footer">
                        {{' '}}
                    </div>--}}
                </div>
            </div>
        </div>
    </div>

@endsection
