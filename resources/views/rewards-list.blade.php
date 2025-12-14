@php
    use App\Models\ConversionAmountPoint;
    use App\Models\Reward;
    use App\Models\Threshold;
    use App\Models\Transactiontype;
    use App\Models\Voucher;
    use Illuminate\Support\Carbon;
    use Illuminate\Support\Facades\Auth;
    use App\Models\Loyaltytransaction;

@endphp
@extends('layouts.app-client')

@section('content')
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="row justify-content-center">

                @include('layouts.client-menu')

                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header"><h5>{{ __('Récompenses') }}</h5></div>

                        <div class="card-body">

                            @include('reward.list-card')

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
