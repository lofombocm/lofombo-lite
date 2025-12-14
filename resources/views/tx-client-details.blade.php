@php
    use App\Models\Reward;use App\Models\Voucher
  ; use Illuminate\Support\Carbon
  ;
@endphp
@extends('layouts.app-client')

@section('content')
    <div class="container">
        <div class="row justify-content-center">

            @include('layouts.client-menu')

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">
                        <h5 style="display: inline;"><strong>{{ __("Détails de la transaction") . '  ' . $tx->id }}</strong></h5>
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

                        <div class="list-group list-group-flush alert alert-info">
                            <a href="#" class="list-group-item list-group-item-action"
                               style="margin-left: 15px; width: 98%;">
                                <h5>
                                    {{__("Date")}}: &nbsp; &nbsp; {{$tx->date}}
                                </h5>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action"
                               style="margin-left: 15px; width: 98%;">
                                <h5>
                                    {{__("Points")}}: &nbsp; &nbsp; {{$tx->point}}
                                </h5>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action"
                               style="margin-left: 15px; width: 98%;">
                                <h5>
                                    {{__("Montant")}}: &nbsp; &nbsp; {{$tx->amount}}
                                </h5>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action"
                               style="margin-left: 15px; width: 98%;">
                                <h5>
                                    {{__("Type de Transaction")}}: &nbsp; &nbsp; {{$tx->transactiontype}}
                                </h5>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action"
                               style="margin-left: 15px; width: 98%;">
                                <h5>
                                    {{__("Détails de la transaction")}}: &nbsp; &nbsp; {{$tx->transactiondetail}}
                                </h5>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action"
                               style="margin-left: 15px; width: 98%;">
                                <h5>
                                    {{__("Client")}}: &nbsp; &nbsp; <br/>
                                    &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;{{__("Nom")}}: {{$client->name}} <br />
                                    &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;{{__("Téléphone")}}: {{$client->telephone}}
                                </h5>
                            </a>
                            @if($tx->products != null)
                                <?php
                                    $products = json_decode($tx->products);
                                ?>
                                @if(count($products))
                                    <a href="#" class="list-group-item list-group-item-action"
                                       style="margin-left: 15px; width: 98%;">
                                        <h5>
                                            @if($tx->transactiontype === 'ENREGISTREMENT ACHAT')
                                                Produits: &nbsp; &nbsp; <br/>
                                                <ol>
                                                    @foreach($products as $product)
                                                            <?php
                                                            //dd($product);
                                                            ?>
                                                        <li>
                                                            {{__("Article")}}: {{$product->name}} &nbsp;&nbsp; {{__("Prix Unitaire (TTC)")}}: {{$product->price}} &nbsp;&nbsp; {{__("Total")}}: {{$product->others}}
                                                        </li>
                                                    @endforeach
                                                </ol>
                                            @endif

                                                @if($tx->transactiontype === 'GENERATION DE BON')
                                                    Bon: &nbsp; &nbsp; <br/>
                                                    <ul>
                                                        @foreach($products as $product)
                                                                <?php
                                                                //dd($product);
                                                                ?>
                                                            <li>
                                                                {{__("N° Série")}}: {{$product->serialnumber}}</li>
                                                            <li>{{__("Type")}}: {{$product->level}} </li>
                                                            <li>{{__("Point")}}: {{$product->point}}</li>
                                                            <li>{{__("Montant")}}: {{$product->amount}}</li>
                                                            <li>{{__("Expiration")}}: {{Carbon::parse($product->expirationdate)->format('d-m-Y H:i:s')}}</li>
                                                        @endforeach
                                                    </ul>
                                                @endif

                                        </h5>
                                    </a>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
