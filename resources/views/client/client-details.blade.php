@php use App\Http\Controllers\Reward\RewardController; use App\Models\Voucher; use \App\Models\Transactiontype;use Illuminate\Support\Carbon; @endphp
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">

            @include('layouts.menu')

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">
                        <h5 style="display: inline;"><strong>{{ 'Details du client: ' . $client->name }}</strong></h5>
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

                        <div class="list-group list-group-flush alert alert-{{($client->active)?'success':'danger'}}">
                            <a href="#" class="list-group-item list-group-item-action"
                               style="margin-left: 15px; width: 98%;">
                                <h5>
                                    Nom: &nbsp; &nbsp; {{$client->name}}
                                    <strong style="display: inline; position: relative; float:right; color: darkred;">
                                        {{ 'Solde: ' }} {{$loyaltyAccount->point_balance}}
                                        {{'Points  (' . $loyaltyAccount->amount_balance . ' ' . $loyaltyAccount->currency_name . ')'}}
                                    </strong>
                                </h5>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action"
                               style="margin-left: 15px; width: 98%;">
                                <h5>
                                    Telephone: &nbsp; &nbsp; {{$client->telephone}}
                                </h5>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action"
                               style="margin-left: 15px; width: 98%;">
                                <h5>
                                    Email: &nbsp;
                                    &nbsp; {{(isset($client->email) or !empty($client->email)) ? $client->email : "N/D"}}
                                </h5>
                            </a>

                            <a href="#" class="list-group-item list-group-item-action"
                               style="margin-left: 15px; width: 98%;">
                                <h5>
                                    Date de Naissance: &nbsp;
                                    <?php
                                      $a = '00';
                                      $m = '00';
                                      $j = '00';
                                      $dateNaissance = '';
                                      if ((isset($client->birthdate) or !empty($client->birthdate))){
                                          $ymd = explode('-', $client->birthdate);
                                          $a = $ymd[0];
                                          $m = $ymd[1];
                                          $j = $ymd[2];
                                          $dateNaissance = $j . '-' . $m . '-' . $a;
                                      }
                                    ?>
                                    &nbsp;{{ ($a !== '00' && $m !== '00' && $j !== '00') ? $dateNaissance : "N/D"}}
                                </h5>
                            </a>

                            <a href="#" class="list-group-item list-group-item-action"
                               style="margin-left: 15px; width: 98%;">
                                <h5>
                                    Civilite: &nbsp;
                                    &nbsp;{{(isset($client->gender) or !empty($client->gender)) ? $client->gender : "N/D"}}
                                </h5>
                            </a>

                            <a href="#" class="list-group-item list-group-item-action"
                               style="margin-left: 15px; width: 98%;">
                                <h5>
                                    Ville: &nbsp;
                                    &nbsp;{{(isset($client->city) or !empty($client->city)) ? $client->city : "N/D"}}
                                </h5>
                            </a>

                            <a href="#" class="list-group-item list-group-item-action"
                               style="margin-left: 15px; width: 98%;">
                                <h5>
                                    Quarter: &nbsp;
                                    &nbsp;{{(isset($client->quarter) or !empty($client->quarter)) ? $client->quarter : "N/D"}}
                                </h5>
                            </a>

                            <a href="#" class="list-group-item list-group-item-action"
                               style="margin-left: 15px; width: 98%;">
                                <h5>
                                    Enregistre par: &nbsp; &nbsp;{{ $user->name }}
                                </h5>
                            </a>
                        </div>

                        <div class="alert alert-{{($client->active)?'success':'danger'}}"
                             style="padding-left: 10px; padding-right: 10px;">
                            @if($client->active)
                                @if($loyaltyAccount->point_balance >= $threshold->classic_threshold)
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                            data-bs-target="#generate-voucher-modal">
                                        {{ 'Generer un bon' }}
                                    </button>
                                    <!-- Modal -->
                                    <div class="modal fade" id="generate-voucher-modal" data-bs-backdrop="static"
                                         data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                                         aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                                            <div class="modal-content">
                                                    <?php

                                                    $bestRewardAndConversion = RewardController::getBestRewards($loyaltyAccount->point_balance);
                                                    $bestReward = null;
                                                    $conversionUsed = null;

                                                    if ($bestRewardAndConversion === null) {
                                                        $bestReward = null;
                                                        $conversionUsed = null;
                                                    } else {
                                                        $bestReward = $bestRewardAndConversion['bestreward'];
                                                        $conversionUsed = $bestRewardAndConversion['conversionused'];
                                                    }

                                                    ?>
                                                <div class="modal-header">
                                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Point cummule:
                                                        <strong
                                                            style="color: darkred;">{{$loyaltyAccount->point_balance}}
                                                            points</strong></h1>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                </div>
                                                <div class="alert alert-light">
                                                    <div class="alert alert-info">
                                                        <ul>
                                                            <li>
                                                                <h6>De {{$threshold->classic_threshold}}
                                                                    a {{$threshold->premium_threshold}} points, vous
                                                                    gagnez un Bon de type <strong
                                                                        style="color: #495057;">Classique</strong></h6>
                                                            </li>
                                                            <li>
                                                                <h6>De {{$threshold->premium_threshold}}
                                                                    a {{$threshold->gold_threshold}} points, vous gagnez
                                                                    un Bon de type <strong style="color: #198754;">Premium</strong>
                                                                </h6>
                                                            </li>
                                                            <li>
                                                                <h6>Au dela de {{$threshold->gold_threshold}} points,
                                                                    vous gagnez un Bon de type <strong
                                                                        style="color: darkgoldenrod;">Gold</strong></h6>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>

                                                @if(!($bestReward === null))
                                                        <?php
                                                        $type = '';
                                                        $points = $conversionUsed->min_point;
                                                        if ($loyaltyAccount->point_balance >= $threshold->gold_threshold) {
                                                            $type = 'GOLD';
                                                        } else if ($loyaltyAccount->point_balance >= $threshold->premium_threshold) {
                                                            $type = 'PREMIUM';
                                                        } else {
                                                            $type = 'CLASSIC';
                                                        }

                                                        ?>
                                                    <div class="alert alert-light" style="margin-top: -20px;">
                                                        <div class="alert alert-primary">
                                                            <h5>
                                                                vous pouvez generer un bon de type
                                                                <strong>{{$type}}</strong>
                                                                vous donnant droit a :
                                                                <strong>{{$bestReward->name}}</strong>
                                                                ayant une valeur de
                                                                <strong>{{$bestReward->value}}</strong>
                                                            </h5>
                                                        </div>
                                                    </div>
                                                    <form method="POST" action="{{route('vouchers.post')}}"
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

                                                            <div class="row mb-3">
                                                                @if(count(Transactiontype::where('code', 'TRANSACTIONTYPE_GEN_VOUCHER')->where('signe', -1)->where('active', true)->get()) === 1)
                                                                        <?php
                                                                        $transactiontype = Transactiontype::where('code', 'TRANSACTIONTYPE_GEN_VOUCHER')->where('signe', -1)->where('active', true)->first();
                                                                        ?>
                                                                    <input type="hidden" name="transactiontypeid"
                                                                           value="{{$transactiontype->id}}">
                                                                    {{--<label for="transactiontype" class="col-md-4 col-form-label text-md-end">{{ 'Type de transaction' }}</label>
                                                                    <div class="col-md-6">
                                                                        <select id="transactiontype" class="form-control form-select form-select-lg @error('transactiontype') is-invalid @enderror" name="transactiontype" >
                                                                            <option value="">Choisissez ici</option>
                                                                            <option value="GOLD">GOLD</option>
                                                                            <option value="PREMIUM">PREMIUM</option>
                                                                            <option value="CLASSIC">CLASSIC</option>
                                                                        </select>

                                                                        @error('level')
                                                                        <span class="invalid-feedback" role="alert">
                                                                             <strong>{{ $message }}</strong>
                                                                         </span>
                                                                        @enderror
                                                                    </div>--}}
                                                                @endif


                                                                <input type="hidden" name="rewardid" id="rewardid"
                                                                       value="{{$bestReward->id}}">
                                                                <input type="hidden" name="clientid" id="clientid"
                                                                       value="{{$client->id}}">
                                                                <input type="hidden" name="conversionpointrewardid"
                                                                       id="conversion_point_reward"
                                                                       value="{{$conversionUsed->id}}">
                                                                <input type="hidden" name="thresholdid"
                                                                       value="{{$threshold->id}}">
                                                                <input type="hidden" name="level" value="{{$type}}">
                                                                {{--<input type="hidden" name="points" value="{{$points}}">--}}
                                                                {{--<input type="hidden" name="clientid" id="clientid" value="{{$client->id}}">--}}


                                                                {{-- --}}
                                                            </div>

                                                            {{--<div class="row mb-3">
                                                                <label for="montant" class="col-md-4 col-form-label text-md-end">{{ 'Montant'}}</label>

                                                                <div class="col-md-6">
                                                                    <input id="montant" type="range" class="@error('montant') is-invalid @enderror" name="montant"
                                                                           value="{{env('GOLD_THRESHOLD')}}" required autocomplete="montant" autofocus
                                                                           min="{{env('GOLD_THRESHOLD')}}" max="{{$loyaltyAccount->amount_from_converted_point}}"
                                                                           onchange="document.getElementById('montantg').innerHTML = this.value" ><small id="montantg">{{env('GOLD_THRESHOLD')}}</small>

                                                                    @error('montant')
                                                                    <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                                    @enderror
                                                                </div>
                                                            </div>--}}


                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-danger"
                                                                    data-bs-dismiss="modal">Annuler
                                                            </button>
                                                            <button type="submit" class="btn btn-success">Generer
                                                            </button>
                                                        </div>
                                                    </form>

                                                @else
                                                    <div>Aucune meilleur recompense trouvee</div>
                                                @endif


                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#confirm-deactivate-client-modal">
                                    {{ 'Bloquer le client' }}
                                </button>

                                <div class="modal fade" id="confirm-deactivate-client-modal" data-bs-backdrop="static"
                                     data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                                     aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="staticBackdropLabel">Vous souhaitez
                                                    desactiver le client <strong
                                                        style="color: darkred;">{{$client->name}}</strong></h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                            </div>
                                            <form method="POST"
                                                  action="{{url('/client/' . $client->id . '/deactivate')}}"
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

                                                    <input type="hidden" name="clientid" value="{{$client->id}}">

                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-danger"
                                                            data-bs-dismiss="modal">Annuler
                                                    </button>
                                                    <button type="submit" class="btn btn-success">Bloquer le client
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#confirm-update-client-modal">
                                    {{ 'Modifier Client' }}
                                </button>

                                <div class="modal fade" id="confirm-update-client-modal" data-bs-backdrop="static"
                                     data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                                     aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="staticBackdropLabel">Vous souhaitez
                                                    activer le client <strong
                                                        style="color: darkred;">{{$client->name}}</strong></h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                            </div>
                                            <form method="POST" action="{{url('/client/' . $client->id . '/update')}}"
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
                                                    <input type="hidden" name="clientid" value="{{$client->id}}">


                                                    <div class="row mb-3">
                                                        <label for="name"
                                                               class="col-md-4 col-form-label text-md-end">{{ 'Nom du Client' }}
                                                            <b class="" style="color: red;">*</b>
                                                        </label>

                                                        <div class="col-md-6">
                                                            <input id="name" type="text"
                                                                   class="form-control @error('name') is-invalid @enderror"
                                                                   name="name" value="{{ $client->name }}" required
                                                                   autocomplete="name" autofocus>

                                                            @error('name')
                                                            <span class="invalid-feedback" role="alert">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3">
                                                        <label for="telephone"
                                                               class="col-md-4 col-form-label text-md-end">
                                                            {{ 'Telephone' }}
                                                            <b class="" style="color: red;">*</b>
                                                        </label>

                                                        <div class="col-md-6">
                                                            <input id="telephone" type="tel"
                                                                   class="form-control @error('telephone') is-invalid @enderror"
                                                                   name="telephone" value="{{ $client->telephone }}"
                                                                   required autocomplete="telephone">
                                                            @error('telephone')
                                                            <span class="invalid-feedback" role="alert">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3">
                                                        <label for="email"
                                                               class="col-md-4 col-form-label text-md-end">{{ 'E-Mail'}}</label>

                                                        <div class="col-md-6">
                                                            <input id="email" type="email"
                                                                   class="form-control @error('email') is-invalid @enderror"
                                                                   name="email" value="{{ $client->email }}"
                                                                   autocomplete="email">

                                                            @error('email')
                                                            <span class="invalid-feedback" role="alert">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3">
                                                        <label for="birthdate"
                                                               class="col-md-4 col-form-label text-md-end"><br>{{ 'Date de Naissance' }}
                                                        </label>
                                                            <?php
                                                            $date = Carbon::now();
                                                            $thisyear = $date->year;
                                                            //selected
                                                            $year = "00";
                                                            $day = "00";
                                                            $month = "00";
                                                            if ($client->birthdate !== null && strlen($client->birthdate) > 0) {
                                                                $ymd = explode('-', $client->birthdate);

                                                                $year = $ymd[0];
                                                                $month = $ymd[1];
                                                                $day = $ymd[2];
                                                            }
                                                            ?>
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <label for="day">Jour</label>
                                                                    <select class="form-select" id="day" name="day">
                                                                        <option>--Choisir ici --</option>
                                                                        <option
                                                                            value="01" {{($day !== '00' ?  ($day === '01' ? 'selected' : ''): '')}}>
                                                                            01
                                                                        </option>
                                                                        <option
                                                                            value="02" {{($day !== '00' ?  ($day === '02' ? 'selected' : ''): '')}}>
                                                                            02
                                                                        </option>
                                                                        <option
                                                                            value="03" {{($day !== '00' ?  ($day === '03' ? 'selected' : ''): '')}}>
                                                                            03
                                                                        </option>
                                                                        <option
                                                                            value="04" {{($day !== '00' ?  ($day === '04' ? 'selected' : ''): '')}}>
                                                                            04
                                                                        </option>
                                                                        <option
                                                                            value="05" {{($day !== '00' ?  ($day === '05' ? 'selected' : ''): '')}}>
                                                                            05
                                                                        </option>
                                                                        <option
                                                                            value="06" {{($day !== '00' ?  ($day === '06' ? 'selected' : ''): '')}}>
                                                                            06
                                                                        </option>
                                                                        <option
                                                                            value="07" {{($day !== '00' ?  ($day === '07' ? 'selected' : ''): '')}}>
                                                                            07
                                                                        </option>
                                                                        <option
                                                                            value="08" {{($day !== '00' ?  ($day === '08' ? 'selected' : ''): '')}}>
                                                                            08
                                                                        </option>
                                                                        <option
                                                                            value="09" {{($day !== '00' ?  ($day === '09' ? 'selected' : ''): '')}}>
                                                                            09
                                                                        </option>
                                                                        <option
                                                                            value="10" {{($day !== '00' ?  ($day === '10' ? 'selected' : ''): '')}}>
                                                                            10
                                                                        </option>
                                                                        <option
                                                                            value="11" {{($day !== '00' ?  ($day === '11' ? 'selected' : ''): '')}}>
                                                                            11
                                                                        </option>
                                                                        <option
                                                                            value="12" {{($day !== '00' ?  ($day === '12' ? 'selected' : ''): '')}}>
                                                                            12
                                                                        </option>
                                                                        <option
                                                                            value="13" {{($day !== '00' ?  ($day === '13' ? 'selected' : ''): '')}}>
                                                                            13
                                                                        </option>
                                                                        <option
                                                                            value="14" {{($day !== '00' ?  ($day === '14' ? 'selected' : ''): '')}}>
                                                                            14
                                                                        </option>
                                                                        <option
                                                                            value="15" {{($day !== '00' ?  ($day === '15' ? 'selected' : ''): '')}}>
                                                                            15
                                                                        </option>
                                                                        <option
                                                                            value="16" {{($day !== '00' ?  ($day === '16' ? 'selected' : ''): '')}}>
                                                                            16
                                                                        </option>
                                                                        <option
                                                                            value="17" {{($day !== '00' ?  ($day === '17' ? 'selected' : ''): '')}}>
                                                                            17
                                                                        </option>
                                                                        <option
                                                                            value="18" {{($day !== '00' ?  ($day === '18' ? 'selected' : ''): '')}}>
                                                                            18
                                                                        </option>
                                                                        <option
                                                                            value="19" {{($day !== '00' ?  ($day === '19' ? 'selected' : ''): '')}}>
                                                                            19
                                                                        </option>
                                                                        <option
                                                                            value="20" {{($day !== '00' ?  ($day === '20' ? 'selected' : ''): '')}}>
                                                                            20
                                                                        </option>
                                                                        <option
                                                                            value="21" {{($day !== '00' ?  ($day === '21' ? 'selected' : ''): '')}}>
                                                                            21
                                                                        </option>
                                                                        <option
                                                                            value="22" {{($day !== '00' ?  ($day === '22' ? 'selected' : ''): '')}}>
                                                                            22
                                                                        </option>
                                                                        <option
                                                                            value="23" {{($day !== '00' ?  ($day === '23' ? 'selected' : ''): '')}}>
                                                                            23
                                                                        </option>
                                                                        <option
                                                                            value="24" {{($day !== '00' ?  ($day === '24' ? 'selected' : ''): '')}}>
                                                                            24
                                                                        </option>
                                                                        <option
                                                                            value="25" {{($day !== '00' ?  ($day === '25' ? 'selected' : ''): '')}}>
                                                                            25
                                                                        </option>
                                                                        <option
                                                                            value="26" {{($day !== '00' ?  ($day === '26' ? 'selected' : ''): '')}}>
                                                                            26
                                                                        </option>
                                                                        <option
                                                                            value="27" {{($day !== '00' ?  ($day === '27' ? 'selected' : ''): '')}}>
                                                                            27
                                                                        </option>
                                                                        <option
                                                                            value="28" {{($day !== '00' ?  ($day === '28' ? 'selected' : ''): '')}}>
                                                                            28
                                                                        </option>
                                                                        <option
                                                                            value="29" {{($day !== '00' ?  ($day === '29' ? 'selected' : ''): '')}}>
                                                                            29
                                                                        </option>
                                                                        <option
                                                                            value="30" {{($day !== '00' ?  ($day === '30' ? 'selected' : ''): '')}}>
                                                                            30
                                                                        </option>
                                                                        <option
                                                                            value="31" {{($day !== '00' ?  ($day === '31' ? 'selected' : ''): '')}}>
                                                                            31
                                                                        </option>
                                                                    </select>

                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label for="month">Mois</label>
                                                                    <select class="form-select" id="month" name="month">
                                                                        <option>--Choisir ici --</option>
                                                                        <option
                                                                            value="01" {{($month !== '00' ?  ($month === '01' ? 'selected' : ''): '')}}>
                                                                            Janvier
                                                                        </option>
                                                                        <option
                                                                            value="02" {{($month !== '00' ?  ($month === '02' ? 'selected' : ''): '')}}>
                                                                            Fevrier
                                                                        </option>
                                                                        <option
                                                                            value="03" {{($month !== '00' ?  ($month === '03' ? 'selected' : ''): '')}}>
                                                                            Mars
                                                                        </option>
                                                                        <option
                                                                            value="04" {{($month !== '00' ?  ($month === '04' ? 'selected' : ''): '')}}>
                                                                            Avril
                                                                        </option>
                                                                        <option
                                                                            value="05" {{($month !== '00' ?  ($month === '05' ? 'selected' : ''): '')}}>
                                                                            Mai
                                                                        </option>
                                                                        <option
                                                                            value="06" {{($month !== '00' ?  ($month === '06' ? 'selected' : ''): '')}}>
                                                                            Juin
                                                                        </option>
                                                                        <option
                                                                            value="07" {{($month !== '00' ?  ($month === '07' ? 'selected' : ''): '')}}>
                                                                            Juillet
                                                                        </option>
                                                                        <option
                                                                            value="08" {{($month !== '00' ?  ($month === '08' ? 'selected' : ''): '')}}>
                                                                            Aout
                                                                        </option>
                                                                        <option
                                                                            value="09" {{($month !== '00' ?  ($month === '09' ? 'selected' : ''): '')}}>
                                                                            Septembre
                                                                        </option>
                                                                        <option
                                                                            value="10" {{($month !== '00' ?  ($month === '10' ? 'selected' : ''): '')}}>
                                                                            Octobre
                                                                        </option>
                                                                        <option
                                                                            value="11" {{($month !== '00' ?  ($month === '11' ? 'selected' : ''): '')}}>
                                                                            Novembre
                                                                        </option>
                                                                        <option
                                                                            value="12" {{($month !== '00' ?  ($month === '12' ? 'selected' : ''): '')}}>
                                                                            Decembre
                                                                        </option>
                                                                    </select>

                                                                </div>

                                                                <div class="col-md-4">
                                                                    <label for="year">Annee</label>
                                                                    <select class="form-select" id="year" name="year">
                                                                        <option>--Choisir ici --</option>
                                                                        @for($i = $thisyear; $i >= 1900; $i--)
                                                                            <option
                                                                                value="{{$i}}" {{($year !== '00' ?  ($year === (''. $i) ? 'selected' : ''): '')}}>{{$i}}</option>
                                                                        @endfor
                                                                    </select>
                                                                </div>
                                                            </div>


                                                            {{--<input id="birthdate" type="date" class="form-control @error('birthdate') is-invalid @enderror" name="birthdate"  autocomplete="birthdate">--}}

                                                            @error('birthdate')
                                                            <span class="invalid-feedback" role="alert">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3">
                                                        <label for="gender"
                                                               class="col-md-4 col-form-label text-md-end">{{ 'Civilite' }}</label>

                                                        <div class="col-md-6">
                                                            <select id="gender"
                                                                    class="form-control form-select form-select-lg @error('gender') is-invalid @enderror"
                                                                    name="gender">
                                                                <option value="">Choisissez ici</option>
                                                                <option
                                                                    value="MONSIEUR" {{$client->gender === 'MONSIEUR' ? 'selected' : ''}}>
                                                                    Monsieur
                                                                </option>
                                                                <option
                                                                    value="MADAME" {{$client->gender === 'MADAME' ? 'selected' : ''}}>
                                                                    Madame
                                                                </option>
                                                                <option
                                                                    value="MADEMOISELLE" {{$client->gender === 'MADEMOISELLE' ? 'selected' : ''}}>
                                                                    Mademoiselle
                                                                </option>
                                                            </select>

                                                            @error('gender')
                                                            <span class="invalid-feedback" role="alert">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3">
                                                        <label for="city"
                                                               class="col-md-4 col-form-label text-md-end">{{ 'Ville' }}</label>

                                                        <div class="col-md-6">
                                                            <input id="city" type="text"
                                                                   class="form-control @error('city') is-invalid @enderror"
                                                                   name="city" autocomplete="city"
                                                                   value="{{ $client->city }}">

                                                            @error('city')
                                                            <span class="invalid-feedback" role="alert">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3">
                                                        <label for="quarter"
                                                               class="col-md-4 col-form-label text-md-end">{{ 'Quartier' }}</label>

                                                        <div class="col-md-6">
                                                            <input id="quarter" type="text"
                                                                   class="form-control @error('quarter') is-invalid @enderror"
                                                                   name="quarter" autocomplete="quarter"
                                                                   value="{{ $client->quarter }}">

                                                            @error('quarter')
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
                                                    <button type="submit" class="btn btn-success">Ajourner le client
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                @if(count(Voucher::all()) !== 0)
                                    <a  class="btn btn-warning" href="{{url('/client/' . $client->id . '/vouchers')}}">
                                        {{ 'voir les bons' }}
                                    </a>
                                @endif

                            @else
                                <span>Client desactive</span> &nbsp;&nbsp;&nbsp;&nbsp;
                                <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                        data-bs-target="#confirm-activate-client-modal">
                                    {{ 'Debloquer le client' }}
                                </button>

                                <div class="modal fade" id="confirm-activate-client-modal" data-bs-backdrop="static"
                                     data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                                     aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="staticBackdropLabel">Vous souhaitez
                                                    activer le client <strong
                                                        style="color: darkred;">{{$client->name}}</strong></h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                            </div>
                                            <form method="POST" action="{{url('/client/' . $client->id . '/activate')}}"
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
                                                    <input type="hidden" name="clientid" value="{{$client->id}}">
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-danger"
                                                            data-bs-dismiss="modal">Annuler
                                                    </button>
                                                    <button type="submit" class="btn btn-success">Debloquer le client
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            @endif

                            <form id="generate-voucher-form" action="{{ route('vouchers.post') }}" method="POST"
                                  class="d-none">
                                @csrf
                            </form>
                        </div>

                    </div>

                    <div class="card-footer">
                        {{'Footer'}}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
