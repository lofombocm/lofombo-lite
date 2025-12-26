@php
    use App\Models\Config;
    use App\Models\Notification;
    use App\Models\Voucher;
    use Illuminate\Support\Carbon;
    use Illuminate\Support\Facades\Auth;

    $notifications = Notification::
        where('recipient_address', Auth::guard('client')->user()->telephone)->where('read', false)->get();
    $unreadMsgNum = count($notifications);

@endphp
@extends('layouts.app-client')
@section('content')
    {{--<div class="container">--}}
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="row justify-content-center">
                @include('layouts.client-menu')
                <div class="col-md-9">
                    <?php
                    //$conversion = ConversionAmountPoint::where('active', true)->where('is_applicable', true)->first();
                    //$threshold = Threshold::where('active', true)->where('is_applicable', true)->first();
                    $configuration = Config::where('is_applicable', true)->first();
                    $levels = json_decode($configuration->levels);
                    ?>
                    <div class="row justify-content-center">
                        <div class="col-md-12">
                            <div class="card">
                                {{--<div class="card-header"></div>--}}
                                <div class="card-body">
                                    <h4>{{ __('Mes Bons') }}</h4>
                                    <?php
                                    $client = Auth::guard('client')->user();
                                    $vouchers = Voucher::where('clientid', $client->id)->orderBy('created_at', 'desc')->get();
                                    ?>
                                    @if(count($vouchers) > 0)
                                        <table class="table table-striped table-responsive table-bordered">
                                            <thead class="" style="color: darkred;">
                                            <th scope="col">
                                                {{ __("N° Série") }}
                                            </th>
                                            <th scope="col">
                                                {{ 'Type' }}
                                            </th>
                                            <th scope="col">
                                                {{ __('Point') }}
                                            </th>
                                            <th scope="col">
                                                {{ __('Expiration') }}
                                            </th>
                                            <th scope="col">
                                                {{ __('Etat') }}
                                            </th>
                                            <th scope="col">
                                                {{ __('Action') }}
                                            </th>
                                            </thead>
                                            <tbody>
                                            @foreach($vouchers as $voucher)
                                                    <?php
                                                    //$client = Client::where('id', $voucher->clientid)->first();
                                                    //$type = $voucher->level === 'CLASSIC' ? 'alert-secondary' : ($voucher->level === 'PREMIUM' ? 'alert-success' : 'alert-warning');
                                                    $type = 'alert-info';
                                                    $validite = __('Valide');
                                                    $expirationdate = Carbon::parse($voucher->expirationdate);
                                                    $expired = false;
                                                    $statut = '';
                                                    if ($voucher->active){
                                                        if ($voucher->is_used){
                                                            $statut = __('UTILISE');
                                                        }else{
                                                            $statut = __('ACTIVE');
                                                        }
                                                    }else{
                                                        if ($expirationdate->isBefore(Carbon::now())) {
                                                            $statut = __('EXPIRE');
                                                        }else{
                                                            $statut = __('GENERE');
                                                        }
                                                    }

                                                    if ($expirationdate->isBefore(Carbon::now())) {
                                                        $validite = __('Invalide');
                                                        $expired = true;
                                                    }
                                                    //$client = Client::where('id', $voucher->clientid)->first();
                                                    //$reward = Reward::where('id', $voucher->reward)->first();
                                                    ?>

                                                <tr >
                                                    <th>
                                                        <h5>{{$voucher->serialnumber}}</h5>
                                                    </th>
                                                    <td>
                                                        <h5>{{$voucher->level}}</h5>
                                                    </td>
                                                    <td>
                                                        <h5>{{$voucher->point}}</h5>
                                                    </td>
                                                    <td>
                                                        <h5>{{$expirationdate->format('d-m-Y H:i:s')}}</h5>
                                                    </td>
                                                    <td>
                                                        <h5 style="display: inline;">{{$statut}}</h5>
                                                    </td>
                                                    <td >
                                                        <div>
                                                                        <span style="float: right;">
                                                                            @if($voucher->active === true)
                                                                                @if($voucher->is_used)
                                                                                    <span class="position-relative top-0 start-100
                                                                                     translate-middle p-2 rounded-pill
                                                                                     bg-dark border border-light
                                                                                     rounded-circle badge">
                                                                                    </span>
                                                                                @else
                                                                                    <span class="position-relative top-0 start-100
                                                                                     translate-middle p-2 rounded-pill
                                                                                     bg-success border border-light
                                                                                     rounded-circle badge">

                                                                                    </span>
                                                                                @endif

                                                                            @else

                                                                                <span class="position-relative top-0 start-100
                                                                                     translate-middle p-2 rounded-pill
                                                                                     bg-danger border border-light
                                                                                     rounded-circle badge">

                                                                                </span>
                                                                            @endif
                                                                        </span>
                                                        </div>

                                                        @if(!$voucher->is_used)
                                                            <div class="dropdown">
                                                                <a class="btn btn-link dropdown-toggle"
                                                                   href="#" role="button"
                                                                   id="dropdownMenuLink"
                                                                   style="text-decoration: none;"
                                                                   data-bs-toggle="dropdown"
                                                                   aria-haspopup="true" aria-expanded="false" >
                                                                    <img src="{{asset('images/icons8-menu-vertical-24.png')}}" alt="^" />
                                                                </a>

                                                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuLink">
                                                                    @if(!$voucher->is_used)
                                                                        <a class="dropdown-item btn btn-link" href="{{route('vouchers.resend.usage.code', $voucher->id)}}"
                                                                           style="text-decoration: none;">
                                                                            <img src="{{asset('images/icons8-transfer-gras-26.png')}}" alt="">
                                                                            {{ __("Renvoyer le code d'utilisation") }}
                                                                        </a>
                                                                    @endif
                                                                    <a class="dropdown-item btn btn-link" href="{{route('vouchers.download', $voucher->id)}}"
                                                                       style="text-decoration: none;">
                                                                        <img src="{{asset('images/icons8-downloading-updates-20.png')}}" alt="{{__('Télécharger')}}">
                                                                        {{ __("Télécharger") }}
                                                                    </a>
                                                                    <a class="dropdown-item btn btn-link" href="#"
                                                                       style="text-decoration: none;">
                                                                        <img src="{{asset('images/icons8-print-20.png')}}" alt="">
                                                                        {{ __("Imprimer") }}
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        @endif

                                                    </td>
                                                </tr>

                                            @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <h5> {{ __("Vous n'avez pas de bon") }}</h5>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
