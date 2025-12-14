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
                            @php
                                $etat = '';
                                if ($state == 'ALL'){
                                    $etat = __("Tous");
                                }
                                elseif ($state === 'ACTIVATED'){
                                    $etat = __('ACTIVE');
                                }elseif($state === 'DEACTIVATED'){
                                    $etat = __('DESACTIVE');
                                }else{
                                    $etat = 'INCOHERENT';
                                }
                            @endphp
                            <span >{{ __("Etat") . ': ' . $etat}}</span><br>
                            @if(isset($from))

                                {{ __("Clients de la période du") . ' ' . Carbon::parse($from)->format('d-m-Y') .
                                ' ' . __("au") . '  ' . Carbon::parse($to)->format('d-m-Y')}}
                            @else
                                <span style="width: 100%; text-align: center; border-bottom: 0 black solid; font-size: large; margin-bottom: 30px;">
                                     {{ __("Tous les Clients") }}
                                </span>
                            @endif

                        </span><br>
                        @if(count($clients) > 0)
                                <?php $index = 1; ?>
                            <br>

                            <table class="table table-bordered m-3">
                                <thead class="" style="color: darkred; border: 1px black solid;">
                                <th scope="col" style="vertical-align: middle;">
                                    {{ '#' }}
                                </th>

                                <th scope="col" style="vertical-align: middle;">
                                    {{ __('Nom') }}
                                </th>

                                <th scope="col" style="vertical-align: middle;">
                                    {{ __('Téléphone') }}
                                </th>
                                <th scope="col" style="vertical-align: middle;">
                                    {{ __('Email') }}
                                </th>
                                <th scope="col">
                                    {{ __('Points de fidélité') }}
                                </th>
                                <th scope="col" style="vertical-align: middle;">
                                    {{ __('Etat') }}
                                </th>

                                </thead>
                                <tbody style="color: black;">
                                @foreach($clients as $client)
                                    <tr style="height: 60px;">
                                        <th scope="row" style="vertical-align: middle;">
                                            <span>{{$index.'- '}}</span>
                                            <br>
                                        </th>

                                        <td style="vertical-align: middle;">
                                            <span>{{ $client->name }}</span>
                                            <br>
                                        </td>

                                        <td style="vertical-align: middle;">
                                            <span style="">
                                                {{ $client->telephone }}</span>
                                            <br>
                                        </td>

                                        <td style="vertical-align: middle;">
                                            <span style="margin-left: 20px;">
                                                @if($client->email != null)
                                                    {{$client->email}}
                                                @else
                                                    {{'N/D'}}
                                                @endif
                                            </span>
                                            <br>
                                        </td>

                                        <td style="vertical-align: middle;">
                                            <span style="">
                                                <?php
                                                    //
                                                    $loyaltyAccount =
                                                        \App\Models\Loyaltyaccount::where('holderid', $client->id)->first();
                                                ?>
                                                {{ intval(strval(decrypt($loyaltyAccount->point_balance))) }}</span>
                                            <br>
                                        </td>

                                        <td style="vertical-align: middle;">
                                            {{$client->active ? 'ACTIVE' : 'DESACTIVE'}}
                                            <br>
                                        </td>
                                    </tr>
                                        <?php $index = $index + 1; ?>
                                @endforeach
                                </tbody>
                            </table>
                        @else
                            <h4>{{ __("Aucun Client trouvé") }}</h4>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


