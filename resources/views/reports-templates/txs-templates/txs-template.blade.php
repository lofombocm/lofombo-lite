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
                        <span style="width: 100%; text-align: center; border-bottom: 0 black solid; font-size: large; margin-bottom: 30px;">
                            @if(isset($from))
                                {{ __("Transactions de la période du") . ' ' . Carbon::parse($from)->format('d-m-Y') .
                                __("au") . '  ' . Carbon::parse($to)->format('d-m-Y')}}
                            @else
                                <span style="width: 100%; text-align: center; border-bottom: 0 black solid; font-size: x-large; margin-bottom: 30px;">
                                     {{ __("Toutes les Transactions") }}
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
                                    {{ __('Date') }}
                                </th>

                                <th scope="col" style="vertical-align: middle;">
                                    {{ __('Montant') }}
                                </th>
                                <th scope="col" style="vertical-align: middle;">
                                   {{ __('Points') }}
                                </th>
                                <th scope="col">
                                    {{ __('Type Transaction') }}
                                </th>
                                <th scope="col" style="vertical-align: middle;">
                                    {{ __('Client') }}
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

                        <br><br>
                            <div class="row" style="border-bottom: 3px black solid; margin-top: 20px;">

                            </div>

                        <br><br>
                            <table>
                                <thead class="" style="color: darkred; border: 1px black solid;">
                                    <th scope="col" style="vertical-align: middle;">
                                        {{ '#' }}
                                    </th>

                                    <th scope="col" style="vertical-align: middle;">
                                        {{ __('Libelé') }}
                                    </th>

                                    <th scope="col" style="vertical-align: middle;">
                                        {{ __('Valeur') }}
                                    </th>
                                </thead>
                                <tbody style="color: black;">
                                    <tr style="height: 60px;">
                                        <th scope="row" style="vertical-align: middle;">
                                            <span>{{'1'.'- '}}</span>
                                            <br>
                                        </th>
                                        <td>{{__("Montant Total")}}</td>
                                        <td>{{$total}}</td>
                                    </tr>

                                    <tr style="height: 60px;">
                                        <th scope="row" style="vertical-align: middle;">
                                            <span>{{'2'.'- '}}</span>
                                            <br>
                                        </th>
                                        <td>{{__("Montant Achat")}}</td>
                                        <td>{{$purchase_total}}</td>
                                    </tr>
                                    <tr style="height: 60px;">
                                        <th scope="row" style="vertical-align: middle;">
                                            <span>{{'3'.'- '}}</span>
                                            <br>
                                        </th>
                                        <td>{{__("Bonus Enregistrement")}}</td>
                                        <td>{{$gift_total}}</td>
                                    </tr>
                                    <tr style="height: 60px;">
                                        <th scope="row" style="vertical-align: middle;">
                                            <span>{{'4'.'- '}}</span>
                                            <br>
                                        </th>
                                        <td>{{__("Bonus Anniversaire")}}</td>
                                        <td>{{$birthdate_total}}</td>
                                    </tr>
                                </tbody>
                            </table>
                            @else
                            <h4>{{ __("Aucune transaction trouvée") }}</h4>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


