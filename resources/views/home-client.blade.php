@php
    use App\Models\Config;
    use App\Models\Loyaltyaccount;
    use App\Models\Notification;
    use App\Models\Voucher;
    use Illuminate\Support\Carbon;
    use Illuminate\Support\Facades\Auth;
    use App\Models\Loyaltytransaction;

    $notifications = Notification::
        where('recipient_address', Auth::guard('client')->user()->telephone)->where('read', false)->get();
    $unreadMsgNum = count($notifications);

@endphp
@extends('layouts.app-client')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            @include('layouts.client-menu')
            <div class="col-md-9">
                <?php
                //$conversion = ConversionAmountPoint::where('active', true)->where('is_applicable', true)->first();
                //$threshold = Threshold::where('active', true)->where('is_applicable', true)->first();
                $configuration = Config::where('is_applicable', true)->first();
                $levels = json_decode($configuration->levels);
                ?>

                <div class="card">
                    <div class="card-header">
                        {{ 'Dashboard' }}
                        <h4 style="color: black; display: inline; float: right;">
                            <strong>
                                Un achat a partir
                                de {{$configuration->amount_per_point}} {{'FCFA'}}
                                donne droit a 1 Point.
                            </strong>
                        </h4>
                        <br>
                    </div>
                    <div class="card-body">
                        <div class="row justify-content-center">

                            <div class="col-md-12 alert alert-light">

                                <div class="row" style="margin-bottom: -5px;">
                                    @php $i = 1; @endphp
                                    @foreach($levels as $level)
                                        <div class="col-md-3">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h5 class="card-title">
                                                        <span class="badge bg-success position-absolute top|start-*"
                                                              style="position: relative; left: 0; font-size: large; margin-top: -18px;">{{$i}}</span>
                                                        &nbsp;&nbsp;&nbsp;&nbsp;Quand le nombre de points cumule atteint
                                                        <strong> {{$level->point}} points</strong>
                                                        vous gagnez d'un bon de type <strong> {{$level->name}}</strong>

                                                    </h5>
                                                </div>
                                            </div>
                                        </div>
                                        @php $i = $i + 1; @endphp
                                    @endforeach
                                </div>
                            </div>
                        </div>

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

                        <div class="row justify-content-center">
                            <div class="col-md-7">
                                <div class="card">
                                    {{--<div class="card-header"></div>--}}
                                    <div class="card-body">
                                        <h4>{{ 'Bons d\'Achat' }}</h4>
                                        <div class="list-group list-group-flush">
                                            <?php
                                            $client = Auth::guard('client')->user();
                                            $vouchers = Voucher::where('clientid', $client->id)->orderBy('created_at', 'desc')->get();
                                            ?>
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
                                                             @if($voucher->level === 'CLASSIC') border: 3px #495057 solid; @endif"
                                                         role="alert">
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
                                                            {{$client->gender}} <i>{{$client->name}}</i> vous beneficiez
                                                            d'un
                                                            bon de niveau : <i>{{$voucher->level}}</i><br><br>
                                                        </h6>

                                                        <h6 style="display: inline;">Points Engage:
                                                            <i>{{$voucher->point}} points</i></h6>
                                                        <h6 style="display: inline; float: right">
                                                            <span style="float: right;">Date d'expiration:
                                                                <i>{{$expirationdate->day . '-' . $expirationdate->month . '-' . $expirationdate->year . ' a ' . $expirationdate->hour . ':' . $expirationdate->minute . ':' . $expirationdate->second}}</i> <br>
                                                            </span>
                                                        </h6>

                                                        <h6><br>
                                                            Validite: <i>{{$validite}} @if($voucher->is_used)
                                                                    <strong>{{'(Bon deja utilise)'}}</strong>
                                                                @endif</i>
                                                        </h6>
                                                        <h6 style="display: inline;">Merci Pour votre fidelite.</h6>
                                                        &nbsp;<h6
                                                            style="display: inline; float: right;">L'equip de
                                                            Marketing </h6>
                                                        <br><br>

                                                        {{--@if(!$voucher->active && !$voucher->is_used)--}}
                                                        {{--<a class="btn btn-success btn-sm" href="#"
                                                           data-bs-toggle="modal"
                                                           data-bs-target="#confirm-activate-voucher-modal">
                                                            <h5>Activer afin de permettre son utilisation</h5>
                                                        </a>
                                                        <div class="modal fade" id="confirm-activate-voucher-modal"
                                                             data-bs-backdrop="static"
                                                             data-bs-keyboard="false" tabindex="-1"
                                                             aria-labelledby="staticBackdropLabel"
                                                             aria-hidden="true">
                                                            <div
                                                                class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h1 class="modal-title fs-5"
                                                                            id="staticBackdropLabel">Vous souhaitez
                                                                            activer le bon <strong
                                                                                style="color: darkred;">{{$voucher->serialnumber}}</strong>
                                                                        </h1>
                                                                        <button type="button" class="btn-close"
                                                                                data-bs-dismiss="modal"
                                                                                aria-label="Close"></button>
                                                                    </div>
                                                                    <form method="POST"
                                                                          action="{{url('/client/' . $client->id . '/vouchers/' . $voucher->id . '/activate')}}"
                                                                          onsubmit="return true;">
                                                                        <div class="modal-body">

                                                                            <input type="hidden" name="error"
                                                                                   id="error"
                                                                                   class="form-control @error('error') is-invalid @enderror">
                                                                            @error('error')
                                                                            <span class="invalid-feedback"
                                                                                  role="alert"
                                                                                  style="position: relative; width: 100%; text-align: center;">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span> <br/>
                                                                            @enderror

                                                                            @csrf

                                                                            <input type="hidden" name="clientid"
                                                                                   value="{{$client->id}}">

                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button"
                                                                                    class="btn btn-danger"
                                                                                    data-bs-dismiss="modal">Annuler
                                                                            </button>
                                                                            <button type="submit"
                                                                                    class="btn btn-success"> Activer
                                                                                le bon
                                                                            </button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>--}}

                                                        {{--@else--}}
                                                        {{--@if(!$voucher->is_used)

                                                            <a class="btn btn-danger btn-sm" href="#"
                                                               data-bs-toggle="modal"
                                                               data-bs-target="#confirm-deactivate-voucher-modal">
                                                                <h5>Desactiver afin d'empecher son utilisation</h5>
                                                            </a>
                                                            <div class="modal fade"
                                                                 id="confirm-deactivate-voucher-modal"
                                                                 data-bs-backdrop="static"
                                                                 data-bs-keyboard="false" tabindex="-1"
                                                                 aria-labelledby="staticBackdropLabel"
                                                                 aria-hidden="true">
                                                                <div
                                                                    class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h1 class="modal-title fs-5"
                                                                                id="staticBackdropLabel">Vous
                                                                                souhaitez
                                                                                desactiver le bon <strong
                                                                                    style="color: darkred;">{{$voucher->serialnumber}}</strong>
                                                                            </h1>
                                                                            <button type="button" class="btn-close"
                                                                                    data-bs-dismiss="modal"
                                                                                    aria-label="Close"></button>
                                                                        </div>
                                                                        <form method="POST"
                                                                              action="{{url('/client/' . $client->id . '/vouchers/' . $voucher->id . '/deactivate')}}"
                                                                              onsubmit="return true;">
                                                                            <div class="modal-body">
                                                                                <input type="hidden" name="error"
                                                                                       id="error"
                                                                                       class="form-control @error('error') is-invalid @enderror">
                                                                                @error('error')
                                                                                <span class="invalid-feedback"
                                                                                      role="alert"
                                                                                      style="position: relative; width: 100%; text-align: center;">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span> <br/>
                                                                                @enderror
                                                                                @csrf
                                                                                <input type="hidden" name="clientid"
                                                                                       value="{{$client->id}}">
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <button type="button"
                                                                                        class="btn btn-danger"
                                                                                        data-bs-dismiss="modal">
                                                                                    Annuler
                                                                                </button>
                                                                                <button type="submit"
                                                                                        class="btn btn-success">
                                                                                    Desactiver le bon
                                                                                </button>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <a class="btn btn-success btn-sm" href="#"
                                                               data-bs-toggle="modal"
                                                               data-bs-target="#confirm-use-voucher-modal">
                                                                <h5>Confirmer l'utilisation du bon</h5>
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
                                                                            <h1 class="modal-title fs-5"
                                                                                id="staticBackdropLabel">Vous
                                                                                souhaitez
                                                                                confirme l'utilisation du bon
                                                                                <strong
                                                                                    style="color: darkred;">{{$voucher->serialnumber}}</strong>
                                                                            </h1>
                                                                            <button type="button" class="btn-close"
                                                                                    data-bs-dismiss="modal"
                                                                                    aria-label="Close"></button>
                                                                        </div>
                                                                        <form method="POST"
                                                                              action="{{url('/client/' . $client->id . '/vouchers/' . $voucher->id . '/use')}}"
                                                                              onsubmit="return true;">
                                                                            <div class="modal-body">

                                                                                <input type="hidden" name="error"
                                                                                       id="error"
                                                                                       class="form-control @error('error') is-invalid @enderror">
                                                                                @error('error')
                                                                                <span class="invalid-feedback"
                                                                                      role="alert"
                                                                                      style="position: relative; width: 100%; text-align: center;">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span> <br/>
                                                                                @enderror

                                                                                @csrf

                                                                                <input type="hidden" name="clientid"
                                                                                       value="{{$client->id}}">
                                                                                <h4><strong style="color: darkred;">En
                                                                                        confirmant l'utilisation du
                                                                                        bon, le systeme ne vous
                                                                                        permet plus de revenir en
                                                                                        arriere. Rassurez-vous
                                                                                        que le client utilise ce
                                                                                        bon.</strong></h4>

                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <button type="button"
                                                                                        class="btn btn-danger"
                                                                                        data-bs-dismiss="modal">
                                                                                    Annuler
                                                                                </button>
                                                                                <button type="submit"
                                                                                        class="btn btn-success">
                                                                                    Confirmez l'utilisation du bon
                                                                                </button>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        @endif--}}
                                                        {{--@endif--}}
                                                    </div>

                                                @endforeach
                                            @else
                                                <h5> Pas de bon pour {{$client->name}}</h5>
                                            @endif


                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="card">
                                    {{--<div class="card-header"></div>--}}
                                    <div class="card-body">
                                        <h4>{{ 'Dernieres transactions' }}</h4>
                                        <div class="list-group list-group-flush alert alert-light">
                                            @php
                                                $clientId = $client->id;
                                                $loyaltyAccount = Loyaltyaccount::where('holderid', $clientId)->first();
                                                $txs = Loyaltytransaction::where('loyaltyaccountid', $loyaltyAccount->id)->orderBy('created_at', 'desc')->limit(10)->get();
                                            @endphp
                                            @foreach($txs as $tx)
                                                @php
                                                    //$transactiontype = Transactiontype::where('id', $tx->transactiontypeid)->first();
                                                @endphp
                                                <a href="#{{$tx->id}}" class="list-group-item list-group-item-action"
                                                   id="{{$tx->id}}">
                                                    <h6>
                                                        ID: &nbsp; &nbsp; {{$tx->id}}
                                                    </h6>
                                                    <h6>
                                                        Le: &nbsp; &nbsp; {{$tx->date}}
                                                    </h6>
                                                    <h6>
                                                        Compte: &nbsp; &nbsp; {{$tx->loyaltyaccountid}}
                                                    </h6>

                                                    <h6>
                                                        Montant: &nbsp; &nbsp; {{$tx->amount}}
                                                    </h6>

                                                    <h6>
                                                        Point: &nbsp; &nbsp; {{$tx->point}}
                                                    </h6>

                                                    <h6>
                                                        Type: &nbsp; &nbsp; {{$tx->transactiontype}}
                                                    </h6>
                                                    <h6>
                                                        Details: {{ $tx->transactiondetail }}
                                                    </h6>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                    {{--<div class="card-footer">
                                        {{ ' ' }}
                                    </div>--}}
                                </div>
                            </div>
                        </div>

                    </div>
                    {{--<div class="card-footer">
                        {{' '}}
                    </div>--}}
                </div>
            </div>

            <div class="modal fade modal-lg" id="notifications-modal" data-bs-backdrop="static"
                 data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                 aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
                     style="overflow-y: initial; width: 75%;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="staticBackdropLabel"
                                style="border: 0 red solid; width: 100%;">
                                <strong
                                    style="color: darkred;">Notifications</strong>
                                <span style="
                                            position:relative;
                                            right: 0;
                                            float: right;
                                            color: darkred;
                                            font-size: x-large;"><strong>{{$unreadMsgNum}}</strong></span>
                            </h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                        </div>
                        {{--<form method="POST" action="{{route('configuration.post')}}"
                              enctype="multipart/form-data" onsubmit="return true;" >--}}
                        <div class="modal-body" style="height: 80vh; overflow-y: auto;">

                            <input type="hidden" name="error" id="error"
                                   class="form-control @error('error') is-invalid @enderror">
                            @error('error')
                            <span class="invalid-feedback" role="alert"
                                  style="position: relative; width: 100%; text-align: center;">
                                                                    <strong>{{ $message }}</strong>
                                                                </span> <br/>
                            @enderror

                            @csrf

                            <div class="card">
                                <div class="card-body">
                                    {{--<div class="list-group list-group-flush">--}}
                                    @foreach($notifications as $notification)
                                        <adiv class="list-group-item list-group-item-action">
                                            <h5>
                                                Objet: <strong>{{$notification->subject}}</strong> &nbsp; &nbsp;
                                                <span class="badge bg-primary position-absolute top|start-*"
                                                      style="position: relative; right: 0; font-size: small;">
                                                        @php
                                                            $sent_at = Carbon::parse($notification->sent_at);
                                                        @endphp
                                                        Le: {{$sent_at->day . '-' . $sent_at->month . '-' . $sent_at->year . ' a ' . $sent_at->hour . ':' . $sent_at->minute . ':' . $sent_at->second}}
                                                        </span>
                                            </h5>
                                            <h5 style="font-size: small;">
                                                De: {{$notification->sender}}
                                                <a href="{{route('notifications.index', $notification->id)}}"
                                                   style="position: relative; right: 0; float:right; text-decoration: none; margin-top: 5px;">
                                                    {{'Details'}}
                                                </a>
                                            </h5>
                                            {{--<br><br>--}}
                                        </adiv>

                                    @endforeach
                                    {{--</div>--}}
                                </div>
                            </div>

                        </div>
                        {{--<div class="modal-footer">
                            <button type="button" class="btn btn-danger"
                                    data-bs-dismiss="modal">Annuler
                            </button>
                            <button type="submit" class="btn btn-success">Enregistrer
                            </button>
                        </div>--}}
                        {{--</form>--}}


                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
