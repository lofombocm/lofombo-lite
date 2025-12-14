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
                                if ($level == 'ALL'){
                                    $level = __('Tous');
                                }
                                if ($state == 'ALL'){
                                    $state = __('Tous');
                                }
                            @endphp
                            <span >{{__("Type") . ': ' . $level}} &nbsp; &nbsp; | &nbsp; &nbsp; {{ __("Etat") .': ' . $state}}</span><br>
                            @if(isset($from))

                                {{ __("Bons de la période du") . ' ' . Carbon::parse($from)->format('d-m-Y') .
                                 __("au") . '  ' . Carbon::parse($to)->format('d-m-Y')}}
                            @else
                                <span style="width: 100%; text-align: center; border-bottom: 0 black solid; font-size: large; margin-bottom: 30px;">
                                     {{ __('Touts les Bons')}}
                                </span>
                            @endif

                        </span><br>
                        @if(count($vouchers) > 0)
                                <?php $index = 1; ?>
                        <br>

                            <table class="table table-bordered m-3">
                                <thead class="" style="color: darkred; border: 1px black solid;">
                                <th scope="col" style="vertical-align: middle;">
                                    {{ '#' }}
                                </th>

                                <th scope="col" style="vertical-align: middle;">
                                    {{ __("N° Série") }}
                                </th>

                                <th scope="col" style="vertical-align: middle;">
                                    {{ __("Client") }}
                                </th>
                                <th scope="col" style="vertical-align: middle;">
                                   {{ __("Type") }}
                                </th>
                                <th scope="col">
                                    {{ 'Point' }}
                                </th>
                                <th scope="col" style="vertical-align: middle;">
                                    {{ __("Etat") }}
                                </th>
                                <th scope="col" style="vertical-align: middle;">
                                    {{ __("Date Exp.") }}
                                </th>

                                </thead>
                                <tbody style="color: black;">
                                @foreach($vouchers as $voucher)
                                    <tr style="height: 60px;">
                                        <th scope="row" style="vertical-align: middle;">
                                            <span>{{$index.'- '}}</span>
                                            <br>
                                        </th>

                                        <td style="vertical-align: middle;">
                                            <span>{{ $voucher->serialnumber }}</span>
                                            <br>
                                        </td>

                                        <td style="vertical-align: middle;">
                                            <span style="">
                                                <?php
                                                    $client = Client::where('id', $voucher->clientid)->first();
                                                ?>
                                                {{ $client->name }}</span>
                                            <br>
                                        </td>

                                        <td style="vertical-align: middle;">
                                            <span style="margin-left: 20px;"> {{$voucher->level}}</span>
                                            <br>
                                        </td>

                                        <td style="vertical-align: middle;">
                                            <span style="">{{$voucher->point}}</span>
                                            <br>
                                        </td>

                                        <td style="vertical-align: middle;">
                                                <?php
                                                $state = '';
                                                if (!$voucher->active && !$voucher->is_used){
                                                    $state = __('GENERE');
                                                }elseif($voucher->active && !$voucher->is_used){
                                                    $state = __('ACTIVE');
                                                }elseif($voucher->active && $voucher->is_used){
                                                    $state = __('UTILISE');
                                                }elseif(!$voucher->active && $voucher->is_used){
                                                    $state = __('INCOHERENT');
                                                }

                                                ?>
                                            {{$state}}
                                            <br>

                                        </td>
                                        <td style="vertical-align: middle;">
                                            <span>{{ Carbon::parse($voucher->expirationdate)->format('d-m-Y') }}</span>
                                        </td>
                                    </tr>
                                        <?php $index = $index + 1; ?>
                                @endforeach
                                </tbody>
                            </table>
                            @else
                            <h4>{{ __('Aucun bon trouvé')}}</h4>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


