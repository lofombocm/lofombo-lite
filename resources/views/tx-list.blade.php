@php
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
                        <div id="top" style="display: inline; float: left;">
                            <a class="btn btn-link"  href="{{url('/'.GuestController::getApplicationLocal().'/home')}}" style="text-decoration: none; font-size: large;">&lt;</a>
                            <button class="btn btn-link" onclick="history.back();" style="text-decoration: none; font-size: large;"><<</button>
                            &nbsp;&nbsp;&nbsp;{{ __("Transactions") }}</div>
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
                            @if(isset($q)) <p> {{__("Le résultat de la recherche pour votre requête")}} <b> {{ $q }} </b> est :</p> @endif
                                    <?php $index = 1; ?>
                                <table class="table table-striped table-responsive table-bordered">
                                    <thead class="" style="color: darkred;">
                                    <th scope="col">
                                        {{ '#' }}
                                    </th>
                                    <th scope="col">
                                        {{__("Client")}}
                                    </th>

                                    <th scope="col">
                                        {{ __('Date') }}
                                    </th>

                                    @if(\Illuminate\Support\Facades\Auth::check())
                                        <th scope="col">
                                            {{ __('Montant') }}
                                        </th>
                                    @endif

                                    <th scope="col">
                                        {{ __("Nbre Points") }}
                                    </th>
                                    <th scope="col">
                                        {{ __("Type Transaction") }}
                                    </th>
                                    <th scope="col">
                                        {{ __("Plus...") }}
                                    </th>

                                    </thead>
                                    <tbody>
                                    @foreach($txs as $tx)
                                        <tr>
                                            <th scope="row">
                                                <h5 >{{$index}}</h5>
                                            </th>

                                            <td>
                                                {{$client->name}}
                                            </td>

                                            <td >
                                                <h5 >{{\Illuminate\Support\Carbon::parse($tx->date)->format('d-m-Y H:i:s')}}</h5>
                                            </td>

                                            @if(\Illuminate\Support\Facades\Auth::check())
                                                <td>
                                                    <h5 style="">{{$tx->amount}}</h5>
                                                </td>
                                            @endif


                                            <td >
                                                <h5 style="">
                                                    {{$tx->transactiontype === 'GENERATION DE BON' ? '-' : '+'}}{{$tx->point}}
                                                </h5>
                                            </td>

                                            <td >
                                                <h5 style="">{{$tx->transactiontype}}</h5>
                                            </td>

                                            <td >
                                                <a href="{{route('home.loyaltytransactions.details', ['txid' => $tx->id/*, 'locale' => \Illuminate\Support\Facades\Request::segment(1)*/])}}"
                                                   class="btn btn-link" style="text-decoration: none;">
                                                    <img src="{{asset('images/icons8-right-chevron-25.png')}}" alt=">"/>
                                                </a>
                                            </td>
                                        </tr>
                                            <?php $index = $index + 1; ?>
                                    @endforeach
                                    </tbody>
                                </table>
                        @else
                            <h5>{{ __("Aucune transaction trouvée") }}</h5>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


{{--
--}}
