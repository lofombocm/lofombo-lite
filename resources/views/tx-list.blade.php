@php
    use App\Models\Transactiontype;
    use App\Models\Loyaltytransaction;

@endphp
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">

            @include('layouts.menu')

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">
                        <div id="top">
                            <button class="btn btn-link" onclick="history.back();" style="text-decoration: none; font-size: large;"><<</button>
                            &nbsp;&nbsp;&nbsp;{{ 'Les Transactions' }}</div>

                    </div>
                    <div class="card-body">

                        <div class="list-group list-group-flush alert alert-light">
                            @foreach($txs as $tx)
                                @php
                                    //$transactiontype = Transactiontype::where('id', $tx->transactiontypeid)->first();
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
                        {{' '}}
                    </div>--}}
                </div>
            </div>
        </div>
    </div>
@endsection


{{--
--}}
