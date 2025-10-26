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
                                            @foreach(App\Models\Voucher::orderBy('created_at', 'desc')->get() as $voucher)
                                                <?php
                                                    $type = $voucher->level === 'CLASSIC' ? 'alert-secondary' : ($voucher->level === 'PREMIUM'? 'alert-success':'alert-warning');
                                                    $validite = 'Valide';
                                                    $expirationdate = \Illuminate\Support\Carbon::parse($voucher->expirationdate);
                                                    if ($expirationdate->isBefore(\Illuminate\Support\Carbon::now())){
                                                        $type = 'alert-danger';
                                                        $validite = 'Invalide';
                                                    }

                                                    $client = \App\Models\Client::where('id', $voucher->clientid)->first();
                                                ?>
                                                <div  class="alert  {{$type}}" style="text-align: left;" role="alert">
                                                    ID: <strong>{{$voucher->id}}</strong> <br>
                                                    Numero de serie: <strong>{{$voucher->serialnumber}}</strong> <br>
                                                    Cher client <strong><i>{{$client->name}} </i></strong>  vous beneficiez d'un bon d'un montant de
                                                    <strong><i>{{(int) $voucher->amount}} &nbsp; {{env('CURRENCY_NAME')}}</i></strong>
                                                         pour faire des achats dans les espaces de <strong><i>{{env('ENTERPRISE')}}</i></strong> <br>
                                                    Points Engage: <strong >{{$voucher->point}}</strong> <br>
                                                    Expire le: <strong>{{$voucher->expirationdate}}</strong> <br>
                                                    Validite: <strong>{{$validite}}</strong> <br> <br>
                                                    <h6 style="display: inline;">Merci Pour votre fidelite.</h6> &nbsp;<h6 style="display: inline;">L'equip de Marketing</h6>
                                                </div>

                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="card">
                                    <div class="card-header"><h4>{{ 'Last Transaction' }}</h4></div>
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
