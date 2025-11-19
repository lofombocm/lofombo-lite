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
                        {{ 'Tableau de bord' }}
                        {{--<h4 style="color: black; display: inline; float: right;">
                            <strong>
                                Un achat a partir
                                de {{$configuration->amount_per_point}} {{'FCFA'}}
                                donne droit a 1 Point.
                            </strong>
                        </h4>
                        <br>--}}
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
                            <div class="col-md-12">
                                <div class="card">
                                    {{--<div class="card-header"></div>--}}
                                    <div class="card-body">
                                        <h4>{{ 'Mes Bons d\'Achat' }}</h4>
                                        <?php
                                        $client = Auth::guard('client')->user();
                                        $vouchers = Voucher::where('clientid', $client->id)->orderBy('created_at', 'desc')->get();
                                        ?>
                                        @if(count($vouchers) > 0)
                                            <table class="table table-striped table-responsive table-bordered">
                                                <thead class="" style="color: darkred;">
                                                <th scope="col">
                                                    {{ 'No. Serie' }}
                                                </th>
                                                <th scope="col">
                                                    {{ 'Niveau' }}
                                                </th>
                                                <th scope="col">
                                                    {{ 'Point' }}
                                                </th>
                                                <th scope="col">
                                                    {{ 'Expiration' }}
                                                </th>
                                                <th scope="col">
                                                    {{ 'Statut' }}
                                                </th>
                                                <th scope="col">
                                                    {{ 'Action' }}
                                                </th>
                                                </thead>
                                                <tbody>
                                                @foreach($vouchers as $voucher)
                                                        <?php
                                                        //$client = Client::where('id', $voucher->clientid)->first();
                                                        //$type = $voucher->level === 'CLASSIC' ? 'alert-secondary' : ($voucher->level === 'PREMIUM' ? 'alert-success' : 'alert-warning');
                                                        $type = 'alert-info';
                                                        $validite = 'Valide';
                                                        $expirationdate = Carbon::parse($voucher->expirationdate);
                                                        $expired = false;
                                                        $statut = '';
                                                        if ($voucher->active){
                                                            if ($voucher->is_used){
                                                                $statut = 'UTILISE';
                                                            }else{
                                                                $statut = 'ACTIVE';
                                                            }
                                                        }else{
                                                            if ($expirationdate->isBefore(Carbon::now())) {
                                                                $statut = 'EXPIRE';
                                                            }else{
                                                                $statut = 'GENERE';
                                                            }
                                                        }

                                                        if ($expirationdate->isBefore(Carbon::now())) {
                                                            $validite = 'Invalide';
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
                                                                <h5>{{$voucher->level}}</h5>
                                                            </td>
                                                            <td>
                                                                <h5>{{$voucher->point}}</h5>
                                                            </td>
                                                            <td>
                                                                <h5>{{$expirationdate->format('d-m-Y H:i:s')}}</h5>
                                                            </td>
                                                            <td>
                                                                <h5 style="display: inline;">{{$statut}}</h5>
                                                            </td>
                                                            <td >
                                                                <div>
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
                                                                </div>
                                                                <div class="dropdown">
                                                                    <a class="btn btn-link dropdown-toggle"
                                                                       href="#" role="button"
                                                                       id="dropdownMenuLink"
                                                                       style="text-decoration: none;"
                                                                       data-bs-toggle="dropdown"
                                                                       aria-haspopup="true" aria-expanded="false" >
                                                                        <img src="{{asset('images/icons8-menu-vertical-24.png')}}" alt="^" />
                                                                    </a>

                                                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuLink">
                                                                        <a class="dropdown-item btn btn-link" href="{{route('vouchers.resend.usage.code', $voucher->id)}}"
                                                                           style="text-decoration: none;">
                                                                            Renvoyer le code d'utilisation
                                                                            <img src="{{asset('images/icons8-transfer-gras-26.png')}}" alt="">
                                                                        </a>
                                                                        <a class="dropdown-item btn btn-link" href="{{route('vouchers.download', $voucher->id)}}"
                                                                            style="text-decoration: none;">
                                                                            Telecharger <img src="{{asset('images/icons8-downloading-updates-20.png')}}" alt="Telecharger">
                                                                        </a>
                                                                        <a class="dropdown-item btn btn-link" href="#"
                                                                           style="text-decoration: none;">
                                                                            Imprimer <img src="{{asset('images/icons8-print-20.png')}}" alt="">
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>

                                                @endforeach
                                                </tbody>
                                            </table>
                                        @else
                                            <h5> Pas de bon pour vous</h5>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            {{--<div class="col-md-5">
                                <div class="card">
                                    --}}{{--<div class="card-header"></div>--}}{{--
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
                                </div>
                            </div>--}}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
