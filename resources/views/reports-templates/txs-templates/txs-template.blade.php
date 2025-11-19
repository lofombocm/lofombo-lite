@php
    use App\Models\Client;
    use SimpleSoftwareIO\QrCode\Facades\QrCode;
    use \Illuminate\Support\Carbon;
@endphp
@extends('layouts.voucher-template')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body" style="margin: 50px;">
                        <br><br><br>
                        <h1 style="width: 100%; text-align: center; border-bottom: 1px black solid;">
                            {{ $config->enterprise_name}}
                        </h1>
                        <h4 style="width: 100%; text-align: right; border-bottom: 0 black solid; text-decoration: underline;">
                            <i>Le {{Carbon::now()->format('d-m-Y')}}</i>
                        </h4>
                        <span style="width: 100%; text-align: center; border-bottom: 0 black solid; font-size: x-large; margin-bottom: 30px;">
                            @if(isset($from))
                                {{ 'Transaction de la periode du ' . Carbon::parse($from)->format('d-m-Y') .
                                ' au ' . Carbon::parse($to)->format('d-m-Y')}}
                            @else
                                <span style="width: 100%; text-align: center; border-bottom: 0 black solid; font-size: x-large; margin-bottom: 30px;">
                                     {{'Toutes les Transactions'}}
                                </span>
                            @endif

                        </span><br>
                        @if(count($txs) > 0)
                                <?php $index = 1; ?>
                        <br>

                            <table class="table table-bordered m-3">
                                <thead class="" style="color: darkred; border: 1px black solid;">
                                <th scope="col" style="vertical-align: middle;">
                                    {{ '#' }}
                                </th>

                                <th scope="col" style="vertical-align: middle;">
                                    {{ 'Date' }}
                                </th>

                                <th scope="col" style="vertical-align: middle;">
                                    {{ 'Montant' }}
                                </th>
                                <th scope="col" style="vertical-align: middle;">
                                   {{ 'Nbre Points' }}
                                </th>
                                <th scope="col">
                                    {{ 'Type Transaction' }}
                                </th>
                                <th scope="col" style="vertical-align: middle;">
                                    {{ 'Client' }}
                                </th>

                                </thead>
                                <tbody style="color: black;">
                                @foreach($txs as $tx)
                                    <tr style="height: 60px;">
                                        <th scope="row" style="vertical-align: middle;">
                                            <span>{{$index.'- '}}</span>
                                            <br>
                                        </th>

                                        <td style="vertical-align: middle;">
                                            <span>{{\Illuminate\Support\Carbon::parse($tx->date)->format('d-m-Y')}}</span>
                                            <br>
                                        </td>

                                        <td style="vertical-align: middle;">
                                            <span style="">{{$tx->amount}}</span>
                                            <br>
                                        </td>

                                        <td style="vertical-align: middle;">
                                            <span style="margin-left: 20px;"> {{$tx->point}}</span>
                                            <br>
                                        </td>

                                        <td style="vertical-align: middle;">
                                            <span style="">{{$tx->transactiontype}}</span>
                                            <br>
                                        </td>

                                        <td style="vertical-align: middle;">
                                                <?php
                                                $client = Client::where('id', $tx->clientid)->first();
                                                ?>
                                            {{$client->name}}
                                            <br>

                                        </td>
                                    </tr>
                                        <?php $index = $index + 1; ?>
                                @endforeach
                                </tbody>
                            </table>
                            @else
                            <h4>{{'Aucune transaction trouvee'}}</h4>
                        @endif
                        {{--<div class="list-group list-group-flush alert alert-info" style="margin-top: -20px;">
                            <a href="#" class="list-group-item list-group-item-action"
                               style="margin-left: 15px; width: 98%; text-decoration: none; color: black;">
                                <span>
                                    No. de Serie: {{$voucher->serialnumber}}
                                    <span style="display: inline; position: relative; float:right; color: #000000;">
                                        {{ 'ID: ' }} {{$voucher->id}}
                                    </span>
                                </span>
                                <br>
                            </a>

                            <a href="#" class="list-group-item list-group-item-action"
                               style="margin-left: 15px; width: 98%; text-decoration: none; color: black;">
                                <br>
                                <span>
                                    Porteur: {{$client->name}}
                                    <span style="display: inline; position: relative; float:right; color: #000000;">
                                        {{ 'Tel: ' }} {{$client->telephone}}
                                    </span>
                                </span>
                                <br>
                            </a>

                            <a href="#" class="list-group-item list-group-item-action"
                               style="margin-left: 15px; width: 98%; text-decoration: none; color: black;">
                                <br>
                                <span>
                                    Point: {{$voucher->point}}
                                </span>
                                <br>
                            </a>

                            <a href="#" class="list-group-item list-group-item-action"
                               style="margin-left: 15px; width: 98%; text-decoration: none; color: black;">
                                <br>
                                <span>
                                    Emetteur: {{$voucher->enterprise}}
                                </span>
                                <br>
                            </a>

                            <a href="#" class="list-group-item list-group-item-action"
                               style="margin-left: 15px; width: 98%; text-decoration: none; color: black;">
                                <br>
                                <span>
                                    Emis le: &nbsp; &nbsp; {{\Illuminate\Support\Carbon::parse($voucher->created_at)->format('d-m-Y H:i:s')}}
                                </span>
                                <br>
                            </a>

                            <a href="#" class="list-group-item list-group-item-action"
                               style="margin-left: 15px; width: 98%; text-decoration: none; color: black;">
                                <br>
                                <span>
                                    Expire le: {{\Illuminate\Support\Carbon::parse($voucher->expirationdate)->format('d-m-Y H:i:s')}}
                                </span>
                                <br>
                            </a>

                            <a href="#" class="list-group-item list-group-item-action"
                               style="margin-left: 15px; width: 98%; text-decoration: none;">
                                <h5 style="display: inline; float: right; margin-top: -110px;">
                                    <?php

                                    $from = [255, 0, 0];
                                    $to = [0, 0, 255];
                                    $qrcode = QrCode::size(200)
                                        ->style('dot')
                                        ->eye('circle')
                                        ->gradient($from[0], $from[1], $from[2], $to[0], $to[1], $to[2], 'diagonal')
                                        ->margin(1)
                                        ->generate($voucher->serialnumber);
                                    ?>
                                    <span style="float: right; text-align: center;">
                                        {{$qrcode}}
                                    </span>
                                    <br>
                                    <span style="float: right; text-align: center; margin-top: 50px; color: black;">
                                        <span><b>{{$voucher->enterprise . ' Vous remercie de votre fidelite.'}}</b></span>
                                    </span>
                                </h5>
                            </a>
                            <br>

                        </div>--}}


                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


