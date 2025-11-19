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
                        <h5 style="display: inline;"><strong>{{ 'Details de la transaction  ' . $tx->id }}</strong></h5>
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
                                    Date: &nbsp; &nbsp; {{$tx->date}}
                                </h5>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action"
                               style="margin-left: 15px; width: 98%;">
                                <h5>
                                    Points: &nbsp; &nbsp; {{$tx->point}}
                                </h5>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action"
                               style="margin-left: 15px; width: 98%;">
                                <h5>
                                    Montant: &nbsp; &nbsp; {{$tx->amount}}
                                </h5>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action"
                               style="margin-left: 15px; width: 98%;">
                                <h5>
                                    Type de transaction: &nbsp; &nbsp; {{$tx->transactiontype}}
                                </h5>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action"
                               style="margin-left: 15px; width: 98%;">
                                <h5>
                                    Datails de la transaction: &nbsp; &nbsp; {{$tx->transactiondetail}}
                                </h5>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action"
                               style="margin-left: 15px; width: 98%;">
                                <h5>
                                    Client: &nbsp; &nbsp; <br/>
                                    &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;Nom: {{$client->name}} <br />
                                    &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;Telephone: {{$client->telephone}}
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
                                                            Nom: {{$product->name}} &nbsp;&nbsp; Prix unitaire: {{$product->price}} &nbsp;&nbsp; Total: {{$product->others}}
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
                                                                Serie: {{$product->serialnumber}}</li>
                                                            <li>Niveau: {{$product->level}} </li>
                                                            <li>Point: {{$product->point}}</li>
                                                            <li>Montant: {{$product->amount}}</li>
                                                            <li>Expiration: {{Carbon::parse($product->expirationdate)->format('d-m-Y H:i:s')}}</li>
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
