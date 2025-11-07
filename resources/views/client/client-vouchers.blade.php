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
                            @if(count($vouchers) > 0)
                                    @foreach($vouchers as $voucher)
                                            <?php
                                            //$type = $voucher->level === 'CLASSIC' ? 'alert-secondary' : ($voucher->level === 'PREMIUM' ? 'alert-success' : 'alert-warning');
                                            $type = 'alert-info';
                                            $validite = 'Valide';
                                            $expirationdate = Carbon::parse($voucher->expirationdate);

                                            if ($expirationdate->isBefore(Carbon::now())) {
                                                $validite = 'Invalide';
                                            }
                                            //$client = Client::where('id', $voucher->clientid)->first();
                                            //$reward = Reward::where('id', $voucher->reward)->first();
                                            ?>
                                        <div class="alert  {{$type}}" style="text-align: left;
                                             @if($voucher->level === 'GOLD') border: 3px darkgoldenrod solid; @endif
                                             @if($voucher->level === 'PREMIUM') border: 3px darkblue solid;@endif
                                             @if($voucher->level === 'CLASSIC') border: 3px #495057 solid; @endif" role="alert" >
                                            <h6 style="display: inline;">ID: <i>{{$voucher->id}}</i></h6>
                                            <h6 style="display: inline; float: right">
                                                <span style="float: right;">
                                                    Numero de serie: <i>{{$voucher->serialnumber}}</i>
                                                    @if($voucher->active === true)
                                                        @if($voucher->is_used)
                                                            <span class="position-absolute top-0 start-100
                                                                                 translate-middle p-2 rounded-pill
                                                                                 bg-dark border border-light
                                                                                 rounded-circle badge">
                                                                        <span class="visually-hidden">
                                                                            Notifications of newly launched courses
                                                                        </span>
                                                                    </span>
                                                        @else
                                                            <span class="position-absolute top-0 start-100
                                                                                 translate-middle p-2 rounded-pill
                                                                                 bg-success border border-light
                                                                                 rounded-circle badge">
                                                                        <span class="visually-hidden">
                                                                            Notifications of newly launched courses
                                                                        </span>
                                                                    </span>
                                                        @endif


                                                    @else

                                                        <span class="position-absolute top-0 start-100
                                                                                 translate-middle p-2 rounded-pill
                                                                                 bg-danger border border-light
                                                                                 rounded-circle badge">
                                                                        <span class="visually-hidden">
                                                                            Notifications of newly launched courses
                                                                        </span>
                                                                    </span>
                                                    @endif
                                                </span>
                                            </h6>
                                            <br><br>
                                            <h6>
                                                {{$client->gender}} <i>{{$client->name}}</i> vous beneficiez d'un
                                                bon de niveau : <i>{{$voucher->level}}</i><br><br>
                                            </h6>

                                            <h6 style="display: inline;">Points Engage: <i>{{$voucher->point}} points</i></h6>
                                            <h6 style="display: inline; float: right">
                                                <span style="float: right;">Date d'expiration:
                                                    <i>{{$expirationdate->day . '-' . $expirationdate->month . '-' . $expirationdate->year . ' a ' . $expirationdate->hour . ':' . $expirationdate->minute . ':' . $expirationdate->second}}</i> <br>
                                                </span>
                                            </h6>

                                                <h6><br>
                                                    Validite: <i>{{$validite}} @if($voucher->is_used)  <strong>{{'(Bon deja utilise)'}}</strong> @endif</i>
                                                </h6>
                                            <h6 style="display: inline;">Merci Pour votre fidelite.</h6> &nbsp;<h6
                                                style="display: inline; float: right;">L'equip de Marketing </h6>
                                            <br><br>

                                            @if(!$voucher->active && !$voucher->is_used)
                                                <a class="btn btn-success btn-sm" href="#" data-bs-toggle="modal"
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
                                                                        style="color: darkred;">{{$voucher->serialnumber}}</strong></h1>
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
                                                @if(!$voucher->is_used)

                                                    <a class="btn btn-danger btn-sm" href="#" data-bs-toggle="modal"
                                                       data-bs-target="#confirm-deactivate-voucher-modal">
                                                        <h5>Desactiver afin d'empecher son utilisation</h5>
                                                    </a>
                                                    <div class="modal fade" id="confirm-deactivate-voucher-modal" data-bs-backdrop="static"
                                                         data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                                                         aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Vous souhaitez
                                                                        desactiver le bon <strong
                                                                            style="color: darkred;">{{$voucher->serialnumber}}</strong></h1>
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

                                                    <a class="btn btn-success btn-sm" href="#" data-bs-toggle="modal"
                                                       data-bs-target="#confirm-use-voucher-modal">
                                                        <h5>Confirmer l'utilisation du bon</h5>
                                                    </a>
                                                    <div class="modal fade" id="confirm-use-voucher-modal" data-bs-backdrop="static"
                                                         data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                                                         aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Vous souhaitez
                                                                        confirme l'utilisation du bon <strong
                                                                            style="color: darkred;">{{$voucher->serialnumber}}</strong></h1>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                            aria-label="Close"></button>
                                                                </div>
                                                                <form method="POST"
                                                                      action="{{url('/client/' . $client->id . '/vouchers/' . $voucher->id . '/use')}}" onsubmit="return true;">
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
                                                                        <h4><strong style="color: darkred;">En confirmant l'utilisation du bon, le systeme ne vous permet plus de revenir en arriere. Rassurez-vous
                                                                                que le client utilise ce bon.</strong></h4>

                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-danger"
                                                                                data-bs-dismiss="modal">Annuler
                                                                        </button>
                                                                        <button type="submit" class="btn btn-success"> Confirmez l'utilisation du bon</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>

                                                @endif
                                            @endif
                                        </div>

                                    @endforeach
                                @else
                                    <h5 > Pas de bon pour {{$client->name}}</h5>
                                @endif
                        </div>

                    </div>

                    <div class="card-footer">
                        {{' '}}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
