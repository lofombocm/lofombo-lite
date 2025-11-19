@php
@endphp
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">

            @include('layouts.menu')

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">
                        <div id="top" style="display: inline; float: left;">
                            <a class="btn btn-link"  href="{{url('/home')}}" style="text-decoration: none; font-size: large;">&lt;</a>
                            <button class="btn btn-link" onclick="history.back();" style="text-decoration: none; font-size: large;"><<</button>
                            &nbsp;&nbsp;&nbsp;{{ 'Les Transactions' }}</div>
                        <div style="display: inline; float: right;">
                            <form action="{{route('home.loyaltytransactions.client.search', $clientid)}}" method="POST" role="search">
                                @csrf
                                <div class="input-group" style="background: white; height: 24px;">
                                    <input type="text" class="form-control" name="q"
                                           placeholder="Search transactions" style="background: white; border-right: 0 white solid;">
                                    <span class="input-group-btn" style="background: white; margin-left: -5px;">
                                        <button type="submit" class="btn btn-default">
                                            <span class="glyphicon glyphicon-search">
                                                <img src="{{asset('images/icons8-search-24.png')}}" alt=""></span>
                                        </button>
                                    </span>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(count($txs) > 0)
                            @if(isset($q)) <p> Le resultat de la recherche pour votre requete <b> {{ $q }} </b> est :</p> @endif
                                    <?php $index = 1; ?>
                                <table class="table table-striped table-responsive table-bordered">
                                    <thead class="" style="color: darkred;">
                                    <th scope="col">
                                        {{ '#' }}
                                    </th>

                                    <th scope="col">
                                        {{ 'Date' }}
                                    </th>

                                    <th scope="col">
                                        {{ 'Montant' }}
                                    </th>
                                    <th scope="col">
                                        {{ 'Nbre Points' }}
                                    </th>
                                    <th scope="col">
                                        {{ 'Type Transaction' }}
                                    </th>
                                    <th scope="col">
                                        {{ 'Plus Details' }}
                                    </th>

                                    </thead>
                                    <tbody>
                                    @foreach($txs as $tx)
                                        <tr>
                                            <th scope="row">
                                                <h5 >{{$index}}</h5>
                                            </th>

                                            <td >
                                                <h5 >{{\Illuminate\Support\Carbon::parse($tx->date)->format('d-m-Y H:i:s')}}</h5>
                                            </td>

                                            <td >
                                                <h5 style="">{{$tx->amount}}</h5>
                                            </td>

                                            <td >
                                                <h5 style="">{{$tx->point}}</h5>
                                            </td>

                                            <td >
                                                <h5 style="">{{$tx->transactiontype}}</h5>
                                            </td>

                                            <td >
                                                <a href="{{route('home.loyaltytransactions.details', $tx->id)}}"
                                                   class="btn btn-link" style="text-decoration: none;">
                                                    <img src="{{asset('images/icons8-right-chevron-25.png')}}" alt=">"/>
                                                </a>
                                            </td>
                                        </tr>
                                            <?php $index = $index + 1; ?>
                                    @endforeach
                                    </tbody>
                                </table>


                                {{--<div class="list-group list-group-flush alert alert-light">
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
                            </div>--}}
                        @else
                            <h5>{{'Aucun detail trouve. Essayer de chercher de nouveau !'}}</h5>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


{{--
--}}
