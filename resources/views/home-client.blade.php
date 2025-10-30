@php
    use App\Models\Reward;
    use App\Models\Transactiontype;
    use App\Models\Voucher;
    use Illuminate\Support\Carbon;
    use Illuminate\Support\Facades\Auth;
    use App\Models\Loyaltytransaction;

@endphp
@extends('layouts.app-client')

@section('content')
    <div class="container">
        <div class="row justify-content-center">

            @include('layouts.client-menu')

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">{{ __('Dashboard') }}</div>
                    <div class="card-body">
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
                                    <div class="card-header"><h4>{{ 'Bons d\'Achat' }}</h4></div>
                                    <div class="card-body">
                                        <div class="list-group list-group-flush">
                                            <?php
                                            $client = Auth::guard('client')->user();
                                            $vouchers = Voucher::where('clientid', $client->id)->orderBy('created_at', 'desc')->get();
                                            ?>
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
                                                <div class="alert  {{$type}}"
                                                     style="text-align: left;
                                                     @if($voucher->level === 'GOLD') background-color: darkgoldenrod; color: white;
                                                      @endif @if($voucher->level === 'PREMIUM') background-color: darkblue; color: white;
                                                      @endif @if($voucher->level === 'CLASSIC') background-color: #495057; color: white; @endif"
                                                     role="alert">
                                                    <h6 style="display: inline;">ID: {{$voucher->id}}</h6>
                                                    <h6 style="display: inline; float: right">
                                                        <span
                                                            style="float: right;">Numero de serie: {{$voucher->serialnumber}}
                                                            @if($voucher->active === true)
                                                                <span class="position-absolute top-0 start-100
                                                                         translate-middle p-2 rounded-pill
                                                                         bg-success border border-light
                                                                         rounded-circle badge">
                                                                <span class="visually-hidden">
                                                                    Notifications of newly launched courses
                                                                </span>
                                                            </span>

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
                                                        {{$client->gender}} <i>{{$client->name}} </i>
                                                        vous beneficiez d'un
                                                        bon vous recompensant de : {{$reward->name}}
                                                        d'une valeur de <i>{{$reward->value}}
                                                                &nbsp; {{env('CURRENCY_NAME')}}</i><br><br>
                                                    </h6>

                                                    <h6 style="display: inline;">Points Engage:
                                                        {{$voucher->point}} points</h6>
                                                    <h6 style="display: inline; float: right">
                                        <span style="float: right;">Date d'expiration:
                                            {{$expirationdate->day . '-' . $expirationdate->month . '-' . $expirationdate->year . ' a ' . $expirationdate->hour . ':' . $expirationdate->minute . ':' . $expirationdate->second}}<br>
                                        </span>
                                                    </h6>

                                                    <h6><br>
                                                        Validite: {{$validite}}
                                                    </h6>
                                                    <h6 style="display: inline;">Merci Pour votre fidelite.</h6>
                                                    &nbsp;<h6
                                                        style="display: inline; float: right;">L'equip de
                                                        Marketing </h6>
                                                    <br><br>
                                                </div>

                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="card">
                                    <div class="card-header"><h4>{{ 'Last Transaction' }}</h4></div>
                                    <div class="card-body">
                                        <div class="list-group list-group-flush alert alert-light">
                                            @foreach(Loyaltytransaction::where('clienttransactionid', $client->id)->orderBy('created_at', 'desc')->limit(10)->get() as $tx)
                                                @php
                                                    $transactiontype = Transactiontype::where('id', $tx->transactiontypeid)->first();
                                                @endphp
                                                <a href="#{{$tx->id}}" class="list-group-item list-group-item-action" id="{{$tx->id}}">
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
                                                        Type: &nbsp; &nbsp; {{$transactiontype->code}}
                                                    </h6>
                                                    <h6>
                                                        Details: {{ $tx->transactiondetail }}
                                                    </h6>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        {{ 'Footer' }}
                                    </div>
                                </div>
                            </div>
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
