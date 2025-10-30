@php use App\Models\Client;use App\Models\Reward;use Illuminate\Support\Carbon; @endphp
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">

            @include('layouts.menu')

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">
                        <h5 style="display: inline;"><strong>{{ 'Bons du client: ' . $client->name }}</strong></h5>
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

                        <div class="list-group list-group-flush">
                            @foreach($vouchers as $voucher)
                                    <?php
                                    $type = $voucher->level === 'CLASSIC' ? 'alert-secondary' : ($voucher->level === 'PREMIUM' ? 'alert-success' : 'alert-warning');
                                    $validite = 'Valide';
                                    $expirationdate = Carbon::parse($voucher->expirationdate);

                                    if ($expirationdate->isBefore(Carbon::now())) {
                                        $type = 'alert-danger';
                                        $validite = 'Invalide';
                                    }
                                    //$client = Client::where('id', $voucher->clientid)->first();
                                    $reward = Reward::where('id', $voucher->reward)->first();
                                    ?>
                                <div class="alert  {{$type}}" style="text-align: left;
                                      @if($voucher->level === 'GOLD') background-color: darkgoldenrod; color: white;@endif
                                      @if($voucher->level === 'PREMIUM') background-color: darkblue; color: white;@endif
                                      @if($voucher->level === 'CLASSIC') background-color: #495057; color: white; @endif" role="alert" >
                                    <h5 style="display: inline;">ID: <strong>{{$voucher->id}}</strong></h5>
                                    <h5 style="display: inline; float: right">
                                        <span style="float: right;">Numero de serie: <strong>{{$voucher->serialnumber}}</strong></span>
                                    </h5>
                                    <br><br>
                                    <h5>
                                        {{$client->gender}} <strong><i>{{$client->name}} </i></strong> vous beneficiez d'un
                                        bon vous recompensant de : <strong>{{$reward->name}}</strong>
                                        d'une valeur de <strong><i>{{$reward->value}} &nbsp; {{env('CURRENCY_NAME')}}</i></strong><br><br>
                                    </h5>

                                    <h5 style="display: inline;">Points Engage: <strong>{{$voucher->point}} points</strong></h5>
                                    <h5 style="display: inline; float: right">
                                        <span style="float: right;">Date d'expiration:
                                            <strong>{{$expirationdate->day . '-' . $expirationdate->month . '-' . $expirationdate->year . ' a ' . $expirationdate->hour . ':' . $expirationdate->minute . ':' . $expirationdate->second}}</strong> <br>
                                        </span>
                                    </h5>

                                        <h5><br>
                                            Validite: <strong>{{$validite}}</strong>
                                        </h5>
                                    <h6 style="display: inline;">Merci Pour votre fidelite.</h6> &nbsp;<h6
                                        style="display: inline; float: right;">L'equip de Marketing </h6>
                                    <br><br>

                                    @if(!$voucher->active)
                                        <a class="btn btn-success" href="#" data-bs-toggle="modal"
                                           data-bs-target="#confirm-activate-voucher-modal">
                                            <h5>Activer afin de permettre son utilisation</h5>
                                        </a>
                                        <div class="modal fade" id="confirm-activate-voucher-modal" data-bs-backdrop="static"
                                             data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                                             aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Vous souhaitez
                                                            activer le bon <strong
                                                                style="color: darkred;">{{$reward->name}}</strong></h1>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                    </div>
                                                    <form method="POST"
                                                          action="{{url('/client/' . $client->id . '/vouchers/' . $voucher->id . '/activate')}}" onsubmit="return true;">
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
                                                            <button type="submit" class="btn btn-success"> Activer le bon
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                    @else
                                        <a class="btn btn-danger" href="#" data-bs-toggle="modal"
                                           data-bs-target="#confirm-deactivate-voucher-modal">
                                            <h5>Activer afin de permettre son utilisation</h5>
                                        </a>
                                        <div class="modal fade" id="confirm-deactivate-voucher-modal" data-bs-backdrop="static"
                                             data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                                             aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Vous souhaitez
                                                            desactiver le bon <strong
                                                                style="color: darkred;">{{$reward->name}}</strong></h1>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                    </div>
                                                    <form method="POST"
                                                          action="{{url('/client/' . $client->id . '/vouchers/' . $voucher->id . '/deactivate')}}" onsubmit="return true;">
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
                                                            <button type="submit" class="btn btn-success"> Desactiver le bon
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                            @endforeach
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
